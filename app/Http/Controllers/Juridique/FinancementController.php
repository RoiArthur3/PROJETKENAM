<?php

namespace App\Http\Controllers\Juridique;

use App\Http\Controllers\Controller;
use App\Models\FinancementDossier;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FinancementController extends Controller
{
    public function index()
    {
        $dossiers = Schema::hasTable('financement_dossiers')
            ? FinancementDossier::latest()->paginate(15)
            : new LengthAwarePaginator(collect(), 0, 15);

        return view('juridique.financements.index', compact('dossiers'));
    }

    public function create()
    {
        return view('juridique.financements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:100|unique:financement_dossiers,reference',
            'intitule' => 'required|string|max:255',
            'type_financement' => 'nullable|string|max:100',
            'organisme_cible' => 'nullable|string|max:255',
            'montant_demande' => 'nullable|numeric|min:0',
            'montant_obtenu' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'date_depot' => 'nullable|date',
            'date_validation' => 'nullable|date',
            'statut' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        FinancementDossier::create($validated);

        return redirect()->route('juridique.financements.index')->with('success', 'Dossier de financement enregistré avec succès.');
    }

    public function show(FinancementDossier $financement)
    {
        if (Schema::hasTable('financement_offres_bancaires')) {
            $financement->load('offresBancaires');
        }

        return view('juridique.financements.show', ['dossier' => $financement]);
    }

    public function edit(FinancementDossier $financement)
    {
        return view('juridique.financements.edit', ['dossier' => $financement]);
    }

    public function update(Request $request, FinancementDossier $financement)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:100|unique:financement_dossiers,reference,' . $financement->id,
            'intitule' => 'required|string|max:255',
            'type_financement' => 'nullable|string|max:100',
            'organisme_cible' => 'nullable|string|max:255',
            'montant_demande' => 'nullable|numeric|min:0',
            'montant_obtenu' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'date_depot' => 'nullable|date',
            'date_validation' => 'nullable|date',
            'statut' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $financement->update($validated);

        return redirect()->route('juridique.financements.index')->with('success', 'Dossier de financement mis à jour avec succès.');
    }

    public function destroy(FinancementDossier $financement)
    {
        $financement->delete();
        return redirect()->route('juridique.financements.index')->with('success', 'Dossier de financement supprimé avec succès.');
    }
}
