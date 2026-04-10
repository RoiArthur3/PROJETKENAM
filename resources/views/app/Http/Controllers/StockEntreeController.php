<?php

namespace App\Http\Controllers;

use App\Models\StockEntree;
use Illuminate\Http\Request;

class StockEntreeController extends Controller
{
    public function index()
    {
        $entrees = StockEntree::with('user')->orderByDesc('date')->paginate(50);
        return view('stock.entrees.index', compact('entrees'));
    }

    public function create()
    {
        return view('stock.entrees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'required|string|in:Achat,Retour,Ajustement,Transfert',
            'produit_nom' => 'required|string|max:200',
            'quantite' => 'required|numeric|min:0.01',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'fournisseur' => 'nullable|string|max:200',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Calculer le montant total
        if ($request->filled('prix_unitaire')) {
            $data['montant_total'] = $data['quantite'] * $data['prix_unitaire'];
        }

        // Ajouter l'utilisateur connecté si disponible
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        StockEntree::create($data);

        return redirect()->route('stock.entrees')->with('success', 'Entrée de stock enregistrée avec succès.');
    }

    public function show(StockEntree $entree)
    {
        return view('stock.entrees.show', compact('entree'));
    }
}
