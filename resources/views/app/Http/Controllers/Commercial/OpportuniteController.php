<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Opportunite;
use Illuminate\Http\Request;

class OpportuniteController extends Controller
{
    public function index()
    {
        $opportunites = Opportunite::with('client')->orderByDesc('created_at')->paginate(15);
        $clients = Client::orderBy('nom')->get();

        return view('commercial.opportunites', compact('opportunites', 'clients'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        return view('commercial.opportunites-create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'montant' => 'required|numeric|min:0',
            'statut' => 'required|string',
            'probabilite' => 'required|integer|min:0|max:100',
            'date_echeance' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Opportunite::create($validated);

        return redirect()->route('commercial.opportunites.index')->with('success', 'Opportunité créée avec succès');
    }

    public function show(Opportunite $opportunite)
    {
        $opportunite->load('client');
        return view('commercial.opportunites-show', compact('opportunite'));
    }

    public function edit(Opportunite $opportunite)
    {
        $clients = Client::orderBy('nom')->get();
        return view('commercial.opportunites-edit', compact('opportunite', 'clients'));
    }

    public function update(Request $request, Opportunite $opportunite)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'montant' => 'required|numeric|min:0',
            'statut' => 'required|string',
            'probabilite' => 'required|integer|min:0|max:100',
            'date_echeance' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $opportunite->update($validated);

        return redirect()->route('commercial.opportunites.index')->with('success', 'Opportunité mise à jour avec succès');
    }

    public function destroy(Opportunite $opportunite)
    {
        $opportunite->delete();

        return redirect()->route('commercial.opportunites.index')->with('success', 'Opportunité supprimée avec succès');
    }
}
