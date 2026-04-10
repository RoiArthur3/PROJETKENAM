<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImpotSocieteController extends Controller
{
    public function index()
    {
        return view('comptabilite.impot-societe');
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'exercice_fiscal' => 'required|integer|min:2020|max:2030',
            'benefice_comptable' => 'required|numeric|min:0',
            'deductions_fiscales' => 'nullable|numeric|min:0',
            'report_deficit_anterieur' => 'nullable|numeric|min:0',
            'acomptes_verses_T1' => 'nullable|numeric|min:0',
            'acomptes_verses_T2' => 'nullable|numeric|min:0',
            'acomptes_verses_T3' => 'nullable|numeric|min:0',
        ]);

        // Calcul du bénéfice fiscal selon CGI Art. 63 (DGI Côte d'Ivoire)
        $benefice_fiscal = $data['benefice_comptable'] 
                        - ($data['deductions_fiscales'] ?? 0)
                        - ($data['report_deficit_anterieur'] ?? 0);

        // Taux d'IS selon taille de l'entreprise (25% standard)
        $taux_is = 25; // 25% du bénéfice fiscal (CGI Art. 63)

        // Calcul de l'IS dû
        $is_du = $benefice_fiscal * ($taux_is / 100);

        // Calcul des acomptes trimestriels obligatoires
        $acompte_T1 = $is_du * 0.25; // 25% de l'IS annuel
        $acompte_T2 = $is_du * 0.25;
        $acompte_T3 = $is_du * 0.25;
        $acompte_T4 = $is_du * 0.25; // Solde à payer

        // Total acomptes versés
        $total_acomptes_verses = ($data['acomptes_verses_T1'] ?? 0) 
                                + ($data['acomptes_verses_T2'] ?? 0) 
                                + ($data['acomptes_verses_T3'] ?? 0);

        // Solde d'IS à payer
        $solde_is = $is_du - $total_acomptes_verses;

        return response()->json([
            'exercice_fiscal' => $data['exercice_fiscal'],
            'benefice_comptable' => $data['benefice_comptable'],
            'deductions_fiscales' => $data['deductions_fiscales'] ?? 0,
            'report_deficit_anterieur' => $data['report_deficit_anterieur'] ?? 0,
            'benefice_fiscal' => $benefice_fiscal,
            'taux_is' => $taux_is,
            'is_du' => $is_du,
            'acomptes_trimestriels' => [
                'T1' => $acompte_T1,
                'T2' => $acompte_T2,
                'T3' => $acompte_T3,
                'T4' => $acompte_T4,
                'total' => $is_du
            ],
            'acomptes_verses' => [
                'T1' => $data['acomptes_verses_T1'] ?? 0,
                'T2' => $data['acomptes_verses_T2'] ?? 0,
                'T3' => $data['acomptes_verses_T3'] ?? 0,
                'total' => $total_acomptes_verses
            ],
            'solde_is' => $solde_is,
            'echeances' => [
                'T1' => '15/04/' . ($data['exercice_fiscal'] + 1),
                'T2' => '15/06/' . ($data['exercice_fiscal'] + 1),
                'T3' => '15/09/' . ($data['exercice_fiscal'] + 1),
                'T4' => '15/12/' . ($data['exercice_fiscal'] + 1),
            ]
        ]);
    }

    public function declarations()
    {
        $declarations = DB::table('impot_societe_declarations')
            ->orderBy('exercice_fiscal', 'desc')
            ->get();

        return view('comptabilite.impot-societe-declarations', compact('declarations'));
    }

    public function sauvegarderDeclaration(Request $request)
    {
        $data = $request->validate([
            'exercice_fiscal' => 'required|integer|unique:impot_societe_declarations,exercice_fiscal',
            'benefice_fiscal' => 'required|numeric|min:0',
            'is_du' => 'required|numeric|min:0',
            'acomptes_verses' => 'required|numeric|min:0',
            'solde_is' => 'required|numeric',
            'date_declaration' => 'required|date',
        ]);

        $declaration = DB::table('impot_societe_declarations')->insert([
            'exercice_fiscal' => $data['exercice_fiscal'],
            'benefice_fiscal' => $data['benefice_fiscal'],
            'is_du' => $data['is_du'],
            'acomptes_verses' => $data['acomptes_verses'],
            'solde_is' => $data['solde_is'],
            'date_declaration' => $data['date_declaration'],
            'statut' => 'déposé',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Déclaration IS sauvegardée avec succès');
    }
}
