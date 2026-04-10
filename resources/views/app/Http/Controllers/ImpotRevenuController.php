<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImpotRevenuController extends Controller
{
    public function index()
    {
        return view('comptabilite.impot-revenu');
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'revenus_salaire' => 'required|numeric|min:0',
            'revenus_autres' => 'nullable|numeric|min:0',
            'charges_familiales' => 'nullable|numeric|min:0',
            'charges_professionnelles' => 'nullable|numeric|min:0',
            'nombre_parts' => 'required|integer|min:1',
        ]);

        // Calcul du revenu imposable
        $revenu_total = $data['revenus_salaire'] + ($data['revenus_autres'] ?? 0);
        $total_charges = ($data['charges_familiales'] ?? 0) + ($data['charges_professionnelles'] ?? 0);
        $revenu_imposable = $revenu_total - $total_charges;

        // Barème progressif de l'impôt (exemples pour illustration)
        $bareme = [
            ['min' => 0, 'max' => 2500000, 'taux' => 0],
            ['min' => 2500001, 'max' => 5000000, 'taux' => 11],
            ['min' => 5000001, 'max' => 10000000, 'taux' => 30],
            ['min' => 10000001, 'max' => 15000000, 'taux' => 35],
            ['min' => 15000001, 'max' => PHP_INT_MAX, 'taux' => 45],
        ];

        $impot_brut = $this->calculerImpotProgressif($revenu_imposable, $bareme);
        $impot_net = $impot_brut / $data['nombre_parts'];

        return response()->json([
            'revenu_total' => $revenu_total,
            'total_charges' => $total_charges,
            'revenu_imposable' => $revenu_imposable,
            'impot_brut' => $impot_brut,
            'impot_net' => $impot_net,
            'nombre_parts' => $data['nombre_parts'],
        ]);
    }

    private function calculerImpotProgressif($revenu, $bareme)
    {
        $impot = 0;
        $tranche_precedente = 0;

        foreach ($bareme as $tranche) {
            if ($revenu <= $tranche['min']) {
                break;
            }

            $base_calcul = min($revenu, $tranche['max']) - $tranche['min'];
            if ($base_calcul > 0) {
                $impot += $base_calcul * ($tranche['taux'] / 100);
            }
        }

        return $impot;
    }

    public function exportPDF(Request $request)
    {
        $data = $request->validate([
            'revenus_salaire' => 'required|numeric|min:0',
            'revenus_autres' => 'nullable|numeric|min:0',
            'charges_familiales' => 'nullable|numeric|min:0',
            'charges_professionnelles' => 'nullable|numeric|min:0',
            'nombre_parts' => 'required|integer|min:1',
        ]);

        // Calcul des données pour le PDF
        $revenu_total = $data['revenus_salaire'] + ($data['revenus_autres'] ?? 0);
        $total_charges = ($data['charges_familiales'] ?? 0) + ($data['charges_professionnelles'] ?? 0);
        $revenu_imposable = $revenu_total - $total_charges;

        $bareme = [
            ['min' => 0, 'max' => 2500000, 'taux' => 0],
            ['min' => 2500001, 'max' => 5000000, 'taux' => 11],
            ['min' => 5000001, 'max' => 10000000, 'taux' => 30],
            ['min' => 10000001, 'max' => 15000000, 'taux' => 35],
            ['min' => 15000001, 'max' => PHP_INT_MAX, 'taux' => 45],
        ];

        $impot_brut = $this->calculerImpotProgressif($revenu_imposable, $bareme);
        $impot_net = $impot_brut / $data['nombre_parts'];

        // Retourner la vue avec les données pour le PDF
        return view('comptabilite.impot-revenu-pdf', [
            'data' => $data,
            'revenu_total' => $revenu_total,
            'total_charges' => $total_charges,
            'revenu_imposable' => $revenu_imposable,
            'impot_brut' => $impot_brut,
            'impot_net' => $impot_net,
        ]);
    }
}
