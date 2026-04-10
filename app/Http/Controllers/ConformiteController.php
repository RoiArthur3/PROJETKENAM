<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConformiteController extends Controller
{
    public function index()
    {
        return view('comptabilite.conformite');
    }

    public function getStatistics()
    {
        // Statistiques de conformité (données d'exemple)
        $stats = [
            'declarations_tva' => [
                'total' => 12,
                'a_jour' => 10,
                'en_retard' => 2,
                'derniere_declaration' => '2024-01-15'
            ],
            'declarations_cnss' => [
                'total' => 12,
                'a_jour' => 12,
                'en_retard' => 0,
                'derniere_declaration' => '2024-01-10'
            ],
            'declarations_impot' => [
                'total' => 4,
                'a_jour' => 3,
                'en_retard' => 1,
                'derniere_declaration' => '2024-01-05'
            ],
            'obligations_fiscales' => [
                'tva' => ['statut' => 'A jour', 'prochaine_echeance' => '2024-02-15'],
                'cnss' => ['statut' => 'A jour', 'prochaine_echeance' => '2024-02-10'],
                'impot_revenu' => ['statut' => 'En retard', 'prochaine_echeance' => '2024-03-31'],
                'taxe_professionnelle' => ['statut' => 'A jour', 'prochaine_echeance' => '2024-12-31']
            ],
            'documents_fiscaux' => [
                'registre_achats' => ['statut' => 'A jour', 'derniere_maj' => '2024-01-20'],
                'registre_ventes' => ['statut' => 'A jour', 'derniere_maj' => '2024-01-20'],
                'livre_journal' => ['statut' => 'A jour', 'derniere_maj' => '2024-01-20'],
                'grand_livre' => ['statut' => 'A jour', 'derniere_maj' => '2024-01-20']
            ]
        ];

        return response()->json($stats);
    }

    public function generateDeclaration(Request $request)
    {
        $type = $request->input('type');
        $periode = $request->input('periode');

        // Logique pour générer la déclaration
        return response()->json([
            'success' => true,
            'message' => "Déclaration $type générée avec succès pour la période $periode",
            'data' => [
                'type' => $type,
                'periode' => $periode,
                'numero' => 'DECL-' . strtoupper($type) . '-' . date('YmdHis'),
                'date_generation' => now()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function exportDeclaration(Request $request)
    {
        $type = $request->input('type');
        $periode = $request->input('periode');

        // Logique pour exporter la déclaration
        return response()->json([
            'success' => true,
            'message' => "Déclaration $type exportée avec succès",
            'file_url' => "/exports/declaration-$type-$periode.pdf"
        ]);
    }
}
