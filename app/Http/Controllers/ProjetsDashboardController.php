<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\Invoice;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\ProjectTask;
use App\Services\OperationCreationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProjetsDashboardController extends Controller
{
    public function __construct(
        private readonly OperationCreationService $operationCreationService,
    ) {
    }

    public function index(Request $request)
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // KPI: Projets actifs (opérations non terminées)
        $projetsActifs = Operation::where('statut_courant', '!=', 'terminee')->count();

        // KPI: Projets terminés ce mois (statut terminé mis à jour ce mois)
        $projetsTerminesMois = Operation::where('statut_courant', 'terminee')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();

        // KPI: Projets en retard (échéance passée et non terminés)
        $projetsEnRetard = Operation::whereNotNull('echeance')
            ->whereDate('echeance', '<', Carbon::today())
            ->where('statut_courant', '!=', 'terminee')
            ->count();

        // KPI: Montant total signé (somme net_amount des factures)
        $montantTotalSigne = (float) (Invoice::sum('net_amount') ?? 0);

        // KPI: Taux de réussite des opportunités (prospects convertis / total)
        $totalProspects = Prospect::count();
        $prospectsConvertis = Prospect::whereIn('statut', ['converti', 'signe', 'signé'])->count();
        $tauxConversion = $totalProspects > 0 ? round(($prospectsConvertis / max(1, $totalProspects)) * 100, 2) : 0;

        // KPI: Clients actifs
        $clientsActifs = Client::where('statut', 'actif')->count();

        // Statistiques détaillées des missions par statut
        $stats = [
            'planification' => Operation::where('statut_courant', 'planification')->count(),
            'en_cours' => Operation::where('statut_courant', 'en_cours')->count(),
            'en_attente' => Operation::where('statut_courant', 'en_attente')->count(),
            'termines' => Operation::where('statut_courant', 'terminee')->count(),
            'retard' => $projetsEnRetard,
            'budget_total' => Operation::sum('montant') ?? 0,
            'budget_reel' => Operation::where('is_paid', true)->sum('montant') ?? 0,
            'total' => Operation::count(),
            'delai_moyen' => $this->calculerDelaiMoyen(),
        ];

        // Données pour le diagramme de Gantt (missions actives avec dates)
        $ganttData = Operation::whereNotNull('created_at')
            ->whereNotNull('echeance')
            ->where('statut_courant', '!=', 'terminee')
            ->select('titre', 'statut_courant', 'created_at', 'echeance', 'demandeur_name')
            ->orderBy('created_at')
            ->get()
            ->map(function ($operation) {
                return [
                    'titre' => $operation->titre,
                    'statut' => $operation->statut_courant,
                    'date_debut' => $operation->created_at,
                    'date_fin' => $operation->echeance,
                    'responsable' => $operation->demandeur_name ?? 'Non assigné'
                ];
            })
            ->toArray();

        // Evolution mensuelle (12 derniers mois)
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = $now->copy()->subMonths($i)->format('Y-m');
        }

        $monthExprCreatedAt = $this->monthFormatExpression('created_at');
        $monthExprIssueDate = $this->monthFormatExpression('issue_date');

        $opsByMonth = Operation::selectRaw($monthExprCreatedAt . " as ym, COUNT(*) as c")
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy('ym')->pluck('c', 'ym');
        $opsPerMonth = array_map(fn($m) => (int)($opsByMonth[$m] ?? 0), $months);

        $amountByMonth = Invoice::selectRaw($monthExprIssueDate . " as ym, SUM(net_amount) as s")
            ->whereNotNull('issue_date')
            ->where('issue_date', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy('ym')->pluck('s', 'ym');
        $amountPerMonth = array_map(fn($m) => (float)($amountByMonth[$m] ?? 0), $months);

        // Funnel Opportunités (par statut)
        $funnelStages = ['prospection','proforma','negociation','signé'];
        $funnelData = [];
        foreach ($funnelStages as $stage) {
            $funnelData[] = Prospect::where('statut', $stage)->count();
        }

        // Répartition par type (proxy: priorite des opérations)
        $pieLabels = ['Haute', 'Moyenne', 'Basse'];
        $pieData = [
            Operation::where('priorite', 'haute')->count(),
            Operation::where('priorite', 'moyenne')->count(),
            Operation::where('priorite', 'basse')->count(),
        ];

        return view('projets.dashboard', [
            'kpis' => [
                'actifs' => $projetsActifs,
                'terminesMois' => $projetsTerminesMois,
                'retard' => $projetsEnRetard,
                'montantTotal' => $montantTotalSigne,
                'tauxConversion' => $tauxConversion,
                'clientsActifs' => $clientsActifs,
            ],
            'stats' => $stats,
            'ganttData' => $ganttData,
            'months' => $months,
            'opsPerMonth' => $opsPerMonth,
            'amountPerMonth' => $amountPerMonth,
            'funnelLabels' => $funnelStages,
            'funnelData' => $funnelData,
            'pieLabels' => $pieLabels,
            'pieData' => $pieData,
        ]);
    }

    private function monthFormatExpression(string $column): string
    {
        try {
            $driver = DB::getDriverName();
        } catch (\Throwable $e) {
            $driver = 'mysql';
        }

        return match ($driver) {
            'sqlite' => "strftime('%Y-%m', $column)",
            'pgsql' => "to_char($column, 'YYYY-MM')",
            default => "DATE_FORMAT($column, '%Y-%m')",
        };
    }

    /**
     * Calculer le délai moyen d'exécution des missions (en jours)
     */
    private function calculerDelaiMoyen(): string
    {
        try {
            $operationsTerminees = Operation::where('statut_courant', 'terminee')
                ->whereNotNull('created_at')
                ->whereNotNull('echeance')
                ->get();

            if ($operationsTerminees->isEmpty()) {
                return '0';
            }

            $totalDays = 0;
            $count = 0;

            foreach ($operationsTerminees as $operation) {
                $debut = Carbon::parse($operation->created_at);
                $fin = Carbon::parse($operation->echeance);
                $totalDays += $debut->diffInDays($fin);
                $count++;
            }

            return $count > 0 ? round($totalDays / $count, 1) : '0';
        } catch (\Exception $e) {
            return '0';
        }
    }

    /**
     * Liste des projets avec filtres avancés
     */
    public function list(Request $request)
    {
        $query = Operation::with('client');

        // Filtre statut
        if ($request->filled('statut')) {
            $query->where('statut_courant', $request->get('statut'));
        }

        // Filtre client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->get('client_id'));
        }

        // Filtre priorité
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->get('priorite'));
        }

        // Filtre période (dates de création)
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->get('date_debut'));
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->get('date_fin'));
        }

        // Clone de la requête filtrée pour calculer quelques statistiques simples
        $statsQuery = clone $query;

        $stats = [
            'en_cours' => (clone $statsQuery)->where('statut_courant', 'en_cours')->count(),
            'terminees' => (clone $statsQuery)->where('statut_courant', 'terminee')->count(),
            'en_retard' => (clone $statsQuery)->where('statut_courant', 'en_retard')->count(),
        ];
        $stats['total'] = array_sum($stats);

        $projets = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        $clients = Client::orderBy('raison_sociale')->get();

        return view('projets.index', compact('projets', 'clients', 'stats'));
    }

    /**
     * Enregistrer un nouveau projet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'titre' => 'required|string|max:255',
            'priorite' => 'nullable|in:haute,moyenne,basse',
            'echeance' => 'nullable|date',
            'service' => 'nullable|string|max:255',
            'responsable_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $operation = $this->operationCreationService
            ->createProject($validated, $request->user())['operation'];

        return redirect()->route('projets.show', $operation)
            ->with('success', 'Projet créé avec succès.');
    }

    /**
     * Formulaire d'édition d'un projet
     */
    public function edit(Operation $projet)
    {
        $clients = Client::orderBy('raison_sociale')->get();

        return view('projets.edit', compact('projet', 'clients'));
    }

    /**
     * Mettre à jour un projet
     */
    public function update(Request $request, Operation $projet)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'titre' => 'required|string|max:255',
            'priorite' => 'nullable|in:haute,moyenne,basse',
            'echeance' => 'nullable|date',
            'service' => 'nullable|string|max:255',
            'responsable_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $projet->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'priorite' => $validated['priorite'] ?? null,
            'echeance' => $validated['echeance'] ?? null,
            'service' => $validated['service'] ?? null,
            'responsable_name' => $validated['responsable_name'] ?? null,
            'client_id' => $validated['client_id'] ?? null,
        ]);

        return redirect()->route('projets.show', $projet)
            ->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Afficher le détail d'un projet
     */
    public function show(Operation $projet)
    {
        $projet->load(['client', 'invoice']);

        return view('projets.show', compact('projet'));
    }

    /**
     * Formulaire de création d'un projet
     */
    public function create()
    {
        $clients = Client::orderBy('raison_sociale')->get();
        $users = \App\Models\User::where('is_active', true)->orderBy('name')->get();
        $vehicules = \App\Models\Vehicule::where('disponible', true)->get();

        return view('projets.create', compact('clients', 'users', 'vehicules'));
    }

    /**
     * API: Obtenir les engins associés à un projet
     */
    public function getEnginsByProjet($projetId)
    {
        try {
            $projet = Operation::findOrFail($projetId);

            // Récupérer les véhicules associés au projet
            $engins = $projet->vehicules()->get()->map(function ($vehicule) {
                return [
                    'id' => $vehicule->id,
                    'immatriculation' => $vehicule->immatriculation,
                    'marque' => $vehicule->marque,
                    'modele' => $vehicule->modele,
                    'type_materiel' => $vehicule->type_materiel,
                    'prix_location' => $vehicule->prix_location ?? 0,
                    'prix_achat' => $vehicule->prix_achat ?? 0,
                    'disponible' => $vehicule->disponible,
                ];
            });

            return response()->json([
                'success' => true,
                'engins' => $engins,
                'projet' => [
                    'id' => $projet->id,
                    'titre' => $projet->titre,
                    'client' => $projet->client ? $projet->client->raison_sociale : 'N/A',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des engins: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher le rapport financier d'un projet
     */
    public function rapportFinancier($projetId)
    {
        $projet = Operation::with(['client', 'user', 'vehicules'])->findOrFail($projetId);
        return view('projets.rapport-financier', compact('projet'));
    }

    /**
     * API: Obtenir les données du rapport financier
     */
    public function rapportFinancierData($projetId, Request $request)
    {
        try {
            $projet = Operation::findOrFail($projetId);

            // Récupérer les pointages associés aux véhicules du projet
            $vehiculeIds = $projet->vehicules()->pluck('vehicules.id');

            $pointagesQuery = \App\Models\Pointage::with(['vehicle'])
                ->whereIn('vehicle_id', $vehiculeIds)
                ->where('statut', 'validé');

            // Appliquer les filtres
            if ($request->filled('date_debut')) {
                $pointagesQuery->whereDate('date_pointage', '>=', $request->date_debut);
            }
            if ($request->filled('date_fin')) {
                $pointagesQuery->whereDate('date_pointage', '<=', $request->date_fin);
            }
            if ($request->filled('engin_id')) {
                $pointagesQuery->where('vehicle_id', $request->engin_id);
            }
            if ($request->filled('type_pointage')) {
                $pointagesQuery->where('unit_type', $request->type_pointage);
            }

            $pointages = $pointagesQuery->orderByDesc('date_pointage')->get();

            // Calculer les totaux
            $totaux = [
                'total_unites' => $pointages->sum('quantity'),
                'total_fournisseur' => $pointages->sum('total_supplier_cost'),
                'total_client' => $pointages->sum('total_client_amount'),
                'total_marge' => $pointages->sum(function($p) {
                    return ($p->total_client_amount ?? 0) - ($p->total_supplier_cost ?? 0);
                }),
            ];

            // Formatter les pointages pour l'affichage
            $pointagesFormates = $pointages->map(function($pointage) {
                return [
                    'id' => $pointage->id,
                    'date_pointage' => $pointage->date_pointage,
                    'vehicule' => $pointage->vehicle,
                    'unit_type' => $pointage->unit_type,
                    'quantity' => $pointage->quantity,
                    'supplier_unit_cost' => $pointage->supplier_unit_cost,
                    'client_unit_price' => $pointage->client_unit_price,
                    'total_supplier_cost' => $pointage->total_supplier_cost,
                    'total_client_amount' => $pointage->total_client_amount,
                    'marge' => ($pointage->total_client_amount ?? 0) - ($pointage->total_supplier_cost ?? 0),
                ];
            });

            return response()->json([
                'success' => true,
                'pointages' => $pointagesFormates,
                'totaux' => $totaux,
                'projet' => [
                    'id' => $projet->id,
                    'titre' => $projet->titre,
                    'cout_estimatif' => $projet->cout_estimatif,
                    'montant_facturer' => $projet->montant_facturer,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer la facture PDF
     */
    public function genererFacture($projetId, Request $request)
    {
        try {
            $projet = Operation::findOrFail($projetId);

            // Récupérer les données des pointages avec les mêmes filtres que le rapport financier
            $vehiculeIds = $projet->vehicules()->pluck('vehicules.id');

            $pointagesQuery = \App\Models\Pointage::with(['vehicle'])
                ->whereIn('vehicle_id', $vehiculeIds)
                ->where('statut', 'validé');

            // Appliquer les filtres
            if ($request->filled('date_debut')) {
                $pointagesQuery->whereDate('date_pointage', '>=', $request->date_debut);
            }
            if ($request->filled('date_fin')) {
                $pointagesQuery->whereDate('date_pointage', '<=', $request->date_fin);
            }
            if ($request->filled('engin_id')) {
                $pointagesQuery->where('vehicle_id', $request->engin_id);
            }
            if ($request->filled('type_pointage')) {
                $pointagesQuery->where('unit_type', $request->type_pointage);
            }

            $pointages = $pointagesQuery->orderBy('date_pointage')->get();

            // Calculer les totaux
            $totaux = [
                'total_unites' => $pointages->sum('quantity'),
                'total_fournisseur' => $pointages->sum('total_supplier_cost'),
                'total_client' => $pointages->sum('total_client_amount'),
                'total_marge' => $pointages->sum(function($p) {
                    return ($p->total_client_amount ?? 0) - ($p->total_supplier_cost ?? 0);
                }),
            ];

            // Générer le PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('projets.facture-pdf', compact('projet', 'pointages', 'totaux'));

            // Format A4 en portrait
            $pdf->setPaper('A4', 'portrait');

            // Nom du fichier
            $filename = 'facture_' . str_replace(' ', '_', $projet->titre) . '_' . date('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération de la facture: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les rapports
     */
    public function reports(Request $request)
    {
        try {
            $query = Operation::with('client');

            // Filtres
            if ($request->filled('statut')) {
                $query->where('statut_courant', $request->get('statut'));
            }

            if ($request->filled('client_id')) {
                $query->where('client_id', $request->get('client_id'));
            }

            if ($request->filled('date_debut')) {
                $query->whereDate('created_at', '>=', $request->get('date_debut'));
            }

            if ($request->filled('date_fin')) {
                $query->whereDate('created_at', '<=', $request->get('date_fin'));
            }

            $projets = $query->orderByDesc('created_at')->get();
            $clients = Client::orderBy('raison_sociale')->get();

            // Statistiques
            $stats = [
                'total' => $projets->count(),
                'en_cours' => $projets->where('statut_courant', 'en_cours')->count(),
                'termines' => $projets->where('statut_courant', 'terminee')->count(),
                'en_retard' => $projets->where('statut_courant', 'en_retard')->count(),
            ];

            return view('projets.reports', compact('projets', 'clients', 'stats'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur dans ProjetsDashboardController@reports: ' . $e->getMessage());

            return view('projets.reports', [
                'projets' => collect([]),
                'clients' => collect([]),
                'stats' => [
                    'total' => 0,
                    'en_cours' => 0,
                    'termines' => 0,
                    'en_retard' => 0,
                ]
            ]);
        }
    }
}
