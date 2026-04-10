<?php

namespace App\Http\Controllers;

use App\Models\Entretien;
use App\Models\Vehicule;
use Illuminate\Http\Request;

class EntretienController extends Controller
{
    public function index()
    {
        $entretiens = Entretien::with(['vehicule', 'user'])->orderByDesc('date')->paginate(50);
        return view('parc.entretiens.index', compact('entretiens'));
    }

    public function create()
    {
        $vehicules = Vehicule::orderBy('immatriculation')->get();
        return view('parc.entretiens.create', compact('vehicules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'date' => 'required|date',
            'type' => 'required|string|in:Vidange,Révision,Contrôle technique,Changement pneus,Autre',
            'kilometrage' => 'nullable|integer|min:0',
            'prestataire' => 'nullable|string|max:200',
            'cout' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        $data['statut'] = 'Planifié';

        Entretien::create($data);

        return redirect()->route('parc.entretiens')->with('success', 'Entretien enregistré avec succès.');
    }
}
