<?php

namespace App\Http\Controllers\Checking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Checking;
use App\Models\InspectionChecklist;
use App\Models\User;
use App\Models\Vehicule;

class CheckingController extends Controller
{
    public function index()
    {
        $checkings = Checking::with(['inspector','checklist','checkable'])
            ->latest('created_at')
            ->paginate(15);
        return view('checking.index', compact('checkings'));
    }

    public function create()
    {
        $checklists = InspectionChecklist::orderBy('name')->get();
        $inspectors = User::orderBy('name')->get();
        $vehicules = Vehicule::select('id','immatriculation','marque','modele')
            ->orderBy('immatriculation')
            ->limit(100)
            ->get();
        return view('checking.create', compact('checklists','inspectors','vehicules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'checklist_id' => 'nullable|exists:inspection_checklists,id',
            'inspector_id' => 'nullable|exists:users,id',
            'scheduled_at' => 'nullable|date',
            'vehicle_input' => 'nullable|string|max:50',
        ]);

        $checking = new Checking();
        $checking->title = $validated['title'];
        $checking->description = $validated['description'] ?? null;
        $checking->type = $validated['type'] ?? 'vehicule';
        $checking->checklist_id = $validated['checklist_id'] ?? null;
        $checking->inspector_id = $validated['inspector_id'] ?? null;
        $checking->scheduled_at = $validated['scheduled_at'] ?? null;
        $checking->status = $validated['scheduled_at'] ? 'scheduled' : 'draft';
        $checking->created_by = auth()->id();

        // Liaison au Parc via checkable (vehicule) si renseigné
        $vehicule = null;
        if (!empty($validated['vehicle_input'])) {
            $vehicule = Vehicule::where('immatriculation', $validated['vehicle_input'])->first();
        }

        $checking->save();

        if ($vehicule) {
            $checking->checkable()->associate($vehicule);
            $checking->save();
        }

        return redirect()->route('checking.show', $checking->id)
            ->with('success', 'Vérification créée avec succès');
    }

    public function show(Checking $checking)
    {
        $checking->load(['inspector','checklist','checkable']);
        return view('checking.show', compact('checking'));
    }

    public function edit(Checking $checking)
    {
        $checklists = InspectionChecklist::orderBy('name')->get();
        $inspectors = User::orderBy('name')->get();
        // Prefill immatriculation if vehicule linked
        $vehiculeImmat = null;
        if ($checking->checkable && \Illuminate\Support\Str::contains($checking->checkable_type, 'Vehicule')) {
            $vehiculeImmat = $checking->checkable->immatriculation ?? null;
        }
        return view('checking.edit', compact('checking','checklists','inspectors','vehiculeImmat'));
    }

    public function update(Request $request, Checking $checking)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'checklist_id' => 'nullable|exists:inspection_checklists,id',
            'inspector_id' => 'nullable|exists:users,id',
            'scheduled_at' => 'nullable|date',
            'vehicle_input' => 'nullable|string|max:50',
        ]);

        $checking->fill([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? 'vehicule',
            'checklist_id' => $validated['checklist_id'] ?? null,
            'inspector_id' => $validated['inspector_id'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => ($validated['scheduled_at'] ?? null) ? 'scheduled' : $checking->status,
        ]);

        $checking->updated_by = auth()->id();
        $checking->save();

        // Re-associer le véhicule si fourni
        if (!empty($validated['vehicle_input'])) {
            $vehicule = Vehicule::where('immatriculation', $validated['vehicle_input'])->first();
            if ($vehicule) {
                $checking->checkable()->associate($vehicule);
                $checking->save();
            }
        }

        return redirect()->route('checking.show', $checking->id)
            ->with('success', 'Vérification mise à jour');
    }

    public function destroy(Checking $checking)
    {
        $checking->delete();
        return redirect()->route('checking.index')->with('success', 'Vérification supprimée');
    }

    public function vehicleSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }
        $items = Vehicule::query()
            ->where('immatriculation', 'like', "%$q%")
            ->orWhere('marque', 'like', "%$q%")
            ->orWhere('modele', 'like', "%$q%")
            ->orderBy('immatriculation')
            ->limit(10)
            ->get(['id','immatriculation','marque','modele']);
        return response()->json($items);
    }

    public function byType()
    {
        $types = Vehicule::distinct()->pluck('type_materiel')->filter()->values();
        $stats = [];
        
        foreach ($types as $type) {
            $stats[$type] = [
                'count' => Vehicule::where('type_materiel', $type)->count(),
                'checkings_count' => Checking::whereHasMorph('checkable', [Vehicule::class], function($query) use ($type) {
                    $query->where('type_materiel', $type);
                })->count(),
                'latest_checking' => Checking::whereHasMorph('checkable', [Vehicule::class], function($query) use ($type) {
                    $query->where('type_materiel', $type);
                })->with('inspector')->latest()->first()
            ];
        }

        return view('checking.by-type', compact('stats'));
    }
}
