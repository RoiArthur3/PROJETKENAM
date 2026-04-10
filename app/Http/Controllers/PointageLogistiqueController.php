<?php

namespace App\Http\Controllers;

use App\Models\PointageLogistique;
use App\Models\Operation;
use App\Models\Vehicule;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PointageLogistiqueController extends Controller
{
    public function create()
    {
        $operations = Operation::query()
            ->orderByDesc('created_at')
            ->get();

        $vehiculesQuery = Vehicule::query();
        if (Schema::hasColumn('vehicules', 'statut')) {
            $vehiculesQuery->where('statut', 'disponible');
        } elseif (Schema::hasColumn('vehicules', 'disponible')) {
            $vehiculesQuery->where('disponible', true);
        }

        $vehicules = $vehiculesQuery->get();
        $drivers = User::where('role', 'chauffeur')->orWhere('role', 'driver')->get();

        return view('pointages.create', compact('operations', 'vehicules', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'operation_id' => 'required|exists:operations,id',
            'vehicle_id' => 'required|exists:vehicules,id',
            'driver_id' => 'required|exists:users,id',
            'date_pointage' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
            'kilometrage_debut' => 'nullable|numeric|min:0',
            'kilometrage_fin' => 'nullable|numeric|min:0',
            'carburant_debut' => 'nullable|numeric|min:0',
            'carburant_fin' => 'nullable|numeric|min:0',
            'objectif_heures' => 'nullable|numeric|min:0',
            'objectif_jours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $pointage = PointageLogistique::create([
                'operation_id' => $request->operation_id,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'date_pointage' => $request->date_pointage,
                'heure_debut' => $request->heure_debut,
                'heure_fin' => $request->heure_fin,
                'kilometrage_debut' => $request->kilometrage_debut,
                'kilometrage_fin' => $request->kilometrage_fin,
                'carburant_debut' => $request->carburant_debut,
                'carburant_fin' => $request->carburant_fin,
                'objectif_heures' => $request->objectif_heures ?? 8, // 8h par défaut
                'objectif_jours' => $request->objectif_jours ?? 1, // 1 jour par défaut
                'notes' => $request->notes,
                'statut' => 'en_cours',
            ]);

            // Calculs automatiques
            $pointage->calculateDuree();
            $pointage->calculateKilometrage();
            $pointage->calculateCarburant();
            $pointage->checkRetard();
            $pointage->calculateEfficacite();

            DB::commit();

            return redirect()->route('rh.pointages-engins.show', $pointage->id)
                ->with('success', 'Pointage enregistré avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pointage = PointageLogistique::with(['operation', 'vehicle', 'driver', 'validator'])
            ->findOrFail($id);

        $stats = PointageLogistique::getStatsByDriver($pointage->driver_id, $pointage->operation_id);
        $progression = $pointage->getProgression();

        return view('pointages.show', compact('pointage', 'stats', 'progression'));
    }

    public function index()
    {
        $pointages = PointageLogistique::with(['operation', 'vehicle', 'driver'])
            ->orderBy('date_pointage', 'desc')
            ->paginate(20);

        return view('pointages.index', compact('pointages'));
    }

    public function edit($id)
    {
        $pointage = PointageLogistique::findOrFail($id);
        $operations = Operation::all();
        $vehicules = Vehicule::all();
        $drivers = User::where('role', 'chauffeur')->orWhere('role', 'driver')->get();

        return view('pointages.edit', compact('pointage', 'operations', 'vehicules', 'drivers'));
    }

    public function update(Request $request, $id)
    {
        $pointage = PointageLogistique::findOrFail($id);

        $request->validate([
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
            'kilometrage_debut' => 'nullable|numeric|min:0',
            'kilometrage_fin' => 'nullable|numeric|min:0',
            'carburant_debut' => 'nullable|numeric|min:0',
            'carburant_fin' => 'nullable|numeric|min:0',
            'objectif_heures' => 'nullable|numeric|min:0',
            'objectif_jours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $pointage->update($request->all());

        // Recalculs
        $pointage->calculateDuree();
        $pointage->calculateKilometrage();
        $pointage->calculateCarburant();
        $pointage->calculateEfficacite();

        return redirect()->route('rh.pointages-engins.show', $pointage->id)
            ->with('success', 'Pointage mis à jour avec succès!');
    }

    public function validatePointage($id)
    {
        $pointage = PointageLogistique::findOrFail($id);

        $pointage->update([
            'statut' => 'valide',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return redirect()->route('rh.pointages-engins.show', $pointage->id)
            ->with('success', 'Pointage validé avec succès!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'motif_rejet' => 'required|string|max:500'
        ]);

        $pointage = PointageLogistique::findOrFail($id);

        $pointage->update([
            'statut' => 'rejete',
            'notes' => $request->motif_rejet,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return redirect()->route('rh.pointages-engins.show', $pointage->id)
            ->with('success', 'Pointage rejeté avec succès!');
    }

    public function dashboard()
    {
        // Statistiques générales
        $stats = [
            'total_pointages' => PointageLogistique::count(),
            'pointages_aujourdhui' => PointageLogistique::today()->count(),
            'pointages_en_retard' => PointageLogistique::enRetard()->count(),
            'pointages_valides' => PointageLogistique::validated()->count(),
            'total_heures' => PointageLogistique::validated()->sum('duree_heures'),
            'total_jours' => PointageLogistique::validated()->sum('duree_jours'),
            'total_km' => PointageLogistique::validated()->sum('kilometrage_jour'),
            'efficacite_moyenne' => PointageLogistique::validated()->avg('efficacite_score'),
        ];

        // Derniers pointages
        $derniersPointages = PointageLogistique::with(['operation', 'vehicle', 'driver'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Pointages en retard
        $pointagesRetard = PointageLogistique::with(['operation', 'vehicle', 'driver'])
            ->enRetard()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Efficacité par conducteur
        $efficaciteParConducteur = PointageLogistique::with(['driver'])
            ->validated()
            ->selectRaw('driver_id, AVG(efficacite_score) as efficacite_moyenne, COUNT(*) as nb_pointages')
            ->groupBy('driver_id')
            ->orderBy('efficacite_moyenne', 'desc')
            ->limit(5)
            ->get();

        return view('pointages.dashboard', compact(
            'stats',
            'derniersPointages',
            'pointagesRetard',
            'efficaciteParConducteur'
        ));
    }

    public function statsByOperation($operationId)
    {
        $operation = Operation::findOrFail($operationId);
        $stats = PointageLogistique::getStatsByOperation($operationId);

        $pointages = PointageLogistique::with(['vehicle', 'driver'])
            ->byOperation($operationId)
            ->orderBy('date_pointage', 'desc')
            ->get();

        return view('pointages.stats-operation', compact('operation', 'stats', 'pointages'));
    }

    public function statsByDriver($driverId)
    {
        $driver = User::findOrFail($driverId);
        $operations = Operation::whereHas('pointages', function($query) use ($driverId) {
            $query->where('driver_id', $driverId);
        })->get();

        $statsParOperation = [];
        foreach ($operations as $operation) {
            $statsParOperation[$operation->id] = PointageLogistique::getStatsByDriver($driverId, $operation->id);
        }

        return view('pointages.stats-driver', compact('driver', 'operations', 'statsParOperation'));
    }
}
