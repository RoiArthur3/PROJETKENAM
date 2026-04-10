<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur principal du Cost Control
 * Gère la navigation et le dashboard central
 */
class CostControlDashboardController extends Controller
{
    /**
     * Dashboard principal - Choix des modules
     */
    public function home()
    {
        return view('materiel.cost-control.home');
    }

    /**
     * Dashboard Engin Standard
     */
    public function engineDashboard(Request $request)
    {
        return app(VehicleCostControlController::class)->index($request);
    }

    /**
     * Dashboard Camion Plateau
     */
    public function plateauDashboard(Request $request)
    {
        $request->merge(['submodule' => 'camion_plateau']);
        return app(VehicleCostControlController::class)->index($request);
    }
}
