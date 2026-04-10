<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxeFormationController extends Controller
{
    public function index()
    {
        return view('comptabilite.taxe-formation');
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|string', // Format: YYYY-MM
            'masse_salariale_brut' => 'required|numeric|min:0',
            'exoneration_apprentissage' => 'nullable|numeric|min:0',
        ]);

        // Taux selon législation ivoirienne (FDFP)
        $taux_tfp = 1.2; // 1.2% pour la Taxe de Formation Professionnelle
        $taux_taxe_apprentissage = 0.4; // 0.4% pour la Taxe d'Apprentissage

        // Base de calcul pour TFP
        $base_tfp = $data['masse_salariale_brut'];
        
        // Base de calcul pour Taxe d'Apprentissage (avec exonérations possibles)
        $base_taxe_apprentissage = $data['masse_salariale_brut'] - ($data['exoneration_apprentissage'] ?? 0);

        // Calcul des taxes
        $tfp = $base_tfp * ($taux_tfp / 100);
        $taxe_apprentissage = $base_taxe_apprentissage * ($taux_taxe_apprentissage / 100);
        $total_taxes = $tfp + $taxe_apprentissage;

        return response()->json([
            'periode' => $data['periode'],
            'masse_salariale_brut' => $data['masse_salariale_brut'],
            'exoneration_apprentissage' => $data['exoneration_apprentissage'] ?? 0,
            'tfp' => [
                'taux' => $taux_tfp,
                'base_calcul' => $base_tfp,
                'montant' => $tfp,
                'reference' => 'FDFP - Loi 2015-532'
            ],
            'taxe_apprentissage' => [
                'taux' => $taux_taxe_apprentissage,
                'base_calcul' => $base_taxe_apprentissage,
                'montant' => $taxe_apprentissage,
                'reference' => 'Taxe d\'Apprentissage - Loi de finances'
            ],
            'total_taxes' => $total_taxes,
            'date_limite_paiement' => $this->getDateLimitePaiement($data['periode']),
        ]);
    }

    public function sauvegarderDeclaration(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|string|unique:taxe_formation_declarations,periode',
            'masse_salariale_brut' => 'required|numeric|min:0',
            'tfp' => 'required|numeric|min:0',
            'taxe_apprentissage' => 'required|numeric|min:0',
            'total_taxes' => 'required|numeric|min:0',
            'date_declaration' => 'required|date',
            'statut' => 'required|string|in:brouillon,déposé,validé,rejeté',
        ]);

        $declaration = DB::table('taxe_formation_declarations')->insert([
            'periode' => $data['periode'],
            'masse_salariale_brut' => $data['masse_salariale_brut'],
            'tfp' => $data['tfp'],
            'taxe_apprentissage' => $data['taxe_apprentissage'],
            'total_taxes' => $data['total_taxes'],
            'date_declaration' => $data['date_declaration'],
            'statut' => $data['statut'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Déclaration TFP sauvegardée avec succès');
    }

    public function historique()
    {
        $declarations = DB::table('taxe_formation_declarations')
            ->orderBy('periode', 'desc')
            ->get();

        return view('comptabilite.taxe-formation-historique', compact('declarations'));
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
