<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    public function index()
    {
        $prospects = Prospect::orderByDesc('created_at')->paginate(15);
        return view('commercial.prospects', compact('prospects'));
    }

    public function create()
    {
        return view('commercial.prospects-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'entreprise' => 'nullable|string|max:255',
            'statut' => 'required|string',
            'source' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Prospect::create($validated);

        return redirect()->route('commercial.prospects.index')->with('success', 'Prospect créé avec succès');
    }

    public function show(Prospect $prospect)
    {
        return view('commercial.prospects-show', compact('prospect'));
    }

    public function edit(Prospect $prospect)
    {
        return view('commercial.prospects-edit', compact('prospect'));
    }

    public function update(Request $request, Prospect $prospect)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'entreprise' => 'nullable|string|max:255',
            'statut' => 'required|string',
            'source' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $prospect->update($validated);

        return redirect()->route('commercial.prospects.index')->with('success', 'Prospect mis à jour avec succès');
    }

    public function destroy(Prospect $prospect)
    {
        $prospect->delete();

        return redirect()->route('commercial.prospects.index')->with('success', 'Prospect supprimé avec succès');
    }
}
