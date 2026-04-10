<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use App\Models\ApprovisionnementCaisse;
use App\Models\Caisse;
use App\Models\CompteComptable;
use App\Models\DepenseCaisse;
use App\Models\Justificatif;
use App\Services\ComptabiliteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DepenseController extends Controller
{
    protected $comptabiliteService;

    public function __construct(ComptabiliteService $comptabiliteService)
    {
        $this->comptabiliteService = $comptabiliteService;
    }

    public function create(ApprovisionnementCaisse $approvisionnement)
    {
        $this->authorize('create', [DepenseCaisse::class, $approvisionnement]);

        $comptes = CompteComptable::where('type', 'charge')
            ->orderBy('numero')
            ->pluck('intitule', 'id');

        return view('tresorerie.depenses.create', compact('approvisionnement', 'comptes'));
    }

    public function store(Request $request, ApprovisionnementCaisse $approvisionnement)
    {
        $this->authorize('create', [DepenseCaisse::class, $approvisionnement]);

        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01|max:' . $approvisionnement->solde_restant,
            'compte_comptable_id' => 'required|exists:comptes_comptables,id',
            'date_depense' => 'required|date',
            'notes' => 'nullable|string',
            'justificatifs.*' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        try {
            DB::beginTransaction();

            // Créer la dépense
            $depense = DepenseCaisse::create([
                'approvisionnement_id' => $approvisionnement->id,
                'caisse_id' => $approvisionnement->caisse_destination_id,
                'montant' => $validated['montant'],
                'libelle' => $validated['libelle'],
                'compte_comptable_id' => $validated['compte_comptable_id'],
                'created_by' => auth()->id(),
                'date_depense' => $validated['date_depense'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Mettre à jour le solde de la caisse
            $approvisionnement->destination->decrement('solde_actuel', $validated['montant']);

            // Enregistrer le mouvement de caisse
            $approvisionnement->destination->mouvements()->create([
                'type_mouvement' => 'depense',
                'montant' => -$validated['montant'],
                'reference_type' => get_class($depense),
                'reference_id' => $depense->id,
                'description' => 'Dépense: ' . $validated['libelle'],
                'created_by' => auth()->id(),
            ]);

            // Gérer les justificatifs
            if ($request->hasFile('justificatifs')) {
                $this->enregistrerJustificatifs($depense, $request->file('justificatifs'));
            }

            // Générer l'écriture comptable si nécessaire
            if ($approvisionnement->statut === 'decaisse') {
                $this->comptabiliteService->genererEcritureDepense($depense);
            }

            // Vérifier si l'approvisionnement est complètement dépensé
            $this->verifierClotureApprovisionnement($approvisionnement);

            DB::commit();

            return redirect()
                ->route('tresorerie.approvisionnements.show', $approvisionnement)
                ->with('success', 'La dépense a été enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de la dépense : ' . $e->getMessage());
        }
    }

    public function justifier(Request $request, DepenseCaisse $depense)
    {
        $this->authorize('justifier', $depense);

        $validated = $request->validate([
            'justificatifs.*' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        try {
            DB::beginTransaction();

            // Gérer les justificatifs
            if ($request->hasFile('justificatifs')) {
                $this->enregistrerJustificatifs($depense, $request->file('justificatifs'));

                // Marquer la dépense comme justifiée
                if (!$depense->est_justifie) {
                    $depense->update(['est_justifie' => true]);
                }
            }

            // Vérifier si l'approvisionnement peut être marqué comme justifié
            $this->verifierJustificationApprovisionnement($depense->approvisionnement);

            DB::commit();

            return back()
                ->with('success', 'Les justificatifs ont été enregistrés avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des justificatifs : ' . $e->getMessage());
        }
    }

    public function validerJustificatif(Request $request, Justificatif $justificatif)
    {
        $this->authorize('valider', $justificatif);

        $validated = $request->validate([
            'valide' => 'required|boolean',
            'commentaires' => 'required_if:valide,false|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $justificatif->update([
                'valide_par' => $validated['valide'] ? auth()->id() : null,
                'date_validation' => $validated['valide'] ? now() : null,
                'commentaires' => $validated['commentaires'] ?? null,
            ]);

            // Vérifier si tous les justificatifs de la dépense sont validés
            $depense = $justificatif->depense;
            $tousValides = $depense->justificatifs()->whereNull('valide_par')->doesntExist();

            if ($tousValides) {
                $depense->update(['est_justifie' => true]);

                // Vérifier si l'approvisionnement peut être marqué comme justifié
                $this->verifierJustificationApprovisionnement($depense->approvisionnement);
            }

            DB::commit();

            $message = $validated['valide']
                ? 'Le justificatif a été validé avec succès.'
                : 'Le justificatif a été marqué comme non valide.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Une erreur est survenue lors de la validation du justificatif : ' . $e->getMessage());
        }
    }

    protected function enregistrerJustificatifs(DepenseCaisse $depense, $fichiers)
    {
        $dossier = 'justificatifs/depenses/' . $depense->id;

        foreach ($fichiers as $fichier) {
            $chemin = $fichier->store($dossier, 'public');

            $depense->justificatifs()->create([
                'fichier' => $chemin,
                'type_fichier' => $fichier->getClientMimeType(),
                'nom_original' => $fichier->getClientOriginalName(),
                'valide_par' => null,
                'date_validation' => null,
                'commentaires' => null,
            ]);
        }
    }

    protected function verifierJustificationApprovisionnement(ApprovisionnementCaisse $approvisionnement)
    {
        // Vérifier si toutes les dépenses sont justifiées
        $toutesJustifiees = $approvisionnement->depenses()
            ->where('est_justifie', false)
            ->doesntExist();

        if ($toutesJustifiees && $approvisionnement->statut === 'decaisse') {
            $approvisionnement->update([
                'statut' => 'justifie',
                'date_cloture' => now(),
            ]);

            // TODO: Envoyer une notification de clôture
        }
    }

    protected function verifierClotureApprovisionnement(ApprovisionnementCaisse $approvisionnement)
    {
        $seuilMinimal = 100; // 1 FCFA

        if ($approvisionnement->solde_restant <= $seuilMinimal) {
            // Générer un remboursement si nécessaire
            if ($approvisionnement->solde_restant > 0) {
                $this->comptabiliteService->genererEcritureRemboursement($approvisionnement);

                // Mettre à jour le solde de la caisse source
                $approvisionnement->source->increment('solde_actuel', $approvisionnement->solde_restant);

                // Mettre à jour le solde de la caisse destination
                $approvisionnement->destination->decrement('solde_actuel', $approvisionnement->solde_restant);
            }

            // Marquer comme clôturé
            $approvisionnement->update([
                'statut' => 'cloture',
                'date_cloture' => now(),
            ]);

            // TODO: Envoyer une notification de clôture
        }
    }
}
