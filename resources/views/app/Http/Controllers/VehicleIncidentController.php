<?php

namespace App\Http\Controllers;

use App\Models\VehicleIncident;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;

class VehicleIncidentController extends Controller
{
    public function index(Request $request)
    {
        $q = VehicleIncident::with(['vehicle','reporter'])
            ->when($request->status, fn($qr) => $qr->where('status', $request->status))
            ->when($request->type, fn($qr) => $qr->where('type', $request->type))
            ->when($request->vehicle_id, fn($qr) => $qr->where('vehicle_id', $request->vehicle_id))
            ->when($request->from, fn($qr) => $qr->whereDate('date_incident', '>=', $request->from))
            ->when($request->to, fn($qr) => $qr->whereDate('date_incident', '<=', $request->to))
            ->orderByDesc('date_incident');

        $incidents = $q->paginate(10)->withQueryString();
        $statuses = ['open','in_progress','closed'];

        $vehicles = Vehicle::orderBy('immatriculation')->get();
        $types = ['Accident', 'Panne', 'Vol', 'Vandalisme', 'Autre'];

        return view('parc.incidents.index', compact('incidents', 'vehicles', 'statuses', 'types'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('immatriculation')->get();
        $types = ['Accident', 'Panne', 'Vol', 'Vandalisme', 'Autre'];
        $severities = ['Faible', 'Moyenne', 'Élevée', 'Critique'];
        
        return view('parc.incidents.create', compact('vehicles', 'types', 'severities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|string|max:50',
            'date_incident' => 'required|date',
            'severity' => 'required|in:Faible,Moyenne,Élevée,Critique',
            'description' => 'required|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['reported_by'] = auth()->id();
        $validated['status'] = 'open';

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('incidents', 'public');
            $validated['attachment'] = $path;
        }

        VehicleIncident::create($validated);

        return redirect()->route('parc.incidents.index')
            ->with('success', 'Incident créé avec succès');
    }

    public function show(VehicleIncident $incident)
    {
        $incident->load(['vehicle', 'reporter']);
        
        return view('parc.incidents.show', compact('incident'));
    }

    public function edit(VehicleIncident $incident)
    {
        $vehicles = Vehicle::orderBy('immatriculation')->get();
        $types = ['Accident', 'Panne', 'Vol', 'Vandalisme', 'Autre'];
        $severities = ['Faible', 'Moyenne', 'Élevée', 'Critique'];
        $statuses = ['open', 'in_progress', 'closed'];
        
        return view('parc.incidents.edit', compact('incident', 'vehicles', 'types', 'severities', 'statuses'));
    }

    public function update(Request $request, VehicleIncident $incident)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|string|max:50',
            'date_incident' => 'required|date',
            'severity' => 'required|in:Faible,Moyenne,Élevée,Critique',
            'description' => 'required|string',
            'status' => 'required|in:open,in_progress,closed',
            'estimated_cost' => 'nullable|numeric|min:0',
            'final_cost' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            if ($incident->attachment && \Storage::disk('public')->exists($incident->attachment)) {
                \Storage::disk('public')->delete($incident->attachment);
            }
            $path = $request->file('attachment')->store('incidents', 'public');
            $validated['attachment'] = $path;
        }

        $incident->update($validated);

        return redirect()->route('parc.incidents.index')
            ->with('success', 'Incident mis à jour avec succès');
    }

    public function destroy(VehicleIncident $incident)
    {
        if ($incident->attachment && \Storage::disk('public')->exists($incident->attachment)) {
            \Storage::disk('public')->delete($incident->attachment);
        }

        $incident->delete();

        return redirect()->route('parc.incidents.index')
            ->with('success', 'Incident supprimé avec succès');
    }
}
