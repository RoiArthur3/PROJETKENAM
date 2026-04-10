<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TfpController extends Controller
{
    /**
     * Taux FDFP Côte d'Ivoire
     * TFP (Taxe de Formation Professionnelle Continue) : 1,2% masse salariale
     * Taxe d'Apprentissage                             : 0,4% masse salariale
     */
    const TAUX_TFP          = 1.2;
    const TAUX_APPRENTISSAGE = 0.4;

    public function index()
    {
        $periode   = request('periode', now()->format('Y-m'));
        [$annee, $mois] = explode('-', $periode);
        $debut = Carbon::create($annee, $mois, 1)->startOfDay();
        $fin   = Carbon::create($annee, $mois, 1)->endOfMonth();

        $masse_salariale    = $this->getMasseSalariale($debut, $fin);
        $tfp                = $masse_salariale * (self::TAUX_TFP / 100);
        $taxe_apprentissage = $masse_salariale * (self::TAUX_APPRENTISSAGE / 100);
        $total              = $tfp + $taxe_apprentissage;

        return view('comptabilite.tfp', compact(
            'periode', 'debut', 'fin',
            'masse_salariale', 'tfp', 'taxe_apprentissage', 'total'
        ));
    }

    public function calculer(Request $request)
    {
        $data = $request->validate([
            'periode'         => 'required|string|regex:/^\d{4}-\d{2}$/',
            'masse_salariale' => 'nullable|numeric|min:0',
        ]);

        [$annee, $mois] = explode('-', $data['periode']);
        $debut = Carbon::create($annee, $mois, 1)->startOfDay();
        $fin   = Carbon::create($annee, $mois, 1)->endOfMonth();

        $masse_salariale = isset($data['masse_salariale'])
            ? (float) $data['masse_salariale']
            : $this->getMasseSalariale($debut, $fin);

        $tfp                = round($masse_salariale * (self::TAUX_TFP / 100), 0);
        $taxe_apprentissage = round($masse_salariale * (self::TAUX_APPRENTISSAGE / 100), 0);
        $total              = $tfp + $taxe_apprentissage;

        return response()->json([
            'periode'              => $data['periode'],
            'masse_salariale'      => $masse_salariale,
            'taux_tfp'             => self::TAUX_TFP,
            'taux_apprentissage'   => self::TAUX_APPRENTISSAGE,
            'tfp'                  => $tfp,
            'taxe_apprentissage'   => $taxe_apprentissage,
            'total'                => $total,
            'echeance'             => $fin->copy()->addDays(15)->format('d/m/Y'),
        ]);
    }

    /**
     * Calcul automatique de la masse salariale brute depuis les bulletins de paie
     */
    private function getMasseSalariale(Carbon $debut, Carbon $fin): float
    {
        $masse = 0.0;

        // Depuis les bulletins de paie si la table existe
        if (Schema::hasTable('personnel_paies')) {
            $masse = (float) DB::table('personnel_paies')
                ->whereBetween('date_paie', [$debut, $fin])
                ->sum('salaire_brut');
        } elseif (Schema::hasTable('personnels')) {
            // Fallback : somme des salaires de base du mois
            $masse = (float) DB::table('personnels')
                ->where('statut', 'actif')
                ->sum('salaire_base');
        }

        return $masse;
    }
}
