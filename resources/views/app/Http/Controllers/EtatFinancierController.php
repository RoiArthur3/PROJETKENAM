<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EcritureComptable;

class EtatFinancierController extends Controller
{
    public function bilan(Request $request)
    {
        try {
            $periode = $request->input('periode');

            $actif = EcritureComptable::where('type', 'actif')
                        ->wherePeriode($periode)
                        ->get();

            $passif = EcritureComptable::where('type', 'passif')
                        ->wherePeriode($periode)
                        ->get();

            if($actif->isEmpty() && $passif->isEmpty()) {
                return response()->json(['error' => 'Aucune donnée disponible'], 404);
            }

            return view('comptabilite.partials.bilan', compact('actif', 'passif'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function compteResultat(Request $request)
    {
        try {
            $periode = $request->input('periode');

            $produits = EcritureComptable::where('type', 'produit')
                            ->wherePeriode($periode)
                            ->get();

            $charges = EcritureComptable::where('type', 'charge')
                            ->wherePeriode($periode)
                            ->get();

            if($produits->isEmpty() && $charges->isEmpty()) {
                return response()->json(['error' => 'Aucune donnée disponible'], 404);
            }

            return view('comptabilite.partials.compte-resultat',
                   compact('produits', 'charges'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generate(Request $request)
    {
        $type = $request->input('type', 'bilan');
        $periode = $request->input('periode', 'mois');

        if ($type === 'bilan') {
            return $this->bilan($request);
        } else {
            return $this->compteResultat($request);
        }
    }
}
