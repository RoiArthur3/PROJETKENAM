<?php

namespace App\Imports;

use App\Models\Facture;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class FacturesImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skipped = 0;

    private ?int $defaultClientId;

    private array $clientIdCache = [];

    public function __construct(?int $defaultClientId = null)
    {
        $this->defaultClientId = $defaultClientId ? (int) $defaultClientId : null;
    }

    private function getClientNameColumn(): string
    {
        if (Schema::hasColumn('clients', 'nom')) {
            return 'nom';
        }

        if (Schema::hasColumn('clients', 'raison_sociale')) {
            return 'raison_sociale';
        }

        if (Schema::hasColumn('clients', 'company_name')) {
            return 'company_name';
        }

        return 'contact_nom';
    }

    private function resolveClientId(?string $clientName): ?int
    {
        $clientName = trim((string) $clientName);
        if ($clientName === '') {
            return null;
        }

        $cacheKey = mb_strtolower($clientName);
        if (array_key_exists($cacheKey, $this->clientIdCache)) {
            return $this->clientIdCache[$cacheKey];
        }

        $nameCol = $this->getClientNameColumn();
        $clientId = DB::table('clients')
            ->whereRaw("LOWER(TRIM({$nameCol})) = ?", [mb_strtolower($clientName)])
            ->value('id');

        $this->clientIdCache[$cacheKey] = $clientId ? (int) $clientId : null;

        return $this->clientIdCache[$cacheKey];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $numero = isset($row['numero']) ? trim((string) $row['numero']) : '';
            $clientId = $row['client_id'] ?? null;
            $clientName = $row['client'] ?? ($row['client_nom'] ?? ($row['nom_client'] ?? null));
            $dateFacture = $row['date_facture'] ?? null;

            if (empty($clientId) && !empty($clientName)) {
                $clientId = $this->resolveClientId((string) $clientName);
            }

            if (empty($clientId) && $this->defaultClientId) {
                $clientId = $this->defaultClientId;
            }

            if ($numero === '' || empty($clientId) || empty($dateFacture)) {
                $this->skipped++;
                continue;
            }

            if (is_numeric($dateFacture)) {
                $dateFacture = Carbon::instance(ExcelDate::excelToDateTimeObject($dateFacture))->toDateString();
            } else {
                $dateFacture = Carbon::parse($dateFacture)->toDateString();
            }

            $dateEcheance = $row['date_echeance'] ?? null;
            if (!empty($dateEcheance)) {
                if (is_numeric($dateEcheance)) {
                    $dateEcheance = Carbon::instance(ExcelDate::excelToDateTimeObject($dateEcheance))->toDateString();
                } else {
                    $dateEcheance = Carbon::parse($dateEcheance)->toDateString();
                }
            } else {
                $dateEcheance = null;
            }

            $data = [
                'client_id' => (int) $clientId,
                'date_facture' => $dateFacture,
                'date_echeance' => $dateEcheance,
                'montant_ht' => (float) ($row['montant_ht'] ?? 0),
                'tva' => (float) ($row['tva'] ?? 18),
                'montant_ttc' => (float) ($row['montant_ttc'] ?? 0),
                'statut' => $row['statut'] ?? 'en_attente',
                'type' => $row['type'] ?? null,
            ];

            Facture::updateOrCreate(
                ['numero' => $numero],
                $data
            );

            $this->imported++;
        }
    }
}
