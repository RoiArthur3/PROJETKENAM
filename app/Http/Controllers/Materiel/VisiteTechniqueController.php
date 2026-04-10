<?php

namespace App\Http\Controllers\Materiel;

use App\Http\Controllers\Controller;
use App\Models\VisiteTechnique;
use App\Models\Vehicle;
use App\Models\Vehicule;
use Illuminate\Http\Request;

class VisiteTechniqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visites = VisiteTechnique::with('vehicle')->latest()->paginate(20);
        
        $stats = [
            'total' => VisiteTechnique::count(),
            'valides' => VisiteTechnique::where('date_expiration', '>', now())->count(),
            'a_renouveler' => VisiteTechnique::whereBetween('date_expiration', [now(), now()->addDays(30)])->count(),
            'expirees' => VisiteTechnique::where('date_expiration', '<=', now())->count(),
        ];
        
        return view('materiel.visites.index', compact('visites', 'stats'));
    }

    public function create()
    {
        $vehicules = $this->getVehiclesForVisites();
        return view('materiel.visites.create', compact('vehicules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date_visite' => 'required|date',
            'date_expiration' => 'required|date|after:date_visite',
            'centre_visite' => 'nullable|string|max:255',
            'numero_certificat' => 'nullable|string|max:255',
            'cout' => 'nullable|numeric|min:0',
            'commentaires' => 'nullable|string',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048'
        ]);

        if ($request->hasFile('piece_jointe')) {
            $validated['piece_jointe'] = $request->file('piece_jointe')->store('visites_techniques', 'public');
        }

        $visite = VisiteTechnique::create($validated);
        
        // Sync with vehicle table for dashboard alerts
        $visite->vehicle->update(['visite_tech_expiry' => $validated['date_expiration']]);

        return redirect()->route('materiel.visites.index')
            ->with('success', 'Visite technique enregistrée avec succès.');
    }

    public function show(VisiteTechnique $visite)
    {
        return view('materiel.visites.show', compact('visite'));
    }

    public function edit(VisiteTechnique $visite)
    {
        $vehicules = $this->getVehiclesForVisites();
        return view('materiel.visites.edit', compact('visite', 'vehicules'));
    }

    public function update(Request $request, VisiteTechnique $visite)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date_visite' => 'required|date',
            'date_expiration' => 'required|date|after:date_visite',
            'centre_visite' => 'nullable|string|max:255',
            'numero_certificat' => 'nullable|string|max:255',
            'cout' => 'nullable|numeric|min:0',
            'commentaires' => 'nullable|string',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048'
        ]);

        if ($request->hasFile('piece_jointe')) {
            $validated['piece_jointe'] = $request->file('piece_jointe')->store('visites_techniques', 'public');
        }

        $visite->update($validated);
        
        // Sync with vehicle table for dashboard alerts
        $visite->vehicle->update(['visite_tech_expiry' => $validated['date_expiration']]);

        return redirect()->route('materiel.visites.index')
            ->with('success', 'Visite technique mise à jour avec succès.');
    }

    public function destroy(VisiteTechnique $visite)
    {
        $visite->delete();
        return redirect()->route('materiel.visites.index')
            ->with('success', 'Visite technique supprimée avec succès.');
    }

    /**
     * Retourne la flotte complete pour les visites techniques.
     *
     * Le module materiel historique stocke les engins dans la table `vehicules`
     * tandis que les visites techniques pointent sur `vehicles`.
     * On synchronise ici les entrees manquantes pour exposer toute la flotte.
     */
    private function getVehiclesForVisites()
    {
        $vehiculesLegacy = Vehicule::query()
            ->select([
                'immatriculation',
                'marque',
                'modele',
                'annee',
                'type_materiel',
                'disponible',
                'kilometrage',
                'date_achat',
                'prix_achat',
            ])
            ->whereNotNull('immatriculation')
            ->where('immatriculation', '!=', '')
            ->get();

        foreach ($vehiculesLegacy as $legacy) {
            $legacyType = strtolower(trim((string) ($legacy->type_materiel ?? '')));
            $mappedType = match ($legacyType) {
                'camion' => 'camion',
                'moto' => 'moto',
                'vehicule', 'véhicule', 'voiture' => 'voiture',
                'engin', 'machine' => 'utilitaire',
                default => 'autre',
            };

            $annee = (int) ($legacy->annee ?? 0);
            $anneeMin = 1900;
            $anneeMax = (int) date('Y') + 1;
            if ($annee < $anneeMin || $annee > $anneeMax) {
                $annee = (int) date('Y');
            }

            $marque = trim((string) ($legacy->marque ?? ''));
            if ($marque === '') {
                $marque = 'N/A';
            }

            $modele = trim((string) ($legacy->modele ?? ''));
            if ($modele === '') {
                $modele = 'N/A';
            }

            Vehicle::firstOrCreate(
                ['immatriculation' => $legacy->immatriculation],
                [
                    'marque' => $marque,
                    'modele' => $modele,
                    'annee' => $annee,
                    'type' => $mappedType,
                    'etat' => 'bon',
                    'disponibilite' => (bool) $legacy->disponible,
                    'kilometrage' => max(0, (int) ($legacy->kilometrage ?? 0)),
                    'date_achat' => $legacy->date_achat,
                    'prix_achat' => $legacy->prix_achat,
                    'description' => 'Synchronise automatiquement depuis la flotte materiel roulant.',
                ]
            );
        }

        return Vehicle::query()
            ->orderBy('type')
            ->orderBy('immatriculation')
            ->get();
    }
}
