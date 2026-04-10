<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovisionnementCaisse;
use App\Models\Caisse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ApprovisionnementCaisseController extends Controller
{
    /**
     * Affiche la liste des approvisionnements
     */
    public function index()
    {
        $approvisionnements = ApprovisionnementCaisse::with(['source', 'destination', 'demandeur', 'validateur'])
            ->latest()
            ->paginate(15);

        $statistiques = [
            'total' => ApprovisionnementCaisse::count(),
            'en_attente' => ApprovisionnementCaisse::where('statut', 'en_attente')->count(),
            'valides' => ApprovisionnementCaisse::where('statut', 'valide')->count(),
            'rejetes' => ApprovisionnementCaisse::where('statut', 'rejete')->count(),
            'montant_total' => ApprovisionnementCaisse::sum('montant'),
        ];

        return view('tresorerie.approvisionnements.index', compact('approvisionnements', 'statistiques'));
    }

    /**
     * Affiche le formulaire de création d'un approvisionnement
     */
    public function create()
    {
        $caisses = Caisse::where('est_active', true)->get();
        return view('tresorerie.approvisionnements.create', compact('caisses'));
    }

    /**
     * Enregistre un nouvel approvisionnement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'caisse_source_id' => 'required|exists:caisses,id',
            'caisse_destination_id' => 'required|exists:caisses,id|different:caisse_source_id',
            'montant' => 'required|numeric|min:0.01',
            'motif' => 'required|string|max:1000',
        ]);

        // Vérifier que la caisse source a suffisamment de fonds
        $caisseSource = Caisse::findOrFail($validated['caisse_source_id']);
        if ($caisseSource->solde_actuel < $validated['montant']) {
            return back()->with('error', 'Solde insuffisant dans la caisse source.')->withInput();
        }

        try {
            DB::beginTransaction();

            // Créer l'approvisionnement
            $approvisionnement = new ApprovisionnementCaisse();
            $approvisionnement->caisse_source_id = $validated['caisse_source_id'];
            $approvisionnement->caisse_destination_id = $validated['caisse_destination_id'];
            $approvisionnement->montant = $validated['montant'];
            $approvisionnement->motif = $validated['motif'];
            $approvisionnement->demandeur_id = Auth::id();
            $approvisionnement->statut = 'en_attente';
            $approvisionnement->save();

            DB::commit();

            return redirect()->route('tresorerie.approvisionnements.show', $approvisionnement)
                ->with('success', 'Demande d\'approvisionnement enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement.')->withInput();
        }
    }

    /**
     * Affiche les détails d'un approvisionnement
     */
    public function show(ApprovisionnementCaisse $approvisionnement)
    {
        $approvisionnement->load(['source', 'destination', 'demandeur', 'validateur', 'depenses']);
        return view('tresorerie.approvisionnements.show', compact('approvisionnement'));
    }

    /**
     * Valide un approvisionnement
     */
    public function valider(Request $request, ApprovisionnementCaisse $approvisionnement)
    {
        if ($approvisionnement->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $caisseSource = $approvisionnement->source;
        if ($caisseSource->solde_actuel < $approvisionnement->montant) {
            return back()->with('error', 'Solde insuffisant dans la caisse source pour valider cette demande.');
        }

        try {
            DB::beginTransaction();

            // Mettre à jour le statut
            $approvisionnement->statut = 'valide';
            $approvisionnement->validateur_id = Auth::id();
            $approvisionnement->date_validation = now();
            $approvisionnement->save();

            // Mettre à jour les soldes des caisses
            $caisseSource->decrement('solde_actuel', $approvisionnement->montant);
            $approvisionnement->destination->increment('solde_actuel', $approvisionnement->montant);

            // Enregistrer le mouvement de caisse
            $mouvementSource = new \App\Models\MouvementCaisse([
                'caisse_id' => $caisseSource->id,
                'type_mouvement' => \App\Models\MouvementCaisse::TYPE_APPROVISIONNEMENT,
                'montant' => $approvisionnement->montant,
                'reference_type' => get_class($approvisionnement),
                'reference_id' => $approvisionnement->id,
                'description' => 'Approvisionnement vers ' . $approvisionnement->destination->nom,
                'created_by' => Auth::id(),
            ]);
            $mouvementSource->save();

            $mouvementDestination = new \App\Models\MouvementCaisse([
                'caisse_id' => $approvisionnement->caisse_destination_id,
                'type_mouvement' => \App\Models\MouvementCaisse::TYPE_APPROVISIONNEMENT,
                'montant' => $approvisionnement->montant,
                'reference_type' => get_class($approvisionnement),
                'reference_id' => $approvisionnement->id,
                'description' => 'Approvisionnement depuis ' . $caisseSource->nom,
                'created_by' => Auth::id(),
            ]);
            $mouvementDestination->save();

            DB::commit();

            return back()->with('success', 'L\'approvisionnement a été validé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la validation.');
        }
    }

    /**
     * Rejette un approvisionnement
     */
    public function rejeter(Request $request, ApprovisionnementCaisse $approvisionnement)
    {
        if ($approvisionnement->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $request->validate([
            'commentaire_rejet' => 'required|string|max:1000',
        ]);

        $approvisionnement->update([
            'statut' => 'rejete',
            'validateur_id' => Auth::id(),
            'date_validation' => now(),
            'commentaire_rejet' => $request->commentaire_rejet,
        ]);

        return back()->with('success', 'La demande d\'approvisionnement a été rejetée.');
    }

    // ... autres méthodes pour la gestion des approvisionnements
}
