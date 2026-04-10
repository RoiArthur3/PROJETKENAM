<?php

namespace App\Http\Controllers\Materiel;

use App\Http\Controllers\Controller;
use App\Models\CarburantAppoint;
use App\Models\Vehicule;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class MaterielController extends Controller
{
    /**
     * Display the materiel dashboard.
     * Redirigé vers le dashboard Fleet (Parc Auto) pour unification.
     */
    public function dashboard()
    {
        return redirect()->route('fleet.dashboard');
    }

    public function vehicules()
    {
        try {
            return redirect()->route('vehicules.index');
        } catch (\Exception $e) {
            return view('materiel.vehicules', ['vehicules' => collect([])]);
        }
    }

    public function maintenance()
    {
        try {
            return app(MaintenanceController::class)->index();
        } catch (\Exception $e) {
            return view('materiel.maintenance', ['maintenances' => collect([])]);
        }
    }

    public function carburant()
    {
        try {
            return app(CarburantController::class)->index();
        } catch (\Exception $e) {
            return view('materiel.carburant', ['carburants' => collect([])]);
        }
    }

    public function rapports()
    {
        try {
            // Initialiser les variables
            $rapports = collect([]);
            $stats = [
                'total_vehicules' => 0,
                'vehicules_actifs' => 0,
                'total_maintenances' => 0,
                'maintenances_en_cours' => 0,
                'total_carburant' => 0,
                'carburant_mois' => 0,
                'cout_total' => 0,
                'operations_recentes' => 0,
            ];

            // Récupérer les véhicules si le modèle existe
            if (class_exists('App\Models\Vehicule')) {
                $vehicules = \App\Models\Vehicule::all();
                $stats['total_vehicules'] = $vehicules->count();
                $stats['vehicules_actifs'] = $vehicules->where('est_actif', true)->count();
            }

            // Récupérer les maintenances si le modèle existe
            if (class_exists('App\Models\Maintenance')) {
                $maintenances = \App\Models\Maintenance::all();
                $stats['total_maintenances'] = $maintenances->count();
                $stats['maintenances_en_cours'] = $maintenances->where('statut', 'en_cours')->count();
            }

            // Récupérer les données de carburant si le modèle existe
            if (class_exists('App\Models\CarburantAppoint')) {
                $carburants = \App\Models\CarburantAppoint::all();
                $stats['total_carburant'] = $carburants->count();
                $stats['carburant_mois'] = $carburants->whereMonth('date', now()->month)->count();
                $stats['cout_total'] = $carburants->sum('montant');
            }

            // Créer des rapports factices pour la démo
            $rapports = collect([
                (object)[
                    'id' => 1,
                    'titre' => 'Rapport Mensuel - Janvier 2026',
                    'type' => 'Mensuel',
                    'periode' => 'Janvier 2026',
                    'date_generation' => now()->subDays(2),
                    'statut' => 'généré',
                    'fichier' => 'rapport_janvier_2026.pdf',
                    'taille' => '2.3 MB',
                    'description' => 'Rapport mensuel des opérations matérielles',
                    'utilisateur' => 'Admin'
                ],
                (object)[
                    'id' => 2,
                    'titre' => 'Rapport Trimestriel - Q4 2025',
                    'type' => 'Trimestriel',
                    'periode' => 'Octobre - Décembre 2025',
                    'date_generation' => now()->subDays(15),
                    'statut' => 'généré',
                    'fichier' => 'rapport_q4_2025.pdf',
                    'taille' => '5.1 MB',
                    'description' => 'Rapport trimestriel des performances matérielles',
                    'utilisateur' => 'Admin'
                ],
                (object)[
                    'id' => 3,
                    'titre' => 'Rapport Annuel - 2025',
                    'type' => 'Annuel',
                    'periode' => 'Année 2025',
                    'date_generation' => now()->subDays(30),
                    'statut' => 'généré',
                    'fichier' => 'rapport_annuel_2025.pdf',
                    'taille' => '8.7 MB',
                    'description' => 'Rapport annuel complet du matériel roulant',
                    'utilisateur' => 'Admin'
                ],
                (object)[
                    'id' => 4,
                    'titre' => 'Rapport d\'Inventaire - Décembre 2025',
                    'type' => 'Inventaire',
                    'periode' => 'Décembre 2025',
                    'date_generation' => now()->subDays(5),
                    'statut' => 'généré',
                    'fichier' => 'inventaire_decembre_2025.pdf',
                    'taille' => '1.2 MB',
                    'description' => 'Rapport d\'inventaire du matériel',
                    'utilisateur' => 'Admin'
                ]
            ]);

            return view('materiel.rapports', compact('rapports', 'stats'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données vides
            $rapports = collect([]);
            $stats = [
                'total_vehicules' => 0,
                'vehicules_actifs' => 0,
                'total_maintenances' => 0,
                'maintenances_en_cours' => 0,
                'total_carburant' => 0,
                'carburant_mois' => 0,
                'cout_total' => 0,
                'operations_recentes' => 0,
            ];

            return view('materiel.rapports', compact('rapports', 'stats'));
        }
    }
}
