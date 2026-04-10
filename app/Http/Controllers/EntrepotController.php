<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use Illuminate\Http\Request;

class EntrepotController extends Controller
{
    public function index()
    {
        try {
            $entrepots = Entrepot::orderBy('nom')->paginate(50);
        } catch (\Exception $e) {
            $entrepots = collect([]);
        }
        return view('entrepots.list', compact('entrepots'));
    }

    public function create()
    {
        return view('entrepots.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'code' => 'required|string|max:50|unique:entrepots,code',
                'nom' => 'required|string|max:200',
                'adresse' => 'nullable|string|max:300',
                'responsable' => 'nullable|string|max:200',
                'telephone' => 'nullable|string|max:20',
                'capacite' => 'nullable|numeric|min:0',
            ]);

            $data['actif'] = true;

            Entrepot::create($data);

            return redirect()->route('stock.entrepots')->with('success', 'Entrepôt créé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création de l\'entrepôt.');
        }
    }

    public function show(Entrepot $entrepot)
    {
        return view('entrepots.show', compact('entrepot'));
    }

    public function edit(Entrepot $entrepot)
    {
        return view('entrepots.edit', compact('entrepot'));
    }

    public function update(Request $request, Entrepot $entrepot)
    {
        try {
            $data = $request->validate([
                'code' => 'required|string|max:50|unique:entrepots,code,' . $entrepot->id,
                'nom' => 'required|string|max:200',
                'adresse' => 'nullable|string|max:300',
                'responsable' => 'nullable|string|max:200',
                'telephone' => 'nullable|string|max:20',
                'capacite' => 'nullable|numeric|min:0',
            ]);

            $entrepot->update($data);

            return redirect()->route('stock.entrepots')->with('success', 'Entrepôt mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour de l\'entrepôt.');
        }
    }

    public function destroy(Entrepot $entrepot)
    {
        try {
            $entrepot->delete();
            return redirect()->route('stock.entrepots')->with('success', 'Entrepôt supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression de l\'entrepôt.');
        }
    }

    public function dashboard()
    {
        try {
            $stats = [
                'total' => Entrepot::count(),
                'actifs' => Entrepot::where('actif', true)->count(),
                'inactifs' => Entrepot::where('actif', false)->count(),
                'en_alerte' => Entrepot::enAlerte()->count(),
                'capacite_totale' => Entrepot::sum('capacite'),
                'capacite_utilisee' => Entrepot::sum('capacite_utilisee'),
                'capacite_disponible' => Entrepot::sum('capacite') - Entrepot::sum('capacite_utilisee'),
            ];

            $entrepots = Entrepot::orderBy('nom')->get();

            // Calculer le taux d'occupation moyen
            $stats['taux_occupation_moyen'] = $stats['capacite_totale'] > 0
                ? ($stats['capacite_utilisee'] / $stats['capacite_totale']) * 100
                : 0;

            // Entrepôts les plus utilisés
            $stats['entrepots_plus_utilises'] = Entrepot::orderBy('capacite_utilisee', 'desc')
                ->limit(5)
                ->get();

            // Entrepôts avec alertes
            $stats['entrepots_alertes'] = Entrepot::enAlerte()
                ->orderBy('capacite_utilisee', 'desc')
                ->limit(5)
                ->get();

        } catch (\Exception $e) {
            $stats = [
                'total' => 0,
                'actifs' => 0,
                'inactifs' => 0,
                'en_alerte' => 0,
                'capacite_totale' => 0,
                'capacite_utilisee' => 0,
                'capacite_disponible' => 0,
                'taux_occupation_moyen' => 0,
                'entrepots_plus_utilises' => collect([]),
                'entrepots_alertes' => collect([]),
            ];
            $entrepots = collect([]);
        }

        return view('entrepots.dashboard', compact('stats', 'entrepots'));
    }

    public function stock()
    {
        try {
            // Récupérer tous les produits avec leur stock actuel
            $produits = \App\Models\Produit::orderBy('designation')
                ->paginate(20);

            // Récupérer les entrepôts avec leurs statistiques
            $entrepots = \App\Models\Entrepot::withCount('produits')
                ->get()
                ->map(function ($entrepot) {
                    // Calculer la valeur du stock pour cet entrepôt
                    $valeur_stock = \App\Models\Produit::where('entrepot_id', $entrepot->id)
                        ->selectRaw('SUM(stock_actuel * prix_unitaire) as total')
                        ->value('total') ?? 0;

                    // Convertir en millions pour l'affichage
                    $entrepot->valeur_stock = $valeur_stock / 1000000;

                    return $entrepot;
                });

            // Statistiques sur les stocks
            $stats = [
                'total_produits' => \App\Models\Produit::count(),
                'produits_en_stock' => \App\Models\Produit::where('stock_actuel', '>', 0)->count(),
                'valeur_totale' => \App\Models\Produit::selectRaw('SUM(stock_actuel * prix_unitaire) as total')->value('total') ?? 0,
                'alertes_stock' => \App\Models\Produit::whereRaw('stock_actuel <= stock_min')->count(),
            ];
        } catch (\Exception $e) {
            $produits = collect([]);
            $entrepots = collect([]);
            $stats = [
                'total_produits' => 0,
                'produits_en_stock' => 0,
                'valeur_totale' => 0,
                'alertes_stock' => 0,
            ];
        }

        return view('entrepots.stock', compact('produits', 'stats', 'entrepots'));
    }

    public function rapports()
    {
        try {
            // Statistiques générales entrepôts
            $stats = [
                'total_entrepots' => Entrepot::count(),
                'entrepots_actifs' => Entrepot::where('actif', true)->count(),
                'total_produits' => \App\Models\Produit::count(),
                'produits_actifs' => \App\Models\Produit::where('actif', true)->count(),
                'total_transferts' => \App\Models\StockTransfert::count(),
                'transferts_mois' => \App\Models\StockTransfert::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
            ];

            $entrepots = Entrepot::orderBy('nom')->get();

            // Produits critiques (stock <= stock_min)
            $produits_critiques = \App\Models\Produit::whereRaw('stock_actuel <= stock_min')->get();

            // Valeur totale du stock
            $valeur_totale_stock = \App\Models\Produit::selectRaw('SUM(stock_actuel * prix_unitaire) as total')->value('total') ?? 0;

            // Entrepôts les plus utilisés
            $entrepots_top = Entrepot::withCount('produits')->orderBy('produits_count', 'desc')->limit(5)->get();

            // Transferts récents (mouvements)
            $transferts_recents = \App\Models\StockTransfert::orderBy('created_at', 'desc')->limit(20)->get();

            // Produits par catégorie
            $produits_par_categorie = \App\Models\Produit::selectRaw("COALESCE(categorie, 'Non catégorisé') as categorie, COUNT(*) as nb, SUM(stock_actuel) as stock_total, SUM(stock_actuel * prix_unitaire) as valeur")
                ->groupBy(\Illuminate\Support\Facades\DB::raw("COALESCE(categorie, 'Non catégorisé')"))
                ->orderByDesc('valeur')
                ->get();

            $rapports = compact(
                'produits_critiques', 'valeur_totale_stock', 'entrepots_top',
                'transferts_recents', 'produits_par_categorie'
            );
        } catch (\Exception $e) {
            $stats = [
                'total_entrepots' => 0, 'entrepots_actifs' => 0,
                'total_produits' => 0, 'produits_actifs' => 0,
                'total_transferts' => 0, 'transferts_mois' => 0,
            ];
            $entrepots = collect([]);
            $rapports = [
                'produits_critiques' => collect([]),
                'valeur_totale_stock' => 0,
                'entrepots_top' => collect([]),
                'transferts_recents' => collect([]),
                'produits_par_categorie' => collect([]),
            ];
        }

        return view('entrepots.rapports', compact('stats', 'entrepots', 'rapports'));
    }

    /**
     * Affiche la liste simplifiée des entrepôts
     */
    public function listSimple()
    {
        try {
            $entrepots = Entrepot::orderBy('nom')->get();
        } catch (\Exception $e) {
            $entrepots = collect([]);
        }

        return view('entrepots.list', compact('entrepots'));
    }
}
