<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\ContratFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PDF;

class ContratFournisseurController extends Controller
{
    /**
     * Afficher la liste des contrats d'un fournisseur
     */
    public function indexByFournisseur(Fournisseur $fournisseur)
    {
        $contrats = $fournisseur->contrats()
            ->withCount('commandes')
            ->orderBy('date_fin', 'desc')
            ->paginate(10);
            
        return view('fournisseurs.contrats.index', compact('fournisseur', 'contrats'));
    }

    /**
     * Afficher la liste des contrats fournisseurs
     */
    public function index()
    {
        $contrats = ContratFournisseur::with('fournisseur')
            ->orderBy('date_fin', 'desc')
            ->paginate(15);
        $fournisseurs = \App\Models\Fournisseur::orderBy('raison_sociale')->get();
        return view('fournisseurs.contrats', compact('contrats', 'fournisseurs'));
    }

    /**
     * Afficher le formulaire de création d'un contrat
     */
    public function create(Fournisseur $fournisseur)
    {
        $typesContrat = [
            'achat' => 'Contrat d\'achat',
            'prestation' => 'Contrat de prestation de service',
            'maintenance' => 'Contrat de maintenance',
            'exclusivite' => 'Contrat d\'exclusivité',
            'cadre' => 'Contrat cadre',
            'autre' => 'Autre type de contrat'
        ];
        
        $modesPaiement = [
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'prelevement' => 'Prélèvement automatique',
            'espece' => 'Espèces',
            'autre' => 'Autre moyen de paiement'
        ];
        
        return view('fournisseurs.contrats.create', compact(
            'fournisseur', 
            'typesContrat',
            'modesPaiement'
        ));
    }

    /**
     * Enregistrer un nouveau contrat
     */
    public function store(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_contrat' => 'required|in:achat,prestation,maintenance,exclusivite,cadre,autre',
            'numero_contrat' => 'nullable|string|max:100|unique:contrat_fournisseurs,numero_contrat',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'renouvellement_auto' => 'boolean',
            'periode_renouvellement' => 'nullable|required_if:renouvellement_auto,1|in:mensuel,trimestriel,semestriel,annuel,biennal',
            'duree_preavis' => 'nullable|integer|min:0',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0|max:100',
            'mode_paiement' => 'nullable|string|max:50',
            'conditions_paiement' => 'nullable|string|max:255',
            'delai_paiement' => 'nullable|integer|min:0',
            'fichier_contrat' => 'nullable|file|mimes:pdf,doc,docx,odt|max:10240',
            'termes_speciaux' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);
        
        // Gestion du fichier du contrat
        if ($request->hasFile('fichier_contrat')) {
            $file = $request->file('fichier_contrat');
            $fileName = 'contrat-' . Str::slug($fournisseur->raison_sociale) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('contrats-fournisseurs', $fileName, 'public');
            
            $validated['fichier_contrat'] = $path;
            $validated['nom_fichier_original'] = $file->getClientOriginalName();
        }
        
        // Calcul du montant TTC
        $validated['montant_ttc'] = $validated['montant_ht'] * (1 + ($validated['tva'] / 100));
        
        // Statut par défaut
        $validated['statut'] = 'en_attente';
        
        // Création du contrat
        $contrat = $fournisseur->contrats()->create($validated);
        
