<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\CommandeFournisseur;
use App\Models\FactureFournisseur;
use App\Models\LigneFactureFournisseur;
use App\Models\PaiementFournisseur;
use App\Models\Caisse;
use App\Models\DepenseCaisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class FactureFournisseurController extends Controller
{
    /**
     * Afficher la liste des factures d'un fournisseur
     */
    public function index(Request $request, $fournisseurId = null)
    {
        // Si un ID de fournisseur est fourni, on charge le fournisseur
        if ($fournisseurId) {
            $fournisseur = Fournisseur::findOrFail($fournisseurId);
            $query = $fournisseur->factures();
        } else {
            // Sinon, on charge toutes les factures
            $query = FactureFournisseur::query();
            $fournisseur = null;
        }

        // Appliquer les filtres
        $query->with(['commande', 'paiements', 'fournisseur']);

        // Filtres
        if ($request->filled('statut')) {
            $statut = $request->input('statut');

            if ($statut === 'payee') {
                $query->where('est_payee', true);
            } elseif ($statut === 'partiellement_payee') {
                $query->where('est_partiellement_payee', true);
            } elseif ($statut === 'en_retard') {
                $query->where('date_echeance', '<', now())
                      ->where('est_payee', false);
            } elseif ($statut === 'a_payer') {
                $query->where('date_echeance', '>=', now())
                      ->where('est_payee', false);
            }
        }

        if ($request->filled('commande_id')) {
            $query->where('commande_id', $request->input('commande_id'));
        }

        if ($request->filled('debut') && $request->filled('fin')) {
            $debut = Carbon::parse($request->input('debut'))->startOfDay();
            $fin = Carbon::parse($request->input('fin'))->endOfDay();
            $query->whereBetween('date_facture', [$debut, $fin]);
        }

        // Filtre de recherche
        if ($request->filled('recherche')) {
            $recherche = $request->input('recherche');
            $query->where(function($q) use ($recherche) {
                $q->where('reference', 'like', "%{$recherche}%")
                  ->orWhere('numero_facture', 'like', "%{$recherche}%");
            });
        }

        // Trier et paginer les résultats
        $factures = $query->latest('date_facture')->paginate(20)->withQueryString();

        // Statistiques
        $stats = [
            'total' => $fournisseur->factures()->count(),
            'montant_total' => $fournisseur->factures()->sum('montant_ttc'),
            'montant_paye' => $fournisseur->factures()->sum('montant_paye'),
            'montant_restant' => $fournisseur->factures()->sum('reste_a_payer'),
            'en_retard' => $fournisseur->factures()
                ->where('date_echeance', '<', now())
                ->where('est_payee', false)
                ->count(),
            'a_payer' => $fournisseur->factures()
                ->where('date_echeance', '>=', now())
                ->where('est_payee', false)
                ->count(),
        ];

        return view('fournisseurs.factures.index', compact(
            'fournisseur',
            'factures',
            'stats'
        ));
    }

    /**
     * Afficher le formulaire de création d'une facture
     */
    public function create(Fournisseur $fournisseur, Request $request)
    {
        $commande = null;
        $lignesFacture = [];

        // Si une commande est spécifiée, charger ses informations
        if ($request->filled('commande_id')) {
            $commande = $fournisseur->commandes()
                ->with(['lignes.article', 'livraisons.lignes'])
                ->findOrFail($request->input('commande_id'));

            // Récupérer les lignes de commande non facturées ou partiellement facturées
            $lignesCommande = $commande->lignes()
                ->where(function($query) {
                    $query->where('statut', 'livree')
                          ->orWhere('statut', 'partiellement_livree');
                })
                ->get()
                ->map(function($ligne) use ($commande) {
                    // Calculer la quantité déjà facturée
                    $quantiteFacturee = $ligne->factures->sum('pivot.quantite');
                    $quantiteLivree = $ligne->quantite_livree;
                    $quantiteRestante = $quantiteLivree - $quantiteFacturee;

                    // Si la quantité restante est négative (erreur possible), la ramener à 0
                    $quantiteRestante = max(0, $quantiteRestante);

                    return [
                        'id' => $ligne->id,
                        'reference_article' => $ligne->reference_article,
                        'designation' => $ligne->designation,
                        'unite' => $ligne->unite,
                        'quantite_commandee' => $ligne->quantite,
                        'quantite_livree' => $quantiteLivree,
                        'quantite_facturee' => $quantiteFacturee,
                        'quantite_restante' => $quantiteRestante,
                        'prix_unitaire_ht' => $ligne->prix_unitaire_ht,
                        'tva_taux' => $ligne->tva_taux,
                        'remise' => $ligne->remise,
                        'montant_ht' => $ligne->montant_ht,
                        'montant_tva' => $ligne->montant_tva,
                        'montant_ttc' => $ligne->montant_ttc,
                        'article' => $ligne->article,
                    ];
                });

            // Filtrer les lignes avec une quantité restante > 0
            $lignesFacture = $lignesCommande->filter(function($ligne) {
                return $ligne['quantite_restante'] > 0;
            })->values();
        }

        // Liste des commandes pouvant être facturées
        $commandes = $fournisseur->commandes()
            ->whereIn('statut', ['livree', 'partiellement_livree'])
            ->whereHas('lignes', function($query) {
                $query->where('statut', 'livree')
                      ->orWhere('statut', 'partiellement_livree');
            })
            ->orderBy('date_commande', 'desc')
            ->get(['id', 'reference', 'date_commande']);

        // Conditions de paiement par défaut
        $conditionsPaiement = [
            '30j' => '30 jours fin de mois',
            '45j' => '45 jours fin de mois',
            '60j' => '60 jours fin de mois',
            '30df' => '30 jours date de facture',
            '45df' => '45 jours date de facture',
            '60df' => '60 jours date de facture',
            'comptant' => 'Comptant',
        ];

        return view('fournisseurs.factures.create', compact(
            'fournisseur',
            'commande',
            'commandes',
            'lignesFacture',
            'conditionsPaiement'
        ));
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function store(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:50|unique:facture_fournisseurs,reference',
            'commande_id' => 'required|exists:commande_fournisseurs,id,fournisseur_id,' . $fournisseur->id,
            'numero_facture' => 'required|string|max:100|unique:facture_fournisseurs,numero_facture',
            'date_facture' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_facture',
            'conditions_paiement' => 'required|string|max:50',
            'frais_livraison' => 'nullable|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
            'type_remise' => 'required_with:remise|in:pourcentage,montant',
            'notes' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.ligne_commande_id' => 'required|exists:ligne_commande_fournisseurs,id,commande_id,' . $request->input('commande_id'),
            'lignes.*.quantite' => 'required|numeric|min:0.001',
            'lignes.*.prix_unitaire_ht' => 'required|numeric|min:0',
            'lignes.*.tva_taux' => 'required|numeric|min:0|max:100',
            'lignes.*.remise' => 'nullable|numeric|min:0|max:100',
            'lignes.*.description' => 'nullable|string',
        ]);

        // Vérifier les quantités facturées
        $lignesCommande = DB::table('ligne_commande_fournisseurs')
            ->whereIn('id', collect($validated['lignes'])->pluck('ligne_commande_id'))
            ->get()
            ->keyBy('id');

        $lignesFactureExistantes = DB::table('ligne_facture_fournisseurs')
            ->whereIn('ligne_commande_id', collect($validated['lignes'])->pluck('ligne_commande_id'))
            ->select('ligne_commande_id', DB::raw('SUM(quantite) as quantite_facturee'))
            ->groupBy('ligne_commande_id')
            ->pluck('quantite_facturee', 'ligne_commande_id');

        foreach ($validated['lignes'] as $ligne) {
            $ligneCommande = $lignesCommande->get($ligne['ligne_commande_id']);

            if (!$ligneCommande) {
                return back()
                    ->withInput()
                    ->with('error', 'Une erreur est survenue lors de la validation des quantités.');
            }

            $quantiteDejaFacturee = $lignesFactureExistantes->get($ligne['ligne_commande_id'], 0);
            $quantiteRestante = $ligneCommande->quantite_livree - $quantiteDejaFacturee;

            if ($ligne['quantite'] > $quantiteRestante) {
                return back()
                    ->withInput()
                    ->with('error', 'La quantité facturée pour la ligne "' . $ligneCommande->designation . '" dépasse la quantité restante (' . $quantiteRestante . ').');
            }
        }

        // Calcul des totaux
        $totaux = $this->calculerTotaux($validated['lignes'], $validated['frais_livraison'] ?? 0, $validated['remise'] ?? 0, $validated['type_remise'] ?? 'pourcentage');

        // Création de la facture
        $facture = new FactureFournisseur([
            'reference' => $validated['reference'] ?? 'FACT-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'fournisseur_id' => $fournisseur->id,
            'commande_id' => $validated['commande_id'],
            'numero_facture' => $validated['numero_facture'],
            'date_facture' => $validated['date_facture'],
            'date_echeance' => $validated['date_echeance'],
            'conditions_paiement' => $validated['conditions_paiement'],
            'frais_livraison' => $validated['frais_livraison'] ?? 0,
            'remise' => $validated['remise'] ?? 0,
            'type_remise' => $validated['type_remise'] ?? 'pourcentage',
            'montant_ht' => $totaux['montant_ht'],
            'tva' => $totaux['tva'],
            'montant_ttc' => $totaux['montant_ttc'],
            'montant_paye' => 0,
            'reste_a_payer' => $totaux['montant_ttc'],
            'statut' => 'non_payee',
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        // Enregistrement dans une transaction
        DB::beginTransaction();

        try {
            $facture->save();

            // Enregistrement des lignes de facture
            $lignesFacture = [];

            foreach ($validated['lignes'] as $ligne) {
                $ligneCommande = $lignesCommande->get($ligne['ligne_commande_id']);

                $montantLigneHT = $ligne['quantite'] * $ligne['prix_unitaire_ht'] * (1 - ($ligne['remise'] ?? 0) / 100);
                $montantLigneTVA = $montantLigneHT * ($ligne['tva_taux'] / 100);

                $lignesFacture[] = [
                    'facture_id' => $facture->id,
                    'ligne_commande_id' => $ligne['ligne_commande_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire_ht' => $ligne['prix_unitaire_ht'],
                    'tva_taux' => $ligne['tva_taux'],
                    'remise' => $ligne['remise'] ?? 0,
                    'montant_ht' => $montantLigneHT,
                    'montant_tva' => $montantLigneTVA,
                    'montant_ttc' => $montantLigneHT + $montantLigneTVA,
                    'description' => $ligne['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Mise à jour du statut de la ligne de commande
                $quantiteTotaleFacturee = $lignesFactureExistances->get($ligne['ligne_commande_id'], 0) + $ligne['quantite'];

                if ($quantiteTotaleFacturee >= $ligneCommande->quantite_livree) {
                    DB::table('ligne_commande_fournisseurs')
                        ->where('id', $ligne['ligne_commande_id'])
                        ->update(['statut' => 'facturee']);
                } else {
                    DB::table('ligne_commande_fournisseurs')
                        ->where('id', $ligne['ligne_commande_id'])
                        ->update(['statut' => 'partiellement_facturee']);
                }
            }

            // Insérer les lignes de facture
            DB::table('ligne_facture_fournisseurs')->insert($lignesFacture);

            // Mise à jour du statut de la commande
            $commande = $facture->commande;
            $lignesCommande = $commande->lignes;

            $toutesFacturees = $lignesCommande->every(function($ligne) {
                return $ligne->statut === 'facturee';
            });

            $aucuneFacturee = $lignesCommande->every(function($ligne) {
                return $ligne->statut !== 'facturee' && $ligne->statut !== 'partiellement_facturee';
            });

            if ($toutesFacturees) {
                $commande->statut = 'facturee';
            } else if ($aucuneFacturee) {
                // Ne rien faire, la commande reste dans son état actuel
            } else {
                $commande->statut = 'partiellement_facturee';
            }

            $commande->save();

            // Mise à jour du fournisseur
            $fournisseur->increment('nombre_factures');
            $fournisseur->total_achats_ht += $facture->montant_ht;
            $fournisseur->total_tva += $facture->tva;
            $facture->total_ttc += $facture->montant_ttc;
            $fournisseur->save();

            DB::commit();

            return redirect()
                ->route('fournisseurs.factures.show', ['fournisseur' => $fournisseur->id, 'facture' => $facture->id])
                ->with('success', 'La facture a été enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de la facture : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une facture
     */
    public function show(Fournisseur $fournisseur, FactureFournisseur $facture)
    {
        // Vérifier que la facture appartient bien au fournisseur
        if ($facture->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }

        $facture->load([
            'lignes.ligneCommande',
            'lignes.article',
            'commande',
            'paiements',
            'user',
            'depensesCaisses.caisse',
        ]);

        // Récupérer les autres factures de la même commande
        $autresFactures = $fournisseur->factures()
            ->where('commande_id', $facture->commande_id)
            ->where('id', '!=', $facture->id)
            ->orderBy('date_facture', 'desc')
            ->get();

        // Calculer le montant payé et le reste à payer
        $montantPaye = $facture->paiements->sum('montant');
        $resteAPayer = $facture->montant_ttc - $montantPaye;

        // Déterminer le statut de paiement
        $statutPaiement = 'non_payee';

        if ($montantPaye >= $facture->montant_ttc) {
            $statutPaiement = 'payee';
        } else if ($montantPaye > 0) {
            $statutPaiement = 'partiellement_payee';
        }

        // Vérifier si la facture est en retard
        $enRetard = !$facture->est_payee && $facture->date_echeance < now();

        // Caisses actives pour le paiement via trésorerie
        $caisses = Caisse::where('est_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'solde_actuel']);

        return view('fournisseurs.factures.show', compact(
            'fournisseur',
            'facture',
            'autresFactures',
            'montantPaye',
            'resteAPayer',
            'statutPaiement',
            'enRetard',
            'caisses'
        ));
    }

    /**
     * Payer une facture via une caisse (Trésorerie)
     */
    public function payerCaisse(Request $request, Fournisseur $fournisseur, FactureFournisseur $facture)
    {
        if ($facture->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }

        $resteAPayer = max(0, (float) $facture->reste_a_payer);

        $validated = $request->validate([
            'caisse_id' => 'required|exists:caisses,id',
            'montant' => 'required|numeric|min:0.01|max:' . $resteAPayer,
            'date_paiement' => 'required|date',
            'mode_paiement' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Création du paiement fournisseur (côté comptabilité)
            $paiement = new PaiementFournisseur([
                'reference' => 'PAYC-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'fournisseur_id' => $fournisseur->id,
                'facture_id' => $facture->id,
                'date_paiement' => $validated['date_paiement'],
                'montant' => $validated['montant'],
                'mode_paiement' => $validated['mode_paiement'] ?? 'especes',
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);
            $paiement->save();

            // Création du mouvement de trésorerie (dépense de caisse)
            $caisse = Caisse::lockForUpdate()->findOrFail($validated['caisse_id']);

            $depense = new DepenseCaisse([
                'reference' => 'DEC-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'caisse_id' => $caisse->id,
                'libelle' => 'Paiement facture fournisseur ' . ($facture->numero_facture ?? $facture->reference),
                'description' => $validated['notes'] ?? null,
                'montant' => $validated['montant'],
                'devise' => 'XOF',
                'date_depense' => $validated['date_paiement'],
                'mode_paiement' => 'especes',
                'statut' => 'paye',
                'createur_id' => auth()->id(),
                'facture_fournisseur_id' => $facture->id,
                'paiement_fournisseur_id' => $paiement->id,
            ]);
            $depense->save();

            // Mise à jour du solde de la caisse
            $caisse->solde_actuel = ($caisse->solde_actuel ?? 0) - $validated['montant'];
            $caisse->save();

            // Mise à jour de la facture
            $facture->montant_paye += $validated['montant'];
            $facture->reste_a_payer = $facture->montant_ttc - $facture->montant_paye;

            if ($facture->reste_a_payer <= 0) {
                $facture->statut = 'payee';
                $facture->date_paiement = $validated['date_paiement'];
            } else {
                $facture->statut = 'partiellement_payee';
            }

            $facture->save();

            DB::commit();

            return back()->with('success', 'Le paiement via caisse a été enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', "Erreur lors de l'enregistrement du paiement via caisse : " . $e->getMessage());
        }
    }

    /**
     * Enregistrer un paiement pour une facture
     */
    public function enregistrerPaiement(Request $request, Fournisseur $fournisseur, FactureFournisseur $facture)
    {
        // Vérifier que la facture appartient bien au fournisseur
        if ($facture->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }

        $validated = $request->validate([
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0.01|max:' . $facture->reste_a_payer,
            'mode_paiement' => 'required|string|max:50',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Création du paiement
        $paiement = new PaiementFournisseur([
            'reference' => $validated['reference'] ?? 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'fournisseur_id' => $fournisseur->id,
            'facture_id' => $facture->id,
            'date_paiement' => $validated['date_paiement'],
            'montant' => $validated['montant'],
            'mode_paiement' => $validated['mode_paiement'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        // Enregistrement dans une transaction
        DB::beginTransaction();

        try {
            $paiement->save();

            // Mise à jour de la facture
            $facture->montant_paye += $validated['montant'];
            $facture->reste_a_payer = $facture->montant_ttc - $facture->montant_paye;

            if ($facture->reste_a_payer <= 0) {
                $facture->statut = 'payee';
                $facture->date_paiement = $validated['date_paiement'];
            } else {
                $facture->statut = 'partiellement_payee';
            }

            $facture->save();

            // Mise à jour du fournisseur
            $fournisseur->total_paiements += $validated['montant'];
            $fournisseur->save();

            DB::commit();

            return back()->with('success', 'Le paiement a été enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Annuler une facture
     */
    public function annuler(Request $request, Fournisseur $fournisseur, FactureFournisseur $facture)
    {
        // Vérifier que la facture appartient bien au fournisseur
        if ($facture->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }

        // Vérifier que la facture n'est pas déjà annulée
        if ($facture->statut === 'annulee') {
            return back()->with('warning', 'Cette facture a déjà été annulée.');
        }

        // Vérifier qu'aucun paiement n'a été effectué
        if ($facture->paiements()->exists()) {
            return back()->with('error', 'Impossible d\'annuler une facture déjà payée.');
        }

        $validated = $request->validate([
            'motif_annulation' => 'required|string|max:255',
        ]);

        // Mise à jour de la facture
        $facture->statut = 'annulee';
        $facture->motif_annulation = $validated['motif_annulation'];

        // Enregistrement dans une transaction
        DB::beginTransaction();

        try {
            $facture->save();

            // Mise à jour des lignes de commande
            foreach ($facture->lignes as $ligneFacture) {
                $ligneCommande = $ligneFacture->ligneCommande;

                // Vérifier si la ligne de commande était marquée comme facturée
                if ($ligneCommande->statut === 'facturee' || $ligneCommande->statut === 'partiellement_facturee') {
                    // Compter le nombre de factures pour cette ligne (hors celle qu'on annule)
                    $nbFactures = DB::table('ligne_facture_fournisseurs')
                        ->where('ligne_commande_id', $ligneCommande->id)
                        ->where('facture_id', '!=', $facture->id)
                        ->count();

                    if ($nbFactures > 0) {
                        $ligneCommande->statut = 'partiellement_facturee';
                    } else {
                        $ligneCommande->statut = $ligneCommande->quantite_livree > 0 ? 'partiellement_livree' : 'en_attente';
                    }

                    $ligneCommande->save();
                }
            }

            // Mise à jour de la commande
            $commande = $facture->commande;
            $lignesCommande = $commande->lignes;

            $toutesFacturees = $lignesCommande->every(function($ligne) {
                return $ligne->statut === 'facturee';
            });

            $aucuneFacturee = $lignesCommande->every(function($ligne) {
                return $ligne->statut !== 'facturee' && $ligne->statut !== 'partiellement_facturee';
            });

            if ($toutesFacturees) {
                $commande->statut = 'facturee';
            } else if ($aucuneFacturee) {
                $commande->statut = $commande->statut === 'facturee' ? 'partiellement_livree' : $commande->statut;
            } else {
                $commande->statut = 'partiellement_facturee';
            }

            $commande->save();

            DB::commit();

            return back()->with('success', 'La facture a été annulée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Une erreur est survenue lors de l\'annulation de la facture : ' . $e->getMessage());
        }
    }

    /**
     * Générer une facture au format PDF
     */
    public function pdf(Fournisseur $fournisseur, FactureFournisseur $facture)
    {
        // Vérifier que la facture appartient bien au fournisseur
        if ($facture->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }

        $facture->load([
            'lignes.ligneCommande',
            'lignes.article',
            'commande',
            'paiements',
            'user'
        ]);

        $pdf = PDF::loadView('fournisseurs.factures.pdf', [
            'fournisseur' => $fournisseur,
            'facture' => $facture,
        ]);

        return $pdf->download('facture-' . $facture->reference . '.pdf');
    }

    /**
     * Calculer les totaux d'une facture
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
}
