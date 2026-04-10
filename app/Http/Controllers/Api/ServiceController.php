<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOperationnel;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Récupérer la liste des services pour les formulaires
     */
    public function index(Request $request)
    {
        $query = ServiceOperationnel::where('actif', true);
        
        // Filtrer par recherche
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('responsable', 'like', "%{$search}%");
            });
        }
        
        // Filtrer par email disponible
        if ($request->has('with_email') && $request->get('with_email')) {
            $query->whereNotNull('email')->where('email', '!=', '');
        }
        
        $services = $query->select('id', 'nom', 'email', 'responsable', 'telephone', 'couleur', 'icone')
                         ->orderBy('nom')
                         ->limit(100)
                         ->get();
        
        return response()->json([
            'success' => true,
            'data' => $services,
            'total' => $services->count()
        ]);
    }
    
    /**
     * Récupérer un service spécifique
     */
    public function show($id)
    {
        $service = ServiceOperationnel::where('actif', true)
                                     ->where('id', $id)
                                     ->first();
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service non trouvé'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }
}
