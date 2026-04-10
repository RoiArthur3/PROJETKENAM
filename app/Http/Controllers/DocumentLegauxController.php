<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use Illuminate\Http\Request;

class DocumentLegauxController extends Controller
{
    public function create()
    {
        $vehicles = Vehicule::orderBy('immatriculation')->get(['id','immatriculation','modele']);
        return view('parc.documents-legaux.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        // Logique de sauvegarde à implémenter
        return redirect()->route('parc.documents-legaux.index')
            ->with('status', 'Document légal enregistré avec succès.');
    }
}
