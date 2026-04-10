<?php

namespace App\Http\Controllers;

use App\Models\VehicleMission;
use App\Models\Personnel;
use App\Services\VehicleMissionCreationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VehicleMissionController extends Controller
{
    private function redirectIfMissionsTableMissing()
    {
        if (!Schema::hasTable('vehicle_missions')) {
            return redirect()->route('materiel.vehicules')
                ->with('warning', "La table des missions n'est pas encore créée. Exécutez les migrations puis réessayez.");
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($redirect = $this->redirectIfMissionsTableMissing()) {
            return $redirect;
        }

        $q = VehicleMission::with(['vehicle', 'user', 'driver', 'client', 'supplier'])
            ->when($request->status, fn($qr) => $qr->where('status', $request->status))
            ->when($request->vehicle_id, fn($qr) => $qr->where('vehicle_id', $request->vehicle_id))
            ->when($request->from, fn($qr) => $qr->whereDate('start_at', '>=', $request->from))
            ->when($request->to, fn($qr) => $qr->whereDate('start_at', '<=', $request->to))
            ->orderByDesc('start_at');

        $missions = $q->paginate(15)->withQueryString();
        $statuses = ['planned','ongoing','done','canceled'];
        $vehicles = \App\Models\Vehicule::orderBy('immatriculation')->get();

        return view('parc.missions', compact('missions', 'statuses', 'vehicles'));
    }

    public function create(Request $request)
    {
        if ($redirect = $this->redirectIfMissionsTableMissing()) {
            return $redirect;
        }

        $vehicles = \App\Models\Vehicule::all();
        $drivers = \App\Models\User::all();
        $chauffeurs = \App\Models\Personnel::where('poste', 'like', '%chauffeur%')
            ->orWhere('poste', 'like', '%conducteur%')
            ->get();
        $suppliers = \App\Models\Fournisseur::all();
        $clients = \App\Models\Client::all();
        $projects = \App\Models\Project::orderByDesc('created_at')->limit(200)->get();
        $statuses = ['planned', 'ongoing', 'done', 'canceled'];
        $defaults = [
            'source_type' => $request->input('source_type'),
            'source_id' => $request->input('source_id'),
            'source_reference' => $request->input('source_reference'),
            'client_id' => $request->input('client_id'),
            'destination' => $request->input('destination'),
            'start_at' => $request->input('start_at', now()->toDateString()),
            'end_at' => $request->input('end_at', now()->addDays(5)->toDateString()),
            'duration_days' => $request->input('duration_days', 6),
            'pointage_submodule' => $request->input('pointage_submodule'),
            'billing_mode' => $request->input('billing_mode'),
        ];

        if ($request->input('source_type') === 'bon_commande' && $request->filled('source_id')) {
            $bonCommande = DB::table('bon_commandes')->find($request->integer('source_id'));

            if ($bonCommande) {
                $defaults['source_reference'] = $bonCommande->reference;
                $defaults['client_id'] = $bonCommande->client_id;
                $defaults['start_at'] = $bonCommande->date_commande ?: $defaults['start_at'];
                $defaults['duration_days'] = max(1, (int) ($bonCommande->duration_days ?? $defaults['duration_days']));
                $defaults['end_at'] = Carbon::parse($defaults['start_at'])
                    ->addDays($defaults['duration_days'] - 1)
                    ->toDateString();
            }
        }

        if ($request->input('source_type') === 'project' && $request->filled('source_id')) {
            $project = \App\Models\Project::find($request->integer('source_id'));

            if ($project) {
                $defaults['source_reference'] = $project->nom;
                $defaults['client_id'] = $project->client_id;
                $defaults['start_at'] = optional($project->date_debut)->format('Y-m-d') ?: $defaults['start_at'];
                $defaults['end_at'] = optional($project->date_fin_prevue)->format('Y-m-d') ?: $defaults['end_at'];

                if (strtolower((string) $project->type) === 'location') {
                    $defaults['pointage_submodule'] = 'camion_plateau';
                    $defaults['billing_mode'] = 'monthly';
                }
            }
        }

        return view('parc.missions-create', compact('vehicles', 'drivers', 'chauffeurs', 'suppliers', 'clients', 'projects', 'statuses', 'defaults'));
    }

    public function store(Request $request, VehicleMissionCreationService $missionCreationService)
    {
        if ($redirect = $this->redirectIfMissionsTableMissing()) {
            return $redirect;
        }

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicules,id',
            'driver_id' => 'nullable|exists:users,id',
            'user_id' => 'nullable|exists:users,id',
            'personnel_id' => 'nullable|exists:personnel,id',
            'supplier_id' => 'nullable|exists:fournisseurs,id',
            'client_id' => 'nullable|exists:clients,id',
            'source_type' => 'nullable|in:prospection,demande_recherche,bon_commande,project,other',
            'source_id' => 'nullable|integer|min:1',
            'source_reference' => 'nullable|string|max:255',
            'pointage_submodule' => 'nullable|in:engin,camion_plateau',
            'billing_mode' => 'nullable|in:standard,monthly,trip',
            'destination' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'objective' => 'nullable|string',
            'duration_days' => 'nullable|integer|min:1',
            'daily_supplier_price' => 'nullable|numeric|min:0',
            'daily_client_price' => 'nullable|numeric|min:0',
            'start_km' => 'nullable|integer|min:0',
            'end_km' => 'nullable|integer|min:0',
            'status' => 'required|in:planned,ongoing,done,canceled',
            'notes' => 'nullable|string',
        ]);

        $missionCreationService->create($validated);

        return redirect()->route('materiel.missions.index')->with('success', 'Projet de location créé avec succès.');
    }

    public function show(VehicleMission $mission)
    {
        $mission->load([
            'vehicle',
            'user',
            'personnel',
            'driver',
            'client',
            'supplier',
            'pointages.driver',
            'financialEntries.decaissement',
            'financialEntries.facture.client',
        ]);
        return view('parc.missions-show', compact('mission'));
    }

    public function edit(VehicleMission $mission)
    {
        $vehicles = \App\Models\Vehicule::all();
        $drivers = \App\Models\User::all();
        $chauffeurs = Personnel::where('poste', 'like', '%chauffeur%')
            ->orWhere('poste', 'like', '%conducteur%')
            ->get();
        $suppliers = \App\Models\Fournisseur::all();
        $clients = \App\Models\Client::all();
        $projects = \App\Models\Project::orderByDesc('created_at')->limit(200)->get();
        $statuses = ['planned', 'ongoing', 'done', 'canceled'];

        return view('parc.missions-edit', compact('mission', 'vehicles', 'drivers', 'chauffeurs', 'suppliers', 'clients', 'projects', 'statuses'));
    }

    public function update(Request $request, VehicleMission $mission, VehicleMissionCreationService $missionCreationService)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicules,id',
            'driver_id' => 'required|exists:users,id',
            'supplier_id' => 'nullable|exists:fournisseurs,id',
            'client_id' => 'nullable|exists:clients,id',
            'source_type' => 'nullable|in:prospection,demande_recherche,bon_commande,project,other',
            'source_id' => 'nullable|integer|min:1',
            'source_reference' => 'nullable|string|max:255',
            'pointage_submodule' => 'nullable|in:engin,camion_plateau',
            'billing_mode' => 'nullable|in:standard,monthly,trip',
            'destination' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'duration_days' => 'required|integer|min:1',
            'daily_supplier_price' => 'required|numeric|min:0',
            'daily_client_price' => 'required|numeric|min:0',
            'start_km' => 'nullable|integer|min:0',
            'end_km' => 'nullable|integer|after_or_equal:start_km',
            'status' => 'required|in:planned,ongoing,done,canceled',
            'notes' => 'nullable|string',
        ]);

        $missionCreationService->update($mission, $validated);

        return redirect()->route('materiel.missions.index')->with('success', 'Projet de location mis à jour avec succès.');
    }

    public function destroy(VehicleMission $mission)
    {
        $mission->delete();
        return redirect()->route('materiel.missions.index')->with('success', 'Projet de location supprimé avec succès.');
    }

    public function export(Request $request)
    {
        if ($redirect = $this->redirectIfMissionsTableMissing()) {
            return $redirect;
        }

        $q = VehicleMission::with(['vehicle','driver'])
            ->when($request->status, fn($qr) => $qr->where('status', $request->status))
            ->when($request->vehicle_id, fn($qr) => $qr->where('vehicle_id', $request->vehicle_id))
            ->when($request->from, fn($qr) => $qr->whereDate('start_at', '>=', $request->from))
            ->when($request->to, fn($qr) => $qr->whereDate('start_at', '<=', $request->to))
            ->orderByDesc('start_at');

        $missions = $q->limit(1000)->get();

        $filename = 'parc_missions_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($missions) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8

            fputcsv($out, ['reference','vehicule','conducteur','destination','debut','fin','statut','marge']);

            foreach ($missions as $m) {
                fputcsv($out, [
                    $m->reference,
                    optional($m->vehicle)->immatriculation,
                    optional($m->driver)->name,
                    $m->destination,
                    optional($m->start_at)->format('Y-m-d H:i'),
                    optional($m->end_at)->format('Y-m-d H:i'),
                    $m->status,
                    $m->gross_margin
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Enregistrer une mission comme complétée et créer la recette (CA)
     */
    public function markCompleted(Request $request, $missionId)
    {
        if ($redirect = $this->redirectIfMissionsTableMissing()) {
            return $redirect;
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $mission = VehicleMission::findOrFail($missionId);

        // Vérification d'accès
        if (!in_array($user->role, ['admin', 'superadmin', 'moderator', 'moderateur', 'trésorier'])) {
            return back()->with('error', "Accès non autorisé.");
        }

        try {
            // Marquer comme facturée
            $mission->update([
                'status' => 'done',
                'is_billed' => true,
                'billed_at' => now(),
            ]);

            // Enregistrer la recette (CA)
            $mission->recordRevenue();

            return back()->with('success', "✅ Mission #{$mission->id} complétée. Recette de {$mission->total_client_amount} FCFA enregistrée dans le CA.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur complétion mission #{$missionId}: " . $e->getMessage());
            return back()->with('error', "Une erreur est survenue : " . $e->getMessage());
        }
    }

    /**
     * Enregistrer manuellement une recette (CA) pour une mission
     */
    public function recordRevenue(Request $request, $missionId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $mission = VehicleMission::findOrFail($missionId);

        // Vérification d'accès (Comptabilité/Trésorerie)
        $canRecord = \App\Services\UserRolePermissionService::canAccessModule($user, 'accounting')
                  || \App\Services\UserRolePermissionService::canAccessModule($user, 'treasury')
                  || in_array($user->role, ['admin', 'superadmin']);

        if (!$canRecord) {
            abort(403, "Seule la comptabilité peut enregistrer les recettes.");
        }

        try {
            // Enregistrer la recette
            if ($mission->recordRevenue()) {
                return back()->with('success', "✅ Recette de {$mission->total_client_amount} FCFA enregistrée au CA pour la mission #{$mission->id}.");
            } else {
                return back()->with('warning', "La recette pour cette mission est déjà enregistrée.");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur enregistrement recette mission #{$missionId}: " . $e->getMessage());
            return back()->with('error', "Une erreur est survenue : " . $e->getMessage());
        }
    }

    /**
     * Enregistrer une dépense (supplier cost) pour une mission
     */
    public function recordExpense(Request $request, $missionId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $mission = VehicleMission::findOrFail($missionId);

        // Vérification d'accès
        if (!in_array($user->role, ['admin', 'superadmin', 'moderator', 'moderateur'])) {
            abort(403, "Accès non autorisé.");
        }

        try {
            // Créer une dépense de caisse pour le coût du fournisseur
            if ($mission->total_supplier_cost > 0) {
                \App\Models\DepenseCaisse::create([
                    'reference' => 'MISSION-' . $mission->id,
                    'caisse_id' => 1, // Caisse principale par défaut
                    'libelle' => 'Dépense Mission Véhicule #' . $mission->id . ' : ' . $mission->reference,
                    'description' => 'Coût fournisseur pour la mission. Destination: ' . $mission->destination,
                    'montant' => $mission->total_supplier_cost,
                    'date_depense' => $mission->end_at ?? now(),
                    'mode_paiement' => 'virement',
                    'statut' => 'validé',
                    'created_by' => $user->id,
                    'valideur_id' => $user->id,
                    'date_validation' => now(),
                ]);

                \Illuminate\Support\Facades\Log::info("Dépense enregistrée pour mission #{$mission->id} : {$mission->total_supplier_cost} FCFA");
                return back()->with('success', "✅ Dépense de {$mission->total_supplier_cost} FCFA enregistrée.");
            }

            return back()->with('warning', "Aucune dépense à enregistrer pour cette mission.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur enregistrement dépense mission #{$missionId}: " . $e->getMessage());
            return back()->with('error', "Une erreur est survenue : " . $e->getMessage());
        }
    }
}
