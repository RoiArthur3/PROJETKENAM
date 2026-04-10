<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectResource;
use App\Models\ProjectOperation;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    /**
     * Valide un projet (passage brouillon → validé)
     */
    public function validateProject(Project $project): bool
    {
        if (!$project->canValidate()) {
            return false;
        }
        
        return $project->update(['statut' => 'valide']);
    }

    /**
     * Démarre un projet (passage validé → en_cours)
     */
    public function startProject(Project $project): bool
    {
        if (!$project->canStart()) {
            return false;
        }
        
        return $project->update(['statut' => 'en_cours']);
    }

    /**
     * Clôt un projet (passage en_cours → terminé)
     */
    public function closeProject(Project $project): bool
    {
        if (!$project->canClose()) {
            return false;
        }
        
        return $project->update([
            'statut' => 'termine',
            'date_fin_reelle' => now()->toDateString(),
        ]);
    }

    /**
     * Archive un projet (passage terminé → clôturé)
     */
    public function archiveProject(Project $project): bool
    {
        if ($project->statut !== 'termine') {
            return false;
        }

        return $project->update(['statut' => 'clotured']);
    }

    /**
     * Affecte une ressource au projet
     */
    public function affectResource(Project $project, array $data): ProjectResource
    {
        $data['project_id'] = $project->id;
        return ProjectResource::create($data);
    }

    /**
     * Libère une ressource du projet
     */
    public function releaseResource(ProjectResource $resource): bool
    {
        return $resource->update([
            'statut' => 'liberee',
            'date_liberation' => now()->toDateString(),
        ]);
    }

    /**
     * Lie une opération au projet
     */
    public function linkOperation(Project $project, int $operationId, array $data = []): ProjectOperation
    {
        $data['project_id'] = $project->id;
        $data['operation_id'] = $operationId;
        
        return ProjectOperation::create($data);
    }

    /**
     * Retire une opération du projet
     */
    public function unlinkOperation(Project $project, int $operationId): bool
    {
        return ProjectOperation::where('project_id', $project->id)
            ->where('operation_id', $operationId)
            ->delete() > 0;
    }

    /**
     * Met à jour l'avancement du projet
     */
    public function updateProgress(Project $project, int $percentage): bool
    {
        if ($percentage < 0 || $percentage > 100) {
            return false;
        }

        return $project->update(['pourcentage_avancement' => $percentage]);
    }

    /**
     * Valide les validations requises pour clôturer le projet
     */
    public function validateClosureRequirements(Project $project): array
    {
        $requirements = [
            'all_operations_complete' => $project->operations()
                ->whereHas('operation', fn ($q) => $q->whereNotIn('statut', ['termine', 'clotured']))
                ->count() === 0,
            'all_resources_released' => $project->resources()
                ->where('statut', '!=', 'liberee')
                ->count() === 0,
            'all_documents_submitted' => $project->documents()->count() > 0,
        ];

        return [
            'all_met' => collect($requirements)->every(fn ($val) => $val),
            'requirements' => $requirements,
        ];
    }
}
