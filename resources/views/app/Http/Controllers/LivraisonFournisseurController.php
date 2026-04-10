<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\CommandeFournisseur;
use App\Models\LivraisonFournisseur;
use App\Models\LigneCommandeFournisseur;
use App\Models\LigneLivraisonFournisseur;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class LivraisonFournisseurController extends Controller
{
    /**
     * Afficher la liste des livraisons d'un fournisseur
     */
    public function index(Fournisseur $fournisseur, Request $request)
    {
        $query = $fournisseur->livraisons()
            ->with(['commande', 'lignes.article', 'user'])
            ->latest('date_livraison');
        
        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }
        
        if ($request->filled('commande_id')) {
            $query->where('commande_id', $request->input('commande_id'));
        }
        
        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('date_livraison', [
                $request->input('debut'),
                $request->input('fin')
            ]);
        }
        
        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where(function($q) use ($recherche) {
                $q->where('reference', 'like', "%{$recherche}%")
                  ->orWhere('bon_livraison', 'like', "%{$recherche}%");
            });
        }
        
        $livraisons = $query->paginate(15)->withQueryString();
        
        // Statistiques
        $stats = [
            'total' => $fournisseur->livraisons()->count(),
            'en_attente' => $fournisseur->livraisons()->where('statut', 'en_attente')->count(),
            'en_cours' => $fournisseur->livraisons()->where('statut', 'en_cours')->count(),
            'receptionnee' => $fournisseur->livraisons()->where('statut', 'receptionnee')->count(),
            'partielle' => $fournisseur->livraisons()->where('statut', 'partielle')->count(),
            'annulee' => $fournisseur->livraisons()->where('statut', 'annulee')->count(),
        ];
        
        return view('fournisseurs.livraisons.index', compact(
            'fournisseur', 
            'livraisons', 
            'stats'
        ));
    }

    /**
     * Afficher le formulaire de création d'une livraison
     */
    public function create(Fournisseur $fournisseur, Request $request)
    {
        $commande = null;
        $lignesCommande = [];
        
        // Si une commande est spécifiée, charger ses informations
        if ($request->filled('commande_id')) {
            $commande = $fournisseur->commandes()
                ->with(['lignes.article'])
                ->findOrFail($request->input('commande_id'));
            
            // Récupérer les lignes de commande non livrées ou partiellement livrées
            $lignesCommande = $commande->lignes()
                ->where(function($query) {
                    $query->where('statut', 'en_attente')
                          ->orWhere('statut', 'partiellement_livree');
                })
                ->get()
                ->map(function($ligne) {
                    // Calculer la quantité déjà livrée
                    $quantiteLivree = $ligne->livraisons->sum('pivot.quantite_livree');
                    $quantiteRestante = $ligne->quantite - $quantiteLivree;
                    
                    return [
                        'id' => $ligne->id,
                        'reference_article' => $ligne->reference_article,
                        'designation' => $ligne->designation,
                        'unite' => $ligne->unite,
                        'quantite_commandee' => $ligne->quantite,
                        'quantite_livree' => $quantiteLivree,
                        'quantite_restante' => $quantiteRestante,
                        'article' => $ligne->article,
                    ];
                });
        }
        
        // Liste des commandes pouvant être livrées
        $commandes = $fournisseur->commandes()
            ->whereIn('statut', ['validee', 'en_cours', 'partiellement_livree'])
            ->whereHas('lignes', function($query) {
                $query->where('statut', 'en_attente')
                      ->orWhere('statut', 'partiellement_livree');
            })
            ->orderBy('date_commande', 'desc')
            ->get(['id', 'reference', 'date_commande']);
        
        return view('fournisseurs.livraisons.create', compact(
            'fournisseur',
            'commande',
            'commandes',
            'lignesCommande'
        ));
    }

    /**
     * Enregistrer une nouvelle livraison
     */
    public function store(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:50|unique:livraison_fournisseurs,reference',
            'commande_id' => 'required|exists:commande_fournisseurs,id,fournisseur_id,' . $fournisseur->id,
            'bon_livraison' => 'nullable|string|max:100',
            'date_livraison' => 'required|date',
            'date_reception' => 'nullable|date|after_or_equal:date_livraison',
            'transporteur' => 'nullable|string|max:100',
            'numero_suivi' => 'nullable|string|max:100',
            'frais_transport' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.ligne_commande_id' => 'required|exists:ligne_commande_fournisseurs,id,commande_id,' . $request->input('commande_id'),
            'lignes.*.quantite_livree' => 'required|numeric|min:0.001',
            'lignes.*.lot_serie' => 'nullable|string|max:100',
            'lignes.*.date_peremption' => 'nullable|date',
            'lignes.*.commentaire' => 'nullable|string',
        ]);
        
        // Vérifier les quantités livrées
        $lignesCommande = LigneCommandeFournisseur::whereIn('id', collect($validated['lignes'])->pluck('ligne_commande_id'))
            ->with(['livraisons'])
            ->get()
            ->keyBy('id');
        
        foreach ($validated['lignes'] as $ligne) {
            $ligneCommande = $lignesCommande->get($ligne['ligne_commande_id']);
            
            if (!$ligneCommande) {
                return back()
                    ->withInput()
                    ->with('error', 'Une erreur est survenue lors de la validation des quantités.');
            }
            
            $quantiteDejaLivree = $ligneCommande->livraisons->sum('pivot.quantite_livree');
            $quantiteRestante = $ligneCommande->quantite - $quantiteDejaLivree;
            
            if ($ligne['quantite_livree'] > $quantiteRestante) {
                return back()
                    ->withInput()
                    ->with('error', 'La quantité livrée pour "' . $ligneCommande->designation . '" dépasse la quantité restante (' . $quantiteRestante . ').');
            }
        }
        
        // Calculer le statut de la livraison
        $statut = 'en_attente';
        $dateReception = $validated['date_reception'] ?? null;
        
        if ($dateReception) {
            $statut = 'receptionnee';
        }
        
        // Création de la livraison
        $livraison = new LivraisonFournisseur([
            'reference' => $validated['reference'] ?? 'LIV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'fournisseur_id' => $fournisseur->id,
            'commande_id' => $validated['commande_id'],
            'bon_livraison' => $validated['bon_livraison'] ?? null,
            'date_livraison' => $validated['date_livraison'],
            'date_reception' => $dateReception,
            'transporteur' => $validated['transporteur'] ?? null,
            'numero_suivi' => $validated['numero_suivi'] ?? null,
            'frais_transport' => $validated['frais_transport'] ?? 0,
            'statut' => $statut,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);
        
        // Enregistrement dans une transaction
        DB::beginTransaction();
        
        try {
            $livraison->save();
            
            // Enregistrement des lignes de livraison
            $lignesLivraison = [];
            
            foreach ($validated['lignes'] as $ligne) {
                $ligneCommande = $lignesCommande->get($ligne['ligne_commande_id']);
                
                $lignesLivraison[$ligneCommande->id] = [
                    'quantite_livree' => $ligne['quantite_livree'],
                    'lot_serie' => $ligne['lot_serie'] ?? null,
                    'date_peremption' => $ligne['date_peremption'] ?? null,
                    'commentaire' => $ligne['commentaire'] ?? null,
                ];
                
                // Mise à jour du statut de la ligne de commande
                $quantiteTotaleLivree = $ligneCommande->livraisons->sum('pivot.quantite_livree') + $ligne['quantite_livree'];
                
                if ($quantiteTotaleLivree >= $ligneCommande->quantite) {
                    $ligneCommande->statut = 'livree';
                } else if ($quantiteTotaleLivree > 0) {
                    $ligneCommande->statut = 'partiellement_livree';
                }
                
                $ligneCommande->save();
            }
            
            // Attacher les lignes de livraison
            $livraison->lignes()->attach($lignesLivraison);
            
            // Mise à jour du statut de la commande
            $commande = $livraison->commande;
            $lignesCommande = $commande->lignes;
            
            $toutesLivrees = $lignesCommande->every(function($ligne) {
                return $ligne->statut === 'livree';
            });
            
            $aucuneLivree = $lignesCommande->every(function($ligne) {
                return $ligne->statut === 'en_attente';
            });
            
            if ($toutesLivrees) {
                $commande->statut = 'livree';
                $commande->date_livraison_reelle = $livraison->date_reception ?? now();
            } else if ($aucuneLivree) {
                $commande->statut = 'en_attente';
            } else {
                $commande->statut = 'partiellement_livree';
                
                if ($livraison->date_reception) {
                    $commande->date_livraison_reelle = $livraison->date_reception;
                }
            }
            
            $commande->save();
            
            // Mise à jour du fournisseur
            $fournisseur->increment('nombre_livraisons');
            $fournisseur->date_derniere_livraison = now();
            $fournisseur->save();
            
            DB::commit();
            
            return redirect()
                ->route('fournisseurs.livraisons.show', ['fournisseur' => $fournisseur->id, 'livraison' => $livraison->id])
                ->with('success', 'La livraison a été enregistrée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de la livraison : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une livraison
     */
    public function show(Fournisseur $fournisseur, LivraisonFournisseur $livraison)
    {
        // Vérifier que la livraison appartient bien au fournisseur
        if ($livraison->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $livraison->load([
            'lignes.article',
            'lignes.ligneCommande',
            'commande',
            'user',
            'receptionnaire'
        ]);
        
        // Récupérer les autres livraisons de la même commande
        $autresLivraisons = $fournisseur->livraisons()
            ->where('commande_id', $livraison->commande_id)
            ->where('id', '!=', $livraison->id)
            ->orderBy('date_livraison', 'desc')
            ->get();
        
        return view('fournisseurs.livraisons.show', compact(
            'fournisseur',
            'livraison',
            'autresLivraisons'
        ));
    }

    /**
     * Marquer une livraison comme réceptionnée
     */
    public function recevoir(Request $request, Fournisseur $fournisseur, LivraisonFournisseur $livraison)
    {
        // Vérifier que la livraison appartient bien au fournisseur
        if ($livraison->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        // Vérifier que la livraison n'est pas déjà réceptionnée
        if ($livraison->est_receptionnee) {
            return back()->with('warning', 'Cette livraison a déjà été réceptionnée.');
        }
        
        $validated = $request->validate([
            'date_reception' => 'required|date|after_or_equal:' . $livraison->date_livraison,
            'notes' => 'nullable|string',
            'receptionnaire_id' => 'required|exists:users,id',
        ]);
        
        // Mise à jour de la livraison
        $livraison->date_reception = $validated['date_reception'];
        $livraison->statut = 'receptionnee';
        $livraison->notes = $validated['notes'] ?? $livraison->notes;
        $livraison->receptionnaire_id = $validated['receptionnaire_id'];
        
        // Enregistrement dans une transaction
        DB::beginTransaction();
        
        try {
            $livraison->save();
            
            // Mise à jour du statut de la commande si nécessaire
            $commande = $livraison->commande;
            
            if ($commande->statut !== 'livree') {
                $toutesLivraisonsRecues = true;
                
                foreach ($commande->livraisons as $liv) {
                    if (!$liv->est_receptionnee) {
                        $toutesLivraisonsRecues = false;
                        break;
                    }
                }
                
                if ($toutesLivraisonsRecues) {
                    $commande->statut = 'livree';
                    $commande->date_livraison_reelle = $livraison->date_reception;
                    $commande->save();
                }
            }
            
            DB::commit();
            
            return back()->with('success', 'La livraison a été marquée comme réceptionnée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la livraison : ' . $e->getMessage());
        }
    }

    /**
     * Annuler une livraison
     */
    public function annuler(Request $request, Fournisseur $fournisseur, LivraisonFournisseur $livraison)
    {
        // Vérifier que la livraison appartient bien au fournisseur
        if ($livraison->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        // Vérifier que la livraison n'est pas déjà annulée
        if ($livraison->est_annulee) {
            return back()->with('warning', 'Cette livraison a déjà été annulée.');
        }
        
        $validated = $request->validate([
            'motif_annulation' => 'required|string|max:255',
        ]);
        
        // Mise à jour de la livraison
        $livraison->statut = 'annulee';
        $livraison->motif_annulation = $validated['motif_annulation'];
        
        // Enregistrement dans une transaction
        DB::beginTransaction();
        
        try {
            $livraison->save();
            
            // Mise à jour des quantités livrées dans les lignes de commande
            foreach ($livraison->lignes as $ligneLivraison) {
                $ligneCommande = $ligneLivraison->ligneCommande;
                
                // Si la ligne de commande était marquée comme livrée, la repasser en attente
                if ($ligneCommande->est_livree) {
                    $ligneCommande->statut = 'partiellement_livree';
                    $ligneCommande->save();
                }
            }
            
            // Mise à jour du statut de la commande
            $commande = $livraison->commande;
            $lignesCommande = $commande->lignes;
            
            $toutesLivrees = $lignesCommande->every(function($ligne) {
                return $ligne->statut === 'livree';
            });
            
            $aucuneLivree = $lignesCommande->every(function($ligne) {
                return $ligne->statut === 'en_attente';
            });
            
            if ($toutesLivrees) {
                $commande->statut = 'livree';
            } else if ($aucuneLivree) {
                $commande->statut = 'en_attente';
            } else {
                $commande->statut = 'partiellement_livree';
            }
            
            $commande->save();
            
            DB::commit();
            
            return back()->with('success', 'La livraison a été annulée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'annulation de la livraison : ' . $e->getMessage());
        }
    }

    /**
     * Générer un bon de livraison au format PDF
     */
    public function bonLivraisonPdf(Fournisseur $fournisseur, LivraisonFournisseur $livraison)
    {
        // Vérifier que la livraison appartient bien au fournisseur
        if ($livraison->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $livraison->load([
            'lignes.article',
            'lignes.ligneCommande',
            'commande',
            'user'
        ]);
        
        $pdf = PDF::loadView('fournisseurs.livraisons.bon-livraison-pdf', [
            'fournisseur' => $fournisseur,
            'livraison' => $livraison,
        ]);
        
        return $pdf->download('bon-livraison-' . $livraison->reference . '.pdf');
    }

    /**
     * Générer un bordereau de livraison au format PDF
     */
    public function bordereauPdf(Fournisseur $fournisseur, LivraisonFournisseur $livraison)
    {
        // Vérifier que la livraison appartient bien au fournisseur
        if ($livraison->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $livraison->load([
            'lignes.article',
            'lignes.ligneCommande',
            'commande',
            'user'
        ]);
        
        $pdf = PDF::loadView('fournisseurs.livraisons.bordereau-pdf', [
            'fournisseur' => $fournisseur,
            'livraison' => $livraison,
        ]);
        
        return $pdf->download('bordereau-livraison-' . $livraison->reference . '.pdf');
    }

    /**
     * Générer un état des livraisons au format PDF
     */
    public function etatLivraisonsPdf(Fournisseur $fournisseur, Request $request)
    {
        $query = $fournisseur->livraisons()
            ->with(['commande', 'lignes.article'])
            ->latest('date_livraison');
        
        // Filtres
        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('date_livraison', [
                $request->input('debut'),
                $request->input('fin')
            ]);
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }
        
        $livraisons = $query->get();
        
        $pdf = PDF::loadView('fournisseurs.livraisons.etat-livraisons-pdf', [
            'fournisseur' => $fournisseur,
            'livraisons' => $livraisons,
            'debut' => $request->input('debut'),
            'fin' => $request->input('fin'),
        ]);
        
        return $pdf->download('etat-livraisons-fournisseur-' . $fournisseur->id . '.pdf');
    }
}
