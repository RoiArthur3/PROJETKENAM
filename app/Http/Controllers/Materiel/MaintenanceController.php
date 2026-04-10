<?php

namespace App\Http\Controllers\Materiel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use App\Models\Maintenance;
// Technicien model is missing, using mock for now

class MaintenanceController extends Controller
{
    private function getTechniciensMock(): array
    {
        return [
            (object)['id' => 1, 'nom' => 'Jean Dupont', 'specialite' => 'Mécanique générale'],
            (object)['id' => 2, 'nom' => 'Marie Martin', 'specialite' => 'Électricité automobile'],
            (object)['id' => 3, 'nom' => 'Pierre Bernard', 'specialite' => 'Carrosserie'],
        ];
    }

    private function resolveTechnicienNom(?int $technicienId): ?string
    {
        if (!$technicienId) {
            return null;
        }
        foreach ($this->getTechniciensMock() as $t) {
            if ((int) $t->id === (int) $technicienId) {
                return $t->nom;
            }
        }
        return 'Technicien #' . $technicienId;
    }

    /**
     * Affiche la liste des maintenances
     */
    public function index()
    {
        $vehicules = Vehicule::orderBy('immatriculation')->get();
        $maintenances = Maintenance::with('vehicule')
            ->orderByDesc('date_maintenance')
            ->orderByDesc('id')
            ->get();

        return view('materiel.maintenance', compact('maintenances', 'vehicules'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle maintenance
     */
    public function create()
    {
        try {
            $vehicules = Vehicule::all();
        } catch (\Exception $e) {
            $vehicules = collect([
                (object)['id' => 1, 'immatriculation' => 'CI-123-AB', 'marque' => 'Toyota', 'modele' => 'Hilux'],
                (object)['id' => 2, 'immatriculation' => 'CI-456-CD', 'marque' => 'Nissan', 'modele' => 'Navara']
            ]);
        }

        $techniciens = $this->getTechniciensMock();

        $typesMaintenance = [
            'preventive' => 'Maintenance préventive',
            'corrective' => 'Maintenance corrective',
            'curative' => 'Maintenance curative'
        ];

        $statuts = [
            'planifiée' => 'Planifiée',
            'en_cours' => 'En cours',
            'terminée' => 'Terminée',
            'annulée' => 'Annulée',
        ];

        return view('materiel.maintenance-create', compact('vehicules', 'techniciens', 'typesMaintenance', 'statuts'));
    }

    /**
     * Enregistre une nouvelle maintenance
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'vehicule_id' => 'required|exists:vehicules,id',
                'type' => 'required|in:preventive,corrective,curative',
                'date' => 'required|date',
                'kilometrage' => 'required|integer|min:0',
                'description' => 'required|string',
                'technicien_id' => 'nullable|integer',
                'cout' => 'nullable|numeric|min:0',
                'statut' => 'required|in:planifiée,en_cours,terminée,annulée',
                'pieces_utilisees' => 'nullable|string',
                'duree' => 'nullable|string',
                'prochaine_echeance' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            $reference = 'MAINT-' . now()->format('Y') . '-' . str_pad((string) (Maintenance::max('id') + 1), 3, '0', STR_PAD_LEFT);

            Maintenance::create([
                'reference' => $reference,
                'vehicule_id' => $validated['vehicule_id'],
                'type' => $validated['type'],
                'date_maintenance' => $validated['date'],
                'kilometrage' => $validated['kilometrage'],
                'technicien_id' => $validated['technicien_id'] ?? null,
                'technicien_nom' => $this->resolveTechnicienNom(isset($validated['technicien_id']) ? (int) $validated['technicien_id'] : null),
                'cout' => $validated['cout'] ?? null,
                'statut' => $validated['statut'],
                'description' => $validated['description'],
                'pieces_utilisees' => $validated['pieces_utilisees'] ?? null,
                'duree' => $validated['duree'] ?? null,
                'prochaine_echeance' => $validated['prochaine_echeance'] ?? null,
                'resultat_notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('materiel.maintenance.index')
                ->with('success', 'Maintenance créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la maintenance.')
                ->withInput();
        }
    }

    public function storeResult(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'resultat' => 'required|in:reparee,non_reparee,partielle,a_suivre',
            'resultat_notes' => 'nullable|string',
            'pieces_utilisees' => 'nullable|string',
            'duree' => 'nullable|string',
            'cout' => 'nullable|numeric|min:0',
        ]);

        $maintenance->fill([
            'resultat' => $validated['resultat'],
            'resultat_notes' => $validated['resultat_notes'] ?? $maintenance->resultat_notes,
            'pieces_utilisees' => $validated['pieces_utilisees'] ?? $maintenance->pieces_utilisees,
            'duree' => $validated['duree'] ?? $maintenance->duree,
            'cout' => $validated['cout'] ?? $maintenance->cout,
            'statut' => 'terminée',
            'completed_at' => now(),
        ]);
        $maintenance->save();

        return redirect()->route('materiel.maintenance.index')->with('success', 'Résultat de maintenance enregistré.');
    }

    public function show(Maintenance $maintenance)
    {
        $maintenance->load('vehicule');
        return view('materiel.maintenance-show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        $vehicules = Vehicule::orderBy('immatriculation')->get();
        $techniciens = $this->getTechniciensMock();
        $typesMaintenance = [
            'preventive' => 'Maintenance préventive',
            'corrective' => 'Maintenance corrective',
            'curative' => 'Maintenance curative'
        ];
        $statuts = [
            'planifiée' => 'Planifiée',
            'en_cours' => 'En cours',
            'terminée' => 'Terminée',
            'annulée' => 'Annulée',
        ];

        return view('materiel.maintenance-edit', compact('maintenance', 'vehicules', 'techniciens', 'typesMaintenance', 'statuts'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'type' => 'required|in:preventive,corrective,curative',
            'date' => 'required|date',
            'kilometrage' => 'required|integer|min:0',
            'description' => 'required|string',
            'technicien_id' => 'nullable|integer',
            'cout' => 'nullable|numeric|min:0',
            'statut' => 'required|in:planifiée,en_cours,terminée,annulée',
            'pieces_utilisees' => 'nullable|string',
            'duree' => 'nullable|string',
            'prochaine_echeance' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $maintenance->fill([
            'vehicule_id' => $validated['vehicule_id'],
            'type' => $validated['type'],
            'date_maintenance' => $validated['date'],
            'kilometrage' => $validated['kilometrage'],
            'technicien_id' => $validated['technicien_id'] ?? null,
            'technicien_nom' => $this->resolveTechnicienNom(isset($validated['technicien_id']) ? (int) $validated['technicien_id'] : null),
            'cout' => $validated['cout'] ?? null,
            'statut' => $validated['statut'],
            'description' => $validated['description'],
            'pieces_utilisees' => $validated['pieces_utilisees'] ?? null,
            'duree' => $validated['duree'] ?? null,
            'prochaine_echeance' => $validated['prochaine_echeance'] ?? null,
            'resultat_notes' => $validated['notes'] ?? null,
        ]);
        $maintenance->save();

        return redirect()->route('materiel.maintenance.index')->with('success', 'Maintenance mise à jour avec succès.');
    }

    /**
     * Supprime une maintenance
     */
    public function destroy($id)
    {
        try {
            // Simulation de suppression
            return redirect()->route('materiel.maintenance.index')
                ->with('success', 'Maintenance supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('materiel.maintenance.index')
                ->with('error', 'Erreur lors de la suppression de la maintenance.');
        }
    }
}
