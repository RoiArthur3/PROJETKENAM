<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use App\Models\ApprovisionnementCaisse;
use App\Models\Caisse;
use App\Models\MouvementCaisse;
use App\Models\User;
use App\Services\ComptabiliteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ApprovisionnementController extends Controller
{
    protected $comptabiliteService;

    public function __construct(ComptabiliteService $comptabiliteService)
    {
        $this->comptabiliteService = $comptabiliteService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', ApprovisionnementCaisse::class);

        $query = ApprovisionnementCaisse::with(['source', 'destination', 'demandeur', 'validateur'])
            ->latest();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('caisse_id')) {
            $query->where(function($q) use ($request) {
                $q->where('caisse_source_id', $request->caisse_id)
                  ->orWhere('caisse_destination_id', $request->caisse_id);
            });
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $approvisionnements = $query->paginate(20);

        $libelleColumn = Schema::hasColumn('caisses', 'nom')
            ? 'nom'
            : (Schema::hasColumn('caisses', 'libelle') ? 'libelle' : 'nom');

        $caisses = Caisse::where('est_active', true)
            ->orderBy($libelleColumn)
            ->pluck($libelleColumn, 'id');

        return view('tresorerie.approvisionnements.index', compact('approvisionnements', 'caisses'));
    }

    public function create()
    {
        $this->authorize('create', ApprovisionnementCaisse::class);

        $caisses = Caisse::where('est_active', true)->orderBy('nom')->get();
        $reference = 'APP-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        return view('tresorerie.approvisionnements.create', compact('caisses', 'reference'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', ApprovisionnementCaisse::class);

        // Caisse destination est optionnelle : si vide, on ajoute directement dans la caisse cible
        $validated = $request->validate([
            'caisse_destination_id' => 'required|exists:caisses,id',
            'caisse_source_id' => 'nullable|exists:caisses,id',
            'montant' => 'required|numeric|min:0.01',
            'motif' => 'required|string|max:255',
            'mode' => 'required|string|max:50',
            'pieces_jointes.*' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        // Si source et destination identiques, on traite comme un ajout direct
        if (!empty($validated['caisse_source_id']) && $validated['caisse_source_id'] == $validated['caisse_destination_id']) {
            $validated['caisse_source_id'] = null;
        }

        $caisseDestination = Caisse::findOrFail($validated['caisse_destination_id']);
        $caisseSource = !empty($validated['caisse_source_id']) ? Caisse::find($validated['caisse_source_id']) : null;

        // Si transfert caisse→caisse, vérifier le solde de la source
        if ($caisseSource && $caisseSource->solde_actuel < $validated['montant']) {
            return back()
                ->withInput()
                ->with('error', 'Solde insuffisant dans la caisse source (' . number_format($caisseSource->solde_actuel, 0, ',', ' ') . ' FCFA).');
        }

        try {
            DB::beginTransaction();

            // Créer l'approvisionnement
            $approvisionnement = ApprovisionnementCaisse::create([
                'caisse_source_id' => $caisseSource ? $caisseSource->id : null,
                'caisse_destination_id' => $caisseDestination->id,
                'montant' => $validated['montant'],
                'statut' => 'valide',
                'motif' => $validated['motif'],
                'mode' => $validated['mode'],
                'demandeur_id' => auth()->id(),
            ]);

            // Mettre à jour les soldes
            if ($caisseSource) {
                // Transfert caisse → caisse : débiter la source
                $caisseSource->decrement('solde_actuel', $validated['montant']);
            }
            // Créditer la caisse destination
            $caisseDestination->increment('solde_actuel', $validated['montant']);

            // Gérer les pièces jointes
            if ($request->hasFile('pieces_jointes')) {
                $this->enregistrerPiecesJointes($approvisionnement, $request->file('pieces_jointes'));
            }

            DB::commit();

            $message = $caisseSource
                ? 'Transfert de ' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA effectué avec succès.'
                : 'Approvisionnement de ' . number_format($validated['montant'], 0, ',', ' ') . ' FCFA enregistré dans la caisse.';

            return redirect()
                ->route('tresorerie.approvisionnements.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function show(ApprovisionnementCaisse $approvisionnement)
    {
        $this->authorize('view', $approvisionnement);

        $approvisionnement->load([
            'source',
            'destination',
            'demandeur',
            'validateur',
            'depenses',
            'depenses.createur',
            'depenses.compteComptable',
            'depenses.justificatifs',
        ]);

        return view('tresorerie.approvisionnements.show', compact('approvisionnement'));
    }

    public function valider(Request $request, ApprovisionnementCaisse $approvisionnement)
    {
        $this->authorize('valider', $approvisionnement);

        if ($approvisionnement->statut !== 'en_attente') {
            return back()
                ->with('error', 'Cette demande ne peut plus être validée.');
        }

        try {
            DB::beginTransaction();

            $approvisionnement->update([
                'statut' => 'valide',
                'valideur_id' => auth()->id(),
                'date_validation' => now(),
            ]);

            // TODO: Envoyer une notification au demandeur

            DB::commit();

            return back()
                ->with('success', 'La demande a été validée avec succès. Un décaissement peut maintenant être effectué.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Une erreur est survenue lors de la validation : ' . $e->getMessage());
        }
    }

    public function rejeter(Request $request, ApprovisionnementCaisse $approvisionnement)
    {
        $this->authorize('valider', $approvisionnement);

        if ($approvisionnement->statut !== 'en_attente') {
            return back()
                ->with('error', 'Cette demande ne peut plus être rejetée.');
        }

        $validated = $request->validate([
            'commentaire_rejet' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $approvisionnement->update([
                'statut' => 'rejete',
                'valideur_id' => auth()->id(),
                'date_validation' => now(),
                'commentaire_rejet' => $validated['commentaire_rejet'],
            ]);

            // Annuler le mouvement dans la caisse source
            $approvisionnement->source->mouvements()->create([
                ...MouvementCaisse::normalizePayload([
                    'type_mouvement' => MouvementCaisse::TYPE_REGULARISATION,
                    'montant' => $approvisionnement->montant,
                    'libelle' => 'Régularisation',
                    'description' => 'Annulation de la demande d\'approvisionnement rejetée - ' . $approvisionnement->motif,
                    'created_by' => auth()->id(),
                    'devise' => $approvisionnement->source->devise ?? 'XOF',
                ], $approvisionnement),
                'description' => 'Annulation de la demande d\'approvisionnement rejetée - ' . $approvisionnement->motif,
            ]);

            // TODO: Envoyer une notification au demandeur avec le motif du rejet

            DB::commit();

            return redirect()
                ->route('tresorerie.approvisionnements.index')
                ->with('success', 'La demande a été rejetée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Une erreur est survenue lors du rejet : ' . $e->getMessage());
        }
    }

    public function decaisser(ApprovisionnementCaisse $approvisionnement)
    {
        $this->authorize('decaisser', $approvisionnement);

        if ($approvisionnement->statut !== 'valide') {
            return back()
                ->with('error', 'Seules les demandes validées peuvent être décaissées.');
        }

        if ($approvisionnement->source->solde_actuel < $approvisionnement->montant) {
            return back()
                ->with('error', 'Le solde de la caisse source est insuffisant pour effectuer ce décaissement.');
        }

        try {
            DB::beginTransaction();

            // Mettre à jour le statut
            $approvisionnement->update([
                'statut' => 'decaisse',
                'date_decaissement' => now(),
            ]);

            // Mettre à jour les soldes
            $approvisionnement->source->decrement('solde_actuel', $approvisionnement->montant);
            $approvisionnement->destination->increment('solde_actuel', $approvisionnement->montant);

            // Enregistrer le mouvement dans la caisse de destination
            $approvisionnement->destination->mouvements()->create(MouvementCaisse::normalizePayload([
                'type_mouvement' => MouvementCaisse::TYPE_APPROVISIONNEMENT,
                'montant' => $approvisionnement->montant,
                'libelle' => 'Approvisionnement',
                'description' => 'Réception d\'approvisionnement de ' . ($approvisionnement->source->nom ?? $approvisionnement->source->libelle ?? '-') . ' - ' . $approvisionnement->motif,
                'created_by' => auth()->id(),
                'devise' => $approvisionnement->destination->devise ?? 'XOF',
            ], $approvisionnement));

            // Générer l'écriture comptable
            $this->comptabiliteService->genererEcritureApprovisionnement($approvisionnement);

            // TODO: Envoyer une notification au demandeur

            DB::commit();

            return redirect()
                ->route('tresorerie.approvisionnements.index')
                ->with('success', 'Le décaissement a été effectué avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Une erreur est survenue lors du décaissement : ' . $e->getMessage());
        }
    }

    protected function enregistrerPiecesJointes($approvisionnement, $fichiers)
    {
        $dossier = 'approvisionnements/' . $approvisionnement->id;

        foreach ($fichiers as $fichier) {
            $chemin = $fichier->store($dossier, 'public');

            $approvisionnement->piecesJointes()->create([
                'nom_original' => $fichier->getClientOriginalName(),
                'chemin' => $chemin,
                'mime_type' => $fichier->getClientMimeType(),
                'taille' => $fichier->getSize(),
                'upload_par' => auth()->id(),
            ]);
        }
    }
}
