<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectKpiService
{
    protected $financeService;

    public function __construct(ProjectFinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    /**
     * Récupère tous les KPIs pour le dashboard du projet
     */
    public function getProjectKpis(Project $project): array
    {
        return [
            'operationsCount' => $this->getOperationsCount($project),
            'resourcesCount' => $this->getResourcesCount($project),
            'expensesTotal' => $this->financeService->getTotalExpenses($project),
            'expensesByType' => $this->financeService->getExpensesByType($project),
            'budgetAnalysis' => $this->financeService->getBudgetAnalysis($project),
            'profitability' => $this->financeService->calculateProfitability($project),
            'progress' => $this->getProgressMetrics($project),
            'alerts' => $this->getAlerts($project),
            'milestonesStatus' => $this->getMilestonesStatus($project),
        ];
    }

    /**
     * Nombre d'opérations liées au projet
     */
    public function getOperationsCount(Project $project): int
    {
        return $project->operations()->count();
    }

    /**
     * Nombre de ressources affectées
     */
    public function getResourcesCount(Project $project): array
    {
        return $project->resources()
            ->where('statut', '!=', 'liberee')
            ->get()
            ->groupBy('type')
            ->map(fn ($items) => count($items))
            ->toArray();
    }

    /**
     * Métriques d'avancement du projet
     */
    public function getProgressMetrics(Project $project): array
    {
        $totalMilestones = $project->milestones()->count();
        $completedMilestones = $project->milestones()->where('statut', 'completee')->count();
        
        return [
            'overall_progress' => $project->pourcentage_avancement,
            'milestones_total' => $totalMilestones,
            'milestones_completed' => $completedMilestones,
            'milestones_progress' => $totalMilestones > 0 ? ($completedMilestones / $totalMilestones) * 100 : 0,
            'start_date' => $project->date_debut,
            'end_date_planned' => $project->date_fin_prevue,
            'end_date_actual' => $project->date_fin_reelle,
        ];
    }

    /**
     * Alertes et avertissements pour le projet
     */
    public function getAlerts(Project $project): array
    {
        $alerts = [];
        $budgetAnalysis = $this->financeService->getBudgetAnalysis($project);

        // Alerte de dépassement de budget
        if ($budgetAnalysis['status'] === 'dépassement') {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Dépassement de Budget',
                'message' => 'Le budget réel a dépassé le budget estimé de ' . $budgetAnalysis['ecart'] . ' FCFA',
            ];
        }

        // Alerte de dépassement de délai
        if ($project->date_fin_reelle && $project->date_fin_reelle->isAfter($project->date_fin_prevue)) {
            $retard = $project->date_fin_reelle->diffInDays($project->date_fin_prevue);
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Retard dans la Livraison',
                'message' => 'Le projet a accusé un retard de ' . $retard . ' jours',
            ];
        }

        // Alerte de milestones retardées
        $retardedMilestones = $project->milestones()
            ->where('statut', 'retardee')
            ->count();
        if ($retardedMilestones > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Milestones Retardées',
                'message' => $retardedMilestones . ' étape(s) sont en retard',
            ];
        }

        return $alerts;
    }

    /**
     * État des milestones
     */
    public function getMilestonesStatus(Project $project): array
    {
        return $project->milestones()
            ->orderBy('date_planifiee')
            ->get()
            ->map(function ($milestone) {
                return [
                    'id' => $milestone->id,
                    'title' => $milestone->titre,
                    'planned_date' => $milestone->date_planifiee,
                    'actual_date' => $milestone->date_reelle,
                    'status' => $milestone->statut,
                    'completion' => $milestone->pourcentage_completion,
                    'is_retarded' => $milestone->isRetarded(),
                ];
            })
            ->toArray();
    }
}
