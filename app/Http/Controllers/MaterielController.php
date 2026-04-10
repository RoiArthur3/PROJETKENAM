<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterielController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $materiels = DB::table('materiels')->paginate(10);
        return view('materiel.index', compact('materiels'));
    }

    public function dashboard()
    {
        $stats = [
            'total' => DB::table('materiels')->count(),
            'actifs' => DB::table('materiels')->where('est_actif', true)->count(),
            'inactifs' => DB::table('materiels')->where('est_actif', false)->count(),
        ];

        return view('materiel.dashboard', compact('stats'));
    }

    public function create()
    {
        return view('materiel.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|unique:materiels',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('materiels')->insert($validated);

        return redirect()->route('materiel.index')->with('success', 'Matériel créé avec succès');
    }

    public function show($id)
    {
        $materiel = DB::table('materiels')->where('id', $id)->first();
        return view('materiel.show', compact('materiel'));
    }

    public function edit($id)
    {
        $materiel = DB::table('materiels')->where('id', $id)->first();
        return view('materiel.edit', compact('materiel'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|unique:materiels,reference,' . $id,
            'description' => 'nullable|string',
        ]);

        $validated['updated_at'] = now();

        DB::table('materiels')->where('id', $id)->update($validated);

        return redirect()->route('materiel.index')->with('success', 'Matériel mis à jour');
    }

    public function destroy($id)
    {
        DB::table('materiels')->where('id', $id)->delete();
        return redirect()->route('materiel.index')->with('success', 'Matériel supprimé');
    }
}
