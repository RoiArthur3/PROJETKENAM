<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportJournalVentes extends Command
{
    protected $signature = 'journal:ventes:import
        {file=public/docs/Anciennes factures.xlsx : Chemin relatif du fichier CSV/XLS/XLSX}
        {--sheet= : Nom de la feuille (optionnel)}
        {--dry-run : Analyse sans ecrire en base}';

    protected $description = 'Importe un journal des ventes (CSV/XLSX) vers le grand journal comptable';

    public function handle(): int
    {
        $file = (string) $this->argument('file');
        $fullPath = $this->resolveInputPath($file);

        if ($fullPath === null || !is_file($fullPath)) {
            $this->error('Fichier introuvable: ' . $file);
            $this->line('Chemins verifies:');
            foreach ($this->candidatePaths($file) as $candidate) {
                $this->line(' - ' . $candidate);
            }
            return self::FAILURE;
        }

        if (!Schema::hasTable('ecritures_comptables')) {
            $this->error('Table ecritures_comptables introuvable.');
            return self::FAILURE;
        }

        $journalId = $this->ensureSalesJournal();
        if ($journalId === null) {
            $this->error('Journal comptable VT introuvable et impossible a creer.');
            return self::FAILURE;
        }

        $spreadsheet = IOFactory::load($fullPath);
        $sheetName = (string) $this->option('sheet');
        $sheet = $sheetName !== '' ? $spreadsheet->getSheetByName($sheetName) : $spreadsheet->getActiveSheet();

        if (!$sheet) {
            $this->error('Feuille introuvable: ' . $sheetName);
            return self::FAILURE;
        }

        $headerMap = $this->buildHeaderMap($sheet);
        if ($this->isExpectedOrderedLayout($sheet)) {
            $headerMap = [
                'jl' => 'A',
                'date' => 'B',
                'piece' => 'C',
                'compte_g' => 'D',
                'compte_tiers' => 'E',
                'libelle' => 'F',
                'debit' => 'G',
                'credit' => 'H',
            ];
            $this->info('Format detecte: ordre fixe JL, DATE, N° PIECE, COMPTE G, COMPTE TIERS, LIBELLE, DEBIT, CREDIT.');
        }
        foreach (['date', 'piece', 'compte_g', 'compte_tiers', 'libelle', 'debit', 'credit'] as $required) {
            if (!isset($headerMap[$required])) {
                $this->error('Colonne manquante: ' . $required);
                $this->line('Colonnes detectees: ' . implode(', ', array_keys($headerMap)));
                return self::FAILURE;
            }
        }

        $groups = [];
        $maxRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $maxRow; $row++) {
            $piece = trim((string) $sheet->getCell($headerMap['piece'] . $row)->getValue());
            if ($piece === '') {
                continue;
            }

            $rawDate = $sheet->getCell($headerMap['date'] . $row)->getValue();
            $compteG = trim((string) $sheet->getCell($headerMap['compte_g'] . $row)->getValue());
            $compteTiers = trim((string) $sheet->getCell($headerMap['compte_tiers'] . $row)->getValue());
            $libelle = trim((string) $sheet->getCell($headerMap['libelle'] . $row)->getValue());
            $debit = $this->parseAmount((string) $sheet->getCell($headerMap['debit'] . $row)->getFormattedValue());
            $credit = $this->parseAmount((string) $sheet->getCell($headerMap['credit'] . $row)->getFormattedValue());

            if (!isset($groups[$piece])) {
                $groups[$piece] = [
                    'date' => $this->parseDate($rawDate),
                    'client_raw' => null,
                    'libelle' => $libelle,
                    'debit_account' => null,
                    'lines' => [],
                ];
            }

            if ($groups[$piece]['date'] === null) {
                $groups[$piece]['date'] = $this->parseDate($rawDate);
            }

            if (($groups[$piece]['client_raw'] === null || $groups[$piece]['client_raw'] === '') && $compteTiers !== '') {
                $groups[$piece]['client_raw'] = $compteTiers;
            }

            if ($groups[$piece]['libelle'] === '' && $libelle !== '') {
                $groups[$piece]['libelle'] = $libelle;
            }

            if ($debit > 0 && $groups[$piece]['debit_account'] === null) {
                $groups[$piece]['debit_account'] = $compteG;
            }

            $groups[$piece]['lines'][] = [
                'row' => $row,
                'compte_g' => $compteG,
                'libelle' => $libelle,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        $dryRun = (bool) $this->option('dry-run');
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($groups as $piece => $item) {
            $clientName = $this->extractClientName((string) ($item['client_raw'] ?? ''));

            $debitAccount = (string) ($item['debit_account'] ?: '411000');
            $date = (string) ($item['date'] ?? Carbon::now()->toDateString());
            $lineNumber = 0;

            foreach ($item['lines'] as $line) {
                $amount = round((float) $line['credit'], 2);
                if ($amount <= 0) {
                    continue;
                }

                $lineNumber++;
                $reference = sprintf('%s-%02d', $piece, $lineNumber);
                $sourceKey = sprintf('%s|%s|%s|%s|%s', $piece, $line['row'], $line['compte_g'], $line['libelle'], $amount);
                $importSourceId = (int) sprintf('%u', crc32($sourceKey));
                $libelle = trim((string) $line['libelle']);
                if ($clientName) {
                    $libelle = $libelle !== '' ? $libelle . ' | ' . $clientName : $clientName;
                }
                if ($libelle === '') {
                    $libelle = 'Journal de ventes importé';
                }

                $payload = [
                    'journal_id' => $journalId,
                    'date' => $date,
                    'reference' => $reference,
                    'piece_comptable' => $piece,
                    'libelle' => $libelle,
                    'compte_debit' => $debitAccount,
                    'compte_credit' => (string) $line['compte_g'],
                    'montant' => $amount,
                    'description' => (string) ($item['libelle'] ?: $libelle),
                    'source_type' => 'journal_vente_import',
                    'source_id' => $importSourceId,
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('ecritures_comptables', 'created_at')) {
                    $payload['created_at'] = now();
                }

                if ($dryRun) {
                    $this->line(sprintf(
                        '[DRY] %s | ref=%s | %s -> %s | %s | %s',
                        $piece,
                        $reference,
                        $debitAccount,
                        $line['compte_g'],
                        number_format($amount, 2, ',', ' '),
                        $libelle
                    ));
                    continue;
                }

                $exists = DB::table('ecritures_comptables')
                    ->where('source_type', 'journal_vente_import')
                    ->where('source_id', $importSourceId)
                    ->exists();

                DB::table('ecritures_comptables')->updateOrInsert(
                    [
                        'source_type' => 'journal_vente_import',
                        'source_id' => $importSourceId,
                    ],
                    $payload
                );

                if ($exists) {
                    $updated++;
                } else {
                    $created++;
                }
            }

            if ($lineNumber === 0) {
                $skipped++;
            }
        }

        $this->info(sprintf('Import grand journal termine: %d ecritures creees, %d mises a jour, %d pieces ignorees (pieces lues: %d).', $created, $updated, $skipped, count($groups)));

        if ($dryRun) {
            $this->warn('Mode dry-run: aucune ecriture en base.');
        }

        return self::SUCCESS;
    }

    private function resolveInputPath(string $file): ?string
    {
        foreach ($this->candidatePaths($file) as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function candidatePaths(string $file): array
    {
        $file = trim($file);
        $paths = [];

        if ($file === '') {
            return $paths;
        }

        if ($this->isAbsolutePath($file)) {
            $paths[] = $file;
            return array_values(array_unique($paths));
        }

        $cleanFile = ltrim($file, '/\\');
        $paths[] = base_path($file);
        $paths[] = base_path($cleanFile);

        if (function_exists('public_path')) {
            $paths[] = public_path($file);
            $paths[] = public_path($cleanFile);

            if (!str_starts_with(str_replace('\\', '/', $cleanFile), 'docs/')) {
                $paths[] = public_path('docs/' . $cleanFile);
            }
        }

        $cwd = getcwd();
        if (is_string($cwd) && $cwd !== '') {
            $paths[] = $cwd . DIRECTORY_SEPARATOR . $file;
            $paths[] = $cwd . DIRECTORY_SEPARATOR . $cleanFile;
        }

        return array_values(array_unique($paths));
    }

    private function isAbsolutePath(string $path): bool
    {
        if (str_starts_with($path, '/')) {
            return true;
        }

        // Windows absolute path: C:\... or C:/...
        return strlen($path) >= 3
            && ctype_alpha($path[0])
            && $path[1] === ':'
            && ($path[2] === '\\' || $path[2] === '/');
    }

    private function ensureSalesJournal(): ?int
    {
        if (!Schema::hasTable('journal_comptables')) {
            return null;
        }

        $existing = DB::table('journal_comptables')->where('code', 'VT')->value('id');
        if ($existing !== null) {
            return (int) $existing;
        }

        $inserted = DB::table('journal_comptables')->insert([
            'code' => 'VT',
            'libelle' => 'Journal des ventes',
            'type' => 'vente',
            'description' => 'Journal des ventes importé',
            'couleur' => '#198754',
            'icone' => 'fas fa-file-invoice-dollar',
            'actif' => true,
            'systeme' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$inserted) {
            return null;
        }

        $id = DB::table('journal_comptables')->where('code', 'VT')->value('id');

        return $id !== null ? (int) $id : null;
    }

    private function buildHeaderMap($sheet): array
    {
        $map = [];

        for ($col = 'A'; $col <= 'Z'; $col++) {
            $raw = (string) $sheet->getCell($col . '1')->getValue();
            $normalized = $this->normalizeHeader($raw);

            if ($normalized === '') {
                continue;
            }

            if (str_contains($normalized, 'date')) {
                $map['date'] = $col;
            } elseif ($normalized === 'jl') {
                $map['jl'] = $col;
            } elseif (str_contains($normalized, 'piece')) {
                $map['piece'] = $col;
            } elseif (str_contains($normalized, 'compte g')) {
                $map['compte_g'] = $col;
            } elseif (str_contains($normalized, 'compte tiers')) {
                $map['compte_tiers'] = $col;
            } elseif (str_contains($normalized, 'libelle')) {
                $map['libelle'] = $col;
            } elseif (str_contains($normalized, 'debit')) {
                $map['debit'] = $col;
            } elseif (str_contains($normalized, 'credit')) {
                $map['credit'] = $col;
            }
        }

        return $map;
    }

    private function isExpectedOrderedLayout($sheet): bool
    {
        $expected = [
            'A' => 'jl',
            'B' => 'date',
            'C' => 'n piece',
            'D' => 'compte g',
            'E' => 'compte tiers',
            'F' => 'libelle',
            'G' => 'debit',
            'H' => 'credit',
        ];

        foreach ($expected as $col => $label) {
            $raw = (string) $sheet->getCell($col . '1')->getValue();
            $normalized = $this->normalizeHeader($raw);

            // Compatibilite pour "N° PIECE" / "No PIECE" / "N PIECE"
            if ($label === 'n piece') {
                if (!str_contains($normalized, 'piece')) {
                    return false;
                }
                continue;
            }

            if ($normalized !== $label) {
                return false;
            }
        }

        return true;
    }

    private function normalizeHeader(string $value): string
    {
        $value = trim(mb_strtolower($value));
        $value = str_replace(['°', 'º', '_', '-'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?: '';

        return trim($value);
    }

    private function parseAmount(string $value): float
    {
        $value = trim($value);
        if ($value === '') {
            return 0.0;
        }

        $value = str_replace([' ', "\xc2\xa0"], '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function parseDate(mixed $rawDate): ?string
    {
        if ($rawDate === null || $rawDate === '') {
            return null;
        }

        if (is_numeric($rawDate)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($rawDate))->toDateString();
        }

        $value = trim((string) $rawDate);
        if ($value === '') {
            return null;
        }

        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->toDateString();
            } catch (\Throwable) {
                // continue
            }
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractClientName(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $name = preg_replace('/^[0-9A-Z]+\s*/', '', $raw);
        $name = trim((string) $name);

        return $name !== '' ? $name : $raw;
    }

    private function resolveClientId(?string $clientName): ?int
    {
        if ($clientName === null || trim($clientName) === '' || !Schema::hasTable('clients')) {
            return null;
        }

        $nameCols = ['nom', 'raison_sociale', 'company_name', 'contact_nom'];
        foreach ($nameCols as $col) {
            if (!Schema::hasColumn('clients', $col)) {
                continue;
            }

            $id = DB::table('clients')
                ->whereRaw('LOWER(TRIM(' . $col . ')) = ?', [mb_strtolower(trim($clientName))])
                ->value('id');

            if ($id !== null) {
                return (int) $id;
            }
        }

        return null;
    }

    private function resolveNumeroColumn(): ?string
    {
        if (Schema::hasColumn('factures', 'numero')) {
            return 'numero';
        }

        if (Schema::hasColumn('factures', 'numero_facture')) {
            return 'numero_facture';
        }

        return null;
    }

    private function buildFacturePayload(
        string $numero,
        string $dateFacture,
        float $montantHt,
        float $montantTva,
        float $montantTtc,
        ?int $clientId,
        ?string $clientName,
        string $designation
    ): array {
        $payload = [];

        if (Schema::hasColumn('factures', 'numero')) {
            $payload['numero'] = $numero;
        }
        if (Schema::hasColumn('factures', 'numero_facture')) {
            $payload['numero_facture'] = $numero;
        }

        if (Schema::hasColumn('factures', 'date_facture')) {
            $payload['date_facture'] = $dateFacture;
        }
        if (Schema::hasColumn('factures', 'date_facturation')) {
            $payload['date_facturation'] = $dateFacture;
        }

        if ($clientId !== null && Schema::hasColumn('factures', 'client_id')) {
            $payload['client_id'] = $clientId;
        }
        if ($clientName !== null && Schema::hasColumn('factures', 'client_nom')) {
            $payload['client_nom'] = $clientName;
        }

        if (Schema::hasColumn('factures', 'designation')) {
            $payload['designation'] = $designation;
        }

        if (Schema::hasColumn('factures', 'montant_ht')) {
            $payload['montant_ht'] = $montantHt;
        }
        if (Schema::hasColumn('factures', 'montant_tva')) {
            $payload['montant_tva'] = $montantTva;
        }
        if (Schema::hasColumn('factures', 'tva')) {
            $payload['tva'] = $montantTva;
        }
        if (Schema::hasColumn('factures', 'tva_taux')) {
            $payload['tva_taux'] = $montantHt > 0 ? round(($montantTva / $montantHt) * 100, 2) : 18.0;
        }
        if (Schema::hasColumn('factures', 'montant_ttc')) {
            $payload['montant_ttc'] = $montantTtc;
        }
        if (Schema::hasColumn('factures', 'montant_restant')) {
            $payload['montant_restant'] = $montantTtc;
        }

        if (Schema::hasColumn('factures', 'statut')) {
            $payload['statut'] = 'en_attente';
        }

        if (Schema::hasColumn('factures', 'created_by') && !isset($payload['created_by'])) {
            $payload['created_by'] = 'system_import';
        }

        if (Schema::hasColumn('factures', 'updated_at')) {
            $payload['updated_at'] = now();
        }
        if (Schema::hasColumn('factures', 'created_at')) {
            $payload['created_at'] = now();
        }

        return $payload;
    }
}