        return redirect()
            ->route('fournisseurs.contrats.show', ['fournisseur' => $fournisseur->id, 'contrat' => $contrat->id])
            ->with('success', 'Le contrat a été créé avec succès.');
    }

    /**
     * Afficher les détails d'un contrat
     */
    public function show(Fournisseur $fournisseur, ContratFournisseur $contrat)
    {
        $contrat->load([
            'commandes' => function($query) {
                $query->orderBy('date_commande', 'desc');
            },
            'user',
            'responsable'
        ]);
        
        // Calcul des statistiques
        $stats = [
            'montant_engage' => $contrat->commandes->sum('montant_ttc'),
            'montant_restant' => max(0, $contrat->montant_ttc - $contrat->commandes->sum('montant_ttc')),
            'nombre_commandes' => $contrat->commandes->count(),
            'derniere_commande' => $contrat->commandes->first() ? $contrat->commandes->first()->created_at->format('d/m/Y') : null,
        ];
        
        return view('fournisseurs.contrats.show', compact('fournisseur', 'contrat', 'stats'));
    }

    /**
     * Afficher le formulaire de modification d'un contrat
     */
    public function edit(Fournisseur $fournisseur, ContratFournisseur $contrat)
    {
        $typesContrat = [
            'achat' => 'Contrat d\'achat',
            'prestation' => 'Contrat de prestation de service',
            'maintenance' => 'Contrat de maintenance',
            'exclusivite' => 'Contrat d\'exclusivité',
            'cadre' => 'Contrat cadre',
            'autre' => 'Autre type de contrat'
        ];
        
        $modesPaiement = [
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'prelevement' => 'Prélèvement automatique',
            'espece' => 'Espèces',
            'autre' => 'Autre moyen de paiement'
        ];
        
        $statuts = [
            'brouillon' => 'Brouillon',
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'resilie' => 'Résilié',
            'expire' => 'Expiré'
        ];
        
        return view('fournisseurs.contrats.edit', compact(
            'fournisseur', 
            'contrat',
            'typesContrat',
            'modesPaiement',
            'statuts'
        ));
    }

    /**
     * Mettre à jour un contrat
     */
    public function update(Request $request, Fournisseur $fournisseur, ContratFournisseur $contrat)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_contrat' => 'required|in:achat,prestation,maintenance,exclusivite,cadre,autre',
            'numero_contrat' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('contrat_fournisseurs', 'numero_contrat')->ignore($contrat->id)
            ],
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'renouvellement_auto' => 'boolean',
            'periode_renouvellement' => 'nullable|required_if:renouvellement_auto,1|in:mensuel,trimestriel,semestriel,annuel,biennal',
            'duree_preavis' => 'nullable|integer|min:0',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0|max:100',
            'mode_paiement' => 'nullable|string|max:50',
            'conditions_paiement' => 'nullable|string|max:255',
            'delai_paiement' => 'nullable|integer|min:0',
            'fichier_contrat' => 'nullable|file|mimes:pdf,doc,docx,odt|max:10240',
            'supprimer_fichier' => 'nullable|boolean',
            'termes_speciaux' => 'nullable|array',
            'statut' => 'required|in:brouillon,en_attente,en_cours,termine,resilie,expire',
            'date_signature' => 'nullable|date',
            'date_resiliation' => 'nullable|date|after_or_equal:date_debut',
            'motif_resiliation' => 'nullable|string|required_if:statut,resilie',
            'notes' => 'nullable|string',
        ]);
        
        // Gestion du fichier du contrat
        if ($request->boolean('supprimer_fichier') && $contrat->fichier_contrat) {
            Storage::disk('public')->delete($contrat->fichier_contrat);
            $validated['fichier_contrat'] = null;
            $validated['nom_fichier_original'] = null;
        } elseif ($request->hasFile('fichier_contrat')) {
            // Supprimer l'ancien fichier si existe
            if ($contrat->fichier_contrat) {
                Storage::disk('public')->delete($contrat->fichier_contrat);
            }
            
            $file = $request->file('fichier_contrat');
            $fileName = 'contrat-' . Str::slug($fournisseur->raison_sociale) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('contrats-fournisseurs', $fileName, 'public');
            
            $validated['fichier_contrat'] = $path;
            $validated['nom_fichier_original'] = $file->getClientOriginalName();
        }
        
        // Calcul du montant TTC
        $validated['montant_ttc'] = $validated['montant_ht'] * (1 + ($validated['tva'] / 100));
        
        // Mise à jour du contrat
        $contrat->update($validated);
        
        return redirect()
            ->route('fournisseurs.contrats.show', ['fournisseur' => $fournisseur->id, 'contrat' => $contrat->id])
            ->with('success', 'Le contrat a été mis à jour avec succès.');
    }

    /**
     * Supprimer un contrat
     */
    public function destroy(Fournisseur $fournisseur, ContratFournisseur $contrat)
    {
        // Vérifier s'il y a des commandes associées
        if ($contrat->commandes()->exists()) {
            return back()
                ->with('error', 'Impossible de supprimer ce contrat car il est associé à des commandes.');
        }
        
        // Supprimer le fichier si existe
        if ($contrat->fichier_contrat) {
            Storage::disk('public')->delete($contrat->fichier_contrat);
        }
        
        $contrat->delete();
        
        return redirect()
            ->route('fournisseurs.contrats.index', $fournisseur)
            ->with('success', 'Le contrat a été supprimé avec succès.');
    }

    /**
     * Télécharger le fichier du contrat
     */
    public function download(Fournisseur $fournisseur, ContratFournisseur $contrat)
    {
        if (!$contrat->fichier_contrat || !Storage::disk('public')->exists($contrat->fichier_contrat)) {
            return back()->with('error', 'Le fichier du contrat est introuvable.');
        }
        
        return Storage::disk('public')->download(
            $contrat->fichier_contrat,
            $contrat->nom_fichier_original ?? 'contrat-' . Str::slug($fournisseur->raison_sociale) . '.pdf'
        );
    }

    /**
     * Générer un PDF du contrat
     */
    public function pdf(Fournisseur $fournisseur, ContratFournisseur $contrat)
    {
        $contrat->load('responsable');
        
        $pdf = PDF::loadView('fournisseurs.contrats.pdf', [
            'fournisseur' => $fournisseur,
            'contrat' => $contrat
        ]);
        
        return $pdf->download('contrat-' . Str::slug($fournisseur->raison_sociale) . '-' . $contrat->id . '.pdf');
    }

    /**
     * Changer le statut d'un contrat
     */
    public function changerStatut(Request $request, Fournisseur $fournisseur, ContratFournisseur $contrat)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_cours,termine,resilie,expire',
            'date_effet' => 'required|date',
            'motif' => 'nullable|string|required_if:statut,resilie',
        ]);
        
        $statut = $request->input('statut');
        $dateEffet = $request->input('date_effet');
        
        $contrat->statut = $statut;
        
        // Mettre à jour les dates en fonction du statut
        if ($statut === 'en_cours' && !$contrat->date_signature) {
            $contrat->date_signature = $dateEffet;
        } elseif ($statut === 'resilie') {
            $contrat->date_resiliation = $dateEffet;
            $contrat->motif_resiliation = $request->input('motif');
        } elseif ($statut === 'termine' && !$contrat->date_fin) {
            $contrat->date_fin = $dateEffet;
        }
        
        $contrat->save();
        
        return back()->with('success', 'Le statut du contrat a été mis à jour avec succès.');
    }

    /**
     * Renouveler un contrat
     */
    public function renouveler(Fournisseur $fournisseur, ContratFournisseur $contrat)
    {
        // Vérifier que le contrat peut être renouvelé
        if (!in_array($contrat->statut, ['termine', 'en_cours', 'expire'])) {
            return back()->with('error', 'Ce contrat ne peut pas être renouvelé dans son état actuel.');
        }
        
        // Créer une copie du contrat avec les mêmes données
        $nouveauContrat = $contrat->replicate();
        $nouveauContrat->reference = null; // La référence sera générée automatiquement
        $nouveauContrat->statut = 'en_attente';
        $nouveauContrat->date_signature = null;
        $nouveauContrat->date_resiliation = null;
        $nouveauContrat->motif_resiliation = null;
        $nouveauContrat->created_at = now();
        $nouveauContrat->updated_at = now();
        
        // Mettre à jour les dates
        $dateDebut = now();
        $dateFin = null;
        
        if ($contrat->date_fin) {
            $duree = $contrat->date_debut->diffInDays($contrat->date_fin);
            $dateFin = $dateDebut->copy()->addDays($duree);
        }
        
        $nouveauContrat->date_debut = $dateDebut;
        $nouveauContrat->date_fin = $dateFin;
        
        // Enregistrer le nouveau contrat
        $nouveauContrat->save();
        
        // Mettre à jour l'ancien contrat
        $contrat->statut = 'termine';
        $contrat->save();
        
        return redirect()
            ->route('fournisseurs.contrats.show', ['fournisseur' => $fournisseur->id, 'contrat' => $nouveauContrat->id])
            ->with('success', 'Le contrat a été renouvelé avec succès.');
    }
}
