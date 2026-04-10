<?php

namespace App\Http\Controllers;

use App\Models\VehicleAssignment;
use App\Models\Vehicule;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class VehicleAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $q = VehicleAssignment::with(['vehicle','driver'])
            ->when($request->status, fn($qr) => $qr->where('status', $request->status))
            ->when($request->vehicle_id, fn($qr) => $qr->where('vehicle_id', $request->vehicle_id))
            ->when($request->from, fn($qr) => $qr->whereDate('assigned_at', '>=', $request->from))
            ->when($request->to, fn($qr) => $qr->whereDate('assigned_at', '<=', $request->to))
            ->orderByDesc('assigned_at');

        $assignments = $q->paginate(10)->withQueryString();
        $statuses = ['assigned','in_progress','completed','cancelled'];

        // Lists for creation form
        $vehicles = Vehicule::orderBy('immatriculation')->get(['id','immatriculation']);
        // Pour l'instant, récupérer tous les utilisateurs comme conducteurs potentiels
        $drivers = User::orderBy('name')->get(['id','name']);

        return view('parc.affectations', compact('assignments','statuses','vehicles','drivers'));
    }

    public function create()
    {
        $vehicles = Vehicule::orderBy('immatriculation')->get(['id', 'immatriculation']);
        $drivers = User::orderBy('name')->get(['id', 'name']);

        return view('parc.affectations-create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicules,id',
            'driver_id' => 'required|exists:users,id',
            'requete_id' => 'nullable|exists:requetes,id',
            'mission' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'assigned_at' => 'required|date',
            'returned_at' => 'nullable|date|after_or_equal:assigned_at',
            'status' => 'required|in:assigned,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        VehicleAssignment::create($data);

        return redirect()->route('parc.affectations.index')
            ->with('success', 'Affectation créée avec succès');
    }

    /**
     * Mettre à jour rapidement le statut d'une affectation.
     */
    public function updateStatus(Request $request, VehicleAssignment $affectation)
    {
        $validated = $request->validate([
            'status' => 'required|in:assigned,in_progress,completed,cancelled',
        ]);

        $affectation->status = $validated['status'];
        $affectation->save();

        return redirect()->route('parc.affectations.index')
            ->with('success', 'Statut de l\'affectation mis à jour.');
    }

    /**
     * Supprimer une affectation.
     */
    public function destroy(VehicleAssignment $affectation)
    {
        $affectation->delete();

        return redirect()->route('parc.affectations.index')
            ->with('success', 'Affectation supprimée avec succès.');
    }

    public function export(Request $request)
    {
        $q = $request->get('q', '');
        $status = $request->get('status', '');
        $from = $request->get('from', '');
        $to = $request->get('to', '');

        $assignments = VehicleAssignment::with(['vehicle', 'driver'])
            ->when($status, fn($qr) => $qr->where('status', $status))
            ->when($from, fn($qr) => $qr->whereDate('assigned_at', '>=', $from))
            ->when($to, fn($qr) => $qr->whereDate('assigned_at', '<=', $to))
            ->orderBy('assigned_at', 'desc')
            ->get();

        $filename = 'affectations_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($assignments) {
            $file = fopen('php://output', 'w');

            // BOM pour l'encodage UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Véhicule',
                'Conducteur',
                'Mission',
                'Destination',
                'Début',
                'Fin',
                'Statut',
                'Notes'
            ], ';');

            // Données
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->id,
                    optional($assignment->vehicle)->immatriculation ?? '',
                    optional($assignment->driver)->name ?? '',
                    $assignment->mission ?? '',
                    $assignment->destination ?? '',
                    $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i:s') : '',
                    $assignment->returned_at ? $assignment->returned_at->format('Y-m-d H:i:s') : '',
                    $assignment->status,
                    $assignment->notes ?? ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
