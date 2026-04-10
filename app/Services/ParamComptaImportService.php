<?php

namespace App\Services;

use App\Models\CompteComptable;
use App\Models\JournalComptable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParamComptaImportService
{
    public function import(string $filePath, bool $dryRun = false): array
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException('Fichier introuvable: ' . $filePath);
        }

        $spreadsheet = $this->loadSpreadsheet($filePath);

        $stats = [
            'dry_run' => $dryRun,
            'source' => $filePath,
            'general_accounts_created' => 0,
            'general_accounts_updated' => 0,
            'client_accounts_created' => 0,
            'client_accounts_updated' => 0,
            'supplier_accounts_created' => 0,
            'supplier_accounts_updated' => 0,
            'journals_created' => 0,
            'journals_updated' => 0,
            'projects_created' => 0,
            'projects_updated' => 0,
            'client_links_updated' => 0,
            'supplier_codes_updated' => 0,
            'warnings' => [],
        ];

        DB::beginTransaction();

        try {
            $this->importGeneralAccounts($spreadsheet->getSheetByName('PCE_G-IMP'), $stats);
            $this->importJournals($spreadsheet->getSheetByName('CODES JOURNAUX'), $stats);
            $this->importTierAccounts($spreadsheet->getSheetByName('TIERS CLIENTS'), 'client', $stats);
            $this->importTierAccounts($spreadsheet->getSheetByName('TIERS FOURNISSEURS'), 'supplier', $stats);
            $this->importProjects($spreadsheet->getSheetByName('PLAN ANALYTIQUE'), $stats);

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            return $stats;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function importGeneralAccounts(?Worksheet $sheet, array &$stats): void
    {
        if (!$sheet || !Schema::hasTable('comptes_comptables')) {
            $stats['warnings'][] = 'Feuille PCE_G-IMP ou table comptes_comptables absente.';
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'compte' => ['compte'],
            'intitule' => ['intitule', 'intitulé'],
            'type' => ['type'],
        ]);

        if (!$headerMap) {
            $stats['warnings'][] = 'Entetes introuvables dans PCE_G-IMP.';
            return;
        }

        foreach ($rows as $row) {
            $code = $this->cleanCell($row[$headerMap['compte']] ?? null);
            $label = $this->cleanCell($row[$headerMap['intitule']] ?? null);
            $rawType = $this->cleanCell($row[$headerMap['type']] ?? null);

            if ($code === '' || $label === '') {
                continue;
            }

            $payload = $this->buildAccountPayload($code, $label, $this->normalizeAccountType($rawType, $code), null);
            $this->upsertAccount($code, $payload, $stats, 'general_accounts');
        }
    }

    private function importJournals(?Worksheet $sheet, array &$stats): void
    {
        if (!$sheet || !Schema::hasTable('journal_comptables')) {
            $stats['warnings'][] = 'Feuille CODES JOURNAUX ou table journal_comptables absente.';
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'code' => ['code'],
            'intitule' => ['intitule', 'intitulé'],
            'type' => ['type'],
            'compte' => ['compte'],
        ]);

        if (!$headerMap) {
            $stats['warnings'][] = 'Entetes introuvables dans CODES JOURNAUX.';
            return;
        }

        foreach ($rows as $row) {
            $code = $this->cleanCell($row[$headerMap['code']] ?? null);
            $label = $this->cleanCell($row[$headerMap['intitule']] ?? null);
            $rawType = $this->cleanCell($row[$headerMap['type']] ?? null);
            $accountCode = $this->cleanCell($row[$headerMap['compte']] ?? null);

            if ($code === '' || $label === '') {
                continue;
            }

            $payload = [
                'code' => $code,
                'libelle' => $label,
                'type' => $this->normalizeJournalType($code, $label, $rawType),
            ];

            if (Schema::hasColumn('journal_comptables', 'description') && $accountCode !== '') {
                $payload['description'] = 'Journal importe depuis PARAM COMPTA. Compte central: ' . $accountCode;
            }

            if (Schema::hasColumn('journal_comptables', 'actif')) {
                $payload['actif'] = true;
            }

            if (Schema::hasColumn('journal_comptables', 'systeme')) {
                $payload['systeme'] = false;
            }

            $journal = JournalComptable::query()->where('code', $code)->first();

            if ($journal) {
                $journal->fill($payload);
                $journal->save();
                $stats['journals_updated']++;
            } else {
                JournalComptable::query()->create($payload);
                $stats['journals_created']++;
            }
        }
    }

    private function importTierAccounts(?Worksheet $sheet, string $tierType, array &$stats): void
    {
        if (!$sheet || !Schema::hasTable('comptes_comptables')) {
            $stats['warnings'][] = 'Feuille de tiers ou table comptes_comptables absente pour ' . $tierType . '.';
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'intitule' => ['intitule', 'intitulé'],
            'num_ct' => ['num_ct'],
            'num_cg' => ['num_cg'],
            'type' => ['type'],
        ]);

        if (!$headerMap) {
            $stats['warnings'][] = 'Entetes introuvables dans la feuille de tiers ' . $tierType . '.';
            return;
        }

        foreach ($rows as $row) {
            $label = $this->cleanCell($row[$headerMap['intitule']] ?? null);
            $auxCode = $this->cleanCell($row[$headerMap['num_ct']] ?? null);
            $generalCode = $this->cleanCell($row[$headerMap['num_cg']] ?? null);

            if ($label === '' || $auxCode === '') {
                continue;
            }

            $payload = $this->buildAccountPayload(
                $auxCode,
                $label,
                $tierType === 'client' ? 'client' : 'fournisseur',
                $this->findAccountIdByCode($generalCode)
            );

            $accountId = $this->upsertAccount(
                $auxCode,
                $payload,
                $stats,
                $tierType === 'client' ? 'client_accounts' : 'supplier_accounts'
            );

            if ($tierType === 'client') {
                $this->linkClientIfPossible($label, $accountId, $stats);
            } else {
                $this->linkSupplierCodeIfPossible($label, $auxCode, $stats);
            }
        }
    }

    private function importProjects(?Worksheet $sheet, array &$stats): void
    {
        if (!$sheet || !Schema::hasTable('projets')) {
            $stats['warnings'][] = 'Feuille PLAN ANALYTIQUE ou table projets absente.';
            return;
        }

        [$headerMap, $rows] = $this->extractRows($sheet, [
            'activite' => ['activite', 'activite '],
            'code_projet' => ['code projet', 'code_projet'],
            'nom_projet' => ['projet chantier', 'projet_chantier'],
            'famille' => ['famille'],
            'designation' => ['designation', 'designations'],
        ]);

        if (!$headerMap) {
            $stats['warnings'][] = 'Entetes introuvables dans PLAN ANALYTIQUE.';
            return;
        }

        foreach ($rows as $row) {
            $code = $this->cleanCell($row[$headerMap['code_projet']] ?? null);
            $name = $this->cleanCell($row[$headerMap['nom_projet']] ?? null);

            if ($code === '' || $name === '') {
                continue;
            }

            $descriptionParts = array_filter([
                'Activite: ' . $this->cleanCell($row[$headerMap['activite']] ?? null),
                'Famille: ' . $this->cleanCell($row[$headerMap['famille']] ?? null),
                'Designation: ' . $this->cleanCell($row[$headerMap['designation']] ?? null),
            ], fn ($value) => !str_ends_with($value, ': '));

            $payload = [
                'code_projet' => $code,
                'nom_projet' => $name,
            ];

            if (Schema::hasColumn('projets', 'description')) {
                $payload['description'] = implode(' | ', $descriptionParts);
            }

            if (Schema::hasColumn('projets', 'created_by')) {
                $payload['created_by'] = 'import_excel';
            }

            $project = DB::table('projets')->where('code_projet', $code)->first();

            if ($project) {
                DB::table('projets')->where('id', $project->id)->update(array_merge($payload, [
                    'updated_at' => now(),
                ]));
                $stats['projects_updated']++;
            } else {
                DB::table('projets')->insert(array_merge($payload, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $stats['projects_created']++;
            }
        }
    }

    private function extractRows(Worksheet $sheet, array $expectedHeaders): array
    {
        $rows = $sheet->toArray(null, false, true, true);
        $headerMap = null;
        $dataStart = null;

        foreach ($rows as $rowNumber => $row) {
            $normalized = [];

            foreach ($row as $column => $value) {
                $clean = $this->normalizeHeader($value);
                if ($clean !== '') {
                    $normalized[$column] = $clean;
                }
            }

            if (empty($normalized)) {
                continue;
            }

            $candidateMap = [];
            foreach ($expectedHeaders as $target => $aliases) {
                foreach ($normalized as $column => $value) {
                    if (in_array($value, $aliases, true)) {
                        $candidateMap[$target] = $column;
                        break;
                    }
                }
            }

            if (count($candidateMap) >= max(2, count($expectedHeaders) - 1)) {
                $headerMap = $candidateMap;
                $dataStart = $rowNumber + 1;
                break;
            }
        }

        if (!$headerMap || !$dataStart) {
            return [null, []];
        }

        $result = [];
        foreach ($rows as $rowNumber => $row) {
            if ($rowNumber < $dataStart) {
                continue;
            }

            if ($this->isRowEmpty($row)) {
                continue;
            }

            $result[] = $row;
        }

        return [$headerMap, $result];
    }

    private function upsertAccount(string $code, array $payload, array &$stats, string $statPrefix): int
    {
        $account = CompteComptable::query()->where('code', $code)->first();

        if ($account) {
            $account->fill($payload);
            $account->save();
            $stats[$statPrefix . '_updated']++;
            return (int) $account->getKey();
        }

        $account = CompteComptable::query()->create($payload);
        $stats[$statPrefix . '_created']++;

        return (int) $account->getKey();
    }

    private function buildAccountPayload(string $code, string $label, ?string $type, ?int $parentId): array
    {
        $payload = [
            'code' => $code,
            'libelle' => $label,
        ];

        if (Schema::hasColumn('comptes_comptables', 'numero')) {
            $payload['numero'] = $code;
        }

        if (Schema::hasColumn('comptes_comptables', 'numero_compte')) {
            $payload['numero_compte'] = $code;
        }

        if (Schema::hasColumn('comptes_comptables', 'intitule')) {
            $payload['intitule'] = $label;
        }

        if (Schema::hasColumn('comptes_comptables', 'description')) {
            $payload['description'] = 'Importe depuis le fichier PARAM COMPTA';
        }

        if (Schema::hasColumn('comptes_comptables', 'type') && $type) {
            $payload['type'] = $type;
        }

        if (Schema::hasColumn('comptes_comptables', 'actif')) {
            $payload['actif'] = true;
        }

        if (Schema::hasColumn('comptes_comptables', 'est_verrouille')) {
            $payload['est_verrouille'] = false;
        }

        if (Schema::hasColumn('comptes_comptables', 'parent_id') && $parentId) {
            $payload['parent_id'] = $parentId;
        }

        return $payload;
    }

    private function findAccountIdByCode(?string $code): ?int
    {
        $code = trim((string) $code);
        if ($code === '') {
            return null;
        }

        $query = CompteComptable::query();

        $query->where('code', $code);

        if (Schema::hasColumn('comptes_comptables', 'numero')) {
            $query->orWhere('numero', $code);
        }

        if (Schema::hasColumn('comptes_comptables', 'numero_compte')) {
            $query->orWhere('numero_compte', $code);
        }

        $account = $query->first();

        return $account ? (int) $account->getKey() : null;
    }

    private function linkClientIfPossible(string $label, int $accountId, array &$stats): void
    {
        if (!Schema::hasTable('clients') || !Schema::hasColumn('clients', 'compte_comptable_id')) {
            return;
        }

        $normalized = mb_strtolower(trim($label));
        if ($normalized === '') {
            return;
        }

        foreach (['nom', 'raison_sociale', 'company_name', 'nom_complet', 'contact_nom'] as $column) {
            if (!Schema::hasColumn('clients', $column)) {
                continue;
            }

            $updated = DB::table('clients')
                ->whereRaw('LOWER(TRIM(' . $column . ')) = ?', [$normalized])
                ->update([
                    'compte_comptable_id' => $accountId,
                    'updated_at' => Schema::hasColumn('clients', 'updated_at') ? now() : DB::raw('updated_at'),
                ]);

            if ($updated > 0) {
                $stats['client_links_updated'] += $updated;
                return;
            }
        }
    }

    private function linkSupplierCodeIfPossible(string $label, string $auxCode, array &$stats): void
    {
        if (!Schema::hasTable('fournisseurs') || !Schema::hasColumn('fournisseurs', 'code_comptable')) {
            return;
        }

        $normalized = mb_strtolower(trim($label));
        if ($normalized === '') {
            return;
        }

        $updated = DB::table('fournisseurs')
            ->whereRaw('LOWER(TRIM(raison_sociale)) = ?', [$normalized])
            ->update([
                'code_comptable' => $auxCode,
                'updated_at' => Schema::hasColumn('fournisseurs', 'updated_at') ? now() : DB::raw('updated_at'),
            ]);

        if ($updated > 0) {
            $stats['supplier_codes_updated'] += $updated;
        }
    }

    private function normalizeAccountType(?string $rawType, string $code): string
    {
        $rawType = mb_strtolower(trim((string) $rawType));

        if ($rawType !== '') {
            return match ($rawType) {
                'g' => 'general',
                'c' => 'client',
                'f' => 'fournisseur',
                default => $rawType,
            };
        }

        return match (substr($code, 0, 1)) {
            '4' => 'tiers',
            '5' => 'tresorerie',
            '6' => 'charge',
            '7' => 'produit',
            default => 'general',
        };
    }

    private function normalizeJournalType(string $code, string $label, ?string $rawType): string
    {
        $code = mb_strtolower(trim($code));
        $label = mb_strtolower(trim($label));
        $rawType = trim((string) $rawType);

        if ($code === 'vt' || str_contains($label, 'vente')) {
            return 'vente';
        }

        if ($code === 'ca' || str_contains($label, 'caisse')) {
            return 'caisse';
        }

        if ($code === 'bq' || str_contains($label, 'banque')) {
            return 'banque';
        }

        if ($code === 'pa' || str_contains($label, 'paie')) {
            return 'paie';
        }

        if ($code === 'im' || str_contains($label, 'immobilisation')) {
            return 'immobilisation';
        }

        if ($code === 'ah' || str_contains($label, 'achat')) {
            return 'achat';
        }

        if ($rawType !== '') {
            return match ($rawType) {
                '0' => 'achat',
                '1' => 'vente',
                '2' => 'caisse',
                default => 'od',
            };
        }

        return 'od';
    }

    private function normalizeHeader(mixed $value): string
    {
        $value = $this->cleanCell($value);
        $value = str_replace(['\n', '\r', '/', '-', '°', 'n°'], [' ', ' ', ' ', ' ', ' ', ' '], $value);
        $value = preg_replace('/\s+/', ' ', $value) ?: '';
        $value = trim(mb_strtolower($value));
        $value = str_replace(['é', 'è', 'ê', 'ë'], 'e', $value);
        $value = str_replace(['à', 'â'], 'a', $value);
        $value = str_replace(['î', 'ï'], 'i', $value);
        $value = str_replace(['ô', 'ö'], 'o', $value);
        $value = str_replace(['ù', 'û', 'ü'], 'u', $value);
        $value = str_replace(['ç', "'", '’'], ['c', '', ''], $value);

        return $value;
    }

    private function cleanCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_float($value) || is_int($value)) {
            $string = (string) $value;
            return str_ends_with($string, '.0') ? substr($string, 0, -2) : $string;
        }

        return trim((string) $value);
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($this->cleanCell($value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function loadSpreadsheet(string $filePath): Spreadsheet
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);

        return $reader->load($filePath);
    }
}