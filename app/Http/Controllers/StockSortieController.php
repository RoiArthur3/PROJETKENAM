<?php

namespace App\Http\Controllers;

use App\Models\StockSortie;
use App\Models\Produit;
use Illuminate\Http\Request;

class StockSortieController extends Controller
{
    public function index()
    {
        $sorties = StockSortie::with(['produit', 'user'])->orderByDesc('date')->paginate(50);
        return view('stock.sorties.index', compact('sorties'));
    }

    public function create()
    {
        $produits = Produit::where('actif', true)->orderBy('designation')->get();
        return view('stock.sorties.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'required|string|in:Utilisation,Perte,Casse,Retour,Autre',
            'produit_nom' => 'required|string|max:200',
            'quantite' => 'required|numeric|min:0.01',
            'destinataire' => 'nullable|string|max:200',
            'demandeur' => 'nullable|string|max:200',
            'motif' => 'nullable|string',
        ]);

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        StockSortie::create($data);

        return redirect()->route('stock.exits')->with('success', 'Sortie de stock enregistrée avec succès.');
    }
}
