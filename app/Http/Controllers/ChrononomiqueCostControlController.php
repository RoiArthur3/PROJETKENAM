<?php

namespace App\Http\Controllers;

use App\Models\VehiclePointage;
use App\Models\Vehicule;
use App\Models\Personnel;
use App\Models\VehicleMission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur pour le pointage chronométrique (Clic Début / Clic Fin)
 * Permet de chronométrer le temps de travail et calculer automatiquement les heures
 */
class ChrononomiqueCostControlController extends Controller
{
    /**
     * Afficher le dashboard du pointage chrono
     */
    public function dashboard(Request $request)
    {
        try {
            $today = now()->toDateString();
            $submodule = $request->input('submodule', 'camion_plateau');

            // Pointages du jour
            $todayPointages = VehiclePointage::where('submodule', $submodule)
                ->whereDate('date_pointage', $today)
                ->with(['vehicle', 'driver', 'mission'])
                ->orderByDesc('date_pointage')
                ->get();

            // Résumé du jour
            $summary = [
                'today_pointages' => $todayPointages->count(),
                'today_hours' => (float) $todayPointages
                    ->where('unit_type', 'heure')
                    ->sum('quantity'),
                'today_cost' => (float) $todayPointages->sum('total_supplier_cost'),
                'today_amount' => (float) $todayPointages->sum('total_client_amount'),
                'today_margin' => (float) ($todayPointages->sum('total_client_amount') - $todayPointages->sum('total_supplier_cost')),
                'active_pointages' => $todayPointages->filter(fn($p) => is_null($p->heure_arrivee))->count(),
            ];

            // Véhicules et chauffeurs disponibles
            $vehicles = Vehicule::orderBy('immatriculation')->get();
            $drivers = Personnel::orderBy('nom')->get();

            // Pointages en cours (sans heure d'arrivée)
            $activePointages = VehiclePointage::where('submodule', $submodule)
                ->whereNull('heure_arrivee')
                ->with(['vehicle', 'driver', 'mission'])
                ->get();

            return view('materiel.cost-control.chronometrique.dashboard', compact(
                'todayPointages',
                'summary',
                'vehicles',
                'drivers',
                'activePointages',
                'submodule',
                'today'
            ));
        } catch (\Throwable $e) {
            Log::error('Chronometrique dashboard failed', ['error' => $e->getMessage()]);
            return redirect()->route('materiel.cost-control.list')
                ->with('error', 'Impossible de charger le dashboard chrono.');
        }
    }

    /**
     * Afficher le formulaire pour démarrer un pointage
     */
    public function startForm()
    {
        try {
            $vehicles = Vehicule::orderBy('immatriculation')->get();
            $drivers = Personnel::orderBy('nom')->orderBy('prenoms')->get();

            return view('materiel.cost-control.chronometrique.start-form', compact(
                'vehicles',
                'drivers'
            ));
        } catch (\Throwable $e) {
            Log::error('Start form failed', ['error' => $e->getMessage()]);
            return redirect()->route('materiel.cost-control.list')
                ->with('error', 'Impossible de charger le formulaire.');
        }
    }

