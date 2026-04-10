<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Services\ProjectService;
use App\Services\ProjectFinanceService;
use App\Services\ProjectKpiService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $projectService;
    protected $financeService;
    protected $kpiService;

    public function __construct(
        ProjectService $projectService,
        ProjectFinanceService $financeService,
        ProjectKpiService $kpiService
    ) {
        $this->middleware('auth');
        $this->projectService = $projectService;
        $this->financeService = $financeService;
        $this->kpiService = $kpiService;
    }

    public function index()
    {
        $projects = Project::with('client', 'responsable')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => Project::count(),
            'brouillon' => Project::where('statut', 'brouillon')->count(),
            'valides' => Project::where('statut', 'valide')->count(),
            'en_cours' => Project::where('statut', 'en_cours')->count(),
            'termines' => Project::where('statut', 'termine')->count(),
            'clotured' => Project::where('statut', 'clotured')->count(),
        ];

        return view('projects.index', compact('projects', 'stats'));
    }

    public function list()
    {
        $projects = Project::with('client', 'responsable')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('projets.list', compact('projects'));
    }

    public function dashboard()
    {
        $projects = Project::all();
        
        $stats = [
            'total' => $projects->count(),
            'brouillon' => $projects->where('statut', 'brouillon')->count(),
            'valides' => $projects->where('statut', 'valide')->count(),
            'en_cours' => $projects->where('statut', 'en_cours')->count(),
            'termines' => $projects->where('statut', 'termine')->count(),
            'clotured' => $projects->where('statut', 'clotured')->count(),
            'budget_total' => $projects->sum('budget_estime') ?? 0,
            'budget_reel' => $projects->sum('budget_reel') ?? 0,
            'operations_total' => 0,
            'resources_total' => 0,
        ];

        // Calculer les opérations et ressources totales
        foreach ($projects as $project) {
            $stats['operations_total'] += $project->operations()->count();
            $stats['resources_total'] += $project->resources()->where('statut', '!=', 'liberee')->count();
        }

        return view('projects.dashboard', compact('stats', 'projects'));
    }

    public function create()
    {
        $clients = Client::all();
        $users = User::all();
        $types = ['transport', 'location', 'chantier', 'livraison_reguliere', 'autre'];

        return view('projects.create', compact('clients', 'users', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'responsable_id' => 'nullable|exists:users,id',
            'nom' => 'required|string|max:255',
            'type' => 'required|in:transport,location,chantier,livraison_reguliere,autre',
            'description' => 'required|string',
            'budget_estime' => 'nullable|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['statut'] = 'brouillon';
        $validated['pourcentage_avancement'] = 0;

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Projet créé avec succès');
    }

    public function show(Project $project)
    {
        $project->load([
            'client',
            'responsable',
            'avances' => function ($query) {
                $query->orderByDesc('date_encaissement')->orderByDesc('id');
            },
        ]);

        $kpis = $this->kpiService->getProjectKpis($project);

        return view('projects.show', compact('project', 'kpis'));
    }

    public function edit(Project $project)
    {
        $clients = Client::all();
        $users = User::all();
        $types = ['transport', 'location', 'chantier', 'livraison_reguliere', 'autre'];

        return view('projects.edit', compact('project', 'clients', 'users', 'types'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'responsable_id' => 'nullable|exists:users,id',
            'nom' => 'required|string|max:255',
            'type' => 'required|in:transport,location,chantier,livraison_reguliere,autre',
            'description' => 'required|string',
            'budget_estime' => 'nullable|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut',
            'notes' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project->id)->with('success', 'Projet mis à jour avec succès');
    }

    public function destroy(Project $project)
    {
        // Ne pas supprimer les projets en cours ou terminés
        if (in_array($project->statut, ['en_cours', 'termine', 'clotured'])) {
            return back()->with('error', 'Impossible de supprimer un projet en cours ou terminé');
        }

        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projet supprimé avec succès');
    }

    /**
     * Valider un projet
     */
    public function validateProject(Project $project)
    {
        if ($this->projectService->validateProject($project)) {
            return back()->with('success', 'Projet validé avec succès');
        }

        return back()->with('error', 'Impossible de valider ce projet');
    }

    /**
     * Démarrer un projet
     */
    public function startProject(Project $project)
    {
        if ($this->projectService->startProject($project)) {
            return back()->with('success', 'Projet démarré avec succès');
        }

        return back()->with('error', 'Impossible de démarrer ce projet');
    }

    /**
     * Clôturer un projet
     */
    public function closeProject(Project $project)
    {
        // Vérifier les conditions de clôture
        $requirements = $this->projectService->validateClosureRequirements($project);

        if (!$requirements['all_met']) {
            return back()->with('error', 'Les conditions de clôture ne sont pas toutes satisfaites');
        }

        if ($this->projectService->closeProject($project)) {
            return back()->with('success', 'Projet clôturé avec succès');
        }

        return back()->with('error', 'Impossible de clôturer ce projet');
    }

    /**
     * Archiver un projet
     */
    public function archiveProject(Project $project)
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
}
