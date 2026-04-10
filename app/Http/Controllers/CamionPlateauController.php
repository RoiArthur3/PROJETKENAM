<?php

namespace App\Http\Controllers;

use App\Models\CamionPlateauParametrage;
use App\Models\Client;
use App\Models\Vehicule;
use App\Models\VehiclePointage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CamionPlateauController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // PARAMÉTRAGE
    // ─────────────────────────────────────────────────────────────────────────

    public function parametrage(Request $request)
    {
        $query = CamionPlateauParametrage::with(['vehicle', 'client'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('type_facturation')) {
            $query->where('type_facturation', $request->type_facturation);
        }

        $params    = $query->paginate(20)->withQueryString();
        $vehicles  = Vehicule::orderBy('immatriculation')->get();
        $clients   = Client::orderBy('raison_sociale')->get();

        return view('materiel.cost-control.camion-plateau.parametrage', compact('params', 'vehicles', 'clients'));
    }

    public function createParametrage()
    {
        $vehicles = Vehicule::orderBy('immatriculation')->get();
        $clients  = Client::orderBy('raison_sociale')->get();
        $param    = new CamionPlateauParametrage();

        return view('materiel.cost-control.camion-plateau.parametrage-form', compact('param', 'vehicles', 'clients'));
    }

    public function storeParametrage(Request $request)
    {
        $data = $request->validate([
            'vehicle_id'             => 'nullable|exists:vehicules,id', // Engin non requis
            'client_id'              => 'nullable|exists:clients,id',
            'type_facturation'       => 'required|in:monthly,trip',
            'monthly_trip_threshold' => 'required|integer|min:0',
            'monthly_flat_rate'      => 'required|numeric|min:0',
            'extra_trip_unit_price'  => 'required|numeric|min:0',
            'trip_client_price'      => 'required|numeric|min:0',
            'supplier_type_paiement' => 'nullable|in:monthly,trip', // Fournisseur non requis
            'supplier_monthly_cost'  => 'nullable|numeric|min:0',
            'supplier_trip_cost'     => 'nullable|numeric|min:0',
            'is_active'              => 'boolean',
            'notes'                  => 'nullable|string|max:2000',
        ]);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['created_by']  = Auth::id();

        CamionPlateauParametrage::create($data);

        return redirect()->route('materiel.cost-control.camion-plateau.parametrage')
            ->with('success', 'Paramétrage enregistré avec succès.');
    }

    public function editParametrage(CamionPlateauParametrage $param)
    {
        $vehicles = Vehicule::orderBy('immatriculation')->get();
        $clients  = Client::orderBy('raison_sociale')->get();

        return view('materiel.cost-control.camion-plateau.parametrage-form', compact('param', 'vehicles', 'clients'));
    }

    public function showParametrage(CamionPlateauParametrage $param)
    {
        $param->load(['vehicle', 'client']);

        return view('materiel.cost-control.camion-plateau.parametrage-show', compact('param'));
    }

    public function updateParametrage(Request $request, CamionPlateauParametrage $param)
    {
        $data = $request->validate([
            'vehicle_id'             => 'nullable|exists:vehicules,id', // Engin non requis
            'client_id'              => 'nullable|exists:clients,id',
            'type_facturation'       => 'required|in:monthly,trip',
            'monthly_trip_threshold' => 'required|integer|min:0',
            'monthly_flat_rate'      => 'required|numeric|min:0',
            'extra_trip_unit_price'  => 'required|numeric|min:0',
            'trip_client_price'      => 'required|numeric|min:0',
            'supplier_type_paiement' => 'nullable|in:monthly,trip', // Fournisseur non requis
            'supplier_monthly_cost'  => 'nullable|numeric|min:0',
            'supplier_trip_cost'     => 'nullable|numeric|min:0',
            'is_active'              => 'boolean',
            'notes'                  => 'nullable|string|max:2000',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $param->update($data);

        return redirect()->route('materiel.cost-control.camion-plateau.parametrage')
            ->with('success', 'Paramétrage mis à jour.');
    }

    public function destroyParametrage(CamionPlateauParametrage $param)
    {
        $param->delete();

        return redirect()->route('materiel.cost-control.camion-plateau.parametrage')
            ->with('success', 'Paramétrage supprimé.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FACTURATION AU MOIS
    // ─────────────────────────────────────────────────────────────────────────

    public function facturations(Request $request)
    {
        $query = VehiclePointage::with(['vehicle', 'mission.client'])
            ->where('submodule', 'camion_plateau')
            ->where('billing_mode', 'monthly');

        if ($request->filled('mois')) {
            $query->whereRaw('DATE_FORMAT(date_pointage, "%Y-%m") = ?', [$request->mois]);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('client_id')) {
            $query->whereHas('mission', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        $pointages = $query->orderBy('date_pointage', 'desc')->get();

        // Grouper par mois + véhicule pour afficher un tableau de facturation
        $grouped = $pointages->groupBy(function ($p) {
            return optional($p->date_pointage)->format('Y-m') . '_' . $p->vehicle_id;
        })->map(function ($items) {
            $first       = $items->first();
            $tripCount   = $items->sum('trip_count');
            $suppTotal   = $items->sum('total_supplier_cost');
            $clientTotal = $items->sum('total_client_amount');

            // Calcul surcharge: nb voyages > seuil parametrage
            $param = CamionPlateauParametrage::where('vehicle_id', $first->vehicle_id)->first();
            $surcharge = 0;
            if ($param && $tripCount > $param->monthly_trip_threshold) {
                $surcharge = ($tripCount - $param->monthly_trip_threshold) * $param->extra_trip_unit_price;
            }

            return [
                'vehicle'      => $first->vehicle,
                'mois'         => optional($first->date_pointage)->format('Y-m'),
                'mois_label'   => optional($first->date_pointage)->format('F Y'),
                'trip_count'   => $tripCount,
                'threshold'    => $param?->monthly_trip_threshold ?? 0,
                'forfait'      => $param?->monthly_flat_rate ?? 0,
                'surcharge'    => $surcharge,
                'client_total' => $clientTotal + $surcharge,
                'supp_total'   => $suppTotal,
                'margin'       => ($clientTotal + $surcharge) - $suppTotal,
                'items'        => $items,
            ];
        })->values();

        $vehicles = Vehicule::orderBy('immatriculation')->get();
        $clients  = Client::orderBy('raison_sociale')->get();

        $totals = [
            'client'  => $grouped->sum('client_total'),
            'supp'    => $grouped->sum('supp_total'),
            'margin'  => $grouped->sum('margin'),
            'voyages' => $grouped->sum('trip_count'),
        ];

        return view('materiel.cost-control.camion-plateau.facturation-mois', compact('grouped', 'vehicles', 'clients', 'totals'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUIVI DES VOYAGES
    // ─────────────────────────────────────────────────────────────────────────

    public function suiviVoyages(Request $request)
    {
        $query = VehiclePointage::with(['vehicle', 'mission.client'])
            ->where('submodule', 'camion_plateau');

        if ($request->filled('date_from')) {
            $query->whereDate('date_pointage', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_pointage', '<=', $request->date_to);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('client_id')) {
            $query->whereHas('mission', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }
        if ($request->filled('billing_mode')) {
            $query->where('billing_mode', $request->billing_mode);
        }

        $voyages  = $query->orderBy('date_pointage', 'desc')->paginate(30)->withQueryString();
        $vehicles = Vehicule::orderBy('immatriculation')->get();
        $clients  = Client::orderBy('raison_sociale')->get();

        $totals = [
            'trips'   => VehiclePointage::where('submodule', 'camion_plateau')->sum('trip_count'),
            'client'  => VehiclePointage::where('submodule', 'camion_plateau')->sum('total_client_amount'),
            'supp'    => VehiclePointage::where('submodule', 'camion_plateau')->sum('total_supplier_cost'),
            'margin'  => VehiclePointage::where('submodule', 'camion_plateau')
                            ->selectRaw('SUM(total_client_amount - total_supplier_cost) as margin')
                            ->value('margin') ?? 0,
        ];

        return view('materiel.cost-control.camion-plateau.suivi-voyages', compact('voyages', 'vehicles', 'clients', 'totals'));
    }
}
