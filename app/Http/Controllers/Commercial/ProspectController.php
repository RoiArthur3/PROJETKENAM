<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProspectController extends Controller
{
    private function ensureProspectsTableExists()
    {
        return Schema::hasTable('prospects');
    }

    public function index()
    {
        $prospects = collect([]);
        $dbError = null;

        if ($this->ensureProspectsTableExists()) {
            $prospects = Prospect::orderByDesc('created_at')->paginate(15);
        } else {
            $dbError = "La table 'prospects' n'existe pas encore. Exécutez les migrations commerciales pour activer ce module.";
        }

        return view('commercial.prospects', compact('prospects', 'dbError'));
    }

    public function create()
    {
        if (!$this->ensureProspectsTableExists()) {
            return redirect()->route('commercial.prospects.index')
                ->with('error', "La table 'prospects' n'existe pas encore.");
        }

        return view('commercial.prospects-create');
    }

    public function store(Request $request)
    {
        if (!$this->ensureProspectsTableExists()) {
            return redirect()->route('commercial.prospects.index')
                ->with('error', "La table 'prospects' n'existe pas encore.");
        }

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
        if (!$this->ensureProspectsTableExists()) {
            return redirect()->route('commercial.prospects.index')
                ->with('error', "La table 'prospects' n'existe pas encore.");
        }

        return view('commercial.prospects-show', compact('prospect'));
    }

    public function edit(Prospect $prospect)
    {
        if (!$this->ensureProspectsTableExists()) {
            return redirect()->route('commercial.prospects.index')
                ->with('error', "La table 'prospects' n'existe pas encore.");
        }

        return view('commercial.prospects-edit', compact('prospect'));
    }

    public function update(Request $request, Prospect $prospect)
    {
        if (!$this->ensureProspectsTableExists()) {
            return redirect()->route('commercial.prospects.index')
                ->with('error', "La table 'prospects' n'existe pas encore.");
        }

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
        if (!$this->ensureProspectsTableExists()) {
            return redirect()->route('commercial.prospects.index')
                ->with('error', "La table 'prospects' n'existe pas encore.");
        }

        $prospect->delete();

        return redirect()->route('commercial.prospects.index')->with('success', 'Prospect supprimé avec succès');
    }
}
