<?php

namespace App\Http\Controllers\Tresorerie;

use App\Models\Caisse;
use App\Models\DepenseCaisse;
use App\Models\CompteComptable;
use App\Models\Expense;
use App\Models\Operation;
use App\Models\User;
use App\Models\JustificatifDepense;
use App\Models\MouvementCaisse;
use App\Models\EcritureComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Notifications\DepenseValideeNotification;
use App\Notifications\DepenseRejeteeNotification;
use App\Http\Controllers\Controller;

class DepenseCaisseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer les paramètres de filtrage
        $search = request('search');
        $statut = request('statut', 'tous');
        $dateDebut = request('date_debut');
        $dateFin = request('date_fin');
        $caisseId = request('caisse_id');
        $beneficiaireId = request('beneficiaire_id');

        // Construire la requête avec les filtres
        $query = DepenseCaisse::with(['caisse', 'compteComptable', 'createur'])
            ->when($search, function($q) use ($search) {
                $q->where(function($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                      ->orWhere('beneficiaire', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($statut !== 'tous', function($q) use ($statut) {
                $q->where('statut', $statut);
            })
            ->when($dateDebut, function($q) use ($dateDebut) {
                $q->whereDate('date_depense', '>=', $dateDebut);
            })
            ->when($dateFin, function($q) use ($dateFin) {
                $q->whereDate('date_depense', '<=', $dateFin);
            })
            ->when($caisseId, function($q) use ($caisseId) {
                $q->where('caisse_id', $caisseId);
            })
            ->when($beneficiaireId, function($q) use ($beneficiaireId) {
                $q->where('beneficiaire_id', $beneficiaireId);
            });

        // Vérifier si l'utilisateur a accès au module de trésorerie
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Si l'utilisateur n'a pas accès au module, ne rien retourner
        if (!$user->canAccessTreasury()) {
            abort(403, 'Accès non autorisé au module de trésorerie.');
        }

        // Si l'utilisateur n'est pas admin, ne montrer que ses dépenses ou celles de ses caisses
        // Exception : les modérateurs avec la permission tresorerie.access voient tout
        $isModerateurTresorerie = in_array($user->role, ['moderator', 'moderateur'], true)
            && $user->hasModulePermission('tresorerie');

        if (!$user->isAdmin() && !$isModerateurTresorerie) {
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('caisse', function($q) use ($user) {
                      $q->where('responsable_id', $user->id);
                  });
            });
        }

        // Trier et paginer les résultats
        $depenses = $query->latest('date_depense')
                         ->paginate(15)
                         ->withQueryString();

        // Calculer les statistiques
        try {
            $totalDepenses = DepenseCaisse::sum('montant');
        } catch (\Exception $e) {
            $totalDepenses = 0;
        }

        try {
            $depensesMois = DepenseCaisse::whereMonth('date_depense', now()->month)
                                       ->whereYear('date_depense', now()->year)
                                       ->sum('montant');
        } catch (\Exception $e) {
            $depensesMois = 0;
        }

        try {
            $moisPrecedent = now()->subMonthNoOverflow();
            $depensesMoisPrecedent = DepenseCaisse::whereMonth('date_depense', $moisPrecedent->month)
                ->whereYear('date_depense', $moisPrecedent->year)
                ->sum('montant');
        } catch (\Exception $e) {
            $depensesMoisPrecedent = 0;
        }

        try {
            $debutPeriodeMoyenne = now()->copy()->startOfMonth()->subMonthsNoOverflow(11);
            $total12Mois = DepenseCaisse::whereDate('date_depense', '>=', $debutPeriodeMoyenne)
                ->sum('montant');
            $moyenneMensuelle = $total12Mois / 12;
        } catch (\Exception $e) {
            $moyenneMensuelle = 0;
        }

        try {
            $enAttente = DepenseCaisse::where('statut', 'soumis')->count();
        } catch (\Exception $e) {
            $enAttente = 0;
        }

        // Récupérer la liste des caisses pour le filtre
        try {
            $caisses = Caisse::orderBy('nom')->get();
        } catch (\Exception $e) {
            $caisses = collect([]); // Collection vide si la table n'existe pas
        }

        try {
            $beneficiairesIds = DepenseCaisse::query()
                ->whereNotNull('beneficiaire_id')
                ->select('beneficiaire_id')
                ->distinct()
                ->pluck('beneficiaire_id');

            $beneficiaires = User::whereIn('id', $beneficiairesIds)
                ->orderBy('name')
                ->get(['id', 'name']);
        } catch (\Exception $e) {
            $beneficiaires = collect([]);
        }

        return view('tresorerie.depenses.index', [
            'depenses' => $depenses,
            'totalDepenses' => $totalDepenses,
            'depensesMois' => $depensesMois,
            'depensesMoisPrecedent' => $depensesMoisPrecedent,
            'enAttente' => $enAttente,
            'moyenneMensuelle' => $moyenneMensuelle,
            'caisses' => $caisses,
            'beneficiaires' => $beneficiaires,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', DepenseCaisse::class);

        $caisses = Caisse::where('est_active', true)->get();

        $comptesQuery = CompteComptable::query();
        if (Schema::hasColumn('comptes_comptables', 'est_verrouille')) {
            $comptesQuery->where('est_verrouille', false);
        } elseif (Schema::hasColumn('comptes_comptables', 'actif')) {
            $comptesQuery->where('actif', true);
        }

        $orderColumn = 'id';
        foreach (['numero', 'numero_compte', 'code', 'id'] as $col) {
            if (Schema::hasColumn('comptes_comptables', $col)) {
                $orderColumn = $col;
                break;
            }
        }

        $comptes = $comptesQuery->orderBy($orderColumn)->get();

        $selectedExpenseId = request()->integer('expense_id') ?: null;
        $selectedCompteId = request()->integer('compte_comptable_id') ?: null;

        $expenses = Expense::query()
            ->where('statut', 'approuvee')
            ->whereNotIn('id', function ($q) {
                $q->select('expense_id')
                    ->from('depense_caisses')
                    ->whereNotNull('expense_id');
            })
            ->orderByDesc('id')
            ->limit(300)
            ->get(['id', 'reference', 'categorie', 'description', 'montant', 'date_depense', 'fournisseur']);

        // Récupérer les opérations approuvées non payées
        $approvedStatus = [
            'Approuvé_en_attente_paiement',
            'bon_pour_accord',
            'pret_execution',
            'approuvee',
            'approuve',
            'validée',
            'validee',
            'validé',
            'valide',
            'approved',
        ];

        $operations = collect();
        if (Schema::hasTable('operations') && Schema::hasColumn('operations', 'statut_courant')) {
            $operationsQuery = Operation::query()
                ->where(function ($query) use ($approvedStatus) {
                    $query->whereIn('statut_courant', $approvedStatus)
                        ->orWhereRaw('LOWER(TRIM(statut_courant)) IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                            'approuve_en_attente_paiement',
                            'bon_pour_accord',
                            'pret_execution',
                            'approuvee',
                            'approuve',
                            'validée',
                            'validee',
                            'validé',
                            'valide',
                            'approved',
                        ]);
                });

            if (Schema::hasColumn('operations', 'is_paid')) {
                $operationsQuery->where(function ($query) {
                    $query->whereNull('is_paid')->orWhere('is_paid', false);
                });
            }

            $operations = $operationsQuery->latest()->get();
        }

        return view('tresorerie.depenses.create', [
            'caisses' => $caisses,
            'comptes' => $comptes,
            'expenses' => $expenses,
            'selectedExpenseId' => $selectedExpenseId,
            'selectedCompteId' => $selectedCompteId,
            'operations' => $operations,
            'modesPaiement' => DepenseCaisse::getModesPaiement()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', DepenseCaisse::class);

        $validated = $request->validate([
            'operation_id' => 'nullable|exists:operations,id',
            'expense_id' => 'required|exists:expenses,id|unique:depense_caisses,expense_id',
            'caisse_id' => 'required|exists:caisses,id',
            'compte_comptable_id' => 'required|exists:comptes_comptables,id',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'date_depense' => 'required|date',
            'beneficiaire' => 'required|string|max:255',
            'mode_paiement' => 'required|string|in:' . implode(',', array_keys(DepenseCaisse::getModesPaiement())),
            'reference_paiement' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $expense = Expense::findOrFail($validated['expense_id']);

        if ($expense->statut !== 'approuvee') {
            return back()
                ->withInput()
                ->with('error', 'La dépense sélectionnée n\'est pas approuvée.');
        }

        try {
            DB::beginTransaction();

            // Créer la dépense
            $depense = new DepenseCaisse();
            $depense->reference = 'DEP-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $depense->operation_id = $validated['operation_id'] ?? null;
            $depense->expense_id = $expense->id;
            $depense->caisse_id = $validated['caisse_id'];
            $depense->compte_comptable_id = $validated['compte_comptable_id'];
            $depense->libelle = $validated['libelle'];
            $depense->montant = $validated['montant'];
            $depense->date_depense = $validated['date_depense'];
            $depense->beneficiaire = $validated['beneficiaire'];
            $depense->mode_paiement = $validated['mode_paiement'];
            $depense->reference_paiement = $validated['reference_paiement'] ?? null;
            $depense->description = $validated['description'] ?? ('Paiement de la dépense comptable: ' . ($expense->reference ?? ('EXP-' . $expense->id)));
            $depense->created_by = Auth::id();
            $depense->save();

            $expense->update([
                'statut' => 'payee',
                'date_paiement' => now(),
                'payee_par' => Auth::id(),
                'reference_paiement' => $validated['reference_paiement'] ?? null,
            ]);

            // Gérer le justificatif s'il y en a un
            if ($request->hasFile('justificatif')) {
                $file = $request->file('justificatif');
                $path = $file->store('justificatifs/depenses', 'public');

                $justificatif = new JustificatifDepense();
                $justificatif->depense_caisse_id = $depense->id;
                $justificatif->nom = $file->getClientOriginalName();
                $justificatif->chemin = $path;
                $justificatif->mime_type = $file->getClientMimeType();
                $justificatif->taille = $file->getSize();
                $justificatif->uploaded_by = Auth::id();
                $justificatif->save();

                $depense->est_justifiee = true;
                $depense->save();
            }

            // Créer le mouvement de caisse (schéma compatible)
            $depense->caisse->mouvements()->create(MouvementCaisse::normalizePayload([
                'type_mouvement' => MouvementCaisse::TYPE_DEPENSE,
                'montant' => -$depense->montant,
                'libelle' => 'Dépense',
                'description' => "Dépense: {$depense->libelle}",
                'created_by' => Auth::id(),
                'devise' => $depense->caisse->devise ?? 'XOF',
            ], $depense));

            // Mettre à jour le solde de la caisse
            $caisse = $depense->caisse;
            $caisse->solde_actuel -= $depense->montant;
            $caisse->save();

            $this->createAccountingEntry($depense);

            DB::commit();

            return redirect()
                ->route('tresorerie.decaissements')
                ->with('success', 'Le décaissement a été enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'enregistrement de la dépense: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de la dépense: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DepenseCaisse $depense)
    {
        $this->authorize('view', $depense);

        $depense->load(['caisse', 'compteComptable', 'createur', 'mouvementCaisse', 'justificatifs']);

        return view('tresorerie.depenses.show', compact('depense'));
    }

    private function createAccountingEntry(DepenseCaisse $depense): void
    {
        if (!Schema::hasTable('ecritures_comptables')) {
            return;
        }

        $existingEntry = EcritureComptable::query()
            ->where('source_type', DepenseCaisse::class)
            ->where('source_id', $depense->id)
            ->first();

        if ($existingEntry) {
            return;
        }

        $debitAccount = $depense->compteComptable?->numero
            ?? $depense->compteComptable?->numero_compte
            ?? $depense->compteComptable?->code
            ?? (string) ($depense->compte_comptable_id ?? '6');

        $creditAccount = '57';
        if ($depense->caisse?->code) {
            $creditAccount = (string) $depense->caisse->code;
        }

        EcritureComptable::create([
            'date' => $depense->date_depense,
            'reference' => $depense->reference,
            'piece_comptable' => $depense->reference_paiement,
            'libelle' => $depense->libelle,
            'compte_debit' => $debitAccount,
            'compte_credit' => $creditAccount,
            'montant' => $depense->montant,
            'description' => $depense->description,
            'source_type' => DepenseCaisse::class,
            'source_id' => $depense->id,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DepenseCaisse $depense)
    {
        $this->authorize('update', $depense);

        if ($depense->est_validee) {
            return redirect()
                ->route('tresorerie.depenses.show', $depense)
                ->with('warning', 'Impossible de modifier une dépense déjà validée.');
        }

        $caisses = Caisse::where('est_active', true)->get();
        $comptes = CompteComptable::where('est_actif', true)->get();

        return view('tresorerie.depenses.edit', [
            'depense' => $depense,
            'caisses' => $caisses,
            'comptes' => $comptes,
            'modesPaiement' => DepenseCaisse::getModesPaiement()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DepenseCaisse $depense)
    {
        $this->authorize('update', $depense);

        if ($depense->est_validee) {
            return back()
                ->with('error', 'Impossible de modifier une dépense déjà validée.');
        }

        $validated = $request->validate([
            'caisse_id' => 'required|exists:caisses,id',
            'compte_comptable_id' => 'required|exists:comptes_comptables,id',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'date_depense' => 'required|date',
            'beneficiaire' => 'required|string|max:255',
            'mode_paiement' => 'required|string|in:' . implode(',', array_keys(DepenseCaisse::getModesPaiement())),
            'reference_paiement' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Sauvegarder l'ancien montant pour ajuster le solde de la caisse
            $ancienMontant = $depense->montant;
            $ancienneCaisseId = $depense->caisse_id;

            // Mettre à jour la dépense
            $depense->caisse_id = $validated['caisse_id'];
            $depense->compte_comptable_id = $validated['compte_comptable_id'];
            $depense->libelle = $validated['libelle'];
            $depense->montant = $validated['montant'];
            $depense->date_depense = $validated['date_depense'];
            $depense->beneficiaire = $validated['beneficiaire'];
            $depense->mode_paiement = $validated['mode_paiement'];
            $depense->reference_paiement = $validated['reference_paiement'] ?? null;
            $depense->description = $validated['description'] ?? null;
            $depense->save();

            // Gérer le justificatif s'il y en a un
            if ($request->hasFile('justificatif')) {
                // Supprimer l'ancien justificatif s'il existe
                if ($depense->justificatifs->isNotEmpty()) {
                    foreach ($depense->justificatifs as $justificatif) {
                        Storage::disk('public')->delete($justificatif->chemin);
                        $justificatif->delete();
                    }
                }

                $file = $request->file('justificatif');
                $path = $file->store('justificatifs/depenses', 'public');

                $justificatif = new JustificatifDepense();
                $justificatif->depense_caisse_id = $depense->id;
                $justificatif->nom = $file->getClientOriginalName();
                $justificatif->chemin = $path;
                $justificatif->mime_type = $file->getClientMimeType();
                $justificatif->taille = $file->getSize();
                $justificatif->uploaded_by = Auth::id();
                $justificatif->save();

                $depense->est_justifiee = true;
                $depense->save();
            }

            // Mettre à jour le mouvement de caisse
            $mouvement = $depense->mouvementCaisse;

            // Si la caisse a changé, supprimer l'ancien mouvement et en créer un nouveau
            if ($ancienneCaisseId != $depense->caisse_id) {
                if ($mouvement) {
                    $mouvement->delete();
                }

                $mouvement = new MouvementCaisse();
                $mouvement->caisse_id = $depense->caisse_id;
                $mouvement->type = 'debit';
                $mouvement->reference_type = get_class($depense);
                $mouvement->reference_id = $depense->id;
            }

            $mouvement->montant = $depense->montant;
            $mouvement->description = "Dépense: {$depense->libelle}";
            $mouvement->save();

            // Mettre à jour les soldes des caisses concernées
            if ($ancienneCaisseId != $depense->caisse_id) {
                // Rembourser l'ancienne caisse
                $ancienneCaisse = Caisse::find($ancienneCaisseId);
                if ($ancienneCaisse) {
                    $ancienneCaisse->solde_actuel += $ancienMontant;
                    $ancienneCaisse->save();
                }

                // Débiter la nouvelle caisse
                $nouvelleCaisse = $depense->caisse;
                $nouvelleCaisse->solde_actuel -= $depense->montant;
                $nouvelleCaisse->save();
            } else {
                // Ajuster le solde de la même caisse si le montant a changé
                if ($ancienMontant != $depense->montant) {
                    $caisse = $depense->caisse;
                    $caisse->solde_actuel += ($ancienMontant - $depense->montant);
                    $caisse->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('tresorerie.depenses.show', $depense)
                ->with('success', 'La dépense a été mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la dépense: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la dépense.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DepenseCaisse $depense)
    {
        $this->authorize('delete', $depense);

        if ($depense->est_validee) {
            return back()
                ->with('error', 'Impossible de supprimer une dépense déjà validée.');
        }

        try {
            DB::beginTransaction();

            // Supprimer les justificatifs
            foreach ($depense->justificatifs as $justificatif) {
                Storage::disk('public')->delete($justificatif->chemin);
                $justificatif->delete();
            }

            // Supprimer le mouvement de caisse
            if ($depense->mouvementCaisse) {
                // Rembourser la caisse
                $caisse = $depense->caisse;
                $caisse->solde_actuel += $depense->montant;
                $caisse->save();

                $depense->mouvementCaisse()->delete();
            }

            // Supprimer la dépense
            $depense->delete();

            DB::commit();

            return redirect()
                ->route('tresorerie.depenses.index')
                ->with('success', 'La dépense a été supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de la dépense: ' . $e->getMessage());

            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de la dépense.');
        }
    }

    /**
     * Valider une dépense
     */
    public function valider(DepenseCaisse $depense)
    {
        $this->authorize('validate', $depense);

        if ($depense->est_validee) {
            return back()
                ->with('warning', 'Cette dépense a déjà été validée.');
        }

        try {
            $depense->date_validation = now();
            $depense->valide_par = Auth::id();
            $depense->statut = 'validee';
            $depense->save();

            // Envoyer une notification au demandeur
            if ($depense->created_by && $depense->created_by != Auth::id()) {
                $depense->createur->notify(new DepenseValideeNotification($depense));
            }

            return back()
                ->with('success', 'La dépense a été validée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation de la dépense: ' . $e->getMessage());

            return back()
                ->with('error', 'Une erreur est survenue lors de la validation de la dépense.');
        }
    }

    /**
     * Rejeter une dépense
     */
    public function rejeter(Request $request, DepenseCaisse $depense)
    {
        $this->authorize('validate', $depense);

        if ($depense->est_validee) {
            return back()
                ->with('error', 'Impossible de rejeter une dépense déjà validée.');
        }

        $validated = $request->validate([
            'motif_rejet' => 'required|string|min:10|max:1000',
        ]);

        try {
            $depense->date_validation = now();
            $depense->valide_par = Auth::id();
            $depense->statut = 'rejetee';
            $depense->motif_rejet = $validated['motif_rejet'];
            $depense->save();

            // Rembourser la caisse si la dépense avait déjà été débitée
            if ($depense->mouvementCaisse) {
                $caisse = $depense->caisse;
                $caisse->solde_actuel += $depense->montant;
                $caisse->save();

                // Marquer le mouvement comme annulé
                $depense->mouvementCaisse->update(['annule' => true]);
            }

            // Envoyer une notification au demandeur
            if ($depense->created_by && $depense->created_by != Auth::id()) {
                $depense->createur->notify(new DepenseRejeteeNotification($depense));
            }

            return redirect()
                ->route('tresorerie.depenses.show', $depense)
                ->with('success', 'La dépense a été rejetée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors du rejet de la dépense: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors du rejet de la dépense.');
        }
    }

    /**
     * Télécharger un justificatif
     */
    public function telechargerJustificatif(JustificatifDepense $justificatif)
    {
        $this->authorize('view', $justificatif->depense);

        if (!Storage::disk('public')->exists($justificatif->chemin)) {
            abort(404, 'Le fichier demandé est introuvable.');
        }

        return Response::download(Storage::disk('public')->path($justificatif->chemin), $justificatif->nom);
    }
}
