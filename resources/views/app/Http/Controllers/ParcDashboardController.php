<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParcDashboardController extends Controller
{
    public function index(Request $request)
    {
        $total = Vehicle::count();
        $disponibles = Vehicle::where('disponibilite', true)->count();
        $nonDisponibles = Vehicle::where('disponibilite', false)->count();
        $maintenance = Vehicle::where('etat', 'maintenance')->count();
        $immobilises = Vehicle::where('etat', 'immobilise')->count();

        $tauxUtilisation = $total > 0 ? round(($disponibles / $total) * 100, 2) : 0;

        // Alerts: assurances & visites techniques expirant sous 30 jours ou expirées
        $today = Carbon::today();
        $in30 = Carbon::today()->addDays(30);
        $assurancesAlert = Vehicle::whereBetween('assurance_expiry', [$today, $in30])
            ->orWhere(function($q) use($today){ $q->whereNotNull('assurance_expiry')->where('assurance_expiry','<',$today); })
            ->count();
        $vtAlert = Vehicle::whereBetween('visite_tech_expiry', [$today, $in30])
            ->orWhere(function($q) use($today){ $q->whereNotNull('visite_tech_expiry')->where('visite_tech_expiry','<',$today); })
            ->count();

        // 12 derniers mois
        $now = now();
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = $now->copy()->subMonths($i)->format('Y-m');
        }

        $driver = DB::connection()->getDriverName();
        $ymExpr = match ($driver) {
            'mysql', 'mariadb' => "DATE_FORMAT(date_depense, '%Y-%m')",
            'pgsql' => "to_char(date_depense, 'YYYY-MM')",
            default => "strftime('%Y-%m', date_depense)",
        };

        // Carburant par mois (Expense)
        $fuelByMonth = Expense::selectRaw("{$ymExpr} as ym, SUM(montant) as s")
            ->whereNotNull('date_depense')
            ->where('service_concerne', 'Parc')
            ->where(function ($q) {
                $q->where('categorie', 'Carburant')
                  ->orWhere('categorie', 'LIKE', '%carbur%');
            })
            ->where('date_depense', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy('ym')->pluck('s', 'ym');
        $fuelPerMonth = array_map(fn($m) => (float)($fuelByMonth[$m] ?? 0), $months);

        // Coûts maintenance par mois
        $maintByMonth = Expense::selectRaw("{$ymExpr} as ym, SUM(montant) as s")
            ->whereNotNull('date_depense')
            ->where('service_concerne', 'Parc')
            ->where(function ($q) {
                $q->whereIn('categorie', ['Maintenance','Réparation','Reparation','Entretien'])
                  ->orWhere('categorie', 'LIKE', '%maint%')
                  ->orWhere('categorie', 'LIKE', '%repar%')
                  ->orWhere('categorie', 'LIKE', '%entret%');
            })
            ->where('date_depense', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy('ym')->pluck('s', 'ym');
        $maintPerMonth = array_map(fn($m) => (float)($maintByMonth[$m] ?? 0), $months);

        // Répartition par état
        $pieLabels = ['Disponible','Maintenance','Immobilisé','Autre'];
        $pieData = [
            $disponibles,
            $maintenance,
            $immobilises,
            max(0, $total - $disponibles - $maintenance - $immobilises),
        ];

        return view('parc.dashboard', [
            'kpis' => [
                'total' => $total,
                'disponibles' => $disponibles,
                'maintenance' => $maintenance,
                'immobilises' => $immobilises,
                'tauxUtilisation' => $tauxUtilisation,
            ],
            'alerts' => [
                'assurances' => $assurancesAlert,
                'visiteTech' => $vtAlert,
            ],
            'months' => $months,
            'fuelPerMonth' => $fuelPerMonth,
            'maintPerMonth' => $maintPerMonth,
            'pieLabels' => $pieLabels,
            'pieData' => $pieData,
        ]);
    }

    public function carburant(Request $request)
    {
        $vehicules = Vehicle::orderBy('immatriculation')->get(['id','immatriculation','modele']);

        $query = Expense::with('vehicle:id,immatriculation,modele')
            ->where('service_concerne', 'Parc')
            ->where(function ($q) {
                $q->where('categorie', 'Carburant')
                  ->orWhere('categorie', 'LIKE', '%carbur%');
            })
            ->when($request->filled('from'), fn($q) => $q->whereDate('date_depense', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('date_depense', '<=', $request->to));

        // Filtre par type carburant
        if ($request->filled('type')) {
            $type = $request->type;
            $query->where(function ($q) use ($type) {
                $q->where('type', $type)
                  ->orWhere('categorie', 'LIKE', '%'.$type.'%');
            });
        }

        // Filtre par véhicule (id ou immatriculation dans un champ texte)
        if ($request->filled('vehicule')) {
            $veh = trim($request->vehicule);
            $query->where(function ($q) use ($veh) {
                $q->where('vehicule_id', $veh);
                // Recherche par immatriculation si c'est du texte
                if (!is_numeric($veh)) {
                    $q->orWhereHas('vehicle', function($vq) use ($veh) {
                        $vq->where('immatriculation', 'like', '%'.$veh.'%');
                    });
                }
            });
        }

        $appoints = $query->orderByDesc('date_depense')->paginate(25)->withQueryString();

        return view('parc.carburant', compact('appoints','vehicules'));
    }

    /**
     * Enregistrer un appoint carburant (création dans Expense).
     */
    public function storeCarburant(Request $request)
    {
        $data = $request->validate([
            'vehicule_id' => 'required|integer',
            'type' => 'required|string|max:50',
            'date' => 'required|date',
            'litres' => 'required|numeric|min:0.01',
            'prix_unitaire' => 'required|numeric|min:0',
            'station' => 'required|string|max:255',
        ]);

        $montant = $data['litres'] * $data['prix_unitaire'];

        try {
            Expense::create([
                'service_concerne' => 'Parc',
                'categorie' => 'Carburant',
                'type' => $data['type'],
                'date_depense' => $data['date'],
                'vehicule_id' => $data['vehicule_id'],
                'quantite' => $data['litres'],
                'prix_unitaire' => $data['prix_unitaire'],
                'montant' => $montant,
                'station' => $data['station'],
                'fournisseur' => $data['station'],
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('status', 'Erreur lors de l\'enregistrement: '.$e->getMessage());
        }

        return redirect()->route('parc.carburant')->with('status', 'Appoint carburant enregistré avec succès.');
    }

    /**
     * Export CSV des appoints carburant.
     */
    public function exportCarburant(Request $request)
    {
        $filename = 'parc_carburant_'.now()->format('Ymd_His').'.csv';

        $query = Expense::query()
            ->where('service_concerne', 'Parc')
            ->where(function ($q) {
                $q->where('categorie', 'Carburant')
                  ->orWhere('categorie', 'LIKE', '%carbur%');
            });

        if ($request->filled('from')) {
            $query->whereDate('date_depense', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date_depense', '<=', $request->to);
        }

        $rows = $query->orderByDesc('date_depense')->limit(500)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['date','vehicule_id','type','litres','prix_unitaire','montant','station']);
            foreach ($rows as $e) {
                fputcsv($out, [
                    optional($e->date_depense)->format('Y-m-d H:i'),
                    $e->vehicule_id,
                    $e->type ?? 'Gasoil',
                    $e->quantite ?? 0,
                    $e->prix_unitaire ?? 0,
                    $e->montant ?? 0,
                    $e->fournisseur ?? ($e->station ?? '-')
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
