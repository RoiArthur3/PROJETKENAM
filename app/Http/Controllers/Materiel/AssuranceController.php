<?php

namespace App\Http\Controllers\Materiel;

use App\Http\Controllers\Controller;
use App\Models\Assurance;
use App\Models\Vehicule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssuranceController extends Controller
{
    /**
     * Display a listing of assurances with alert status.
     */
    public function index()
    {
        $assurances = Assurance::with('vehicle')
            ->orderBy('date_fin', 'asc')
            ->get();

        // Ajouter la statut d'alerte pour chaque assurance
        $assurances = $assurances->map(function ($assurance) {
            $days_remaining = now()->diffInDays($assurance->date_fin, false);
            
            if ($days_remaining < 0) {
                $assurance->alert_type = 'danger'; // Expirée
                $assurance->alert_message = 'Expirée';
            } elseif ($days_remaining <= 30) {
                $assurance->alert_type = 'warning'; // À renouveler bientôt
                $assurance->alert_message = "$days_remaining jours restants";
            } else {
                $assurance->alert_type = 'success'; // OK
                $assurance->alert_message = "$days_remaining jours restants";
            }
            
            return $assurance;
        });

        // Statistiques
        $stats = [
            'total' => Assurance::count(),
            'actives' => $assurances->where('alert_type', 'success')->count(),
            'a_renouveler' => $assurances->where('alert_type', 'warning')->count(),
            'expirees' => $assurances->where('alert_type', 'danger')->count(),
        ];

        $totalVehicles = Vehicule::count();

        return view('materiel.assurances.index', compact('assurances', 'stats', 'totalVehicles'));
    }

    /**
     * Show the form for creating a new assurance.
     */
    public function create()
    {
        $vehicules = Vehicule::where('disponible', true)
            ->orderBy('immatriculation')
            ->get();

        return view('materiel.assurances.create', compact('vehicules'));
    }

    /**
     * Store a newly created assurance in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'numero_police' => 'required|string|unique:assurances',
            'assureur' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'prime_annuelle' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Assurance::create($validated);

        return redirect()->route('materiel.assurances.index')
            ->with('success', 'Assurance ajoutée avec succès');
    }

    /**
     * Display the specified assurance.
     */
    public function show(Assurance $assurance)
    {
        $assurance->load('vehicle');
        
        $days_remaining = now()->diffInDays($assurance->date_fin, false);
        
        if ($days_remaining < 0) {
            $assurance->alert_type = 'danger';
            $assurance->alert_message = 'Expirée depuis ' . abs($days_remaining) . ' jours';
        } elseif ($days_remaining <= 30) {
            $assurance->alert_type = 'warning';
            $assurance->alert_message = "$days_remaining jours restants";
        } else {
            $assurance->alert_type = 'success';
            $assurance->alert_message = "$days_remaining jours restants";
        }

        return view('materiel.assurances.show', compact('assurance'));
    }

    /**
     * Show the form for editing the specified assurance.
     */
    public function edit(Assurance $assurance)
    {
        $vehicules = Vehicule::where('disponible', true)
            ->orderBy('immatriculation')
            ->get();

        return view('materiel.assurances.edit', compact('assurance', 'vehicules'));
    }

    /**
     * Update the specified assurance in storage.
     */
    public function update(Request $request, Assurance $assurance)
    {
        $validated = $request->validate([
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'numero_police' => 'required|string|unique:assurances,numero_police,' . $assurance->id,
            'assureur' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'prime_annuelle' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $assurance->update($validated);

        return redirect()->route('materiel.assurances.index')
            ->with('success', 'Assurance mise à jour avec succès');
    }

    /**
     * Remove the specified assurance from storage.
     */
    public function destroy(Assurance $assurance)
    {
        $assurance->delete();

        return redirect()->route('materiel.assurances.index')
            ->with('success', 'Assurance supprimée avec succès');
    }

    /**
     * Get assurances expiring soon for dashboard alerts.
     */
    public static function getExpiringAlerts($days = 30)
    {
        return Assurance::where('date_fin', '<=', now()->addDays($days))
            ->where('date_fin', '>', now())
            ->with('vehicle')
            ->orderBy('date_fin', 'asc')
            ->get();
    }

    /**
     * Get expired assurances for dashboard alerts.
     */
    public static function getExpiredAlerts()
    {
        return Assurance::where('date_fin', '<', now())
            ->with('vehicle')
            ->orderBy('date_fin', 'desc')
            ->get();
    }
}
