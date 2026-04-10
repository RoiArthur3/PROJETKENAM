<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\Vehicle;
use App\Models\StockMovement;
use App\Models\Invoice;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingController extends Controller
{
    /**
     * Page d'accueil du module reporting
     */
    public function index()
    {
        return redirect()->route('reporting.dashboard');
    }

    /**
     * Dashboard du module reporting — données réelles
     */
    public function dashboard()
    {
        try {
            // ── Opérations ──
            $total_operations = \App\Models\Operation::count();
            $operations_today = \App\Models\Operation::whereDate('created_at', today())->count();
            $operations_month = \App\Models\Operation::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();
            $operations_pending = \App\Models\Operation::where('statut_courant', 'pending_validation')->count();
            $operations_approved = \App\Models\Operation::where('statut_courant', 'approuvee')->count();
            $operations_paid = \App\Models\Operation::where('statut_courant', 'payee')->count();
            $operations_rejected = \App\Models\Operation::where('statut_courant', 'rejetee')->count();
            $montant_operations = \App\Models\Operation::sum('montant');

            // Répartition par statut
            $ops_by_status = \App\Models\Operation::select('statut_courant', DB::raw('count(*) as nb'), DB::raw('COALESCE(SUM(montant),0) as total_montant'))
                ->groupBy('statut_courant')
                ->get();

            // Opérations récentes
            $recent_operations = \App\Models\Operation::with(['initiateur', 'client'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // ── Trésorerie ──
            $caisses = \App\Models\Caisse::where('est_active', true)->get();
            $solde_total_caisses = $caisses->sum('solde_actuel');
            $nb_caisses = $caisses->count();

            // Mouvements de caisse ce mois
            $mouv_month = \App\Models\MouvementCaisse::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
            $entrees_mois = (clone $mouv_month)->where('type_mouvement', 'entree')->sum('montant');
            $sorties_mois = (clone $mouv_month)->where('type_mouvement', 'sortie')->sum('montant');

            // Dépenses de caisse ce mois (pas dans MouvementCaisse)
            $depenses_mois = \App\Models\DepenseCaisse::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->sum('montant');

            // Encaissements ce mois (comptés via MouvementCaisse, ici juste le count)
            $nb_encaissements_mois = \App\Models\Encaissement::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();

            // Approvisionnements
            $nb_approv_pending = \App\Models\ApprovisionnementCaisse::where('statut', 'en_attente')->count();
            $nb_approv_total = \App\Models\ApprovisionnementCaisse::count();

            // Derniers mouvements de trésorerie (MouvementCaisse + DepenseCaisse)
            $derniers_mouvements = collect([]);

            $recent_mouv = \App\Models\MouvementCaisse::with(['caisse', 'createur'])
                ->orderBy('created_at', 'desc')->limit(5)->get();
            foreach ($recent_mouv as $m) {
                $isEntree = in_array($m->type_mouvement, ['entree', 'transfert_entrant']);
                $derniers_mouvements->push((object)[
                    'date' => $m->created_at,
                    'type' => $isEntree ? 'Entrée' : 'Sortie',
                    'libelle' => $m->libelle ?? 'Mouvement',
                    'montant' => abs($m->montant),
                    'caisse' => $m->caisse->nom ?? '-',
                    'is_entree' => $isEntree,
                ]);
            }

            $recent_dep = \App\Models\DepenseCaisse::with(['caisse'])
                ->orderBy('created_at', 'desc')->limit(5)->get();
            foreach ($recent_dep as $d) {
                $derniers_mouvements->push((object)[
                    'date' => $d->created_at,
                    'type' => 'Sortie',
                    'libelle' => $d->libelle ?? 'Dépense',
                    'montant' => abs($d->montant),
                    'caisse' => $d->caisse->nom ?? '-',
                    'is_entree' => false,
                ]);
            }
            $derniers_mouvements = $derniers_mouvements->sortByDesc('date')->take(10)->values();

            // ── Évolution mensuelle des opérations (6 derniers mois) ──
            $chart_ops = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $count = \App\Models\Operation::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)->count();
                $montant = \App\Models\Operation::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)->sum('montant');
                $chart_ops[] = [
                    'label' => $month->translatedFormat('M Y'),
                    'count' => $count,
                    'montant' => (float) $montant,
                ];
            }

            // ── Utilisateurs ──
            $nb_users = \App\Models\User::count();

            // ── Stats compilées ──
            $stats = [
                'total_operations' => $total_operations,
                'operations_today' => $operations_today,
                'operations_month' => $operations_month,
                'operations_pending' => $operations_pending,
                'operations_approved' => $operations_approved,
                'operations_paid' => $operations_paid,
                'operations_rejected' => $operations_rejected,
                'montant_operations' => $montant_operations,
                'solde_total_caisses' => $solde_total_caisses,
                'nb_caisses' => $nb_caisses,
                'entrees_mois' => $entrees_mois,
                'sorties_mois' => $sorties_mois + $depenses_mois,
                'nb_encaissements_mois' => $nb_encaissements_mois,
                'nb_approv_pending' => $nb_approv_pending,
                'nb_approv_total' => $nb_approv_total,
                'depenses_mois' => $depenses_mois,
                'nb_users' => $nb_users,
            ];

            return view('reporting.dashboard', compact(
                'stats',
                'ops_by_status',
                'recent_operations',
                'caisses',
                'derniers_mouvements',
                'chart_ops'
            ));
        } catch (\Exception $e) {
            \Log::error("Erreur reporting dashboard: " . $e->getMessage() . ' - ' . $e->getTraceAsString());

            return view('reporting.dashboard', [
                'stats' => [
                    'total_operations' => 0, 'operations_today' => 0, 'operations_month' => 0,
                    'operations_pending' => 0, 'operations_approved' => 0, 'operations_paid' => 0,
                    'operations_rejected' => 0, 'montant_operations' => 0,
                    'solde_total_caisses' => 0, 'nb_caisses' => 0,
                    'entrees_mois' => 0, 'sorties_mois' => 0, 'nb_encaissements_mois' => 0,
                    'nb_approv_pending' => 0, 'nb_approv_total' => 0, 'depenses_mois' => 0,
                    'nb_users' => 0,
                ],
                'ops_by_status' => collect([]),
                'recent_operations' => collect([]),
                'caisses' => collect([]),
                'derniers_mouvements' => collect([]),
                'chart_ops' => [],
            ]);
        }
    }

    /**
     * Rapport financier - Tous les mouvements de trésorerie
     *
     * Sources de données (sans double comptage) :
     *  - MouvementCaisse : source principale (contient déjà les encaissements + approvisionnements)
     *  - DepenseCaisse   : dépenses de caisse (PAS loguées dans MouvementCaisse)
     *  - Virement        : virements bancaires effectués (CompteBancaire, pas Caisse)
     *  - PaiementFournisseur : paiements fournisseurs (sorties)
     *  - Paie            : paiements salaires (sorties)
     *
     * NE PAS ajouter séparément Encaissement ni ApprovisionnementCaisse car
     * leur création (encaissementsStore / ApprovisionnementController::store)
     * insère déjà un MouvementCaisse → sinon double comptage.
     */
    public function financier(Request $request)
    {
        $period = $request->get('period', 'month');
        $type_filter = $request->get('type', '');
        $caisse_filter = $request->get('caisse', '');
        $date_debut = $request->get('date_debut', '');
        $date_fin = $request->get('date_fin', '');

        // Dates selon la période ou les dates personnalisées
        if ($date_debut && $date_fin) {
            $startDate = Carbon::parse($date_debut)->startOfDay();
            $endDate = Carbon::parse($date_fin)->endOfDay();
        } else {
            $startDate = $this->getStartDate($period);
            $endDate = now()->endOfDay();
        }

        try {
            // ── Caisses ──
            $caisses = \App\Models\Caisse::orderBy('nom')->get();
            $solde_total = $caisses->sum('solde_actuel');

            $transactions = collect([]);

            // ──────────────────────────────────────────────────────────
            // 1. MouvementCaisse = source principale
            //    Contient déjà : encaissements, approvisionnements, opérations bancaires
            // ──────────────────────────────────────────────────────────
            $mouvQuery = \App\Models\MouvementCaisse::with(['caisse', 'createur'])
                ->whereBetween('created_at', [$startDate, $endDate]);
            if ($caisse_filter) {
                $mouvQuery->where('caisse_id', $caisse_filter);
            }
            if ($type_filter === 'entree') {
                $mouvQuery->where('type_mouvement', 'entree');
            } elseif ($type_filter === 'sortie') {
                $mouvQuery->where('type_mouvement', 'sortie');
            }
            $mouvements = $mouvQuery->orderBy('created_at', 'desc')->get();

            foreach ($mouvements as $m) {
                $isEntree = in_array($m->type_mouvement, ['entree', 'transfert_entrant']);
                // Déduire la catégorie depuis source_type si disponible
                $categorie = 'Mouvement';
                if ($m->source_type) {
                    if (str_contains($m->source_type, 'Encaissement')) $categorie = 'Encaissement';
                    elseif (str_contains($m->source_type, 'Approvisionnement')) $categorie = 'Approvisionnement';
                    elseif (str_contains($m->source_type, 'Depense')) $categorie = 'Dépense';
                }
                $transactions->push((object)[
                    'date' => $m->created_at,
                    'type' => $isEntree ? 'Entrée' : 'Sortie',
                    'categorie' => $categorie,
                    'reference' => $m->reference ?? '-',
                    'libelle' => $m->libelle ?? 'Mouvement de caisse',
                    'description' => $m->description ?? '',
                    'montant' => abs($m->montant),
                    'caisse' => $m->caisse->nom ?? $m->caisse->libelle ?? '-',
                    'auteur' => $m->createur->name ?? '-',
                    'statut' => $m->statut ?? 'comptabilise',
                    'is_entree' => $isEntree,
                ]);
            }

            // ──────────────────────────────────────────────────────────
            // 2. DepenseCaisse (PAS loguées dans MouvementCaisse)
            //    → toujours des sorties
            // ──────────────────────────────────────────────────────────
            if ($type_filter !== 'entree') {
                $depQuery = \App\Models\DepenseCaisse::with(['caisse', 'createur', 'beneficiaire'])
                    ->whereBetween('created_at', [$startDate, $endDate]);
                if ($caisse_filter) {
                    $depQuery->where('caisse_id', $caisse_filter);
                }
                $depenses = $depQuery->orderBy('created_at', 'desc')->get();

                foreach ($depenses as $d) {
                    $transactions->push((object)[
                        'date' => $d->created_at,
                        'type' => 'Sortie',
                        'categorie' => 'Dépense',
                        'reference' => '-',
                        'libelle' => $d->libelle ?? 'Dépense de caisse',
                        'description' => $d->notes ?? '',
                        'montant' => abs($d->montant),
                        'caisse' => $d->caisse->nom ?? $d->caisse->libelle ?? '-',
                        'auteur' => $d->createur->name ?? ($d->beneficiaire->name ?? '-'),
                        'statut' => $d->est_justifie ? 'justifié' : 'non justifié',
                        'is_entree' => false,
                    ]);
                }
            } else {
                $depenses = collect([]);
            }

            // ──────────────────────────────────────────────────────────
            // 3. Virement bancaire (effectués) — système CompteBancaire
            //    → sortie pour le compte source, entrée pour le destination
            // ──────────────────────────────────────────────────────────
            try {
                $virQuery = \App\Models\Virement::with(['compteSource.banque', 'compteDestination.banque', 'initiateur'])
                    ->where('statut', 'effectue')
                    ->whereBetween('created_at', [$startDate, $endDate]);
                $virements = $virQuery->orderBy('created_at', 'desc')->get();

                foreach ($virements as $v) {
                    $srcLabel = $v->compteSource->intitule_compte ?? ($v->compteSource->banque->nom ?? 'Compte source');
                    $dstLabel = $v->compteDestination->intitule_compte ?? ($v->compteDestination->banque->nom ?? 'Compte dest.');

                    if ($type_filter !== 'entree') {
                        $transactions->push((object)[
                            'date' => $v->created_at,
                            'type' => 'Sortie',
                            'categorie' => 'Virement',
                            'reference' => $v->reference ?? '-',
                            'libelle' => 'Virement → ' . $dstLabel,
                            'description' => $v->motif ?? '',
                            'montant' => abs($v->montant) + abs($v->frais ?? 0),
                            'caisse' => $srcLabel,
                            'auteur' => $v->initiateur->name ?? '-',
                            'statut' => $v->statut,
                            'is_entree' => false,
                        ]);
                    }
                    if ($type_filter !== 'sortie') {
                        $transactions->push((object)[
                            'date' => $v->created_at,
                            'type' => 'Entrée',
                            'categorie' => 'Virement',
                            'reference' => $v->reference ?? '-',
                            'libelle' => 'Virement ← ' . $srcLabel,
                            'description' => $v->motif ?? '',
                            'montant' => abs($v->montant),
                            'caisse' => $dstLabel,
                            'auteur' => $v->initiateur->name ?? '-',
                            'statut' => $v->statut,
                            'is_entree' => true,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $virements = collect([]);
            }

            // ──────────────────────────────────────────────────────────
            // 4. PaiementFournisseur (sorties — paiements non annulés)
            // ──────────────────────────────────────────────────────────
            if ($type_filter !== 'entree') {
                try {
                    $pfQuery = \App\Models\PaiementFournisseur::with(['fournisseur', 'user'])
                        ->where('est_annule', false)
                        ->whereBetween('created_at', [$startDate, $endDate]);
                    $paiementsFournisseurs = $pfQuery->orderBy('created_at', 'desc')->get();

                    foreach ($paiementsFournisseurs as $pf) {
                        $transactions->push((object)[
                            'date' => $pf->created_at,
                            'type' => 'Sortie',
                            'categorie' => 'Paiement Fournisseur',
                            'reference' => $pf->reference ?? '-',
                            'libelle' => 'Paiement ' . ($pf->fournisseur->nom ?? 'Fournisseur'),
                            'description' => $pf->notes ?? '',
                            'montant' => abs($pf->montant),
                            'caisse' => $pf->mode_paiement ?? 'Virement',
                            'auteur' => $pf->user->name ?? '-',
                            'statut' => $pf->validateur_id ? 'validé' : 'en attente',
                            'is_entree' => false,
                        ]);
                    }
                } catch (\Exception $e) {
                    $paiementsFournisseurs = collect([]);
                }
            } else {
                $paiementsFournisseurs = collect([]);
            }

            // ──────────────────────────────────────────────────────────
            // 5. Paie / Salaires (sorties — payés)
            // ──────────────────────────────────────────────────────────
            if ($type_filter !== 'entree') {
                try {
                    $paieQuery = \App\Models\Paie::with(['user'])
                        ->whereNotNull('date_paiement')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                    $salaires = $paieQuery->orderBy('created_at', 'desc')->get();

                    foreach ($salaires as $s) {
                        $transactions->push((object)[
                            'date' => $s->created_at,
                            'type' => 'Sortie',
                            'categorie' => 'Salaire',
                            'reference' => 'SAL-' . $s->id,
                            'libelle' => 'Salaire ' . ($s->user->name ?? 'Agent') . ' ' . ($s->mois ?? '') . '/' . ($s->annee ?? ''),
                            'description' => $s->commentaires ?? '',
                            'montant' => abs($s->net_a_payer ?? 0),
                            'caisse' => 'Paie',
                            'auteur' => '-',
                            'statut' => $s->statut ?? 'payé',
                            'is_entree' => false,
                        ]);
                    }
                } catch (\Exception $e) {
                    $salaires = collect([]);
                }
            } else {
                $salaires = collect([]);
            }

            // ── Trier par date décroissante ──
            $transactions = $transactions->sortByDesc('date')->values();

            // ── KPIs ──
            $total_entrees = $transactions->where('is_entree', true)->sum('montant');
            $total_sorties = $transactions->where('is_entree', false)->sum('montant');

            $nb_entrees_mouv = $mouvements->whereIn('type_mouvement', ['entree', 'transfert_entrant'])->count();
            $nb_sorties_mouv = $mouvements->whereIn('type_mouvement', ['sortie', 'transfert_sortant'])->count();

            $financial_data = [
                'total_entrees' => $total_entrees,
                'total_sorties' => $total_sorties,
                'solde_net' => $total_entrees - $total_sorties,
                'solde_total_caisses' => $solde_total,
                'nb_transactions' => $transactions->count(),
                'nb_entrees' => $nb_entrees_mouv,
                'nb_depenses' => ($depenses instanceof \Countable || is_array($depenses)) ? count($depenses) : 0,
                'nb_virements' => isset($virements) ? $virements->count() : 0,
                'nb_paiements_fourn' => isset($paiementsFournisseurs) ? $paiementsFournisseurs->count() : 0,
                'nb_salaires' => isset($salaires) ? $salaires->count() : 0,
            ];

            // ── Données graphiques : évolution journalière sur la période ──
            $chart_data = [];
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $dayKey = $current->format('Y-m-d');
                $dayEntrees = $transactions->filter(function($t) use ($dayKey) {
                    return $t->is_entree && $t->date->format('Y-m-d') === $dayKey;
                })->sum('montant');
                $daySorties = $transactions->filter(function($t) use ($dayKey) {
                    return !$t->is_entree && $t->date->format('Y-m-d') === $dayKey;
                })->sum('montant');

                $chart_data[] = [
                    'date' => $current->format('d/m'),
                    'entrees' => $dayEntrees,
                    'sorties' => $daySorties,
                ];
                $current->addDay();
            }

            // ── Répartition par catégorie ──
            $repartition = $transactions->groupBy('categorie')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'entrees' => $group->where('is_entree', true)->sum('montant'),
                    'sorties' => $group->where('is_entree', false)->sum('montant'),
                ];
            });

            return view('reporting.financier', compact(
                'financial_data',
                'transactions',
                'caisses',
                'chart_data',
                'repartition',
                'period',
                'type_filter',
                'caisse_filter',
                'date_debut',
                'date_fin'
            ));
        } catch (\Throwable $e) {
            \Log::error("Erreur reporting financier: " . $e->getMessage() . ' - ' . $e->getTraceAsString());

            return view('reporting.financier', [
                'financial_data' => [
                    'total_entrees' => 0,
                    'total_sorties' => 0,
                    'solde_net' => 0,
                    'solde_total_caisses' => 0,
                    'nb_transactions' => 0,
                    'nb_entrees' => 0,
                    'nb_depenses' => 0,
                    'nb_virements' => 0,
                    'nb_paiements_fourn' => 0,
                    'nb_salaires' => 0,
                ],
                'transactions' => collect([]),
                'caisses' => collect([]),
                'chart_data' => [],
                'repartition' => collect([]),
                'period' => 'month',
                'type_filter' => '',
                'caisse_filter' => '',
                'date_debut' => '',
                'date_fin' => '',
            ]);
        }
    }

    /**
     * Rapport des opérations
     */
    public function operations()
    {
        try {
            // Initialiser les variables
            $operations = collect([]);
            $stats = [
                'total_operations' => 0,
                'operations_today' => 0,
                'operations_week' => 0,
                'operations_month' => 0,
                'success_rate' => 0,
                'avg_duration' => 0,
            ];

            // Récupérer les opérations si le modèle existe
            if (class_exists('App\Models\Operation')) {
                $operations = \App\Models\Operation::with(['user', 'vehicle'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

                // Calculer les statistiques
                $stats['total_operations'] = \App\Models\Operation::count();
                $stats['operations_today'] = \App\Models\Operation::whereDate('created_at', today())->count();
                $stats['operations_week'] = \App\Models\Operation::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $stats['operations_month'] = \App\Models\Operation::whereMonth('created_at', now()->month)->count();

                // Taux de succès
                $completed = \App\Models\Operation::where('status', 'completed')->count();
                $stats['success_rate'] = $stats['total_operations'] > 0 ? ($completed / $stats['total_operations']) * 100 : 0;

                // Durée moyenne
                $avgDuration = \App\Models\Operation::whereNotNull('duration')->avg('duration');
                $stats['avg_duration'] = $avgDuration ?? 0;
            }

            // Si le modèle Operation n'existe pas, créer des données factices pour la démo
            if ($operations->isEmpty()) {
                $operations = collect([
                    (object)[
                        'id' => 1,
                        'reference' => 'OP-2026-001',
                        'type' => 'Livraison',
                        'status' => 'completed',
                        'client' => 'Client A',
                        'user' => (object)['name' => 'Jean Dupont'],
                        'vehicle' => (object)['plate_number' => 'CI-123-AB'],
                        'created_at' => now()->subHours(2),
                        'duration' => 45,
                        'amount' => 150000
                    ],
                    (object)[
                        'id' => 2,
                        'reference' => 'OP-2026-002',
                        'type' => 'Collecte',
                        'status' => 'in_progress',
                        'client' => 'Client B',
                        'user' => (object)['name' => 'Marie Martin'],
                        'vehicle' => (object)['plate_number' => 'CI-456-CD'],
                        'created_at' => now()->subHours(4),
                        'duration' => 30,
                        'amount' => 85000
                    ],
                    (object)[
                        'id' => 3,
                        'reference' => 'OP-2026-003',
                        'type' => 'Transfert',
                        'status' => 'pending',
                        'client' => 'Client C',
                        'user' => (object)['name' => 'Pierre Bernard'],
                        'vehicle' => (object)['plate_number' => 'CI-789-EF'],
                        'created_at' => now()->subHours(6),
                        'duration' => null,
                        'amount' => 120000
                    ]
                ]);

                // Mettre à jour les statistiques factices
                $stats['total_operations'] = 3;
                $stats['operations_today'] = 3;
                $stats['operations_week'] = 3;
                $stats['operations_month'] = 3;
                $stats['success_rate'] = 33.33;
                $stats['avg_duration'] = 37.5;
            }

            return view('reporting.operations', compact('operations', 'stats'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données vides
            $operations = collect([]);
            $stats = [
                'total_operations' => 0,
                'operations_today' => 0,
                'operations_week' => 0,
                'operations_month' => 0,
                'success_rate' => 0,
                'avg_duration' => 0,
            ];

            return view('reporting.operations', compact('operations', 'stats'));
        }
    }

    /**
     * Rapport de performance
     */
    public function performance()
    {
        try {
            // Performance par Service
            $services = \App\Models\ServiceOperationnel::all();
            $service_performance = [];

            foreach ($services as $service) {
                $ops = \App\Models\Operation::where('operational_service_id', $service->id)->get();
                $total_ops = $ops->count();
                $completed = $ops->where('statut', 'completed')->count();
                $success_rate = $total_ops > 0 ? round(($completed / $total_ops) * 100, 1) : 0;

                $service_performance[] = [
                    'nom' => $service->nom ?? 'N/A',
                    'code' => $service->code ?? 'N/A',
                    'count' => $total_ops,
                    'success_rate' => $success_rate,
                    'avg_time' => $ops->isEmpty() ? 0 : round($ops->avg('duree_estimee') ?? 0, 1)
                ];
            }

            // Performance par Agent (Responsable)
            $agents = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('name', ['agent', 'moderator', 'admin']);
            })->get();

            $agent_performance = [];
            foreach ($agents as $agent) {
                $ops = \App\Models\Operation::where('responsable_id', $agent->id)->get();
                $total_ops = $ops->count();
                $completed = $ops->where('statut', 'completed')->count();
                $success_rate = $total_ops > 0 ? round(($completed / $total_ops) * 100, 1) : 0;

                if ($total_ops > 0) {
                    $agent_performance[] = [
                        'name' => $agent->name ?? 'N/A',
                        'phone' => $agent->phone ?? 'N/A',
                        'count' => $total_ops,
                        'success_rate' => $success_rate,
                        'productivity' => round(($completed / $total_ops) * 100, 1)
                    ];
                }
            }

            // Performance par Véhicule/Engin
            $vehicles = \App\Models\Vehicle::all();
            $vehicle_performance = [];

            foreach ($vehicles as $vehicle) {
                $km_traveled = \App\Models\Operation::where('vehicule_id', $vehicle->id)->sum('distance') ?? 0;
                $fuel_consumption = \App\Models\Expense::where('type', 'fuel')
                    ->where('reference_id', $vehicle->id)->sum('amount') ?? 0;
                $maintenance = \App\Models\Expense::where('type', 'maintenance')
                    ->where('reference_id', $vehicle->id)->sum('amount') ?? 0;
                $ops = \App\Models\Operation::where('vehicule_id', $vehicle->id)->get();

                $vehicle_performance[] = [
                    'registration' => $vehicle->immatriculation ?? 'N/A',
                    'brand_model' => ($vehicle->marque ?? 'N/A') . ' ' . ($vehicle->modele ?? ''),
                    'status' => $vehicle->status ?? 'active',
                    'ops_count' => $ops->count(),
                    'km_traveled' => round($km_traveled, 1),
                    'fuel_cost' => round($fuel_consumption, 0),
                    'maintenance_cost' => round($maintenance, 0),
                    'status_color' => $vehicle->status === 'active' ? 'success' : 'warning'
                ];
            }

            return view('reporting.performance', compact('service_performance', 'agent_performance', 'vehicle_performance'));
        } catch (\Exception $e) {
            return view('reporting.performance', [
                'service_performance' => [],
                'agent_performance' => [],
                'vehicle_performance' => []
            ]);
        }
    }

    /**
     * Rapport détaillé par service
     */
    public function services(Request $request)
    {
        try {
            // Récupérer tous les services opérationnels
            $services = \App\Models\ServiceOperationnel::all();

            // Paramètres de filtrage
            $period = $request->get('period', 'month');
            $search = $request->get('search', '');
            $startDate = $this->getStartDate($period);

            $service_stats = [];

            foreach ($services as $service) {
                // Filtrer par période et recherche si nécessaire
                $query = \App\Models\Operation::where('operational_service_id', $service->id);

                // Appliquer la période
                if ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }

                $ops = $query->get();

                // Sauter si recherche précisée et ne correspond pas
                if ($search && stripos($service->nom, $search) === false && stripos($service->code, $search) === false) {
                    continue;
                }

                // Calculer les stats
                $count = $ops->count();
                $total_amount = $ops->sum('montant');
                $avg_amount = $count > 0 ? $total_amount / $count : 0;

                // Répartition par statut
                $statuses = $ops->groupBy('statut_courant')->map->count();

                // Temps de validation moyen pour ce service (basé sur les étapes de validation)
                $avg_validation_time = DB::table('operation_service_validation')
                    ->where('service_operationnel_id', $service->id)
                    ->whereNotNull('date_validation')
                    ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, date_validation)) as avg_hours'))
                    ->first()
                    ->avg_hours ?? 0;

                $service_stats[] = (object)[
                    'id' => $service->id,
                    'nom' => $service->nom,
                    'code' => $service->code,
                    'count' => $count,
                    'total_amount' => $total_amount,
                    'avg_amount' => $avg_amount,
                    'statuses' => $statuses,
                    'avg_validation_time' => round($avg_validation_time, 1),
                    'success_rate' => $count > 0 ? (($ops->where('statut_courant', 'approuvee')->count() + $ops->where('statut_courant', 'termine')->count()) / $count) * 100 : 0
                ];
            }

            return view('reporting.services', compact('service_stats'));
        } catch (\Exception $e) {
            \Log::error("Erreur reporting services: " . $e->getMessage());
            $service_stats = [];
            return view('reporting.services', compact('service_stats'));
        }
    }

    /**
     * Rapport des opérations par période
     */
    public function operationsReport(Request $request, $period = 'month')
    {
        $startDate = $this->getStartDate($period);

        $operations = Operation::where('created_at', '>=', $startDate)
            ->with(['client', 'vehicleAssignments.vehicle', 'operationStaff.user'])
            ->get();

        $totalRevenu = Invoice::sum('net_amount');

        $stats = [
            'total_operations' => $operations->count(),
            'completed_operations' => $operations->where('statut_courant', 'termine')->count(),
            'ongoing_operations' => $operations->where('statut_courant', 'en_cours')->count(),
            'by_priority' => $operations->groupBy('priorite')->map->count(),
            'by_service' => $operations->groupBy('service')->map->count(),
            'avg_completion_time' => $this->calculateAvgCompletionTime($operations),
            'total_revenu' => $totalRevenu, // Ajout de la clé manquante
        ];

        return response()->json([
            'period' => $period,
            'start_date' => $startDate,
            'stats' => $stats,
            'operations' => $operations
        ]);
    }

    /**
     * Rapport du parc auto par période
     */
    public function fleetReport(Request $request, $period = 'month')
    {
        $startDate = $this->getStartDate($period);

        $vehicles = Vehicle::with(['vehicleAssignments', 'incidents', 'entretiens'])->get();

        $stats = [
            'total_vehicles' => $vehicles->count(),
            'available_vehicles' => $vehicles->where('disponibilite', true)->count(),
            'maintenance_vehicles' => $vehicles->where('etat', 'maintenance')->count(),
            'total_assignments' => $vehicles->sum(function($v) { return $v->vehicleAssignments->count(); }),
            'total_incidents' => $vehicles->sum(function($v) { return $v->incidents->count(); }),
            'total_maintenance_cost' => $vehicles->sum(function($v) { return $v->entretiens->sum('cout'); }),
        ];

        return response()->json([
            'period' => $period,
            'stats' => $stats,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Rapport d'entrepôt par période
     */
    public function warehouseReport(Request $request, $period = 'month')
    {
        $startDate = $this->getStartDate($period);

        $movements = StockMovement::where('created_at', '>=', $startDate)
            ->with(['product', 'warehouse'])
            ->get();

        $stats = [
            'total_movements' => $movements->count(),
            'entries' => $movements->where('type', 'entree')->count(),
            'exits' => $movements->where('type', 'sortie')->count(),
            'transfers' => $movements->where('type', 'transfert')->count(),
            'total_value' => $movements->sum('total_value'),
            'by_product' => $movements->groupBy('product_id')->map->sum('quantity'),
        ];

        return response()->json([
            'period' => $period,
            'stats' => $stats,
            'movements' => $movements
        ]);
    }

    /**
     * Rapport financier par période
     */
    public function financialReport(Request $request, $period = 'month')
    {
        $startDate = $this->getStartDate($period);

        $invoices = Invoice::where('created_at', '>=', $startDate)->get();
        $expenses = Expense::where('created_at', '>=', $startDate)->get();

        $stats = [
            'total_revenue' => $invoices->sum('net_amount'),
            'total_expenses' => $expenses->sum('montant'),
            'net_profit' => $invoices->sum('net_amount') - $expenses->sum('montant'),
            'paid_invoices' => $invoices->where('status', 'payee')->count(),
            'unpaid_invoices' => $invoices->where('status', '!=', 'payee')->count(),
            'pending_expenses' => $expenses->where('statut', 'en_attente')->count(),
            'by_invoice_status' => $invoices->groupBy('status')->map->count(),
            'by_expense_category' => $expenses->groupBy('categorie')->map->sum('montant'),
        ];

        return response()->json([
            'period' => $period,
            'stats' => $stats,
            'invoices' => $invoices,
            'expenses' => $expenses
        ]);
    }

    /**
     * Export des rapports
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'financial');
        $format = $request->get('format', 'json');
        $period = $request->get('period', 'month');

        try {
            $data = [];

            switch ($type) {
                case 'financial':
                    $startDate = $this->getStartDate($period);
                    $mouvements = \App\Models\MouvementCaisse::with(['caisse', 'createur'])
                        ->where('created_at', '>=', $startDate)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    $encaissements = \App\Models\Encaissement::with(['caisse', 'createur'])
                        ->where('created_at', '>=', $startDate)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    $depenses = \App\Models\DepenseCaisse::with(['caisse', 'createur'])
                        ->where('created_at', '>=', $startDate)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    $data = [
                        'mouvements' => $mouvements->toArray(),
                        'encaissements' => $encaissements->toArray(),
                        'depenses' => $depenses->toArray(),
                        'total_entrees' => $encaissements->sum('montant') + $mouvements->where('type_mouvement', 'entree')->sum('montant'),
                        'total_sorties' => $depenses->sum('montant') + $mouvements->where('type_mouvement', 'sortie')->sum('montant'),
                    ];
                    break;

                case 'operations':
                    if (class_exists('App\Models\Operation')) {
                        $data = Operation::with(['client', 'serviceOperationnel'])
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->toArray();
                    }
                    break;

                default:
                    return response()->json(['error' => 'Type de rapport inconnu'], 404);
            }

            if ($format === 'json') {
                return response()->json($data);
            }

            return response()->json(['message' => 'Format "' . $format . '" non encore implémenté']);
        } catch (\Exception $e) {
            \Log::error("Erreur reporting export: " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'export'], 500);
        }
    }

    /**
     * Obtenir la date de début selon la période
     */
    private function getStartDate($period)
    {
        return match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    /**
     * Calculer le temps moyen de completion
     */
    private function calculateAvgCompletionTime($operations)
    {
        $completedOps = $operations->where('statut_courant', 'termine');
        if ($completedOps->isEmpty()) return 0;

        $totalTime = 0;
        foreach ($completedOps as $op) {
            $startDate = Carbon::parse($op->created_at);
            $endDate = Carbon::parse($op->updated_at);
            $totalTime += $startDate->diffInHours($endDate);
        }

        return round($totalTime / $completedOps->count(), 1);
    }
}
