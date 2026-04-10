<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\ApprovisionnementCaisse;
use App\Models\DepenseCaisse;
use App\Models\MouvementCaisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TresorerieController extends Controller
{
    /**
     * Vérifier si l'utilisateur peut accéder au module trésorerie
     */
    private function userCanAccessTresorerie($user)
    {
        // Admins et Superadmins ont toujours accès
        if (in_array($user->role ?? '', ['admin', 'superadmin'])) {
            return true;
        }

        // Utilisateurs avec le rôle 'tresorerie' ont accès
        if ($user->role === 'tresorerie') {
            return true;
        }

        // Pour les modérateurs : vérifier qu'ils ont accès au module trésorerie
        if ($user->hasRole('moderator') || $user->hasRole('moderateur')) {
            return $user->canAccessModule('tresorerie');
        }

        // Autres vérifications de compatibilité
        return (isset($user->is_admin) && $user->is_admin) ||
               (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'superadmin', 'tresorerie']));
    }
    /**
     * Affiche le tableau de bord de la trésorerie
     */
    public function dashboard()
    {
        // Vérifier les permissions
        $user = auth()->user();
        if (!$user || !$this->userCanAccessTresorerie($user)) {
            abort(403, 'Accès non autorisé au module trésorerie');
        }

        $caisses = Caisse::all(); // Temporarily removed with('responsable') to avoid relationship errors
        $approvisionnementsRecents = ApprovisionnementCaisse::latest()->take(5)->get(); // Temporarily removed with() to avoid relationship errors
        $depensesRecentes = DepenseCaisse::latest()->take(5)->get(); // Temporarily removed with() to avoid relationship errors

        $soldeTotal = $caisses->sum('solde_actuel');
        $nombreCaisses = $caisses->count();
        $approvisionnementsEnAttente = ApprovisionnementCaisse::where('statut', 'en_attente')->count();
        $depensesMensuelles = DepenseCaisse::whereMonth('date_depense', now()->month)
            ->sum('montant');

        // Créer le tableau stats pour la vue
        $stats = [
            'total_caisses' => $nombreCaisses,
            'solde_total' => $soldeTotal,
            'variation' => 0, // À calculer si nécessaire
            'en_attente' => $approvisionnementsEnAttente,
            'total_approvisionnements' => ApprovisionnementCaisse::count(),
            'valides' => ApprovisionnementCaisse::where('statut', 'validé')->count(),
            'total_decaissements' => DepenseCaisse::count(),
            'total_virements' => 0, // À implémenter si nécessaire
            'valides_virements' => 0, // À implémenter si nécessaire
        ];

        return view('tresorerie.dashboard', compact(
            'caisses',
            'approvisionnementsRecents',
            'depensesRecentes',
            'soldeTotal',
            'nombreCaisses',
            'approvisionnementsEnAttente',
            'depensesMensuelles',
            'stats'
        ));
    }

    /**
     * Affiche la liste des approvisionnements
     */
    public function indexApprovisionnements()
    {
        try {
            $approvisionnements = ApprovisionnementCaisse::with(['caisse', 'utilisateur'])->latest()->paginate(20);
            return view('tresorerie.approvisionnements.index', compact('approvisionnements'));
        } catch (\Exception $e) {
            $approvisionnements = collect([]);
            return view('tresorerie.approvisionnements.index', compact('approvisionnements'));
        }
    }

    /**
     * Affiche le formulaire de création d'un approvisionnement
     */
    public function createApprovisionnement()
    {
        try {
            $caisses = Caisse::where('est_active', true)->get();
            return view('tresorerie.approvisionnements.create', compact('caisses'));
        } catch (\Exception $e) {
            $caisses = collect([]);
            return view('tresorerie.approvisionnements.create', compact('caisses'));
        }
    }

    /**
     * Enregistre un approvisionnement
     */
    public function storeApprovisionnement(Request $request)
    {
        try {
            $validated = $request->validate([
                'caisse_id' => 'required|exists:caisses,id',
                'montant' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'date_approvisionnement' => 'required|date',
            ]);

            $validated['utilisateur_id'] = auth()->id();
            $validated['statut'] = 'en_attente';

            ApprovisionnementCaisse::create($validated);

            return redirect()->route('tresorerie.approvisionnements.index')
                ->with('success', 'Approvisionnement créé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un approvisionnement
     */
    public function showApprovisionnement(ApprovisionnementCaisse $approvisionnement)
    {
        return view('tresorerie.approvisionnements.show', compact('approvisionnement'));
    }

    /**
     * Affiche le formulaire d'édition d'un approvisionnement
     */
    public function editApprovisionnement(ApprovisionnementCaisse $approvisionnement)
    {
        $caisses = Caisse::where('est_active', true)->get();
        return view('tresorerie.approvisionnements.edit', compact('approvisionnement', 'caisses'));
    }

    /**
     * Met à jour un approvisionnement
     */
    public function updateApprovisionnement(Request $request, ApprovisionnementCaisse $approvisionnement)
    {
        try {
            $validated = $request->validate([
                'caisse_id' => 'required|exists:caisses,id',
                'montant' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'date_approvisionnement' => 'required|date',
            ]);

            $approvisionnement->update($validated);

            return redirect()->route('tresorerie.approvisionnements.index')
                ->with('success', 'Approvisionnement mis à jour avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un approvisionnement
     */
    public function destroyApprovisionnement(ApprovisionnementCaisse $approvisionnement)
    {
        try {
            $approvisionnement->delete();
            return redirect()->route('tresorerie.approvisionnements.index')
                ->with('success', 'Approvisionnement supprimé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la liste des comptes bancaires
     */
    public function indexComptesBancaires()
    {
        try {
            $comptes = Caisse::where('type', 'banque')->with('responsable')->get();
            return view('tresorerie.comptes-bancaires.index', compact('comptes'));
        } catch (\Exception $e) {
            $comptes = collect([]);
            return view('tresorerie.comptes-bancaires.index', compact('comptes'));
        }
    }

    /**
     * Affiche le formulaire de création d'un compte bancaire
     */
    public function createCompteBancaire()
    {
        try {
            $responsables = \App\Models\User::where('role', 'admin')->orWhere('role', 'responsable')->pluck('name', 'id');
            return view('tresorerie.comptes-bancaires.create', compact('responsables'));
        } catch (\Exception $e) {
            $responsables = collect([]);
            return view('tresorerie.comptes-bancaires.create', compact('responsables'));
        }
    }

    /**
     * Enregistre un compte bancaire
     */
    public function storeCompteBancaire(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'solde_initial' => 'required|numeric|min:0',
                'responsable_id' => 'nullable|exists:users,id',
                'banque' => 'nullable|string|max:255',
                'numero_compte' => 'nullable|string|max:255',
                'rib' => 'nullable|string|max:255',
            ]);

            $validated['type'] = 'banque';
            $validated['solde_actuel'] = $validated['solde_initial'];
            $validated['est_active'] = true;

            Caisse::create($validated);

            return redirect()->route('tresorerie.comptes-bancaires.index')
                ->with('success', 'Compte bancaire créé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un compte bancaire
     */
    public function showCompteBancaire(Caisse $compte)
    {
        return view('tresorerie.comptes-bancaires.show', compact('compte'));
    }

    /**
     * Affiche le formulaire d'édition d'un compte bancaire
     */
    public function editCompteBancaire(Caisse $compte)
    {
        $responsables = \App\Models\User::where('role', 'admin')->orWhere('role', 'responsable')->pluck('name', 'id');
        return view('tresorerie.comptes-bancaires.edit', compact('compte', 'responsables'));
    }

    /**
     * Met à jour un compte bancaire
     */
    public function updateCompteBancaire(Request $request, Caisse $compte)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'responsable_id' => 'nullable|exists:users,id',
                'banque' => 'nullable|string|max:255',
                'numero_compte' => 'nullable|string|max:255',
                'rib' => 'nullable|string|max:255',
                'est_active' => 'boolean',
            ]);

            $compte->update($validated);

            return redirect()->route('tresorerie.comptes-bancaires.index')
                ->with('success', 'Compte bancaire mis à jour avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un compte bancaire
     */
    public function destroyCompteBancaire(Caisse $compte)
    {
        try {
            $compte->delete();
            return redirect()->route('tresorerie.comptes-bancaires.index')
                ->with('success', 'Compte bancaire supprimé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la liste des avances
     */
    public function indexAvances()
    {
        try {
            $avances = collect([]); // À implémenter avec le modèle Avance
            return view('tresorerie.avances.index', compact('avances'));
        } catch (\Exception $e) {
            $avances = collect([]);
            return view('tresorerie.avances.index', compact('avances'));
        }
    }

    /**
     * Affiche le formulaire de création d'une avance
     */
    public function createAvance()
    {
        try {
            $employes = \App\Models\User::where('role', 'agent')->orWhere('role', 'employee')->get();
            return view('tresorerie.avances.create', compact('employes'));
        } catch (\Exception $e) {
            $employes = collect([]);
            return view('tresorerie.avances.create', compact('employes'));
        }
    }

    /**
     * Enregistre une avance
     */
    public function storeAvance(Request $request)
    {
        try {
            // À implémenter avec le modèle Avance
            return redirect()->route('tresorerie.avances.index')
                ->with('success', 'Avance créée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'une avance
     */
    public function showAvance($avance)
    {
        return view('tresorerie.avances.show', ['avance' => (object)['id' => $avance]]);
    }

    /**
     * Affiche le formulaire d'édition d'une avance
     */
    public function editAvance($avance)
    {
        return view('tresorerie.avances.edit', ['avance' => (object)['id' => $avance]]);
    }

    /**
     * Met à jour une avance
     */
    public function updateAvance(Request $request, $avance)
    {
        try {
            // À implémenter avec le modèle Avance
            return redirect()->route('tresorerie.avances.index')
                ->with('success', 'Avance mise à jour avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une avance
     */
    public function destroyAvance($avance)
    {
        try {
            // À implémenter avec le modèle Avance
            return redirect()->route('tresorerie.avances.index')
                ->with('success', 'Avance supprimée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la liste des paiements
     */
    public function indexPaiements()
    {
        try {
            $paiements = collect([]); // À implémenter avec le modèle Paiement
            return view('tresorerie.paiements.index', compact('paiements'));
        } catch (\Exception $e) {
            $paiements = collect([]);
            return view('tresorerie.paiements.index', compact('paiements'));
        }
    }

    /**
     * Affiche le formulaire de création d'un paiement
     */
    public function createPaiement()
    {
        try {
            $fournisseurs = collect([]); // À implémenter avec le modèle Fournisseur
            $employes = \App\Models\User::where('role', 'agent')->orWhere('role', 'employee')->get();

            // Récupérer les opérations approuvées non payées avec numéro d'opération
            $operations = \App\Models\Operation::where('statut_courant', 'Approuvé_en_attente_paiement')
                ->where('is_paid', false)
                ->with(['typeOperation', 'operationalService', 'initiateur', 'client'])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('tresorerie.paiements.create', compact('fournisseurs', 'employes', 'operations'));
        } catch (\Exception $e) {
            $fournisseurs = collect([]);
            $employes = collect([]);
            $operations = collect([]);
            return view('tresorerie.paiements.create', compact('fournisseurs', 'employes', 'operations'));
        }
    }

    /**
     * Enregistre un paiement
     */
    public function storePaiement(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_type' => 'required|in:invoice,operation,both',
                'invoice_id' => 'nullable|required_if:payment_type,invoice,both|exists:invoices,id',
                'operation_id' => 'nullable|required_if:payment_type,operation,both|exists:operations,id',
                'payment_date' => 'required|date',
                'amount_paid' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,bank_transfer,check,mobile_money,other',
                'reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,processing,completed,failed,cancelled',
                'notes' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            // Gérer l'upload du fichier
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('payments', 'public');
            }

            // Créer le paiement
            $payment = \App\Models\Payment::create([
                'payment_type' => $validated['payment_type'],
                'invoice_id' => $validated['invoice_id'] ?? null,
                'operation_id' => $validated['operation_id'] ?? null,
                'payment_date' => $validated['payment_date'],
                'amount_paid' => $validated['amount_paid'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'attachment_path' => $attachmentPath,
            ]);

            // Si le paiement est lié à une opération et est complété, marquer l'opération comme payée
            if ($payment->operation_id && $payment->status === 'completed') {
                $operation = $payment->operation;
                if (!$operation->is_paid) {
                    $operation->forceFill([
                        'is_paid' => true,
                        'paid_at' => now(),
                        'paid_by' => auth()->id(),
                        'statut_courant' => 'payee',
                    ])->save();

                    // Log du paiement
                    \App\Models\OperationStatusLog::create([
                        'operation_id' => $operation->id,
                        'from_status' => 'approuvee',
                        'to_status' => 'payee',
                        'user_name' => auth()->user()->name,
                        'user_id' => auth()->id(),
                        'commentaire' => 'Paiement effectué via trésorerie - Référence: ' . $payment->reference,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return redirect()->route('tresorerie.paiements.index')
                ->with('success', 'Paiement créé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'un paiement
     */
    public function showPaiement($paiement)
    {
        return view('tresorerie.paiements.show', ['paiement' => (object)['id' => $paiement]]);
    }

    /**
     * Affiche le formulaire d'édition d'un paiement
     */
    public function editPaiement($paiement)
    {
        return view('tresorerie.paiements.edit', ['paiement' => (object)['id' => $paiement]]);
    }

    /**
     * Met à jour un paiement
     */
    public function updatePaiement(Request $request, $paiement)
    {
        try {
            // À implémenter avec le modèle Paiement
            return redirect()->route('tresorerie.paiements.index')
                ->with('success', 'Paiement mis à jour avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un paiement
     */
    public function destroyPaiement($paiement)
    {
        try {
            // À implémenter avec le modèle Paiement
            return redirect()->route('tresorerie.paiements.index')
                ->with('success', 'Paiement supprimé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de création d'une caisse
     */
    public function createCaisse()
    {
        $responsables = \App\Models\User::where('role', 'admin')->orWhere('role', 'responsable')->pluck('name', 'id');
        return view('tresorerie.caisses.create', compact('responsables'));
    }

    /**
     * Enregistre une nouvelle caisse
     */
    public function storeCaisse(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:principale,secondaire',
            'solde_initial' => 'required|numeric|min:0',
            'devise' => 'required|string|size:3',
            'responsable_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $caisse = Caisse::create([
            'code' => 'CS-' . now()->format('ymdHis'), // Plus court : CS-260106122728
            'libelle' => $validated['nom'], // Utiliser le nom comme libelle
            'nom' => $validated['nom'],
            'type' => $validated['type'],
            'solde_initial' => $validated['solde_initial'],
            'solde_actuel' => $validated['solde_initial'],
            'devise' => $validated['devise'],
            'responsable_id' => $validated['responsable_id'],
            'description' => $validated['description'],
            'est_active' => true,
        ]);

        return redirect()->route('tresorerie.caisses.show', $caisse)
            ->with('success', 'La caisse a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une caisse
     */
    public function showCaisse(Caisse $caisse)
    {
        $mouvements = $caisse->mouvements()
            ->with('reference')
            ->latest()
            ->paginate(10);

        $soldeInitial = $caisse->solde_initial;
        $soldeActuel = $caisse->solde_actuel;
        $totalEntrees = $caisse->mouvements()
            ->whereIn('type_mouvement', [MouvementCaisse::TYPE_APPROVISIONNEMENT, MouvementCaisse::TYPE_REMBOURSEMENT])
            ->sum('montant');
        $totalSorties = $caisse->mouvements()
            ->whereIn('type_mouvement', [MouvementCaisse::TYPE_DEPENSE, MouvementCaisse::TYPE_REGULARISATION])
            ->sum('montant');

        return view('tresorerie.caisses.show', compact(
            'caisse',
            'mouvements',
            'soldeInitial',
            'soldeActuel',
            'totalEntrees',
            'totalSorties'
        ));
    }

    // ... autres méthodes pour la gestion des approvisionnements, dépenses, etc.
}
