<?php

namespace App\Http\Controllers;

use App\Models\Inventaire;
use App\Models\Entrepot;
use Illuminate\Http\Request;

class InventaireController extends Controller
{
    public function index()
    {
        $inventaires = Inventaire::with('user')->orderByDesc('date')->paginate(50);
        return view('stock.inventaire.index', compact('inventaires'));
    }

    public function create()
    {
        $entrepots = Entrepot::where('actif', true)->orderBy('nom')->get();
        return view('stock.inventaire.create', compact('entrepots'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'entrepot_nom' => 'required|string|max:200',
            'responsable' => 'required|string|max:200',
            'type' => 'required|string|in:Complet,Partiel,Tournant',
            'notes' => 'nullable|string',
        ]);

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        $data['statut'] = 'En cours';

        Inventaire::create($data);

        return redirect()->route('stock.inventaire')->with('success', 'Inventaire créé avec succès.');
    }
}
