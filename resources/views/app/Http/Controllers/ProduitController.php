<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::orderBy('designation')->paginate(50);
        return view('stock.produits.index', compact('produits'));
    }

    public function create()
    {
        return view('stock.produits.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:produits,code',
            'designation' => 'required|string|max:200',
            'categorie' => 'nullable|string|max:100',
            'unite' => 'required|string|max:50',
            'stock_min' => 'required|integer|min:0',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'emplacement' => 'nullable|string|max:200',
            'description' => 'nullable|string',
        ]);

        $data['stock_actuel'] = 0;
        $data['actif'] = true;

        Produit::create($data);

        return redirect()->route('stock.produits')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Produit $produit)
    {
        return view('stock.produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:produits,code,' . $produit->id,
            'designation' => 'required|string|max:200',
            'categorie' => 'nullable|string|max:100',
            'unite' => 'required|string|max:50',
            'stock_min' => 'required|integer|min:0',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'emplacement' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'actif' => 'boolean',
        ]);

        $produit->update($data);

        return redirect()->route('stock.produits')->with('success', 'Produit mis à jour avec succès.');
    }
}
