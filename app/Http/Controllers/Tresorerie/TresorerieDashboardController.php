<?php

namespace App\Http\Controllers\Tresorerie;

use App\Models\DepenseCaisse;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TresorerieDashboardController extends Controller
{
    public function index()
    {
        $soldeTotal = \App\Models\Caisse::where('est_active', true)->sum('solde_actuel');
        $nombreCaisses = \App\Models\Caisse::where('est_active', true)->count();
        $approvisionnementsEnAttente = \App\Models\ApprovisionnementCaisse::where('statut', 'en_attente')->count();

        /** @var User|null $user */
        $user = Auth::user();

        $depensesMensuelles = $this->applyDecaissementVisibility(
            DepenseCaisse::query(),
            $user
        )
            ->whereMonth('date_depense', now()->month)
            ->whereYear('date_depense', now()->year)
            ->sum('montant');

        // Calculer le total des décaissements depuis DepenseCaisse (cohérence avec page /decaissements)
        $totalDecaissements = $this->applyDecaissementVisibility(
            DepenseCaisse::query(),
            $user
        )->sum('montant');

        $approvisionnementsRecents = \App\Models\ApprovisionnementCaisse::with(['source', 'destination', 'demandeur'])
            ->latest()
            ->limit(5)
            ->get();

        $encaissementsRecents = \App\Models\Encaissement::with(['caisse', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        // Dépenses récentes pour le dashboard
        $depensesRecentes = $this->applyDecaissementVisibility(
            DepenseCaisse::with(['caisse', 'createur', 'beneficiaire']),
            $user
        )
            ->latest()
            ->limit(5)
            ->get();

        $caisses = \App\Models\Caisse::with(['responsable'])->get();

        // Vérifier si la table virements existe
        try {
            $totalVirements = \App\Models\Virement::count();
            $virements = \App\Models\Virement::latest()->limit(5)->get();
        } catch (\Exception $e) {
            $totalVirements = 0;
            $virements = collect();
        }

        // Statistiques pour les KPIs
        $stats = [
            'total_caisses' => $nombreCaisses,
            'solde_total' => $soldeTotal,
            'variation' => 0, // À calculer selon votre logique métier
            'en_attente' => $approvisionnementsEnAttente,
            'total_approvisionnements' => \App\Models\ApprovisionnementCaisse::count(),
            'valides' => \App\Models\ApprovisionnementCaisse::where('statut', 'validé')->count(),
            'total_decaissements' => $totalDecaissements,
            'total_encaissements' => \App\Models\Encaissement::count(),
            'total_virements' => $totalVirements,
        ];

        // Données pour les listes récentes
        $approvisionnements = $approvisionnementsRecents;
        $decaissements = $this->applyDecaissementVisibility(
            DepenseCaisse::with(['caisse', 'createur', 'beneficiaire']),
            $user
        )
            ->latest()
            ->limit(5)
            ->get();
        $encaissements = $encaissementsRecents;

        return view('tresorerie.dashboard', compact(
            'soldeTotal',
            'nombreCaisses',
            'approvisionnementsEnAttente',
            'depensesMensuelles',
            'approvisionnementsRecents',
            'depensesRecentes',
            'encaissementsRecents',
            'caisses',
            'stats',
            'approvisionnements',
            'decaissements',
            'encaissements',
            'virements'
        ));
    }

    public function encaissements()
    {
        $encaissements = \App\Models\Encaissement::with(['caisse', 'createur'])
            ->orderBy('date_encaissement', 'desc')
            ->get();

        return view('tresorerie.encaissements', compact('encaissements'));
    }

    public function encaissementsCreate()
    {
        return view('tresorerie.encaissements-create');
    }

    public function encaissementsStore(Request $request)
    {
        $validated = $request->validate([
            'date_encaissement' => 'required|date',
            'type_encaissement' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'montant' => 'required|numeric|min:1',
            'caisse_id' => 'required|exists:caisses,id',
            'mode_paiement' => 'nullable|string|max:100',
            'client' => 'nullable|string|max:255',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $encaissement = \App\Models\Encaissement::create([
                'date_encaissement' => $validated['date_encaissement'],
                'type_encaissement' => $validated['type_encaissement'],
                'description' => $validated['description'],
                'montant' => $validated['montant'],
                'caisse_id' => $validated['caisse_id'],
                'mode_paiement' => $validated['mode_paiement'] ?? null,
                'client' => $validated['client'] ?? null,
                'statut' => 'validé',
                'created_by' => Auth::id(),
            ]);

            // Mettre à jour le solde de la caisse
            $caisse = \App\Models\Caisse::findOrFail($validated['caisse_id']);
            $caisse->increment('solde_actuel', $validated['montant']);

            // Enregistrer le mouvement
            $caisse->mouvements()->create(\App\Models\MouvementCaisse::normalizePayload([
                'type_mouvement' => 'encaissement',
                'montant' => $validated['montant'],
                'libelle' => 'Encaissement - ' . $validated['type_encaissement'],
                'description' => $validated['description'],
                'created_by' => Auth::id(),
                'devise' => $caisse->devise ?? 'XOF',
            ], $encaissement));

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('tresorerie.encaissements')
                ->with('success', 'Encaissement de ' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA enregistré avec succès !');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function decaissements()
    {
        /** @var User|null $user */
        $user = Auth::user();

        $decaissements = $this->applyDecaissementVisibility(
            DepenseCaisse::with(['caisse', 'createur', 'beneficiaire']),
            $user
        )
            ->latest()
            ->get();

        return view('tresorerie.decaissements', compact('decaissements'));
    }

    public function decaissementsShow($id)
    {
        /** @var User|null $user */
        $user = Auth::user();

        $decaissement = $this->applyDecaissementVisibility(
            DepenseCaisse::with(['caisse', 'createur', 'beneficiaire']),
            $user
        )
            ->where('id', $id)
            ->firstOrFail();

        return view('tresorerie.decaissements-show', compact('decaissement'));
    }

    private function applyDecaissementVisibility($query, ?User $user)
    {
        if (!$user) {
            abort(403, 'Accès non autorisé');
        }

        if (in_array($user->role, ['admin', 'superadmin'], true)) {
            return $query;
        }

        return $query->where(function ($innerQuery) use ($user) {
            $innerQuery->where('created_by', $user->id)
                ->orWhere('createur_id', $user->id)
                ->orWhereHas('caisse', function ($caisseQuery) use ($user) {
                    $caisseQuery->where('responsable_id', $user->id);
                });
        });
    }

    public function soldesCaisse()
    {
        $soldes = \App\Models\Caisse::where('est_active', true)->get();
        return view('tresorerie.soldes-caisse', compact('soldes'));
    }

    public function paiementsFournisseurs()
    {
        $paiements = collect();
        return view('tresorerie.paiements-fournisseurs', compact('paiements'));
    }

    public function paiementsSalaires()
    {
        $paiements = collect();
        return view('tresorerie.paiements-salaires', compact('paiements'));
    }

    public function banque()
    {
        $caisses = \App\Models\Caisse::where('type', 'banque')->get();
        $caisseIds = $caisses->pluck('id');

        $mouvements = \App\Models\MouvementCaisse::whereIn('caisse_id', $caisseIds)
            ->with('caisse')
            ->latest()
            ->get();

        $operationsBancaires = $mouvements->map(function($mvt) {
            $mvt->date_operation = $mvt->date_mouvement;
            $mvt->type = ($mvt->type_mouvement == 'entree' ? 'crédit' : 'débit');
            $mvt->banque = $mvt->caisse->nom;
            $mvt->statut = $mvt->statut ?? 'validé';
            return $mvt;
        });

        return view('tresorerie.banque', compact('caisses', 'operationsBancaires'));
    }

    public function banqueShow($id)
    {
        $operation = \App\Models\MouvementCaisse::with('caisse')->findOrFail($id);
        return view('tresorerie.banque-show', compact('operation'));
    }

    public function banqueEdit($id)
    {
        $operation = \App\Models\MouvementCaisse::findOrFail($id);
        $caisses = \App\Models\Caisse::where('type', 'banque')->get();
        return view('tresorerie.banque-edit', compact('operation', 'caisses'));
    }

    public function banqueUpdate(Request $request, $id)
    {
        $operation = \App\Models\MouvementCaisse::findOrFail($id);

        $validated = $request->validate([
            'date_mouvement' => 'required|date',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'type_mouvement' => 'required|in:entree,sortie',
            'description' => 'nullable|string',
            'caisse_id' => 'required|exists:caisses,id',
        ]);

        $operation->update($validated);

        return redirect()->route('tresorerie.banque')
            ->with('success', 'Opération bancaire mise à jour avec succès.');
    }

    public function banqueDestroy($id)
    {
        $operation = \App\Models\MouvementCaisse::findOrFail($id);
        $operation->delete();

        return redirect()->route('tresorerie.banque')
            ->with('success', 'Opération bancaire supprimée avec succès.');
    }

    public function banqueCreate()
    {
        $caisses = \App\Models\Caisse::where('type', 'banque')->get();
        return view('tresorerie.banque-create', compact('caisses'));
    }

    public function banqueStore(Request $request)
    {
        $validated = $request->validate([
            'date_mouvement' => 'required|date',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'type_mouvement' => 'required|in:entree,sortie',
            'description' => 'nullable|string',
            'caisse_id' => 'required|exists:caisses,id',
        ]);

        $validated['reference'] = 'BAN-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
        $validated['created_by'] = \Illuminate\Support\Facades\Auth::id();
        $validated['statut'] = 'validé';

        \App\Models\MouvementCaisse::create($validated);

        return redirect()->route('tresorerie.banque')
            ->with('success', 'Opération bancaire enregistrée avec succès.');
    }

    public function flux()
    {
        $movements = \App\Models\MouvementCaisse::with(['caisse'])->latest()->get();

        $flux = $movements->map(function($item) {
            // Unifier les types pour la vue (entrant/sortant)
            $item->type = in_array($item->type_mouvement, ['entree', 'credit']) ? 'entrant' : 'sortant';

            // Source : Libellé ou Nom de la caisse
            $item->source = $item->libelle ?: ($item->caisse->nom ?? 'Inconnu');

            // Catégorie : Utiliser le libellé du type (via accessor) ou une valeur par défaut
            $item->categorie = $item->libelle_type ?? 'Mouvement';

            // Compte : Nom de la caisse
            $item->compte = $item->caisse->nom ?? 'Inconnu';

            // Alias de date pour compatibilité
            $item->date_operation = $item->date_mouvement;

            return $item;
        });

        return view('tresorerie.flux', compact('flux'));
    }

    public function avances()
    {
        $avances = collect(); // Stub pour éviter l'erreur 500 si la vue en attend une liste
        return view('tresorerie.avances', compact('avances'));
    }

    public function avancesCreate()
    {
        return view('tresorerie.avances-create');
    }

    public function avancesStore(Request $request)
    {
        // Simulation de stockage
        return redirect()->route('tresorerie.avances.index')
            ->with('success', 'Avance enregistrée avec succès !');
    }

    public function paiements()
    {
        $paiementsFournisseurs = \App\Models\PaiementFournisseur::with(['fournisseur', 'user'])->latest()->get();
        $paiementsSalaires = \App\Models\Paie::with('user')->latest()->get();

        $paiements = collect();

        foreach($paiementsFournisseurs as $p) {
            $paiements->push((object)[
                'id' => $p->id,
                'reference' => $p->reference,
                'date_paiement' => $p->date_paiement,
                'beneficiaire' => $p->fournisseur->nom ?? 'Inconnu',
                'type' => 'Fournisseur',
                'montant' => $p->montant,
                'statut' => $p->est_annule ? 'annulé' : ($p->validateur_id ? 'validé' : 'en attente'),
                'mode_paiement' => $p->mode_paiement ?? 'Virement'
            ]);
        }

        foreach($paiementsSalaires as $p) {
            $paiements->push((object)[
                'id' => $p->id,
                'reference' => 'SAL-' . $p->id,
                'date_paiement' => $p->date_paiement,
                'beneficiaire' => $p->user->name ?? 'Agent',
                'type' => 'Salaire',
                'montant' => $p->net_a_payer,
                'statut' => $p->statut ?? 'validé',
                'mode_paiement' => 'Virement'
            ]);
        }

        $paiements = $paiements->sortByDesc('date_paiement');

        return view('tresorerie.paiements', compact('paiements'));
    }

    public function paiementsShow($id)
    {
        return back()->with('info', 'Détails du paiement (ID: ' . $id . ') bientôt disponible.');
    }

    public function paiementsEdit($id)
    {
        return back()->with('info', 'Modification du paiement (ID: ' . $id . ') bientôt disponible.');
    }

    public function paiementsUpdate(Request $request, $id)
    {
        return back()->with('success', 'Paiement mis à jour (simulé).');
    }

    public function paiementsDestroy($id)
    {
        return back()->with('success', 'Paiement supprimé (simulé).');
    }

    public function paiementsCreate()
    {
        return view('tresorerie.paiements-create');
    }

    public function paiementsStore(Request $request)
    {
        // Simulation de stockage
        return redirect()->route('tresorerie.paiements.index')
            ->with('success', 'Paiement enregistré avec succès !');
    }

    public function getSoldeCaisses()
    {
        return response()->json([]);
    }

    public function getAvancesOuvertes()
    {
        return response()->json([]);
    }

    public function getDepensesMensuelles()
    {
        return response()->json([]);
    }

    /**
     * Page A Payer - Affiche les dépenses en attente de paiement
     */
    public function aPayer()
    {
        $user = Auth::user();

        // Récupérer les dépenses en attente de paiement
        $depensesAPayer = $this->applyDecaissementVisibility(
            DepenseCaisse::with(['caisse', 'createur', 'beneficiaire'])
                ->where('statut', 'en_attente'),
            $user
        )->get();

        // Récupérer aussi les opérations validées non payées
        $operationsAPayer = \App\Models\Operation::where('statut_courant', 'validée')
            ->where('montant', '>', 0)
            ->with(['user', 'service'])
            ->get();

        return view('tresorerie.a-payer', compact('depensesAPayer', 'operationsAPayer'));
    }
}
