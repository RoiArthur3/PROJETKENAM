<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    /**
     * Afficher l'historique des commandes
     */
    public function index(Request $request)
    {
        $query = DB::table('commandes_recentes as cr');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('cr.statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->where('cr.date_commande', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('cr.date_commande', '<=', $request->date_fin);
        }

        $commandes = $query->orderBy('cr.date_commande', 'desc')->paginate(15);

        return view('commande.index', compact('commandes'));
    }

    /**
     * Afficher les détails d'une commande
     */
    public function show($id)
    {
        $commande = DB::table('commandes_recentes as cr')
            ->leftJoin('users', 'cr.user_id', '=', 'users.id')
            ->select('cr.*', 'users.name as created_by_name')
            ->where('cr.id', $id)
            ->first();

        if (!$commande) {
            abort(404);
        }

        // Récupérer l'historique des modifications
        $historique = DB::table('historique_commandes as hc')
            ->leftJoin('users', 'hc.user_id', '=', 'users.id')
            ->select('hc.*', 'users.name as user_name')
            ->where('hc.commande_id', $id)
            ->orderBy('hc.date_action', 'desc')
            ->get();

        return view('commande.show', compact('commande', 'historique'));
    }

    /**
     * Créer une nouvelle commande
     */
    public function create()
    {
        $vehicules = DB::table('vehicules')->where('disponible', true)->get();
        $chauffeurs = DB::table('users')->where('role', 'agent')->get();
        $clients = DB::table('clients')->get();

        return view('commande.create', compact('vehicules', 'chauffeurs', 'clients'));
    }

    /**
     * Enregistrer une nouvelle commande
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255|unique:commandes_recentes',
            'client_nom' => 'required|string|max:255',
            'montant_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,confirmee,en_cours,livree,annulee',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'description' => 'nullable|string|max:1000'
        ]);

        $validated['user_id'] = Auth::id();

        $commandeId = DB::table('commandes_recentes')->insertGetId($validated);

        // Ajouter l'historique
        DB::table('historique_commandes')->insert([
            'commande_id' => $commandeId,
            'action' => 'cree',
            'description' => 'Commande créée',
            'user_id' => Auth::id(),
            'date_action' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('commercial.commande.index')->with('success', 'Commande créée avec succès');
    }

    /**
     * API pour les statistiques des commandes
     */
    public function apiStats()
    {
        $stats = [
            'total' => DB::table('commandes_recentes')->count(),
            'en_attente' => DB::table('commandes_recentes')->where('statut', 'en_attente')->count(),
            'confirmee' => DB::table('commandes_recentes')->where('statut', 'confirmee')->count(),
            'en_cours' => DB::table('commandes_recentes')->where('statut', 'en_cours')->count(),
            'livree' => DB::table('commandes_recentes')->where('statut', 'livree')->count(),
            'annulee' => DB::table('commandes_recentes')->where('statut', 'annulee')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * API pour la liste des commandes
     */
    public function apiIndex(Request $request)
    {
        $query = DB::table('commandes_recentes as cr')
            ->leftJoin('users', 'cr.user_id', '=', 'users.id')
            ->select('cr.*', 'users.name as created_by_name');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('cr.statut', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cr.reference', 'like', "%{$search}%")
                  ->orWhere('cr.client_nom', 'like', "%{$search}%");
            });
        }

        $commandes = $query->orderBy('cr.date_commande', 'desc')->paginate(15);

        return response()->json($commandes);
    }

    /**
     * Mettre à jour une commande
     */
    public function update(Request $request, $id)
    {
        $commande = DB::table('commandes_recentes')->where('id', $id)->first();

        if (!$commande) {
            abort(404);
        }

        $validated = $request->validate([
            'reference' => 'required|string|max:255|unique:commandes_recentes,reference,' . $id,
            'client_nom' => 'required|string|max:255',
            'montant_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,confirmee,en_cours,livree,annulee',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'description' => 'nullable|string|max:1000'
        ]);

        DB::table('commandes_recentes')->where('id', $id)->update($validated);

        // Ajouter l'historique
        DB::table('historique_commandes')->insert([
            'commande_id' => $id,
            'action' => 'modifie',
            'description' => 'Commande modifiée',
            'nouvelle_valeur' => json_encode($validated),
            'user_id' => Auth::id(),
            'date_action' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('commercial.commande.index')
            ->with('success', 'Commande mise à jour avec succès');
    }

    /**
     * Supprimer une commande
     */
    public function destroy($id)
    {
        $commande = DB::table('commandes_recentes')->where('id', $id)->first();

        if (!$commande) {
            abort(404);
        }

        DB::table('commandes_recentes')->where('id', $id)->delete();

        return redirect()->route('commercial.commande.index')
            ->with('success', 'Commande supprimée avec succès');
    }
}
