<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TvaDeclarativeController extends Controller
{
    /**
     * Taux TVA standard Côte d'Ivoire (CGI Art. 339)
     */
    const TAUX_TVA = 18;

    public function index()
    {
        $periode   = request('periode', now()->format('Y-m'));
        [$annee, $mois] = explode('-', $periode);
        $debut = Carbon::create($annee, $mois, 1)->startOfDay();
        $fin   = Carbon::create($annee, $mois, 1)->endOfMonth();

        $tva_collectee  = $this->calculerTvaCollectee($debut, $fin);
        $tva_deductible = $this->calculerTvaDeductible($debut, $fin);
        $solde_a_reverser = max(0, $tva_collectee - $tva_deductible);
        $credit_tva = max(0, $tva_deductible - $tva_collectee);

        return view('comptabilite.tva-declarative', compact(
            'periode', 'debut', 'fin',
            'tva_collectee', 'tva_deductible',
            'solde_a_reverser', 'credit_tva'
        ));
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'periode'        => 'required|string|regex:/^\d{4}-\d{2}$/',
            'tva_collectee'  => 'nullable|numeric|min:0',
            'tva_deductible' => 'nullable|numeric|min:0',
        ]);

        [$annee, $mois] = explode('-', $data['periode']);
        $debut = Carbon::create($annee, $mois, 1)->startOfDay();
        $fin   = Carbon::create($annee, $mois, 1)->endOfMonth();

        // Priorité : valeurs saisies manuellement, sinon calcul automatique
        $tva_collectee  = isset($data['tva_collectee'])  ? (float) $data['tva_collectee']
                                                         : $this->calculerTvaCollectee($debut, $fin);
        $tva_deductible = isset($data['tva_deductible']) ? (float) $data['tva_deductible']
                                                         : $this->calculerTvaDeductible($debut, $fin);

        $solde = $tva_collectee - $tva_deductible;

        return response()->json([
            'periode'          => $data['periode'],
            'taux_tva'         => self::TAUX_TVA,
            'tva_collectee'    => $tva_collectee,
            'tva_deductible'   => $tva_deductible,
            'solde'            => $solde,
            'a_reverser_dgi'   => max(0, $solde),
            'credit_reporte'   => max(0, -$solde),
            'echeance_paiement'=> $fin->copy()->addDays(15)->format('d/m/Y'),
        ]);
    }

    /**
     * TVA collectée = TVA sur les factures émises (ventes)
     */
    private function calculerTvaCollectee(Carbon $debut, Carbon $fin): float
    {
        $tva = 0.0;

        if (Schema::hasTable('factures')) {
            $tva += (float) DB::table('factures')
                ->whereBetween('date_facture', [$debut, $fin])
                ->sum('tva');
        }

        if (Schema::hasTable('recettes')) {
            // Si la table recettes stocke le montant TTC, on recalcule la TVA
            $ttc = (float) DB::table('recettes')
                ->whereBetween('date_recette', [$debut, $fin])
                ->whereNull('facture_id') // éviter double compte
                ->sum('montant');
            $tva += $ttc * (self::TAUX_TVA / (100 + self::TAUX_TVA)); // TVA incluse
        }

        return round($tva, 2);
    }

    /**
     * TVA déductible = TVA sur les achats / dépenses professionnelles
     */
    private function calculerTvaDeductible(Carbon $debut, Carbon $fin): float
    {
        $tva = 0.0;

        if (Schema::hasTable('depenses')) {
            // Hypothèse : les dépenses professionnelles sont soumises à TVA à 18%
            $montant_ht = (float) DB::table('depenses')
                ->whereBetween('date_depense', [$debut, $fin])
                ->where('tva_deductible', true)
                ->sum('montant');
            $tva += $montant_ht * (self::TAUX_TVA / 100);
        }

        return round($tva, 2);
    }
}
