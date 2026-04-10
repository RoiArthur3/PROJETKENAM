<?php

namespace App\Http\Controllers;

use App\Models\Reparation;
use App\Models\Vehicule;
use Illuminate\Http\Request;

class ReparationController extends Controller
{
    public function index()
    {
        $reparations = Reparation::with(['vehicule', 'user'])
            ->orderByDesc('date')
            ->paginate(50);
        return view('parc.reparations.index', compact('reparations'));
    }

    public function create()
    {
        $vehicules = Vehicule::orderBy('immatriculation')->get();
        return view('parc.reparations.create', compact('vehicules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'date' => 'required|date',
            'type_panne' => 'required|string|in:Mécanique,Électrique,Carrosserie,Pneumatique,Autre',
            'urgence' => 'required|string|in:Normale,Urgente,Critique',
            'description' => 'required|string',
            'garage' => 'nullable|string|max:200',
            'cout_estime' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Ajouter l'utilisateur connecté
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        // Statut par défaut
        $data['statut'] = 'En attente';

        Reparation::create($data);

        return redirect()->route('parc.reparations')->with('success', 'Réparation enregistrée avec succès.');
    }

    public function show(Reparation $reparation)
    {
        $reparation->load(['vehicule', 'user']);
        return view('parc.reparations.show', compact('reparation'));
    }

    public function edit(Reparation $reparation)
    {
        $vehicules = Vehicule::orderBy('immatriculation')->get();
        return view('parc.reparations.edit', compact('reparation', 'vehicules'));
    }

    public function update(Request $request, Reparation $reparation)
    {
        $data = $request->validate([
            'statut' => 'required|string|in:En attente,En cours,Terminée,Annulée',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'cout_reel' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $reparation->update($data);

        return redirect()->route('parc.reparations')->with('success', 'Réparation mise à jour avec succès.');
    }
}
