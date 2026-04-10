<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use App\Models\Virement;
use App\Models\CompteBancaire;
use App\Models\Banque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class VirementController extends Controller
{
    /**
     * Affiche la liste des virements
     */
    /**
     * Affiche la liste des virements
     */
    public function index()
    {
        try {
            $virements = Virement::with(['compteSource', 'compteDestination', 'initiateur', 'validateur'])
                ->latest('date_virement')
                ->paginate(15);

            // Statistiques sécurisées
            $stats = [
                'total' => Virement::count(),
                'en_attente' => Virement::where('statut', 'en_attente')->count(),
                'effectues' => Virement::where('statut', 'effectue')->count(),
                'annules' => Virement::where('statut', 'annule')->count(),
                'echecs' => Virement::where('statut', 'echec')->count(),
                'montant_total' => Virement::where('statut', 'effectue')->sum('montant') ?? 0,
            ];
        } catch (\Exception $e) {
            \Log::error("Erreur lors de l'affichage de la liste des virements : " . $e->getMessage());
            // En cas d'erreur (ex: table manquante), on retourne des données vides pour ne pas planter la page
            $virements = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $stats = [
                'total' => 0, 'en_attente' => 0, 'effectues' => 0,
                'annules' => 0, 'echecs' => 0, 'montant_total' => 0
            ];
            session()->flash('error', 'Une erreur est survenue lors du chargement des données. Veuillez contacter l\'administrateur.');
        }

        return view('tresorerie.virements.index', compact('virements', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'un virement
     */
    public function create()
    {
        $comptes = CompteBancaire::with('banque')
            ->orderBy('banque_id')
            ->orderBy('numero_compte')
            ->get()
            ->groupBy('banque.nom');

        return view('tresorerie.virements.create', compact('comptes'));
    }

    /**
     * Enregistre un nouveau virement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'compte_source_id' => [
                'required',
                'exists:compte_bancaires,id',
                'different:compte_destination_id'
            ],
            'compte_destination_id' => [
                'required',
                'exists:compte_bancaires,id',
                'different:compte_source_id'
            ],
            'montant' => 'required|numeric|min:0.01',
            'date_virement' => 'required|date|after_or_equal:today',
            'frais' => 'nullable|numeric|min:0',
            'motif' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier que le compte source a un solde suffisant
            $compteSource = CompteBancaire::findOrFail($validated['compte_source_id']);
            $montantTotal = $validated['montant'] + ($validated['frais'] ?? 0);

            if ($compteSource->solde < $montantTotal) {
                return back()
                    ->withInput()
                    ->with('error', 'Le solde du compte source est insuffisant pour effectuer ce virement.');
            }

            // Créer le virement
            $virement = Virement::create([
                'compte_source_id' => $validated['compte_source_id'],
                'compte_destination_id' => $validated['compte_destination_id'],
                'montant' => $validated['montant'],
                'date_virement' => $validated['date_virement'],
                'frais' => $validated['frais'] ?? 0,
                'motif' => $validated['motif'],
                'notes' => $validated['notes'] ?? null,
                'statut' => 'en_attente',
                'devise' => 'XOF', // À adapter selon les besoins
                'taux_change' => 1, // À adapter pour les devises étrangères
                'initie_par' => auth()->id(),
            ]);

            // Si le virement est immédiat, le traiter
            if ($request->has('executer_immediatement')) {
                $this->traiterVirement($virement);
            }

            DB::commit();

            return redirect()
                ->route('tresorerie.virements.show', $virement)
                ->with('success', 'Le virement a été créé avec succès.' .
                    ($virement->statut === 'effectue' ? ' Le virement a été exécuté avec succès.' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création du virement : ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du virement : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un virement
     */
    public function show(Virement $virement)
    {
        $virement->load(['compteSource.banque', 'compteDestination.banque', 'initiateur', 'validateur']);
        return view('tresorerie.virements.show', compact('virement'));
    }

    /**
     * Affiche le formulaire de modification d'un virement
     */
    public function edit(Virement $virement)
    {
        if ($virement->statut !== 'en_attente') {
            return redirect()
                ->route('tresorerie.virements.show', $virement)
                ->with('warning', 'Seuls les virements en attente peuvent être modifiés.');
        }

        $comptes = CompteBancaire::with('banque')
            ->orderBy('banque_id')
            ->orderBy('numero_compte')
            ->get()
            ->groupBy('banque.nom');

        return view('tresorerie.virements.edit', compact('virement', 'comptes'));
    }

    /**
     * Met à jour un virement
     */
    public function update(Request $request, Virement $virement)
    {
        if ($virement->statut !== 'en_attente') {
            return redirect()
                ->route('tresorerie.virements.show', $virement)
                ->with('error', 'Seuls les virements en attente peuvent être modifiés.');
        }

        $validated = $request->validate([
            'compte_source_id' => [
                'required',
                'exists:compte_bancaires,id',
                'different:compte_destination_id'
            ],
            'compte_destination_id' => [
                'required',
                'exists:compte_bancaires,id',
                'different:compte_source_id'
            ],
            'montant' => 'required|numeric|min:0.01',
            'date_virement' => 'required|date|after_or_equal:today',
            'frais' => 'nullable|numeric|min:0',
            'motif' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier que le compte source a un solde suffisant
            $compteSource = CompteBancaire::findOrFail($validated['compte_source_id']);
            $montantTotal = $validated['montant'] + ($validated['frais'] ?? 0);

            if ($compteSource->solde < $montantTotal) {
                return back()
                    ->withInput()
                    ->with('error', 'Le solde du compte source est insuffisant pour effectuer ce virement.');
            }

            // Mettre à jour le virement
            $virement->update([
                'compte_source_id' => $validated['compte_source_id'],
                'compte_destination_id' => $validated['compte_destination_id'],
                'montant' => $validated['montant'],
                'date_virement' => $validated['date_virement'],
                'frais' => $validated['frais'] ?? 0,
                'motif' => $validated['motif'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Si le virement est marqué comme à exécuter immédiatement
            if ($request->has('executer_immediatement')) {
                $this->traiterVirement($virement);
            }

            DB::commit();

            return redirect()
                ->route('tresorerie.virements.show', $virement)
                ->with('success', 'Le virement a été mis à jour avec succès.' .
                    ($virement->statut === 'effectue' ? ' Le virement a été exécuté avec succès.' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour du virement : ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du virement : ' . $e->getMessage());
        }
    }

    /**
     * Supprime un virement
     */
    public function destroy(Virement $virement)
    {
        if ($virement->statut !== 'en_attente') {
            return back()
                ->with('error', 'Seuls les virements en attente peuvent être supprimés.');
        }

        try {
            $virement->delete();

            return redirect()
                ->route('tresorerie.virements.index')
                ->with('success', 'Le virement a été supprimé avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du virement : ' . $e->getMessage());

            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression du virement : ' . $e->getMessage());
        }
    }

    /**
     * Traite un virement (exécution du transfert de fonds)
     */
    protected function traiterVirement(Virement $virement)
    {
        if ($virement->statut !== 'en_attente') {
            throw new \Exception('Seuls les virements en attente peuvent être traités.');
        }

        DB::beginTransaction();

        try {
            $compteSource = $virement->compteSource;
            $compteDestination = $virement->compteDestination;
            $montantTotal = $virement->montant + $virement->frais;

            // Vérifier que le compte source a un solde suffisant
            if ($compteSource->solde < $montantTotal) {
                throw new \Exception('Le solde du compte source est insuffisant pour effectuer ce virement.');
            }

            // Effectuer le transfert de fonds
            $compteSource->decrement('solde', $montantTotal);
            $compteDestination->increment('solde', $virement->montant);

            // Mettre à jour le statut du virement
            $virement->update([
                'statut' => 'effectue',
                'valide_par' => auth()->id(),
                'date_validation' => now(),
                'reference_operation' => 'VIR-' . now()->format('Ymd') . '-' . Str::random(6),
            ]);

            // Enregistrer les mouvements de compte
            $this->enregistrerMouvement(
                $compteSource->id,
                'debit',
                $montantTotal,
                'Virement sortant vers ' . $compteDestination->intitule_compte,
                'virement_sortant',
                $virement->id
            );

            $this->enregistrerMouvement(
                $compteDestination->id,
                'credit',
                $virement->montant,
                'Virement entrant depuis ' . $compteSource->intitule_compte,
                'virement_entrant',
                $virement->id
            );

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            // Marquer le virement comme échoué
            $virement->update([
                'statut' => 'echec',
                'notes' => ($virement->notes ?? '') . '\nÉchec du traitement : ' . $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Enregistre un mouvement de compte
     */
    protected function enregistrerMouvement($compteId, $type, $montant, $libelle, $typeMouvement, $referenceId = null)
    {
        // À implémenter selon votre logique de suivi des mouvements
        // Exemple :
        /*
        return MouvementCompte::create([
            'compte_bancaire_id' => $compteId,
            'type' => $type,
            'montant' => $montant,
            'libelle' => $libelle,
            'type_mouvement' => $typeMouvement,
            'reference_id' => $referenceId,
            'date_operation' => now(),
            'statut' => 'termine',
        ]);
        */
    }

    /**
     * Annule un virement
     */
    public function annuler(Virement $virement)
    {
        if (!in_array($virement->statut, ['en_attente', 'effectue'])) {
            return back()
                ->with('error', 'Ce virement ne peut pas être annulé.');
        }

        try {
            DB::beginTransaction();

            if ($virement->statut === 'effectue') {
                // Si le virement a déjà été exécuté, il faut inverser les opérations
                $compteSource = $virement->compteSource;
                $compteDestination = $virement->compteDestination;
                $montantTotal = $virement->montant + $virement->frais;

                // Vérifier que le compte de destination a un solde suffisant pour le remboursement
                if ($compteDestination->solde < $virement->montant) {
                    throw new \Exception('Le solde du compte de destination est insuffisant pour annuler ce virement.');
                }

                // Inverser les opérations
                $compteSource->increment('solde', $montantTotal);
                $compteDestination->decrement('solde', $virement->montant);

                // Enregistrer les mouvements d'annulation
                $this->enregistrerMouvement(
                    $compteSource->id,
                    'credit',
                    $montantTotal,
                    'Annulation virement N°' . $virement->reference,
                    'annulation_virement',
                    $virement->id
                );

                $this->enregistrerMouvement(
                    $compteDestination->id,
                    'debit',
                    $virement->montant,
                    'Annulation virement N°' . $virement->reference,
                    'annulation_virement',
                    $virement->id
                );
            }

            // Mettre à jour le statut du virement
            $virement->update([
                'statut' => 'annule',
                'valide_par' => auth()->id(),
                'date_validation' => now(),
                'notes' => ($virement->notes ?? '') . '\nVirement annulé le ' . now()->format('d/m/Y H:i'),
            ]);

            DB::commit();

            return redirect()
                ->route('tresorerie.virements.show', $virement)
                ->with('success', 'Le virement a été annulé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de l\'annulation du virement : ' . $e->getMessage());

            return back()
                ->with('error', 'Une erreur est survenue lors de l\'annulation du virement : ' . $e->getMessage());
        }
    }

    /**
     * Traite les virements en attente programmés pour aujourd'hui
     */
    public function traiterVirementsProgrammes()
    {
        $virements = Virement::where('statut', 'en_attente')
            ->whereDate('date_virement', '<=', now())
            ->get();

        $resultats = [
            'success' => 0,
            'echecs' => 0,
            'details' => []
        ];

        foreach ($virements as $virement) {
            try {
                $this->traiterVirement($virement);
                $resultats['success']++;
                $resultats['details'][] = [
                    'reference' => $virement->reference,
                    'statut' => 'succes',
                    'message' => 'Virement traité avec succès.'
                ];
            } catch (\Exception $e) {
                $resultats['echecs']++;
                $resultats['details'][] = [
                    'reference' => $virement->reference,
                    'statut' => 'echec',
                    'message' => $e->getMessage()
                ];
                \Log::error('Erreur lors du traitement du virement ' . $virement->reference . ' : ' . $e->getMessage());
            }
        }

        return $resultats;
    }
}