    /**
     * Démarrer un pointage (Clic Début)
     */
    public function start(Request $request)
    {
        try {
            $data = $request->validate([
                'vehicle_id' => 'required|exists:vehicules,id',
                'driver_id' => 'required|exists:personnels,id',
                'vehicle_mission_id' => 'nullable|exists:vehicle_missions,id',
                'task_label' => 'required|string|max:255',
                'departure_location' => 'nullable|string|max:255',
                'supplier_unit_cost' => 'required|numeric|min:0',
                'client_unit_price' => 'required|numeric|min:0',
                'submodule' => 'required|in:camion_plateau,engin',
            ]);

            $now = now();

            $pointage = VehiclePointage::create([
                'date_pointage' => $now->toDateString(),
                'heure_pointage' => $now->toTimeString(),
                'heure_depart' => $now->toTimeString(),
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'],
                'vehicle_mission_id' => $data['vehicle_mission_id'],
                'task_label' => $data['task_label'],
                'departure_location' => $data['departure_location'],
                'unit_type' => 'heure',
                'quantity' => 0, // Sera calculé au clic fin
                'supplier_unit_cost' => $data['supplier_unit_cost'],
                'client_unit_price' => $data['client_unit_price'],
                'total_supplier_cost' => 0,
                'total_client_amount' => 0,
                'submodule' => $data['submodule'],
                'billing_mode' => 'chrono',
                'statut' => 'en-cours',
                'user_id' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'pointage_id' => $pointage->id,
                'message' => 'Pointage démarré',
                'start_time' => $now->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Pointage start failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur au démarrage'], 500);
        }
    }

    /**
     * Arrêter un pointage (Clic Fin) - Calcule les heures automatiquement
     */
    public function stop(Request $request, VehiclePointage $pointage)
    {
        try {
            $data = $request->validate([
                'arrival_location' => 'nullable|string|max:255',
            ]);

            $now = now();

            // Vérifier que le pointage est en cours
            if (!is_null($pointage->heure_arrivee)) {
                return response()->json(['success' => false, 'error' => 'Pointage déjà terminé'], 400);
            }

            // Calculer les heures travaillées
            $startTime = Carbon::createFromTimeString($pointage->heure_depart, 'UTC');
            $endTime = $now;
            $hoursWorked = $startTime->diffInMinutes($endTime) / 60; // En heures avec décimales
            $hoursWorked = round($hoursWorked, 2);

            // Calculer les coûts
            $totalSupplierCost = round($hoursWorked * $pointage->supplier_unit_cost, 2);
            $totalClientAmount = round($hoursWorked * $pointage->client_unit_price, 2);

            // Mettre à jour le pointage
            $pointage->update([
                'heure_arrivee' => $now->toTimeString(),
                'arrival_location' => $data['arrival_location'],
                'quantity' => $hoursWorked,
                'total_supplier_cost' => $totalSupplierCost,
                'total_client_amount' => $totalClientAmount,
                'statut' => 'termine',
            ]);

            return response()->json([
                'success' => true,
                'pointage_id' => $pointage->id,
                'message' => 'Pointage arrêté',
                'hours_worked' => $hoursWorked,
                'supplier_cost' => $totalSupplierCost,
                'client_amount' => $totalClientAmount,
                'end_time' => $now->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Pointage stop failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur à l\'arrêt'], 500);
        }
    }

    /**
     * Annuler un pointage en cours
     */
    public function cancel(Request $request, VehiclePointage $pointage)
    {
        try {
            if (!is_null($pointage->heure_arrivee)) {
                return response()->json(['success' => false, 'error' => 'Impossible d\'annuler un pointage terminé'], 400);
            }

            $pointage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pointage annulé',
            ]);
        } catch (\Throwable $e) {
            Log::error('Pointage cancel failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur lors de l\'annulation'], 500);
        }
    }

    /**
     * Lister les pointages à facturer
     */
    public function toInvoice(Request $request)
    {
        try {
            $submodule = $request->input('submodule', 'camion_plateau');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $search = $request->input('search');
            $vehicleId = $request->input('vehicle_id');
            $driverId = $request->input('driver_id');

            $query = VehiclePointage::where('submodule', $submodule)
                ->where('statut', 'termine')
                ->where('billing_mode', 'chrono')
                ->whereNull('is_billed')
                ->orWhere('is_billed', false)
                ->with(['vehicle', 'driver', 'mission']);

            // Filtres
            if ($dateFrom) {
                $query->whereDate('date_pointage', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('date_pointage', '<=', $dateTo);
            }
            if ($vehicleId) {
                $query->where('vehicle_id', $vehicleId);
            }
            if ($driverId) {
                $query->where('driver_id', $driverId);
            }
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('task_label', 'like', "%{$search}%")
                      ->orWhere('departure_location', 'like', "%{$search}%")
                      ->orWhere('arrival_location', 'like', "%{$search}%");
                });
            }

            $pointages = $query->orderByDesc('date_pointage')->paginate(20);

            // Résumé à facturer
            $summaryQuery = clone $query;
            $summary = [
                'total_pointages' => $summaryQuery->count(),
                'total_hours' => (float) $summaryQuery->sum('quantity'),
                'total_client_amount' => (float) $summaryQuery->sum('total_client_amount'),
                'total_supplier_cost' => (float) $summaryQuery->sum('total_supplier_cost'),
            ];
            $summary['total_margin'] = $summary['total_client_amount'] - $summary['total_supplier_cost'];

            // Listes pour filtres
            $vehicles = Vehicule::orderBy('immatriculation')->get();
            $drivers = Personnel::orderBy('nom')->get();

            return view('materiel.cost-control.chronometrique.to-invoice', compact(
                'pointages',
                'summary',
                'vehicles',
                'drivers',
                'submodule',
                'dateFrom',
                'dateTo',
                'search',
                'vehicleId',
                'driverId'
            ));
        } catch (\Throwable $e) {
            Log::error('To invoice list failed', ['error' => $e->getMessage()]);
            return redirect()->route('materiel.cost-control.list')
                ->with('error', 'Impossible de charger la liste à facturer.');
        }
    }

    /**
     * Marquer des pointages comme facturés (bulk action)
     */
    public function markInvoiced(Request $request)
    {
        try {
            $data = $request->validate([
                'pointage_ids' => 'required|array|min:1',
                'pointage_ids.*' => 'exists:vehicle_pointages,id',
            ]);

            $updated = VehiclePointage::whereIn('id', $data['pointage_ids'])
                ->update([
                    'is_billed' => true,
                    'billed_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => "{$updated} pointages marqués comme facturés",
                'updated_count' => $updated,
            ]);
        } catch (\Throwable $e) {
            Log::error('Mark invoiced failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur lors de la facturation'], 500);
        }
    }
}
