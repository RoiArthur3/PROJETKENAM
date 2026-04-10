<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anomaly;
use App\Models\Inspection;
use App\Models\Vehicule;

class AuditController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        // Récupération des inspections réelles (paginées)
        // On suppose que "Contrôles" fait référence aux Inspections
        $audits = \App\Models\Inspection::with(['inspector'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('audit.index', compact('audits'));
    }

    public function dashboard()
    {
        // Dashboard réservé aux contrôles des véhicules/engins
        $controles = Inspection::with(['checking.checkable', 'anomalies'])
            ->whereHas('checking', function ($query) {
                $query->where('checkable_type', Vehicule::class);
            })
            ->get();

        // Alertes liées uniquement aux inspections véhicule/engin
        $alertes = Anomaly::with(['inspection.checking.checkable'])
            ->whereIn('status', ['open', 'in_progress'])
            ->whereHas('inspection.checking', function ($query) {
                $query->where('checkable_type', Vehicule::class);
            })
            ->orderBy('severity', 'desc')
            ->get();

        $stats = [
            'total' => $controles->count(),
            'aujourdhui' => $controles->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(),
            'cette_semaine' => $controles->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'vehicules' => $controles->filter(function ($inspection) {
                return data_get($inspection, 'checking.checkable.type_materiel') === 'Vehicule';
            })->count(),
            'engins' => $controles->filter(function ($inspection) {
                return in_array(data_get($inspection, 'checking.checkable.type_materiel'), ['Machine', 'Camion'], true);
            })->count(),
        ];

        // Calcul des scores mensuels (6 derniers mois)
        $monthlyScores = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('F'); // Full month name

            // Inspections du mois
            $monthInspections = $controles->filter(function ($item) use ($date) {
                return $item->created_at->format('Y-m') === $date->format('Y-m');
            });

            // Moyenne des scores si disponible
            // On utilise inspection_score calculé dans le modèle Inspection
            $avgScore = $monthInspections->avg(fn($insp) => $insp->inspection_score ?? 0);

            $monthlyScores[$monthKey] = round($avgScore ?? 0, 1);
        }

        // Calcul de conformité par type de matériel (Véhicule/Machine/Camion)
        $complianceByService = $controles->groupBy(function ($inspection) {
            return data_get($inspection, 'checking.checkable.type_materiel', 'Non défini');
        })->map(function ($group) {
            $total = $group->count();
            if ($total === 0) return 0;

            // Conformité = inspection avec result = 'conform' ou 'conform_with_reserves' ?
            // Le tableau de bord affiche "Conformité"
            $conformeCount = $group->filter(fn($i) => in_array($i->result, ['conform', 'conform_with_reserves']))->count();
            return round(($conformeCount / $total) * 100, 1);
        })->sortDesc()->take(5); // Top 5 services

        return view('audit.dashboard', compact('stats', 'controles', 'alertes', 'monthlyScores', 'complianceByService'));
    }

    public function historique()
    {
        $logs = \App\Models\AuditLog::with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('audit.historique', compact('logs'));
    }

    public function reports()
    {
        // Logique pour les rapports
        return view('audit.reports');
    }

    public function show($id)
    {
        return "Détails de l'audit " . $id;
    }
}
