<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FactureComptableController extends Controller
{
    public function index()
    {
        return view('comptabilite.factures');
    }

    public function create()
    {
        return view('comptabilite.factures-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:client,fournisseur',
            'numero' => 'required|string|max:50',
            'date_emission' => 'required|date',
            'date_echeance' => 'required|date',
            'tiers' => 'required|string|max:255',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'statut' => 'required|in:paye,partiellement_paye,impayee,annulee',
            'description' => 'nullable|string'
        ]);

        // Logique de stockage (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Facture créée avec succès',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        return view('comptabilite.factures-show', compact('id'));
    }

    public function edit($id)
    {
        return view('comptabilite.factures-edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'type' => 'required|in:client,fournisseur',
            'numero' => 'required|string|max:50',
            'date_emission' => 'required|date',
            'date_echeance' => 'required|date',
            'tiers' => 'required|string|max:255',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'statut' => 'required|in:paye,partiellement_paye,impayee,annulee',
            'description' => 'nullable|string'
        ]);

        // Logique de mise à jour (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Facture mise à jour avec succès',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        // Logique de suppression (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Facture supprimée avec succès'
        ]);
    }

    public function getStatistics()
    {
        // Statistiques des factures (données d'exemple)
        $stats = [
            'total_factures' => 145,
            'factures_clients' => 89,
            'factures_fournisseurs' => 56,
            'montant_total' => 12500000,
            'montant_en_attente' => 3200000,
            'factures_payees' => 98,
            'factures_impayees' => 12,
            'factures_partiellement_payees' => 35,
            'mois_courant' => [
                'emises' => 23,
                'payees' => 18,
                'impayees' => 5
            ]
        ];

        return response()->json($stats);
    }
}
