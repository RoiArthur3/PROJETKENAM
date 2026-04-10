<?php

namespace App\Http\Controllers;

use App\Imports\HistoricalDataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HistoricalImportController extends Controller
{
    public function index()
    {
        $availableFiles = $this->availableHistoricalFiles();

        return view('imports.historical', [
            'entities' => HistoricalDataImport::supportedEntities(),
            'defaultEntity' => 'fournisseurs',
            'availableFiles' => $availableFiles,
            'importBlueprints' => $this->importBlueprints(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity' => 'required|in:' . implode(',', array_keys(HistoricalDataImport::supportedEntities())),
            'mode_import' => 'required|in:creer,maj,creer_maj',
            'fichier' => 'nullable|file|mimes:xlsx,xls,csv|required_without:doc_file',
            'doc_file' => 'nullable|string|required_without:fichier',
            'action' => 'nullable|in:import,preview',
        ]);

        $import = new HistoricalDataImport(
            $request->string('entity')->toString(),
            $request->string('mode_import')->toString()
        );
        $entity = $request->string('entity')->toString();
        $action = (string) $request->input('action', 'import');

        $docFile = trim((string) $request->input('doc_file', ''));
        $sources = $this->availableHistoricalFiles();
        $selectedSource = $docFile !== '' ? ($sources[$docFile] ?? null) : null;

        if ($docFile !== '' && !$selectedSource) {
            return back()->withInput()->with('error', 'Le fichier selectionne n\'est pas autorise ou introuvable.');
        }

        try {
            $filePath = $selectedSource
                ? $selectedSource['path']
                : $request->file('fichier')->getRealPath();

            [$expectedHeaders, $foundHeaders, $isValid] = $this->inspectHeaders($filePath, $entity);

            if ($action === 'preview') {
                $preview = [
                    'entity' => $entity,
                    'source' => $selectedSource['label'] ?? 'upload',
                    'is_valid' => $isValid,
                    'expected' => $expectedHeaders,
                    'found' => $foundHeaders,
                ];

                $message = $isValid
                    ? 'Pre-controle reussi: l\'ordre des colonnes est conforme.'
                    : 'Pre-controle echoue: l\'ordre des colonnes ne correspond pas au modele attendu.';

                return back()
                    ->withInput()
                    ->with($isValid ? 'success' : 'warning', $message)
                    ->with('header_preview', $preview);
            }

            if (!$isValid) {
                throw new \InvalidArgumentException(
                    "Ordre des colonnes invalide. Attendu: [" . implode(', ', $expectedHeaders) . "] | Trouve: [" . implode(', ', $foundHeaders) . "]"
                );
            }

            Excel::import($import, $filePath);

            $message = sprintf(
                'Import historique termine. %d lignes lues, %d creees, %d mises a jour, %d ignorees.',
                $import->rowCount,
                $import->createdCount,
                $import->updatedCount,
                $import->skippedCount
            );

            if (!empty($import->errors)) {
                return back()
                    ->with('warning', $message)
                    ->with('import_errors', array_slice($import->errors, 0, 50));
            }

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->with('error', 'Echec import: ' . $e->getMessage());
        }
    }

    public function template(string $entity)
    {
        if (!array_key_exists($entity, HistoricalDataImport::supportedEntities())) {
            abort(404);
        }

        $headers = HistoricalDataImport::templateHeaders($entity);
        $filename = 'template-import-' . $entity . '.csv';

        $callback = static function () use ($headers): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, $headers, ';');
            fclose($stream);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string, array{path:string,label:string}>
     */
    private function availableHistoricalFiles(): array
    {
        $directories = [
            ['path' => base_path('docs'), 'label' => 'docs'],
            ['path' => public_path('docs'), 'label' => 'public/docs'],
            ['path' => base_path('imports/excel-a-traiter'), 'label' => 'imports/excel-a-traiter'],
        ];

        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        $files = [];

        foreach ($directories as $directory) {
            $dirPath = $directory['path'];
            if (!File::isDirectory($dirPath)) {
                continue;
            }

            foreach (File::files($dirPath) as $file) {
                $extension = strtolower($file->getExtension());
                if (!in_array($extension, $allowedExtensions, true)) {
                    continue;
                }

                $key = $directory['label'] . '/' . $file->getFilename();
                $files[$key] = [
                    'path' => $file->getPathname(),
                    'label' => $key,
                ];
            }
        }

        ksort($files);

        return $files;
    }

    private function validateStrictHeaders(string $filePath, string $entity): void
    {
        [$expectedHeaders, $foundHeaders, $isValid] = $this->inspectHeaders($filePath, $entity);

        if ($isValid) {
            return;
        }

        throw new \InvalidArgumentException(
            "Ordre des colonnes invalide. Attendu: [" . implode(', ', $expectedHeaders) . "] | Trouve: [" . implode(', ', $foundHeaders) . "]"
        );
    }

    /**
     * @return array{0: string[], 1: string[], 2: bool}
     */
    private function inspectHeaders(string $filePath, string $entity): array
    {
        $expectedHeaders = HistoricalDataImport::templateHeaders($entity);
        $foundHeaders = $this->extractFirstRowHeaders($filePath);

        $expectedNormalized = array_map(fn (string $header): string => $this->normalizeHeader($header), $expectedHeaders);
        $foundNormalized = array_map(fn (string $header): string => $this->normalizeHeader($header), $foundHeaders);

        return [$expectedHeaders, $foundHeaders, $expectedNormalized === $foundNormalized];
    }

    /**
     * @return string[]
     */
    private function extractFirstRowHeaders(string $filePath): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);

        $sheet = $spreadsheet->getSheet(0);
        $highestColumn = $sheet->getHighestColumn();

        $headers = [];
        foreach ($sheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, false)[0] ?? [] as $value) {
            $header = trim((string) $value);
            $header = preg_replace('/^\xEF\xBB\xBF/u', '', $header) ?? $header;
            if ($header !== '') {
                $headers[] = $header;
            }
        }

        if (count($headers) === 1 && str_contains($headers[0], ';')) {
            $headers = array_values(array_filter(array_map('trim', explode(';', $headers[0])), static fn (string $part): bool => $part !== ''));
        }

        return $headers;
    }

    private function normalizeHeader(string $header): string
    {
        return strtolower(trim($header));
    }

    private function importBlueprints(): array
    {
        return [
            'fournisseurs' => [
                'label' => 'Fournisseurs',
                'target_table' => 'fournisseurs',
                'target_page' => '/imports/historique',
                'ordered_columns' => [
                    ['name' => 'reference', 'required' => true, 'description' => 'Identifiant unique fournisseur'],
                    ['name' => 'raison_sociale', 'required' => true, 'description' => 'Nom de la societe'],
                    ['name' => 'email', 'required' => false, 'description' => 'Email principal'],
                    ['name' => 'telephone', 'required' => false, 'description' => 'Numero de contact'],
                    ['name' => 'ville', 'required' => false, 'description' => 'Ville'],
                    ['name' => 'pays', 'required' => false, 'description' => 'Pays'],
                    ['name' => 'est_actif', 'required' => false, 'description' => '1/0, oui/non, true/false'],
                ],
            ],
            'clients' => [
                'label' => 'Clients',
                'target_table' => 'clients',
                'target_page' => '/imports/historique',
                'ordered_columns' => [
                    ['name' => 'code', 'required' => true, 'description' => 'Code client unique'],
                    ['name' => 'raison_sociale', 'required' => true, 'description' => 'Nom du client'],
                    ['name' => 'contact_nom', 'required' => false, 'description' => 'Contact principal'],
                    ['name' => 'email', 'required' => false, 'description' => 'Email principal'],
                    ['name' => 'telephone', 'required' => false, 'description' => 'Telephone principal'],
                    ['name' => 'ville', 'required' => false, 'description' => 'Ville'],
                    ['name' => 'pays', 'required' => false, 'description' => 'Pays'],
                    ['name' => 'actif', 'required' => false, 'description' => '1/0, oui/non, true/false'],
                ],
            ],
            'factures' => [
                'label' => 'Factures',
                'target_table' => 'factures',
                'target_page' => '/imports/historique',
                'ordered_columns' => [
                    ['name' => 'numero', 'required' => true, 'description' => 'Numero unique de facture'],
                    ['name' => 'client_id', 'required' => true, 'description' => 'ID client existant'],
                    ['name' => 'date_facture', 'required' => true, 'description' => 'Date facture (YYYY-MM-DD ou date Excel)'],
                    ['name' => 'montant_ht', 'required' => false, 'description' => 'Montant hors taxe'],
                    ['name' => 'tva', 'required' => false, 'description' => 'Montant TVA'],
                    ['name' => 'montant_ttc', 'required' => false, 'description' => 'Montant TTC'],
                    ['name' => 'statut', 'required' => false, 'description' => 'Statut facture'],
                ],
            ],
            'plan_comptable' => [
                'label' => 'Plan comptable',
                'target_table' => 'comptes_comptables',
                'target_page' => '/comptabilite/import-param-compta',
                'ordered_columns' => [
                    ['name' => 'compte', 'required' => true, 'description' => 'Code compte general'],
                    ['name' => 'intitule', 'required' => true, 'description' => 'Libelle du compte'],
                    ['name' => 'type', 'required' => false, 'description' => 'Type (G/C/F)'],
                ],
                'sheets' => [
                    ['name' => 'PCE_G-IMP', 'required_columns' => ['compte', 'intitule', 'type']],
                    ['name' => 'CODES JOURNAUX', 'required_columns' => ['code', 'intitule', 'type', 'compte']],
                    ['name' => 'TIERS CLIENTS', 'required_columns' => ['intitule', 'num_ct', 'num_cg', 'type']],
                    ['name' => 'TIERS FOURNISSEURS', 'required_columns' => ['intitule', 'num_ct', 'num_cg', 'type']],
                    ['name' => 'PLAN ANALYTIQUE', 'required_columns' => ['activite', 'code_projet', 'nom_projet', 'famille', 'designation']],
                ],
            ],
        ];
    }

    public function audit()
    {
        $tables = [
            'fournisseurs' => [
                'label' => 'Fournisseurs',
                'count_col' => 'id',
                'name_col' => 'raison_sociale',
                'ref_col' => 'reference',
                'date_col' => 'updated_at',
                'amount_col' => null,
            ],
            'clients' => [
                'label' => 'Clients',
                'count_col' => 'id',
                'name_col' => 'raison_sociale',
                'ref_col' => 'code',
                'date_col' => 'updated_at',
                'amount_col' => null,
            ],
            'factures' => [
                'label' => 'Factures clients',
                'count_col' => 'id',
                'name_col' => 'numero',
                'ref_col' => 'numero',
                'date_col' => 'date_facture',
                'amount_col' => 'montant_ttc',
            ],
            'encaissements' => [
                'label' => 'Encaissements',
                'count_col' => 'id',
                'name_col' => 'reference',
                'ref_col' => 'reference',
                'date_col' => 'date_encaissement',
                'amount_col' => 'montant',
            ],
            'facture_fournisseurs' => [
                'label' => 'Factures fournisseurs',
                'count_col' => 'id',
                'name_col' => 'numero_facture',
                'ref_col' => 'reference',
                'date_col' => 'date_facture',
                'amount_col' => 'montant_ttc',
            ],
            'paiement_fournisseurs' => [
                'label' => 'Paiements fournisseurs',
                'count_col' => 'id',
                'name_col' => 'reference',
                'ref_col' => 'reference',
                'date_col' => 'date_paiement',
                'amount_col' => 'montant',
            ],
            'mouvements_caisse' => [
                'label' => 'Mouvements de caisse',
                'count_col' => 'id',
                'name_col' => 'description',
                'ref_col' => null,
                'date_col' => 'created_at',
                'amount_col' => 'montant',
            ],
            'comptes_comptables' => [
                'label' => 'Plan comptable',
                'count_col' => 'id',
                'name_col' => 'intitule',
                'ref_col' => 'code',
                'date_col' => 'updated_at',
                'amount_col' => null,
            ],
        ];

        $stats = [];
        foreach ($tables as $table => $meta) {
            if (!Schema::hasTable($table)) {
                $stats[$table] = ['label' => $meta['label'], 'exists' => false];
                continue;
            }

            $query = DB::table($table);
            $count = $query->count();
            $sum = $meta['amount_col'] ? (float) DB::table($table)->sum($meta['amount_col']) : null;

            $recent = DB::table($table)
                ->orderByDesc($meta['count_col'])
                ->limit(5)
                ->get();

            $stats[$table] = [
                'label'     => $meta['label'],
                'exists'    => true,
                'count'     => $count,
                'sum'       => $sum,
                'name_col'  => $meta['name_col'],
                'ref_col'   => $meta['ref_col'],
                'date_col'  => $meta['date_col'],
                'amount_col' => $meta['amount_col'],
                'recent'    => $recent,
            ];
        }

        // Integrity checks
        $integrity = [];

        if (Schema::hasTable('factures') && Schema::hasTable('clients')) {
            $orphanFactures = DB::table('factures')
                ->whereNotNull('client_id')
                ->whereNotExists(fn ($q) => $q->select('id')->from('clients')->whereColumn('clients.id', 'factures.client_id'))
                ->count();
            if ($orphanFactures > 0) {
                $integrity[] = ['type' => 'danger', 'message' => "{$orphanFactures} facture(s) client avec client_id introuvable dans la table clients."];
            }
        }

        if (Schema::hasTable('encaissements') && Schema::hasTable('factures')) {
            $orphanEnc = DB::table('encaissements')
                ->whereNotNull('facture_id')
                ->whereNotExists(fn ($q) => $q->select('id')->from('factures')->whereColumn('factures.id', 'encaissements.facture_id'))
                ->count();
            if ($orphanEnc > 0) {
                $integrity[] = ['type' => 'warning', 'message' => "{$orphanEnc} encaissement(s) avec facture_id non lié."];
            }
        }

        if (Schema::hasTable('paiement_fournisseurs') && Schema::hasTable('facture_fournisseurs')) {
            $orphanPf = DB::table('paiement_fournisseurs')
                ->whereNotNull('facture_id')
                ->whereNotExists(fn ($q) => $q->select('id')->from('facture_fournisseurs')->whereColumn('facture_fournisseurs.id', 'paiement_fournisseurs.facture_id'))
                ->count();
            if ($orphanPf > 0) {
                $integrity[] = ['type' => 'warning', 'message' => "{$orphanPf} paiement(s) fournisseur avec facture_id non liée."];
            }
        }

        if (Schema::hasTable('mouvements_caisse')) {
            $noDesc = DB::table('mouvements_caisse')->whereNull('description')->orWhere('description', '')->count();
            if ($noDesc > 0) {
                $integrity[] = ['type' => 'info', 'message' => "{$noDesc} mouvement(s) caisse sans description."];
            }
        }

        if (empty($integrity)) {
            $integrity[] = ['type' => 'success', 'message' => 'Aucun probleme d\'integrite detecte.'];
        }

        return view('imports.audit', compact('stats', 'integrity'));
    }
}
