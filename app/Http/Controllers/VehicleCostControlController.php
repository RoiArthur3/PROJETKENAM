<?php

namespace App\Http\Controllers;

use App\Models\DepenseCaisse;
use App\Models\Facture;
use App\Models\Operation;
use App\Models\Personnel;
use App\Models\User;
use App\Models\Vehicule;
use App\Models\VehicleFinancialEntry;
use App\Models\VehicleMission;
use App\Models\Pointage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Services\ProjetFinancialService;

// ...existing code...
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class VehicleCostControlController extends Controller
{
    private ProjetFinancialService $financialService;

    public function __construct(ProjetFinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    /**
     * Afficher la liste des pointages (page dédiée)
     */
    public function list(Request $request)
    {
        try {
            if (!$this->hasPointagesTable()) {
                return redirect()->route('materiel.vehicules')
                    ->with('warning', 'Le module Cost Control n\'est pas encore initialisé (table vehicle_pointages manquante).');
            }

            $submodule = $request->input('submodule');
            $dateFrom  = $request->input('date_from');
            $dateTo    = $request->input('date_to');
            $missionId = $request->input('mission_id');
            $search    = $request->input('search');

            $pointagesQuery = Pointage::with(['mission.vehicle', 'mission.client', 'vehicle', 'driver', 'operation'])
                ->when($submodule && $this->pointageColumnExists('submodule'), fn ($q) => $q->where('submodule', $submodule))
                ->when($dateFrom, fn ($q) => $q->whereDate('date_pointage', '>=', $dateFrom))
                ->when($dateTo,   fn ($q) => $q->whereDate('date_pointage', '<=', $dateTo))
                ->when($missionId, fn ($q) => $q->where('vehicle_mission_id', $missionId))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('task_label', 'like', "%{$search}%")
                              ->orWhere('departure_location', 'like', "%{$search}%")
                              ->orWhere('arrival_location', 'like', "%{$search}%")
                              ->orWhere('delivery_note_number', 'like', "%{$search}%")
                              ->orWhereHas('vehicle', fn ($vq) => $vq->where('immatriculation', 'like', "%{$search}%"));
                    });
                })
                ->orderByDesc('date_pointage')
                ->orderByDesc('id');

            $pointages = $pointagesQuery->paginate(20)->withQueryString();

            $summaryQuery = clone $pointagesQuery;
            $listSummary = [
                'total_pointages'    => (clone $summaryQuery)->count(),
                'total_supplier_cost'=> (float) (clone $summaryQuery)->sum('total_supplier_cost'),
                'total_client_amount'=> (float) (clone $summaryQuery)->sum('total_client_amount'),
                'total_hours'        => $this->pointageColumnExists('unit_type')
                    ? (float) (clone $summaryQuery)->where('unit_type', 'heure')->sum('quantity') : 0.0,
                'total_days'         => $this->pointageColumnExists('unit_type')
                    ? (float) (clone $summaryQuery)->where('unit_type', 'jour')->sum('quantity')  : 0.0,
                'total_trips'        => $this->pointageColumnExists('trip_count')
                    ? (float) (clone $summaryQuery)->sum('trip_count') : 0.0,
            ];
            $listSummary['total_margin'] = $listSummary['total_client_amount'] - $listSummary['total_supplier_cost'];

            $missions = $this->missionTableExists()
                ? VehicleMission::with(['vehicle', 'client'])->orderByDesc('start_at')->limit(200)->get()
                : collect();

            if ($submodule === 'camion_plateau') {
                $pageTitle = 'Liste des pointages - Camions Plateau';
            } elseif ($submodule === 'engin') {
                $pageTitle = 'Liste des pointages - Standard';
            } else {
                $pageTitle = 'Liste des pointages - Tous modules';
            }

            $vehicles = Vehicule::orderBy('immatriculation')->get();
            $suppliers = \App\Models\Fournisseur::orderBy('raison_sociale')->get();

            return view('materiel.cost-control.list', compact('pointages', 'listSummary', 'submodule', 'pageTitle', 'missions', 'vehicles', 'suppliers'));
        } catch (\Throwable $e) {
            Log::error('Cost control list failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return redirect()->route('materiel.vehicules')->with('error', 'Impossible de charger la liste Cost Control pour le moment.');
        }
    }

    public function camionPlateau(Request $request)
    {
        $request->merge(['submodule' => 'camion_plateau']);

        return $this->list($request);
    }

    public function listStandard(Request $request)
    {
        $request->merge(['submodule' => 'engin']);

        return $this->list($request);
    }

    public function listCamionPlateau(Request $request)
    {
        $request->merge(['submodule' => 'camion_plateau']);

        return $this->list($request);
    }

    /**
     * Sous-module : rapport Cost Control par engin (ligne par ligne) avec montants.
     */
    public function rapportCostControl(Request $request)
    {
        try {
            if (!$this->hasPointagesTable()) {
                return redirect()->route('materiel.vehicules')
                    ->with('warning', 'Le module Cost Control n\'est pas encore initialise (table vehicle_pointages manquante).');
            }

            $vehicleId = $request->input('vehicle_id');
            $missionId = $request->input('mission_id');
            $dateFrom  = $request->input('date_from');
            $dateTo    = $request->input('date_to');
            $export    = $request->input('export');

            $baseQuery = Pointage::with(['vehicle', 'mission.client'])
                ->when($this->pointageColumnExists('submodule'), fn ($q) => $q->where('submodule', 'engin'))
                ->when($vehicleId, fn ($q) => $q->where('vehicle_id', $vehicleId))
                ->when($missionId, fn ($q) => $q->where('vehicle_mission_id', $missionId))
                ->when($dateFrom, fn ($q) => $q->whereDate('date_pointage', '>=', $dateFrom))
                ->when($dateTo, fn ($q) => $q->whereDate('date_pointage', '<=', $dateTo));

            $pointages = (clone $baseQuery)
                ->orderBy('date_pointage')
                ->orderBy('vehicle_id')
                ->orderBy('vehicle_mission_id')
                ->orderBy('id')
                ->paginate(100)
                ->withQueryString();

            $allRows = (clone $baseQuery)
                ->orderBy('date_pointage')
                ->orderBy('id')
                ->get();

            $totals = [
                'lines' => $allRows->count(),
                'quantity' => (float) $allRows->sum('quantity'),
                'supplier' => (float) $allRows->sum('total_supplier_cost'),
                'client' => (float) $allRows->sum('total_client_amount'),
            ];
            $totals['margin'] = $totals['client'] - $totals['supplier'];

            if ($export === 'csv') {
                return $this->exportRapportCostControlCsv($allRows);
            }

            if ($export === 'xlsx') {
                return $this->exportRapportCostControlXlsx($allRows);
            }

            if ($export === 'pdf') {
                $filename = 'rapport-cost-controle-engin-' . now()->format('Ymd-His') . '.pdf';

                return Pdf::loadView('materiel.cost-control.rapport-cost-controle-pdf', [
                    'rows' => $allRows,
                    'totals' => $totals,
                    'filters' => [
                        'vehicle_id' => $vehicleId,
                        'mission_id' => $missionId,
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                    ],
                ])->setPaper('a4', 'landscape')->download($filename);
            }

            $byVehicle = $allRows
                ->groupBy(function ($row) {
                    return $row->vehicle_id ?: 'unknown';
                })
                ->map(function ($rows, $vehicleKey) {
                    $first = $rows->first();
                    $vehicleLabel = optional($first->vehicle)->immatriculation
                        ?: optional($first->vehicle)->name
                        ?: 'Engin inconnu';

                    return [
                        'vehicle_key' => (string) $vehicleKey,
                        'vehicle_label' => $vehicleLabel,
                        'lines' => $rows->count(),
                        'quantity' => (float) $rows->sum('quantity'),
                        'supplier' => (float) $rows->sum('total_supplier_cost'),
                        'client' => (float) $rows->sum('total_client_amount'),
                        'margin' => (float) $rows->sum('total_client_amount') - (float) $rows->sum('total_supplier_cost'),
                    ];
                })
                ->sortBy('vehicle_label', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            $vehicles = Vehicule::orderBy('immatriculation')->get();

            $missionIds = (clone $baseQuery)
                ->whereNotNull('vehicle_mission_id')
                ->distinct()
                ->pluck('vehicle_mission_id')
                ->filter();

            $missions = $this->missionTableExists()
                ? VehicleMission::with(['vehicle', 'client'])
                    ->whereIn('id', $missionIds)
                    ->orderByDesc('start_at')
                    ->get()
                : collect();

            return view('materiel.cost-control.rapport-cost-controle', [
                'pointages' => $pointages,
                'totals' => $totals,
                'byVehicle' => $byVehicle,
                'vehicles' => $vehicles,
                'missions' => $missions,
                'filters' => [
                    'vehicle_id' => $vehicleId,
                    'mission_id' => $missionId,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Rapport cost control failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);

            return redirect()->route('materiel.cost-control.engin.list')
                ->with('error', 'Impossible de generer le rapport Cost Control pour le moment.');
        }
    }

    /**
     * Sous-module : liste des projets de location terminés avec récap pointages + marge.
     */
    public function projetsTermines(Request $request)
    {
        try {
            if (!$this->missionTableExists()) {
                return redirect()->route('materiel.cost-control.list')
                    ->with('warning', 'Le module Missions n\'est pas disponible.');
            }

            $search    = $request->input('search');
            $dateFrom  = $request->input('date_from');
            $dateTo    = $request->input('date_to');
            $submodule = $request->input('submodule');

            $missionsQuery = VehicleMission::with(['vehicle', 'client', 'driver', 'pointages'])
                ->where('status', 'done')
                ->when($submodule, fn ($q) => $q->where('pointage_submodule', $submodule))
                ->when($dateFrom, fn ($q) => $q->whereDate('end_at', '>=', $dateFrom))
                ->when($dateTo,   fn ($q) => $q->whereDate('end_at', '<=', $dateTo))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('reference', 'like', "%{$search}%")
                              ->orWhere('destination', 'like', "%{$search}%")
                              ->orWhereHas('client', fn ($cq) => $cq->where('nom', 'like', "%{$search}%"))
                              ->orWhereHas('vehicle', fn ($vq) => $vq->where('immatriculation', 'like', "%{$search}%"));
                    });
                })
                ->orderByDesc('end_at');

            $missions = $missionsQuery->paginate(20)->withQueryString();

            // Compute per-mission pointage aggregates
            foreach ($missions as $mission) {
                $mission->ptg_count          = $mission->pointages->count();
                $mission->ptg_supplier_total = (float) $mission->pointages->sum('total_supplier_cost');
                $mission->ptg_client_total   = (float) $mission->pointages->sum('total_client_amount');
                $mission->ptg_margin         = $mission->ptg_client_total - $mission->ptg_supplier_total;
                $mission->ptg_margin_pct     = $mission->ptg_client_total > 0
                    ? round($mission->ptg_margin / $mission->ptg_client_total * 100, 1) : 0;
            }

            $totals = [
                'missions'  => $missions->total(),
                'supplier'  => (clone $missionsQuery)->get()->sum('ptg_supplier_total'),
                'client'    => (clone $missionsQuery)->get()->sum('ptg_client_total'),
            ];
            $totals['margin'] = $totals['client'] - $totals['supplier'];

            return view('materiel.cost-control.projets-termines', compact('missions', 'totals', 'search', 'dateFrom', 'dateTo', 'submodule'));
        } catch (\Throwable $e) {
            Log::error('Projets terminés failed', ['error' => $e->getMessage()]);
            return redirect()->route('materiel.cost-control.list')->with('error', 'Impossible de charger les projets terminés.');
        }
    }

    /**
     * Fiche de pointage imprimable pour une mission terminée.
     */
    public function fichePointage(VehicleMission $mission)
    {
        $mission->load(['vehicle', 'client', 'driver', 'pointages' => function ($q) {
            $q->with(['vehicle', 'driver'])->orderBy('date_pointage');
        }]);

        $supplierTotal = (float) $mission->pointages->sum('total_supplier_cost');
        $clientTotal   = (float) $mission->pointages->sum('total_client_amount');
        $margin        = $clientTotal - $supplierTotal;
        $marginPct     = $clientTotal > 0 ? round($margin / $clientTotal * 100, 1) : 0;

        return view('materiel.cost-control.fiche-pointage', compact('mission', 'supplierTotal', 'clientTotal', 'margin', 'marginPct'));
    }
    public function index(Request $request)
    {
        try {
            if (!$this->hasPointagesTable() || !$this->hasVehicleFinancialEntriesTable()) {
                return redirect()->route('materiel.vehicules')
                    ->with('warning', 'Le module Cost Control n\'est pas encore initialisé (tables requises manquantes).');
            }

            $pointagesQuery = Pointage::with([
                'mission.vehicle',
                'mission.client',
                'vehicle',
                'driver',
                'operation',
            ])
                ->when($request->filled('vehicle_mission_id'), fn ($query) => $query->where('vehicle_mission_id', $request->vehicle_mission_id))
                ->when($request->filled('vehicle_id'), fn ($query) => $query->where('vehicle_id', $request->vehicle_id))
                ->when($request->filled('date_from'), fn ($query) => $query->whereDate('date_pointage', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn ($query) => $query->whereDate('date_pointage', '<=', $request->date_to))
                ->orderByDesc('date_pointage')
                ->orderByDesc('id');

            $entriesQuery = VehicleFinancialEntry::with([
                'mission.vehicle',
                'mission.client',
                'vehicle',
                'operation',
                'decaissement',
                'facture.client',
            ])
                ->when($request->filled('vehicle_mission_id'), fn ($query) => $query->where('vehicle_mission_id', $request->vehicle_mission_id))
                ->when($request->filled('vehicle_id'), fn ($query) => $query->where('vehicle_id', $request->vehicle_id))
                ->when($request->filled('date_from'), fn ($query) => $query->whereDate('transaction_date', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn ($query) => $query->whereDate('transaction_date', '<=', $request->date_to))
                ->orderByDesc('transaction_date')
                ->orderByDesc('id');

        $pointages = $pointagesQuery->paginate(15, ['*'], 'pointages_page')->withQueryString();
        $financialEntries = $entriesQuery->paginate(15, ['*'], 'entries_page')->withQueryString();

        $pointageSummaryQuery = clone $pointagesQuery;
        $entriesSummaryQuery = clone $entriesQuery;
        $pointageCost = $this->pointageColumnExists('total_supplier_cost')
            ? (float) (clone $pointageSummaryQuery)->sum('total_supplier_cost')
            : 0.0;
        $operationalRevenue = $this->pointageColumnExists('total_client_amount')
            ? (float) (clone $pointageSummaryQuery)->sum('total_client_amount')
            : 0.0;
        $additionalCharges = (float) (clone $entriesSummaryQuery)->where('type', 'expense')->sum('amount');
        $recognizedRevenue = (float) (clone $entriesSummaryQuery)->where('type', 'revenue')->sum('amount');
        $totalCost = $pointageCost + $additionalCharges;
        $totalRevenue = $recognizedRevenue > 0 ? $recognizedRevenue : $operationalRevenue;

        $summary = [
            'total_pointages' => (clone $pointageSummaryQuery)->count(),
            'total_hours' => $this->pointageColumnExists('quantity') && $this->pointageColumnExists('unit_type')
                ? (float) (clone $pointageSummaryQuery)->where('unit_type', 'heure')->sum('quantity')
                : 0.0,
            'total_days' => $this->pointageColumnExists('quantity') && $this->pointageColumnExists('unit_type')
                ? (float) (clone $pointageSummaryQuery)->where('unit_type', 'jour')->sum('quantity')
                : 0.0,
            'pointage_cost' => $pointageCost,
            'additional_charges' => $additionalCharges,
            'total_cost' => $totalCost,
            'operational_revenue' => $operationalRevenue,
            'recognized_revenue' => $recognizedRevenue,
            'total_revenue' => $totalRevenue,
            'total_entries' => (clone $entriesSummaryQuery)->count(),
        ];
        $summary['total_margin'] = (float) $summary['total_revenue'] - (float) $summary['total_cost'];

        // Utiliser une sous-requête pour éviter le problème ONLY_FULL_GROUP_BY
        $chargesByCategory = $this->hasVehicleFinancialEntriesTable()
            ? DB::table(DB::raw('(
                SELECT
                    category,
                    SUM(amount) as total,
                    MAX(transaction_date) as derniere_entree,
                    MAX(id) as dernier_id
                FROM vehicle_financial_entries
                WHERE type = \'expense\'
                GROUP BY category
            ) as sub'))
                ->orderByDesc('derniere_entree')
                ->orderByDesc('dernier_id')
                ->get()
            : collect();

        $missions = $this->missionTableExists()
            ? VehicleMission::with(['vehicle', 'driver', 'client'])
                ->orderByDesc('start_at')
                ->limit(100)
                ->get()
            : collect();

        $missionSummaries = $this->missionTableExists()
            ? VehicleMission::with(['vehicle', 'driver', 'client', 'pointages', 'financialEntries'])
                ->orderByDesc('start_at')
                ->limit(20)
                ->get()
            : collect();

        $vehicles = Schema::hasTable('vehicules')
            ? Vehicule::orderBy('immatriculation')->get()
            : collect();

            return view('materiel.cost-control.index', compact(
                'pointages',
                'financialEntries',
                'summary',
                'chargesByCategory',
                'missions',
                'missionSummaries',
                'vehicles'
            ));
        } catch (\Throwable $e) {
            Log::error('Cost control dashboard failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request' => $request->all(),
            ]);

            return redirect()->route('materiel.vehicules')
                ->with('error', 'Impossible de charger le dashboard Cost Control pour le moment.');
        }
    }

    public function create(Request $request)
    {
        $pointage = new Pointage();
        $missions = $this->missionTableExists()
            ? VehicleMission::with(['vehicle', 'driver', 'client'])
                ->orderByDesc('start_at')
                ->limit(100)
                ->get()
            : collect();
        $vehicles = Vehicule::orderBy('immatriculation')->get();
        $drivers = Personnel::orderBy('nom')->orderBy('prenoms')->get();
        $operations = Operation::orderByDesc('created_at')->limit(100)->get();
        $selectedMissionId = $request->integer('mission');
        return view('materiel.cost-control.form', compact(
            'pointage',
            'missions',
            'vehicles',
            'drivers',
            'operations',
            'selectedMissionId'
        ));
    }

    /**
     * Formulaire simplifié pour pointage Engin Standard
     * Avec sélection du fournisseur (Kenam ou autre)
     */
    public function createEnginPointage(Request $request)
    {
        // Charger les projets actifs
        $projets = Operation::where('statut_courant', '!=', 'terminee')
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer tous les véhicules disponibles (sauf les Camions qui vont au plateau)
        $vehicleModels = Vehicule::where('disponible', true)
            ->where(function ($q) {
                $q->whereNull('type_materiel')
                  ->orWhere('type_materiel', '!=', 'Camion');
            })
            ->orderBy('immatriculation')
            ->get();

        $vehicles = $vehicleModels->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'immatriculation' => $vehicle->immatriculation,
                'marque' => $vehicle->marque,
                'modele' => $vehicle->modele,
                'client_price_per_hour' => $vehicle->prix_location ?? 0,
                'supplier_price_per_hour' => $vehicle->prix_achat ?? 0,
                'prix_location' => $vehicle->prix_location ?? 0,
            ];
        });

        // Charger les fournisseurs
        $suppliers = \App\Models\Fournisseur::orderBy('raison_sociale')->get();

        return view('materiel.cost-control.engin-pointage-form', [
            'projets' => $projets,
            'vehicles' => $vehicleModels,
            'suppliers' => $suppliers,
            'vehiclesWithPrices' => $vehicles->toArray(),
        ]);
    }

    public function createFinancialEntry(Request $request)
    {
        $entry = new VehicleFinancialEntry();
        $missions = $this->missionTableExists()
            ? VehicleMission::with(['vehicle', 'driver', 'client'])
                ->orderByDesc('start_at')
                ->limit(100)
                ->get()
            : collect();
        $vehicles = Vehicule::orderBy('immatriculation')->get();
        $operations = Operation::orderByDesc('created_at')->limit(100)->get();
        $decaissements = DepenseCaisse::with(['caisse'])
            ->orderByDesc('date_depense')
            ->limit(200)
            ->get();
        $factures = Facture::with('client')
            ->orderByDesc('date_facture')
            ->limit(200)
            ->get();
        $selectedMissionId = $request->integer('mission');
        $defaultEntryType = $request->input('type', 'charge');

        return view('materiel.cost-control.financial-entry-form', compact(
            'entry',
            'missions',
            'vehicles',
            'operations',
            'decaissements',
            'factures',
            'selectedMissionId',
            'defaultEntryType'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validatePointage($request);
        $pointage = Pointage::create($this->buildPayload($data));
        $this->syncMissionTimelineFromPointages($pointage->mission);

        // Mettre à jour le montant à facturer du projet si un projet est associé
        if (!empty($data['projet_id'])) {
            try {
                $this->financialService->updateMontantFacturer($data['projet_id']);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du montant à facturer: ' . $e->getMessage());
            }
        }

        return redirect()->route(
            $pointage->submodule === 'camion_plateau' ? 'materiel.cost-control.plateau.list' : 'materiel.cost-control.engin.list'
        )
            ->with('success', 'Pointage d\'engin enregistré avec succès.');
    }

    public function storeBatch(Request $request)
    {
        $rows = $request->input('rows', []);
        $saved = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            // Skip totally empty rows (no mission and no vehicle selected)
            if (empty($row['vehicle_mission_id']) && empty($row['vehicle_id'])) {
                continue;
            }
            if (empty($row['date_pointage'])) {
                continue;
            }

            try {
                // Re-hydrate as a fake Request so we can reuse validatePointage / buildPayload
                $fakeRequest = new Request($row);
                $data = $this->validatePointage($fakeRequest);
                $pointage = Pointage::create($this->buildPayload($data));
                $this->syncMissionTimelineFromPointages($pointage->mission);
                $saved++;
            } catch (\Illuminate\Validation\ValidationException $e) {
                $rowLabel = 'Ligne ' . ($index + 1);
                foreach ($e->errors() as $field => $msgs) {
                    $errors[] = "{$rowLabel} – {$field}: " . implode(', ', $msgs);
                }
            } catch (\Throwable $e) {
                $errors[] = 'Ligne ' . ($index + 1) . ' : ' . $e->getMessage();
            }
        }

        $submodule = $request->input('submodule');
        $redirectRoute = $submodule === 'camion_plateau'
            ? 'materiel.cost-control.camion-plateau'
            : 'materiel.cost-control.list';
        $params = $submodule ? ['submodule' => $submodule] : [];

        if ($saved > 0 && empty($errors)) {
            return redirect()->route($redirectRoute, $params)
                ->with('success', "{$saved} pointage(s) enregistré(s) avec succès.");
        }

        if ($saved > 0 && !empty($errors)) {
            return redirect()->route($redirectRoute, $params)
                ->with('success', "{$saved} pointage(s) enregistré(s).")
                ->with('warning', 'Certaines lignes ont été ignorées : ' . implode(' | ', $errors));
        }

        return redirect()->route($redirectRoute, $params)
            ->withErrors(['batch' => empty($errors) ? 'Aucune ligne valide à enregistrer.' : implode(' | ', $errors)]);
    }

    public function edit(Pointage $pointage)
    {
        $missions = $this->missionTableExists()
            ? VehicleMission::with(['vehicle', 'driver', 'client'])
                ->orderByDesc('start_at')
                ->limit(100)
                ->get()
            : collect();
        $vehicles = Vehicule::orderBy('immatriculation')->get();
        $drivers = Personnel::orderBy('nom')->orderBy('prenoms')->get();
        $operations = Operation::orderByDesc('created_at')->limit(100)->get();
        $selectedMissionId = $pointage->vehicle_mission_id;

        return view('materiel.cost-control.form', compact(
            'pointage',
            'missions',
            'vehicles',
            'drivers',
            'operations',
            'selectedMissionId'
        ));
    }

    public function show(Pointage $pointage)
    {
        $pointage->load(['mission.vehicle', 'mission.client', 'vehicle', 'driver', 'operation']);

        return view('materiel.cost-control.show', compact('pointage'));
    }

    public function update(Request $request, Pointage $pointage)
    {
        $data = $this->validatePointage($request);
        $pointage->update($this->buildPayload($data));
        $this->syncMissionTimelineFromPointages($pointage->mission);

        // Mettre à jour le montant à facturer du projet si un projet est associé
        if (!empty($data['projet_id'])) {
            try {
                $this->financialService->updateMontantFacturer($data['projet_id']);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du montant à facturer: ' . $e->getMessage());
            }
        } elseif ($pointage->operation_id) {
            // Si le pointage était déjà associé à un projet, mettre à jour ce projet
            try {
                $this->financialService->updateMontantFacturer($pointage->operation_id);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du montant à facturer: ' . $e->getMessage());
            }
        }

        return redirect()->route(
            $pointage->submodule === 'camion_plateau' ? 'materiel.cost-control.plateau.list' : 'materiel.cost-control.engin.list'
        )
            ->with('success', 'Pointage d\'engin mis à jour avec succès.');
    }

    public function storeFinancialEntry(Request $request)
    {
        $data = $this->validateFinancialEntry($request);
        VehicleFinancialEntry::create($this->buildFinancialEntryPayload($data));

        return redirect()->route('materiel.cost-control.index')
            ->with('success', 'Écriture financière enregistrée avec succès.');
    }

    public function editFinancialEntry(VehicleFinancialEntry $entry)
    {
        $missions = $this->missionTableExists()
            ? VehicleMission::with(['vehicle', 'driver', 'client'])
                ->orderByDesc('start_at')
                ->limit(100)
                ->get()
            : collect();
        $vehicles = Vehicule::orderBy('immatriculation')->get();
        $operations = Operation::orderByDesc('created_at')->limit(100)->get();
        $decaissements = DepenseCaisse::with(['caisse'])
            ->orderByDesc('date_depense')
            ->limit(200)
            ->get();
        $factures = Facture::with('client')
            ->orderByDesc('date_facture')
            ->limit(200)
            ->get();
        $selectedMissionId = $entry->vehicle_mission_id;
        $defaultEntryType = $entry->type;

        return view('materiel.cost-control.financial-entry-form', compact(
            'entry',
            'missions',
            'vehicles',
            'operations',
            'decaissements',
            'factures',
            'selectedMissionId',
            'defaultEntryType'
        ));
    }

    public function showFinancialEntry(VehicleFinancialEntry $entry)
    {
        $entry->load(['mission.vehicle', 'mission.client', 'vehicle', 'operation', 'decaissement.caisse', 'facture.client']);

        return view('materiel.cost-control.financial-entry-show', compact('entry'));
    }

    public function updateFinancialEntry(Request $request, VehicleFinancialEntry $entry)
    {
        $data = $this->validateFinancialEntry($request);
        $entry->update($this->buildFinancialEntryPayload($data));

        return redirect()->route('materiel.cost-control.index')
            ->with('success', 'Écriture financière mise à jour avec succès.');
    }

    public function destroyFinancialEntry(VehicleFinancialEntry $entry)
    {
        $entry->delete();

        return redirect()->route('materiel.cost-control.index')
            ->with('success', 'Écriture financière supprimée avec succès.');
    }

    public function destroy(Pointage $pointage)
    {
        $mission = $pointage->mission;
        $submodule = $pointage->submodule;
        $operationId = $pointage->operation_id;

        $pointage->delete();
        $this->syncMissionTimelineFromPointages($mission);

        // Mettre à jour le montant à facturer du projet si un projet est associé
        if ($operationId) {
            try {
                $this->financialService->updateMontantFacturer($operationId);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du montant à facturer: ' . $e->getMessage());
            }
        }

        return redirect()->route(
            $submodule === 'camion_plateau' ? 'materiel.cost-control.camion-plateau' : 'materiel.cost-control.list'
        )
            ->with('success', 'Pointage d\'engin supprimé avec succès.');
    }

    private function validatePointage(Request $request): array
    {
        $missionRule = $this->missionTableExists()
            ? 'nullable|exists:vehicle_missions,id|required_without:operation_id'
            : 'nullable';

        $rules = [
            'vehicle_mission_id' => $missionRule,
            'operation_id' => 'nullable|exists:operations,id|required_without:vehicle_mission_id',
            'projet_id' => 'nullable|exists:operations,id',
            'vehicle_id' => 'nullable|exists:vehicules,id|required_without:vehicle_mission_id',
            'fournisseur_id' => 'nullable|string',
            'date_pointage' => 'required|date',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i',
            'unit_type' => 'nullable|in:heure,jour',
            'quantity' => 'nullable|numeric|min:0.01',
            'supplier_unit_cost' => 'nullable|numeric|min:0',
            'client_unit_price' => 'nullable|numeric|min:0',
            'submodule' => 'nullable|in:engin,camion_plateau',
            'billing_mode' => 'nullable|in:standard,monthly,trip',
            'task_label' => 'nullable|string|max:255',
            'trip_count' => 'nullable|numeric|min:0',
            'monthly_trip_threshold' => 'nullable|numeric|min:0',
            'monthly_flat_rate' => 'nullable|numeric|min:0',
            'extra_trip_unit_price' => 'nullable|numeric|min:0',
            'departure_location' => 'nullable|string|max:255',
            'arrival_location' => 'nullable|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
            'fuel_amount' => 'nullable|numeric|min:0',
            'delivery_note_number' => 'nullable|string|max:255',
            'road_fees' => 'nullable|numeric|min:0',
            'toll_fees' => 'nullable|numeric|min:0',
            'other_fees' => 'nullable|numeric|min:0',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
        ];

        if ($request->input('submodule') === 'camion_plateau') {
            $rules['task_label'] = 'required|string|max:255';
            $rules['trip_count'] = 'required|numeric|min:1';
            $rules['supplier_unit_cost'] = 'required|numeric|min:0';
            $rules['billing_mode'] = 'required|in:monthly,trip';

            if ($request->input('billing_mode') === 'monthly') {
                $rules['monthly_trip_threshold'] = 'required|numeric|min:0';
                $rules['monthly_flat_rate'] = 'required|numeric|min:0';
            }

            if ($request->input('billing_mode') === 'trip') {
                $rules['client_unit_price'] = 'required|numeric|min:0';
                $rules['departure_location'] = 'required|string|max:255';
                $rules['arrival_location'] = 'required|string|max:255';
                $rules['distance_km'] = 'required|numeric|min:0';
                $rules['delivery_note_number'] = 'required|string|max:255';
            }
        }

        return $request->validate($rules);
    }

    private function buildPayload(array $data): array
    {
        $mission = $this->missionTableExists() && !empty($data['vehicle_mission_id'])
            ? VehicleMission::find($data['vehicle_mission_id'])
            : null;

        if ($mission) {
            $missionProfile = $mission->resolvePointageProfile();
            if (empty($data['submodule'])) {
                $data['submodule'] = $missionProfile['submodule'];
            }
            if (empty($data['billing_mode'])) {
                $data['billing_mode'] = $missionProfile['billing_mode'];
            }
            $data['vehicle_id'] = $mission->vehicle_id;
            $data['fournisseur_id'] = $data['fournisseur_id'] ?? $mission->supplier_id;
            $data['supplier_unit_cost'] = $data['supplier_unit_cost'] ?? $mission->daily_supplier_price ?? 0;
            $data['client_unit_price'] = $data['client_unit_price'] ?? $mission->daily_client_price ?? 0;
            $data['operation_id'] = $data['operation_id'] ?? $mission->operation_id;
        }

        // Déterminer si c'est Kenam ou un autre fournisseur
        $isKenam = ($data['fournisseur_id'] ?? null) === 'kenam';

        // Si c'est Kenam, pas de coût fournisseur
        if ($isKenam) {
            $data['supplier_unit_cost'] = 0;
            $data['source'] = 'kenam';
        } else {
            $data['source'] = 'supplier';
        }

        if (empty($data['submodule'])) {
            $vehicleType = '';
            if (!empty($data['vehicle_id'])) {
                $vehicle = Vehicule::find($data['vehicle_id']);
                $vehicleType = strtolower((string) ($vehicle->type_materiel ?? ''));
            }
            $data['submodule'] = str_contains($vehicleType, 'camion') ? 'camion_plateau' : 'engin';
        }

        if ($data['submodule'] === 'engin') {
            $data['billing_mode'] = 'standard';
        } elseif (empty($data['billing_mode']) || !in_array($data['billing_mode'], ['monthly', 'trip'], true)) {
            $data['billing_mode'] = 'trip';
        }

        $submodule = $data['submodule'];

        $pointageTable = $this->pointageTableName();

        if (!empty($data['heure_debut']) && $pointageTable && Schema::hasColumn($pointageTable, 'heure_arrivee')) {
            $data['heure_arrivee'] = $data['heure_debut'];
        }
        if (!empty($data['heure_fin']) && $pointageTable && Schema::hasColumn($pointageTable, 'heure_depart')) {
            $data['heure_depart'] = $data['heure_fin'];
        }

        $data['statut'] = $data['statut'] ?? 'validé';

        foreach (['trip_count', 'monthly_trip_threshold', 'monthly_flat_rate', 'extra_trip_unit_price', 'distance_km', 'fuel_amount', 'road_fees', 'toll_fees', 'other_fees'] as $numericField) {
            $data[$numericField] = (float) ($data[$numericField] ?? 0);
        }

        if ($submodule === 'camion_plateau') {
            $tripCount = max(1, (float) ($data['trip_count'] ?? 1));
            $supplierAmount = (float) ($data['supplier_unit_cost'] ?? 0);
            $clientUnitPrice = (float) ($data['client_unit_price'] ?? 0);
            $monthlyThreshold = (float) ($data['monthly_trip_threshold'] ?? 0);
            $monthlyFlatRate = (float) ($data['monthly_flat_rate'] ?? 0);
            $extraTripUnitPrice = (float) ($data['extra_trip_unit_price'] ?? 0);
            $additionalCosts = (float) $data['fuel_amount']
                + (float) $data['road_fees']
                + (float) $data['toll_fees']
                + (float) $data['other_fees'];

            if ($data['billing_mode'] === 'monthly') {
                $extraTrips = max(0, $tripCount - $monthlyThreshold);
                $clientAmount = $monthlyFlatRate + ($extraTrips * $extraTripUnitPrice);
                $data['client_unit_price'] = $tripCount > 0 ? round($clientAmount / $tripCount, 2) : $monthlyFlatRate;
            } else {
                $clientAmount = $tripCount * $clientUnitPrice;
            }

            $data['trip_count'] = $tripCount;
            $data['unit_type'] = 'jour';
            $data['quantity'] = $tripCount;
            $data['supplier_unit_cost'] = $supplierAmount;
            $data['total_supplier_cost'] = $supplierAmount + $additionalCosts;
            $data['total_client_amount'] = $clientAmount;
            $data['created_by'] = Auth::id();

            return $data;
        }

        if (!isset($data['unit_type']) || empty($data['unit_type'])) {
            $data['unit_type'] = 'heure';
        }

        if ((empty($data['quantity']) || (float) $data['quantity'] <= 0) && !empty($data['heure_debut']) && !empty($data['heure_fin'])) {
            $start = Carbon::createFromFormat('H:i', $data['heure_debut']);
            $end = Carbon::createFromFormat('H:i', $data['heure_fin']);
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
            $minutes = $start->diffInMinutes($end);
            $data['unit_type'] = 'heure';
            $data['quantity'] = round($minutes / 60, 2);
        }

        if (empty($data['quantity']) || (float) $data['quantity'] <= 0) {
            $data['quantity'] = $data['unit_type'] === 'jour' ? 1 : 0.01;
        }

        $supplierUnitCost = (float) ($data['supplier_unit_cost'] ?? 0);
        if ($supplierUnitCost <= 0 && !empty($data['vehicle_id'])) {
            $vehicle = Vehicule::find($data['vehicle_id']);
            if ($vehicle && (float) ($vehicle->prix_location ?? 0) > 0) {
                $supplierUnitCost = (float) $vehicle->prix_location;
            }
        }
        $clientUnitPrice = (float) ($data['client_unit_price'] ?? 0);
        $quantity = (float) $data['quantity'];

        $data['supplier_unit_cost'] = $supplierUnitCost;
        $data['client_unit_price'] = $clientUnitPrice;
        $data['total_supplier_cost'] = $quantity * $supplierUnitCost;
        $data['total_client_amount'] = $quantity * $clientUnitPrice;
        $data['created_by'] = Auth::id();

        // Ajouter le projet_id si présent
        if (isset($data['projet_id'])) {
            $data['operation_id'] = $data['projet_id'];
        }

        return $data;
    }

    private function syncMissionTimelineFromPointages(?VehicleMission $mission): void
    {
        if (!$mission) {
            return;
        }

        $mission->loadMissing('pointages');

        if ($mission->pointages->isEmpty()) {
            if (!in_array($mission->status, ['done', 'canceled'], true)) {
                $mission->update([
                    'status' => 'planned',
                ]);
            }

            return;
        }

        $firstPointageDate = $mission->pointages
            ->min(fn (Pointage $pointage) => optional($pointage->date_pointage)?->format('Y-m-d'));

        if (!$firstPointageDate) {
            return;
        }

        $startAt = Carbon::parse($firstPointageDate)->startOfDay();
        $durationDays = max(1, (int) ($mission->duration_days ?? 1));
        $endAt = (clone $startAt)->addDays($durationDays - 1)->endOfDay();

        $updates = [
            'start_at' => $startAt,
            'end_at' => $endAt,
        ];

        if (!in_array($mission->status, ['done', 'canceled'], true)) {
            $updates['status'] = 'ongoing';
        }

        $mission->update($updates);
    }

    private function validateFinancialEntry(Request $request): array
    {
        $categories = implode(',', array_keys(VehicleFinancialEntry::categories()));
        $sources = implode(',', array_keys(VehicleFinancialEntry::sourceModules()));
        $entryTypes = implode(',', array_keys(VehicleFinancialEntry::entryTypes()));
        $missionRule = $this->missionTableExists()
            ? 'nullable|exists:vehicle_missions,id|required_without:operation_id'
            : 'nullable';

        return $request->validate([
            'vehicle_mission_id' => $missionRule,
            'operation_id' => 'nullable|exists:operations,id|required_without:vehicle_mission_id',
            'vehicle_id' => 'nullable|exists:vehicules,id',
            'type' => 'required|in:' . $entryTypes,
            'category' => 'required|in:' . $categories,
            'transaction_date' => 'required|date',
            'label' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0.01',
            'source_module' => 'required|in:' . $sources,
            'depense_caisse_id' => 'nullable|exists:depense_caisses,id',
            'facture_id' => 'nullable|exists:factures,id',
            'external_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
    }

    private function buildFinancialEntryPayload(array $data): array
    {
        $mission = $this->missionTableExists() && !empty($data['vehicle_mission_id'])
            ? VehicleMission::find($data['vehicle_mission_id'])
            : null;

        if ($mission && empty($data['vehicle_id'])) {
            $data['vehicle_id'] = $mission->vehicle_id;
        }

        if ($data['source_module'] === 'tresorerie_decaissement' && !empty($data['depense_caisse_id'])) {
            $decaissement = DepenseCaisse::findOrFail($data['depense_caisse_id']);
            $data['type'] = 'expense';
            $data['amount'] = (float) $decaissement->montant;
            $data['label'] = $data['label'] ?: ($decaissement->libelle ?: ('Décaissement ' . $decaissement->reference));
            $data['external_reference'] = $decaissement->reference;
            $data['transaction_date'] = $decaissement->date_depense;
            $data['operation_id'] = $data['operation_id'] ?? $decaissement->operation_id;
        }

        if ($data['source_module'] === 'facture' && !empty($data['facture_id'])) {
            $facture = Facture::findOrFail($data['facture_id']);
            $data['type'] = 'revenue';
            $data['category'] = 'invoice';
            $data['amount'] = (float) ($facture->montant_ttc ?? $facture->montant_ht ?? 0);
            $data['label'] = $data['label'] ?: ('Facture ' . ($facture->numero ?? $facture->numero_facture ?? ('#' . $facture->id)));
            $data['external_reference'] = $facture->numero ?? $facture->numero_facture ?? ('FAC-' . $facture->id);
            $data['transaction_date'] = $facture->date_facture;
            $data['operation_id'] = $data['operation_id'] ?? $facture->operation_id;
        }

        if ($data['source_module'] === 'tresorerie_avance') {
            $data['type'] = 'expense';
        }

        $data['amount'] = (float) ($data['amount'] ?? 0);
        $data['label'] = $data['label'] ?: 'Écriture cost control';
        $data['created_by'] = Auth::id();

        return $data;
    }

    private function exportRapportCostControlCsv($rows)
    {
        $filename = 'rapport-cost-controle-engin-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Date pointage',
                'Engin',
                'Mission',
                'Client',
                'Periode mission debut',
                'Periode mission fin',
                'Quantite',
                'Cout fournisseur',
                'Montant client',
                'Marge',
            ], ';');

            foreach ($rows as $row) {
                $mission = $row->mission;
                $supplier = (float) ($row->total_supplier_cost ?? 0);
                $client = (float) ($row->total_client_amount ?? 0);

                fputcsv($handle, [
                    optional($row->date_pointage)->format('Y-m-d') ?? '',
                    optional($row->vehicle)->immatriculation ?? optional($row->vehicle)->name ?? 'Engin inconnu',
                    $mission->reference ?? ('Mission #' . ($row->vehicle_mission_id ?? '')),
                    optional($mission->client)->nom ?? optional($mission->client)->name ?? '',
                    optional($mission?->start_at)->format('Y-m-d H:i') ?? '',
                    optional($mission?->end_at)->format('Y-m-d H:i') ?? '',
                    (float) ($row->quantity ?? 0),
                    $supplier,
                    $client,
                    $client - $supplier,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function exportRapportCostControlXlsx($rows)
    {
        $filename = 'rapport-cost-controle-engin-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($rows) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Pointages');

            $headers = [
                'Date pointage',
                'Engin',
                'Mission',
                'Client',
                'Periode mission debut',
                'Periode mission fin',
                'Quantite',
                'Cout fournisseur',
                'Montant client',
                'Marge',
            ];

            // En-têtes avec style (gras, couleur de fond bleu clair, centré)
            foreach ($headers as $index => $header) {
                $cell = $sheet->getCellByColumnAndRow($index + 1, 1);
                $cell->setValue($header);
                $style = $cell->getStyle();
                $style->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0066CC');
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
            }

            $rowNumber = 2;
            foreach ($rows as $row) {
                $mission = $row->mission;
                $supplier = (float) ($row->total_supplier_cost ?? 0);
                $client = (float) ($row->total_client_amount ?? 0);
                $margin = $client - $supplier;

                // Colonne A: Date (format date)
                $cellA = $sheet->getCellByColumnAndRow(1, $rowNumber);
                $cellA->setValue(optional($row->date_pointage)->format('Y-m-d') ?? '');
                if ($row->date_pointage) {
                    $cellA->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Colonnes B-G: Texte
                $sheet->setCellValueByColumnAndRow(2, $rowNumber, optional($row->vehicle)->immatriculation ?? optional($row->vehicle)->name ?? 'Engin inconnu');
                $sheet->setCellValueByColumnAndRow(3, $rowNumber, $mission->reference ?? ('Mission #' . ($row->vehicle_mission_id ?? '')));
                $sheet->setCellValueByColumnAndRow(4, $rowNumber, optional($mission->client)->nom ?? optional($mission->client)->name ?? '');
                $sheet->setCellValueByColumnAndRow(5, $rowNumber, optional($mission?->start_at)->format('Y-m-d H:i') ?? '');
                $sheet->setCellValueByColumnAndRow(6, $rowNumber, optional($mission?->end_at)->format('Y-m-d H:i') ?? '');

                // Colonne G: Quantité (nombre avec 2 décimales)
                $cellG = $sheet->getCellByColumnAndRow(7, $rowNumber);
                $cellG->setValue((float) ($row->quantity ?? 0));
                $cellG->getStyle()->getNumberFormat()->setFormatCode('0.00');
                $cellG->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Colonnes H, I, J: Montants (format FCFA)
                $cellH = $sheet->getCellByColumnAndRow(8, $rowNumber);
                $cellH->setValue($supplier);
                $cellH->getStyle()->getNumberFormat()->setFormatCode('#,##0');
                $cellH->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $cellI = $sheet->getCellByColumnAndRow(9, $rowNumber);
                $cellI->setValue($client);
                $cellI->getStyle()->getNumberFormat()->setFormatCode('#,##0');
                $cellI->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $cellJ = $sheet->getCellByColumnAndRow(10, $rowNumber);
                $cellJ->setValue($margin);
                $cellJ->getStyle()->getNumberFormat()->setFormatCode('#,##0');
                $cellJ->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                // Colorer la marge (vert si > 0, rouge sinon)
                if ($margin < 0) {
                    $cellJ->getStyle()->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFCC0000'));
                } else {
                    $cellJ->getStyle()->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF00B050'));
                }

                $rowNumber++;
            }

            // Largeurs de colonnes
            $sheet->getColumnDimension('A')->setWidth(12);
            $sheet->getColumnDimension('B')->setWidth(14);
            $sheet->getColumnDimension('C')->setWidth(16);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(18);
            $sheet->getColumnDimension('G')->setWidth(10);
            $sheet->getColumnDimension('H')->setWidth(14);
            $sheet->getColumnDimension('I')->setWidth(14);
            $sheet->getColumnDimension('J')->setWidth(14);

            // Gel de la première ligne
            $sheet->freezePane('A2');

            // Hauteur de l'en-tête
            $sheet->getRowDimension(1)->setRowHeight(25);

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function missionTableExists(): bool
    {
        return Schema::hasTable('vehicle_missions');
    }

    private function pointageColumnExists(string $column): bool
    {
        $table = $this->pointageTableName();

        return $table ? Schema::hasColumn($table, $column) : false;
    }

    private function hasPointagesTable(): bool
    {
        return $this->pointageTableName() !== null;
    }

    private function hasVehicleFinancialEntriesTable(): bool
    {
        return Schema::hasTable('vehicle_financial_entries');
    }

    private function pointageTableName(): ?string
    {
        $preferredTable = (new Pointage())->getTable();

        if (Schema::hasTable($preferredTable)) {
            return $preferredTable;
        }

        if ($preferredTable !== 'vehicle_pointages' && Schema::hasTable('vehicle_pointages')) {
            return 'vehicle_pointages';
        }

        if ($preferredTable !== 'pointages' && Schema::hasTable('pointages')) {
            return 'pointages';
        }

        return null;
    }
}
