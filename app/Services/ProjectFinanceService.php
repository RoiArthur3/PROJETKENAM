<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProjectFinanceService
{
    /**
     * Récupère le total des dépenses par type pour un projet
     */
    public function getExpensesByType(Project $project): array
    {
        if (!Schema::hasTable('project_expenses')) {
            return [];
        }

        return ProjectExpense::where('project_id', $project->id)
            ->get()
            ->groupBy('type')
            ->map(fn ($expenses) => $expenses->sum('montant'))
            ->toArray();
    }

    /**
     * Récupère le total des dépenses du projet
     */
    public function getTotalExpenses(Project $project): float
    {
        // Somme des dépenses classiques
        $expenses = 0.0;
        if (Schema::hasTable('project_expenses')) {
            $expenses = (float) ProjectExpense::where('project_id', $project->id)->sum('montant');
        }

        // Ajout du coût des missions engins liées (pointages)
        $missionsCost = 0.0;
        if (Schema::hasTable('vehicle_missions') && Schema::hasTable('vehicle_pointages')) {
            $missions = \App\Models\VehicleMission::where('source_type', 'project')
                ->where('source_id', $project->id)
                ->pluck('id');
            if ($missions->count() > 0) {
                $missionsCost = (float) \App\Models\VehiclePointage::whereIn('vehicle_mission_id', $missions)
                    ->sum('total_supplier_cost');
            }
        }

        return $expenses + $missionsCost;
    }

    /**
     * Récupère les revenus liés aux opérations du projet
     */
    public function getTotalRevenues(Project $project): float
    {
        $operationsRevenue = 0.0;
        $encaissementsRevenue = 0.0;

        try {
            if (Schema::hasTable('project_operations')) {
                $operationsRevenue = (float) DB::table('project_operations')
                    ->where('project_id', $project->id)
                    ->sum('montant_consomme');
            }

            if (Schema::hasTable('encaissements')) {
                $encaissementsRevenue = (float) DB::table('encaissements')
                    ->where('project_id', $project->id)
                    ->where('statut', 'validé')
                    ->sum('montant');
            }

            return $operationsRevenue + $encaissementsRevenue;
        } catch (\Throwable $e) {
            Log::warning('Project revenues fallback to 0: ' . $e->getMessage(), ['project_id' => $project->id]);
            return $operationsRevenue;
        }
    }

    /**
     * Calcule la rentabilité du projet
     */
    public function calculateProfitability(Project $project): array
    {
        $revenues = $this->getTotalRevenues($project);
        $expenses = $this->getTotalExpenses($project);
        $profit = $revenues - $expenses;
        $margin = $revenues > 0 ? ($profit / $revenues) * 100 : 0;

        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'profit' => $profit,
            'margin' => round($margin, 2),
        ];
    }

    /**
     * Analyse du budget : prévu vs réel
     */
    public function getBudgetAnalysis(Project $project): array
    {
        $budgetEstime = $project->budget_estime ?? 0;
        $budgetReel = $this->getTotalExpenses($project);
        $ecart = $budgetReel - $budgetEstime;
        $tauxUtilisation = $budgetEstime > 0 ? ($budgetReel / $budgetEstime) * 100 : 0;

        return [
            'budget_estime' => $budgetEstime,
            'budget_reel' => $budgetReel,
            'ecart' => $ecart,
            'taux_utilisation' => round($tauxUtilisation, 2),
            'pourcentage_restant' => round(100 - $tauxUtilisation, 2),
            'status' => $ecart > 0 ? 'dépassement' : 'dans_budget',
        ];
    }

    /**
     * Enregistre une dépense pour le projet
     */
    public function recordExpense(Project $project, array $data): ProjectExpense
    {
        $data['project_id'] = $project->id;
        $data['created_by'] = auth()->user()?->id;
        
        return ProjectExpense::create($data);
    }

    /**
     * Actualise le budget réel du projet
     */
    public function updateProjectRealBudget(Project $project): void
    {
        // Met à jour le budget réel avec toutes les dépenses y compris les pointages engins
        $totalExpenses = $this->getTotalExpenses($project);
        $project->update(['budget_reel' => $totalExpenses]);
    }
}
