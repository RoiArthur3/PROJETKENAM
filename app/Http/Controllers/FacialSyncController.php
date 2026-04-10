<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\FacialDevice;
use App\Services\HikvisionFaceSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FacialSyncController extends Controller
{
    private HikvisionFaceSyncService $syncService;

    public function __construct(HikvisionFaceSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Afficher l'interface de synchronisation faciale
     */
    public function index()
    {
        $employees = \App\Models\Personnel::with(['user'])
            ->when(request('sync_status'), function($query, $status) {
                if ($status === 'null') {
                    $query->whereNull('facial_sync_status');
                } else {
                    $query->where('facial_sync_status', $status);
                }
            })
            ->when(request('service'), function($query, $service) {
                $query->where('service', $service);
            })
            ->orderBy('nom')
            ->paginate(50);

        $services = \App\Models\Personnel::distinct()->pluck('service')->filter();

        $stats = [
            'total' => \App\Models\Personnel::count(),
            'synced' => \App\Models\Personnel::where('facial_sync_status', 'synced')->count(),
            'pending' => \App\Models\Personnel::whereNull('facial_sync_status')->count(),
            'failed' => \App\Models\Personnel::where('facial_sync_status', 'failed')->count(),
        ];

        return view('rh.facial-sync.index', compact('employees', 'services', 'stats'));
    }

    /**
     * Synchroniser un employé spécifique vers la caméra
     */
    public function syncEmployee(Personnel $personnel): JsonResponse
    {
        try {
            $device = FacialDevice::where('is_active', true)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun terminal facial actif trouvé'
                ], 404);
            }

            $success = $this->syncService->syncEmployeeToCamera($personnel, $device);

            return response()->json([
                'success' => $success,
                'message' => $success
                    ? 'Employé synchronisé avec succès'
                    : 'Erreur lors de la synchronisation',
                'employee' => $personnel->load(['user', 'device'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchroniser tous les employés en attente
     */
    public function syncAllPending(): JsonResponse
    {
        try {
            $results = $this->syncService->syncAllPendingEmployees();

            return response()->json([
                'success' => true,
                'message' => 'Synchronisation terminée',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un employé de la caméra
     */
    public function removeEmployee(Personnel $personnel): JsonResponse
    {
        try {
            $device = FacialDevice::where('is_active', true)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun terminal facial actif trouvé'
                ], 404);
            }

            $success = $this->syncService->removeEmployeeFromCamera($personnel, $device);

            return response()->json([
                'success' => $success,
                'message' => $success
                    ? 'Employé supprimé de la caméra'
                    : 'Erreur lors de la suppression'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier le statut de synchronisation d'un employé
     */
    public function checkStatus(Personnel $personnel): JsonResponse
    {
        try {
            $device = FacialDevice::where('is_active', true)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun terminal facial actif trouvé'
                ], 404);
            }

            $status = $this->syncService->checkSyncStatus($personnel, $device);

            return response()->json([
                'success' => true,
                'status' => $status,
                'employee' => $personnel->load(['user', 'device'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchronisation en masse depuis la vue employés
     */
    public function bulkSync(Request $request): JsonResponse
    {
        try {
            $employeeIds = $request->input('employee_ids', []);

            if (empty($employeeIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun employé sélectionné'
                ], 400);
            }

            $device = FacialDevice::where('is_active', true)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun terminal facial actif trouvé'
                ], 404);
            }

            $results = [
                'success' => 0,
                'failed' => 0,
                'details' => []
            ];

            $employees = Personnel::whereIn('id', $employeeIds)->get();

            foreach ($employees as $employee) {
                $success = $this->syncService->syncEmployeeToCamera($employee, $device);

                if ($success) {
                    $results['success']++;
                    $results['details'][] = [
                        'id' => $employee->id,
                        'matricule' => $employee->matricule,
                        'nom' => $employee->nom_complet,
                        'status' => 'success'
                    ];
                } else {
                    $results['failed']++;
                    $results['details'][] = [
                        'id' => $employee->id,
                        'matricule' => $employee->matricule,
                        'nom' => $employee->nom_complet,
                        'status' => 'failed'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Synchronisation en masse terminée',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
