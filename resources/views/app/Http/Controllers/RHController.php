<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pointage;
use App\Models\Paie;
use App\Models\HeureSup;
use App\Models\Conge;
use App\Models\Service;
use App\Models\EntrepriseSettings;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RHController extends Controller
{
    /**
     * Afficher le dashboard RH
     */
    public function dashboard()
    {
        try {
            // Vérifier les permissions
            $user = auth()->user();
            if (!$user || !$user->canAccessModule('rh')) {
                abort(403);
            }

            // Données pour les graphiques
            $evolutionData = $this->getEvolutionData();
            $servicesData = $this->getServicesData();

            // Données pour les KPIs
            $kpis = $this->getKPIs();

            // Dernières embauches
            $recentHires = $this->getRecentHires();

            // Alertes RH dynamiques
            $alertes = $this->getRHAlerts();

            // Congés en attente
            $congesEnAttente = $this->getCongesEnAttente();

            return view('rh.dashboard', compact(
                'evolutionData', 'servicesData', 'kpis',
                'recentHires', 'alertes', 'congesEnAttente'
            ));
        } catch (\Exception $e) {
            return view('rh.dashboard', [
                'evolutionData' => [],
                'servicesData' => [],
                'kpis' => [],
                'recentHires' => collect(),
                'alertes' => [],
                'congesEnAttente' => collect(),
            ]);
        }
    }

    /**
     * Afficher la liste des employés/agents
     */
    public function index(Request $request)
    {
        // Vérifier les permissions
        $user = auth()->user();
        if (!$user || !$user->canAccessModule('rh')) {
            abort(403);
        }

        $query = User::query();

        // Filtrer par rôle si spécifié
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        // Filtrer par service si spécifié
        if ($request->filled('service')) {
            $query->where('service_id', $request->get('service'));
        }

        // Rechercher par nom ou email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $agents = $query->orderBy('name')->paginate(25);

        return view('rh.agents', compact('agents'));
    }

    /**
     * Afficher la liste des employés (alias de index)
     */
    public function employes(Request $request)
    {
        return $this->index($request);
    }

    public function create()
    {
        return $this->createAgent();
    }

    public function store(Request $request)
    {
        return $this->storeAgent($request);
    }

    public function update(Request $request, $user)
    {
        return $this->updateAgent($request, $user);
    }

    /**
     * Gestion des présences
     */
    public function presence(Request $request)
    {
        $query = Pointage::with('user');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $pointages = $query->orderBy('created_at', 'desc')->paginate(50);
        return view('rh.presence', compact('pointages'));
    }

    /**
     * Gestion des services
     */
    public function services()
    {
        $services = Service::orderBy('nom')->get();
        return view('rh.services', compact('services'));
    }

    /**
     * Gestion des responsables
     */
    public function responsables()
    {
        $responsables = User::where('role', 'admin')->orWhere('role', 'moderator')->get();
        return view('rh.responsables', compact('responsables'));
    }

    /**
     * Obtenir les KPIs du dashboard RH
     */
    public function getKPIs()
    {
        try {
            // Total des agents/employés
            $total = User::whereIn('role', ['agent', 'employe', 'moderator'])->count();

            // Présents aujourd'hui: pointages validés distincts
            $presents = Pointage::whereDate('created_at', today())
                ->where('statut', '!=', 'absent')
                ->distinct('user_id')
                ->count('user_id');

            $taux = $total > 0 ? round(($presents / $total) * 100, 1) . '%' : '0%';

            // Agents actifs
            $agentsActifs = Schema::hasColumn('users', 'is_active')
                ? User::whereIn('role', ['agent', 'employe', 'moderator'])
                    ->where('is_active', true)
                    ->count()
                : $total;

            // Agents en période d'essai (moins de 3 mois)
            $agentsEssai = Schema::hasColumn('users', 'date_embauche')
                ? User::whereIn('role', ['agent', 'employe', 'moderator'])
                    ->where('date_embauche', '>=', now()->subMonths(3))
                    ->count()
                : 0;

            // Agents partis ce mois
            $agentsDepart = Schema::hasColumn('users', 'date_depart')
                ? User::whereNotNull('date_depart')
                    ->whereMonth('date_depart', now()->month)
                    ->count()
                : 0;

            // Évolution mensuelle (nouvelles embauches ce mois)
            $evolutionMois = Schema::hasColumn('users', 'date_embauche')
                ? User::whereIn('role', ['agent', 'employe', 'moderator'])
                    ->whereMonth('date_embauche', now()->month)
                    ->count()
                : 0;

            // Congés actifs
            $congesActifs = class_exists('App\Models\Conge')
                ? Conge::where('statut', 'approuve')
                    ->where('date_fin', '>=', today())
                    ->count()
                : 0;

            // Absents pour maladie
            $absentsMaladie = class_exists('App\Models\Conge')
                ? Conge::where('statut', 'approuve')
                    ->where('type', 'maladie')
                    ->whereDate('created_at', today())
                    ->count()
                : 0;

            // Retours prévus cette semaine
            $retourPrevu = class_exists('App\Models\Conge')
                ? Conge::where('statut', 'approuve')
                    ->whereBetween('date_fin', [today(), today()->addDays(7)])
                    ->count()
                : 0;

            return [
                'total_agents' => $total,
                'agents_actifs' => $agentsActifs,
                'agents_essai' => $agentsEssai,
                'agents_depart' => $agentsDepart,
                'evolution_mois' => $evolutionMois,
                'presence_aujourdhui' => $presents,
                'taux_presence' => $taux,
                'conges_actifs' => $congesActifs,
                'retours_semaine' => $retourPrevu,
                'absents_maladie' => $absentsMaladie,
                'retour_prevu' => $retourPrevu,
            ];
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des valeurs par défaut
            return [
                'total_agents' => 0,
                'agents_actifs' => 0,
                'agents_essai' => 0,
                'agents_depart' => 0,
                'evolution_mois' => 0,
                'presence_aujourdhui' => 0,
                'taux_presence' => '0%',
                'conges_actifs' => 0,
                'retours_semaine' => 0,
                'absents_maladie' => 0,
                'retour_prevu' => 0,
            ];
        }
    }

    /**
     * Obtenir les données d'évolution
     */
    public function getEvolutionData()
    {
        try {
            $evolutionData = [];

            // Données des 6 derniers mois
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthName = $month->format('M');
                $year = $month->format('Y');
                $monthNum = $month->month;

                // Nouvelles embauches ce mois
                $nouvellesEmbauches = Schema::hasColumn('users', 'date_embauche')
                    ? User::whereIn('role', ['agent', 'employe', 'moderator'])
                        ->whereYear('date_embauche', $year)
                        ->whereMonth('date_embauche', $monthNum)
                        ->count()
                    : 0;

                // Départs ce mois
                $departs = Schema::hasColumn('users', 'date_depart')
                    ? User::whereNotNull('date_depart')
                        ->whereYear('date_depart', $year)
                        ->whereMonth('date_depart', $monthNum)
                        ->count()
                    : 0;

                // Total agents à la fin du mois
                $totalAgents = User::whereIn('role', ['agent', 'employe', 'moderator'])
                    ->where(function($query) use ($month) {
                        $query->whereNull('date_depart')
                              ->orWhere('date_depart', '>', $month->endOfMonth());
                    })
                    ->count();

                $evolutionData[] = [
                    'month' => $monthName,
                    'nouvelles' => $nouvellesEmbauches,
                    'departs' => $departs,
                    'total' => $totalAgents
                ];
            }

            return $evolutionData;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtenir les données par services
     */
    public function getServicesData()
    {
        try {
            $servicesData = [];

            if (class_exists('App\Models\Service')) {
                $services = Service::with('users')->get();

                foreach ($services as $service) {
                    $agentsCount = $service->users ? $service->users->count() : 0;

                    // Présents aujourd'hui dans ce service
                    $presentsCount = Pointage::whereDate('created_at', today())
                        ->whereHas('user', function($query) use ($service) {
                            $query->where('service_id', $service->id);
                        })
                        ->distinct('user_id')
                        ->count('user_id');

                    $servicesData[] = [
                        'name' => $service->nom,
                        'agents' => $agentsCount,
                        'presents' => $presentsCount,
                        'taux_presence' => $agentsCount > 0 ? round(($presentsCount / $agentsCount) * 100, 1) : 0
                    ];
                }
            } else {
                // Fallback si le modèle Service n'existe pas
                $servicesData = [
                    ['name' => 'Administration', 'agents' => 0, 'presents' => 0, 'taux_presence' => 0],
                    ['name' => 'Opérations', 'agents' => 0, 'presents' => 0, 'taux_presence' => 0],
                    ['name' => 'Commercial', 'agents' => 0, 'presents' => 0, 'taux_presence' => 0],
                ];
            }

            return $servicesData;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Dernières embauches
     */
    private function getRecentHires()
    {
        try {
            return User::whereIn('role', ['agent', 'employe', 'moderator'])
                ->whereNotNull('date_embauche')
                ->orderBy('date_embauche', 'desc')
                ->take(5)
                ->get(['id', 'name', 'role', 'date_embauche', 'contrat', 'service_id']);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Alertes RH dynamiques
     */
    private function getRHAlerts()
    {
        $alertes = [];
        try {
            // Contrats arrivant à échéance (dans les 30 prochains jours)
            if (Schema::hasColumn('users', 'date_fin_contrat')) {
                $contratsExpiring = User::whereNotNull('date_fin_contrat')
                    ->whereBetween('date_fin_contrat', [today(), today()->addDays(30)])
                    ->count();
                if ($contratsExpiring > 0) {
                    $alertes[] = [
                        'type' => 'warning',
                        'icon' => 'fa-file-contract',
                        'message' => "<strong>{$contratsExpiring} contrat(s)</strong> arrive(nt) à échéance dans les 30 jours",
                        'link' => route('rh.contrats.index'),
                    ];
                }
            }

            // Congés en attente de validation
            if (class_exists('App\Models\Conge')) {
                $congesPending = Conge::where('statut', 'en_attente')->count();
                if ($congesPending > 0) {
                    $alertes[] = [
                        'type' => 'info',
                        'icon' => 'fa-calendar-times',
                        'message' => "<strong>{$congesPending} demande(s) de congé</strong> en attente de validation",
                        'link' => route('rh.conges.index'),
                    ];
                }
            }

            // Période d'essai se terminant bientôt
            if (Schema::hasColumn('users', 'periode_essai') && Schema::hasColumn('users', 'date_embauche')) {
                $essaiEndingSoon = User::whereNotNull('periode_essai')
                    ->whereNotNull('date_embauche')
                    ->whereRaw("DATE_ADD(date_embauche, INTERVAL periode_essai DAY) BETWEEN ? AND ?", [today(), today()->addDays(30)])
                    ->count();
                if ($essaiEndingSoon > 0) {
                    $alertes[] = [
                        'type' => 'warning',
                        'icon' => 'fa-hourglass-half',
                        'message' => "<strong>{$essaiEndingSoon} période(s) d'essai</strong> se termine(nt) bientôt",
                        'link' => route('rh.employes.index'),
                    ];
                }
            }

            // Agents sans pointage aujourd'hui
            $totalAgents = User::whereIn('role', ['agent', 'employe', 'moderator'])->where('is_active', true)->count();
            $presentsToday = Pointage::whereDate('created_at', today())->distinct('user_id')->count('user_id');
            $absentsToday = $totalAgents - $presentsToday;
            if ($absentsToday > 0 && now()->hour >= 9) {
                $alertes[] = [
                    'type' => 'danger',
                    'icon' => 'fa-user-times',
                    'message' => "<strong>{$absentsToday} agent(s)</strong> sans pointage aujourd'hui",
                    'link' => route('rh.pointages.index'),
                ];
            }

            if (empty($alertes)) {
                $alertes[] = [
                    'type' => 'success',
                    'icon' => 'fa-check-circle',
                    'message' => 'Aucune alerte RH en cours',
                    'link' => '#',
                ];
            }
        } catch (\Exception $e) {
            // silencieux
        }
        return $alertes;
    }

    /**
     * Congés en attente de validation
     */
    private function getCongesEnAttente()
    {
        try {
            return Conge::with('user')
                ->where('statut', 'en_attente')
                ->orderBy('date_demande', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Liste des contrats (depuis la table users: contrat, date_embauche, salaire)
     */
    public function contrats(Request $request)
    {
        $q = User::query()->whereIn('role', ['agent', 'employe', 'moderator', 'admin']);

        if (Schema::hasColumn('users', 'contrat')) {
            // Filtres
            if ($request->filled('type')) {
                $q->where('contrat', $request->get('type'));
            }
            $cdi = User::where('contrat','CDI')->count();
            $cdd = User::where('contrat','CDD')->count();
        } else {
            $cdi = 0;
            $cdd = 0;
        }

        if ($request->filled('agent')) {
            $agent = trim($request->get('agent'));
            $q->where(function($w) use ($agent){
                $w->where('name','like',"%$agent%")
                  ->orWhere('email','like',"%$agent%");
            });
        }

        $contrats = $q->orderBy('name')->paginate(25);
        $total = $q->count();

        return view('rh.contrats', compact('contrats','total','cdi','cdd'));
    }

    /**
     * Créer un contrat
     */
    public function createContrat()
    {
        $users = User::orderBy('name')->get(['id','name','email','role']);
        return view('rh.contrats-create', compact('users'));
    }

    /**
     * Enregistrer un contrat
     */
    public function storeContrat(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'contrat' => 'required|string',
            'date_embauche' => 'required|date',
            'salaire' => 'required|numeric',
        ]);

        $user = User::find($validated['user_id']);
        if (\Illuminate\Support\Facades\Schema::hasColumn('users','contrat')) {
            $user->contrat = $validated['contrat'];
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('users','date_embauche')) {
            $user->date_embauche = $validated['date_embauche'];
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('users','salaire')) {
            $user->salaire = $validated['salaire'];
        }
        $user->save();

        return redirect()->route('rh.contrats.index')->with('success', 'Contrat enregistré pour '.$user->name);
    }

    /**
     * Afficher la liste des agents
     */
    public function agents()
    {
        try {
            // Vérifier les permissions
            $user = auth()->user();
            if (!$user || !$user->canAccessModule('rh')) {
                abort(403);
            }

            $agents = User::with(['service'])->orderBy('name')->paginate(25);
            return view('rh.agents.index', compact('agents'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une vue simplifiée
            $agents = collect([]);
            return view('rh.agents.index', compact('agents'));
        }
    }

    /**
     * Créer un agent
     */
    public function createAgent()
    {
        // Vérifier les permissions
        $user = auth()->user();
        if (!$user || !$user->canAccessModule('rh')) {
            abort(403);
        }

        $services = \App\Models\Service::orderBy('nom')->get();
        return view('rh.agents.create', compact('services'));
    }

    /**
     * Enregistrer un agent
     */
    public function storeAgent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|max:50',
            'service_id' => 'nullable|integer|exists:services,id',
            'telephone' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'contrat' => 'nullable|string|max:50',
            'date_embauche' => 'nullable|date',
            'date_fin_contrat' => 'nullable|date|after_or_equal:date_embauche',
            'salaire' => 'nullable|numeric',
            'actif' => 'nullable|boolean',
        ]);

        if (!empty($validated['telephone']) && empty($validated['phone'])) {
            $validated['phone'] = $validated['telephone'];
        }

        $validated['role'] = $validated['role'] ?? 'agent';
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = (bool) ($validated['actif'] ?? false);
        unset($validated['actif']);

        if (($validated['contrat'] ?? null) === 'CDI') {
            $validated['date_fin_contrat'] = null;
        }

        $user = User::create($validated);

        // Permissions modules : opérations uniquement
        $permissionFields = [
            'can_access_dashboard',
            'can_access_operations',
            'can_access_hr',
            'can_access_fleet',
            'can_access_suppliers',
            'can_access_warehouse',
            'can_access_accounting',
            'can_access_invoicing',
            'can_access_reporting',
            'can_access_commercial',
            'can_access_prospection',
            'can_access_ateliers',
            'can_access_projects',
            'can_access_audit',
            'can_access_services',
            'can_access_comptes',
            'can_access_system',
        ];

        foreach ($permissionFields as $field) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', $field)) {
                $user->{$field} = ($field === 'can_access_operations');
            }
        }
        $user->save();

        return redirect()->route('rh.agents.index')->with('success', 'Agent créé et enregistré dans la base.');
    }

    /**
     * Afficher un agent
     */
    public function show($user)
    {
        return $this->showAgent($user);
    }

    /**
     * Modifier un agent
     */
    public function edit($user)
    {
        return $this->editAgent($user);
    }

    /**
     * Supprimer un agent
     */
    public function destroy($user)
    {
        $user = User::findOrFail($user);

        // Vérifier si l'utilisateur peut être supprimé
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('rh.agents.index')->with('success', 'Agent supprimé avec succès.');
    }

    /**
     * Afficher un agent
     */
    public function showAgent($agent)
    {
        $agent = User::with('service')->findOrFail($agent);

        // Calcul des heures de retard mensuelles
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyPointages = Pointage::where('user_id', $agent->id)
            ->whereMonth('date_pointage', $currentMonth)
            ->whereYear('date_pointage', $currentYear)
            ->get();

        $totalDelayMinutes = 0;
        foreach ($monthlyPointages as $pointage) {
            if ($pointage->heure_arrivee) {
                $arrivalTime = \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_arrivee);
                $delayLimit = \Carbon\Carbon::createFromTime(7, 30, 0);

                if ($arrivalTime->greaterThan($delayLimit)) {
                    $totalDelayMinutes += $arrivalTime->diffInMinutes($delayLimit);
                }
            }
        }

        $hireYear = $agent->date_embauche ? \Carbon\Carbon::parse($agent->date_embauche)->year : null;

        return view('rh.agents.show', compact('agent', 'totalDelayMinutes', 'hireYear'));
    }

    /**
     * Modifier un agent
     */
    public function editAgent($agent)
    {
        $agent = User::with('service')->findOrFail($agent);
        $services = Service::orderBy('nom')->get();
        return view('rh.agents.edit', compact('agent', 'services'));
    }

    /**
     * Mettre à jour un agent
     */
    public function updateAgent(Request $request, $agent)
    {
        $agent = User::findOrFail($agent);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $agent->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|max:50',
            'telephone' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'service_id' => 'nullable|integer|exists:services,id',
            'contrat' => 'nullable|string|max:50',
            'date_embauche' => 'nullable|date',
            'date_fin_contrat' => 'nullable|date|after_or_equal:date_embauche',
            'salaire' => 'nullable|numeric',
        ]);

        if (array_key_exists('password', $validated) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (($validated['contrat'] ?? null) === 'CDI') {
            $validated['date_fin_contrat'] = null;
        }

        $agent->fill($validated);
        $agent->save();

        return redirect()->route('rh.agents.edit', $agent)->with('success', 'Agent mis à jour avec succès');
    }

    /**
     * Supprimer un agent
     */
    public function deleteAgent($agent)
    {
        // Suppression simple de l'utilisateur (pas de soft delete défini sur User)
        if ($user = User::find($agent)) {
            $name = $user->name;
            $user->delete();
            return redirect()->route('rh.agents.index')->with('success', 'Agent supprimé : '.$name);
        }

        return redirect()->route('rh.agents.index')->with('error', 'Agent introuvable');
    }

    /**
     * Exporter les agents
     */
    public function export()
    {
        $agents = User::orderBy('name')->get(['id','name','email','role']);

        $filename = 'agents_rh_export_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        $callback = function() use ($agents) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nom', 'Email', 'Poste']);
            foreach ($agents as $agent) {
                fputcsv($handle, [$agent->id, $agent->name, $agent->email, $agent->role]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Afficher les affectations
     */
    public function affectations()
    {
        $agents = User::with('service')->orderBy('name')->paginate(25);
        $total = $agents->total();
        return view('rh.affectations.index', compact('agents', 'total'));
    }

    /**
     * Créer une affectation
     */
    public function createAffectation()
    {
        $agents = User::whereIn('role', ['agent', 'moderator'])->orderBy('name')->get(['id', 'name', 'service_id']);
        $services = Service::actif()->orderBy('nom')->get();

        return view('rh.affectations.create', compact('agents', 'services'));
    }

    /**
     * Enregistrer une affectation
     */
    public function storeAffectation(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'date_affectation' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $ancienService = $user->service_id;

        // Mettre à jour le service de l'agent
        $user->service_id = $validated['service_id'];
        $user->save();

        $nouveauService = Service::find($validated['service_id']);
        $message = "Affectation de {$user->name} au service {$nouveauService->nom} effectuée avec succès.";

        return redirect()->route('rh.affectations.index')->with('success', $message);
    }

    /**
     * Afficher une affectation
     */
    public function showAffectation($affectation)
    {
        $affectationData = [
            'id' => $affectation,
            'agent_nom' => 'Test Agent',
            'vehicule_immat' => 'TEST-123',
            'date_debut' => now()->format('d/m/Y'),
            'statut' => 'Actif'
        ];
        return view('rh.affectations-show', compact('affectationData'));
    }

    /**
     * Modifier une affectation
     */
    public function editAffectation($affectation)
    {
        $affectationData = [
            'id' => $affectation,
            'agent_nom' => 'Test Agent',
            'vehicule_immat' => 'TEST-123'
        ];
        return view('rh.affectations-edit', compact('affectationData'));
    }

    /**
     * Mettre à jour une affectation
     */
    public function updateAffectation(Request $request, $affectation)
    {
        $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'date_affectation' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($affectation);
        $user->service_id = $request->service_id;
        // Add other fields if needed: date_affectation, etc.
        $user->save();

        return redirect()->route('rh.affectations.index')->with('success', 'Affectation mise à jour avec succès');
    }

    /**
     * Supprimer une affectation
     */
    public function deleteAffectation($affectation)
    {
        return redirect()->route('rh.affectations.index')->with('success', 'Affectation supprimée');
    }

    /**
     * Afficher les pointages du personnel
     */
    public function pointages(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        if (!Schema::hasTable('pointages')) {
            $pointages = collect();
        } else {
            $query = Pointage::with('user')
                ->whereDate('date_pointage', $date);
            
            if (Schema::hasColumn('pointages', 'heure_pointage')) {
                $query->orderBy('heure_pointage');
            } else {
                $query->orderBy('created_at');
            }

            $pointages = $query->get();
        }

        // Calculs avec les règles
        $pointages->transform(function ($pointage) {
            $pointage->delay_minutes = 0;
            $pointage->worked_hours = 0;

            $parseTime = function ($time) {
                if (empty($time)) {
                    return null;
                }
                try {
                    return Carbon::createFromFormat('H:i:s', $time);
                } catch (\Throwable $e) {
                    try {
                        return Carbon::createFromFormat('H:i', $time);
                    } catch (\Throwable $e2) {
                        return null;
                    }
                }
            };

            if ($pointage->heure_arrivee) {
                $arrivalTime = $parseTime($pointage->heure_arrivee);
                $delayLimit = Carbon::createFromTime(7, 30, 0); // 7h30

                if (!$arrivalTime) {
                    return $pointage;
                }

                if ($arrivalTime->greaterThan($delayLimit)) {
                    $pointage->delay_minutes = $arrivalTime->diffInMinutes($delayLimit);
                }

                if ($pointage->heure_depart) {
                    $departureTime = $parseTime($pointage->heure_depart);
                    if ($departureTime) {
                        $workedMinutes = $arrivalTime->diffInMinutes($departureTime, false);
                        if ($workedMinutes < 0) {
                            $workedMinutes = 0;
                        }

                        // Cap à 10h (600 minutes)
                        $pointage->worked_hours = round(min($workedMinutes / 60, 10), 2);
                    }
                }
            }

            return $pointage;
        });

        $totalAgents = User::count();
        $presents = $pointages->count();
        $retards = $pointages->where('delay_minutes', '>', 0)->count();
        $absents = max(0, $totalAgents - $presents);
        $tauxPresence = $totalAgents > 0 ? round(($presents / $totalAgents) * 100) : 0;

        $agents = User::orderBy('name')->get(['id', 'name']);

        return view('rh.pointages.index', compact(
            'pointages',
            'date',
            'presents',
            'retards',
            'absents',
            'tauxPresence',
            'agents'
        ));
    }

    /**
     * Créer un pointage
     */
    public function createPointage()
    {
        $agents = User::where('is_active', true)->get();
        return view('rh.pointages.create', compact('agents'));
    }

    /**
     * Enregistrer un pointage
     */
    public function storePointage(Request $request)
    {
        return redirect()->route('rh.pointages.index')->with('success', 'Pointage enregistré');
    }

    /**
     * Pointages en masse
     */
    public function massPointages()
    {
        $agents = User::where('is_active', true)->get();
        return view('rh.pointages.masse', compact('agents'));
    }

    /**
     * Enregistrer les pointages en masse
     */
    public function storeMassPointages(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'pointages' => 'required|array',
            'pointages.*.user_id' => 'required|exists:users,id',
            'pointages.*.heure_arrivee' => 'nullable|date_format:H:i',
            'pointages.*.heure_depart' => 'nullable|date_format:H:i',
            'pointages.*.statut' => 'required|in:present,absent,retard',
        ]);

        foreach ($validated['pointages'] as $pointageData) {
            if ($pointageData['heure_arrivee'] || $pointageData['heure_depart'] || $pointageData['statut'] !== 'absent') {
                Pointage::updateOrCreate([
                    'user_id' => $pointageData['user_id'],
                    'date_pointage' => $validated['date'],
                ], [
                    'heure_arrivee' => $pointageData['heure_arrivee'],
                    'heure_depart' => $pointageData['heure_depart'],
                    'statut' => $pointageData['statut'],
                    'validated_at' => now(),
                    'validated_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('rh.pointages.index')->with('success', 'Pointages en masse enregistrés avec succès');
    }

    /**
     * Historique des pointages
     */
    public function historyPointages(Request $request)
    {
        return view('rh.pointages.history', [
            'pointages' => collect(),
            'users' => collect()
        ]);
    }

    /**
     * Heures supplémentaires
     */
    public function heuresSup(Request $request)
    {
        $moisInput = $request->input('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        $heuresSup = HeureSup::with('user')->forMonth($period)->get();

        $totalMinutes      = (int) $heuresSup->sum('duree_minutes');
        $totalValideesMin  = (int) $heuresSup->where('statut', 'valide')->sum('duree_minutes');
        $totalAttenteMin   = (int) $heuresSup->where('statut', 'en_attente')->sum('duree_minutes');
        $totalMontant      = (int) $heuresSup->sum('montant');

        $heuresMois        = $this->formatMinutesForDisplay($totalMinutes);
        $heuresValidees    = $this->formatMinutesForDisplay($totalValideesMin);
        $heuresAttente     = $this->formatMinutesForDisplay($totalAttenteMin);
        $coutMensuel       = number_format($totalMontant, 0, ',', ' ') . ' FCFA';

        return view('rh.heures-sup', compact(
            'heuresSup',
            'heuresMois',
            'heuresValidees',
            'heuresAttente',
            'coutMensuel',
            'period'
        ));
    }

    protected function formatMinutesForDisplay(?int $minutes): string
    {
        if (!$minutes) {
            return '0h';
        }

        $heures = intdiv($minutes, 60);
        $mins   = $minutes % 60;

        return sprintf('%dh%02d', $heures, $mins);
    }

    /**
     * Créer une heure supplémentaire
     */
    public function createHeureSup()
    {
        return view('rh.heures-sup.create');
    }

    /**
     * Enregistrer une heure supplémentaire
     */
    public function storeHeureSup(Request $request)
    {
        return redirect()->route('rh.heures-sup.index')->with('success', 'Heure supplémentaire enregistrée');
    }

    /**
     * Afficher une heure supplémentaire
     */
    public function showHeureSup($heure)
    {
        $heureData = [
            'id' => $heure,
            'agent_nom' => 'Test Agent',
            'nombre_heures' => 2,
            'date' => now()->format('d/m/Y')
        ];
        return view('rh.heures-sup.show', compact('heureData'));
    }

    /**
     * Modifier une heure supplémentaire
     */
    public function editHeureSup($heure)
    {
        $heureData = [
            'id' => $heure,
            'agent_nom' => 'Test Agent'
        ];
        return view('rh.heures-sup.edit', compact('heureData'));
    }

    /**
     * Mettre à jour une heure supplémentaire
     */
    public function updateHeureSup(Request $request, $heure)
    {
        return redirect()->route('rh.heures-sup.index')->with('success', 'Heure supplémentaire mise à jour');
    }

    /**
     * Supprimer une heure supplémentaire
     */
    public function deleteHeureSup($heure)
    {
        return redirect()->route('rh.heures-sup.index')->with('success', 'Heure supplémentaire supprimée');
    }

    /**
     * Paie - Index
     */
    public function paieIndex()
    {
        $moisInput = request('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        $annee = (int) $period->format('Y');
        $mois = (int) $period->format('m');

        try {
            // Récupérer tous les agents/employés
            $agents = User::where('role', 'agent')->orWhere('role', 'employe')->get();

            $salaires = collect();

            foreach ($agents as $agent) {
                // Calculer les heures travaillées pour le mois
                $heuresTravaillees = $this->calculerHeuresTravaillees($agent->id, $annee, $mois);

                // Appliquer la logique de calcul
                $heuresPayees = min($heuresTravaillees, 8); // Max 8H par jour
                $salaireJournalier = $heuresPayees * 3000; // 3000 FCFA par heure

                // Calculer le salaire mensuel (environ 22 jours ouvrables)
                $salaireMensuel = $salaireJournalier * 22;

                $salaires->push((object)[
                    'user' => $agent,
                    'heures_travaillees' => $heuresTravaillees,
                    'heures_payees' => $heuresPayees,
                    'salaire_journalier' => $salaireJournalier,
                    'salaire_brut' => $salaireMensuel,
                    'cnps_salariale' => $salaireMensuel * 0.0565, // 5.65% CNPS
                    'autres_retenues' => 0,
                    'net_a_payer' => $salaireMensuel * (1 - 0.0565),
                    'statut' => 'non_paye'
                ]);
            }

            $totalBrut = $salaires->sum('salaire_brut');
            $totalCharges = $salaires->sum(fn($p) => $p->cnps_salariale + $p->autres_retenues);
            $totalNet = $salaires->sum('net_a_payer');
            $bulletins = $salaires->count();
            $bulletinsPayes = $salaires->where('statut', 'paye')->count();
        } catch (\Exception $e) {
            $salaires = collect();
            $totalBrut = 0;
            $totalCharges = 0;
            $totalNet = 0;
            $bulletins = 0;
            $bulletinsPayes = 0;
        }

        return view('rh.paie.index', compact('salaires','period','totalBrut','totalCharges','totalNet','bulletins','bulletinsPayes'));
    }

    /**
     * Calculer les heures travaillées pour un agent pendant un mois
     * en tenant compte de la pause de 12H-14H non comptée
     */
    private function calculerHeuresTravaillees($agentId, $annee, $mois)
    {
        $heuresTotales = 0;

        // Récupérer les pointages du mois
        $pointages = Pointage::where('user_id', $agentId)
            ->whereYear('date_pointage', $annee)
            ->whereMonth('date_pointage', $mois)
            ->orderBy('date_pointage')
            ->get();

        foreach ($pointages as $pointage) {
            if ($pointage->heure_arrivee && $pointage->heure_depart) {
                $arrivee = Carbon::parse($pointage->heure_arrivee);
                $depart = Carbon::parse($pointage->heure_depart);

                // Calculer les heures travaillées dans la journée
                $heuresJournee = $depart->diffInHours($arrivee);

                // Soustraire la pause de 12H-14H si elle est incluse
                $pauseDejeuner = $this->calculerPauseDejeuner($arrivee, $depart);
                $heuresEffectives = $heuresJournee - $pauseDejeuner;

                $heuresTotales += max(0, $heuresEffectives);
            }
        }

        return $heuresTotales;
    }

    /**
     * Calculer la durée de la pause dejeuner (12H-14H)
     */
    private function calculerPauseDejeuner($arrivee, $depart)
    {
        $pauseDebut = Carbon::parse($arrivee->format('Y-m-d') . ' 12:00:00');
        $pauseFin = Carbon::parse($arrivee->format('Y-m-d') . ' 14:00:00');

        // Si l'employé travaille pendant la période de pause
        if ($arrivee < $pauseFin && $depart > $pauseDebut) {
            $debutPause = max($arrivee, $pauseDebut);
            $finPause = min($depart, $pauseFin);
            return $finPause->diffInHours($debutPause);
        }

        return 0;
    }

    /**
     * Paie
     */
    public function paie()
    {
        $moisInput = request('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        $annee = (int) $period->format('Y');
        $mois = (int) $period->format('m');

        try {
            $salaires = Paie::with('user')
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->get()
                ->sortBy(function ($paie) {
                    return $paie->user->name ?? '';
                })->values();

            $totalBrut = $salaires->sum('brut');
            $totalCharges = $salaires->sum(fn($p) => $p->cnps_salariale + $p->autres_retenues);
            $totalNet = $salaires->sum('net_a_payer');
            $bulletins = $salaires->count();
            $bulletinsPayes = $salaires->where('statut', 'paye')->count();
        } catch (\Exception $e) {
            $salaires = collect();
            $totalBrut = 0;
            $totalCharges = 0;
            $totalNet = 0;
            $bulletins = 0;
            $bulletinsPayes = 0;
        }

        return view('rh.paie.index', compact('salaires','period','totalBrut','totalCharges','totalNet','bulletins','bulletinsPayes'));
    }

    /**
     * Exporter les fiches de paie du mois en cours sous forme de PDF A4
     * (1 page par agent, avec CNPS provisoire et net à payer)
     */
    public function exportPaie()
    {
        $mois = request('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $mois)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        // Taux provisoires à valider par un expert CI (rendables configurables plus tard)
        $cnpsTauxSalarial = 0.063; // 6,3 %
        $cnpsTauxPatronal = 0.077; // 7,7 %

        // Seuls les agents et modérateurs sont pris en compte pour la paie
        $users = User::whereIn('role', ['agent', 'moderator'])->orderBy('name')->get();

        $bulletins = [];
        foreach ($users as $user) {
            // Salaire de base depuis la colonne users.salaire si disponible, sinon 0
            $salaireBase = 0;
            if (Schema::hasColumn('users', 'salaire')) {
                $salaireBase = (float) ($user->salaire ?? 0);
            }

            $heuresSup = 0.0;
            $primes = 0.0;

            $brut = $salaireBase + $heuresSup + $primes;

            // Assiette CNPS (sans plafond spécifique pour l'instant)
            $assietteCnps = $brut;

            $cnpsSalariale = round($assietteCnps * $cnpsTauxSalarial, 0);
            $cnpsPatronale = round($assietteCnps * $cnpsTauxPatronal, 0);
            $autresRetenues = 0; // IRPP, AMU, etc. à ajouter plus tard

            $netAPayer = $brut - $cnpsSalariale - $autresRetenues;

            $bulletins[] = [
                'agent'           => $user,
                'mois_label'      => $period->translatedFormat('F Y'),
                'mois_numeric'    => $period->format('m'),
                'annee'           => $period->format('Y'),
                'salaire_base'    => $salaireBase,
                'heures_sup'      => $heuresSup,
                'primes'          => $primes,
                'brut'            => $brut,
                'cnps_salariale'  => $cnpsSalariale,
                'cnps_patronale'  => $cnpsPatronale,
                'autres_retenues' => $autresRetenues,
                'net_a_payer'     => $netAPayer,
            ];
        }

        $settings = EntrepriseSettings::getActive();
        $entreprise = [
            'nom' => $settings->nom_entreprise ?? config('app.name', 'KENAM SERVICES'),
            'adresse' => $settings->adresse ?? '',
            'telephone' => $settings->telephone ?? '',
            'email' => $settings->email_contact ?? '',
            'website' => $settings->site_web ?? '',
            'cnps' => $settings->cnss ?? '',
            'logo_path' => !empty($settings?->logo_path) ? public_path('storage/' . $settings->logo_path) : null,
        ];

        $pdf = Pdf::loadView('rh.paie-bulletin', [
            'entreprise' => $entreprise,
            'period' => $period,
            'bulletins' => $bulletins,
        ])->setPaper('a4', 'portrait');

        $filename = 'bulletins_paie_'.$period->format('Y_m').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Exporter un bulletin de paie individuel en PDF A4
     */
    public function pdfPaie(Paie $paie)
    {
        $period = Carbon::createFromDate($paie->annee, $paie->mois, 1)->startOfMonth();

        $settings = EntrepriseSettings::getActive();
        $entreprise = [
            'nom' => $settings->nom_entreprise ?? config('app.name', 'KENAM SERVICES'),
            'adresse' => $settings->adresse ?? '',
            'telephone' => $settings->telephone ?? '',
            'email' => $settings->email_contact ?? '',
            'website' => $settings->site_web ?? '',
            'cnps' => $settings->cnss ?? '',
            'logo_path' => !empty($settings?->logo_path) ? public_path('storage/' . $settings->logo_path) : null,
        ];

        $bulletins = [[
            'agent'           => $paie->user,
            'mois_label'      => $period->translatedFormat('F Y'),
            'mois_numeric'    => $paie->mois,
            'annee'           => $paie->annee,
            'salaire_base'    => $paie->salaire_base,
            'heures_sup'      => $paie->heures_sup ?? 0,
            'primes'          => $paie->primes ?? 0,
            'brut'            => $paie->brut,
            'cnps_salariale'  => $paie->cnps_salariale,
            'cnps_patronale'  => $paie->cnps_patronale,
            'autres_retenues' => $paie->autres_retenues,
            'net_a_payer'     => $paie->net_a_payer,
        ]];

        $pdf = Pdf::loadView('rh.paie-bulletin', [
            'entreprise' => $entreprise,
            'period' => $period,
            'bulletins' => $bulletins,
        ])->setPaper('a4', 'portrait');

        $filename = 'bulletin_paie_'.$paie->user->id.'_'.$paie->annee.'_'.$paie->mois.'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Générer les fiches de paie pour tous les agents pour un mois donné
     */
    public function generateAllPaie(Request $request)
    {
        $moisInput = $request->input('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        $annee = (int) $period->format('Y');
        $mois  = (int) $period->format('m');

        // Taux CNPS provisoires (à valider et rendre configurables)
        $cnpsTauxSalarial = 0.063;
        $cnpsTauxPatronal = 0.077;

        // Seuls les agents et modérateurs sont pris en compte pour la paie
        $users = User::whereIn('role', ['agent', 'moderator'])->orderBy('name')->get();

        $crees = 0;
        $misAJour = 0;

        foreach ($users as $user) {
            // Salaire de base depuis users.salaire si la colonne existe
            $salaireBase = 0;
            if (Schema::hasColumn('users', 'salaire')) {
                $salaireBase = (float) ($user->salaire ?? 0);
            }

            $heuresSup = 0.0;
            $primes    = 0.0;

            $brut = $salaireBase + $heuresSup + $primes;

            $assietteCnps   = $brut;
            $cnpsSalariale  = round($assietteCnps * $cnpsTauxSalarial, 0);
            $cnpsPatronale  = round($assietteCnps * $cnpsTauxPatronal, 0);
            $autresRetenues = 0;

            $netAPayer = $brut - $cnpsSalariale - $autresRetenues;

            $data = [
                'salaire_base'   => $salaireBase,
                'heures_sup'     => $heuresSup,
                'primes'         => $primes,
                'brut'           => $brut,
                'cnps_salariale' => $cnpsSalariale,
                'cnps_patronale' => $cnpsPatronale,
                'autres_retenues'=> $autresRetenues,
                'net_a_payer'    => $netAPayer,
                'statut'         => 'genere',
            ];

            $paie = Paie::where('user_id', $user->id)
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->first();

            if ($paie) {
                $paie->fill($data)->save();
                $misAJour++;
            } else {
                try {
                    Paie::create(array_merge($data, [
                        'user_id' => $user->id,
                        'annee'   => $annee,
                        'mois'    => $mois,
                    ]));
                    $crees++;
                } catch (\Exception $e) {
                    // Ignorer si la table paies n'existe pas
                    continue;
                }
            }
        }

        $message = "Bulletins générés pour le mois ".$period->translatedFormat('F Y').". Créés: $crees, mis à jour: $misAJour.";

        return redirect()->route('rh.paie.index', ['mois' => $period->format('Y-m')])
            ->with('success', $message);
    }

    /**
     * Créer une fiche de paie
     */
    public function createPaie()
    {
        $users = User::orderBy('name')->get(['id','name','email','role']);
        $period = now();
        return view('rh.paie-create', compact('users','period'));
    }

    /**
     * Stocker une fiche de paie
     */
    public function storePaie(Request $request)
    {
        $validated = $request->validate([
            'agent_id'     => 'required|integer|exists:users,id',
            'mois'         => 'required|integer|min:1|max:12',
            'annee'        => 'required|integer|min:2000',
            'salaire_base' => 'required|numeric|min:0',
            'heures_sup'   => 'nullable|numeric|min:0',
            'primes'       => 'nullable|numeric|min:0',
            'commentaires' => 'nullable|string',
        ]);

        $heuresSup = $validated['heures_sup'] ?? 0;
        $primes = $validated['primes'] ?? 0;

        $brut = $validated['salaire_base'] + $heuresSup + $primes;

        // Taux CNPS provisoires (à valider et rendre configurables)
        $cnpsTauxSalarial = 0.063;
        $cnpsTauxPatronal = 0.077;

        $assietteCnps = $brut;
        $cnpsSalariale = round($assietteCnps * $cnpsTauxSalarial, 0);
        $cnpsPatronale = round($assietteCnps * $cnpsTauxPatronal, 0);
        $autresRetenues = 0; // IRPP, AMU, etc., à ajouter plus tard

        $netAPayer = $brut - $cnpsSalariale - $autresRetenues;

        Paie::create([
            'user_id'        => $validated['agent_id'],
            'mois'           => (int) $validated['mois'],
            'annee'          => (int) $validated['annee'],
            'salaire_base'   => $validated['salaire_base'],
            'heures_sup'     => $heuresSup,
            'primes'         => $primes,
            'brut'           => $brut,
            'cnps_salariale' => $cnpsSalariale,
            'cnps_patronale' => $cnpsPatronale,
            'autres_retenues'=> $autresRetenues,
            'net_a_payer'    => $netAPayer,
            'statut'         => 'genere',
            'commentaires'   => $validated['commentaires'] ?? null,
        ]);

        $moisRedirect = sprintf('%04d-%02d', $validated['annee'], $validated['mois']);

        return redirect()
            ->route('rh.paie.index', ['mois' => $moisRedirect])
            ->with('success', 'Fiche de paie créée et enregistrée.');
    }

    /**
     * Mettre à jour une fiche de paie manuellement
     */
    public function updatePaie(Request $request, Paie $paie)
    {
        $request->validate([
            'salaire_base' => 'required|numeric|min:0',
            'brut' => 'required|numeric|min:0',
            'cnps_salariale' => 'required|numeric|min:0',
            'autres_retenues' => 'required|numeric|min:0',
            'net_a_payer' => 'required|numeric|min:0',
        ]);

        $paie->update($request->only([
            'salaire_base',
            'brut',
            'cnps_salariale',
            'autres_retenues',
            'net_a_payer'
        ]));

        return back()->with('success', 'Fiche de paie mise à jour avec succès');
    }

    /**
     * Afficher le formulaire d'édition d'une fiche de paie
     */
    public function editPaie(Paie $paie)
    {
        return view('rh.paie.edit', compact('paie'));
    }

    /**
     * Afficher les congés/absences
     */
    public function conges(Request $request)
    {
        try {
            // Récupération des données depuis la base de données
            $conges = Conge::with(['user', 'valideur'])
                ->orderBy('date_demande', 'desc')
                ->paginate(20);

            // Calculer les statistiques
            $totalConges = $conges->total();
            $congesEnCours = $conges->where('statut', 'en_cours')->count();
            $congesEnAttente = $conges->where('statut', 'en_attente')->count();
            $congesApprouves = $conges->where('statut', 'approuve')->count();

            return view('rh.conges', compact('conges', 'totalConges', 'congesEnCours', 'congesEnAttente', 'congesApprouves'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des valeurs par défaut
            $conges = collect([]);
            $totalConges = 0;
            $congesEnCours = 0;
            $congesEnAttente = 0;
            $congesApprouves = 0;

            return view('rh.conges', compact('conges', 'totalConges', 'congesEnCours', 'congesEnAttente', 'congesApprouves'));
        }
    }

    /**
     * Créer un congé
     */
    public function createConge()
    {
        $agents = User::orderBy('name')->get(['id', 'name']);
        return view('rh.conges.create', compact('agents'));
    }

    /**
     * Enregistrer un congé
     */
    public function storeConge(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'type_conge' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        // Ici, on pourrait enregistrer dans une table conges
        // Pour l'instant, juste une redirection avec succès
        return redirect()->route('rh.conges.index')->with('success', 'Demande de congé enregistrée avec succès');
    }

    /**
     * Afficher un congé
     */
    public function showConge($conge)
    {
        // Simulation de données pour le congé
        $congeData = [
            'id' => $conge,
            'agent' => ['name' => 'Agent Test'],
            'type_conge' => 'Annuel',
            'date_debut' => '2024-01-01',
            'date_fin' => '2024-01-05',
            'motif' => 'Vacances',
            'statut' => 'En attente'
        ];

        return view('rh.conges.show', compact('congeData'));
    }

    /**
     * Modifier un congé
     */
    public function editConge($conge)
    {
        $agents = User::orderBy('name')->get(['id', 'name']);
        $congeData = [
            'id' => $conge,
            'agent_id' => 1,
            'type_conge' => 'Annuel',
            'date_debut' => '2024-01-01',
            'date_fin' => '2024-01-05',
            'motif' => 'Vacances'
        ];

        return view('rh.conges.edit', compact('congeData', 'agents'));
    }

    /**
     * Mettre à jour un congé
     */
    public function updateConge(Request $request, $conge)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'type_conge' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        return redirect()->route('rh.conges.index')->with('success', 'Congé mis à jour avec succès');
    }

    /**
     * Supprimer un congé
     */
    public function deleteConge($conge)
    {
        return redirect()->route('rh.conges.index')->with('success', 'Congé supprimé avec succès');
    }

    /**
     * Afficher une fiche de paie
     */
    public function showPaie($paie)
    {
        $paieData = [
            'id' => $paie,
            'agent' => ['name' => 'Agent Test'],
            'mois' => 1,
            'annee' => 2024,
            'salaire_base' => 150000,
            'brut' => 165000,
            'net_a_payer' => 145000
        ];

        return view('rh.paie.show', compact('paieData'));
    }

    /**
     * Formulaire de recrutement (hiring)
     */
    public function recrutement()
    {
        $services = Service::orderBy('nom')->get();
        return view('rh.recrutement', compact('services'));
    }

    /**
     * Enregistrer un nouveau recrutement
     */
    public function storeRecrutement(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'service_id' => 'required|exists:services,id',
            'role' => 'required|string',

            // Ivorian HR Fields
            'sexe' => 'required|string|in:M,F',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string',
            'nationalite' => 'required|string',
            'situation_matrimoniale' => 'required|string',
            'nombre_enfants' => 'required|integer|min:0',
            'adresse_postale' => 'nullable|string',
            'n_cnps' => 'nullable|string',
            'n_cmu' => 'nullable|string',

            // Contrat
            'contrat' => 'required|string',
            'date_embauche' => 'required|date',
            'date_fin_contrat' => 'nullable|date|after:date_embauche',
            'periode_essai' => 'nullable|integer',
            'categorie_professionnelle' => 'nullable|string',

            // Salaire
            'salaire_base' => 'required|numeric|min:0',
            'sursalaire' => 'nullable|numeric|min:0',
            'indemnite_transport' => 'nullable|numeric|min:0',
            'indemnite_logement' => 'nullable|numeric|min:0',
            'autres_primes' => 'nullable|numeric|min:0',
        ]);

        $validated['name'] = $validated['nom'] . ' ' . $validated['prenom'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['salaire'] = $validated['salaire_base'] + ($validated['sursalaire'] ?? 0) + ($validated['indemnite_transport'] ?? 0) + ($validated['indemnite_logement'] ?? 0) + ($validated['autres_primes'] ?? 0);
        $validated['is_active'] = true;

        $user = User::create($validated);

        return redirect()->route('rh.contrats.index')->with('success', 'Recrutement réussi pour ' . $user->name);
    }

    /**
     * Afficher un contrat
     */
    public function showContrat($user)
    {
        $agent = User::with('service')->findOrFail($user);
        return view('rh.contrats-show', compact('agent'));
    }

    /**
     * Modifier un contrat
     */
    public function editContrat($user)
    {
        $agent = User::findOrFail($user);
        $services = Service::orderBy('nom')->get();
        return view('rh.contrats-edit', compact('agent', 'services'));
    }

    /**
     * Mettre à jour un contrat
     */
    public function updateContrat(Request $request, $user)
    {
        $agent = User::findOrFail($user);

        $validated = $request->validate([
            'contrat' => 'required|string',
            'date_embauche' => 'required|date',
            'date_fin_contrat' => 'nullable|date',
            'salaire_base' => 'required|numeric',
            'sursalaire' => 'nullable|numeric',
            'indemnite_transport' => 'nullable|numeric',
            'indemnite_logement' => 'nullable|numeric',
            'autres_primes' => 'nullable|numeric',
            'categorie_professionnelle' => 'nullable|string',
        ]);

        $agent->update($validated);
        $agent->salaire = $validated['salaire_base'] + ($validated['sursalaire'] ?? 0) + ($validated['indemnite_transport'] ?? 0) + ($validated['indemnite_logement'] ?? 0) + ($validated['autres_primes'] ?? 0);
        $agent->save();

        return redirect()->route('rh.contrats.show', $agent->id)->with('success', 'Contrat mis à jour');
    }

    /**
     * Générer le contrat au format PDF
     */
    public function contratPdf($user)
    {
        $agent = User::with('service')->findOrFail($user);
        $pdf = PDF::loadView('rh.contrats.pdf', compact('agent'));
        return $pdf->download('Contrat_'.$agent->name.'.pdf');
    }

    /**
     * Supprimer un contrat (alias suppression user / désactivation)
     */
    public function deleteContrat($user)
    {
        $agent = User::findOrFail($user);
        $agent->is_active = false;
        $agent->save();
        return redirect()->route('rh.contrats.index')->with('success', 'Agent désactivé et contrat archivé');
    }

    /**
     * Supprimer une fiche de paie
     */
    public function deletePaie($paie)
    {
        $p = Paie::findOrFail($paie);
        $p->delete();
        return redirect()->route('rh.paie.index')->with('success', 'Fiche de paie supprimée avec succès');
    }

    /**
     * Exporter les contrats
     */
    public function exportContrats()
    {
        $contrats = User::orderBy('name')
            ->select('id', 'name', 'email', 'phone', 'role', 'created_at')
            ->get();

        $filename = 'contrats_export_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        $callback = function() use ($contrats) {
            $handle = fopen('php://output', 'w');

            // BOM pour UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Nom', 'Email', 'Téléphone', 'Poste', 'Date d\'embauche'], ';');
            foreach ($contrats as $contrat) {
                fputcsv($handle, [
                    $contrat->id,
                    $contrat->name,
                    $contrat->email,
                    $contrat->phone ?? '',
                    $contrat->role,
                    $contrat->created_at ? $contrat->created_at->format('Y-m-d') : ''
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Créer une nouvelle fiche de paie
     */
    public function paieCreate()
    {
        $agents = User::orderBy('name')->get();
        return view('rh.paie-create', compact('agents'));
    }

    /**
     * Stocker une nouvelle fiche de paie
     */
    public function paieStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'mois' => 'required|date_format:Y-m',
            'salaire_brut' => 'required|numeric|min:0',
        ]);

        try {
            $paie = Paie::create($validated);
            return redirect()->route('rh.paie.show', $paie->id)
                ->with('success', 'Fiche de paie créée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher une fiche de paie
     */
    public function paieShow($id)
    {
        try {
            $paie = Paie::findOrFail($id);
            $paie->load('user');
            return view('rh.paie-show', compact('paie'));
        } catch (\Exception $e) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Fiche de paie non trouvée');
        }
    }

    /**
     * Éditer une fiche de paie
     */
    public function paieEdit($id)
    {
        try {
            $paie = Paie::findOrFail($id);
            $agents = User::orderBy('name')->get();
            return view('rh.paie-edit', compact('paie', 'agents'));
        } catch (\Exception $e) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Fiche de paie non trouvée');
        }
    }

    /**
     * Mettre à jour une fiche de paie
     */
    public function paieUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'mois' => 'required|date_format:Y-m',
            'salaire_brut' => 'required|numeric|min:0',
        ]);

        try {
            $paie = Paie::findOrFail($id);
            $paie->update($validated);
            return redirect()->route('rh.paie.show', $paie->id)
                ->with('success', 'Fiche de paie mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une fiche de paie
     */
    public function paieDestroy($id)
    {
        try {
            $paie = Paie::findOrFail($id);
            $paie->delete();
            return redirect()->route('rh.paie.index')
                ->with('success', 'Fiche de paie supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression');
        }
    }

    /**
     * Générer un PDF de la fiche de paie
     */
    public function paiePdf($id)
    {
        try {
            $paie = Paie::findOrFail($id);
            $paie->load('user');

            // Pour l'instant, redirection vers la page de visualisation
            return redirect()->route('rh.paie.show', $id);
        } catch (\Exception $e) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Fiche de paie non trouvée');
        }
    }

    /**
     * Exporter les fiches de paie
     */
    public function paieExport(Request $request)
    {
        $mois = $request->get('mois', now()->format('Y-m'));

        try {
            $paies = Paie::when($mois, fn($q) => $q->where('mois', 'like', $mois . '%'))
                ->with('user')
                ->get();

            $filename = 'paies_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ];

            $callback = function() use ($paies) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($handle, ['ID', 'Agent', 'Mois', 'Salaire Brut', 'Retenues', 'Net'], ';');
                foreach ($paies as $paie) {
                    fputcsv($handle, [
                        $paie->id,
                        $paie->user->name ?? '',
                        $paie->mois ?? '',
                        $paie->salaire_brut ?? 0,
                        $paie->retenues ?? 0,
                        ($paie->salaire_brut ?? 0) - ($paie->retenues ?? 0)
                    ], ';');
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Générer toutes les fiches de paie pour un mois
     */
    public function paieGenerateAll(Request $request)
    {
        $mois = $request->get('mois', now()->format('Y-m'));

        try {
            $agents = User::where('is_active', true)->get();
            $count = 0;

            foreach ($agents as $agent) {
                $exists = Paie::where('user_id', $agent->id)
                    ->where('mois', 'like', $mois . '%')
                    ->exists();

                if (!$exists) {
                    Paie::create([
                        'user_id' => $agent->id,
                        'mois' => $mois . '-01',
                        'salaire_brut' => 0,
                    ]);
                    $count++;
                }
            }

            return redirect()->route('rh.paie.index', ['mois' => $mois])
                ->with('success', "Génération terminée: $count nouvelles fiches créées");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la génération: ' . $e->getMessage());
        }
    }
}
