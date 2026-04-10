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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard Global Détaillé
     */
    public function globalDashboard()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'superadmin') {
            return redirect($user ? $user->getFirstAccessibleModuleUrl() : '/login')
                ->with('error', 'Accès refusé au tableau de bord général.');
        }

        try {
            $comptabilite = $this->getDetailedAccountingStats();
            $rh = $this->getRHStats();
            $fournisseurs = $this->getFournisseursStats();
            $operations = $this->getOperationsStats();
            $clients = $this->getClientsStats();
            $magasin = $this->getMagasinStats();
            $vehicules = $this->getVehiculesStats();
            $alerts = $this->getAlerts();

            // Récupérer les caisses pour le dashboard
            $caisses = \App\Models\Caisse::all();

            return view('dashboard.global', compact('comptabilite', 'rh', 'fournisseurs', 'operations', 'clients', 'magasin', 'vehicules', 'caisses', 'alerts'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les statistiques détaillées de comptabilité
     */
    private function getDetailedAccountingStats()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Factures - CORRECTIF: utiliser les valeurs d'énumération réelles (avec accents)
        $factures_total = DB::table('factures')->count() ?? 0;
        $factures_payees = DB::table('factures')->where('statut', 'payée')->count() ?? 0;
        $factures_en_attente = DB::table('factures')->where('statut', 'impayée')->count() ?? 0;
        // Factures en retard: combinaison de statut + date_echeance (si la colonne existe)
        $factures_en_retard = 0;
        if (Schema::hasColumn('factures', 'date_echeance')) {
            $factures_en_retard = DB::table('factures')
                ->where('statut', 'impayée')
                ->where('date_echeance', '<', now())
                ->count() ?? 0;
        }
        $factures_impayees = DB::table('factures')
            ->whereIn('statut', ['impayée', 'partiellement_payée'])
            ->count() ?? 0;
        $factures_annulees = DB::table('factures')->where('statut', 'annulée')->count() ?? 0;

        // CA Total (Somme des encaissements)
        $ca_total = DB::table('encaissements')
            ->sum('montant') ?? 0;

        // Montant encaissé (ce mois)
        $montant_encaisse = DB::table('encaissements')
            ->where('date_encaissement', '>=', $startOfMonth)
            ->where('date_encaissement', '<=', $endOfMonth)
            ->sum('montant') ?? 0;

        // Montant en attente - CORRECTIF: utiliser les valeurs réelles avec accents
        $montant_en_attente = DB::table('factures')
            ->whereIn('statut', ['impayée', 'partiellement_payée'])
            ->sum('montant_ttc') ?? 0;

        // Dépenses
        $depenses_total = DB::table('depense_caisses')
            ->sum('montant') ?? 0;

        // Trésorerie
        $solde_banque = DB::table('compte_bancaires')->sum('solde') ?? 0;
        $caisse = DB::table('caisses')->sum('solde_courant') ?? 0;
        $autres_comptes = 0; // À calculer selon vos besoins

        $solde_tresorerie = $solde_banque + $caisse + $autres_comptes;
        $resultat_estime = $ca_total - $depenses_total;
        $taux_recouvrement = $ca_total > 0 ? round(($montant_encaisse / $ca_total) * 100, 1) : 0;
        $liquidite_courte = $montant_en_attente > 0 ? round(($solde_tresorerie / $montant_en_attente) * 100, 1) : 0;

        // Calcul métier plateforme: priorité aux missions (CA client et coût fournisseur)
        $caReference = $ca_total;
        $coutDirectMissions = 0;
        if (Schema::hasTable('vehicle_missions')) {
            try {
                $caReference = DB::table('vehicle_missions')
                    ->where('status', '!=', 'canceled')
                    ->sum('total_client_amount') ?? $caReference;

                $coutDirectMissions = DB::table('vehicle_missions')
                    ->where('status', '!=', 'canceled')
                    ->sum('total_supplier_cost') ?? 0;
            } catch (\Exception $e) {
                Log::warning('Global dashboard mission metrics fallback', ['error' => $e->getMessage()]);
            }
        }

        $ebe = $caReference - ($coutDirectMissions + $depenses_total);
        $caf = $ebe;
        $dette_fournisseur = 0;
        if (Schema::hasTable('facture_fournisseurs')) {
            try {
                $supplierDebtQuery = DB::table('facture_fournisseurs');
                if (Schema::hasColumn('facture_fournisseurs', 'deleted_at')) {
                    $supplierDebtQuery->whereNull('deleted_at');
                }
                if (Schema::hasColumn('facture_fournisseurs', 'statut')) {
                    $supplierDebtQuery->whereIn('statut', ['non_payee', 'partiellement_payee']);
                }

                if (Schema::hasColumn('facture_fournisseurs', 'reste_a_payer')) {
                    $dette_fournisseur = $supplierDebtQuery->sum('reste_a_payer') ?? 0;
                } else {
                    $supplierTtc = (clone $supplierDebtQuery)->sum('montant_ttc') ?? 0;
                    $supplierPaid = (clone $supplierDebtQuery)->sum('montant_paye') ?? 0;
                    $dette_fournisseur = max(0, $supplierTtc - $supplierPaid);
                }
            } catch (\Exception $e) {
                Log::warning('Global dashboard supplier debt fallback', ['error' => $e->getMessage()]);
            }
        }

        // TRI métier (retour sur investissement opérationnel): marge mission / coût direct mission
        $tri = $coutDirectMissions > 0
            ? round((($caReference - $coutDirectMissions) / $coutDirectMissions) * 100, 2)
            : 0;

        return [
            'ca_total' => $ca_total,
            'ca_reference_metier' => $caReference,
            'cout_direct_missions' => $coutDirectMissions,
            'factures_total' => $factures_total,
            'factures_payees' => $factures_payees,
            'factures_en_attente' => $factures_en_attente,
            'factures_impayees' => $factures_impayees,
            'factures_annulees' => $factures_annulees,
            'montant_encaisse' => $montant_encaisse,
            'montant_en_attente' => $montant_en_attente,
            'depenses_total' => $depenses_total,
            'solde_banque' => $solde_banque,
            'caisse' => $caisse,
            'autres_comptes' => $autres_comptes,
            'solde_tresorerie' => $solde_tresorerie,
            'resultat_estime' => $resultat_estime,
            'taux_recouvrement' => $taux_recouvrement,
            'liquidite_courte' => $liquidite_courte,
            'ebe' => $ebe,
            'caf' => $caf,
            'dette_fournisseur' => $dette_fournisseur,
            'tri' => $tri,
        ];
    }

    /**
     * Récupère les statistiques RH
     */
    private function getRHStats()
    {
        return [
            'total_personnel' => DB::table('personnel')->count() ?? 0,
            'actifs' => DB::table('personnel')->where('statut', 'ACTIF')->where('date_depart', null)->count() ?? 0,
            'en_essai' => DB::table('personnel')
                ->where('statut', 'ACTIF')
                ->where('fin_periode_essai', '>', now())
                ->count() ?? 0,
            'resilles' => DB::table('personnel')
                ->whereIn('statut', ['DEMISSION', 'LICENCIE', 'RETRAITE'])
                ->count() ?? 0,
            'conges' => DB::table('personnel_conges')->where('statut', 'approuvee')->count() ?? 0,
        ];
    }

    /**
     * Récupère les statistiques Fournisseurs
     */
    private function getFournisseursStats()
    {
        return [
            'total_fournisseurs' => DB::table('fournisseurs')->count() ?? 0,
            'actifs' => DB::table('fournisseurs')->where('est_actif', true)->count() ?? 0,
            'commandes_en_cours' => Schema::hasTable('commande_fournisseur') ? DB::table('commande_fournisseur')->where('statut', 'en_cours')->count() ?? 0 : 0,
            'montant_total_commandes' => Schema::hasTable('commande_fournisseur') ? DB::table('commande_fournisseur')->sum('montant_total') ?? 0 : 0,
        ];
    }

    /**
     * Récupère les statistiques Opérations
     */
    private function getOperationsStats()
    {
        return [
            'total_operations' => DB::table('operations')->count() ?? 0,
            'en_cours' => DB::table('operations')->where('statut', 'en_cours')->count() ?? 0,
            'terminees' => DB::table('operations')->where('statut', 'terminee')->count() ?? 0,
            'revenue_total' => DB::table('operations')->sum('montant') ?? 0,
        ];
    }

    /**
     * Récupère les statistiques Clients
     */
    private function getClientsStats()
    {
        return [
            'total_clients' => DB::table('clients')->count() ?? 0,
            'actifs' => DB::table('clients')->where('statut', 'actif')->count() ?? 0,
            'commandes_en_attente' => Schema::hasTable('commandes') ? DB::table('commandes')->where('statut', 'en_attente')->count() ?? 0 : 0,
            'montant_total_commandes' => Schema::hasTable('commandes') ? DB::table('commandes')->sum('montant_total') ?? 0 : 0,
        ];
    }



    /**
     * Récupère les statistiques Véhicules
     */
    private function getVehiculesStats()
    {
        return [
            'total_vehicules' => DB::table('vehicules')->count() ?? 0,
            'disponibles' => DB::table('vehicules')->where('disponible', true)->count() ?? 0,
            'en_mission' => 0, // À implémenter quand statut_detail sera mis à jour
            'en_maintenance' => 0, // À implémenter quand statut_detail sera mis à jour
        ];
    }

    /**
     * Récupère les statistiques d'Assurances
     */
    private function getAssurancesStats()
    {
        try {
            $total_engins = DB::table('vehicules')->count() ?? 0;

            // Assurances expirant dans 2 mois (entre maintenant et dans 60 jours)
            $assurances_2mois = 0;
            if (Schema::hasTable('assurances')) {
                $assurances_2mois = DB::table('assurances')
                    ->where('date_fin', '>', now())
                    ->where('date_fin', '<=', now()->addDays(60))
                    ->where('statut', '!=', 'annulee')
                    ->count();
            }

            return [
                'total_engins' => $total_engins,
                'assurances_expiration_2mois' => $assurances_2mois,
            ];
        } catch (\Exception $e) {
            return [
                'total_engins' => 0,
                'assurances_expiration_2mois' => 0,
            ];
        }
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'superadmin') {
            return redirect($user ? $user->getFirstAccessibleModuleUrl() : '/login')
                ->with('error', 'Accès refusé au tableau de bord général.');
        }

        try {
            // Utiliser les vraies statistiques pour avoir les données complètes
            $stats = $this->getStats();
            $charts = $this->getChartsData();
            $alerts = $this->getAlerts();
            $tresorerie = $this->getTreasuryStats();
            $comptabilite = $this->getAccountingStats();
            $magasin = $this->getMagasinStats();
            $assurances = $this->getAssurancesStats();
            $previsionalTreasury = $this->getPrevisionalTreasuryStats();
            $smsStats = $this->getSMSStats();
            $expiringContracts = $this->getExpiringContractsCount();
            $operationsParStatut = $this->getOperationsParStatut();
            $operationsSixMois = $this->getOperationsSixDerniersMois();

            return view('dashboard', compact('stats', 'charts', 'alerts', 'tresorerie', 'comptabilite', 'magasin', 'assurances', 'previsionalTreasury', 'smsStats', 'expiringContracts', 'operationsParStatut', 'operationsSixMois'));
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

    /**
     * Version rapide des statistiques pour éviter les ralentissements
     */
    private function getStatsFast()
    {
        try {
            // Statistiques simples et rapides
            return [
                'ca_jour' => 0,
                'ca_mois' => 0,
                'ca_annee' => 0,
                'impaye_count' => 0,
                'impaye_somme' => 0,
                'validations_en_attente' => Operation::whereIn('statut_courant', ['pending_validation', 'en_validation'])->count(),
                'paiements_en_attente' => Operation::whereIn('statut_courant', ['Approuvé_en_attente_paiement', 'bon_pour_accord'])->count(),
                'montant_en_validation' => 0,
                'montant_a_payer' => 0,
                'montant_paye_mois' => 0,
                'missions_actives' => 0,
                'missions_mois' => 0,
                'montant_missions_mois' => 0,
                'cout_missions_mois' => 0,
                'marge_missions_mois' => 0,
                'vehicules_dispo' => 0,
                'vehicules_total' => 0,
                'clients_actifs' => 0,
                'fournisseurs_actifs' => 0,
                'factures_impayees' => 0,
                'depenses_mois' => 0,
                'stock_alertes' => 0,
                'taux_realisation' => 0,
                'marge_moyenne' => 0,
                'performance_equipes' => 0,
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getStats()
    {
        try {
            // 1. Chiffre d'affaires (Total des Factures Émises) - CONSOLIDÉ
            $ca_jour = 0;
            $ca_mois = 0;
            $ca_annee = 0;
            $ca_mois_label = 'Mois en cours';

            // FACTURES ÉMISES (Source principale du CA - montants facturés, pas seulement encaissés)
            if (class_exists('App\Models\Facture')) {
                try {
                    // Factures émises aujourd'hui
                    $ca_jour += \App\Models\Facture::whereDate('date_facture', today())->sum('montant_ttc') ?? 0;
                    // Factures émises ce mois
                    $ca_mois += \App\Models\Facture::whereMonth('date_facture', now()->month)->whereYear('date_facture', now()->year)->sum('montant_ttc') ?? 0;
                    // Factures émises cette année
                    $ca_annee += \App\Models\Facture::whereYear('date_facture', now()->year)->sum('montant_ttc') ?? 0;
                } catch (\Exception $e) {}
            }

            // Invoices (Factures alternatives) - complément au CA
            if (class_exists('App\Models\Invoice')) {
                try {
                    $invoiceTable = (new \App\Models\Invoice)->getTable();
                    $amountColumn = Schema::hasColumn($invoiceTable, 'total_amount_tax_incl') ? 'total_amount_tax_incl' : (Schema::hasColumn($invoiceTable, 'net_amount') ? 'net_amount' : 'total_amount');
                    $dateColumn = Schema::hasColumn($invoiceTable, 'invoice_date') ? 'invoice_date' : 'date_invoice';

                    if (Schema::hasColumn($invoiceTable, $dateColumn)) {
                        $ca_jour += \App\Models\Invoice::whereDate($dateColumn, today())->sum($amountColumn) ?? 0;
                        $ca_mois += \App\Models\Invoice::whereMonth($dateColumn, now()->month)->whereYear($dateColumn, now()->year)->sum($amountColumn) ?? 0;
                        $ca_annee += \App\Models\Invoice::whereYear($dateColumn, now()->year)->sum($amountColumn) ?? 0;
                    }
                } catch (\Exception $e) {}
            }

            // Fallback intelligent: si le mois courant est vide, afficher le dernier mois facturé
            if ($ca_mois <= 0 && class_exists('App\Models\Facture')) {
                try {
                    $dernierMoisFacture = \App\Models\Facture::query()
                        ->whereNotNull('date_facture')
                        ->orderByDesc('date_facture')
                        ->value('date_facture');

                    if ($dernierMoisFacture) {
                        $dernierMoisDate = Carbon::parse($dernierMoisFacture);
                        $caDernierMois = \App\Models\Facture::query()
                            ->whereMonth('date_facture', $dernierMoisDate->month)
                            ->whereYear('date_facture', $dernierMoisDate->year)
                            ->sum('montant_ttc') ?? 0;

                        if ($caDernierMois > 0) {
                            $ca_mois = $caDernierMois;
                            $ca_mois_label = 'Dernier mois facture (' . $dernierMoisDate->format('m/Y') . ')';
                        }
                    }
                } catch (\Exception $e) {}
            }

            // 2. Impayés / À Encaisser (Factures impayées + Recettes en attente)
            $impaye_count = 0;
            $impaye_somme = 0;
            try {
                if (class_exists('App\Models\Facture')) {
                    $factureImpayeesQuery = \App\Models\Facture::query()
                        ->where(function ($query) {
                            $query->whereNull('statut')
                                ->orWhereNotIn('statut', ['payee', 'payée', 'paye', 'paid', 'annulee', 'annulée', 'cancelled']);
                        });

                    $impaye_count += $factureImpayeesQuery->count();
                    $impaye_somme += $factureImpayeesQuery->sum('montant_ttc');
                }
                if (class_exists('App\Models\Invoice')) {
                    $invoiceTable = (new \App\Models\Invoice)->getTable();
                    $amountColumn = Schema::hasColumn($invoiceTable, 'total_amount_tax_incl') ? 'total_amount_tax_incl' : 'net_amount';

                    $impaye_count += \App\Models\Invoice::whereNotIn('status', ['payee', 'paid', 'annulee', 'cancelled'])->count();
                    $impaye_somme += \App\Models\Invoice::whereNotIn('status', ['payee', 'paid', 'annulee', 'cancelled'])->sum($amountColumn);
                }
                if (class_exists('App\Models\Recette')) {
                    $impaye_count += \App\Models\Recette::where('statut', 'en_attente')->count();
                    $impaye_somme += \App\Models\Recette::where('statut', 'en_attente')->sum('montant');
                }
            } catch (\Exception $e) { Log::error("Dashboard Stats Impayés Error: " . $e->getMessage()); }

            // 3. Opérations : Détail financier
            $validations_en_attente = 0;
            $paiements_en_attente = 0;
            $montant_en_validation = 0;
            $montant_a_payer = 0;
            $montant_paye_mois = 0;
            try {
                $validations_en_attente = Operation::whereIn('statut_courant', ['pending_validation', 'en_validation', 'en_attente', 'en_validation_responsable', 'en_validation_dg'])->count();
                $paiements_en_attente = Operation::whereIn('statut_courant', ['Approuvé_en_attente_paiement', 'bon_pour_accord', 'pret_execution'])->count();

                // En validation (Pas encore approuvé final)
                $montant_en_validation = Operation::whereIn('statut_courant', [
                    'pending_validation', 'en_validation', 'en_attente',
                    'en_validation_responsable', 'en_validation_dg'
                ])->sum('montant');

                // À payer (Approuvé mais pas encore payé)
                $montant_a_payer = Operation::whereIn('statut_courant', [
                    'Approuvé_en_attente_paiement', 'bon_pour_accord', 'pret_execution'
                ])->sum('montant');

                // Payé ce mois-ci
                $montant_paye_mois = Operation::where('statut_courant', 'payee')
                    ->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)
                    ->sum('montant');
            } catch (\Exception $e) { Log::error("Dashboard Stats Operations Error: " . $e->getMessage()); }

            // 4. Missions et véhicules
            $missions_actives = 0;
            $missions_mois = 0;
            $montant_missions_mois = 0;
            $cout_missions_mois = 0;
            $marge_missions_mois = 0;
            $vehicules_dispo = 0;
            $vehicules_total = 0;

            try {
                if (Schema::hasTable('vehicle_missions')) {
                    $missions_actives = DB::table('vehicle_missions')->where('status', 'ongoing')->count();
                    $missions_mois = DB::table('vehicle_missions')
                        ->whereMonth('start_at', now()->month)
                        ->whereYear('start_at', now()->year)
                        ->count();

                    $montant_missions_mois = DB::table('vehicle_missions')
                        ->whereMonth('start_at', now()->month)
                        ->whereYear('start_at', now()->year)
                        ->where('status', '!=', 'canceled')
                        ->sum('total_client_amount');

                    $cout_missions_mois = DB::table('vehicle_missions')
                        ->whereMonth('start_at', now()->month)
                        ->whereYear('start_at', now()->year)
                        ->where('status', '!=', 'canceled')
                        ->sum('total_supplier_cost');

                    $marge_missions_mois = $montant_missions_mois - $cout_missions_mois;
                } else {
                    $missions_actives = Operation::where('statut_courant', 'en_cours')->count();
                    $missions_mois = Operation::whereMonth('date_operation', now()->month)->count();
                }

                if (Schema::hasTable('vehicules')) {
                    $vehicules_total = DB::table('vehicules')->count();
                    $vehicules_dispo = DB::table('vehicules')->whereIn('statut', ['disponible', 'libre', 'ACTIF'])->count();
                } elseif (Schema::hasTable('vehicles')) {
                    $vehicules_total = DB::table('vehicles')->count();
                    $vehicules_dispo = DB::table('vehicles')->where('disponible', 1)->count();
                }
            } catch (\Exception $e) {}

            return [
                'ca' => [
                    'jour' => $ca_jour,
                    'mois' => $ca_mois,
                    'annee' => $ca_annee,
                    'mois_label' => $ca_mois_label,
                ],
                'missions' => [
                    'actives' => $missions_actives,
                    'mois' => $missions_mois,
                    'montant_mois' => $montant_missions_mois,
                    'cout_mois' => $cout_missions_mois,
                    'marge_mois' => $marge_missions_mois,
                ],
                'parc' => [
                    'dispo' => $vehicules_dispo,
                    'total' => $vehicules_total,
                    'taux_utilisation' => $vehicules_total > 0 ? round((($vehicules_total - $vehicules_dispo) / $vehicules_total) * 100, 1) : 0,
                ],
                'factures_impayees' => [
                    'count' => $impaye_count,
                    'total' => $impaye_somme,
                ],
                'operations' => [
                    'mois' => Operation::whereMonth('date_operation', now()->month)->count(),
                    'total' => Operation::count(),
                    'en_attente_count' => $validations_en_attente + $paiements_en_attente,
                    'en_validation_total' => $montant_en_validation,
                    'a_payer_total' => $montant_a_payer,
                    'paye_mois_total' => $montant_paye_mois,
                ],
                'clients' => ['total' => Schema::hasTable('clients') ? DB::table('clients')->count() : 0],
                'fournisseurs' => ['total' => Schema::hasTable('fournisseurs') ? DB::table('fournisseurs')->count() : 0],
                'validations' => [
                    'en_attente' => $validations_en_attente,
                    'paiement_en_attente' => $paiements_en_attente,
                ],
            ];

        } catch (\Exception $e) {
            Log::error("Global Dashboard Stats Error: " . $e->getMessage());
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
        // CA Mensuel sur les 12 derniers mois (basé sur les encaissements)
        $ca_mensuel = [];
        $labels = [];

        try {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->translatedFormat('M');
                $ca_mensuel[] = \App\Models\Encaissement::whereMonth('date_encaissement', $date->month)
                    ->whereYear('date_encaissement', $date->year)
                    ->sum('montant') ?? 0;
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
            $vehicules_dispo = DB::table('vehicules')->where('disponible', true)->count();
            $en_mission = 0;
            if (Schema::hasTable('vehicle_missions')) {
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
            $factures_echues = Facture::whereIn('statut', ['impayée', 'partiellement_payée'])
                    ->count();
            } catch (\Exception $e) {}

            $stock_faible = 0;
            try {
                $stock_faible = Produit::where('stock_actuel', '<=', DB::raw('stock_min'))->count();
            } catch (\Exception $e) {}

            $assurances_expirees = 0;
            try {
                if (Schema::hasTable('assurances')) {
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

            $visites_expirees = 0;
            try {
                if (Schema::hasTable('visites_techniques')) {
                    $visites_expirees = DB::table('visites_techniques')
                        ->where('date_prochaine', '<=', now()->addDays(15))
                        ->where('statut', '!=', 'annulee')
                        ->count();
                }
            } catch (\Exception $e) {}

            return [
                'factures_echues' => $factures_echues,
                'retards_retour' => $retards_retour,
                'stock_faible' => $stock_faible,
                'assurances_expirees' => $assurances_expirees,
                'visites_expirees' => $visites_expirees,
            ];
        } catch (\Exception $e) {
            return [
                'factures_echues' => 0,
                'retards_retour' => 0,
                'stock_faible' => 0,
                'assurances_expirees' => 0,
                'visites_expirees' => 0,
            ];
        }
    }

    /**
     * Récupère une liste unifiée des dernières activités
     */
    private function getRecentActivity()
    {
        $activities = collect();

        try {
            // 1. Dernières Opérations
            if (class_exists('App\Models\Operation')) {
                $operations = \App\Models\Operation::latest()->take(5)->get();
                foreach ($operations as $op) {
                    $status_info = $this->getStatusInfo($op->statut_courant);
                    $activities->push([
                        'id' => $op->id,
                        'time' => $op->created_at->format('H:i'),
                        'type' => 'Opération',
                        'message' => "Opération #" . ($op->code_operation ?? $op->id) . " - " . ($op->designation ?? 'Mission'),
                        'module' => 'Opérations',
                        'module_class' => 'bg-info',
                        'status_label' => $status_info['label'],
                        'status_class' => $status_info['class'],
                        'icon' => 'fas fa-cog',
                        'date' => $op->created_at
                    ]);
                }
            }

            // 2. Dernières Factures
            if (class_exists('App\Models\Facture')) {
                $factures = \App\Models\Facture::with('client')->latest()->take(3)->get();
                foreach ($factures as $f) {
                    $activities->push([
                        'id' => $f->id,
                        'time' => $f->created_at->format('H:i'),
                        'type' => 'Facture',
                        'message' => "Facture #" . ($f->numero ?? $f->id) . " pour " . ($f->client->nom ?? 'Client'),
                        'module' => 'Comptabilité',
                        'module_class' => 'bg-danger',
                        'status_label' => ucfirst($f->statut),
                        'status_class' => $f->statut == 'payee' ? 'bg-success' : 'bg-warning',
                        'icon' => 'fas fa-file-invoice-dollar',
                        'date' => $f->created_at
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Recent Activity Error: " . $e->getMessage());
        }

        return $activities->sortByDesc('date')->take(8);
    }

    /**
     * Aide pour les libellés et couleurs de statut
     */
    private function getStatusInfo($status)
    {
        switch ($status) {
            case 'pending_validation':
            case 'en_validation':
            case 'en_validation_responsable':
                return ['label' => 'En validation', 'class' => 'bg-warning text-dark'];
            case 'Approuvé_en_attente_paiement':
            case 'approved':
            case 'approuvee':
                return ['label' => 'Approuvée', 'class' => 'bg-info'];
            case 'payee':
            case 'payée':
            case 'terminee':
            case 'termine':
                return ['label' => 'Terminée', 'class' => 'bg-success'];
            case 'rejetee':
            case 'rejete':
                return ['label' => 'Rejetée', 'class' => 'bg-danger'];
            case 'draft':
            case 'brouillon':
                return ['label' => 'Brouillon', 'class' => 'bg-secondary'];
            default:
                return ['label' => $status ?? 'N/A', 'class' => 'bg-light text-dark'];
        }
    }

    private function getTreasuryStats()
    {
        try {
            // Solde des caisses - Tenter les deux variantes de colonne
            $soldeCaisses = 0;
            try {
                $result = \App\Models\Caisse::where('est_active', true)->sum('solde_actuel');
                $soldeCaisses = max(0, $result ?? 0);
            } catch (\Exception $e1) {
                try {
                    // Fallback: Récupérer toutes les caisses et sommer manuellement
                    $result = \App\Models\Caisse::all()->where('est_active', true)->sum('solde_actuel');
                    $soldeCaisses = max(0, $result ?? 0);
                } catch (\Exception $e2) {
                    Log::warning('Treasury: Could not fetch caisse balances', ['error' => $e2->getMessage()]);
                }
            }

            // Solde des comptes bancaires - Tenter plusieurs variantes de colonne
            $soldeBanques = 0;
            if (class_exists('\App\Models\CompteBancaire')) {
                try {
                    // Essayer avec est_actif (booléen)
                    $result = \App\Models\CompteBancaire::where('est_actif', true)->sum('solde');
                    $soldeBanques = max(0, $result ?? 0);

                    // Si le résultat est encore 0, essayer sans le filtre est_actif
                    if ($soldeBanques == 0) {
                        $result = \App\Models\CompteBancaire::sum('solde');
                        $soldeBanques = max(0, $result ?? 0);
                    }
                } catch (\Exception $e) {
                    try {
                        // Fallback: verifier si la colonne existe
                        $result = \App\Models\CompteBancaire::all()->sum('solde');
                        $soldeBanques = max(0, $result ?? 0);
                    } catch (\Exception $e2) {
                        Log::warning('Treasury: Could not fetch compte_bancaire balances', ['error' => $e2->getMessage()]);
                    }
                }
            }

            // Approvisionnements du mois
            $approvisionnementsMonth = 0;
            if (class_exists('\App\Models\ApprovisionnementCaisse')) {
                try {
                    $result = \App\Models\ApprovisionnementCaisse::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->where('statut', 'validé')
                        ->sum('montant');
                    $approvisionnementsMonth = max(0, $result ?? 0);
                } catch (\Exception $e) {
                    Log::warning('Treasury: Could not fetch approvisionnements', ['error' => $e->getMessage()]);
                }
            }

            // Dépenses du mois
            $depensesMonth = 0;
            if (class_exists('\App\Models\DepenseCaisse')) {
                try {
                    $result = \App\Models\DepenseCaisse::whereMonth('date_depense', now()->month)
                        ->whereYear('date_depense', now()->year)
                        ->where('statut', 'validé')
                        ->sum('montant');
                    $depensesMonth = max(0, $result ?? 0);

                    // Fallback sans le filtre statut si résultat = 0
                    if ($depensesMonth == 0) {
                        $result = \App\Models\DepenseCaisse::whereMonth('date_depense', now()->month)
                            ->whereYear('date_depense', now()->year)
                            ->sum('montant');
                        $depensesMonth = max(0, $result ?? 0);
                    }
                } catch (\Exception $e) {
                    Log::warning('Treasury: Could not fetch depenses', ['error' => $e->getMessage()]);
                }
            }

            return [
                'solde_caisses' => $soldeCaisses,
                'solde_banques' => $soldeBanques,
                'total' => $soldeCaisses + $soldeBanques,
                'approvisionnements_mois' => $approvisionnementsMonth,
                'depenses_mois' => $depensesMonth,
                'variation_mois' => $approvisionnementsMonth - $depensesMonth,
                'nombre_caisses' => \App\Models\Caisse::where('est_active', true)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Treasury: Critical error in getTreasuryStats', ['error' => $e->getMessage()]);
            return [
                'solde_caisses' => 0, 'solde_banques' => 0, 'total' => 0,
                'approvisionnements_mois' => 0, 'depenses_mois' => 0, 'variation_mois' => 0,
                'nombre_caisses' => 0,
            ];
        }
    }

    private function getAccountingStats()
    {
        try {
            // REVENUS: Montant total des factures du mois (toutes les factures émises/créées)
            $revenusMois = 0;
            if (class_exists('\App\Models\Facture')) {
                // Somme de TOUTES les factures créées le mois, peu importe le statut de paiement
                $revenusMois = \App\Models\Facture::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('montant_ttc') ?? 0;
            }

            // DÉPENSES: Montant des décaissements du mois
            $depensesMois = 0;

            // Essayer d'abord la table decaissements si elle existe
            if (Schema::hasTable('decaissements')) {
                try {
                    $depensesMois = DB::table('decaissements')
                        ->whereMonth('date_decaissement', now()->month)
                        ->whereYear('date_decaissement', now()->year)
                        ->sum('montant') ?? 0;
                } catch (\Exception $e) {
                    Log::warning("Error fetching decaissements: " . $e->getMessage());
                }
            }

            // Fallback: Dépenses caisses si la table decaissements n'existe pas ou ne retourne rien
            if ($depensesMois == 0 && class_exists('\App\Models\DepenseCaisse')) {
                try {
                    $depensesMois = \App\Models\DepenseCaisse::whereMonth('date_depense', now()->month)
                        ->whereYear('date_depense', now()->year)
                        ->sum('montant') ?? 0;
                } catch (\Exception $e) {
                    Log::warning("Error fetching depense_caisses: " . $e->getMessage());
                }
            }

            // Factures impayées (pour info)
            $facturesImpayees = 0;
            $montantImpaye = 0;
            if (class_exists('\App\Models\Facture')) {
                $facturesImpayeesQuery = \App\Models\Facture::query()
                    ->where(function ($query) {
                        $query->whereNull('statut')
                            ->orWhereNotIn('statut', ['payee', 'payée', 'paye', 'paid', 'annulee', 'annulée', 'cancelled']);
                    });

                $facturesImpayees = $facturesImpayeesQuery->count();
                $montantImpaye = $facturesImpayeesQuery->sum('montant_ttc') ?? 0;
            }

            // Base métier plateforme: on privilégie les missions (CA client / coût fournisseur)
            $revenusMetierMois = $revenusMois;
            $coutDirectMissionsMois = 0;
            if (Schema::hasTable('vehicle_missions')) {
                try {
                    $revenusMetierMois = DB::table('vehicle_missions')
                        ->whereMonth('start_at', now()->month)
                        ->whereYear('start_at', now()->year)
                        ->where('status', '!=', 'canceled')
                        ->sum('total_client_amount') ?? $revenusMetierMois;

                    $coutDirectMissionsMois = DB::table('vehicle_missions')
                        ->whereMonth('start_at', now()->month)
                        ->whereYear('start_at', now()->year)
                        ->where('status', '!=', 'canceled')
                        ->sum('total_supplier_cost') ?? 0;
                } catch (\Exception $e) {
                    Log::warning("Error computing monthly mission metrics: " . $e->getMessage());
                }
            }

            // Indicateurs financiers métier
            $ebe = $revenusMetierMois - ($coutDirectMissionsMois + $depensesMois);
            $caf = $ebe;
            $detteFournisseur = 0;

            if (Schema::hasTable('facture_fournisseurs')) {
                try {
                    $query = DB::table('facture_fournisseurs');
                    if (Schema::hasColumn('facture_fournisseurs', 'deleted_at')) {
                        $query->whereNull('deleted_at');
                    }

                    if (Schema::hasColumn('facture_fournisseurs', 'statut')) {
                        $query->whereIn('statut', ['non_payee', 'partiellement_payee']);
                    }

                    if (Schema::hasColumn('facture_fournisseurs', 'reste_a_payer')) {
                        $detteFournisseur = $query->sum('reste_a_payer') ?? 0;
                    } else {
                        $montantTtc = (clone $query)->sum('montant_ttc') ?? 0;
                        $montantPaye = (clone $query)->sum('montant_paye') ?? 0;
                        $detteFournisseur = max(0, $montantTtc - $montantPaye);
                    }
                } catch (\Exception $e) {
                    Log::warning("Error computing supplier debt: " . $e->getMessage());
                }
            }

            // TRI métier = retour sur investissement opérationnel sur les coûts directs missions
            $tri = $coutDirectMissionsMois > 0
                ? round((($revenusMetierMois - $coutDirectMissionsMois) / $coutDirectMissionsMois) * 100, 2)
                : 0;

            return [
                'revenus_mois' => $revenusMois,
                'revenus_metier_mois' => $revenusMetierMois,
                'cout_direct_missions_mois' => $coutDirectMissionsMois,
                'depenses_mois' => $depensesMois,
                'resultat_mois' => $revenusMois - $depensesMois,
                'factures_impayees' => $facturesImpayees,
                'montant_impaye' => $montantImpaye,
                'ebe' => $ebe,
                'caf' => $caf,
                'dette_fournisseur' => $detteFournisseur,
                'tri' => $tri,
            ];
        } catch (\Exception $e) {
            Log::error("Accounting Stats Error: " . $e->getMessage());
            return [
                'revenus_mois' => 0, 'depenses_mois' => 0, 'resultat_mois' => 0,
                'factures_impayees' => 0, 'montant_impaye' => 0,
                'ebe' => 0, 'caf' => 0, 'dette_fournisseur' => 0, 'tri' => 0,
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
                'invoices_pending' => Facture::whereIn('statut', ['en_attente', 'en_retard'])->count(),
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
        return response()->json($this->getChartsData());
    }

    /**
     * Flux de trésorerie prévisionnel
     */
    private function getPrevisionalTreasuryStats()
    {
        try {
            // Factures en attente d'encaissement (Revenu à venir)
            $revenu_attendu = DB::table('factures')
                ->whereIn('statut', ['en_attente', 'en_retard'])
                ->sum('montant_ttc') ?? 0;

            // Dépenses approuvées non payées (Dépenses à venir)
            $depenses_prevues = DB::table('operations')
                ->whereIn('statut_courant', ['Approuvé_en_attente_paiement', 'bon_pour_accord', 'pret_execution'])
                ->sum('montant') ?? 0;

            return [
                'revenu_attendu' => $revenu_attendu,
                'depenses_prevues' => $depenses_prevues,
                'solde_previsionnel' => $revenu_attendu - $depenses_prevues,
            ];
        } catch (\Exception $e) {
            return ['revenu_attendu' => 0, 'depenses_prevues' => 0, 'solde_previsionnel' => 0];
        }
    }

    /**
     * Statistiques SMS
     */
    private function getSMSStats()
    {
        try {
            $sms_envoyes_mois = DB::table('notifications')
                ->where('type', 'sms')
                ->where('status', 'sent')
                ->whereMonth('sent_at', now()->month)
                ->whereYear('sent_at', now()->year)
                ->count();

            $config = \App\Models\EntrepriseSettings::getActive();

            return [
                'envoyes_mois' => $sms_envoyes_mois,
                'is_active' => $config->sms_is_active ?? false,
                'provider' => $config->sms_provider ?? 'N/A',
            ];
        } catch (\Exception $e) {
            return ['envoyes_mois' => 0, 'is_active' => false, 'provider' => 'N/A'];
        }
    }

    /**
     * Contrats expirant dans les 30 prochains jours
     */
    private function getExpiringContractsCount()
    {
        try {
            return DB::table('personnel_contrats')
                ->where('statut', 'ACTIF')
                ->whereIn('type_contrat', ['CDD', 'STAGE'])
                ->whereNotNull('date_fin')
                ->where('date_fin', '>', now())
                ->where('date_fin', '<=', now()->addDays(30))
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Récupère les opérations groupées par statut
     */
    private function getOperationsParStatut()
    {
        try {
            $statuts = [
                'pending_validation' => 'En validation',
                'en_validation' => 'En validation',
                'en_validation_responsable' => 'En validation',
                'en_validation_dg' => 'En validation',
                'Approuvé_en_attente_paiement' => 'En attente paiement',
                'bon_pour_accord' => 'En attente paiement',
                'pret_execution' => 'En attente paiement',
                'en_cours' => 'En cours',
                'payee' => 'Payée',
                'terminee' => 'Terminée',
                'terminate' => 'Terminée',
                'rejetee' => 'Rejetée',
                'annulee' => 'Annulée',
            ];

            $data = [];
            $colors_map = [
                'En validation' => '#FFC107',
                'En attente paiement' => '#17A2B8',
                'En cours' => '#007BFF',
                'Payée' => '#28A745',
                'Terminée' => '#6C757D',
                'Rejetée' => '#DC3545',
                'Annulée' => '#6F42C1',
            ];

            foreach ($statuts as $statut => $label) {
                $count = Operation::where('statut_courant', $statut)->count();
                if ($count > 0) {
                    if (!isset($data[$label])) {
                        $data[$label] = ['count' => 0, 'color' => $colors_map[$label] ?? '#999'];
                    }
                    $data[$label]['count'] += $count;
                }
            }

            // Préparer les données pour le graphique
            $labels = array_keys($data);
            $counts = array_column($data, 'count');

            // Extraire les couleurs dans le bon ordre (important pour Chart.js)
            $colorsArray = [];
            foreach ($labels as $label) {
                $colorsArray[] = $data[$label]['color'] ?? $colors_map[$label] ?? '#999';
            }

            return [
                'labels' => $labels,
                'data' => $counts,
                'colors' => $colorsArray,
                'total' => array_sum($counts),
            ];
        } catch (\Exception $e) {
            Log::error("Operations par Statut Error: " . $e->getMessage());
            return [
                'labels' => [],
                'data' => [],
                'colors' => [],
                'total' => 0,
            ];
        }
    }

    /**
     * Récupère l'évolution des opérations sur les 6 derniers mois
     */
    private function getOperationsSixDerniersMois()
    {
        try {
            $labels = [];
            $creees = [];
            $terminees = [];
            $payees = [];

            // Les 6 derniers mois
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->translatedFormat('M y');

                // Créées ce mois
                $creees[] = Operation::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();

                // Terminées ce mois
                $terminees[] = Operation::whereMonth('updated_at', $date->month)
                    ->whereYear('updated_at', $date->year)
                    ->whereIn('statut_courant', ['terminee', 'terminate', 'payee'])
                    ->count();

                // Payées ce mois
                $payees[] = Operation::whereMonth('updated_at', $date->month)
                    ->whereYear('updated_at', $date->year)
                    ->where('statut_courant', 'payee')
                    ->count();
            }

            return [
                'labels' => $labels,
                'creees' => $creees,
                'terminees' => $terminees,
                'payees' => $payees,
            ];
        } catch (\Exception $e) {
            Log::error("Operations 6 derniers mois Error: " . $e->getMessage());
            return [
                'labels' => [],
                'creees' => [],
                'terminees' => [],
                'payees' => [],
            ];
        }
    }
}
