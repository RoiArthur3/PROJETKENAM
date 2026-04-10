<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetenueSourceController extends Controller
{
    public function index()
    {
        return view('comptabilite.retenue-source');
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'montant_ht' => 'required|numeric|min:0',
            'type_prestation' => 'required|string|in:service_local,service_non_resident,importation,royalties,dividendes',
            'beneficiaire_resident' => 'required|boolean',
            'assujetti_tva' => 'required|boolean',
        ]);

        // Taux de retenue à la source selon CGI Art. 165-180 (DGI Côte d'Ivoire)
        $taux_ras = $this->getTauxRAS($data['type_prestation'], $data['beneficiaire_resident']);
        
        // Calcul de la retenue à la source
        $retenue_source = $data['montant_ht'] * ($taux_ras / 100);
        
        // Calcul de la TVA si assujetti
        $tva = 0;
        if ($data['assujetti_tva']) {
            $tva = $data['montant_ht'] * 0.18; // TVA à 18%
        }
        
        $montant_ttc = $data['montant_ht'] + $tva;
        $montant_a_payer = $montant_ttc - $retenue_source;

        return response()->json([
            'montant_ht' => $data['montant_ht'],
            'type_prestation' => $data['type_prestation'],
            'beneficiaire_resident' => $data['beneficiaire_resident'],
            'taux_ras' => $taux_ras,
            'retenue_source' => $retenue_source,
            'assujetti_tva' => $data['assujetti_tva'],
            'tva' => $tva,
            'montant_ttc' => $montant_ttc,
            'montant_a_payer' => $montant_a_payer,
            'reference_cgi' => $this->getReferenceCGI($data['type_prestation']),
        ]);
    }

    public function sauvegarderDeclaration(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|string|unique:retenue_source_declarations,periode',
            'total_montant_ht' => 'required|numeric|min:0',
            'total_retenue_source' => 'required|numeric|min:0',
            'total_tva' => 'required|numeric|min:0',
            'total_montant_ttc' => 'required|numeric|min:0',
            'date_declaration' => 'required|date',
            'statut' => 'required|string|in:brouillon,déposé,validé,rejeté',
        ]);

        $declaration = DB::table('retenue_source_declarations')->insert([
            'periode' => $data['periode'],
            'total_montant_ht' => $data['total_montant_ht'],
            'total_retenue_source' => $data['total_retenue_source'],
            'total_tva' => $data['total_tva'],
            'total_montant_ttc' => $data['total_montant_ttc'],
            'date_declaration' => $data['date_declaration'],
            'statut' => $data['statut'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Déclaration RAS sauvegardée avec succès');
    }

    public function historique()
    {
        $declarations = DB::table('retenue_source_declarations')
            ->orderBy('periode', 'desc')
            ->get();

        return view('comptabilite.retenue-source-historique', compact('declarations'));
    }

    private function getTauxRAS($type_prestation, $beneficiaire_resident)
    {
        // Taux selon CGI Art. 165-180 (DGI Côte d'Ivoire)
        $taux = [
            'service_local' => $beneficiaire_resident ? 5.0 : 10.0,
            'service_non_resident' => 15.0,
            'importation' => 5.0,
            'royalties' => 10.0,
            'dividendes' => 10.0,
        ];

        return $taux[$type_prestation] ?? 10.0; // Taux par défaut
    }

    private function getReferenceCGI($type_prestation)
    {
        $references = [
            'service_local' => 'CGI Art. 165',
            'service_non_resident' => 'CGI Art. 166',
            'importation' => 'CGI Art. 167',
            'royalties' => 'CGI Art. 168',
            'dividendes' => 'CGI Art. 169',
        ];

        return $references[$type_prestation] ?? 'CGI Art. 165-180';
    }
}
