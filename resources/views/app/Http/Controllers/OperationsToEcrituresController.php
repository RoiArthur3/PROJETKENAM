<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OperationsToEcrituresController extends Controller
{
    public function index()
    {
        return view('comptabilite.operations-to-ecritures');
    }

    public function getOperations()
    {
        // Simuler des données d'opérations à transformer
        $operations = [
            [
                'id' => 1,
                'date' => '2024-01-15',
                'libelle' => 'Achat fournitures bureau',
                'montant' => 45000,
                'type' => 'depense',
                'compte_debit' => '606300',
                'compte_credit' => '531000',
                'statut' => 'en_attente',
                'fournisseur' => 'Bureau Plus',
                'reference' => 'OP001'
            ],
            [
                'id' => 2,
                'date' => '2024-01-14',
                'libelle' => 'Vente produits',
                'montant' => 120000,
                'type' => 'recette',
                'compte_debit' => '531000',
                'compte_credit' => '707000',
                'statut' => 'transforme',
                'client' => 'Client A',
                'reference' => 'OP002'
            ],
            [
                'id' => 3,
                'date' => '2024-01-13',
                'libelle' => 'Paiement salaire',
                'montant' => 250000,
                'type' => 'depense',
                'compte_debit' => '641100',
                'compte_credit' => '531000',
                'statut' => 'en_attente',
                'beneficiaire' => 'Employé X',
                'reference' => 'OP003'
            ],
            [
                'id' => 4,
                'date' => '2024-01-12',
                'libelle' => 'Encaissement client',
                'montant' => 80000,
                'type' => 'recette',
                'compte_debit' => '531000',
                'compte_credit' => '411000',
                'statut' => 'transforme',
                'client' => 'Client B',
                'reference' => 'OP004'
            ],
            [
                'id' => 5,
                'date' => '2024-01-11',
                'libelle' => 'Achat matières premières',
                'montant' => 320000,
                'type' => 'depense',
                'compte_debit' => '601000',
                'compte_credit' => '531000',
                'statut' => 'en_attente',
                'fournisseur' => 'Fournisseur A',
                'reference' => 'OP005'
            ]
        ];

        return response()->json($operations);
    }

    public function getStatistics()
    {
        // Statistiques des opérations
        $stats = [
            'total_operations' => 156,
            'operations_en_attente' => 45,
            'operations_transformees' => 111,
            'taux_transformation' => 71.2,
            'periode_actuelle' => [
                'janvier' => 45,
                'fevrier' => 38,
                'mars' => 42,
                'avril' => 31
            ],
            'par_type' => [
                'depenses' => 89,
                'recettes' => 67
            ],
            'derniere_transformation' => '2024-01-15 14:30'
        ];

        return response()->json($stats);
    }

    public function transformOperation(Request $request)
    {
        $operationId = $request->input('operation_id');
        $compteDebit = $request->input('compte_debit');
        $compteCredit = $request->input('compte_credit');
        $journal = $request->input('journal', 'AC');
        $reference = $request->input('reference');

        // Logique de transformation
        return response()->json([
            'success' => true,
            'message' => 'Opération transformée avec succès',
            'data' => [
                'operation_id' => $operationId,
                'ecriture_id' => 'ECR-' . date('YmdHis'),
                'compte_debit' => $compteDebit,
                'compte_credit' => $compteCredit,
                'journal' => $journal,
                'reference' => $reference,
                'date_transformation' => now()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function transformMultiple(Request $request)
    {
        $operations = $request->input('operations', []);
        $journal = $request->input('journal', 'AC');

        // Logique de transformation multiple
        return response()->json([
            'success' => true,
            'message' => 'Opérations transformées avec succès',
            'data' => [
                'operations_count' => count($operations),
                'transformations_reussies' => count($operations),
                'transformations_echouees' => 0,
                'journal' => $journal,
                'date_transformation' => now()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function getPlanComptable()
    {
        // Plan comptable simplifié
        $planComptable = [
            'classe_1' => [
                '101' => 'Capital',
                '106' => 'Réserves',
                '120' => 'Résultat de l\'exercice'
            ],
            'classe_2' => [
                '211' => 'Terrains',
                '213' => 'Constructions',
                '215' => 'Matériel et outillage',
                '218' => 'Autres immobilisations corporelles'
            ],
            'classe_3' => [
                '311' => 'Matières premières',
                '315' => 'Produits finis',
                '355' => 'Produits intermédiaires',
                '370' => 'Stocks de marchandises'
            ],
            'classe_4' => [
                '401' => 'Fournisseurs',
                '411' => 'Clients',
                '421' => 'Personnel',
                '431' => 'Sécurité sociale',
                '444' => 'État',
                '486' => 'Charges constatées d\'avance'
            ],
            'classe_5' => [
                '511' => 'Valeurs à l\'encaissement',
                '514' => 'Banques',
                '531' => 'Caisse',
                '590' => 'Virements internes'
            ],
            'classe_6' => [
                '601' => 'Achats de matières premières',
                '606' => 'Achats non stockés',
                '613' => 'Loyers',
                '616' => 'Primes d\'assurance',
                '624' => 'Transports',
                '625' => 'Déplacements, missions',
                '626' => 'Frais postaux et télécommunications',
                '627' => 'Services bancaires',
                '641' => 'Rémunérations du personnel',
                '645' => 'Charges de sécurité sociale',
                '661' => 'Charges d\'intérêts',
                '671' => 'Charges exceptionnelles'
            ],
            'classe_7' => [
                '701' => 'Ventes de produits finis',
                '706' => 'Prestations de services',
                '707' => 'Ventes de marchandises',
                '708' => 'Produits des activités annexes',
                '761' => 'Produits de participation',
                '771' => 'Produits exceptionnels'
            ]
        ];

        return response()->json($planComptable);
    }

    public function previewTransformation(Request $request)
    {
        $operations = $request->input('operations', []);

        // Logique de prévisualisation
        return response()->json([
            'success' => true,
            'message' => 'Prévisualisation générée',
            'data' => [
                'operations_count' => count($operations),
                'total_debit' => 535000,
                'total_credit' => 535000,
                'ecritures' => [
                    [
                        'date' => '2024-01-15',
                        'libelle' => 'Achat fournitures bureau',
                        'compte_debit' => '606300',
                        'compte_credit' => '531000',
                        'montant' => 45000
                    ],
                    [
                        'date' => '2024-01-13',
                        'libelle' => 'Paiement salaire',
                        'compte_debit' => '641100',
                        'compte_credit' => '531000',
                        'montant' => 250000
                    ]
                ]
            ]
        ]);
    }

    public function exportEcritures(Request $request)
    {
        $format = $request->input('format', 'pdf');

        // Logique d'export
        return response()->json([
            'success' => true,
            'message' => "Écritures exportées en $format avec succès",
            'file_url' => "/exports/ecritures/operations-transformees.$format"
        ]);
    }
}
