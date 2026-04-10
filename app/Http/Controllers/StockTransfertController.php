<?php

namespace App\Http\Controllers;

use App\Models\StockTransfert;
use Illuminate\Http\Request;

class StockTransfertController extends Controller
{
    public function index()
    {
        $transferts = StockTransfert::with('user')->orderByDesc('date')->paginate(50);
        return view('stock.transferts.index', compact('transferts'));
    }

    public function create()
    {
        return view('stock.transferts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'produit_nom' => 'required|string|max:200',
            'quantite' => 'required|numeric|min:0.01',
            'entrepot_source' => 'required|string|max:200',
            'entrepot_destination' => 'required|string|max:200',
            'motif' => 'nullable|string',
        ]);

        // Vérifier que source et destination sont différents
        if ($data['entrepot_source'] === $data['entrepot_destination']) {
            return back()->withErrors(['entrepot_destination' => 'L\'entrepôt de destination doit être différent de l\'entrepôt source.'])->withInput();
        }

        // Ajouter l'utilisateur connecté si disponible
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        // Statut par défaut
        $data['statut'] = 'En attente';

        StockTransfert::create($data);

        return redirect()->route('warehouse.transferts')->with('success', 'Transfert créé avec succès.');
    }

    public function show(StockTransfert $transfert)
    {
        return view('stock.transferts.show', compact('transfert'));
    }

    public function updateStatut(StockTransfert $transfert, Request $request)
    {
        $request->validate([
            'statut' => 'required|in:En attente,En cours,Terminé,Annulé',
        ]);

        $transfert->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }
}
