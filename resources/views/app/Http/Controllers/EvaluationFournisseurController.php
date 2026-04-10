<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\EvaluationFournisseur;
use App\Models\CommandeFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class EvaluationFournisseurController extends Controller
{
    /**
     * Afficher la liste des évaluations d'un fournisseur
     */
    public function index(Fournisseur $fournisseur, Request $request)
    {
        $query = $fournisseur->evaluations()
            ->with(['evaluateur', 'validateur'])
            ->latest('date_evaluation');
        
        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }
        
        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('date_evaluation', [
                $request->input('debut'),
                $request->input('fin')
            ]);
        }
        
        $evaluations = $query->paginate(10)->withQueryString();
        
        // Statistiques
        $stats = [
            'total' => $fournisseur->evaluations()->count(),
            'moyenne' => round($fournisseur->evaluations()->avg('note_globale_ponderee'), 1),
            'en_cours' => $fournisseur->evaluations()->where('statut', 'en_cours')->count(),
            'termine' => $fournisseur->evaluations()->where('statut', 'termine')->count(),
            'annule' => $fournisseur->evaluations()->where('statut', 'annule')->count(),
        ];
        
        // Dernières commandes non évaluées
        $commandesNonEvaluees = $fournisseur->commandes()
            ->whereDoesntHave('evaluations')
            ->where('statut', 'livree')
            ->where('date_livraison_reelle', '>=', now()->subMonths(6))
            ->orderBy('date_livraison_reelle', 'desc')
            ->take(5)
            ->get();
        
        return view('fournisseurs.evaluations.index', compact(
            'fournisseur', 
            'evaluations', 
            'stats',
            'commandesNonEvaluees'
        ));
    }

    /**
     * Afficher le formulaire de création d'une évaluation
     */
    public function create(Fournisseur $fournisseur, Request $request)
    {
        // Vérifier s'il y a une commande à évaluer
        $commande = null;
        if ($request->filled('commande_id')) {
            $commande = CommandeFournisseur::where('fournisseur_id', $fournisseur->id)
                ->findOrFail($request->input('commande_id'));
        }
        
        // Critères d'évaluation par défaut
        $criteres = [
            'qualite' => [
                ['libelle' => 'Conformité des produits/services', 'poids' => 30],
                ['libelle' => 'Qualité des produits/services', 'poids' => 30],
                ['libelle' => 'Emballage et conditionnement', 'poids' => 20],
                ['libelle' => 'Documentation fournie', 'poids' => 20],
            ],
            'prix' => [
                ['libelle' => 'Compétitivité des prix', 'poids' => 40],
                ['libelle' => 'Respect des tarifs convenus', 'poids' => 30],
                ['libelle' => 'Transparence des coûts', 'poids' => 30],
            ],
            'delai' => [
                ['libelle' => 'Respect des délais de livraison', 'poids' => 50],
                ['libelle' => 'Délai de réponse aux demandes', 'poids' => 30],
                ['libelle' => 'Réactivité face aux urgences', 'poids' => 20],
            ],
            'service' => [
                ['libelle' => 'Accueil et disponibilité', 'poids' => 25],
                ['libelle' => 'Qualité du conseil', 'poids' => 25],
                ['libelle' => 'Gestion des réclamations', 'poids' => 30],
                ['libelle' => 'Proactivité', 'poids' => 20],
            ],
            'reactivite' => [
                ['libelle' => 'Réactivité aux demandes', 'poids' => 40],
                ['libelle' => 'Capacité à résoudre les problèmes', 'poids' => 40],
                ['libelle' => 'Communication', 'poids' => 20],
            ]
        ];
        
        // Période d'évaluation par défaut
        $periodeDebut = now()->startOfMonth()->subMonth();
        $periodeFin = now()->endOfMonth()->subMonth();
        
        return view('fournisseurs.evaluations.create', compact(
            'fournisseur',
            'commande',
            'criteres',
            'periodeDebut',
            'periodeFin'
        ));
    }

    /**
     * Enregistrer une nouvelle évaluation
     */
    public function store(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:50|unique:evaluation_fournisseurs,reference',
            'date_evaluation' => 'required|date',
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date|after_or_equal:periode_debut',
            'commande_id' => 'nullable|exists:commande_fournisseurs,id,fournisseur_id,' . $fournisseur->id,
            'evaluateur_id' => 'required|exists:users,id',
            'criteres' => 'required|array',
            'criteres.qualite' => 'required|array',
            'criteres.qualite.*.libelle' => 'required|string|max:255',
            'criteres.qualite.*.note' => 'required|numeric|min:0|max:5',
            'criteres.qualite.*.poids' => 'required|numeric|min:0|max:100',
            'criteres.qualite.*.commentaire' => 'nullable|string',
            'criteres.prix' => 'required|array',
            'criteres.prix.*.libelle' => 'required|string|max:255',
            'criteres.prix.*.note' => 'required|numeric|min:0|max:5',
            'criteres.prix.*.poids' => 'required|numeric|min:0|max:100',
            'criteres.prix.*.commentaire' => 'nullable|string',
            'criteres.delai' => 'required|array',
            'criteres.delai.*.libelle' => 'required|string|max:255',
            'criteres.delai.*.note' => 'required|numeric|min:0|max:5',
            'criteres.delai.*.poids' => 'required|numeric|min:0|max:100',
            'criteres.delai.*.commentaire' => 'nullable|string',
            'criteres.service' => 'required|array',
            'criteres.service.*.libelle' => 'required|string|max:255',
            'criteres.service.*.note' => 'required|numeric|min:0|max:5',
            'criteres.service.*.poids' => 'required|numeric|min:0|max:100',
            'criteres.service.*.commentaire' => 'nullable|string',
            'criteres.reactivite' => 'required|array',
            'criteres.reactivite.*.libelle' => 'required|string|max:255',
            'criteres.reactivite.*.note' => 'required|numeric|min:0|max:5',
            'criteres.reactivite.*.poids' => 'required|numeric|min:0|max:100',
            'criteres.reactivite.*.commentaire' => 'nullable|string',
            'points_forts' => 'required|array|min:1',
            'points_forts.*' => 'required|string|max:255',
            'points_faibles' => 'required|array|min:1',
            'points_faibles.*' => 'required|string|max:255',
            'preconisations' => 'required|array|min:1',
            'preconisations.*' => 'required|string|max:255',
            'commentaires' => 'nullable|string',
            'est_anonyme' => 'boolean',
        ]);
        
        // Calcul des notes par catégorie
        $notes = [
            'qualite' => $this->calculerNoteCategorie($validated['criteres']['qualite']),
            'prix' => $this->calculerNoteCategorie($validated['criteres']['prix']),
            'delai' => $this->calculerNoteCategorie($validated['criteres']['delai']),
            'service' => $this->calculerNoteCategorie($validated['criteres']['service']),
            'reactivite' => $this->calculerNoteCategorie($validated['criteres']['reactivite']),
        ];
        
        // Calcul de la note globale pondérée
        $poidTotal = 0;
        $noteGlobalePonderee = 0;
        
        $poidsCategories = [
            'qualite' => 30,
            'prix' => 20,
            'delai' => 25,
            'service' => 15,
            'reactivite' => 10,
        ];
        
        foreach ($notes as $categorie => $note) {
            $noteGlobalePonderee += $note['moyenne'] * $poidsCategories[$categorie];
            $poidTotal += $poidsCategories[$categorie];
        }
        
        $noteGlobalePonderee = $poidTotal > 0 ? $noteGlobalePonderee / $poidTotal : 0;
        
        // Déterminer le classement
        $classement = $this->determinerClassement($noteGlobalePonderee);
        
        // Création de l'évaluation
        $evaluation = new EvaluationFournisseur([
            'reference' => $validated['reference'] ?? 'EVAL-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'fournisseur_id' => $fournisseur->id,
            'commande_id' => $validated['commande_id'] ?? null,
            'date_evaluation' => $validated['date_evaluation'],
            'periode_debut' => $validated['periode_debut'],
            'periode_fin' => $validated['periode_fin'],
            'evaluateur_id' => $validated['evaluateur_id'],
            'note_qualite' => $notes['qualite']['moyenne'],
            'note_prix' => $notes['prix']['moyenne'],
            'note_delai' => $notes['delai']['moyenne'],
            'note_service' => $notes['service']['moyenne'],
            'note_reactivite' => $notes['reactivite']['moyenne'],
            'note_globale' => ($notes['qualite']['moyenne'] + $notes['prix']['moyenne'] + $notes['delai']['moyenne'] + 
                              $notes['service']['moyenne'] + $notes['reactivite']['moyenne']) / 5,
            'note_globale_ponderee' => $noteGlobalePonderee,
            'classement' => $classement,
            'criteres_qualite' => $validated['criteres']['qualite'],
            'criteres_prix' => $validated['criteres']['prix'],
            'criteres_delai' => $validated['criteres']['delai'],
            'criteres_service' => $validated['criteres']['service'],
            'criteres_reactivite' => $validated['criteres']['reactivite'],
            'points_forts' => $validated['points_forts'],
            'points_faibles' => $validated['points_faibles'],
            'preconisations' => $validated['preconisations'],
            'commentaires' => $validated['commentaires'] ?? null,
            'est_anonyme' => $validated['est_anonyme'] ?? false,
            'statut' => 'termine',
            'user_id' => auth()->id(),
        ]);
        
        // Enregistrement dans une transaction
        DB::beginTransaction();
        
        try {
            $evaluation->save();
            
            // Lier la commande à l'évaluation si spécifiée
            if ($evaluation->commande_id) {
                $evaluation->commandes()->attach($evaluation->commande_id);
            }
            
            // Mettre à jour la note moyenne du fournisseur
            $fournisseur->evaluation_moyenne = $fournisseur->evaluations()
                ->where('statut', 'termine')
                ->avg('note_globale_ponderee');
                
            // Mettre à jour le classement du fournisseur
            $fournisseur->classement = $this->determinerClassement($fournisseur->evaluation_moyenne);
            
            $fournisseur->save();
            
            DB::commit();
            
            return redirect()
                ->route('fournisseurs.evaluations.show', ['fournisseur' => $fournisseur->id, 'evaluation' => $evaluation->id])
                ->with('success', 'L\'évaluation a été enregistrée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'évaluation : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une évaluation
     */
    public function show(Fournisseur $fournisseur, EvaluationFournisseur $evaluation)
    {
        $evaluation->load(['evaluateur', 'validateur', 'commandes']);
        
        // Statistiques des évaluations du fournisseur
        $stats = [
            'moyenne' => $fournisseur->evaluations()
                ->where('statut', 'termine')
                ->avg('note_globale_ponderee'),
            'total' => $fournisseur->evaluations()->count(),
            'derniere_evaluation' => $fournisseur->evaluations()
                ->where('id', '!=', $evaluation->id)
                ->latest('date_evaluation')
                ->first(),
            'evolution' => $this->calculerEvolution($fournisseur, $evaluation),
        ];
        
        return view('fournisseurs.evaluations.show', compact(
            'fournisseur',
            'evaluation',
            'stats'
        ));
    }

    /**
     * Valider une évaluation
     */
    public function valider(Request $request, Fournisseur $fournisseur, EvaluationFournisseur $evaluation)
    {
        if ($evaluation->est_valide) {
            return back()->with('warning', 'Cette évaluation a déjà été validée.');
        }
        
        $evaluation->est_valide = true;
        $evaluation->date_validation = now();
        $evaluation->validateur_id = auth()->id();
        $evaluation->save();
        
        return back()->with('success', 'L\'évaluation a été validée avec succès.');
    }

    /**
     * Annuler une évaluation
     */
    public function annuler(Request $request, Fournisseur $fournisseur, EvaluationFournisseur $evaluation)
    {
        if ($evaluation->est_valide) {
            return back()->with('error', 'Impossible d\'annuler une évaluation déjà validée.');
        }
        
        $evaluation->statut = 'annule';
        $evaluation->save();
        
        return back()->with('success', 'L\'évaluation a été annulée avec succès.');
    }

    /**
     * Générer un PDF de l'évaluation
     */
    public function pdf(Fournisseur $fournisseur, EvaluationFournisseur $evaluation)
    {
        $evaluation->load(['evaluateur', 'validateur', 'commandes']);
        
        $pdf = PDF::loadView('fournisseurs.evaluations.pdf', [
            'fournisseur' => $fournisseur,
            'evaluation' => $evaluation,
        ]);
        
        return $pdf->download('evaluation-' . $evaluation->reference . '.pdf');
    }

    /**
     * Calculer la note d'une catégorie de critères
     */
    private function calculerNoteCategorie($criteres)
    {
        $totalPondere = 0;
        $totalPoids = 0;
        
        foreach ($criteres as $critere) {
            $note = $critere['note'];
            $poids = $critere['poids'];
            
            $totalPondere += $note * $poids;
            $totalPoids += $poids;
        }
        
        $moyenne = $totalPoids > 0 ? $totalPondere / $totalPoids : 0;
        
        return [
            'moyenne' => round($moyenne, 2),
            'total_pondere' => $totalPondere,
            'total_poids' => $totalPoids,
        ];
    }
    
    /**
     * Déterminer le classement en fonction de la note
     */
    private function determinerClassement($note)
    {
        if ($note >= 4.5) return 'Excellent';
        if ($note >= 4.0) return 'Très bon';
        if ($note >= 3.5) return 'Bon';
        if ($note >= 3.0) return 'Satisfaisant';
        if ($note >= 2.0) return 'À améliorer';
        return 'Insuffisant';
    }
    
    /**
     * Calculer l'évolution par rapport à la dernière évaluation
     */
    private function calculerEvolution($fournisseur, $evaluationCourante)
    {
        $derniereEvaluation = $fournisseur->evaluations()
            ->where('id', '!=', $evaluationCourante->id)
            ->where('statut', 'termine')
            ->latest('date_evaluation')
            ->first();
            
        if (!$derniereEvaluation) {
            return [
                'variation' => 0,
                'tendance' => 'stable',
            ];
        }
        
        $variation = $evaluationCourante->note_globale_ponderee - $derniereEvaluation->note_globale_ponderee;
        
        return [
            'variation' => $variation,
            'tendance' => $variation > 0 ? 'up' : ($variation < 0 ? 'down' : 'stable'),
            'derniere_note' => $derniereEvaluation->note_globale_ponderee,
            'date_derniere_evaluation' => $derniereEvaluation->date_evaluation,
        ];
    }
}
