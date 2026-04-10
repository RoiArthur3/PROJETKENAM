<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\CommandeFournisseur;
use App\Models\ContratFournisseur;
use App\Models\LigneCommandeFournisseur;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PDF;

class CommandeFournisseurController extends Controller
{
    /**
     * Afficher la liste globale des commandes fournisseurs
     */
    public function indexGlobal()
    {
        try {
            $commandes = CommandeFournisseur::with('fournisseur')
                ->orderBy('date_commande', 'desc')
                ->paginate(15);
            $fournisseurs = \App\Models\Fournisseur::orderBy('raison_sociale')->get();

            return view('fournisseurs.commandes', compact('commandes', 'fournisseurs'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une vue avec des données vides
            $commandes = collect();
            $fournisseurs = collect();

            return view('fournisseurs.commandes', compact('commandes', 'fournisseurs'))
                ->with('error', 'Une erreur est survenue lors du chargement des commandes: ' . $e->getMessage());
        }
    }

    /**
     * Afficher la liste des commandes pour un fournisseur spécifique
     */
    public function index(Fournisseur $fournisseur, Request $request)
    {
        $query = $fournisseur->commandes()
            ->with(['lignes', 'contrat'])
            ->withCount('lignes')
            ->latest('date_commande');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('date_commande', [
                $request->input('debut'),
                $request->input('fin')
            ]);
        } elseif ($request->filled('debut')) {
            $query->where('date_commande', '>=', $request->input('debut'));
        } elseif ($request->filled('fin')) {
            $query->where('date_commande', '<=', $request->input('fin'));
        }

        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where(function($q) use ($recherche) {
                $q->where('reference', 'like', "%{$recherche}%")
                  ->orWhere('designation', 'like', "%{$recherche}%");
            });
        }

        $commandes = $query->paginate(15)->withQueryString();

        return view('fournisseurs.commandes.index', compact('fournisseur', 'commandes'));
    }

    /**
     * Afficher le formulaire de création d'une commande
     */
    public function create(Fournisseur $fournisseur)
    {
        $contrats = $fournisseur->contrats()
            ->where('statut', 'en_cours')
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->get();

        $articlesRecents = Product::whereIn('id', function($query) use ($fournisseur) {
                $query->select('article_id')
                    ->from('ligne_commande_fournisseurs')
                    ->join('commande_fournisseurs', 'ligne_commande_fournisseurs.commande_id', '=', 'commande_fournisseurs.id')
                    ->where('commande_fournisseurs.fournisseur_id', $fournisseur->id)
                    ->whereNotNull('article_id')
                    ->groupBy('article_id')
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(10);
            })
            ->get();

        return view('fournisseurs.commandes.create', compact('fournisseur', 'contrats', 'articlesRecents'));
    }

