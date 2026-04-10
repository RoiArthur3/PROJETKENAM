<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicule;
use Carbon\Carbon;

class ParcController extends Controller
{
    public function dashboard()
    {
        // Vérifier les permissions
        $user = auth()->user();
        if (!$user || !$user->canAccessModule('parc')) {
            abort(403);
        }

        // KPIs
        $total = Vehicule::count();
        $disponibles = Vehicule::where('disponible', 1)->count();
        // We calculate immobilized as total - available for now
        $immobilises = $total - $disponibles;

        $tauxUtilisation = $total > 0 ? round(($disponibles / $total) * 100) : 0;

        $kpis = [
            'total' => $total,
            'disponibles' => $disponibles,
            'maintenance' => 0, // Placeholder
            'immobilises' => $immobilises,
            'tauxUtilisation' => $tauxUtilisation
        ];

        // Chart Data (Mocking for now to match view expectations)
        $months = ['Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
        $fuelPerMonth = [0, 0, 0, 0, 0, 0];
        $maintPerMonth = [0, 0, 0, 0, 0, 0];
        $pieLabels = ['Disponibles', 'Indisponibles'];
        $pieData = [$disponibles, $immobilises];

        return view('parc.dashboard', compact(
            'kpis',
            'months',
            'fuelPerMonth',
            'maintPerMonth',
            'pieLabels',
            'pieData'
        ));
    }
}
