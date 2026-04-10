<?php

namespace App\Http\Controllers\Juridique;

use App\Http\Controllers\Controller;
use App\Models\JuridiqueContrat;
use App\Services\JuridiqueService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ContratController extends Controller
{
    protected $juridiqueService;

    public function __construct(JuridiqueService $juridiqueService)
    {
        $this->juridiqueService = $juridiqueService;
    }

    public function index(Request $request)
    {
        $query = Schema::hasTable('juridique_contrats') ? JuridiqueContrat::query() : null;

        if ($query) {
            // Filtrage
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }
            if ($request->filled('type_contrat')) {
                $query->where('type_contrat', $request->type_contrat);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                      ->orWhere('titre', 'like', "%{$search}%")
                      ->orWhere('partie_contractante', 'like', "%{$search}%");
                });
            }

            $contrats = $query->with(['documents', 'financementDossiers'])
                             ->latest()
                             ->paginate(15)
                             ->withQueryString();
        } else {
            $contrats = new LengthAwarePaginator(collect(), 0, 15);
        }

        // Statistiques pour le dashboard
        $stats = [
            'total' => Schema::hasTable('juridique_contrats') ? JuridiqueContrat::count() : 0,
            'actifs' => Schema::hasTable('juridique_contrats') ? JuridiqueContrat::actifs()->count() : 0,
            'expirant_bientot' => Schema::hasTable('juridique_contrats') ? JuridiqueContrat::expirantBientot()->count() : 0,
        ];

        return view('juridique.contrats.index', compact('contrats', 'stats'));
    }

    public function create()
    {
        return view('juridique.contrats.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:100|unique:juridique_contrats,reference',
            'titre' => 'required|string|max:255',
            'type_contrat' => 'nullable|string|max:100',
            'partie_contractante' => 'nullable|string|max:255',
            'objet' => 'nullable|string',
            'date_signature' => 'nullable|date',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'montant' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        JuridiqueContrat::create($validated);

        return redirect()->route('juridique.contrats.index')->with('success', 'Contrat enregistré avec succès.');
    }

    public function show(JuridiqueContrat $contrat)
    {
        if (Schema::hasTable('juridique_documents')) {
            $contrat->load('documents');
        }

        if (Schema::hasTable('financement_dossiers')) {
            $contrat->load('financementDossiers');
        }

        return view('juridique.contrats.show', compact('contrat'));
    }

    /**
     * Créer un dossier de financement à partir de ce contrat
     */
    public function creerFinancement(JuridiqueContrat $contrat, Request $request)
    {
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'type_financement' => 'nullable|string|max:100',
            'organisme_cible' => 'nullable|string|max:255',
            'montant_demande' => 'required|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        try {
            $dossier = $this->juridiqueService->creerDossierFinancementDepuisContrat($contrat, $validated);

            return redirect()
                ->route('juridique.financements.show', $dossier)
                ->with('success', 'Dossier de financement créé à partir du contrat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création du dossier: ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer un contrat
     */
    public function duplicate(JuridiqueContrat $contrat)
    {
        $nouveauContrat = $contrat->replicate();
        $nouveauContrat->reference = null; // Sera généré automatiquement
        $nouveauContrat->titre .= ' (Copie)';
        $nouveauContrat->statut = 'brouillon';
        $nouveauContrat->created_by = Auth::id();
        $nouveauContrat->save();

        return redirect()
            ->route('juridique.contrats.edit', $nouveauContrat)
            ->with('success', 'Contrat dupliqué avec succès.');
    }

    /**
     * Exporter les contrats
     */
    public function export(Request $request)
    {
        $query = JuridiqueContrat::query();

        // Appliquer les mêmes filtres que la méthode index
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type_contrat')) {
            $query->where('type_contrat', $request->type_contrat);
        }

        $contrats = $query->with(['documents', 'financementDossiers'])->get();

        $filename = 'contrats_juridiques_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($contrats) {
            $file = fopen('php://output', 'w');

            // En-tête CSV
            fputcsv($file, [
                'Référence', 'Titre', 'Type', 'Partie contractante',
                'Date signature', 'Date début', 'Date fin', 'Montant',
                'Devise', 'Statut', 'Documents', 'Dossiers financement'
            ]);

            foreach ($contrats as $contrat) {
                fputcsv($file, [
                    $contrat->reference,
                    $contrat->titre,
                    $contrat->type_contrat,
                    $contrat->partie_contractante,
                    $contrat->date_signature?->format('d/m/Y'),
                    $contrat->date_debut?->format('d/m/Y'),
                    $contrat->date_fin?->format('d/m/Y'),
                    $contrat->montant,
                    $contrat->devise,
                    $contrat->statut,
                    $contrat->documents->count(),
                    $contrat->financementDossiers->count(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function edit(JuridiqueContrat $contrat)
    {
        return view('juridique.contrats.edit', compact('contrat'));
    }

    public function update(Request $request, JuridiqueContrat $contrat)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:100|unique:juridique_contrats,reference,' . $contrat->id,
            'titre' => 'required|string|max:255',
            'type_contrat' => 'nullable|string|max:100',
            'partie_contractante' => 'nullable|string|max:255',
            'objet' => 'nullable|string',
            'date_signature' => 'nullable|date',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'montant' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $contrat->update($validated);

        return redirect()->route('juridique.contrats.index')->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy(JuridiqueContrat $contrat)
    {
        $contrat->delete();
        return redirect()->route('juridique.contrats.index')->with('success', 'Contrat supprimé avec succès.');
    }
}
