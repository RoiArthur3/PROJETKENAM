<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileOperationController extends Controller
{
    /**
     * Dashboard stats and recent operations for mobile
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        // Stats
        $stats = [
            'pending' => DB::table('operation_service_validation')
                ->where('statut', 'EN_COURS')
                ->when(!$user->isAdmin(), function ($query) use ($user) {
                    $query->where('service_operationnel_id', $user->service_id);
                })->count(),
                
            'approved' => Operation::where('statut_courant', 'approuvee')
                ->when(!$user->isAdmin(), function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
        ];

        // Recent Operations
        $recent = Operation::with(['typeOperation'])
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('demandeur_email', $user->email);
            })
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent' => $recent,
            'user' => [
                'name' => $user->name,
                'role' => $user->role,
                'service' => $user->operationalService?->nom
            ]
        ]);
    }

    /**
     * Complete list of operations for mobile
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Operation::with(['typeOperation', 'client'])
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('demandeur_email', $user->email);
            });

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('objet', 'like', "%{$request->search}%")
                  ->orWhere('numero_ordre', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('statut_courant', $request->status);
        }

        $operations = $query->latest()->paginate(15);

        return response()->json($operations);
    }
}
