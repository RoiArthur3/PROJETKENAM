<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyscohadaController extends Controller
{
    public function index()
    {
        $comptes = [];
        $classes = [];

        if (Schema::hasTable('syscohada_plan_comptable')) {
            $comptes = DB::table('syscohada_plan_comptable')
                ->where('est_actif', true)
                ->orderBy('numero_compte')
                ->get();

            $classes = DB::table('syscohada_plan_comptable')
                ->where('est_actif', true)
                ->select('classe')
                ->distinct()
                ->orderBy('classe')
                ->pluck('classe');
        }

        $descriptions_classes = [
            '1' => 'Comptes de capitaux, emprunts et dettes assimilées',
            '2' => 'Comptes d\'immobilisations',
            '3' => 'Comptes de stocks',
            '4' => 'Comptes de tiers',
            '5' => 'Comptes de trésorerie',
            '6' => 'Comptes de charges des activités ordinaires',
            '7' => 'Comptes de produits des activités ordinaires',
            '8' => 'Comptes des autres charges et des autres produits',
        ];

        return view('comptabilite.syscohada', compact('comptes', 'classes', 'descriptions_classes'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');

        if (!Schema::hasTable('syscohada_plan_comptable')) {
            return response()->json([]);
        }

        $comptes = DB::table('syscohada_plan_comptable')
            ->where('est_actif', true)
            ->where(function ($query) use ($q) {
                $query->where('numero_compte', 'like', "%{$q}%")
                      ->orWhere('nom_compte', 'like', "%{$q}%");
            })
            ->orderBy('numero_compte')
            ->limit(50)
            ->get();

        return response()->json($comptes);
    }
}
