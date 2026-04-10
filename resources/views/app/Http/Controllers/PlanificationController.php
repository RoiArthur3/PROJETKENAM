<?php

namespace App\Http\Controllers;

use App\Models\Planification;
use App\Models\ServiceOperationnel;
use Illuminate\Http\Request;

class PlanificationController extends Controller
{
    /**
     * Afficher la liste des planifications
     */
    public function index()
    {
        $planifications = Planification::with(['service', 'createur'])
            ->orderBy('date_planification', 'desc')
            ->paginate(20);

        return view('controle-audit.planifications.index', compact('planifications'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $services = ServiceOperationnel::actif()->orderBy('nom')->get();
        $types = [
            'audit_interne' => 'Audit Interne',
            'audit_externe' => 'Audit Externe',
            'controle_qualite' => 'Contrôle Qualité',
            'inspection_securite' => 'Inspection Sécurité',
            'evaluation_risque' => 'Évaluation des Risques',
            'revue_processus' => 'Revue de Processus',
            'verification_conformite' => 'Vérification Conformité'
        ];

        $priorites = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];

        $statuts = [
            'planifie' => 'Planifiée',
            'en_cours' => 'En Cours',
            'terminee' => 'Terminée',
            'annulee' => 'Annulée',
            'reportee' => 'Reportée'
        ];

        return view('controle-audit.planifications.create', compact(
            'services',
            'types',
            'priorites',
            'statuts'
        ));
    }

    /**
     * Enregistrer une nouvelle planification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type_planification' => 'required|in:audit_interne,audit_externe,controle_qualite,inspection_securite,evaluation_risque,revue_processus,verification_conformite',
            'service_concerne_id' => 'required|exists:services_operationnels,id',
            'date_planification' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'lieu' => 'required|string|max:255',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'statut' => 'required|in:planifie,en_cours,terminee,annulee,reportee',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:2048',
            'notes' => 'nullable|string|max:1000',
            'budget_estime' => 'nullable|numeric|min:0',
            'rapport_attendu' => 'boolean',
        ]);

        // Gérer les documents
        $documentsPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('planifications', 'public');
                $documentsPaths[] = $path;
            }
        }

        $planification = Planification::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'type_planification' => $validated['type_planification'],
            'service_concerne_id' => $validated['service_concerne_id'],
            'date_planification' => $validated['date_planification'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
            'lieu' => $validated['lieu'],
            'priorite' => $validated['priorite'],
            'statut' => $validated['statut'],
            'createur_id' => auth()->id(),
            'participants' => $validated['participants'] ?? [],
            'documents' => $documentsPaths,
            'notes' => $validated['notes'] ?? null,
            'budget_estime' => $validated['budget_estime'] ?? null,
            'rapport_attendu' => $validated['rapport_attendu'] ?? false,
        ]);

        return redirect()
            ->route('controle-audit.planifications.index')
            ->with('success', 'Planification créée avec succès');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Planification $planification)
    {
        $services = ServiceOperationnel::actif()->orderBy('nom')->get();
        $types = [
            'audit_interne' => 'Audit Interne',
            'audit_externe' => 'Audit Externe',
            'controle_qualite' => 'Contrôle Qualité',
            'inspection_securite' => 'Inspection Sécurité',
            'evaluation_risque' => 'Évaluation des Risques',
            'revue_processus' => 'Revue de Processus',
            'verification_conformite' => 'Vérification Conformité'
        ];

        $priorites = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];

        $statuts = [
            'planifie' => 'Planifiée',
            'en_cours' => 'En Cours',
            'terminee' => 'Terminée',
            'annulee' => 'Annulée',
            'reportee' => 'Reportée'
        ];

        return view('controle-audit.planifications.edit', compact(
            'planification',
            'services',
            'types',
            'priorites',
            'statuts'
        ));
    }

    /**
     * Mettre à jour une planification
     */
    public function update(Request $request, Planification $planification)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type_planification' => 'required|in:audit_interne,audit_externe,controle_qualite,inspection_securite,evaluation_risque,revue_processus,verification_conformite',
            'service_concerne_id' => 'required|exists:services_operationnels,id',
            'date_planification' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'lieu' => 'required|string|max:255',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'statut' => 'required|in:planifie,en_cours,terminee,annulee,reportee',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:2048',
            'notes' => 'nullable|string|max:1000',
            'budget_estime' => 'nullable|numeric|min:0',
            'rapport_attendu' => 'boolean',
        ]);

        // Gérer les documents
        $documentsPaths = $planification->documents ?? [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('planifications', 'public');
                $documentsPaths[] = $path;
            }
        }

        $planification->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'type_planification' => $validated['type_planification'],
            'service_concerne_id' => $validated['service_concerne_id'],
            'date_planification' => $validated['date_planification'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
            'lieu' => $validated['lieu'],
            'priorite' => $validated['priorite'],
            'statut' => $validated['statut'],
            'participants' => $validated['participants'] ?? [],
            'documents' => $documentsPaths,
            'notes' => $validated['notes'] ?? null,
            'budget_estime' => $validated['budget_estime'] ?? null,
            'rapport_attendu' => $validated['rapport_attendu'] ?? false,
        ]);

        return redirect()
            ->route('controle-audit.planifications.index')
            ->with('success', 'Planification mise à jour avec succès');
    }

    /**
     * Supprimer une planification
     */
    public function destroy(Planification $planification)
    {
        $planification->delete();

        return redirect()
            ->route('controle-audit.planifications.index')
            ->with('success', 'Planification supprimée avec succès');
    }

    /**
     * Afficher les détails d'une planification
     */
    public function show(Planification $planification)
    {
        $planification->load(['service', 'createur', 'participants']);
        return view('controle-audit.planifications.show', compact('planification'));
    }

    /**
     * Dupliquer une planification
     */
    public function duplicate(Planification $planification)
    {
        $nouvellePlanification = $planification->replicate();
        $nouvellePlanification->titre = $planification->titre . ' (Copie)';
        $nouvellePlanification->statut = 'planifie';
        $nouvellePlanification->createur_id = auth()->id();
        $nouvellePlanification->save();

        return redirect()
            ->route('controle-audit.planifications.edit', $nouvellePlanification)
            ->with('success', 'Planification dupliquée avec succès');
    }

    /**
     * API pour récupérer les planifications
     */
    public function apiIndex(Request $request)
    {
        $query = Planification::with(['service', 'createur']);

        // Filtrage par date
        if ($request->has('date_debut')) {
            $query->whereDate('date_planification', '>=', $request->date_debut);
        }
        if ($request->has('date_fin')) {
            $query->whereDate('date_planification', '<=', $request->date_fin);
        }

        // Filtrage par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrage par priorité
        if ($request->has('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        // Filtrage par service
        if ($request->has('service_id')) {
            $query->where('service_concerne_id', $request->service_id);
        }

        $planifications = $query->orderBy('date_planification', 'desc')->paginate(20);

        return response()->json($planifications);
    }

    /**
     * Changer le statut d'une planification
     */
    public function changeStatut(Request $request, Planification $planification)
    {
        $validated = $request->validate([
            'statut' => 'required|in:planifie,en_cours,terminee,annulee,reportee',
            'motif' => 'required_if:statut,annulee,reportee|string|max:500'
        ]);

        $planification->update([
            'statut' => $validated['statut'],
            'notes' => $validated['motif'] ?? $planification->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'statut' => $validated['statut']
        ]);
    }
}
