<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entretien;
use App\Models\Assurance;
use App\Models\VehicleAssignment;
use Illuminate\Support\Collection;

class ParcHistoriqueController extends Controller
{
    public function index(Request $request)
    {
        $historiques = new Collection();

        // Ajouter les entretiens à l'historique
        $entretiens = Entretien::with('vehicule')->get();
        foreach ($entretiens as $entretien) {
            $historiques->push([
                'date' => $entretien->date,
                'vehicule' => $entretien->vehicule->immatriculation ?? 'N/A',
                'type' => 'Entretien',
                'description' => $entretien->type . ' - ' . $entretien->description,
                'montant' => $entretien->cout,
                'responsable' => $entretien->user->name ?? 'N/A',
            ]);
        }

        // Ajouter les assurances à l'historique
        $assurances = Assurance::with('vehicle')->get();
        foreach ($assurances as $assurance) {
            $historiques->push([
                'date' => $assurance->date_debut,
                'vehicule' => $assurance->vehicle->immatriculation ?? 'N/A',
                'type' => 'Assurance',
                'description' => 'Police ' . $assurance->numero_police . ' chez ' . $assurance->assureur,
                'montant' => $assurance->prime_annuelle,
                'responsable' => 'N/A',
            ]);
        }

        // Ajouter les affectations à l'historique
        $affectations = VehicleAssignment::with('vehicle', 'driver')->get();
        foreach ($affectations as $affectation) {
            $historiques->push([
                'date' => $affectation->assigned_at,
                'vehicule' => $affectation->vehicle->immatriculation ?? 'N/A',
                'type' => 'Affectation',
                'description' => 'Affecté à ' . ($affectation->driver->name ?? 'N/A') . ' pour la mission: ' . $affectation->mission,
                'montant' => null,
                'responsable' => 'N/A',
            ]);
        }

        // Trier par date
        $historiques = $historiques->sortByDesc('date');

        return view('parc.historiques', compact('historiques'));
    }
}
