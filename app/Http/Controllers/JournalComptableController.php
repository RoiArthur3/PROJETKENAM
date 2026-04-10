<?php

namespace App\Http\Controllers;

use App\Models\EcritureComptable;
use App\Models\JournalComptable;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JournalComptableController extends Controller
{
    public function grandJournal(Request $request)
    {
        $journaux = JournalComptable::query()
            ->orderBy('code')
            ->get();

        [$debut, $fin, $entries] = $this->buildFilteredJournalEntries($request, $journaux);

        $stats = [
            'total' => $entries->count(),
            'total_debit' => $entries->sum('debit'),
            'total_credit' => $entries->sum('credit'),
            'solde' => $entries->sum('debit') - $entries->sum('credit'),
            'journaux_actifs' => $journaux->where('actif', true)->count(),
        ];

        $parJournal = $entries
            ->groupBy('journal_code')
            ->map(fn (Collection $group) => [
                'journal' => $group->first()['journal_libelle'],
                'count' => $group->count(),
                'montant' => $group->sum('montant'),
            ])
            ->sortKeys();

        return view('comptabilite.ecritures', [
            'entries' => $entries,
            'stats' => $stats,
            'journaux' => $journaux,
            'parJournal' => $parJournal,
            'filters' => [
                'date_debut' => $debut->format('Y-m-d'),
                'date_fin' => $fin->format('Y-m-d'),
                'journal' => $request->string('journal')->toString(),
                'source' => $request->string('source')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function exportGrandJournalExcel(Request $request)
    {
        $journaux = JournalComptable::query()->orderBy('code')->get();
        [$debut, $fin, $entries] = $this->buildFilteredJournalEntries($request, $journaux);

        $filename = 'grand-journal-' . $debut->format('Ymd') . '-' . $fin->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($entries): void {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['Date', 'Journal', 'Reference', 'Libelle', 'Compte Debit', 'Compte Credit', 'Montant', 'Source', 'Piece'], ';');

            foreach ($entries as $entry) {
                fputcsv($stream, [
                    $entry['date']->format('d/m/Y'),
                    $entry['journal_code'] . ' - ' . $entry['journal_libelle'],
                    $entry['reference'],
                    $entry['libelle'],
                    $entry['compte_debit'],
                    $entry['compte_credit'],
                    number_format((float) $entry['montant'], 2, '.', ''),
                    $entry['source_type'],
                    $entry['piece'],
                ], ';');
            }

            fclose($stream);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportGrandJournalPdf(Request $request)
    {
        $journaux = JournalComptable::query()->orderBy('code')->get();
        [$debut, $fin, $entries] = $this->buildFilteredJournalEntries($request, $journaux);

        $stats = [
            'total' => $entries->count(),
            'total_debit' => $entries->sum('debit'),
            'total_credit' => $entries->sum('credit'),
            'solde' => $entries->sum('debit') - $entries->sum('credit'),
        ];

        $pdf = Pdf::loadView('comptabilite.rapports.grand-journal-pdf', [
            'entries' => $entries,
            'stats' => $stats,
            'debut' => $debut,
            'fin' => $fin,
            'filters' => [
                'journal' => $request->string('journal')->toString(),
                'source' => $request->string('source')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('grand-journal-' . $debut->format('Ymd') . '-' . $fin->format('Ymd') . '.pdf');
    }

    public function createEcriture()
    {
        return view('comptabilite.ecritures-create', [
            'journaux' => JournalComptable::query()->where('actif', true)->orderBy('code')->get(),
            'ecriture' => null,
        ]);
    }

    public function storeEcriture(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:100',
            'journal_id' => 'required|exists:journal_comptables,id',
            'piece_comptable' => 'nullable|string|max:100',
            'libelle' => 'required|string|max:255',
            'compte_debit' => 'required|string|max:50',
            'compte_credit' => 'required|string|max:50|different:compte_debit',
            'montant' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'piece_jointe' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        $cheminFichier = null;
        $nomFichier = null;
        if ($request->hasFile('piece_jointe')) {
            $file = $request->file('piece_jointe');
            $nomFichier = $file->getClientOriginalName();
            $cheminFichier = $file->store('ecritures_comptables', 'public');
        }

        EcritureComptable::create([
            'journal_id' => $data['journal_id'],
            'date' => $data['date'],
            'reference' => $data['reference'],
            'piece_comptable' => $data['piece_comptable'] ?? null,
            'piece_jointe' => $cheminFichier,
            'piece_jointe_nom' => $nomFichier,
            'libelle' => $data['libelle'],
            'compte_debit' => $data['compte_debit'],
            'compte_credit' => $data['compte_credit'],
            'montant' => $data['montant'],
            'description' => $data['description'] ?? null,
            'source_type' => 'manual',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('comptabilite.ecritures.index')
            ->with('success', 'Écriture comptable enregistrée.');
    }

    public function editEcriture(EcritureComptable $ecriture)
    {
        return view('comptabilite.ecritures-create', [
            'journaux' => JournalComptable::query()->where('actif', true)->orderBy('code')->get(),
            'ecriture' => $ecriture,
        ]);
    }

    public function updateEcriture(Request $request, EcritureComptable $ecriture)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:100',
            'journal_id' => 'required|exists:journal_comptables,id',
            'piece_comptable' => 'nullable|string|max:100',
            'libelle' => 'required|string|max:255',
            'compte_debit' => 'required|string|max:50',
            'compte_credit' => 'required|string|max:50|different:compte_debit',
            'montant' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        $updateData = array_merge(
            collect($data)->except('piece_jointe')->toArray(),
            ['updated_by' => Auth::id()]
        );

        if ($request->hasFile('piece_jointe')) {
            // Supprimer l'ancien fichier si présent
            if ($ecriture->piece_jointe) {
                Storage::disk('public')->delete($ecriture->piece_jointe);
            }
            $file = $request->file('piece_jointe');
            $updateData['piece_jointe'] = $file->store('ecritures_comptables', 'public');
            $updateData['piece_jointe_nom'] = $file->getClientOriginalName();
        }

        $ecriture->update($updateData);

        return redirect()
            ->route('comptabilite.ecritures.index')
            ->with('success', 'Écriture comptable mise à jour.');
    }

    public function destroyEcriture(EcritureComptable $ecriture)
    {
        $ecriture->delete();

        return redirect()
            ->route('comptabilite.ecritures.index')
            ->with('success', 'Écriture comptable supprimée.');
    }

    public function journauxIndex()
    {
        $journaux = JournalComptable::query()
            ->withCount('ecritures')
            ->orderBy('code')
            ->get();

        return view('comptabilite.journaux.index', compact('journaux'));
    }

    public function storeJournal(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10|unique:journal_comptables,code',
            'libelle' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'couleur' => 'nullable|string|max:20',
            'icone' => 'nullable|string|max:50',
            'actif' => 'nullable|boolean',
        ]);

        JournalComptable::create([
            'code' => strtoupper($data['code']),
            'libelle' => $data['libelle'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'couleur' => $data['couleur'] ?? '#0d6efd',
            'icone' => $data['icone'] ?? 'fas fa-book',
            'actif' => $request->boolean('actif', true),
            'systeme' => false,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Journal créé avec succès.');
    }

    public function updateJournal(Request $request, JournalComptable $journal)
    {
        $data = $request->validate([
            'libelle' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'couleur' => 'nullable|string|max:20',
            'icone' => 'nullable|string|max:50',
            'actif' => 'nullable|boolean',
        ]);

        $journal->update([
            'libelle' => $data['libelle'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'couleur' => $data['couleur'] ?? $journal->couleur,
            'icone' => $data['icone'] ?? $journal->icone,
            'actif' => $request->boolean('actif'),
        ]);

        return redirect()
            ->route('comptabilite.journaux.index')
            ->with('success', 'Journal mis à jour.');
    }

    public function importVentesForm()
    {
        return view('comptabilite.journaux.import-ventes');
    }

    public function importVentesStore(Request $request)
    {
        $data = $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:csv,xls,xlsx|max:20480',
            'sheet' => 'nullable|string|max:100',
            'dry_run' => 'nullable|boolean',
        ]);

        $sheet = (string) ($data['sheet'] ?? '');
        $dryRun = $request->boolean('dry_run');

        $results = [];
        $errors = 0;

        foreach ($request->file('files', []) as $uploadedFile) {
            $safeName = Str::random(8) . '-' . preg_replace('/[^A-Za-z0-9._-]/', '_', $uploadedFile->getClientOriginalName());
            $relativePath = $uploadedFile->storeAs('imports/journaux', $safeName);
            $absolutePath = storage_path('app/' . $relativePath);

            try {
                $params = [
                    'file' => $absolutePath,
                    '--sheet' => $sheet,
                ];

                if ($dryRun) {
                    $params['--dry-run'] = true;
                }

                $exitCode = Artisan::call('journal:ventes:import', $params);
                $output = trim(Artisan::output());

                $results[] = [
                    'file' => $uploadedFile->getClientOriginalName(),
                    'exit_code' => $exitCode,
                    'success' => $exitCode === 0,
                    'message' => $output !== '' ? $output : ($exitCode === 0 ? 'Import terminé.' : 'Erreur import.'),
                ];

                if ($exitCode !== 0) {
                    $errors++;
                }
            } catch (\Throwable $e) {
                $errors++;
                $results[] = [
                    'file' => $uploadedFile->getClientOriginalName(),
                    'exit_code' => 1,
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            } finally {
                if (Storage::disk('local')->exists($relativePath)) {
                    Storage::disk('local')->delete($relativePath);
                }
            }
        }

        $successCount = count($results) - $errors;
        $flashMessage = $errors === 0
            ? "Import terminé: {$successCount} fichier(s) traité(s) avec succès."
            : "Import partiel: {$successCount} succès, {$errors} échec(s).";

        return back()->with([
            'success' => $flashMessage,
            'import_results' => $results,
        ]);
    }

    private function resolvePeriod(Request $request): array
    {
        $debut = $request->filled('date_debut')
            ? Carbon::parse($request->input('date_debut'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $fin = $request->filled('date_fin')
            ? Carbon::parse($request->input('date_fin'))->endOfDay()
            : Carbon::now()->endOfMonth();

        return [$debut, $fin];
    }

    private function buildFilteredJournalEntries(Request $request, Collection $journaux): array
    {
        [$debut, $fin] = $this->resolvePeriod($request);
        $entries = $this->buildGrandJournalEntries($request, $debut, $fin, $journaux);

        return [$debut, $fin, $entries];
    }

    private function buildGrandJournalEntries(Request $request, Carbon $debut, Carbon $fin, Collection $journaux): Collection
    {
        $journalMap = $journaux->keyBy('code');

        $entries = collect()
            ->merge($this->manualEntries($debut, $fin, $journalMap))
            ->merge($this->factureEntries($debut, $fin, $journalMap))
            ->merge($this->encaissementEntries($debut, $fin, $journalMap))
            ->merge($this->depenseEntries($debut, $fin, $journalMap))
            ->merge($this->vehicleEntries($debut, $fin, $journalMap));

        if ($request->filled('journal')) {
            $entries = $entries->where('journal_code', $request->input('journal'));
        }

        if ($request->filled('source')) {
            $entries = $entries->where('source_type', $request->input('source'));
        }

        if ($request->filled('search')) {
            $search = mb_strtolower($request->input('search'));
            $entries = $entries->filter(function (array $entry) use ($search) {
                return str_contains(mb_strtolower($entry['reference']), $search)
                    || str_contains(mb_strtolower($entry['libelle']), $search)
                    || str_contains(mb_strtolower($entry['piece']), $search)
                    || str_contains(mb_strtolower($entry['compte_debit']), $search)
                    || str_contains(mb_strtolower($entry['compte_credit']), $search);
            });
        }

        return $entries
            ->sortByDesc(fn (array $entry) => sprintf('%s-%010d', $entry['date']->format('YmdHis'), $entry['sort_id']))
            ->values();
    }

    private function manualEntries(Carbon $debut, Carbon $fin, Collection $journalMap): Collection
    {
        if (!Schema::hasTable('ecritures_comptables')) {
            return collect();
        }

        return EcritureComptable::query()
            ->with('journal')
            ->whereBetween('date', [$debut, $fin])
            ->orderByDesc('date')
            ->get()
            ->map(function (EcritureComptable $ecriture) use ($journalMap) {
                $code = $ecriture->journal?->code ?? 'OD';
                $sourceType = (string) ($ecriture->source_type ?: 'manual');
                return $this->makeEntry(
                    date: Carbon::parse($ecriture->date),
                    journalCode: $code,
                    journalMap: $journalMap,
                    reference: $ecriture->reference,
                    libelle: $ecriture->libelle,
                    compteDebit: $ecriture->compte_debit,
                    compteCredit: $ecriture->compte_credit,
                    montant: (float) $ecriture->montant,
                    piece: $ecriture->piece_comptable ?? '',
                    sourceType: $sourceType,
                    sourceId: (int) $ecriture->id,
                    editable: $sourceType === 'manual',
                );
            });
    }

    private function factureEntries(Carbon $debut, Carbon $fin, Collection $journalMap): Collection
    {
        if (!Schema::hasTable('factures')) {
            return collect();
        }

        return DB::table('factures')
            ->whereBetween('date_facture', [$debut, $fin])
            ->where(function ($query) {
                $query->whereNull('statut')
                    ->orWhereNotIn('statut', ['annulee', 'annulée']);
            })
            ->get(['id', 'numero', 'date_facture', 'montant_ttc', 'description'])
            ->map(fn ($row) => $this->makeEntry(
                date: Carbon::parse($row->date_facture),
                journalCode: 'VT',
                journalMap: $journalMap,
                reference: (string) ($row->numero ?? ('FAC-' . $row->id)),
                libelle: (string) ($row->description ?: 'Facture client'),
                compteDebit: '411000',
                compteCredit: '707000',
                montant: (float) $row->montant_ttc,
                piece: (string) ($row->numero ?? ''),
                sourceType: 'facture',
                sourceId: (int) $row->id,
            ));
    }

    private function encaissementEntries(Carbon $debut, Carbon $fin, Collection $journalMap): Collection
    {
        if (!Schema::hasTable('encaissements')) {
            return collect();
        }

        return DB::table('encaissements')
            ->whereBetween('date_encaissement', [$debut, $fin])
            ->where(function ($query) {
                $query->whereNull('statut')->orWhere('statut', 'valide');
            })
            ->get(['id', 'reference', 'date_encaissement', 'montant', 'description', 'mode_paiement'])
            ->map(function ($row) use ($journalMap) {
                $isCash = str_contains(mb_strtolower((string) $row->mode_paiement), 'cash')
                    || str_contains(mb_strtolower((string) $row->mode_paiement), 'espe');
                $journalCode = $isCash ? 'CA' : 'BQ';

                return $this->makeEntry(
                    date: Carbon::parse($row->date_encaissement),
                    journalCode: $journalCode,
                    journalMap: $journalMap,
                    reference: (string) ($row->reference ?? ('ENC-' . $row->id)),
                    libelle: (string) ($row->description ?: 'Encaissement client'),
                    compteDebit: $isCash ? '531000' : '512000',
                    compteCredit: '411000',
                    montant: (float) $row->montant,
                    piece: (string) ($row->reference ?? ''),
                    sourceType: 'encaissement',
                    sourceId: (int) $row->id,
                );
            });
    }

    private function depenseEntries(Carbon $debut, Carbon $fin, Collection $journalMap): Collection
    {
        if (!Schema::hasTable('depenses')) {
            return collect();
        }

        $columns = ['id', 'date_depense', 'montant', 'libelle'];

        return DB::table('depenses')
            ->whereBetween('date_depense', [$debut, $fin])
            ->whereNull('deleted_at')
            ->get($columns)
            ->map(fn ($row) => $this->makeEntry(
                date: Carbon::parse($row->date_depense),
                journalCode: 'AC',
                journalMap: $journalMap,
                reference: 'DEP-' . $row->id,
                libelle: (string) ($row->libelle ?: 'Dépense'),
                compteDebit: '601000',
                compteCredit: '531000',
                montant: (float) $row->montant,
                piece: 'DEP-' . $row->id,
                sourceType: 'depense',
                sourceId: (int) $row->id,
            ));
    }

    private function vehicleEntries(Carbon $debut, Carbon $fin, Collection $journalMap): Collection
    {
        if (!Schema::hasTable('vehicle_financial_entries')) {
            return collect();
        }

        // Déterminer les colonnes à utiliser (schéma ancien vs nouveau)
        $hasNewSchema = Schema::hasColumn('vehicle_financial_entries', 'entry_date') && 
                        Schema::hasColumn('vehicle_financial_entries', 'entry_type');
        $dateColumn = $hasNewSchema ? 'entry_date' : 'transaction_date';
        $typeColumn = $hasNewSchema ? 'entry_type' : 'type';
        $amountColumn = Schema::hasColumn('vehicle_financial_entries', 'amount') ? 'amount' : 'montant';
        
        // Sélectionner les colonnes disponibles
        $selectColumns = ['id', $dateColumn, $typeColumn, $amountColumn];
        
        if (Schema::hasColumn('vehicle_financial_entries', 'label')) {
            $selectColumns[] = 'label';
        } elseif (Schema::hasColumn('vehicle_financial_entries', 'description')) {
            $selectColumns[] = 'description as label';
        }
        
        if (Schema::hasColumn('vehicle_financial_entries', 'source_module')) {
            $selectColumns[] = 'source_module';
        }
        
        if (Schema::hasColumn('vehicle_financial_entries', 'category')) {
            $selectColumns[] = 'category';
        } elseif (Schema::hasColumn('vehicle_financial_entries', 'categorie')) {
            $selectColumns[] = 'categorie as category';
        }

        $rows = DB::table('vehicle_financial_entries')
            ->whereBetween($dateColumn, [$debut, $fin])
            ->get($selectColumns);

        return $rows->map(function ($row) use ($journalMap, $typeColumn, $dateColumn, $amountColumn) {
            // Normaliser la valeur de type (revenue/expense ou revenue/charge)
            $typeValue = mb_strtolower((string) $row->{$typeColumn});
            $isRevenue = in_array($typeValue, ['revenue', 'produit', 'income']);
            
            $sourceModule = isset($row->source_module) ? mb_strtolower((string) $row->source_module) : '';
            $isCash = str_contains($sourceModule, 'cash') || str_contains($sourceModule, 'tresorerie');

            return $this->makeEntry(
                date: Carbon::parse($row->{$dateColumn}),
                journalCode: $isRevenue ? 'VT' : ($isCash ? 'CA' : 'AC'),
                journalMap: $journalMap,
                reference: 'VEH-' . $row->id,
                libelle: (string) (isset($row->label) && $row->label ? $row->label : (isset($row->category) && $row->category ? $row->category : 'Mouvement véhicule')),
                compteDebit: $isRevenue ? '411000' : '602000',
                compteCredit: $isRevenue ? '707100' : ($isCash ? '531000' : '401000'),
                montant: (float) $row->{$amountColumn},
                piece: 'VEH-' . $row->id,
                sourceType: $isRevenue ? 'vehicule_revenue' : 'vehicule_expense',
                sourceId: (int) $row->id,
            );
        });
    }

    // ─── AJAX endpoint: detail d'une écriture ────────────────────────────────

    public function detailEcriture(Request $request): JsonResponse
    {
        $sourceType = (string) $request->query('source_type', '');
        $sourceId   = (int)   $request->query('source_id',   0);

        if ($sourceType === '' || $sourceId <= 0) {
            return response()->json(['error' => 'Paramètres invalides.'], 400);
        }

        $detail = match ($sourceType) {
            'manual',
            'journal_vente_import' => $this->detailManual($sourceId),
            'facture'          => $this->detailFacture($sourceId),
            'encaissement'     => $this->detailEncaissement($sourceId),
            'depense'          => $this->detailDepense($sourceId),
            'vehicule_revenue',
            'vehicule_expense' => $this->detailVehicle($sourceId),
            default            => null,
        };

        if ($detail === null) {
            return response()->json(['error' => 'Document source introuvable.'], 404);
        }

        // Audit trail
        $modelMap = [
            'manual'           => 'EcritureComptable',
            'journal_vente_import' => 'EcritureComptable',
            'facture'          => 'Facture',
            'encaissement'     => 'Encaissement',
            'depense'          => 'Depense',
            'vehicule_revenue' => 'VehicleFinancialEntry',
            'vehicule_expense' => 'VehicleFinancialEntry',
        ];

        $auditTrail = [];
        if (Schema::hasTable('audit_logs') && isset($modelMap[$sourceType])) {
            $modelClass = $modelMap[$sourceType];
            $auditTrail = DB::table('audit_logs')
                ->where('model_id', $sourceId)
                ->where('model_type', 'LIKE', '%' . $modelClass . '%')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(['action', 'old_values', 'new_values', 'ip_address', 'created_at', 'user_id'])
                ->map(function ($log) {
                    $userName = DB::table('users')->where('id', $log->user_id)->value('name');
                    return [
                        'action'     => $log->action,
                        'old_values' => $log->old_values ? json_decode($log->old_values, true) : null,
                        'new_values' => $log->new_values ? json_decode($log->new_values, true) : null,
                        'ip_address' => $log->ip_address,
                        'par'        => $userName ?? 'Système',
                        'date'       => $log->created_at,
                    ];
                })->toArray();
        }

        return response()->json([
            'detail'      => $detail,
            'audit_trail' => $auditTrail,
        ]);
    }

    private function detailManual(int $id): ?array
    {
        if (!Schema::hasTable('ecritures_comptables')) {
            return null;
        }

        $row = DB::table('ecritures_comptables')
            ->where('ecritures_comptables.id', $id)
            ->leftJoin('journal_comptables', 'journal_comptables.id', '=', 'ecritures_comptables.journal_id')
            ->leftJoin('users as u_cree',    'u_cree.id',    '=', 'ecritures_comptables.created_by')
            ->leftJoin('users as u_modifie', 'u_modifie.id', '=', 'ecritures_comptables.updated_by')
            ->first([
                'ecritures_comptables.reference',
                'ecritures_comptables.date',
                'ecritures_comptables.libelle',
                'ecritures_comptables.description',
                'ecritures_comptables.compte_debit',
                'ecritures_comptables.compte_credit',
                'ecritures_comptables.montant',
                'ecritures_comptables.piece_comptable',
                'ecritures_comptables.created_at',
                'ecritures_comptables.updated_at',
                'journal_comptables.code as journal_code',
                'journal_comptables.libelle as journal_libelle',
                'u_cree.name as cree_par',
                'u_modifie.name as modifie_par',
            ]);

        if (!$row) {
            return null;
        }

        return [
            'type'          => 'Écriture manuelle',
            'reference'     => $row->reference,
            'date'          => $row->date,
            'journal'       => trim($row->journal_code . ' – ' . $row->journal_libelle, ' –'),
            'libelle'       => $row->libelle,
            'description'   => $row->description,
            'compte_debit'  => $row->compte_debit,
            'compte_credit' => $row->compte_credit,
            'montant'       => $row->montant,
            'piece'         => $row->piece_comptable,
            'cree_par'      => $row->cree_par  ?? 'Système',
            'modifie_par'   => $row->modifie_par,
            'created_at'    => $row->created_at,
            'updated_at'    => $row->updated_at,
        ];
    }

    private function detailFacture(int $id): ?array
    {
        if (!Schema::hasTable('factures')) {
            return null;
        }

        $row = DB::table('factures')->where('id', $id)->first();
        if (!$row) {
            return null;
        }

        return [
            'type'          => 'Facture client',
            'reference'     => $row->numero ?? 'FAC-' . $id,
            'date'          => $row->date_facture ?? null,
            'journal'       => 'VT – Ventes',
            'libelle'       => $row->description ?? 'Facture client',
            'description'   => $row->description ?? null,
            'compte_debit'  => '411000',
            'compte_credit' => '707000',
            'montant'       => $row->montant_ttc ?? $row->montant_ht ?? 0,
            'statut'        => $row->statut ?? null,
            'piece'         => $row->numero ?? null,
            'cree_par'      => null,
            'created_at'    => $row->created_at ?? null,
        ];
    }

    private function detailEncaissement(int $id): ?array
    {
        if (!Schema::hasTable('encaissements')) {
            return null;
        }

        $row = DB::table('encaissements')->where('id', $id)->first();
        if (!$row) {
            return null;
        }

        $isCash = str_contains(mb_strtolower((string) ($row->mode_paiement ?? '')), 'cash')
               || str_contains(mb_strtolower((string) ($row->mode_paiement ?? '')), 'espe');

        return [
            'type'          => 'Encaissement',
            'reference'     => $row->reference ?? 'ENC-' . $id,
            'date'          => $row->date_encaissement ?? null,
            'journal'       => $isCash ? 'CA – Caisse' : 'BQ – Banque',
            'libelle'       => $row->description ?? 'Encaissement client',
            'description'   => $row->description ?? null,
            'compte_debit'  => $isCash ? '531000' : '512000',
            'compte_credit' => '411000',
            'montant'       => $row->montant ?? 0,
            'statut'        => $row->statut ?? null,
            'mode_paiement' => $row->mode_paiement ?? null,
            'piece'         => $row->reference ?? null,
            'cree_par'      => null,
            'created_at'    => $row->created_at ?? null,
        ];
    }

    private function detailDepense(int $id): ?array
    {
        if (!Schema::hasTable('depenses')) {
            return null;
        }

        $row = DB::table('depenses')->where('id', $id)->first();
        if (!$row) {
            return null;
        }

        return [
            'type'          => 'Dépense',
            'reference'     => 'DEP-' . $id,
            'date'          => $row->date_depense ?? null,
            'journal'       => 'AC – Achats',
            'libelle'       => $row->libelle ?? 'Dépense',
            'description'   => $row->description ?? null,
            'compte_debit'  => '601000',
            'compte_credit' => '531000',
            'montant'       => $row->montant ?? 0,
            'categorie'     => $row->categorie ?? null,
            'piece'         => 'DEP-' . $id,
            'cree_par'      => null,
            'created_at'    => $row->created_at ?? null,
        ];
    }

    private function detailVehicle(int $id): ?array
    {
        if (!Schema::hasTable('vehicle_financial_entries')) {
            return null;
        }

        $row = DB::table('vehicle_financial_entries')->where('id', $id)->first();
        if (!$row) {
            return null;
        }

        $isRevenue = ($row->entry_type ?? '') === 'revenue';

        return [
            'type'          => $isRevenue ? 'Revenu véhicule' : 'Charge véhicule',
            'reference'     => 'VEH-' . $id,
            'date'          => $row->entry_date ?? null,
            'journal'       => $isRevenue ? 'VT – Ventes' : 'AC – Achats',
            'libelle'       => $row->label ?? $row->category ?? 'Mouvement véhicule',
            'description'   => $row->label ?? null,
            'compte_debit'  => $isRevenue ? '411000' : '602000',
            'compte_credit' => $isRevenue ? '707100' : '401000',
            'montant'       => $row->amount ?? 0,
            'statut'        => null,
            'categorie'     => $row->category ?? null,
            'mode_paiement' => $row->source_module ?? null,
            'piece'         => 'VEH-' . $id,
            'cree_par'      => null,
            'created_at'    => $row->created_at ?? null,
        ];
    }

    private function makeEntry(
        Carbon $date,
        string $journalCode,
        Collection $journalMap,
        string $reference,
        string $libelle,
        string $compteDebit,
        string $compteCredit,
        float $montant,
        string $piece,
        string $sourceType,
        int $sourceId,
        bool $editable = false,
    ): array {
        $journal = $journalMap->get($journalCode);

        return [
            'sort_id' => $sourceId,
            'date' => $date,
            'journal_code' => $journalCode,
            'journal_libelle' => $journal?->libelle ?? $journalCode,
            'reference' => $reference,
            'libelle' => $libelle,
            'compte_debit' => $compteDebit,
            'compte_credit' => $compteCredit,
            'debit' => $montant,
            'credit' => $montant,
            'montant' => $montant,
            'piece' => $piece,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'editable' => $editable,
        ];
    }
}