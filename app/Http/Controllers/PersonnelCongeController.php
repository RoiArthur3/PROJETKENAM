<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PersonnelCongeController extends Controller
{
    /**
     * Afficher la gestion des congés d'un personnel
     */
    public function index(Personnel $personnel)
    {
        $this->authorize('view', $personnel);
        
        // Calcul des statistiques
        $stats = [
            'solde' => $personnel->soldeConges(),
            'pris' => $personnel->conges()->where('statut', 'VALIDE')->sum('nb_jours'),
            'enAttente' => $personnel->conges()->where('statut', 'EN_ATTENTE')->count(),
            'totalAnnee' => $personnel->conges()->whereYear('date_debut', now()->year)->count(),
        ];

        return view('rh.personnel.conges', compact('personnel', 'stats'));
    }

    /**
     * Enregistrer une nouvelle demande de congé
     */
    public function store(Request $request, Personnel $personnel)
    {
        $this->authorize('update', $personnel);

        $validated = $request->validate([
            'type' => 'required|in:ANNUEL,MALADIE,MATERNITE,PATERNITE,EXCEPTIONNEL',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string|max:500',
        ]);

        // Calcul du nombre de jours
        $dateDebut = \Carbon\Carbon::parse($validated['date_debut']);
        $dateFin = \Carbon\Carbon::parse($validated['date_fin']);
        $nbJours = $dateDebut->diffInDays($dateFin) + 1;

        // Vérification du solde pour congé annuel
        if ($validated['type'] === 'ANNUEL') {
            $soldeDisponible = $personnel->soldeConges();
            if ($nbJours > $soldeDisponible) {
                return back()->with('error', "Solde insuffisant ! Il vous reste {$soldeDisponible} jours de congé annuel.");
            }
        }

        // Création de la demande de congé
        $personnel->conges()->create([
            'type' => $validated['type'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'nb_jours' => $nbJours,
            'motif' => $validated['motif'],
            'statut' => 'EN_ATTENTE',
            'demande_par' => Auth::id(),
            'date_demande' => now(),
        ]);

        return back()->with('success', 'Demande de congé enregistrée avec succès');
    }

    /**
     * Annuler une demande de congé
     */
    public function destroy(Personnel $personnel, $congeId)
    {
        $this->authorize('update', $personnel);

        $conge = $personnel->conges()->findOrFail($congeId);

        if ($conge->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Seules les demandes en attente peuvent être annulées');
        }

        $conge->update([
            'statut' => 'ANNULE',
            'date_annulation' => now(),
            'annule_par' => Auth::id(),
        ]);

        return back()->with('success', 'Demande de congé annulée avec succès');
    }

    /**
     * Valider une demande de congé (pour les managers)
     */
    public function valider(Request $request, Personnel $personnel, $congeId)
    {
        $this->authorize('manageConges', Personnel::class);

        $conge = $personnel->conges()->findOrFail($congeId);

        if ($conge->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Cette demande ne peut plus être validée');
        }

        $validated = $request->validate([
            'decision' => 'required|in:VALIDE,REFUSE',
            'motif_decision' => 'required_if:decision,REFUSE|string|max:500',
        ]);

        $conge->update([
            'statut' => $validated['decision'],
            'date_decision' => now(),
            'decision_par' => Auth::id(),
            'motif_decision' => $validated['motif_decision'] ?? null,
        ]);

        $message = $validated['decision'] === 'VALIDE' 
            ? 'Demande de congé validée avec succès' 
            : 'Demande de congé refusée';

        return back()->with('success', $message);
    }
}
