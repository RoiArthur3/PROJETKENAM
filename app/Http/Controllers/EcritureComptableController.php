<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EcritureComptableController extends Controller
{
    public function create()
    {
        return view('comptabilite.ecritures-create');
    }

    public function index()
    {
        return view('comptabilite.ecritures');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'compte_debit' => 'required|string',
            'compte_credit' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'journal' => 'required|string',
            'piece_comptable' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Logique pour sauvegarder l'écriture comptable (à implémenter avec la base de données)

        return response()->json([
            'success' => true,
            'message' => 'Écriture comptable ajoutée avec succès',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'compte_debit' => 'required|string',
            'compte_credit' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'journal' => 'required|string',
            'piece_comptable' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Logique pour mettre à jour l'écriture comptable

        return response()->json([
            'success' => true,
            'message' => 'Écriture comptable mise à jour avec succès',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        // Logique pour supprimer l'écriture comptable

        return response()->json([
            'success' => true,
            'message' => 'Écriture comptable supprimée avec succès'
        ]);
    }

    public function getStatistics()
    {
        // Statistiques des écritures comptables (données d'exemple)
        $stats = [
            'total_ecritures' => 156,
            'total_debit' => 45000000,
            'total_credit' => 45000000,
            'solde' => 0,
            'ecritures_mois' => 28,
            'par_journal' => [
                'Achats' => 45,
                'Ventes' => 38,
                'Banque' => 52,
                'Caisse' => 21,
            ],
            'par_compte' => [
                '512000 - Banque' => 15000000,
                '531000 - Caisse' => 8000000,
                '401000 - Fournisseurs' => 12000000,
                '411000 - Clients' => 10000000,
            ],
            'evolution_mensuelle' => [
                'Jan' => 12000000,
                'Fev' => 13500000,
                'Mar' => 15000000,
                'Avr' => 14500000,
                'Mai' => 16000000,
                'Jun' => 15000000,
            ]
        ];

        return response()->json($stats);
    }

    public function exportPDF(Request $request)
    {
        $filters = $request->all();

        // Logique pour générer le PDF du journal comptable
        return view('comptabilite.ecritures-pdf', compact('filters'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->all();

        // Logique pour générer l'export Excel
        $csvContent = "Date,Référence,Libellé,Compte Débit,Compte Crédit,Montant,Débit,Crédit,Journal,Pièce\n";

        // Données d'exemple
        $ecritures = [
            ['01/01/2024', 'AC001', 'Achat fournitures', '606000', '401000', '500000', '500000', '', 'Achats', 'FAC001'],
            ['02/01/2024', 'VT001', 'Vente marchandises', '411000', '707000', '1200000', '', '1200000', 'Ventes', 'FAC002'],
            ['03/01/2024', 'BN001', 'Paiement loyer', '613000', '512000', '1500000', '1500000', '', 'Banque', 'BN001'],
        ];

        foreach ($ecritures as $ecriture) {
            $csvContent .= implode(',', $ecriture) . "\n";
        }

        $filename = 'journal_comptable_' . date('Y-m-d') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
