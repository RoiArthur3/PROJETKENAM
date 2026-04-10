<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\Vehicle;
use App\Models\StockMovement;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\User;
use App\Models\Client;
use App\Models\Fournisseur;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalysesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('module_access:reporting');
    }

    /**
     * Affiche le tableau de bord des analyses
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'month');
        $kpis = $this->buildKpis($period);
        $startDate = $this->getStartDate($period);

        $operations = Operation::with('client')
            ->where('created_at', '>=', $startDate)
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        if ($operations->isEmpty()) {
            $operations = collect([ (object)['id' => 0, 'numero_projet' => 'N/A', 'reference' => 'N/A', 'client' => (object)['nom' => 'N/A', 'name' => 'N/A'], 'service' => 'N/A', 'created_at' => now()] ]);
        }

        return view('analyses.dashboard', compact('kpis', 'period', 'operations'));
    }

    /**
     * Exporte les données de CA et d'entrepôt au format CSV
     */
    public function exportCaEntrepot(Request $request)
    {
        // Logique d'export CA Entrepot
        // À implémenter selon les besoins spécifiques
        return response()->json([
            'success' => true,
            'message' => 'Export CA Entrepot en cours de développement'
        ]);
    }

    /**
     * Affiche la vue des comparatifs
     */
    public function comparatifs(Request $request)
    {
        $period1 = $request->get('period1', 'month');
        $period2 = $request->get('period2', 'last_month');
        $metric = $request->get('metric', 'operations');

        $startDate1 = $this->getStartDate($period1);
        $endDate1 = Carbon::now();

        $startDate2 = $this->getStartDate($period2);
        $endDate2 = $startDate1->copy()->subDay();

        $data1 = $this->getMetricData($metric, $startDate1, $endDate1);
        $data2 = $this->getMetricData($metric, $startDate2, $endDate2);

        return view('analyses.comparatifs', compact('data1', 'data2', 'period1', 'period2', 'metric'));
    }

    /**
     * Affiche les rapports d'analyse
     */
    public function rapports()
    {
        return view('analyses.rapports');
    }

    /**
     * Affiche les statistiques avancées
     */
    public function statistiques()
    {
        return view('analyses.statistiques');
    }

    /**
     * Statistiques par service
     */
    public function statsService(Request $request)
    {
        $period = $request->get('period', 'month');
        $service = $request->get('service', 'all');

        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        // Statistiques par service
        $serviceStats = Operation::whereBetween('created_at', [$startDate, $endDate])
            ->when($service !== 'all', function($query) use ($service) {
                return $query->where('service', $service);
            })
            ->select('service', DB::raw('COUNT(*) as total_operations'))
            ->groupBy('service')
            ->get();

        // Performance par service (calcul côté PHP pour compatibilité multi-SGBD)
        $operations = Operation::whereBetween('created_at', [$startDate, $endDate])
            ->when($service !== 'all', function($query) use ($service) {
                return $query->where('service', $service);
            })
            ->get();

        $performanceData = $operations
            ->groupBy('service')
            ->map(function ($group, $serviceKey) {
                $total = $group->count();
                $completed = $group->where('statut_courant', 'termine')->count();
                $inProgress = $group->where('statut_courant', 'en_cours')->count();
                $overdue = $group
                    ->where('statut_courant', '!=', 'termine')
                    ->where('echeance', '<', Carbon::now())
                    ->count();

                return (object) [
                    'service' => $serviceKey,
                    'total' => $total,
                    'completed' => $completed,
                    'in_progress' => $inProgress,
                    'overdue' => $overdue,
                    'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
                ];
            })
            ->values();

        return view('analyses.stats-service', compact('serviceStats', 'performanceData', 'period', 'service'));
    }

    public function kpi(Request $request)
    {
        $period = $request->get('period', 'month');
        $kpis = $this->buildKpis($period);

        return view('analyses.kpi', compact('kpis', 'period'));
    }

    /**
     * Calculer l'ensemble des KPIs principaux pour une période donnée.
     */
    private function buildKpis(string $period): array
    {
        $startDate = $this->getStartDate($period);

        return [
            'operations' => [
                'total' => Operation::where('created_at', '>=', $startDate)->count(),
                'completed' => Operation::where('created_at', '>=', $startDate)
                    ->where('statut_courant', 'termine')
                    ->count(),
                'avg_duration' => $this->calculateAvgOperationDuration($startDate),
                'by_priority' => Operation::where('created_at', '>=', $startDate)
                    ->select('priorite', DB::raw('COUNT(*) as count'))
                    ->groupBy('priorite')
                    ->get(),
            ],
            'fleet' => [
                'total_vehicles' => Vehicle::count(),
                'available_vehicles' => Vehicle::where('disponibilite', true)->count(),
                'utilization_rate' => $this->calculateFleetUtilization($startDate),
                'maintenance_cost' => $this->calculateMaintenanceCost($startDate),
            ],
            'warehouse' => [
                'total_products' => \App\Models\Product::count(),
                'stock_value' => \App\Models\StockLevel::sum('total_value'),
                'low_stock_alerts' => \App\Models\StockLevel::where('low_stock_alert', true)->count(),
                'turnover_rate' => $this->calculateStockTurnover($startDate),
            ],
            'financial' => [
                'total_revenue' => Invoice::where('created_at', '>=', $startDate)->sum('net_amount'),
                'total_expenses' => Expense::where('created_at', '>=', $startDate)->sum('montant'),
                'profit_margin' => $this->calculateProfitMargin($startDate),
                'avg_payment_delay' => $this->calculateAvgPaymentDelay($startDate),
            ],
            'hr' => [
                'total_employees' => User::whereIn('role', ['agent', 'chef_service', 'controleur', 'comptable'])->count(),
                'active_employees' => User::whereIn('role', ['agent', 'chef_service', 'controleur', 'comptable'])
                    ->where('is_active', true)
                    ->count(),
                'attendance_rate' => $this->calculateAttendanceRate($startDate),
                'overtime_hours' => $this->calculateOvertimeHours($startDate),
            ],
            'commercial' => [
                'total_clients' => Client::count(),
                'new_clients' => Client::where('created_at', '>=', $startDate)->count(),
                'active_clients' => Client::count(), // Temporarily count all clients until statut column is added
                'client_satisfaction' => $this->calculateClientSatisfaction($startDate),
            ]
        ];
    }

    public function graphiques(Request $request)
    {
        $type = $request->get('type', 'operations');
        $period = $request->get('period', 'month');

        $startDate = $this->getStartDate($period);

        $chartData = match($type) {
            'operations' => $this->getOperationsChartData($startDate),
            'fleet' => $this->getFleetChartData($startDate),
            'warehouse' => $this->getWarehouseChartData($startDate),
            'financial' => $this->getFinancialChartData($startDate),
            'hr' => $this->getHRChartData($startDate),
            default => []
        };

        return view('analyses.graphiques', compact('chartData', 'type', 'period'));
    }

    /**
     * Gère les exports de données
     */
    public function exports(Request $request)
    {
        $type = $request->get('type', 'operations');
        $period = $request->get('period', 'month');
        $format = $request->get('format', 'pdf');

        // Logique d'export (PDF/Excel)
        return view('analyses.exports', compact('type', 'period', 'format'));
    }

    // Méthodes privées pour les calculs

    private function getStartDate($period)
    {
        return match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::now()->startOfYear(),
            'last_month' => Carbon::now()->subMonth()->startOfMonth(),
            'last_quarter' => Carbon::now()->subQuarter()->startOfQuarter(),
            'last_year' => Carbon::now()->subYear()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    private function getMetricData($metric, $startDate, $endDate)
    {
        return match($metric) {
            'operations' => Operation::whereBetween('created_at', [$startDate, $endDate])->count(),
            'revenue' => Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('net_amount'),
            'expenses' => Expense::whereBetween('created_at', [$startDate, $endDate])->sum('montant'),
            'profit' => Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('net_amount') -
                       Expense::whereBetween('created_at', [$startDate, $endDate])->sum('montant'),
            default => 0
        };
    }

    private function calculateAvgOperationDuration($startDate)
    {
        $operations = Operation::where('created_at', '>=', $startDate)
            ->where('statut_courant', 'termine')
            ->get();

        if ($operations->isEmpty()) return 0;

        $totalHours = 0;
        foreach ($operations as $op) {
            $start = Carbon::parse($op->created_at);
            $end = Carbon::parse($op->updated_at);
            $totalHours += $start->diffInHours($end);
        }

        return round($totalHours / $operations->count(), 1);
    }

    private function calculateFleetUtilization($startDate)
    {
        $totalVehicles = Vehicle::count();
        // Temporarily set usedVehicles to 0 until proper vehicle relationship is established in operations
        $usedVehicles = 0; // Operation::where('created_at', '>=', $startDate)->distinct('vehicule_id')->count('vehicule_id');

        return $totalVehicles > 0 ? round(($usedVehicles / $totalVehicles) * 100, 1) : 0;
    }

    private function calculateMaintenanceCost($startDate)
    {
        // Calcul simplifié - à adapter selon vos données réelles
        return Expense::where('created_at', '>=', $startDate)
            ->where('categorie', 'maintenance')
            ->sum('montant');
    }

    private function calculateStockTurnover($startDate)
    {
        $startStock = \App\Models\StockLevel::sum('current_stock');
        $sales = StockMovement::where('created_at', '>=', $startDate)
            ->where('type', 'sortie')
            ->sum('quantity');

        return $startStock > 0 ? round(($sales / $startStock) * 100, 1) : 0;
    }

    private function calculateProfitMargin($startDate)
    {
        $revenue = Invoice::where('created_at', '>=', $startDate)->sum('net_amount');
        $expenses = Expense::where('created_at', '>=', $startDate)->sum('montant');

        return $revenue > 0 ? round((($revenue - $expenses) / $revenue) * 100, 1) : 0;
    }

    private function calculateAvgPaymentDelay($startDate)
    {
        // Temporarily return 0 until payment_date column is added to invoices table
        // $invoices = Invoice::where('created_at', '>=', $startDate)
        //     ->whereNotNull('payment_date')
        //     ->get();

        // if ($invoices->isEmpty()) return 0;

        // $totalDelay = 0;
        // foreach ($invoices as $invoice) {
        //     $dueDate = Carbon::parse($invoice->due_date);
        //     $paymentDate = Carbon::parse($invoice->payment_date);
        //     $totalDelay += $dueDate->diffInDays($paymentDate);
        // }

        // return round($totalDelay / $invoices->count(), 1);
        return 0;
    }

    private function calculateAttendanceRate($startDate)
    {
        // Calcul simplifié - à adapter selon vos données de pointage
        $totalEmployees = User::whereIn('role', ['agent', 'chef_service', 'controleur', 'comptable'])->count();
        $presentEmployees = User::whereIn('role', ['agent', 'chef_service', 'controleur', 'comptable'])
            ->where('last_login_at', '>=', $startDate)
            ->count();

        return $totalEmployees > 0 ? round(($presentEmployees / $totalEmployees) * 100, 1) : 0;
    }

    private function calculateOvertimeHours($startDate)
    {
        // Calcul basé sur les heures supplémentaires enregistrées dans les pointages
        // Si la table heure_sups n'existe pas, on retourne 0 ou on calcule depuis pointages
        try {
            if (class_exists('\App\Models\Pointage')) {
                return \App\Models\Pointage::where('date', '>=', $startDate)
                    ->sum('heures_supplementaires') ?? 0;
            }
        } catch (\Exception $e) {
            // Table non disponible
        }
        return 0;
    }

    private function calculateClientSatisfaction($startDate)
    {
        // Calcul basé sur les évaluations ou retours clients
        // À adapter selon vos données réelles
        return 85.5; // Valeur exemple
    }

    // Méthodes pour les graphiques
    private function getOperationsChartData($startDate)
    {
        $monthExpr = $this->monthFormatExpression('created_at');
        return Operation::where('created_at', '>=', $startDate)
            ->selectRaw($monthExpr . ' as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getFleetChartData($startDate)
    {
        try {
            return \App\Models\Vehicule::select('disponible', DB::raw('COUNT(*) as count'))
                ->groupBy('disponible')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getWarehouseChartData($startDate)
    {
        try {
            return \App\Models\StockBalance::with('warehouse')
                ->selectRaw('warehouse_id, SUM(qty_available) as stock, SUM(qty_available * unit_price) as value')
                ->groupBy('warehouse_id')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getFinancialChartData($startDate)
    {
        $monthExpr = $this->monthFormatExpression('created_at');
        $revenue = Invoice::where('created_at', '>=', $startDate)
            ->selectRaw($monthExpr . ' as month, SUM(net_amount) as amount')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $expenses = Expense::where('created_at', '>=', $startDate)
            ->selectRaw($monthExpr . ' as month, SUM(montant) as amount')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'revenue' => $revenue,
            'expenses' => $expenses
        ];
    }

    private function getHRChartData($startDate)
    {
        return User::whereIn('role', ['agent', 'chef_service', 'controleur', 'comptable'])
            ->select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get();
    }

    /**
     * Expression SQL portable pour formatter une date en YYYY-MM selon le driver DB.
     */
    private function monthFormatExpression(string $column = 'created_at'): string
    {
        try {
            $driver = DB::getDriverName();
        } catch (\Throwable $e) {
            $driver = 'mysql';
        }

        return match ($driver) {
            'sqlite' => "strftime('%Y-%m', $column)",
            'pgsql' => "to_char($column, 'YYYY-MM')",
            default => "DATE_FORMAT($column, '%Y-%m')",
        };
    }
}
