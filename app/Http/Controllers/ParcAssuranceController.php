<?php

namespace App\Http\Controllers;

use App\Models\Assurance;
use App\Models\Vehicule;
use Illuminate\Http\Request;

class ParcAssuranceController extends Controller
{
    public function index(Request $request)
    {
        $q = Assurance::with('vehicle');

        if ($request->filled('vehicule')) {
            $term = trim($request->vehicule);
            $q->whereHas('vehicle', function($v) use ($term) {
                $v->where('immatriculation', 'like', "%{$term}%")
                  ->orWhere('modele', 'like', "%{$term}%");
            });
        }

        if ($request->filled('assureur')) {
            $q->where('assureur', 'like', '%'.trim($request->assureur).'%');
        }

        if ($request->filled('from')) {
            $q->whereDate('date_debut', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('date_fin', '<=', $request->to);
        }

        if ($request->filled('statut')) {
            $q->where('statut', $request->statut);
        }

        $assurances = $q->orderByDesc('date_fin')->paginate(20)->withQueryString();
        $vehicles   = Vehicule::orderBy('immatriculation')->get(['id','immatriculation']);

        return view('parc.assurances', compact('assurances','vehicles'));
    }

    public function create()
    {
        $vehicles = Vehicule::orderBy('immatriculation')->get(['id','immatriculation','modele']);
        return view('parc.assurances-create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id'     => 'required|exists:vehicules,id',
            'assureur'       => 'required|string|max:255',
            'numero_police'  => 'required|string|max:255',
            'date_debut'     => 'required|date',
            'date_fin'       => 'required|date|after_or_equal:date_debut',
            'prime_annuelle' => 'nullable|numeric|min:0',
            'statut'         => 'required|in:active,a_renouveler,expiree',
            'notes'          => 'nullable|string',
        ]);

        Assurance::create($data);

        return redirect()->route('parc.assurances.index')
            ->with('status', "Police d'assurance enregistrée avec succès.");
    }

    public function destroy(Assurance $assurance)
    {
        $assurance->delete();

        return redirect()->route('parc.assurances.index')
            ->with('status', "Police d'assurance supprimée.");
    }
}
