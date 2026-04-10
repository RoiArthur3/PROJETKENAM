<?php

namespace App\Http\Controllers\Juridique;

use App\Http\Controllers\Controller;
use App\Models\FinancementEcheance;
use App\Models\FinancementOffreBancaire;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class EcheanceController extends Controller
{
    public function index()
    {
        $echeances = Schema::hasTable('financement_echeances')
            ? FinancementEcheance::with('offre')->orderBy('date_echeance')->paginate(20)
            : new LengthAwarePaginator(collect(), 0, 20);

        return view('juridique.echeances.index', compact('echeances'));
    }

    public function create()
    {
        $offres = Schema::hasTable('financement_offres_bancaires')
            ? FinancementOffreBancaire::orderBy('banque')->get()
            : collect();

        return view('juridique.echeances.create', compact('offres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'offre_id' => 'required|exists:financement_offres_bancaires,id',
            'numero_echeance' => 'required|integer|min:1',
            'date_echeance' => 'required|date',
            'capital' => 'nullable|numeric|min:0',
            'interet' => 'nullable|numeric|min:0',
            'mensualite' => 'nullable|numeric|min:0',
            'solde_restant' => 'nullable|numeric|min:0',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        FinancementEcheance::create($validated);

        return redirect()->route('juridique.echeances.index')->with('success', 'Échéance enregistrée avec succès.');
    }

    public function show(FinancementEcheance $echeance)
    {
        if (Schema::hasTable('financement_offres_bancaires')) {
            $echeance->load('offre');
        }

        return view('juridique.echeances.show', compact('echeance'));
    }

    public function edit(FinancementEcheance $echeance)
    {
        $offres = Schema::hasTable('financement_offres_bancaires')
            ? FinancementOffreBancaire::orderBy('banque')->get()
            : collect();

        return view('juridique.echeances.edit', compact('echeance', 'offres'));
    }

    public function update(Request $request, FinancementEcheance $echeance)
    {
        $validated = $request->validate([
            'offre_id' => 'required|exists:financement_offres_bancaires,id',
            'numero_echeance' => 'required|integer|min:1',
            'date_echeance' => 'required|date',
            'capital' => 'nullable|numeric|min:0',
            'interet' => 'nullable|numeric|min:0',
            'mensualite' => 'nullable|numeric|min:0',
            'solde_restant' => 'nullable|numeric|min:0',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $echeance->update($validated);

        return redirect()->route('juridique.echeances.index')->with('success', 'Échéance mise à jour avec succès.');
    }

    public function destroy(FinancementEcheance $echeance)
    {
        $echeance->delete();
        return redirect()->route('juridique.echeances.index')->with('success', 'Échéance supprimée avec succès.');
    }
}
