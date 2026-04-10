<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileAppController extends Controller
{
    /**
     * Page d'accueil de l'application PWA
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Rediriger selon le rôle pour une expérience PWA optimisée
        switch ($user->role) {
            case 'superadmin':
            case 'admin':
                return redirect()->route('operations.dashboard');
            case 'moderator':
            case 'moderateur':
                return redirect()->route('operations.dashboard');
            case 'agent':
                return redirect()->route('operations.index');
            default:
                return redirect()->route('operations.index');
        }
    }

    /**
     * Dashboard PWA optimisé pour mobile
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Stats pour le dashboard mobile
        $stats = [
            'en_attente' => \App\Models\Operation::where('statut_courant', 'en_attente_de_validation')->count(),
            'en_cours' => \App\Models\Operation::where('statut_courant', 'en_cours')->count(),
            'approuvees' => \App\Models\Operation::where('statut_courant', 'Approuvé_en_attente_paiement')->count(),
            'payees' => \App\Models\Operation::where('statut_courant', 'payee')->count(),
        ];

        // Opérations récentes pour mobile
        $recentOperations = \App\Models\Operation::with(['typeOperation', 'client'])
            ->when(!in_array($user->role, ['admin', 'superadmin']), function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('demandeur_email', $user->email);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('mobile.dashboard', compact('stats', 'recentOperations', 'user'));
    }

    /**
     * API endpoint pour les données PWA
     */
    public function apiData(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = [
            'user' => [
                'name' => $user->name,
                'role' => $user->role,
                'phone' => $user->telephone,
                'permissions' => $this->getUserPermissions($user),
            ],
            'stats' => $this->getDashboardStats($user),
            'recent_operations' => $this->getRecentOperations($user, 5),
            'notifications' => $this->getNotifications($user),
        ];

        return response()->json($data);
    }

    private function getUserPermissions($user)
    {
        return [
            'can_create_operations' => $user->canAccessModule('operations'),
            'can_validate' => $user->canAccessModule('validations'),
            'can_pay' => $user->canAccessModule('treasury'),
            'can_accounting' => $user->canAccessModule('accounting'),
            'can_reports' => $user->canAccessModule('reports'),
        ];
    }

    private function getDashboardStats($user)
    {
        return [
            'pending_validations' => \App\Models\Operation::where('statut_courant', 'en_attente_de_validation')
                ->when(!in_array($user->role, ['admin', 'superadmin']), function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
            'my_operations' => \App\Models\Operation::where('user_id', $user->id)->count(),
            'to_validate' => \App\Models\Operation::where('statut_courant', 'en_validation')
                ->when(!in_array($user->role, ['admin', 'superadmin']), function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
            'to_pay' => \App\Models\Operation::where('statut_courant', 'Approuvé_en_attente_paiement')
                ->when(!$user->canAccessModule('accounting'), function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
        ];
    }

    private function getRecentOperations($user, $limit = 5)
    {
        return \App\Models\Operation::with(['typeOperation', 'client'])
            ->when(!in_array($user->role, ['admin', 'superadmin']), function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('demandeur_email', $user->email);
            })
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($operation) {
                return [
                    'id' => $operation->id,
                    'titre' => $operation->titre,
                    'statut' => $operation->statut_courant,
                    'montant' => $operation->montant_estime,
                    'date' => $operation->created_at->format('d/m/Y'),
                    'client' => $operation->client?->name,
                    'type' => $operation->typeOperation?->name,
                    'status_color' => $this->getStatusColor($operation->statut_courant),
                ];
            });
    }

    private function getNotifications($user)
    {
        return [
            'count' => 0, // À implémenter avec un système de notifications
            'items' => [],
        ];
    }

    private function getStatusColor($status)
    {
        $colors = [
            'en_attente_de_validation' => 'warning',
            'en_validation' => 'info',
            'Approuvé_en_attente_paiement' => 'success',
            'payee' => 'primary',
            'rejetee' => 'danger',
            'en_cours' => 'secondary',
            'termine' => 'dark',
        ];

        return $colors[$status] ?? 'secondary';
    }
}
