<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacturationController extends Controller
{
    public function index()
    {
        return view('comptabilite.facturation');
    }

    public function create()
    {
        return view('comptabilite.facturation-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|integer',
            'numero_facture' => 'required|string|max:255',
            'date_facture' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_facture',
            'articles' => 'required|array',
            'articles.*.description' => 'required|string',
            'articles.*.quantite' => 'required|numeric|min:0',
            'articles.*.prix_unitaire' => 'required|numeric|min:0',
            'articles.*.tva' => 'required|numeric|min:0|max:100',
            'remise_globale' => 'nullable|numeric|min:0|max:100',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Logique pour sauvegarder la facture (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Facture créée avec succès',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        // Logique pour récupérer les détails de la facture
        return view('comptabilite.facturation-show', compact('id'));
    }

    public function edit($id)
    {
        return view('comptabilite.facturation-edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'client_id' => 'required|integer',
            'numero_facture' => 'required|string|max:255',
            'date_facture' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_facture',
            'articles' => 'required|array',
            'articles.*.description' => 'required|string',
            'articles.*.quantite' => 'required|numeric|min:0',
            'articles.*.prix_unitaire' => 'required|numeric|min:0',
            'articles.*.tva' => 'required|numeric|min:0|max:100',
            'remise_globale' => 'nullable|numeric|min:0|max:100',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Logique pour mettre à jour la facture

        return response()->json([
            'success' => true,
            'message' => 'Facture mise à jour avec succès',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        // Logique pour supprimer la facture

        return response()->json([
            'success' => true,
            'message' => 'Facture supprimée avec succès'
        ]);
    }

    public function getStatistics()
    {
        // Statistiques de facturation (données d'exemple)
        $stats = [
            'total_factures' => 45,
            'factures_en_attente' => 12,
            'factures_payees' => 28,
            'factures_en_retard' => 5,
            'montant_total' => 85000000,
            'montant_en_attente' => 15000000,
            'montant_paye' => 65000000,
            'montant_en_retard' => 5000000,
            'par_mois' => [
                'Jan' => 12000000,
                'Fev' => 13500000,
                'Mar' => 15000000,
                'Avr' => 14500000,
                'Mai' => 16000000,
                'Jun' => 14000000,
            ],
            'top_clients' => [
                ['nom' => 'Client A', 'montant' => 15000000],
                ['nom' => 'Client B', 'montant' => 12000000],
                ['nom' => 'Client C', 'montant' => 8000000],
            ]
        ];

        return response()->json($stats);
    }

    public function exportPDF(Request $request)
    {
        $filters = $request->all();

        // Logique pour générer le PDF des factures
        return view('comptabilite.facturation-pdf', compact('filters'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->all();

        // Logique pour générer l'export Excel
        $csvContent = "Numéro,Client,Date,Échéance,Montant HT,TVA,Montant TTC,Statut\n";

        // Données d'exemple
        $factures = [
            ['FAC001', 'Client A', '01/01/2024', '31/01/2024', '10000000', '1800000', '11800000', 'Payée'],
            ['FAC002', 'Client B', '05/01/2024', '05/02/2024', '8000000', '1440000', '9440000', 'En attente'],
            ['FAC003', 'Client C', '10/01/2024', '10/02/2024', '12000000', '2160000', '14160000', 'En retard'],
        ];

        foreach ($factures as $facture) {
            $csvContent .= implode(',', $facture) . "\n";
        }

        $filename = 'factures_' . date('Y-m-d') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
