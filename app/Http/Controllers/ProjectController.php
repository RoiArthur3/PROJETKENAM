<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Affiche la liste des projets.
     */
    public function index()
    {
        $projects = Project::orderBy('created_at', 'desc')->paginate(10);
        return view('projets.index', compact('projects'));
    }

    /**
     * Affiche le formulaire de création de projet.
     */
    public function create()
    {
        $vehicules = \App\Models\Vehicule::all();
        $clients = \App\Models\Client::all();
        $users = \App\Models\User::all();

        return view('projets.create', compact('vehicules', 'clients', 'users'));
    }

    /**
     * Enregistre un nouveau projet.
     */
    public function store(ProjectRequest $request)
    {
        $project = $this->projectService->createProject($request->validated());
        return redirect()->route('projets.show', $project)
            ->with('success', 'Projet créé avec succès');
    }

    /**
     * Affiche les détails d'un projet.
     */
    public function show(Project $project)
    {
        $project->load(['missions', 'pointages', 'vehicules']);
        return view('projets.show', compact('project'));
    }

    /**
     * Affiche le formulaire d'édition de projet.
     */
    public function edit(Project $project)
    {
        return view('projets.edit', compact('project'));
    }

    /**
     * Met à jour les informations d'un projet.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $project = $this->projectService->updateProject($project, $request->validated());
        return redirect()->route('projets.show', $project)
            ->with('success', 'Projet mis à jour avec succès');
    }

    /**
     * Supprime un projet.
     */
    public function destroy(Project $project)
    {
        if ($this->projectService->deleteProject($project)) {
            return redirect()->route('projets.index')
                ->with('success', 'Projet supprimé avec succès');
        }

        return redirect()->route('projets.index')
            ->with('error', 'Impossible de supprimer ce projet');
    }

    /**
     * Affiche le rapport de pointage des engins pour un projet donné, filtrable par engin et période.
     */
    public function pointageReport(Request $request, Project $project)
    {
        $vehiculeId = $request->input('vehicule_id');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        // Récupère toutes les missions engins liées au projet
        $missions = \App\Models\VehicleMission::where('source_type', 'project')
            ->where('source_id', $project->id)
            ->when($vehiculeId, fn($q) => $q->where('vehicle_id', $vehiculeId))
            ->pluck('id');

        // Récupère tous les pointages filtrés
        $pointages = \App\Models\VehiclePointage::with(['vehicle'])
            ->whereIn('vehicle_mission_id', $missions)
            ->when($dateDebut, fn($q) => $q->whereDate('date_pointage', '>=', $dateDebut))
            ->when($dateFin, fn($q) => $q->whereDate('date_pointage', '<=', $dateFin))
            ->orderBy('date_pointage', 'desc')
            ->get();

        // Liste des engins affectés
        $vehicules = \App\Models\Vehicule::whereIn('id',
            \App\Models\VehicleMission::where('source_type', 'project')
                ->where('source_id', $project->id)
                ->pluck('vehicle_id')
        )->get();

        return view('projets.pointage-report', compact('project', 'pointages', 'vehicules', 'vehiculeId', 'dateDebut', 'dateFin'));
    }

    /**
     * Affiche le dashboard des projets avec KPIs.
     */
    public function dashboard()
    {
        $projects = Project::withCount(['missions', 'vehicules'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $kpis = [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('statut', 'actif')->count(),
            'total_missions' => \App\Models\VehicleMission::count(),
            'total_vehicules' => \App\Models\Vehicule::count(),
        ];

        return view('projets.dashboard', compact('projects', 'kpis'));
    }

    /**
     * Archive un projet.
     */
    public function archive(Project $project)
    {
        if ($this->projectService->archiveProject($project)) {
            return back()->with('success', 'Projet archivé avec succès');
        }

        return back()->with('error', 'Impossible d\'archiver ce projet');
    }

    /**
     * Mettre à jour l'avancement
     */
    public function updateProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'pourcentage_avancement' => 'required|integer|min:0|max:100',
        ]);

        $this->projectService->updateProgress($project, $validated['pourcentage_avancement']);

        return back()->with('success', 'Avancement mis à jour');
    }

    /**
     * Prolonger un projet
     */
    public function prolongerProjet(Request $request, Project $project)
    {
        $validated = $request->validate([
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif_prolongation' => 'required|string|max:1000',
        ]);

        $project->update([
            'date_fin' => $validated['date_fin'],
            'motif_prolongation' => $validated['motif_prolongation'],
            'date_derniere_modification' => now(),
        ]);

        return back()->with('success', 'Projet prolongé avec succès');
    }

    /**
     * Clôturer un projet
     */
    public function closeProject(Request $request, Project $project)
    {
        $validated = $request->validate([
            'date_fin_reelle' => 'required|date',
            'rapport_cloture' => 'required|string|max:2000',
            'avancement_final' => 'required|integer|min:0|max:100',
        ]);

        $project->update([
            'statut' => 'termine',
            'date_fin_reelle' => $validated['date_fin_reelle'],
            'rapport_cloture' => $validated['rapport_cloture'],
            'avancement' => $validated['avancement_final'],
            'date_cloture' => now(),
        ]);

        return redirect()->route('projets.index')->with('success', 'Projet clôturé avec succès');
    }

    /**
     * Exporte les données d'un projet au format Excel.
     */
    public function export(Project $project)
    {
        return $this->projectService->exportProject($project);
    }

    /**
     * API: Récupère les statistiques d'un projet.
     */
    public function stats(Project $project)
    {
        $stats = $this->projectService->getProjectStats($project);
        return response()->json($stats);
    }
}
