<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RapportsController extends Controller
{
    public function index()
    {
        return view('comptabilite.rapports');
    }

    public function chiffreAffaires()
    {
        return view('comptabilite.rapports.chiffre-affaires');
    }

    public function depenses()
    {
        return view('comptabilite.rapports.depenses');
    }

    public function benefices()
    {
        return view('comptabilite.rapports.benefices');
    }

    public function tresorerie()
    {
        return view('comptabilite.rapports.tresorerie');
    }

    public function getStatistics()
    {
        // Statistiques des rapports (données d'exemple)
        $stats = [
            'rapports_generes' => 45,
            'rapports_mois' => 12,
            'rapports_en_attente' => 3,
            'dernier_rapport' => '2024-01-15',
            'types_rapports' => [
                'chiffre_affaires' => 15,
                'depenses' => 12,
                'benefices' => 10,
                'tresorerie' => 8
            ],
            'periodes' => [
                'jour' => 5,
                'semaine' => 8,
                'mois' => 20,
                'trimestre' => 7,
                'annee' => 5
            ]
        ];

        return response()->json($stats);
    }

    public function generateRapport(Request $request)
    {
        $type = $request->input('type');
        $periode = $request->input('periode');
        $format = $request->input('format', 'pdf');

        // Logique de génération de rapport
        return response()->json([
            'success' => true,
            'message' => "Rapport $type généré avec succès pour la période $periode",
            'data' => [
                'type' => $type,
                'periode' => $periode,
                'format' => $format,
                'numero' => 'RPT-' . strtoupper($type) . '-' . date('YmdHis'),
                'date_generation' => now()->format('Y-m-d H:i:s'),
                'file_url' => "/exports/rapports/rapport-$type-$periode.$format"
            ]
        ]);
    }

    public function getRapportData(Request $request)
    {
        $type = $request->input('type');
        $periode = $request->input('periode');

        // Données selon le type de rapport
        $data = [];

        switch($type) {
            case 'chiffre_affaires':
                $data = [
                    'total_ca' => 2500000,
                    'evolution' => 15.5,
                    'par_mois' => [
                        '2023-09' => 1800000,
                        '2023-10' => 2100000,
                        '2023-11' => 2300000,
                        '2023-12' => 2500000,
                        '2024-01' => 2200000
                    ],
                    'par_client' => [
                        'Client A' => 800000,
                        'Client B' => 600000,
                        'Client C' => 450000,
                        'Client D' => 350000,
                        'Autres' => 300000
                    ]
                ];
                break;

            case 'depenses':
                $data = [
                    'total_depenses' => 1800000,
                    'evolution' => 8.2,
                    'par_categorie' => [
                        'Personnel' => 800000,
                        'Loyer' => 300000,
                        'Fournitures' => 250000,
                        'Services' => 200000,
                        'Autres' => 250000
                    ],
                    'par_mois' => [
                        '2023-09' => 1500000,
                        '2023-10' => 1600000,
                        '2023-11' => 1700000,
                        '2023-12' => 1800000,
                        '2024-01' => 1650000
                    ]
                ];
                break;

            case 'benefices':
                $data = [
                    'total_benefices' => 700000,
                    'marge_nette' => 28.0,
                    'par_mois' => [
                        '2023-09' => 300000,
                        '2023-10' => 500000,
                        '2023-11' => 600000,
                        '2023-12' => 700000,
                        '2024-01' => 550000
                    ],
                    'ratio_ca_depenses' => [
                        'ca' => 2500000,
                        'depenses' => 1800000,
                        'benefice' => 700000
                    ]
                ];
                break;

            case 'tresorerie':
                $data = [
                    'solde_actuel' => 1200000,
                    'entrees' => 2800000,
                    'sorties' => 1600000,
                    'flux_mensuel' => [
                        '2023-09' => 800000,
                        '2023-10' => 950000,
                        '2023-11' => 1100000,
                        '2023-12' => 1200000,
                        '2024-01' => 1050000
                    ],
                    'previsions' => [
                        'fevrier' => 1300000,
                        'mars' => 1400000,
                        'avril' => 1350000
                    ]
                ];
                break;
        }

        return response()->json($data);
    }

    public function exportRapport(Request $request)
    {
        $type = $request->input('type');
        $periode = $request->input('periode');
        $format = $request->input('format', 'pdf');

        // Logique d'export
        return response()->json([
            'success' => true,
            'message' => "Rapport $type exporté en $format avec succès",
            'file_url' => "/exports/rapports/rapport-$type-$periode.$format"
        ]);
    }

    public function planifierRapport(Request $request)
    {
        $data = $request->all();

        // Logique de planification
        return response()->json([
            'success' => true,
            'message' => 'Rapport planifié avec succès',
            'data' => $data
        ]);
    }
}
