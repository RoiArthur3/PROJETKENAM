<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use Illuminate\Http\Request;

class CaisseController extends Controller
{
    public function index()
    {
        $caisses = Caisse::with('responsable')->get();
        return view('tresorerie.caisses.index', compact('caisses'));
    }

    public function create()
    {
        return view('tresorerie.caisses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'solde_initial' => 'required|numeric',
            'responsable_id' => 'nullable|exists:users,id',
        ]);

        Caisse::create($request->all());
        return redirect()->route('tresorerie.caisses.index')
            ->with('success', 'Caisse créée avec succès.');
    }

    public function show(Caisse $caisse)
    {
        $caisse->load('mouvements', 'responsable');
        return view('tresorerie.caisses.show', compact('caisse'));
    }

    public function edit(Caisse $caisse)
    {
        return view('tresorerie.caisses.edit', compact('caisse'));
    }

    public function update(Request $request, Caisse $caisse)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'responsable_id' => 'nullable|exists:users,id',
        ]);

        $caisse->update($request->all());
        return redirect()->route('tresorerie.caisses.index')
            ->with('success', 'Caisse mise à jour avec succès.');
    }

    public function destroy(Caisse $caisse)
    {
        $caisse->delete();
        return redirect()->route('tresorerie.caisses.index')
            ->with('success', 'Caisse supprimée avec succès.');
    }
}
