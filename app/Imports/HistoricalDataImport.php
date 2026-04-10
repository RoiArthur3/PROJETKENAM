<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Facture;
use App\Models\Fournisseur;
use App\Models\Personnel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class HistoricalDataImport implements ToCollection, WithHeadingRow
{
    public int $rowCount = 0;
    public int $createdCount = 0;
    public int $updatedCount = 0;
    public int $skippedCount = 0;

    /** @var string[] */
    public array $errors = [];

    public function __construct(
        private readonly string $entity,
        private readonly string $mode = 'creer_maj'
    ) {
    }

    public static function supportedEntities(): array
    {
        return [
            'fournisseurs' => 'Fournisseurs',
            'clients' => 'Clients',
            'personnel' => 'Personnel',
            'factures' => 'Factures',
        ];
    }

    public static function templateHeaders(string $entity): array
    {
        return match ($entity) {
            'fournisseurs' => [
                'reference', 'raison_sociale', 'email', 'telephone', 'ville', 'pays', 'est_actif',
            ],
            'clients' => [
                'code', 'raison_sociale', 'contact_nom', 'email', 'telephone', 'ville', 'pays', 'actif',
            ],
            'personnel' => [
                'matricule', 'nom', 'prenoms', 'email_personnel', 'telephone_principal', 'poste', 'service', 'statut',
            ],
            'factures' => [
                'numero', 'client_id', 'date_facture', 'montant_ht', 'tva', 'montant_ttc', 'statut',
            ],
            default => ['id'],
        };
    }

    public function collection(Collection $rows): void
    {
        $config = $this->entityConfig($this->entity);
        if (!$config) {
            $this->errors[] = 'Entite non supportee: ' . $this->entity;
            return;
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = $config['model'];
        $uniqueCandidates = $config['unique_candidates'];
        $aliases = $config['aliases'];

        $prototype = new $modelClass();
        $fillable = $prototype->getFillable();
        $allowedColumns = Schema::getColumnListing($prototype->getTable());

        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $this->rowCount++;

            try {
                $raw = $row->toArray();
                $mapped = $this->mapRow($raw, $aliases);
                $filtered = $this->sanitizeFields($mapped, $allowedColumns, $fillable);

                $uniqueField = null;
                $uniqueValue = null;

                foreach ($uniqueCandidates as $candidate) {
                    if (!empty($filtered[$candidate])) {
                        $uniqueField = $candidate;
                        $uniqueValue = $filtered[$candidate];
                        break;
                    }
                }

                if (!$uniqueField || $uniqueValue === null || $uniqueValue === '') {
                    $this->skippedCount++;
                    $this->errors[] = "Ligne {$line}: aucune cle d'identification valide.";
                    continue;
                }

                $existing = $modelClass::query()->where($uniqueField, $uniqueValue)->first();

                if ($existing && $this->mode === 'creer') {
                    $this->skippedCount++;
                    continue;
                }

                if (!$existing && $this->mode === 'maj') {
                    $this->skippedCount++;
                    continue;
                }

                if ($existing) {
                    $existing->fill($filtered);
                    $existing->save();
                    $this->updatedCount++;
                    continue;
                }

                if (in_array('created_by', $fillable, true) && empty($filtered['created_by']) && Auth::id()) {
                    $filtered['created_by'] = Auth::id();
                }

                if (in_array('user_id', $fillable, true) && empty($filtered['user_id']) && Auth::id()) {
                    $filtered['user_id'] = Auth::id();
                }

                $modelClass::query()->create($filtered);
                $this->createdCount++;
            } catch (\Throwable $e) {
                $this->skippedCount++;
                $this->errors[] = "Ligne {$line}: " . $e->getMessage();
            }
        }
    }

    private function mapRow(array $row, array $aliases): array
    {
        $mapped = [];

        foreach ($row as $key => $value) {
            $sourceKey = trim((string) $key);
            if ($sourceKey === '') {
                continue;
            }

            $target = $aliases[$sourceKey] ?? $sourceKey;
            $mapped[$target] = $value;
        }

        return $mapped;
    }

    private function sanitizeFields(array $fields, array $allowedColumns, array $fillable): array
    {
        $allowed = array_flip($allowedColumns);
        $fillableSet = array_flip($fillable);
        $result = [];

        foreach ($fields as $key => $value) {
            if (!isset($allowed[$key]) || !isset($fillableSet[$key])) {
                continue;
            }

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value === '') {
                continue;
            }

            if (in_array($key, ['est_actif', 'actif'], true)) {
                $normalized = strtolower((string) $value);
                $value = in_array($normalized, ['1', 'true', 'oui', 'yes', 'actif'], true) ? 1 : 0;
            }

            if (in_array($key, ['montant_ht', 'montant_ttc', 'tva', 'salaire_base', 'credit_limit'], true)) {
                $value = (float) str_replace([',', ' '], ['.', ''], (string) $value);
            }

            if (in_array($key, ['date_facture', 'date_echeance', 'date_paiement'], true)) {
                $normalizedDate = $this->normalizeDateValue($value);
                if ($normalizedDate === null) {
                    continue;
                }
                $value = $normalizedDate;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    private function normalizeDateValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $formats = [
            'Y-m-d',
            'Y/m/d',
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'm/d/Y',
            'd/m/y',
            'd-m-y',
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $raw);
            if ($date instanceof \DateTime) {
                return $date->format('Y-m-d');
            }
        }

        $timestamp = strtotime($raw);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    private function entityConfig(string $entity): ?array
    {
        $configs = [
            'fournisseurs' => [
                'model' => Fournisseur::class,
                'unique_candidates' => ['reference', 'raison_sociale', 'email'],
                'aliases' => [
                    'nom' => 'raison_sociale',
                    'statut' => 'est_actif',
                ],
            ],
            'clients' => [
                'model' => Client::class,
                'unique_candidates' => ['code', 'raison_sociale', 'email'],
                'aliases' => [
                    'nom' => 'raison_sociale',
                    'contact' => 'contact_nom',
                    'statut' => 'actif',
                ],
            ],
            'personnel' => [
                'model' => Personnel::class,
                'unique_candidates' => ['matricule', 'email_personnel'],
                'aliases' => [
                    'email' => 'email_personnel',
                    'telephone' => 'telephone_principal',
                ],
            ],
            'factures' => [
                'model' => Facture::class,
                'unique_candidates' => ['numero'],
                'aliases' => [
                    'numero_facture' => 'numero',
                ],
            ],
        ];

        return Arr::get($configs, $entity);
    }
}
