<?php

namespace App\Http\Controllers;

use App\Models\TypeOperation;
use Illuminate\Http\Request;

class TypeOperationController extends Controller
{
    public function index()
    {
        $types = TypeOperation::orderBy('libelle')->get();
        return view('parametrage.types-operations.index', compact('types'));
    }

    public function create()
    {
        return view('parametrage.types-operations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:types_operations,code',
            'libelle' => 'required|string|max:100',
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:20',
            'icone' => 'nullable|string|max:50',
            'actif' => 'boolean',
        ]);

        TypeOperation::create($data);

        return redirect()->route('admin.types-operations.index')
            ->with('success', 'Type d\'opération créé avec succès.');
    }

    public function edit(TypeOperation $typeOperation)
    {
        return view('parametrage.types-operations.edit', compact('typeOperation'));
    }

    public function update(Request $request, TypeOperation $typeOperation)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:types_operations,code,' . $typeOperation->id,
            'libelle' => 'required|string|max:100',
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:20',
            'icone' => 'nullable|string|max:50',
            'actif' => 'boolean',
        ]);

        $typeOperation->update($data);

        return redirect()->route('admin.types-operations.index')
            ->with('success', 'Type d\'opération mis à jour avec succès.');
    }

    public function destroy(TypeOperation $typeOperation)
    {
        $typeOperation->delete();

        return redirect()->route('admin.types-operations.index')
            ->with('success', 'Type d\'opération supprimé avec succès.');
    }
}
