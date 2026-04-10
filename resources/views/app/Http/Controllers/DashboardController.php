<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Vehicle;
use App\Models\VehicleMission;
use App\Models\Operation;
use App\Models\Produit;
use App\Models\CommandeFournisseur;
use App\Models\Caisse;
use App\Models\CompteBancaire;
use App\Models\EcritureComptable;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Restriction pour les agents
        if (auth()->check() && auth()->user()->role === 'agent') {
            return redirect()->route('operations.index')->with('info', 'Vous avez été redirigé vers la liste des opérations car vous n\'avez pas accès au tableau de bord central.');
        }

        try {
            $stats = $this->getStats();
            $charts = $this->getChartsData();
            $alerts = $this->getAlerts();
            $recentData = $this->getRecentActivity();
            $tresorerie = $this->getTreasuryStats();
            $comptabilite = $this->getAccountingStats();
            $magasin = $this->getMagasinStats();

            return view('dashboard-simple', compact('stats', 'charts', 'alerts', 'recentData', 'tresorerie', 'comptabilite', 'magasin'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un dashboard simplifié
            return view('dashboard-simple', [
                'stats' => [],
                'charts' => [],
                'alerts' => [],
                'recentData' => [],
                'tresorerie' => ['solde_caisses' => 0, 'solde_banques' => 0, 'total' => 0],
                'comptabilite' => ['revenus_mois' => 0, 'depenses_mois' => 0],
                'magasin' => ['total_produits' => 0, 'alertes_stock' => 0, 'valeur_stock' => 0, 'entrees_mois' => 0, 'sorties_mois' => 0],
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getStats()
    {
        try {
            // Chiffre d'affaires avec gestion d'erreur
            $ca_jour = 0;
            $ca_mois = 0;
            $ca_annee = 0;

            if (class_exists('App\Models\Facture')) {
                try {
                    $ca_jour = Facture::whereDate('date_facture', today())->whereIn('statut', ['payee', 'payée'])->sum('montant_ttc');
                    $ca_mois = Facture::whereMonth('date_facture', now()->month)->whereIn('statut', ['payee', 'payée'])->sum('montant_ttc');
                    $ca_annee = Facture::whereYear('date_facture', now()->year)->whereIn('statut', ['payee', 'payée'])->sum('montant_ttc');
                } catch (\Exception $e) {
                    // Garder les valeurs à 0 si erreur
                }
            }

            // Missions et véhicules avec gestion d'erreur
            $missions_actives = 0;
            $missions_mois = 0;
            $vehicules_dispo = 0;
            $vehicules_total = 0;

            try {
                if (\Schema::hasTable('vehicle_missions')) {
                    $missions_actives = DB::table('vehicle_missions')->where('status', 'en_cours')->count();
                    $missions_mois = DB::table('vehicle_missions')->whereMonth('created_at', now()->month)->count();
                } else {
                    // Utiliser les opérations en cours comme proxy pour les missions
                    $missions_actives = Operation::whereIn('statut_courant', ['en_cours', 'pending_validation', 'en_validation'])->count();
                    $missions_mois = Operation::whereMonth('date_operation', now()->month)->count();
                }
            } catch (\Exception $e) {}

            try {
                $vehicules_total = DB::table('vehicules')->count();
                $vehicules_dispo = DB::table('vehicules')->where('statut', 'disponible')->count();
                if ($vehicules_dispo == 0 && $vehicules_total > 0) {
                    // Fallback si la colonne statut n'existe pas ou a d'autres valeurs
                    $vehicules_dispo = DB::table('vehicules')->where('disponibilite', true)->count();
                }
            } catch (\Exception $e) {}

            // Clients et fournisseurs
            $clients_total = 0;
            $fournisseurs_total = 0;
            $validations_en_attente = 0;

            try {
                $clients_total = DB::table('clients')->count();
            } catch (\Exception $e) {}

            try {
                $fournisseurs_total = DB::table('fournisseurs')->count();
            } catch (\Exception $e) {}

            try {
                $validations_en_attente = Operation::whereIn('statut_courant', ['pending_validation', 'en_attente', 'en_validation'])->count();
            } catch (\Exception $e) {}

            // Factures impayées avec gestion d'erreur
            $factures_impayees_count = 0;
            $factures_impayees_somme = 0;

            if (class_exists('App\Models\Facture')) {
                try {
                    $factures_impayees_count = Facture::whereIn('statut', ['impayee', 'impayée'])->count();
                    $factures_impayees_somme = Facture::whereIn('statut', ['impayee', 'impayée'])->sum('montant_ttc');
                } catch (\Exception $e) {}
            }

            // Opérations avec gestion d'erreur
            $operations_mois = 0;
            $operations_total = 0;

            if (class_exists('App\Models\Operation')) {
                try {
                    $operations_mois = Operation::whereMonth('date_operation', now()->month)->count();
                    $operations_total = Operation::count();
                } catch (\Exception $e) {}
            }

            return [
                'ca' => [
                    'jour' => $ca_jour,
                    'mois' => $ca_mois,
                    'annee' => $ca_annee,
                ],
                'missions' => [
                    'actives' => $missions_actives,
                    'mois' => $missions_mois,
                ],
                'parc' => [
                    'dispo' => $vehicules_dispo,
                    'total' => $vehicules_total,
                    'taux_utilisation' => $vehicules_total > 0 ? round((($vehicules_total - $vehicules_dispo) / $vehicules_total) * 100, 1) : 0,
                ],
                'factures_impayees' => [
                    'count' => $factures_impayees_count,
                    'total' => $factures_impayees_somme,
                ],
                'operations' => [
                    'mois' => $operations_mois,
                    'total' => $operations_total,
                ],
                'clients' => [
                    'total' => $clients_total,
                ],
                'fournisseurs' => [
                    'total' => $fournisseurs_total,
                ],
                'validations' => [
                    'en_attente' => $validations_en_attente,
                ],
            ];
        } catch (\Exception $e) {
            // Retourner des valeurs par défaut en cas d'erreur
            return [
                'ca' => ['jour' => 0, 'mois' => 0, 'annee' => 0],
                'missions' => ['actives' => 0, 'mois' => 0],
                'parc' => ['dispo' => 0, 'total' => 0, 'taux_utilisation' => 0],
                'factures_impayees' => ['count' => 0, 'total' => 0],
                'operations' => ['mois' => 0, 'total' => 0],
                'clients' => ['total' => 0],
                'fournisseurs' => ['total' => 0],
                'validations' => ['en_attente' => 0],
            ];
        }
    }

    private function getChartsData()
    {
        // CA Mensuel sur les 12 derniers mois
        $ca_mensuel = [];
        $labels = [];

        try {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->translatedFormat('M');
                $ca_mensuel[] = Facture::whereMonth('date_facture', $date->month)
                    ->whereYear('date_facture', $date->year)
                    ->whereIn('statut', ['payee', 'payée'])
                    ->sum('montant_ttc');
            }
        } catch (\Exception $e) {
            $labels = [];
            $ca_mensuel = [];
            for ($i = 11; $i >= 0; $i--) {
                $labels[] = now()->subMonths($i)->translatedFormat('M');
                $ca_mensuel[] = 0;
            }
        }

        // Utilisation Engins (Pie Chart)
        $util_data = [0, 0, 0]; // Dispo, En mission, Maintenance
        try {
            $vehicules_total = DB::table('vehicules')->count();
            $vehicules_dispo = DB::table('vehicules')->where('statut', 'disponible')->count();
            if ($vehicules_dispo == 0 && $vehicules_total > 0) {
                $vehicules_dispo = DB::table('vehicules')->where('disponibilite', true)->count();
            }
            $en_mission = 0;
            if (\Schema::hasTable('vehicle_missions')) {
                $en_mission = DB::table('vehicle_missions')->where('status', 'en_cours')->count();
            }
            $maintenance = $vehicules_total - $vehicules_dispo - $en_mission;
            if ($maintenance < 0) $maintenance = 0;
            $util_data = [$vehicules_dispo, $en_mission, $maintenance];
        } catch (\Exception $e) {}

        return [
            'ca_labels' => $labels,
            'ca_data' => $ca_mensuel,
            'util_data' => $util_data,
        ];
    }

    private function getAlerts()
    {
        try {
            $factures_echues = 0;
            try {
                $factures_echues = Facture::whereIn('statut', ['impayee', 'impayée'])
                    ->where('date_facture', '<', now()->subDays(7))
                    ->count();
            } catch (\Exception $e) {}

            $stock_faible = 0;
            try {
                $stock_faible = Produit::where('stock_actuel', '<=', DB::raw('stock_min'))->count();
            } catch (\Exception $e) {}

            $assurances_expirees = 0;
            try {
                if (\Schema::hasTable('assurances')) {
                    $assurances_expirees = DB::table('assurances')
                        ->where('date_fin', '<', now())
                        ->where('statut', '!=', 'annulee')
                        ->count();
                }
            } catch (\Exception $e) {}

            $retards_retour = 0;
            try {
                // Opérations en retard (échéance dépassée et non terminées)
                $retards_retour = Operation::where('echeance', '<', now())
                    ->whereNotIn('statut_courant', ['terminee', 'termine', 'approuvee', 'approuve', 'rejetee'])
                    ->count();
            } catch (\Exception $e) {}

            return [
                'factures_echues' => $factures_echues,
                'retards_retour' => $retards_retour,
                'stock_faible' => $stock_faible,
                'assurances_expirees' => $assurances_expirees,
            ];
        } catch (\Exception $e) {
            return [
                'factures_echues' => 0,
                'retards_retour' => 0,
                'stock_faible' => 0,
                'assurances_expirees' => 0,
            ];
        }
    }

    private function getRecentActivity()
    {
        $factures = collect();
        $missions = collect();
        $commandes = collect();

        try {
            $factures = Facture::with('client')->latest()->take(5)->get();
        } catch (\Exception $e) {}

        try {
            // Dernières opérations actives comme "missions"
            $missions = Operation::whereIn('statut_courant', ['en_cours', 'pending_validation', 'en_validation'])
                ->latest('date_operation')
                ->take(5)
                ->get();
        } catch (\Exception $e) {}

        try {
            $commandes = CommandeFournisseur::with('fournisseur')->where('statut', 'en_attente')->take(5)->get();
        } catch (\Exception $e) {}

        return [
            'factures' => $factures,
            'missions' => $missions,
            'commandes' => $commandes,
        ];
    }

    private function getTreasuryStats()
    {
        try {
            // Solde des caisses actives
            $soldeCaisses = \App\Models\Caisse::where('est_active', true)->sum('solde_actuel') ?? 0;

            // Solde des comptes bancaires (si la table existe)
            $soldeBanques = 0;
            try {
                if (class_exists('\App\Models\CompteBancaire')) {
                    $soldeBanques = \App\Models\CompteBancaire::sum('solde') ?? 0;
                }
            } catch (\Exception $e) {
                // Table n'existe pas encore
            }

            // Approvisionnements du mois
            $approvisionnementsMonth = \App\Models\ApprovisionnementCaisse::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('montant') ?? 0;

            // Dépenses du mois
            $depensesMonth = \App\Models\DepenseCaisse::whereMonth('date_depense', now()->month)
                ->whereYear('date_depense', now()->year)
                ->sum('montant') ?? 0;

            return [
                'solde_caisses' => $soldeCaisses,
                'solde_banques' => $soldeBanques,
                'total' => $soldeCaisses + $soldeBanques,
                'approvisionnements_mois' => $approvisionnementsMonth,
                'depenses_mois' => $depensesMonth,
                'variation_mois' => $approvisionnementsMonth - $depensesMonth,
                'nombre_caisses' => \App\Models\Caisse::where('est_active', true)->count(),
                'approvisionnements_en_attente' => \App\Models\ApprovisionnementCaisse::where('statut', 'en_attente')->count(),
            ];
        } catch (\Exception $e) {
            return [
                'solde_caisses' => 0,
                'solde_banques' => 0,
                'total' => 0,
                'approvisionnements_mois' => 0,
                'depenses_mois' => 0,
                'variation_mois' => 0,
                'nombre_caisses' => 0,
                'approvisionnements_en_attente' => 0,
            ];
        }
    }

    private function getAccountingStats()
    {
        try {
            // Revenus du mois (factures payées)
            $revenusMois = 0;
            if (class_exists('\App\Models\Facture')) {
                $revenusMois = \App\Models\Facture::whereMonth('date_facture', now()->month)
                    ->whereYear('date_facture', now()->year)
                    ->whereIn('statut', ['payee', 'payée'])
                    ->sum('montant_ttc') ?? 0;
            }

            // Dépenses du mois (depuis la trésorerie)
            $depensesMois = \App\Models\DepenseCaisse::whereMonth('date_depense', now()->month)
                ->whereYear('date_depense', now()->year)
                ->sum('montant') ?? 0;

            // Écritures comptables (si la table existe)
            $ecrituresCount = 0;
            try {
                if (class_exists('\App\Models\EcritureComptable')) {
                    $ecrituresCount = \App\Models\EcritureComptable::whereMonth('created_at', now()->month)->count();
                }
            } catch (\Exception $e) {}

            // Factures impayées
            $facturesImpayees = 0;
            $montantImpaye = 0;
            if (class_exists('\App\Models\Facture')) {
                $facturesImpayees = \App\Models\Facture::whereIn('statut', ['impayee', 'impayée'])->count();
                $montantImpaye = \App\Models\Facture::whereIn('statut', ['impayee', 'impayée'])->sum('montant_ttc') ?? 0;
            }

            return [
                'revenus_mois' => $revenusMois,
                'depenses_mois' => $depensesMois,
                'resultat_mois' => $revenusMois - $depensesMois,
                'ecritures_mois' => $ecrituresCount,
                'factures_impayees' => $facturesImpayees,
                'montant_impaye' => $montantImpaye,
            ];
        } catch (\Exception $e) {
            return [
                'revenus_mois' => 0,
                'depenses_mois' => 0,
                'resultat_mois' => 0,
                'ecritures_mois' => 0,
                'factures_impayees' => 0,
                'montant_impaye' => 0,
            ];
        }
    }

    private function getMagasinStats()
    {
        try {
            $total_produits = DB::table('produits')->count();
            $alertes_stock = DB::table('produits')->whereRaw('stock_actuel <= stock_min')->count();
            $valeur_stock = DB::table('produits')->sum(DB::raw('prix_unitaire * stock_actuel'));

            $entrees_mois = 0;
            $sorties_mois = 0;
            try {
                $entrees_mois = DB::table('stock_entrees')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();
            } catch (\Exception $e) {}

            try {
                $sorties_mois = DB::table('stock_sorties')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();
            } catch (\Exception $e) {}

            $categories = DB::table('produits')->distinct()->count('categorie');

            return [
                'total_produits' => $total_produits,
                'alertes_stock' => $alertes_stock,
                'valeur_stock' => $valeur_stock,
                'entrees_mois' => $entrees_mois,
                'sorties_mois' => $sorties_mois,
                'categories' => $categories,
            ];
        } catch (\Exception $e) {
            return [
                'total_produits' => 0,
                'alertes_stock' => 0,
                'valeur_stock' => 0,
                'entrees_mois' => 0,
                'sorties_mois' => 0,
                'categories' => 0,
            ];
        }
    }

    public function stats()
    {
        try {
            return response()->json([
                'operations' => [
                    'count' => Operation::count(),
                    'mois' => Operation::whereMonth('created_at', now()->month)->count(),
                ],
                'maintenance' => [
                    'count' => 0, // À implémenter si nécessaire
                ],
                'controles' => [
                    'count' => 0, // À implémenter si nécessaire
                ],
                'formations' => [
                    'count' => 0, // À implémenter si nécessaire
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'operations' => ['count' => 0, 'mois' => 0],
                'maintenance' => ['count' => 0],
                'controles' => ['count' => 0],
                'formations' => ['count' => 0]
            ]);
        }
    }

    public function kpis()
    {
        try {
            return response()->json([
                'operations_today' => Operation::whereDate('created_at', today())->count(),
                'vehicles_available' => Vehicle::where('disponible', true)->count(),
                'vehicles_total' => Vehicle::count(),
                'agents_present' => 0, // À implémenter avec la table de pointage
                'agents_total' => 0, // À implémenter avec la table users
                'invoices_paid' => Facture::where('statut', 'payee')->count(),
                'invoices_pending' => Facture::where('statut', 'impayee')->count(),
                'stock_alerts' => Produit::where('stock_actuel', '<=', 'stock_min')->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'operations_today' => 0,
                'vehicles_available' => 0,
                'vehicles_total' => 0,
                'agents_present' => 0,
                'agents_total' => 0,
                'invoices_paid' => 0,
                'invoices_pending' => 0,
                'stock_alerts' => 0,
            ]);
        }
    }

    public function chart($type)
    {
        try {
            if ($type === 'operations') {
                // Données mensuelles pour les opérations
                $data = [];
                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $data[] = [
                        'month' => $date->format('M'),
                        'count' => Operation::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count()
                    ];
                }
                return response()->json($data);
            }

            return response()->json([]);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
