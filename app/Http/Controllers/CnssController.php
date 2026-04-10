<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CnssController extends Controller
{
    public function index()
    {
        return view('comptabilite.cnss');
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|string',
            'salaries' => 'required|array|min:1',
            'salaries.*.nom' => 'required|string',
            'salaries.*.matricule' => 'required|string',
            'salaries.*.salaire_brut' => 'required|numeric|min:0',
            'salaries.*.categorie' => 'required|string',
        ]);

        // Taux CNSS (exemple pour illustration)
        $taux = [
            'employeur' => [
                'prestation_familiale' => 5.5,
                'accident_travail' => 2.0,
                'retraite' => 7.5,
                'total' => 15.0
            ],
            'employe' => [
                'retraite' => 5.5,
                'total' => 5.5
            ],
            'global' => 20.5 // Total employeur + employé
        ];

        $resultats = [];
        $total_salaire_brut = 0;
        $total_cotisation_employeur = 0;
        $total_cotisation_employe = 0;
        $total_cotisation_global = 0;

        foreach ($data['salaries'] as $salarie) {
            $salaire_brut = $salarie['salaire_brut'];

            // Plafond CNPS officiel Côte d'Ivoire (mis à jour régulièrement)
            $plafond = 1647315; // 1 647 315 FCFA/mois (plafond CNPS officiel)
            $base_calcul = min($salaire_brut, $plafond);

            // Calcul des cotisations
            $cotisations = [
                'employeur' => [
                    'prestation_familiale' => $base_calcul * ($taux['employeur']['prestation_familiale'] / 100),
                    'accident_travail' => $base_calcul * ($taux['employeur']['accident_travail'] / 100),
                    'retraite' => $base_calcul * ($taux['employeur']['retraite'] / 100),
                    'total' => $base_calcul * ($taux['employeur']['total'] / 100)
                ],
                'employe' => [
                    'retraite' => $base_calcul * ($taux['employe']['retraite'] / 100),
                    'total' => $base_calcul * ($taux['employe']['total'] / 100)
                ]
            ];

            $cotisation_global = $cotisations['employeur']['total'] + $cotisations['employe']['total'];

            $resultats[] = [
                'salarie' => $salarie,
                'base_calcul' => $base_calcul,
                'cotisations' => $cotisations,
                'cotisation_global' => $cotisation_global
            ];

            // Cumuls
            $total_salaire_brut += $salaire_brut;
            $total_cotisation_employeur += $cotisations['employeur']['total'];
            $total_cotisation_employe += $cotisations['employe']['total'];
            $total_cotisation_global += $cotisation_global;
        }

        return response()->json([
            'periode' => $data['periode'],
            'taux' => $taux,
            'resultats' => $resultats,
            'totaux' => [
                'salaire_brut' => $total_salaire_brut,
                'cotisation_employeur' => $total_cotisation_employeur,
                'cotisation_employe' => $total_cotisation_employe,
                'cotisation_global' => $total_cotisation_global
            ],
            'plafond' => $plafond
        ]);
    }

    public function exportPDF(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|string',
            'salaries' => 'required|array|min:1',
            'salaries.*.nom' => 'required|string',
            'salaries.*.matricule' => 'required|string',
            'salaries.*.salaire_brut' => 'required|numeric|min:0',
            'salaries.*.categorie' => 'required|string',
        ]);

        // Recalculer les données pour le PDF
        $taux = [
            'employeur' => [
                'prestation_familiale' => 5.5,
                'accident_travail' => 2.0,
                'retraite' => 7.5,
                'total' => 15.0
            ],
            'employe' => [
                'retraite' => 5.5,
                'total' => 5.5
            ],
            'global' => 20.5
        ];

        $resultats = [];
        $total_salaire_brut = 0;
        $total_cotisation_employeur = 0;
        $total_cotisation_employe = 0;
        $total_cotisation_global = 0;
        $plafond = 300000;

        foreach ($data['salaries'] as $salarie) {
            $salaire_brut = $salarie['salaire_brut'];
            $base_calcul = min($salaire_brut, $plafond);

            $cotisations = [
                'employeur' => [
                    'prestation_familiale' => $base_calcul * ($taux['employeur']['prestation_familiale'] / 100),
                    'accident_travail' => $base_calcul * ($taux['employeur']['accident_travail'] / 100),
                    'retraite' => $base_calcul * ($taux['employeur']['retraite'] / 100),
                    'total' => $base_calcul * ($taux['employeur']['total'] / 100)
                ],
                'employe' => [
                    'retraite' => $base_calcul * ($taux['employe']['retraite'] / 100),
                    'total' => $base_calcul * ($taux['employe']['total'] / 100)
                ]
            ];

            $cotisation_global = $cotisations['employeur']['total'] + $cotisations['employe']['total'];

            $resultats[] = [
                'salarie' => $salarie,
                'base_calcul' => $base_calcul,
                'cotisations' => $cotisations,
                'cotisation_global' => $cotisation_global
            ];

            $total_salaire_brut += $salaire_brut;
            $total_cotisation_employeur += $cotisations['employeur']['total'];
            $total_cotisation_employe += $cotisations['employe']['total'];
            $total_cotisation_global += $cotisation_global;
        }

        return view('comptabilite.cnss-pdf', [
            'periode' => $data['periode'],
            'taux' => $taux,
            'resultats' => $resultats,
            'totaux' => [
                'salaire_brut' => $total_salaire_brut,
                'cotisation_employeur' => $total_cotisation_employeur,
                'cotisation_employe' => $total_cotisation_employe,
                'cotisation_global' => $total_cotisation_global
            ],
            'plafond' => $plafond
        ]);
    }
}
