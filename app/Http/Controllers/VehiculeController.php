<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class VehiculeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicule::with('fournisseur');

        // Recherche plein-texte simple
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('immatriculation', 'like', "%{$search}%")
                  ->orWhere('marque', 'like', "%{$search}%")
                  ->orWhere('modele', 'like', "%{$search}%");
            });
        }

        // Filtre de statut (basé sur la colonne 'disponible')
        if ($request->filled('statut')) {
            $statut = $request->get('statut');
            if ($statut === 'Disponible') {
                $query->where('disponible', true);
            } elseif (in_array($statut, ['En mission', 'En maintenance', 'Immobilisé'])) {
                // Sans autres colonnes, on considère ces statuts comme non disponibles
                $query->where('disponible', false);
            }
        }

        // Filtre par type de matériel roulant si présent
        if ($request->filled('type')) {
            $query->where('type_materiel', $request->get('type'));
        }

        // Filtre par provenance
        if ($request->filled('provenance')) {
            $query->where('provenance', $request->get('provenance'));
        }

        $vehicules = $query->latest('created_at')->paginate(10)->withQueryString();

        // KPIs globaux (non filtrés)
        $totalVehicles = Vehicule::count();
        $availableCount = Vehicule::where('disponible', true)->count();

        return view('vehicules.index', compact('vehicules', 'totalVehicles', 'availableCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fournisseurs = Fournisseur::orderBy('raison_sociale')->get();
        return view('vehicules.create', compact('fournisseurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(Vehicule::rules());

        Vehicule::create($validated);

        return redirect()
            ->route('materiel.vehicules')
            ->with('success', 'Véhicule créé avec succès')
            ->withInput(); // Conserver les filtres actuels
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vehicule = Vehicule::findOrFail($id);
        return view('materiel.vehicules-show', compact('vehicule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vehicule = Vehicule::findOrFail($id);
        return view('materiel.vehicules-edit', compact('vehicule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vehicule = Vehicule::findOrFail($id);
        $rules = Vehicule::rules();
        $rules['immatriculation'] = 'required|string|max:20|unique:vehicules,immatriculation,' . $vehicule->id;

        $validated = $request->validate($rules);

        $vehicule->update($validated);

        return redirect()
            ->route('materiel.vehicules')
            ->with('success', 'Véhicule mis à jour avec succès')
            ->withInput(); // Conserver les filtres actuels
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vehicule = Vehicule::findOrFail($id);
        $vehicule->delete();

        return redirect()
            ->route('materiel.vehicules')
            ->with('success', 'Véhicule supprimé avec succès')
            ->withInput(); // Conserver les filtres actuels
    }
}
