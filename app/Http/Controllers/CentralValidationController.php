<?php

namespace App\Http\Controllers;

use App\Models\Validation;
use App\Models\ValidationLog;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CentralValidationController extends Controller
{
    /**
     * Affiche la liste des validations en attente
     *
     * @return \Illuminate\View\View
     */
    public function pending()
    {
        try {
            $user = auth()->user();

            // Pour un superadmin, afficher toutes les opérations en attente de validation
            if ($user && $user->role === 'superadmin') {
                $operations = \App\Models\Operation::with(['initiateur', 'validateur', 'services'])
                    ->whereIn('statut_courant', ['en_attente', 'pending_validation', 'en_cours'])
                    ->latest()
                    ->paginate(15);

                \Illuminate\Support\Facades\Log::info('Opérations en attente pour superadmin: ' . $operations->count());

                return view('validations.pending', [
                    'validations' => collect([]), // Pas de validations dans la table Validation
                    'operations' => $operations,
                    'operationalServices' => collect([]),
                    'errors' => new \Illuminate\Support\MessageBag()
                ]);
            }

            // Pour les autres utilisateurs, chercher dans la table Validation
            $validations = \App\Models\Validation::where('statut', 'en_attente')
                ->with(['initiateur', 'validateur', 'logs.user', 'operation'])
                ->latest()
                ->paginate(15);

            // Ajout d'un log pour débogage
            \Illuminate\Support\Facades\Log::info('Validations en attente chargées : ' . $validations->count());

            return view('validations.pending', [
                'validations' => $validations,
                'operations' => collect([]),
                'operationalServices' => collect([]),
                'errors' => new \Illuminate\Support\MessageBag()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur dans CentralValidationController@pending: ' . $e->getMessage());

            return view('validations.pending', [
                'validations' => collect([]),
                'operations' => collect([]),
                'operationalServices' => collect([]),
                'errors' => new \Illuminate\Support\MessageBag()
            ]);
        }
    }

    /**
     * Affiche la liste des validations approuvées
     *
     * @return \Illuminate\View\View
     */
    public function approved()
    {
        try {
            $validations = \App\Models\Validation::where('statut', 'approuve')
                ->with(['initiateur', 'validateur', 'logs.user', 'operation'])
                ->latest()
                ->paginate(15);

            \Illuminate\Support\Facades\Log::info('Validations approuvées chargées : ' . $validations->count());

            return view('validations.approved', [
                'validations' => $validations,
                'operationalServices' => collect([]),
                'errors' => $errors ?? new \Illuminate\Support\MessageBag(),
                'operations' => $validations
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur dans CentralValidationController@approved: ' . $e->getMessage());
            return view('validations.approved', [
                'validations' => collect([]),
                'operationalServices' => collect([]),
                'errors' => new \Illuminate\Support\MessageBag(),
                'operations' => collect([])
            ]);
        }
    }

    /**
     * Affiche la liste des validations rejetées
     *
     * @return \Illuminate\View\View
     */
    public function rejected()
    {
        try {
            $validations = \App\Models\Validation::where('statut', 'rejete')
                ->with(['initiateur', 'validateur', 'logs.user', 'operation'])
                ->latest()
                ->paginate(15);

            \Illuminate\Support\Facades\Log::info('Validations rejetées chargées : ' . $validations->count());

            return view('validations.rejected', [
                'validations' => $validations,
                'operationalServices' => collect([]),
                'errors' => $errors ?? new \Illuminate\Support\MessageBag(),
                'operations' => $validations
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur dans CentralValidationController@rejected: ' . $e->getMessage());
            return view('validations.rejected', [
                'validations' => collect([]),
                'operationalServices' => collect([]),
                'errors' => new \Illuminate\Support\MessageBag(),
                'operations' => collect([])
            ]);
        }
    }


    /**
     * Affiche l'historique de toutes les validations
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function history(Request $request)
    {
        try {
            $query = \App\Models\Validation::with(['initiateur', 'validateur', 'logs.user', 'operation'])
                ->latest();

            // Filtres
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('titre', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $validations = $query->paginate(20);

            \Illuminate\Support\Facades\Log::info('Historique des validations chargé : ' . $validations->count());

            return view('validations.history', [
                'validations' => $validations,
                'operationalServices' => collect([]),
                'errors' => $errors ?? new \Illuminate\Support\MessageBag(),
                'operations' => $validations
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur dans CentralValidationController@history: ' . $e->getMessage());
            return view('validations.history', [
                'validations' => collect([]),
                'operationalServices' => collect([]),
                'errors' => new \Illuminate\Support\MessageBag(),
                'operations' => collect([])
            ]);
        }
    }

    /**
     * Affiche la liste de toutes les validations
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Récupérer toutes les validations avec les relations nécessaires
        $query = Validation::with(['initiateur', 'validateur', 'logs.user', 'operation'])
            ->latest();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('module')) {
            $query->where('module_source', $request->module);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Par défaut pour les utilisateurs normaux : n'afficher que leurs validations (scope=mine)
        // Le superadmin peut tout voir (scope=all)
        $scope = $request->input('scope');
        $isSuperadmin = $user && method_exists($user, 'hasRole') && $user->hasRole('superadmin');

        if (!$isSuperadmin && !$scope) {
            $scope = 'mine';
        }

        if ($scope === 'mine' && auth()->check()) {
            // Filtrer par utilisateur/service uniquement si scope=mine est explicitement demandé
            if ($user->service) {
                $query->where(function($q) use ($user) {
                    $q->where('validateur_id', $user->id)
                      ->orWhere('initiateur_id', $user->id)
                      ->orWhere(function($subQ) use ($user) {
                          $subQ->whereNull('validateur_id')
                               ->whereHas('initiateur', function($userQ) use ($user) {
                                   $userQ->where('service', $user->service);
                               });
                      });
                });
            } else {
                $query->where(function($q) use ($user) {
                    $q->where('validateur_id', $user->id)
                      ->orWhere('initiateur_id', $user->id)
                      ->orWhereNull('validateur_id');
                });
            }
        }

        // Séparer les validations en attente et l'historique
        $validations = $query->paginate(15);

        // Statistiques détaillées
        $statistics = WorkflowService::getStatistics();

        // Statistiques par statut
        $statsByStatus = Validation::selectRaw('statut, COUNT(*) as count')
            ->groupBy('statut')
            ->get()
            ->pluck('count', 'statut');

        // Répartition par module
        $statsByModule = Validation::selectRaw('module_source, COUNT(*) as count')
            ->groupBy('module_source')
            ->get()
            ->pluck('count', 'module_source');

        // Fallback: si aucun résultat mais des validations existent, relâcher les filtres utilisateur
        if ($validations->isEmpty() && Validation::count() > 0 && $request->input('scope') === 'mine') {
            $query = Validation::with(['initiateur', 'validateur', 'logs', 'operation'])->latest();
            $validations = $query->paginate(15);
        }

        return view('validations.index', compact(
            'validations',
            'statistics',
            'statsByStatus',
            'statsByModule'
        ));
    }

    public function show(Validation $validation): View
    {
        $validation->load(['initiateur', 'validateur', 'logs.user']);

        return view('validations.show', compact('validation'));
    }

    public function process(Request $request, Validation $validation): RedirectResponse
    {
        $request->validate([
            'decision' => 'required|in:approve,reject,correct',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        if ($validation->statut !== 'en_attente' && $validation->statut !== 'en_cours') {
            return back()->with('error', 'Cette validation ne peut plus être traitée.');
        }

        // Empêcher un utilisateur de valider sa propre demande
        if ($validation->initiateur_id && $validation->initiateur_id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas valider votre propre requête.');
        }

        if ($validation->validateur_id && $validation->validateur_id !== auth()->id()) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à valider cette demande.');
        }

        WorkflowService::validate($validation->id, $request->decision, $request->commentaire);

        $message = match($request->decision) {
            'approve' => 'La demande a été approuvée avec succès.',
            'reject' => 'La demande a été rejetée.',
            'correct' => 'La demande a été retournée pour correction.',
            default => 'La demande a été traitée.'
        };

        return redirect()->route('operations.valider')
            ->with('success', $message);
    }

    public function dashboard(): View
    {
        $user = auth()->user();

        // Mes validations à traiter
        $myValidations = Validation::with(['initiateur'])
            ->where(function($q) use ($user) {
                $q->where('validateur_id', $user->id)
                  ->orWhereNull('validateur_id');
            })
            ->whereIn('statut', ['en_attente', 'en_cours'])
            ->latest()
            ->limit(5)
            ->get();

        // Mes demandes soumises
        $myRequests = Validation::with(['validateur'])
            ->where('initiateur_id', $user->id)
            ->whereIn('statut', ['en_attente', 'en_cours'])
            ->latest()
            ->limit(5)
            ->get();

        // Activité récente (logs)
        $recentLogs = ValidationLog::with(['validation', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        // Statistiques globales
        $statistics = WorkflowService::getStatistics();

        // Données pour graphiques (6 derniers mois)
        $months = collect(range(5, 0))->map(function($i) {
            return now()->subMonths($i)->format('M');
        })->toArray();

        $perMonthPending = [];
        $perMonthApproved = [];
        $perMonthRejected = [];
        $avgProcTime = [];

        foreach(range(5, 0) as $i) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();

            $perMonthPending[] = Validation::where('statut', 'en_attente')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $perMonthApproved[] = Validation::where('statut', 'valide')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $perMonthRejected[] = Validation::where('statut', 'rejete')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // Temps moyen de traitement pour ce mois
            $validations = Validation::whereNotNull('date_validation')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            if ($validations->count() > 0) {
                $totalHours = $validations->sum(function($v) {
                    return $v->created_at->diffInHours($v->date_validation);
                });
                $avgProcTime[] = round($totalHours / $validations->count(), 1);
            } else {
                $avgProcTime[] = 0;
            }
        }

        // Répartition par statut (pour pie chart)
        $statusLabels = ['En attente', 'En cours', 'Validées', 'Rejetées'];
        $statusCounts = [
            Validation::where('statut', 'en_attente')->count(),
            Validation::where('statut', 'en_cours')->count(),
            Validation::where('statut', 'valide')->count(),
            Validation::where('statut', 'rejete')->count(),
        ];

        return view('validations.dashboard', compact(
            'myValidations',
            'myRequests',
            'recentLogs',
            'statistics',
            'months',
            'perMonthPending',
            'perMonthApproved',
            'perMonthRejected',
            'avgProcTime',
            'statusLabels',
            'statusCounts'
        ));
    }
}
