<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImpotSocietesController extends Controller
{
    /**
     * Taux IS en Côte d'Ivoire (CGI Art. 63)
     */
    const TAUX_IS = 25;

    public function index()
    {
        $annee = request('annee', now()->year);
        $benefice = $this->calculerBeneficeImposable($annee);
        $is = $this->calculerIS($benefice);
        $acomptes = $this->calculerAcomptesTrimestriels($is);

        return view('comptabilite.impot-societes', compact('benefice', 'is', 'acomptes', 'annee'));
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'annee'                  => 'required|integer|min:2000|max:2100',
            'benefice_brut'          => 'required|numeric|min:0',
            'charges_non_deductibles'=> 'nullable|numeric|min:0',
            'credits_is'             => 'nullable|numeric|min:0',
        ]);

        $benefice_imposable = $data['benefice_brut'] + ($data['charges_non_deductibles'] ?? 0);
        $is_brut = $benefice_imposable * (self::TAUX_IS / 100);
        $credits = $data['credits_is'] ?? 0;
        $is_net = max(0, $is_brut - $credits);
        $acomptes = $this->calculerAcomptesTrimestriels($is_net);

        return response()->json([
            'annee'                  => $data['annee'],
            'benefice_brut'          => $data['benefice_brut'],
            'charges_non_deductibles'=> $data['charges_non_deductibles'] ?? 0,
            'benefice_imposable'     => $benefice_imposable,
            'taux_is'                => self::TAUX_IS,
            'is_brut'                => $is_brut,
            'credits_is'             => $credits,
            'is_net'                 => $is_net,
            'acomptes'               => $acomptes,
        ]);
    }

    /**
     * Calcul automatique du bénéfice depuis la BDD
     */
    private function calculerBeneficeImposable(int $annee): float
    {
        $debut = Carbon::create($annee, 1, 1)->startOfDay();
        $fin   = Carbon::create($annee, 12, 31)->endOfDay();

        $produits = 0;
        $charges  = 0;

        if (Schema::hasTable('factures')) {
            $produits += (float) DB::table('factures')
                ->whereBetween('date_facture', [$debut, $fin])
                ->where('statut', 'payee')
                ->sum('montant_ht');
        }

        if (Schema::hasTable('recettes')) {
            $produits += (float) DB::table('recettes')
                ->whereBetween('date_recette', [$debut, $fin])
                ->sum('montant');
        }

        if (Schema::hasTable('depenses')) {
            $charges += (float) DB::table('depenses')
                ->whereBetween('date_depense', [$debut, $fin])
                ->sum('montant');
        }

        return max(0, $produits - $charges);
    }

    private function calculerIS(float $benefice): float
    {
        return $benefice * (self::TAUX_IS / 100);
    }

    /**
     * Acomptes trimestriels = IS de l'exercice précédent / 4 (CGI Art. 79)
     * Versés en : mars (1er), juin (2e), septembre (3e), décembre (solde)
     */
    private function calculerAcomptesTrimestriels(float $is_annuel): array
    {
        $acompte = $is_annuel / 4;

        return [
            ['trimestre' => '1er acompte (31 mars)',      'montant' => $acompte, 'taux' => '25%'],
            ['trimestre' => '2ème acompte (30 juin)',     'montant' => $acompte, 'taux' => '25%'],
            ['trimestre' => '3ème acompte (30 septembre)','montant' => $acompte, 'taux' => '25%'],
            ['trimestre' => 'Solde (31 décembre)',         'montant' => $acompte, 'taux' => '25%'],
        ];
    }
}
