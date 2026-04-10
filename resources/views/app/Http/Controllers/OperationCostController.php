<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\OperationCost;
use Illuminate\Http\Request;

class OperationCostController extends Controller
{
    public function store(Request $request, Operation $operation)
    {
        $data = $request->validate([
            'type' => 'required|in:depense,facture',
            'description' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'fournisseur' => 'nullable|string|max:255',
        ]);

        $data['operation_id'] = $operation->id;
        OperationCost::create($data);

        return redirect()->route('operations.couts', $operation)
            ->with('status', 'Coût ajouté avec succès.');
    }
}
