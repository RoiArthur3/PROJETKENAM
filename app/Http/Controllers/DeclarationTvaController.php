<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeclarationTvaController extends Controller
{
    public function index()
    {
        return view('comptabilite.declaration-tva');
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|string', // Format: YYYY-MM
            'tva_collectee_ventes' => 'required|numeric|min:0',
            'tva_collectee_prestations' => 'required|numeric|min:0',
            'tva_deductible_achats' => 'required|numeric|min:0',
            'tva_deductible_charges' => 'required|numeric|min:0',
            'tva_deductible_investissements' => 'required|numeric|min:0',
            'credit_tva_anterieur' => 'nullable|numeric|min:0',
        ]);

        // Calcul TVA collectée totale
        $tva_collectee_totale = $data['tva_collectee_ventes'] 
                                + $data['tva_collectee_prestations'];

        // Calcul TVA déductible totale
        $tva_deductible_totale = $data['tva_deductible_achats'] 
                                + $data['tva_deductible_charges'] 
                                + $data['tva_deductible_investissements'];

        // Calcul TVA à reverser (formule DGI)
        $tva_a_reverser = $tva_collectee_totale 
                          - $tva_deductible_totale 
                          - ($data['credit_tva_anterieur'] ?? 0);

        // Si crédit de TVA (négatif), il sera reporté
        $credit_tva_reporter = $tva_a_reverser < 0 ? abs($tva_a_reverser) : 0;
        $tva_a_payer = $tva_a_reverser > 0 ? $tva_a_reverser : 0;

        return response()->json([
            'periode' => $data['periode'],
            'tva_collectee' => [
                'ventes' => $data['tva_collectee_ventes'],
                'prestations' => $data['tva_collectee_prestations'],
                'total' => $tva_collectee_totale
            ],
            'tva_deductible' => [
                'achats' => $data['tva_deductible_achats'],
                'charges' => $data['tva_deductible_charges'],
                'investissements' => $data['tva_deductible_investissements'],
                'total' => $tva_deductible_totale
            ],
            'credit_tva_anterieur' => $data['credit_tva_anterieur'] ?? 0,
            'tva_a_reverser' => $tva_a_reverser,
            'credit_tva_reporter' => $credit_tva_reporter,
            'tva_a_payer' => $tva_a_payer,
            'date_limite_paiement' => $this->getDateLimitePaiement($data['periode']),
            'formulaire_dgi' => [
                'numero' => '301-C',
                'reference' => 'TVA ' . $data['periode']
            ]
        ]);
    }

    public function sauvegarderDeclaration(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|string|unique:declaration_tva,periode',
            'tva_collectee_totale' => 'required|numeric|min:0',
            'tva_deductible_totale' => 'required|numeric|min:0',
            'tva_a_reverser' => 'required|numeric',
            'credit_tva_reporter' => 'required|numeric|min:0',
            'date_declaration' => 'required|date',
            'statut' => 'required|string|in:brouillon,déposé,validé,rejeté',
        ]);

        $declaration = DB::table('declaration_tva')->insert([
            'periode' => $data['periode'],
            'tva_collectee_totale' => $data['tva_collectee_totale'],
            'tva_deductible_totale' => $data['tva_deductible_totale'],
            'tva_a_reverser' => $data['tva_a_reverser'],
            'credit_tva_reporter' => $data['credit_tva_reporter'],
            'date_declaration' => $data['date_declaration'],
            'statut' => $data['statut'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Déclaration TVA sauvegardée avec succès');
    }

    public function historique()
    {
        $declarations = DB::table('declaration_tva')
            ->orderBy('periode', 'desc')
            ->get();

        return view('comptabilite.declaration-tva-historique', compact('declarations'));
    }

    private function getDateLimitePaiement($periode)
    {
        // Format: YYYY-MM
        $annee = substr($periode, 0, 4);
        $mois = substr($periode, 5, 2);
        
        // Date limite: 15 du mois suivant
        return '15/' . ($mois + 1) . '/' . $annee;
    }
}
