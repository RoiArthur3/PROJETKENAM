<?php

namespace App\Http\Controllers\Materiel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicule;
// Chauffeur model is missing, using mock for now

class CarburantController extends Controller
{
    /**
     * Affiche la liste des pleins de carburant
     */
    public function index()
    {
        // Données factices pour la démo
        $carburants = collect([
            (object)[
                'id' => 1,
                'reference' => 'CARB-2026-001',
                'date' => now()->subDays(5),
                'vehicule' => 'Toyota Hilux',
                'type_carburant' => 'Gazole',
                'quantite' => 60,
                'prix_unitaire' => 800,
                'montant_total' => 48000,
                'kilometrage_avant' => 44500,
                'kilometrage_apres' => 45000,
                'consommation' => 12.0,
                'station' => 'Total',
                'chauffeur' => 'Jean Dupont'
            ],
            (object)[
                'id' => 2,
                'reference' => 'CARB-2026-002',
                'date' => now()->subDays(2),
                'vehicule' => 'Nissan Navara',
                'type_carburant' => 'Gazole',
                'quantite' => 55,
                'prix_unitaire' => 800,
                'montant_total' => 44000,
                'kilometrage_avant' => 61500,
                'kilometrage_apres' => 62000,
                'consommation' => 11.0,
                'station' => 'Shell',
                'chauffeur' => 'Marie Kouassi'
            ]
        ]);

        return view('materiel.carburant', compact('carburants'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau plein
     */
    public function create()
    {
        // Données factices pour la démo
        $vehicules = [
            (object)['id' => 1, 'immatriculation' => 'CI-123-AB', 'marque' => 'Toyota', 'modele' => 'Hilux'],
            (object)['id' => 2, 'immatriculation' => 'CI-456-CD', 'marque' => 'Nissan', 'modele' => 'Navara']
        ];

        $chauffeurs = [
            (object)['id' => 1, 'nom' => 'Jean Dupont', 'contact' => '07000000'],
            (object)['id' => 2, 'nom' => 'Marie Kouassi', 'contact' => '07000001']
        ];

        $typesCarburant = [
            'Gazole' => 'Gazole',
            'Essence' => 'Essence',
            'Sans Plomb 95' => 'Sans Plomb 95',
            'Sans Plomb 98' => 'Sans Plomb 98',
            'GPL' => 'GPL',
            'GNR' => 'GNR (Gazole Non Routier)'
        ];

        $stations = [
            'Total' => 'Total',
            'Shell' => 'Shell',
            'Vivo Energy' => 'Vivo Energy',
            'Petro Ivoire' => 'Petro Ivoire',
            'Tidiane' => 'Tidiane',
            'Autre' => 'Autre'
        ];

        return view('materiel.carburant-create', compact('vehicules', 'chauffeurs', 'typesCarburant', 'stations'));
    }

    /**
     * Enregistre un nouveau plein de carburant
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'date' => 'required|date',
            'type_carburant' => 'required|string|max:50',
            'quantite' => 'required|numeric|min:0.1',
            'prix_unitaire' => 'required|numeric|min:0',
            'kilometrage_avant' => 'required|integer|min:0',
            'kilometrage_apres' => 'required|integer|min:' . ($request->kilometrage_avant + 1),
            'station' => 'required|string|max:100',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'bon_de_commande' => 'nullable|string|max:50',
            'facture' => 'nullable|string|max:50',
            'commentaire' => 'nullable|string'
        ]);

        // Calcul du montant total
        $validated['montant_total'] = $validated['quantite'] * $validated['prix_unitaire'];

        // Calcul de la consommation (si kilométrage après > kilométrage avant)
        $distance = $validated['kilometrage_apres'] - $validated['kilometrage_avant'];
        $validated['consommation'] = $distance > 0 ? ($validated['quantite'] / $distance) * 100 : 0;

        // En production, on enregistrerait en base de données ici
        // $carburant = Carburant::create($validated);

        // Redirection avec message de succès
        return redirect()->route('materiel.carburant.index')
                         ->with('success', 'Le plein de carburant a été enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un plein de carburant
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Affiche le formulaire de modification d'un plein
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Met à jour un plein de carburant existant
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Supprime un plein de carburant
     */
    public function destroy(string $id)
    {
        //
    }
}
