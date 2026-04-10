<?php

namespace App\Http\Controllers\Juridique;

use App\Http\Controllers\Controller;
use App\Models\FinancementDossier;
use App\Models\FinancementEcheance;
use App\Models\FinancementOffreBancaire;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OffreBancaireController extends Controller
{
    public function index()
    {
        $offres = Schema::hasTable('financement_offres_bancaires')
            ? FinancementOffreBancaire::with('dossier')->latest()->paginate(15)
            : new LengthAwarePaginator(collect(), 0, 15);

        return view('juridique.offres.index', compact('offres'));
    }

    public function create()
    {
        $dossiers = Schema::hasTable('financement_dossiers')
            ? FinancementDossier::orderBy('intitule')->get()
            : collect();

        return view('juridique.offres.create', compact('dossiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dossier_id' => 'required|exists:financement_dossiers,id',
            'reference' => 'nullable|string|max:100|unique:financement_offres_bancaires,reference',
            'banque' => 'required|string|max:255',
            'montant_propose' => 'nullable|numeric|min:0',
            'taux_interet' => 'nullable|numeric|min:0',
            'duree_mois' => 'nullable|integer|min:1',
            'date_proposition' => 'nullable|date',
            'date_acceptation' => 'nullable|date',
            'statut' => 'nullable|string|max:50',
            'conditions' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        FinancementOffreBancaire::create($validated);

        return redirect()->route('juridique.offres.index')->with('success', 'Offre bancaire enregistrée avec succès.');
    }

    public function show(FinancementOffreBancaire $offre)
    {
        $relations = [];

        if (Schema::hasTable('financement_dossiers')) {
            $relations[] = 'dossier';
        }

        if (Schema::hasTable('financement_echeances')) {
            $relations['echeances'] = function ($query) {
                $query->orderBy('numero_echeance');
            };
        }

        if (!empty($relations)) {
            $offre->load($relations);
        }

        return view('juridique.offres.show', compact('offre'));
    }

    public function generateEcheancier(Request $request, FinancementOffreBancaire $offre)
    {
        $validated = $request->validate([
            'date_premiere_echeance' => 'required|date',
        ]);

        $montant = (float) ($offre->montant_propose ?? 0);
        $duree = (int) ($offre->duree_mois ?? 0);
        $tauxAnnuel = (float) ($offre->taux_interet ?? 0);

        if ($montant <= 0 || $duree <= 0) {
            return redirect()->route('juridique.offres.show', $offre)
                ->with('error', 'Le montant proposé et la durée doivent être renseignés pour générer un échéancier.');
        }

        DB::transaction(function () use ($offre, $validated, $montant, $duree, $tauxAnnuel) {
            FinancementEcheance::where('offre_id', $offre->id)->delete();

            $dateEcheance = Carbon::parse($validated['date_premiere_echeance']);
            $tauxMensuel = $tauxAnnuel > 0 ? ($tauxAnnuel / 100) / 12 : 0;
            $mensualite = $tauxMensuel > 0
                ? ($montant * $tauxMensuel) / (1 - pow(1 + $tauxMensuel, -$duree))
                : ($montant / $duree);

            $soldeRestant = $montant;

            for ($i = 1; $i <= $duree; $i++) {
                $interet = $tauxMensuel > 0 ? $soldeRestant * $tauxMensuel : 0;
                $capital = $mensualite - $interet;

                if ($i === $duree) {
                    $capital = $soldeRestant;
                    $mensualite = $capital + $interet;
                }

                $soldeRestant = max(0, $soldeRestant - $capital);

                FinancementEcheance::create([
                    'offre_id' => $offre->id,
                    'numero_echeance' => $i,
                    'date_echeance' => $dateEcheance->copy()->addMonths($i - 1)->toDateString(),
                    'capital' => round($capital, 2),
                    'interet' => round($interet, 2),
                    'mensualite' => round($mensualite, 2),
                    'solde_restant' => round($soldeRestant, 2),
                    'statut' => 'a_payer',
                    'notes' => 'Généré automatiquement depuis l’offre bancaire',
                ]);
            }
        });

        return redirect()->route('juridique.offres.show', $offre)
            ->with('success', 'Échéancier généré avec succès.');
    }

    public function edit(FinancementOffreBancaire $offre)
    {
        $dossiers = Schema::hasTable('financement_dossiers')
            ? FinancementDossier::orderBy('intitule')->get()
            : collect();

        return view('juridique.offres.edit', compact('offre', 'dossiers'));
    }

    public function update(Request $request, FinancementOffreBancaire $offre)
    {
        $validated = $request->validate([
            'dossier_id' => 'required|exists:financement_dossiers,id',
            'reference' => 'nullable|string|max:100|unique:financement_offres_bancaires,reference,' . $offre->id,
            'banque' => 'required|string|max:255',
            'montant_propose' => 'nullable|numeric|min:0',
            'taux_interet' => 'nullable|numeric|min:0',
            'duree_mois' => 'nullable|integer|min:1',
            'date_proposition' => 'nullable|date',
            'date_acceptation' => 'nullable|date',
            'statut' => 'nullable|string|max:50',
            'conditions' => 'nullable|string',
        ]);

        $offre->update($validated);

        return redirect()->route('juridique.offres.index')->with('success', 'Offre bancaire mise à jour avec succès.');
    }

    public function destroy(FinancementOffreBancaire $offre)
    {
        $offre->delete();
        return redirect()->route('juridique.offres.index')->with('success', 'Offre bancaire supprimée avec succès.');
    }
}
