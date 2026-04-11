<?php

namespace App\Http\Controllers\Juridique;

use App\Http\Controllers\Controller;
use App\Models\JuridiqueFinancement;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FinancementController extends Controller
{
    public function index()
    {
        $dossiers = Schema::hasTable('juridique_financements')
            ? JuridiqueFinancement::with('contrat')->latest()->paginate(15)
            : new LengthAwarePaginator(collect(), 0, 15);

        return view('juridique.financements.index', compact('dossiers'));
    }

    public function create()
    {
        $contrats = Schema::hasTable('juridique_contrats')
            ? \App\Models\JuridiqueContrat::orderBy('titre')->get()
            : collect();

        return view('juridique.financements.create', compact('contrats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:100|unique:juridique_financements,reference',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contrat_id' => 'nullable|exists:juridique_contrats,id',
            'type_financement' => 'nullable|string|max:100',
            'organisme_preteur' => 'nullable|string|max:255',
            'montant_emprunte' => 'nullable|numeric|min:0',
            'taux_interet' => 'nullable|numeric|min:0|max:100',
            'duree_mois' => 'nullable|integer|min:1',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'mensualite' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        JuridiqueFinancement::create($validated);

        return redirect()->route('juridique.financements.index')->with('success', 'Dossier de financement enregistré avec succès.');
    }

    public function show(JuridiqueFinancement $financement)
    {
        if (Schema::hasTable('financement_offres_bancaires')) {
            $financement->load('offresBancaires');
        }

        return view('juridique.financements.show', ['dossier' => $financement]);
    }

    public function edit(JuridiqueFinancement $financement)
    {
        $contrats = Schema::hasTable('juridique_contrats')
            ? \App\Models\JuridiqueContrat::orderBy('titre')->get()
            : collect();

        return view('juridique.financements.edit', ['dossier' => $financement, 'contrats' => $contrats]);
    }

    public function update(Request $request, JuridiqueFinancement $financement)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:100|unique:juridique_financements,reference,' . $financement->id,
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contrat_id' => 'nullable|exists:juridique_contrats,id',
            'type_financement' => 'nullable|string|max:100',
            'organisme_preteur' => 'nullable|string|max:255',
            'montant_emprunte' => 'nullable|numeric|min:0',
            'taux_interet' => 'nullable|numeric|min:0|max:100',
            'duree_mois' => 'nullable|integer|min:1',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'mensualite' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $financement->update($validated);

        return redirect()->route('juridique.financements.index')->with('success', 'Dossier de financement mis à jour avec succès.');
    }

    public function destroy(JuridiqueFinancement $financement)
    {
        $financement->delete();
        return redirect()->route('juridique.financements.index')->with('success', 'Dossier de financement supprimé avec succès.');
    }
}
