<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CongeController extends Controller
{
    public function index()
    {
        $conges = Conge::with(['user', 'valideur'])
            ->orderByDesc('date_demande')
            ->paginate(50);
        return view('rh.conges.index', compact('conges'));
    }

    public function create()
    {
        return view('rh.conges.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'agent_nom' => 'required|string|max:200',
            'type_conge' => 'required|string|in:Congé annuel,Congé maladie,Congé maternité,Congé sans solde,Permission,Autre',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        // Calculer le nombre de jours
        $dateDebut = Carbon::parse($data['date_debut']);
        $dateFin = Carbon::parse($data['date_fin']);
        $data['nombre_jours'] = $dateDebut->diffInDays($dateFin) + 1;

        // Ajouter l'utilisateur connecté
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        // Date de demande et statut par défaut
        $data['date_demande'] = now()->toDateString();
        $data['statut'] = 'En attente';

        Conge::create($data);

        return redirect()->route('rh.conges.index')->with('success', 'Demande de congé enregistrée avec succès.');
    }

    public function show(Conge $conge)
    {
        $conge->load(['user', 'valideur']);
        return view('rh.conges.show', compact('conge'));
    }

    public function updateStatut(Request $request, Conge $conge)
    {
        $request->validate([
            'statut' => 'required|in:Approuvé,Refusé',
            'commentaire_valideur' => 'nullable|string',
        ]);

        $conge->update([
            'statut' => $request->statut,
            'valideur_id' => auth()->id(),
            'date_validation' => now(),
            'commentaire_valideur' => $request->commentaire_valideur,
        ]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }
}