    /**
     * Enregistrer une nouvelle commande
     */
    public function store(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:50|unique:commande_fournisseurs,reference',
            'contrat_id' => 'nullable|exists:contrat_fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'adresse_livraison' => 'nullable|string|max:255',
            'code_postal_livraison' => 'nullable|string|max:10',
            'ville_livraison' => 'nullable|string|max:100',
            'pays_livraison' => 'nullable|string|max:100',
            'mode_paiement' => 'nullable|string|max:50',
            'conditions_paiement' => 'nullable|string|max:255',
            'delai_paiement' => 'nullable|integer|min:0',
            'frais_livraison' => 'nullable|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
            'type_remise' => 'required_with:remise|in:pourcentage,montant',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.article_id' => 'nullable|exists:articles,id',
            'lignes.*.reference_article' => 'nullable|string|max:100',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.description' => 'nullable|string',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'required|string|max:20',
            'lignes.*.prix_unitaire_ht' => 'required|numeric|min:0',
            'lignes.*.tva_taux' => 'required|numeric|min:0|max:100',
            'lignes.*.remise' => 'nullable|numeric|min:0|max:100',
            'lignes.*.date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'lignes.*.notes' => 'nullable|string',
        ]);

        // Calcul des totaux
        $totaux = $this->calculerTotaux($validated['lignes'], $validated['frais_livraison'] ?? 0, $validated['remise'] ?? 0, $validated['type_remise'] ?? 'pourcentage');

        // Création de la commande
        $commande = new CommandeFournisseur([
            'reference' => $validated['reference'] ?? 'CMD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'fournisseur_id' => $fournisseur->id,
            'contrat_id' => $validated['contrat_id'] ?? null,
            'date_commande' => $validated['date_commande'],
            'date_livraison_prevue' => $validated['date_livraison_prevue'] ?? null,
            'adresse_livraison' => $validated['adresse_livraison'] ?? $fournisseur->adresse,
            'code_postal_livraison' => $validated['code_postal_livraison'] ?? $fournisseur->code_postal,
            'ville_livraison' => $validated['ville_livraison'] ?? $fournisseur->ville,
            'pays_livraison' => $validated['pays_livraison'] ?? $fournisseur->pays,
            'mode_paiement' => $validated['mode_paiement'] ?? null,
            'conditions_paiement' => $validated['conditions_paiement'] ?? null,
            'delai_paiement' => $validated['delai_paiement'] ?? null,
            'frais_livraison' => $validated['frais_livraison'] ?? 0,
            'remise' => $validated['remise'] ?? 0,
            'type_remise' => $validated['type_remise'] ?? 'pourcentage',
            'montant_ht' => $totaux['montant_ht'],
            'tva' => $totaux['tva'],
            'montant_ttc' => $totaux['montant_ttc'],
            'statut' => 'brouillon',
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        // Enregistrement dans une transaction
        DB::beginTransaction();

        try {
            $commande->save();

            // Création des lignes de commande
            foreach ($validated['lignes'] as $ligne) {
                $ligneCommande = new LigneCommandeFournisseur([
                    'commande_id' => $commande->id,
                    'article_id' => $ligne['article_id'] ?? null,
                    'reference_article' => $ligne['reference_article'] ?? null,
                    'designation' => $ligne['designation'],
                    'description' => $ligne['description'] ?? null,
                    'quantite' => $ligne['quantite'],
                    'unite' => $ligne['unite'],
                    'prix_unitaire_ht' => $ligne['prix_unitaire_ht'],
                    'tva_taux' => $ligne['tva_taux'],
                    'remise' => $ligne['remise'] ?? 0,
                    'montant_ht' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 - ($ligne['remise'] ?? 0) / 100),
                    'montant_tva' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * ($ligne['tva_taux'] / 100) * (1 - ($ligne['remise'] ?? 0) / 100),
                    'montant_ttc' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 + ($ligne['tva_taux'] / 100)) * (1 - ($ligne['remise'] ?? 0) / 100),
                    'date_livraison_prevue' => $ligne['date_livraison_prevue'] ?? $validated['date_livraison_prevue'] ?? null,
                    'statut' => 'en_attente',
                    'notes' => $ligne['notes'] ?? null,
                ]);

                $ligneCommande->save();
            }

            // Mise à jour du fournisseur
            $fournisseur->increment('nombre_commandes');
            $fournisseur->date_derniere_commande = now();
            $fournisseur->save();

            // Mise à jour du contrat si spécifié
            if ($commande->contrat_id) {
                $contrat = $commande->contrat;
                $contrat->montant_consomme = $contrat->commandes()->sum('montant_ttc');
                $contrat->montant_restant = max(0, $contrat->montant_ttc - $contrat->montant_consomme);
                $contrat->save();
            }

            DB::commit();

            return redirect()
                ->route('fournisseurs.commandes.show', ['fournisseur' => $fournisseur->id, 'commande' => $commande->id])
                ->with('success', 'La commande a été créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la commande : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une commande
     */
    public function show(Fournisseur $fournisseur, CommandeFournisseur $commande)
    {
        $commande->load([
            'lignes',
            'contrat',
            'livraisons' => function($query) {
                $query->with('lignes');
            },
            'factures',
            'user',
            'responsable'
        ]);

        // Calcul des statistiques
        $stats = [
            'quantite_commandee' => $commande->lignes->sum('quantite'),
            'quantite_livree' => $commande->livraisons->sum(function($livraison) {
                return $livraison->lignes->sum('quantite_livree');
            }),
            'quantite_restante' => $commande->lignes->sum('quantite') - $commande->livraisons->sum(function($livraison) {
                return $livraison->lignes->sum('quantite_livree');
            }),
            'montant_paye' => $commande->factures->sum('montant_paye'),
            'reste_a_payer' => $commande->montant_ttc - $commande->factures->sum('montant_paye'),
        ];

        return view('fournisseurs.commandes.show', compact('fournisseur', 'commande', 'stats'));
    }

    /**
     * Afficher le formulaire de modification d'une commande
     */
    public function edit(Fournisseur $fournisseur, CommandeFournisseur $commande)
    {
        if (!in_array($commande->statut, ['brouillon', 'en_attente'])) {
            return back()->with('error', 'Seules les commandes en statut "Brouillon" ou "En attente" peuvent être modifiées.');
        }

        $commande->load('lignes');

        // Récupérer les contrats actifs du fournisseur
        $contrats = $fournisseur->contrats()
            ->where('statut', 'en_cours')
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->get();

        // Récupérer les articles fréquemment commandés
        $articlesRecents = Product::whereIn('id', function($query) use ($fournisseur) {
                $query->select('article_id')
                    ->from('ligne_commande_fournisseurs')
                    ->join('commande_fournisseurs', 'ligne_commande_fournisseurs.commande_id', '=', 'commande_fournisseurs.id')
                    ->where('commande_fournisseurs.fournisseur_id', $fournisseur->id)
                    ->whereNotNull('article_id')
                    ->groupBy('article_id')
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(10);
            })
            ->get();

        return view('fournisseurs.commandes.edit', compact('fournisseur', 'commande', 'contrats', 'articlesRecents'));
    }

    /**
     * Mettre à jour une commande
     */
    public function update(Request $request, Fournisseur $fournisseur, CommandeFournisseur $commande)
    {
        if (!in_array($commande->statut, ['brouillon', 'en_attente'])) {
            return back()->with('error', 'Seules les commandes en statut "Brouillon" ou "En attente" peuvent être modifiées.');
        }

        $validated = $request->validate([
            'reference' => [
                'required',
                'string',
                'max:50',
                Rule::unique('commande_fournisseurs', 'reference')->ignore($commande->id)
            ],
            'contrat_id' => 'nullable|exists:contrat_fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'adresse_livraison' => 'nullable|string|max:255',
            'code_postal_livraison' => 'nullable|string|max:10',
            'ville_livraison' => 'nullable|string|max:100',
            'pays_livraison' => 'nullable|string|max:100',
            'mode_paiement' => 'nullable|string|max:50',
            'conditions_paiement' => 'nullable|string|max:255',
            'delai_paiement' => 'nullable|integer|min:0',
            'frais_livraison' => 'nullable|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
            'type_remise' => 'required_with:remise|in:pourcentage,montant',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.id' => 'nullable|exists:ligne_commande_fournisseurs,id,commande_id,' . $commande->id,
            'lignes.*.article_id' => 'nullable|exists:articles,id',
            'lignes.*.reference_article' => 'nullable|string|max:100',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.description' => 'nullable|string',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'required|string|max:20',
            'lignes.*.prix_unitaire_ht' => 'required|numeric|min:0',
            'lignes.*.tva_taux' => 'required|numeric|min:0|max:100',
            'lignes.*.remise' => 'nullable|numeric|min:0|max:100',
            'lignes.*.date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'lignes.*.notes' => 'nullable|string',
        ]);

        // Calcul des totaux
        $totaux = $this->calculerTotaux($validated['lignes'], $validated['frais_livraison'] ?? 0, $validated['remise'] ?? 0, $validated['type_remise'] ?? 'pourcentage');

        // Mise à jour de la commande
        $commande->fill([
            'reference' => $validated['reference'],
            'contrat_id' => $validated['contrat_id'] ?? null,
            'date_commande' => $validated['date_commande'],
            'date_livraison_prevue' => $validated['date_livraison_prevue'] ?? null,
            'adresse_livraison' => $validated['adresse_livraison'] ?? $fournisseur->adresse,
            'code_postal_livraison' => $validated['code_postal_livraison'] ?? $fournisseur->code_postal,
            'ville_livraison' => $validated['ville_livraison'] ?? $fournisseur->ville,
            'pays_livraison' => $validated['pays_livraison'] ?? $fournisseur->pays,
            'mode_paiement' => $validated['mode_paiement'] ?? null,
            'conditions_paiement' => $validated['conditions_paiement'] ?? null,
            'delai_paiement' => $validated['delai_paiement'] ?? null,
            'frais_livraison' => $validated['frais_livraison'] ?? 0,
            'remise' => $validated['remise'] ?? 0,
            'type_remise' => $validated['type_remise'] ?? 'pourcentage',
            'montant_ht' => $totaux['montant_ht'],
            'tva' => $totaux['tva'],
            'montant_ttc' => $totaux['montant_ttc'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Enregistrement dans une transaction
        DB::beginTransaction();

        try {
            $commande->save();

            // Récupérer les IDs des lignes existantes
            $lignesExistantes = $commande->lignes->pluck('id')->toArray();
            $lignesMisesAJour = [];

            // Mise à jour ou création des lignes de commande
            foreach ($validated['lignes'] as $ligne) {
                $ligneData = [
                    'article_id' => $ligne['article_id'] ?? null,
                    'reference_article' => $ligne['reference_article'] ?? null,
                    'designation' => $ligne['designation'],
                    'description' => $ligne['description'] ?? null,
                    'quantite' => $ligne['quantite'],
                    'unite' => $ligne['unite'],
                    'prix_unitaire_ht' => $ligne['prix_unitaire_ht'],
                    'tva_taux' => $ligne['tva_taux'],
                    'remise' => $ligne['remise'] ?? 0,
                    'montant_ht' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 - ($ligne['remise'] ?? 0) / 100),
                    'montant_tva' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * ($ligne['tva_taux'] / 100) * (1 - ($ligne['remise'] ?? 0) / 100),
                    'montant_ttc' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 + ($ligne['tva_taux'] / 100)) * (1 - ($ligne['remise'] ?? 0) / 100),
                    'date_livraison_prevue' => $ligne['date_livraison_prevue'] ?? $validated['date_livraison_prevue'] ?? null,
                    'notes' => $ligne['notes'] ?? null,
                ];

                if (isset($ligne['id'])) {
                    // Mise à jour d'une ligne existante
                    $ligneCommande = LigneCommandeFournisseur::findOrFail($ligne['id']);
                    $ligneCommande->update($ligneData);
                    $lignesMisesAJour[] = $ligne['id'];
                } else {
                    // Création d'une nouvelle ligne
                    $ligneCommande = new LigneCommandeFournisseur($ligneData);
                    $commande->lignes()->save($ligneCommande);
                    $lignesMisesAJour[] = $ligneCommande->id;
                }
            }

            // Suppression des lignes qui ne sont plus présentes
            $lignesASupprimer = array_diff($lignesExistantes, $lignesMisesAJour);
            if (!empty($lignesASupprimer)) {
                LigneCommandeFournisseur::whereIn('id', $lignesASupprimer)->delete();
            }

            // Mise à jour du contrat si nécessaire
            if ($commande->isDirty('contrat_id') || $commande->isDirty('montant_ttc')) {
                // Mettre à jour l'ancien contrat si nécessaire
                if ($commande->isDirty('contrat_id')) {
                    $ancienContrat = ContratFournisseur::find($commande->getOriginal('contrat_id'));
                    if ($ancienContrat) {
                        $ancienContrat->montant_consomme = $ancienContrat->commandes()
                            ->where('id', '!=', $commande->id)
                            ->sum('montant_ttc');
                        $ancienContrat->montant_restant = max(0, $ancienContrat->montant_ttc - $ancienContrat->montant_consomme);
                        $ancienContrat->save();
                    }
                }

                // Mettre à jour le nouveau contrat
                if ($commande->contrat_id) {
                    $nouveauContrat = $commande->contrat;
                    $nouveauContrat->montant_consomme = $nouveauContrat->commandes->sum('montant_ttc');
                    $nouveauContrat->montant_restant = max(0, $nouveauContrat->montant_ttc - $nouveauContrat->montant_consomme);
                    $nouveauContrat->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('fournisseurs.commandes.show', ['fournisseur' => $fournisseur->id, 'commande' => $commande->id])
                ->with('success', 'La commande a été mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la commande : ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut d'une commande
     */
    public function changerStatut(Request $request, Fournisseur $fournisseur, CommandeFournisseur $commande)
    {
        $request->validate([
            'statut' => 'required|in:validee,en_cours,livree,partiellement_livree,annulee,refusee',
            'date_effet' => 'required|date',
            'motif' => 'nullable|string|required_if:statut,annulee,refusee',
        ]);

        $ancienStatut = $commande->statut;
        $nouveauStatut = $request->input('statut');
        $dateEffet = $request->input('date_effet');

        // Vérifier les transitions autorisées
        $transitionsAutorisees = [
            'brouillon' => ['validee', 'annulee'],
            'en_attente' => ['validee', 'annulee', 'refusee'],
            'validee' => ['en_cours', 'annulee'],
            'en_cours' => ['livree', 'partiellement_livree', 'annulee'],
            'partiellement_livree' => ['livree', 'en_cours', 'annulee'],
        ];

        if (!in_array($nouveauStatut, $transitionsAutorisees[$ancienStatut] ?? [])) {
            return back()->with('error', 'Transition de statut non autorisée.');
        }

        // Mise à jour du statut
        $commande->statut = $nouveauStatut;

        // Mise à jour des dates en fonction du statut
        if ($nouveauStatut === 'validee' && !$commande->date_validation) {
            $commande->date_validation = $dateEffet;
        } elseif ($nouveauStatut === 'en_cours' && !$commande->date_debut_traitement) {
            $commande->date_debut_traitement = $dateEffet;
        } elseif (in_array($nouveauStatut, ['livree', 'partiellement_livree']) && !$commande->date_livraison_reelle) {
            $commande->date_livraison_reelle = $dateEffet;
        } elseif (in_array($nouveauStatut, ['annulee', 'refusee'])) {
            $commande->motif_annulation = $request->input('motif');
        }

        $commande->save();

        // Si la commande est annulée, annuler également les lignes non livrées
        if (in_array($nouveauStatut, ['annulee', 'refusee'])) {
            $commande->lignes()
                ->whereNotIn('statut', ['livree', 'partiellement_livree'])
                ->update(['statut' => 'annulee']);
        }

        return back()->with('success', 'Le statut de la commande a été mis à jour avec succès.');
    }

    /**
     * Générer un PDF de la commande
     */
    public function pdf(Fournisseur $fournisseur, CommandeFournisseur $commande)
    {
        $commande->load(['lignes', 'fournisseur', 'contrat']);

        $pdf = PDF::loadView('fournisseurs.commandes.pdf', [
            'commande' => $commande,
            'fournisseur' => $fournisseur,
        ]);

        return $pdf->download('commande-' . $commande->reference . '.pdf');
    }

    /**
     * Calculer les totaux d'une commande
     */
    private function calculerTotaux($lignes, $fraisLivraison = 0, $remise = 0, $typeRemise = 'pourcentage')
    {
        $montantHT = 0;
        $tva = 0;

        foreach ($lignes as $ligne) {
            $montantLigneHT = $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 - ($ligne['remise'] ?? 0) / 100);
            $montantLigneTVA = $montantLigneHT * ($ligne['tva_taux'] / 100);

            $montantHT += $montantLigneHT;
            $tva += $montantLigneTVA;
        }

        // Ajout des frais de livraison
        $montantHT += $fraisLivraison;

        // Application de la remise globale
        if ($remise > 0) {
            if ($typeRemise === 'pourcentage') {
                $montantRemise = $montantHT * ($remise / 100);
            } else {
                $montantRemise = min($remise, $montantHT);
            }

            $montantHT -= $montantRemise;
        }

        $montantTTC = $montantHT + $tva;

        return [
            'montant_ht' => round($montantHT, 2),
            'tva' => round($tva, 2),
            'montant_ttc' => round($montantTTC, 2),
        ];
    }

    /**
     * Afficher le formulaire de création d'une commande globale (choisir fournisseur)
     */
    public function createGlobal()
    {
        $fournisseurs = \App\Models\Fournisseur::orderBy('raison_sociale')->get();
        $articles = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('produits')) {
                $articles = \App\Models\Produit::where('actif', true)->orderBy('designation')->get();
            }
        } catch (\Throwable $e) {
            $articles = collect();
        }

        return view('fournisseurs.commandes.create', compact('fournisseurs', 'articles'));
    }

    /**
     * Enregistrer une nouvelle commande globale
     */
    public function storeGlobal(Request $request)
    {
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'reference' => 'nullable|string|max:50|unique:commande_fournisseurs,reference',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'mode_paiement' => 'nullable|string|max:50',
            'conditions_paiement' => 'nullable|string|max:255',
            'frais_livraison' => 'nullable|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
            'type_remise' => 'required_with:remise|in:pourcentage,montant',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.article_id' => 'nullable|exists:produits,id',
            'lignes.*.reference_article' => 'nullable|string|max:100',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.description' => 'nullable|string',
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.unite' => 'required|string|max:20',
            'lignes.*.prix_unitaire_ht' => 'required|numeric|min:0',
            'lignes.*.tva_taux' => 'required|numeric|min:0|max:100',
            'lignes.*.remise' => 'nullable|numeric|min:0|max:100',
            'lignes.*.notes' => 'nullable|string',
        ]);

        $fournisseur = \App\Models\Fournisseur::findOrFail($validated['fournisseur_id']);

        // Calcul des totaux
        $totaux = $this->calculerTotaux($validated['lignes'], $validated['frais_livraison'] ?? 0, $validated['remise'] ?? 0, $validated['type_remise'] ?? 'pourcentage');

        // Création de la commande
        $commande = new CommandeFournisseur([
            'reference' => $validated['reference'] ?? 'CMD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'fournisseur_id' => $fournisseur->id,
            'date_commande' => $validated['date_commande'],
            'date_livraison_prevue' => $validated['date_livraison_prevue'] ?? null,
            'adresse_livraison' => $fournisseur->adresse,
            'code_postal_livraison' => $fournisseur->code_postal,
            'ville_livraison' => $fournisseur->ville,
            'pays_livraison' => $fournisseur->pays,
            'mode_paiement' => $validated['mode_paiement'] ?? null,
            'conditions_paiement' => $validated['conditions_paiement'] ?? null,
            'frais_livraison' => $validated['frais_livraison'] ?? 0,
            'remise' => $validated['remise'] ?? 0,
            'type_remise' => $validated['type_remise'] ?? 'pourcentage',
            'montant_ht' => $totaux['montant_ht'],
            'tva' => $totaux['tva'],
            'montant_ttc' => $totaux['montant_ttc'],
            'statut' => 'brouillon',
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        // Enregistrement dans une transaction
        DB::beginTransaction();

        try {
            $commande->save();

            // Création des lignes de commande
            foreach ($validated['lignes'] as $ligne) {
                $ligneCommande = new LigneCommandeFournisseur([
                    'commande_id' => $commande->id,
                    'article_id' => $ligne['article_id'] ?? null,
                    'reference_article' => $ligne['reference_article'] ?? null,
                    'designation' => $ligne['designation'],
                    'description' => $ligne['description'] ?? null,
                    'quantite' => $ligne['quantite'],
                    'unite' => $ligne['unite'],
                    'prix_unitaire_ht' => $ligne['prix_unitaire_ht'],
                    'tva_taux' => $ligne['tva_taux'],
                    'remise' => $ligne['remise'] ?? 0,
                    'montant_ht' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 - ($ligne['remise'] ?? 0) / 100),
                    'montant_tva' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * ($ligne['tva_taux'] / 100) * (1 - ($ligne['remise'] ?? 0) / 100),
                    'montant_ttc' => $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 + ($ligne['tva_taux'] / 100)) * (1 - ($ligne['remise'] ?? 0) / 100),
                    'date_livraison_prevue' => $validated['date_livraison_prevue'] ?? null,
                    'statut' => 'en_attente',
                    'notes' => $ligne['notes'] ?? null,
                ]);

                $ligneCommande->save();
            }

            // Mise à jour du fournisseur
            $fournisseur->increment('nombre_commandes');
            $fournisseur->date_derniere_commande = now();
            $fournisseur->save();

            DB::commit();

            return redirect()
                ->route('fournisseurs.commandes.show', $commande->id)
                ->with('success', 'La commande a été créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la commande : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une commande globale
     */
    public function showGlobal(CommandeFournisseur $commande)
    {
        $commande->load([
            'fournisseur',
            'lignes',
            'user'
        ]);

        return view('fournisseurs.commandes.show', compact('commande'));
    }
}
