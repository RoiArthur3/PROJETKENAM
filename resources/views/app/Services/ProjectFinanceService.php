<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Support\Facades\DB;

class ProjectFinanceService
{
    /**
     * Récupère le total des dépenses par type pour un projet
     */
    public function getExpensesByType(Project $project): array
    {
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
        return ProjectExpense::where('project_id', $project->id)->sum('montant');
    }

    /**
     * Récupère les revenus liés aux opérations du projet
     */
    public function getTotalRevenues(Project $project): float
    {
        // Somme des factures générées pour les opérations du projet
        return DB::table('project_operations')
            ->where('project_id', $project->id)
            ->sum('montant_consomme'); // À adapter selon votre logique réelle
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
        $data['created_by'] = auth()->id();
        
        return ProjectExpense::create($data);
    }

    /**
     * Actualise le budget réel du projet
     */
    public function updateProjectRealBudget(Project $project): void
    {
        $totalExpenses = $this->getTotalExpenses($project);
        $project->update(['budget_reel' => $totalExpenses]);
    }
}
