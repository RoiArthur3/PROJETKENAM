<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index()
    {
        return view('comptabilite.classification');
    }

    public function getStatistics()
    {
        // Statistiques de classification (données d'exemple)
        $stats = [
            'total_transactions' => 156,
            'transactions_classifiees' => 142,
            'transactions_non_classifiees' => 14,
            'taux_classification' => 91.0,
            'depenses_vs_charges' => [
                'depenses' => 89,
                'charges' => 67
            ],
            'categories' => [
                'exploitation' => 45,
                'financieres' => 23,
                'exceptionnelles' => 12,
                'personnel' => 38,
                'externes' => 24
            ],
            'regles_automatiques' => 12,
            'classifications_manuelles' => 130
        ];

        return response()->json($stats);
    }

    public function getTransactions(Request $request)
    {
        $periode = $request->input('periode', 'mois');
        $type = $request->input('type', 'tous');
        $categorie = $request->input('categorie', 'toutes');

        // Simuler des données de transactions
        $transactions = [
            [
                'id' => 1,
                'date' => '2024-01-15',
                'libelle' => 'Achat fournitures bureau',
                'montant' => 45000,
                'type' => 'charge',
                'categorie' => 'exploitation',
                'sous_categorie' => '606',
                'statut' => 'classifié',
                'fournisseur' => 'Bureau Plus'
            ],
            [
                'id' => 2,
                'date' => '2024-01-14',
                'libelle' => 'Loyer janvier',
                'montant' => 150000,
                'type' => 'charge',
                'categorie' => 'exploitation',
                'sous_categorie' => '613',
                'statut' => 'classifié',
                'fournisseur' => 'Propriétaire'
            ],
            [
                'id' => 3,
                'date' => '2024-01-13',
                'libelle' => 'Carburant véhicule',
                'montant' => 25000,
                'type' => 'depense',
                'categorie' => 'transport',
                'sous_categorie' => '624',
                'statut' => 'non classifié',
                'fournisseur' => 'Total'
            ],
            [
                'id' => 4,
                'date' => '2024-01-12',
                'libelle' => 'Prime assurance',
                'montant' => 75000,
                'type' => 'charge',
                'categorie' => 'externes',
                'sous_categorie' => '616',
                'statut' => 'classifié',
                'fournisseur' => 'AXA'
            ],
            [
                'id' => 5,
                'date' => '2024-01-11',
                'libelle' => 'Achat matières premières',
                'montant' => 320000,
                'type' => 'charge',
                'categorie' => 'exploitation',
                'sous_categorie' => '601',
                'statut' => 'classifié',
                'fournisseur' => 'Fournisseur A'
            ]
        ];

        return response()->json($transactions);
    }

    public function classifierTransaction(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $type = $request->input('type');
        $categorie = $request->input('categorie');
        $sousCategorie = $request->input('sous_categorie');
        $notes = $request->input('notes');

        // Logique de classification
        return response()->json([
            'success' => true,
            'message' => 'Transaction classifiée avec succès',
            'data' => [
                'transaction_id' => $transactionId,
                'type' => $type,
                'categorie' => $categorie,
                'sous_categorie' => $sousCategorie,
                'notes' => $notes,
                'date_classification' => now()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function classificationAutomatique()
    {
        // Logique de classification automatique
        return response()->json([
            'success' => true,
            'message' => 'Classification automatique lancée',
            'data' => [
                'transactions_traitees' => 14,
                'classifications_reussies' => 12,
                'classifications_echouees' => 2,
                'temps_execution' => '2.3s'
            ]
        ]);
    }

    public function exportClassification(Request $request)
    {
        $format = $request->input('format', 'pdf');

        // Logique d'export
        return response()->json([
            'success' => true,
            'message' => "Export en $format généré avec succès",
            'file_url' => "/exports/classification.$format"
        ]);
    }

    public function create()
    {
        return view('comptabilite.classification.regles.create');
    }

    public function ajouterRegle(Request $request)
    {
        $regle = $request->all();

        // Logique d'ajout de règle
        return response()->json([
            'success' => true,
            'message' => 'Règle de classification ajoutée avec succès',
            'data' => $regle
        ]);
    }
}
