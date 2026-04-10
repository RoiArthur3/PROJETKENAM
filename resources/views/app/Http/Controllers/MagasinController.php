<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MagasinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('permission:magasin.access'); // Désactivé temporairement
    }

    /**
     * Page d'accueil du module Magasin
     */
    public function index()
    {
        return redirect()->route('magasin.dashboard');
    }

    /**
     * Dashboard du module Magasin
     */
    public function dashboard()
    {
        try {
            $stats = [
                'total_produits' => DB::table('produits')->count(),
                'produits_actifs' => DB::table('produits')->where('actif', true)->count(),
                'total_entrees' => DB::table('stock_entrees')->whereDate('created_at', today())->count(),
                'total_sorties' => DB::table('stock_sorties')->whereDate('created_at', today())->count(),
                'valeur_stock' => DB::table('produits')->sum(DB::raw('prix_unitaire * stock_actuel')),
                'alertes_stock' => DB::table('produits')->whereRaw('stock_actuel <= stock_min')->count(),
                'entrees_mois' => DB::table('stock_entrees')
                    ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'sorties_mois' => DB::table('stock_sorties')
                    ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'total_categories' => DB::table('produits')->distinct()->count('categorie'),
            ];

            $recent_entrees = DB::table('stock_entrees')
                ->leftJoin('produits', 'stock_entrees.produit_id', '=', 'produits.id')
                ->select('stock_entrees.*', 'produits.designation as produit_nom')
                ->orderBy('stock_entrees.created_at', 'desc')
                ->limit(10)
                ->get();

            $recent_sorties = DB::table('stock_sorties')
                ->leftJoin('produits', 'stock_sorties.produit_id', '=', 'produits.id')
                ->select('stock_sorties.*', 'produits.designation as produit_nom')
                ->orderBy('stock_sorties.created_at', 'desc')
                ->limit(10)
                ->get();

            $produits_critiques = DB::table('produits')
                ->whereRaw('stock_actuel <= stock_min')
                ->orderBy('stock_actuel')
                ->limit(10)
                ->get();

            $produits_par_categorie = DB::table('produits')
                ->selectRaw("COALESCE(categorie, 'Non catégorisé') as categorie, COUNT(*) as nb, SUM(stock_actuel) as stock_total, SUM(stock_actuel * prix_unitaire) as valeur")
                ->groupBy(DB::raw("COALESCE(categorie, 'Non catégorisé')"))
                ->orderByDesc('valeur')
                ->get();

        } catch (\Exception $e) {
            $stats = [
                'total_produits' => 0, 'produits_actifs' => 0,
                'total_entrees' => 0, 'total_sorties' => 0,
                'valeur_stock' => 0, 'alertes_stock' => 0,
                'entrees_mois' => 0, 'sorties_mois' => 0,
                'total_categories' => 0,
            ];
            $recent_entrees = collect([]);
            $recent_sorties = collect([]);
            $produits_critiques = collect([]);
            $produits_par_categorie = collect([]);
        }

        return view('admin.magasin.dashboard', compact(
            'stats', 'recent_entrees', 'recent_sorties',
            'produits_critiques', 'produits_par_categorie'
        ));
    }

    /**
     * Page d'inventaire
     */
    /**
     * Page d'inventaire
     */
    public function inventaire(Request $request)
    {
        $query = DB::table('produits');

        // Filtre Recherche
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('designation', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filtre Catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->get('categorie'));
        }

        // Filtre Statut
        if ($request->filled('statut')) {
            $statut = $request->get('statut');
            if ($statut === 'rupture') {
                $query->where('stock_actuel', '<=', DB::raw('stock_min'));
            } elseif ($statut === 'alerte') {
                $query->where('stock_actuel', '>', DB::raw('stock_min'))
                      ->where('stock_actuel', '<=', DB::raw('stock_min * 2'));
            } elseif ($statut === 'disponible') {
                $query->where('stock_actuel', '>', DB::raw('stock_min * 2'));
            }
        }

        // Clone pour les stats globales (sans filtres pour avoir une vue d'ensemble, ou avec ? Généralement avec filtres c'est mieux pour le contexte, mais pour les KPI globaux on peut vouloir tout voir. Faisons simple : stats globales)
        $statsQuery = DB::table('produits');

        $stats = [
            'total' => $statsQuery->count(),
            'valeur_stock' => $statsQuery->sum(DB::raw('prix_unitaire * stock_actuel')),
            'rupture' => (clone $statsQuery)->where('stock_actuel', '<=', DB::raw('stock_min'))->count(),
            'alerte' => (clone $statsQuery)->where('stock_actuel', '>', DB::raw('stock_min'))
                                           ->where('stock_actuel', '<=', DB::raw('stock_min * 2'))->count(),
        ];

        // Récupérer les catégories pour le filtre
        $categories = DB::table('produits')->distinct()->pluck('categorie');

        $produits = $query->orderBy('designation')->paginate(20)->withQueryString();

        return view('admin.magasin.inventaire', compact('produits', 'stats', 'categories'));
    }

    /**
     * Page des entrées
     */
    public function entrees()
    {
        $entrees = DB::table('stock_entrees')
            ->join('produits', 'stock_entrees.produit_id', '=', 'produits.id')
            ->join('users', 'stock_entrees.user_id', '=', 'users.id')
            ->select('stock_entrees.*', 'produits.designation as produit_nom', 'users.name as user_name')
            ->orderBy('stock_entrees.created_at', 'desc')
            ->paginate(20);

        return view('admin.magasin.entrees', compact('entrees'));
    }

    /**
     * Page des sorties
     */
    public function sorties()
    {
        $sorties = DB::table('stock_sorties')
            ->join('produits', 'stock_sorties.produit_id', '=', 'produits.id')
            ->join('users', 'stock_sorties.user_id', '=', 'users.id')
            ->select('stock_sorties.*', 'produits.designation as produit_nom', 'users.name as user_name')
            ->orderBy('stock_sorties.created_at', 'desc')
            ->paginate(20);

        return view('admin.magasin.sorties', compact('sorties'));
    }

    /**
     * Page des rapports
     */
    public function rapports()
    {
        $stats_mois = DB::table('stock_entrees')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantite');

        $stats_sorties_mois = DB::table('stock_sorties')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantite');

        $produits_critiques = DB::table('produits')
            ->where('stock_actuel', '<=', DB::raw('stock_min * 1.2'))
            ->orderBy('stock_actuel', 'asc')
            ->limit(10)
            ->get();

        return view('admin.magasin.rapports', compact('stats_mois', 'stats_sorties_mois', 'produits_critiques'));
    }

    /**
     * Créer un produit
     */
    public function createProduit()
    {
        return view('admin.magasin.produits.create');
    }

    /**
     * Stocker un nouveau produit
     */
    public function storeProduit(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:produits',
            'description' => 'nullable|string',
            'prix_unitaire' => 'required|numeric|min:0',
            'stock_actuel' => 'required|integer|min:0',
            'stock_min' => 'required|integer|min:0',
            'unite' => 'required|string|max:50',
            'categorie' => 'required|string|max:100'
        ]);

        $validated['created_by'] = Auth::id();

        DB::table('produits')->insert($validated);

        return redirect()->route('magasin.dashboard')
            ->with('success', 'Produit créé avec succès');
    }

    /**
     * Afficher un produit
     */
    public function showProduit($id)
    {
        $produit = DB::table('produits')->find($id);

        if (!$produit) {
            return redirect()->route('magasin.dashboard')
                ->with('error', 'Produit non trouvé');
        }

        $mouvements = DB::table('stock_movements')
            ->where('produit_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.magasin.produits.show', compact('produit', 'mouvements'));
    }

    /**
     * Modifier un produit
     */
    public function editProduit($id)
    {
        $produit = DB::table('produits')->find($id);

        if (!$produit) {
            return redirect()->route('magasin.dashboard')
                ->with('error', 'Produit non trouvé');
        }

        return view('admin.magasin.produits.edit', compact('produit'));
    }

    /**
     * Mettre à jour un produit
     */
    public function updateProduit(Request $request, $id)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:produits,code,'.$id,
            'description' => 'nullable|string',
            'prix_unitaire' => 'required|numeric|min:0',
            'stock_actuel' => 'required|integer|min:0',
            'stock_min' => 'required|integer|min:0',
            'unite' => 'required|string|max:50',
            'categorie' => 'required|string|max:100'
        ]);

        $validated['updated_by'] = Auth::id();

        DB::table('produits')->where('id', $id)->update($validated);

        return redirect()->route('magasin.dashboard')
            ->with('success', 'Produit mis à jour avec succès');
    }

    /**
     * Supprimer un produit
     */
    public function destroyProduit($id)
    {
        DB::table('produits')->where('id', $id)->delete();

        return redirect()->route('magasin.dashboard')
            ->with('success', 'Produit supprimé avec succès');
    }

    /**
     * Créer une entrée
     */
    public function createEntree()
    {
        $produits = DB::table('produits')
            ->orderBy('designation')
            ->get();

        $fournisseurs = DB::table('fournisseurs')
            ->orderBy('raison_sociale')
            ->get();

        return view('admin.magasin.entrees.create', compact('produits', 'fournisseurs'));
    }

    /**
     * Stocker une entrée
     */
    public function storeEntree(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'fournisseur' => 'nullable|string|max:255',
            'reference_entree' => 'nullable|string|max:100',
            'motif' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Créer l'entrée
            DB::table('stock_entrees')->insert([
                'produit_id' => $validated['produit_id'],
                'quantite' => $validated['quantite'],
                'prix_unitaire' => $validated['prix_unitaire'] ?? 0,
                'fournisseur' => $validated['fournisseur'] ?? null,
                'reference_entree' => $validated['reference_entree'] ?? null,
                'motif' => $validated['motif'] ?? null,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mettre à jour le stock du produit
            DB::table('produits')
                ->where('id', $validated['produit_id'])
                ->increment('stock_actuel', $validated['quantite']);

            // Mettre à jour le prix unitaire si fourni
            if (!empty($validated['prix_unitaire'])) {
                DB::table('produits')
                    ->where('id', $validated['produit_id'])
                    ->update(['prix_unitaire' => $validated['prix_unitaire']]);
            }

            DB::commit();

            return redirect()->route('magasin.entrees')
                ->with('success', 'Entrée de stock enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    /**
     * Créer une sortie
     */
    public function createSortie()
    {
        $produits = DB::table('produits')
            ->where('stock_actuel', '>', 0)
            ->orderBy('designation')
            ->get();

        return view('admin.magasin.sorties.create', compact('produits'));
    }

    /**
     * Stocker une sortie
     */
    public function storeSortie(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'motif' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'reference_sortie' => 'nullable|string|max:100'
        ]);

        // Vérifier le stock disponible
        $produit = DB::table('produits')->find($validated['produit_id']);
        if ($produit->stock_actuel < $validated['quantite']) {
            return back()->withInput()->with('error', 'Stock insuffisant');
        }

        $validated['user_id'] = Auth::id();
        $validated['reference_sortie'] = $validated['reference_sortie'] ?? 'SORT-' . date('YmdHis');

        DB::beginTransaction();
        try {
            // Insérer la sortie
            DB::table('stock_sorties')->insert($validated);

            // Mettre à jour le stock
            DB::table('produits')
                ->where('id', $validated['produit_id'])
                ->decrement('stock_actuel', $validated['quantite']);

            // Enregistrer le mouvement
            DB::table('stock_movements')->insert([
                'produit_id' => $validated['produit_id'],
                'type' => 'sortie',
                'quantite' => $validated['quantite'],
                'reference' => $validated['reference_sortie'],
                'motif' => $validated['motif'],
                'user_id' => Auth::id(),
                'created_at' => now()
            ]);

            DB::commit();
            return redirect()->route('magasin.dashboard')
                ->with('success', 'Sortie enregistrée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Erreur lors de l\'enregistrement de la sortie');
        }
    }

    /**
     * Afficher une sortie
     */
    public function showSortie($id)
    {
        $sortie = DB::table('stock_sorties')
            ->join('produits', 'stock_sorties.produit_id', '=', 'produits.id')
            ->join('users', 'stock_sorties.user_id', '=', 'users.id')
            ->select('stock_sorties.*', 'produits.designation as produit_nom', 'users.name as user_name')
            ->where('stock_sorties.id', $id)
            ->first();

        if (!$sortie) {
            return redirect()->route('magasin.sorties.index')
                ->with('error', 'Sortie non trouvée');
        }

        return view('admin.magasin.sorties.show', compact('sortie'));
    }

    /**
     * Modifier une sortie
     */
    public function editSortie($id)
    {
        $sortie = DB::table('stock_sorties')->find($id);

        if (!$sortie) {
            return redirect()->route('magasin.sorties.index')
                ->with('error', 'Sortie non trouvée');
        }

        $produits = DB::table('produits')->orderBy('designation')->get();

        return view('admin.magasin.sorties.edit', compact('sortie', 'produits'));
    }

    /**
     * Mettre à jour une sortie
     */
    public function updateSortie(Request $request, $id)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'motif' => 'required|string|max:255',
            'destination' => 'required|string|max:255'
        ]);

        DB::table('stock_sorties')->where('id', $id)->update($validated);

        return redirect()->route('magasin.sorties.index')
            ->with('success', 'Sortie mise à jour avec succès');
    }

    /**
     * Supprimer une sortie
     */
    public function destroySortie($id)
    {
        DB::table('stock_sorties')->where('id', $id)->delete();

        return redirect()->route('magasin.sorties.index')
            ->with('success', 'Sortie supprimée avec succès');
    }
}
