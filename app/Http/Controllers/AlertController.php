<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    /**
     * Afficher la liste des alertes
     */
    public function index(Request $request)
    {
        $query = DB::table('active_vehicle_alerts');

        // Filtres
        if ($request->filled('type_alerte')) {
            $query->where('va.type_alerte', $request->type_alerte);
        }

        if ($request->filled('niveau_alerte')) {
            $query->where('va.niveau_alerte', $request->niveau_alerte);
        }

        if ($request->filled('date_debut')) {
            $query->where('va.date_alerte', '>=', $request->date_debut);
        }

        $alertes = $query->orderBy('va.date_alerte', 'desc')->orderBy('va.niveau_alerte', 'desc')->paginate(15);

        return view('alerts.index', compact('alertes'));
    }

    /**
     * Créer une nouvelle alerte
     */
    public function create()
    {
        $vehicules = DB::table('vehicules')->where('disponible', true)->get();

        return view('alerts.create', compact('vehicules'));
    }

    /**
     * Enregistrer une nouvelle alerte
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicules,id',
            'type_alerte' => 'required|in:assurance,maintenance,kilometrage',
            'message' => 'required|string|max:500',
            'date_alerte' => 'required|date',
            'niveau_alerte' => 'required|in:info,warning,critical'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('vehicle_alerts')->insert($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alerte créée avec succès');
    }

    /**
     * Afficher les détails d'une alerte
     */
    public function show($id)
    {
        $alerte = DB::table('active_vehicle_alerts')
            ->where('va.id', $id)
            ->first();

        if (!$alerte) {
            abort(404);
        }

        return view('alerts.show', compact('alerte'));
    }

    /**
     * Mettre à jour une alerte
     */
    public function update(Request $request, $id)
    {
        $alerte = DB::table('vehicle_alerts')->where('id', $id)->first();

        if (!$alerte) {
            abort(404);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'niveau_alerte' => 'required|in:info,warning,critical',
            'date_alerte' => 'required|date',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['updated_at'] = now();

        DB::table('vehicle_alerts')->where('id', $id)->update($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alerte mise à jour avec succès');
    }

    /**
     * Marquer une alerte comme traitée
     */
    public function markAsProcessed(Request $request, $id)
    {
        $alerte = DB::table('vehicle_alerts')->where('id', $id)->first();

        if (!$alerte) {
            abort(404);
        }

        DB::table('vehicle_alerts')->where('id', $id)->update([
            'traitee' => 1,
            'updated_by' => Auth::id(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alerte marquée comme traitée'
        ]);
    }

    /**
     * Supprimer une alerte
     */
    public function destroy($id)
    {
        $alerte = DB::table('vehicle_alerts')->where('id', $id)->first();

        if (!$alerte) {
            abort(404);
        }

        DB::table('vehicle_alerts')->where('id', $id)->delete();

        return redirect()->route('alerts.index')
            ->with('success', 'Alerte supprimée avec succès');
    }

    /**
     * API: Statistiques des alertes
     */
    public function apiStats()
    {
        $stats = [
            'total' => DB::table('vehicle_alerts')->count(),
            'actives' => DB::table('vehicle_alerts')->where('traitee', 0)->count(),
            'traitees' => DB::table('vehicle_alerts')->where('traitee', 1)->count(),
            'par_type' => [
                'assurance' => DB::table('vehicle_alerts')->where('type_alerte', 'assurance')->count(),
                'maintenance' => DB::table('vehicle_alerts')->where('type_alerte', 'maintenance')->count(),
                'kilometrage' => DB::table('vehicle_alerts')->where('type_alerte', 'kilometrage')->count(),
            ],
            'par_niveau' => [
                'info' => DB::table('vehicle_alerts')->where('niveau_alerte', 'info')->count(),
                'warning' => DB::table('vehicle_alerts')->where('niveau_alerte', 'warning')->count(),
                'critical' => DB::table('vehicle_alerts')->where('niveau_alerte', 'critical')->count(),
            ],
            'recentes' => DB::table('vehicle_alerts')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * API: Alertes actives
     */
    public function activeAlerts()
    {
        $alertes = DB::table('active_vehicle_alerts')
            ->where('traitee', 0)
            ->orderBy('date_alerte', 'desc')
            ->orderBy('niveau_alerte', 'desc')
            ->limit(10)
            ->get();

        return response()->json($alertes);
    }

    /**
     * API: Index des alertes
     */
    public function apiIndex()
    {
        $alertes = DB::table('vehicle_alerts')
            ->select('vehicle_alerts.*', 'vehicules.immatriculation', 'vehicules.marque', 'vehicules.modele')
            ->leftJoin('vehicules', 'vehicle_alerts.vehicle_id', '=', 'vehicules.id')
            ->orderBy('vehicle_alerts.created_at', 'desc')
            ->paginate(20);

        return response()->json($alertes);
    }
}
