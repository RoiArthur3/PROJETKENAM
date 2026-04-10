<?php

namespace App\Http\Controllers;

use App\Exports\IndicateursFinanciersExport;
use App\Exports\AnalyseActiviteExport;
use App\Exports\AnalyseComparativeExport;
use App\Models\Operation;
use App\Models\User;
use App\Models\DepenseCaisse;
use App\Models\JournalComptable;
use App\Services\AccountingWorkbookUploadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class ComptabiliteController extends Controller
{
    /**
     * Dashboard comptabilité
     */
    public function dashboard()
    {
        try {
            // Debug: vérifier si les tables existent avant de faire les requêtes
            $tablesExist = [
                'factures' => Schema::hasTable('factures'),
                'operations' => Schema::hasTable('operations'),
                'depenses' => Schema::hasTable('depenses'),
                'recettes' => Schema::hasTable('recettes'),
                'compte_bancaires' => Schema::hasTable('compte_bancaires'),
                'caisses' => Schema::hasTable('caisses'),
                'paiements' => Schema::hasTable('paiements'),
                'ecritures_comptables' => Schema::hasTable('ecritures_comptables'),
            ];

            // Si une table essentielle manque, afficher un message d'erreur mais continuer avec les données disponibles
            $missingTables = array_keys(array_filter($tablesExist, fn($exists) => !$exists));
            $errorMessages = [];
            if (!empty($missingTables)) {
                $errorMessages[] = 'Tables manquantes: ' . implode(', ', $missingTables);
            }

            // === STATISTIQUES FINANCIÈRES RÉELLES DE LA BASE DE DONNÉES ===

            // Factures réelles dans la table factures
            $totalFactures = $tablesExist['factures'] ? DB::table('factures')->count() : 0;
            $montantTotalFactures = $tablesExist['factures'] ? DB::table('factures')->sum('montant_ttc') : 0;

            // Factures impayées (montant et nombre)
            $facturesImpayees = $tablesExist['factures'] ? DB::table('factures')->where('statut', 'impayee')->count() : 0;
            $montantFacturesImpayees = $tablesExist['factures'] ? DB::table('factures')->where('statut', 'impayee')->sum('montant_ttc') : 0;

            // Factures payées
            $facturesPayees = $tablesExist['factures'] ? DB::table('factures')->where('statut', 'payee')->count() : 0;
            $montantFacturesPayees = $tablesExist['factures'] ? DB::table('factures')->where('statut', 'payee')->sum('montant_ttc') : 0;

            // Opérations payées (chiffre d'affaires réel) - utiliser is_paid = 1
            $operationsPayees = $tablesExist['operations'] ? DB::table('operations')->where('is_paid', 1)->count() : 0;
            $montantOperationsPayees = $tablesExist['operations'] ? DB::table('operations')->where('is_paid', 1)->sum('montant') : 0;

            // === CALCULS COMBINÉS POUR LE CHIFFRE D'AFFAIRES ===
            // Si pas de factures, utiliser les opérations payées comme équivalent
            $totalFacturesComptabilite = $totalFactures + $operationsPayees;
            $montantTotalFacturesComptabilite = $montantTotalFactures + $montantOperationsPayees;

            // Dépenses totales
            $totalDepenses = $tablesExist['depenses'] ? DB::table('depenses')->count() : 0;
            $montantTotalDepenses = $tablesExist['depenses'] ? DB::table('depenses')->sum('montant') : 0;

            // Recettes totales
            $totalRecettes = $tablesExist['recettes'] ? DB::table('recettes')->count() : 0;
            $montantTotalRecettes = $tablesExist['recettes'] ? DB::table('recettes')->sum('montant') : 0;

            // Soldes de trésorerie
            $soldeBancaires = $tablesExist['compte_bancaires'] ? DB::table('compte_bancaires')->sum('solde') : 0;
            $soldeCaisses = $tablesExist['caisses'] ? DB::table('caisses')->sum('solde_actuel') : 0; // colonne réelle : solde_actuel
            $soldeTotalTresorerie = $soldeBancaires + $soldeCaisses;

            // Paiements (décaissements réels — table paiements)
            $totalPaiements = $tablesExist['paiements'] ? DB::table('paiements')->count() : 0;
            $montantTotalPaiements = $tablesExist['paiements'] ? DB::table('paiements')->sum('montant') : 0;

            // Calculs financiers basés sur les vraies données
            $chiffreAffaires = $montantOperationsPayees + $montantFacturesPayees;
            $totalCharges = $montantTotalDepenses;
            $resultatNet = $chiffreAffaires - $totalCharges;
            $tresorerieDisponible = $soldeTotalTresorerie + $montantFacturesImpayees;

            $stats = [
                'total_factures' => $totalFacturesComptabilite,
                'montant_total_factures' => $montantTotalFacturesComptabilite,
                'factures_reelles' => $totalFactures,
                'operations_payees' => $operationsPayees,
                'factures_impayees' => $facturesImpayees,
                'montant_factures_impayees' => $montantFacturesImpayees,
                'montant_impaye' => $montantFacturesImpayees,          // alias attendu par la vue
                'factures_en_retard' => $tablesExist['factures']
                    ? DB::table('factures')->where('statut', 'impayee')->where('date_echeance', '<', now()->toDateString())->count()
                    : 0,
                'factures_payees' => $facturesPayees,
                'montant_factures_payees' => $montantFacturesPayees,
                'montant_operations_payees' => $montantOperationsPayees,
                'total_depenses' => $totalDepenses,
                'montant_total_depenses' => $montantTotalDepenses,
                'montant_depenses' => $montantTotalDepenses,           // alias attendu par la vue
                'total_recettes' => $totalRecettes,
                'montant_total_recettes' => $montantTotalRecettes,
                'montant_recettes' => $montantTotalRecettes,           // alias attendu par la vue
                'solde_bancaires' => $soldeBancaires,
                'solde_caisses' => $soldeCaisses,
                'solde_total_tresorerie' => $soldeTotalTresorerie,
                'solde_tresorerie' => $soldeTotalTresorerie,           // alias attendu par la vue
                'total_paiements' => $totalPaiements,
                'montant_total_paiements' => $montantTotalPaiements,
                'total_ecritures' => $tablesExist['ecritures_comptables']
                    ? DB::table('ecritures_comptables')->count()
                    : 0,
                'chiffre_affaires' => $chiffreAffaires,
                'total_charges' => $totalCharges,
                'resultat_net' => $resultatNet,
                'tresorerie_disponible' => $tresorerieDisponible,
            ];

            // Données pour les graphiques (basées sur les vraies données)
            $charts = [
                'factures_par_mois' => $tablesExist['factures'] ? $this->getFacturesParMois() : collect([]),
                'depenses_par_mois' => $tablesExist['depenses'] ? $this->getDepensesParMois() : collect([]),
                'recettes_par_mois' => $tablesExist['recettes'] ? $this->getRecettesParMois() : collect([]),
                'operations_par_mois' => $tablesExist['operations'] ? $this->getOperationsParMois() : collect([]),
            ];

            $viewData = compact('stats', 'charts');

            // Ajouter les messages d'erreur s'il y en a
            if (!empty($errorMessages)) {
                $viewData['error'] = implode(' | ', $errorMessages);
            }

            return view('comptabilite.dashboard', $viewData);
        } catch (\Exception $e) {
            // En cas d'erreur, afficher le dashboard avec message d'erreur au lieu de rediriger
            return view('comptabilite.dashboard', [
                'stats' => [],
                'charts' => [],
                'error' => 'Erreur lors du chargement du dashboard: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Index des dépenses (décaissements)
     */
    public function depensesIndex()
    {
        try {
            /** @var User|null $user */
            $user = Auth::user();

            // Utiliser DepenseCaisse de trésorerie au lieu de la table depenses
            $depensesQuery = \App\Models\DepenseCaisse::with([
                'caisse',
                'createur',      // relation correcte (non 'creator')
                'beneficiaire'
            ]);

            // Appliquer les mêmes filtres de visibilité que la trésorerie
            if ($user && !in_array($user->role, ['admin', 'superadmin'], true)) {
                $depensesQuery->whereHas('caisse', function($query) use ($user) {
                    $query->where('responsable_id', $user->id);
                });
            }

            $depenses = $depensesQuery->latest()->get();

            // Fallback sur la table depenses si depense_caisses est vide
            if ($depenses->isEmpty() && Schema::hasTable('depenses')) {
                $depenses = DB::table('depenses')
                    ->orderByDesc('date_depense')
                    ->get()
                    ->map(function ($d) {
                        return (object) [
                            'id'          => $d->id,
                            'libelle'     => $d->libelle ?? ($d->description ?? ''),
                            'montant'     => $d->montant ?? 0,
                            'date_depense'=> $d->date_depense ?? null,
                            'statut'      => $d->statut ?? null,
                            'mode_paiement' => $d->mode_paiement ?? null,
                            'caisse'      => null,
                            'createur'    => null,
                            'beneficiaire'=> null,
                        ];
                    });
            }

            return view('comptabilite.depenses', compact('depenses'));

        } catch (\Exception $e) {
            Log::error('Erreur dans depensesIndex: ' . $e->getMessage());
            return view('comptabilite.depenses', ['depenses' => collect([])])
                ->with('error', 'Erreur lors du chargement des dépenses: ' . $e->getMessage());
        }
    }

    /**
     * Créer une dépense
     */
    public function depensesCreate()
    {
        try {
            $fournisseurs = DB::table('fournisseurs')->get();
            return view('comptabilite.depenses-create', compact('fournisseurs'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Index des factures
     */
    public function facturesIndex()
    {
        try {
            // Debug: vérifier si la table factures existe
            if (!Schema::hasTable('factures')) {
                return view('comptabilite.factures', ['factures' => collect([])])
                    ->with('error', 'La table factures n\'existe pas dans la base de données.');
            }

            $factures = DB::table('factures')
                ->leftJoin('clients', 'factures.client_id', '=', 'clients.id')
                ->select('factures.*', 'clients.nom as client_nom')
                ->orderBy('factures.created_at', 'desc')
                ->paginate(20);

            // Debug: vérifier si les données sont bien récupérées
            if ($factures->isEmpty()) {
                $factures = collect([]);
            }

            return view('comptabilite.factures', compact('factures'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner la vue avec des données vides et un message
            return view('comptabilite.factures', ['factures' => collect([])])
                ->with('error', 'Erreur lors du chargement des factures: ' . $e->getMessage());
        }
    }

    /**
     * Créer une facture
     */
    public function facturesCreate()
    {
        try {
            $clients = DB::table('clients')->get();
            $bonsCommande = Schema::hasTable('bons_commande')
                ? DB::table('bons_commande')->orderByDesc('id')->get()
                : collect([]);

            return view('comptabilite.facturation-create', compact('clients', 'bonsCommande'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function facturesStore(Request $request)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'numero' => 'required|string|max:50|unique:factures,numero',
                'numero_fne' => 'nullable|string|max:255',
                'date_facture' => 'required|date',
                'bon_commande_id' => 'nullable',
                'mission' => 'nullable|string|max:255',
                'acompte_paye' => 'nullable|numeric|min:0',
                'montant_ht' => 'required|numeric|min:0',
                'tva' => 'nullable|numeric|min:0|max:100',
                'montant_ttc' => 'required|numeric|min:0',
                'statut' => 'required|in:payee,impayee,en_attente,annulee',
                'description' => 'nullable|string|max:500',
                'notes' => 'nullable|string|max:1000',
            ], [
                'client_id.required' => 'Le client est obligatoire',
                'client_id.exists' => 'Le client sélectionné n\'existe pas',
                'numero.required' => 'Le numéro de facture est obligatoire',
                'numero.unique' => 'Ce numéro de facture existe déjà',
                'montant_ht.required' => 'Le montant HT est obligatoire',
                'montant_ht.numeric' => 'Le montant HT doit être un nombre',
                'montant_ttc.required' => 'Le montant TTC est obligatoire',
                'statut.required' => 'Le statut est obligatoire',
            ]);

            // Vérifier si la table factures existe
            if (!Schema::hasTable('factures')) {
                return back()->with('error', 'La table factures n\'existe pas. Veuillez contacter l\'administrateur.');
            }

            // Calcul automatique du montant TTC si non fourni
            if (!isset($validated['montant_ttc']) || $validated['montant_ttc'] == 0) {
                $tva = $validated['tva'] ?? 18; // TVA par défaut 18%
                $validated['montant_ttc'] = $validated['montant_ht'] * (1 + $tva / 100);
            }

            // Préparer les données pour l'insertion
            $factureData = [
                'client_id' => $validated['client_id'],
                'numero' => $validated['numero'],
                'date_facture' => $validated['date_facture'],
                'montant_ht' => $validated['montant_ht'],
                'tva' => $validated['tva'] ?? 18,
                'montant_ttc' => $validated['montant_ttc'],
                'statut' => $validated['statut'],
                'description' => $validated['description'] ?? null,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('factures', 'numero_fne')) {
                $factureData['numero_fne'] = $validated['numero_fne'] ?? null;
            }

            if (Schema::hasColumn('factures', 'bon_commande_id')) {
                $factureData['bon_commande_id'] = $validated['bon_commande_id'] ?? null;
            }

            if (Schema::hasColumn('factures', 'mission')) {
                $factureData['mission'] = $validated['mission'] ?? null;
            }

            if (Schema::hasColumn('factures', 'acompte_paye')) {
                $factureData['acompte_paye'] = $validated['acompte_paye'] ?? null;
            }

            // Insérer la facture
            $factureId = DB::table('factures')->insertGetId($factureData);

            // Journaliser l'action
            Log::info('Facture créée avec succès', [
                'facture_id' => $factureId,
                'numero' => $validated['numero'],
                'client_id' => $validated['client_id'],
                'montant_ttc' => $validated['montant_ttc'],
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->route('comptabilite.factures')
                ->with('success', 'Facture créée avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Erreur de validation: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la facture', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la facture: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le détail d'une facture
     */
    public function facturesShow($id)
    {
        $facture = DB::table('factures')->where('id', $id)->first();

        if (!$facture) {
            return redirect()->route('comptabilite.factures.index')
                ->with('error', 'Facture introuvable.');
        }

        return view('comptabilite.facturation-show', compact('facture'));
    }

    /**
     * Afficher le formulaire d'edition d'une facture
     */
    public function facturesEdit($id)
    {
        $facture = DB::table('factures')->where('id', $id)->first();

        if (!$facture) {
            return redirect()->route('comptabilite.factures.index')
                ->with('error', 'Facture introuvable.');
        }

        $clientLabelColumn = $this->resolveClientLabelColumn();
        $clients = DB::table('clients')
            ->select('id', DB::raw("{$clientLabelColumn} as nom"))
            ->orderBy($clientLabelColumn)
            ->get();

        $missions = collect();
        if (Schema::hasTable('vehicle_missions')) {
            $missions = DB::table('vehicle_missions')
                ->select('id', 'reference', 'client_id', 'source_type', 'source_id', 'source_reference')
                ->orderByDesc('id')
                ->limit(500)
                ->get();
        }

        $bonsCommandeTable = $this->resolveBonCommandeTable();
        $bonsCommande = collect();
        if ($bonsCommandeTable !== null) {
            $numeroColumn = Schema::hasColumn($bonsCommandeTable, 'numero_bc') ? 'numero_bc' : 'numero';
            $bonsCommande = DB::table($bonsCommandeTable)
                ->select('id', 'client_id', DB::raw("{$numeroColumn} as numero"))
                ->orderByDesc('id')
                ->limit(1000)
                ->get();
        }

        $bcByNumero = $bonsCommande->keyBy('numero');
        $missionMeta = [];

        foreach ($missions as $mission) {
            $autoBonCommandeId = null;

            if (!empty($mission->source_reference) && isset($bcByNumero[$mission->source_reference])) {
                $autoBonCommandeId = (int) $bcByNumero[$mission->source_reference]->id;
            } elseif (!empty($mission->source_type) && str_contains((string) $mission->source_type, 'BonCommande')) {
                $candidateId = (int) ($mission->source_id ?? 0);
                if ($candidateId > 0 && $bonsCommande->firstWhere('id', $candidateId)) {
                    $autoBonCommandeId = $candidateId;
                }
            }

            $missionMeta[$mission->id] = [
                'client_id' => $mission->client_id ? (int) $mission->client_id : null,
                'bon_commande_id' => $autoBonCommandeId,
            ];
        }

        return view('comptabilite.factures-edit', compact('facture', 'clients', 'missions', 'bonsCommande', 'missionMeta'));
    }

    /**
     * Mettre a jour une facture
     */
    public function facturesUpdate(Request $request, $id)
    {
        $facture = DB::table('factures')->where('id', $id)->first();

        if (!$facture) {
            return redirect()->route('comptabilite.factures.index')
                ->with('error', 'Facture introuvable.');
        }

        $rules = [
            'client_id' => 'required|exists:clients,id',
            'date_facture' => 'required|date',
            'date_echeance' => 'nullable|date',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'nullable|numeric|min:0|max:100',
            'montant_ttc' => 'required|numeric|min:0',
            'statut' => 'required|string|max:50',
            'observations' => 'nullable|string|max:1000',
            'numero_fne' => 'nullable|string|max:100',
            'vehicle_mission_id' => 'nullable|integer',
            'bon_commande_id' => 'nullable',
        ];

        if (Schema::hasTable('vehicle_missions')) {
            $rules['vehicle_mission_id'] = 'nullable|exists:vehicle_missions,id';
        }

        $validated = $request->validate($rules);

        $payload = [
            'client_id' => (int) $validated['client_id'],
            'montant_ht' => (float) $validated['montant_ht'],
            'montant_ttc' => (float) $validated['montant_ttc'],
            'statut' => (string) $validated['statut'],
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('factures', 'date_facture')) {
            $payload['date_facture'] = $validated['date_facture'];
        }
        if (Schema::hasColumn('factures', 'date_facturation')) {
            $payload['date_facturation'] = $validated['date_facture'];
        }

        if (Schema::hasColumn('factures', 'date_echeance')) {
            $payload['date_echeance'] = $validated['date_echeance'] ?? null;
        }

        if (Schema::hasColumn('factures', 'tva')) {
            $payload['tva'] = (float) ($validated['tva'] ?? 0);
        }
        if (Schema::hasColumn('factures', 'tva_taux')) {
            $payload['tva_taux'] = (float) ($validated['tva'] ?? 0);
        }

        if (Schema::hasColumn('factures', 'notes')) {
            $payload['notes'] = $validated['observations'] ?? null;
        }
        if (Schema::hasColumn('factures', 'description')) {
            $payload['description'] = $validated['observations'] ?? null;
        }

        if (Schema::hasColumn('factures', 'numero_fne')) {
            $payload['numero_fne'] = $validated['numero_fne'] ?? null;
        }
        if (Schema::hasColumn('factures', 'vehicle_mission_id')) {
            $payload['vehicle_mission_id'] = $validated['vehicle_mission_id'] ?? null;
        }

        if (Schema::hasColumn('factures', 'bon_commande_id')) {
            $payload['bon_commande_id'] = $validated['bon_commande_id'] ?? null;
        }

        if (Schema::hasColumn('factures', 'bon_commande_numero')) {
            $payload['bon_commande_numero'] = $this->resolveBonCommandeNumero($validated['bon_commande_id'] ?? null);
        }

        DB::table('factures')->where('id', $id)->update($payload);

        return redirect()->route('comptabilite.factures.edit', $id)
            ->with('success', 'Facture mise a jour avec succes.');
    }

    private function resolveClientLabelColumn(): string
    {
        if (Schema::hasColumn('clients', 'nom')) {
            return 'nom';
        }
        if (Schema::hasColumn('clients', 'raison_sociale')) {
            return 'raison_sociale';
        }
        if (Schema::hasColumn('clients', 'nom_complet')) {
            return 'nom_complet';
        }

        return 'id';
    }

    private function resolveBonCommandeTable(): ?string
    {
        if (Schema::hasTable('bons_commande')) {
            return 'bons_commande';
        }
        if (Schema::hasTable('bon_commandes')) {
            return 'bon_commandes';
        }

        return null;
    }

    private function resolveBonCommandeNumero($bonCommandeId): ?string
    {
        if (empty($bonCommandeId)) {
            return null;
        }

        $table = $this->resolveBonCommandeTable();
        if ($table === null) {
            return null;
        }

        $numeroColumn = Schema::hasColumn($table, 'numero_bc') ? 'numero_bc' : 'numero';

        return DB::table($table)->where('id', $bonCommandeId)->value($numeroColumn);
    }

    /**
     * Index des recettes
     */
    public function recettesIndex()
    {
        try {
            // Debug: vérifier si la table recettes existe
            if (!Schema::hasTable('recettes')) {
                return view('comptabilite.recettes', ['recettes' => collect([])])
                    ->with('error', 'La table recettes n\'existe pas dans la base de données.');
            }

            $recettes = DB::table('recettes')
                ->leftJoin('clients', 'recettes.client_id', '=', 'clients.id')
                ->select('recettes.*', 'clients.nom as client_nom')
                ->orderBy('recettes.created_at', 'desc')
                ->paginate(20);

            return view('comptabilite.recettes', compact('recettes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Index des paiements
     */
    public function paiementsIndex()
    {
        try {
            // Debug: vérifier si la table paiements existe
            if (!Schema::hasTable('paiements')) {
                return view('comptabilite.tresorerie.approvisionnements', ['approvisionnements' => collect([])])
                    ->with('error', 'La table paiements n\'existe pas dans la base de données.');
            }

            $paiements = DB::table('paiements')
                ->leftJoin('fournisseurs', 'paiements.fournisseur_id', '=', 'fournisseurs.id')
                ->leftJoin('clients', 'paiements.client_id', '=', 'clients.id')
                ->select('paiements.*',
                        'fournisseurs.nom as fournisseur_nom',
                        'clients.nom as client_nom')
                ->orderBy('paiements.created_at', 'desc')
                ->paginate(20);

            // Debug: vérifier si les données sont bien récupérées
            if ($paiements->isEmpty()) {
                $paiements = collect([]);
            }

            // Envoyer la variable $approvisionnements pour compatibilité avec la vue
            return view('comptabilite.tresorerie.approvisionnements', ['approvisionnements' => $paiements]);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner la vue avec des données vides et un message
            return view('comptabilite.tresorerie.approvisionnements', ['approvisionnements' => collect([])])
                ->with('error', 'Erreur lors du chargement des paiements: ' . $e->getMessage());
        }
    }

    /**
     * Créer un paiement
     */
    public function paiementsCreate()
    {
        try {
            $fournisseurs = DB::table('fournisseurs')->get();
            $clients = DB::table('clients')->get();
            return view('comptabilite.tresorerie.approvisionnements-create', compact('fournisseurs', 'clients'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Rapport compte de résultat
     */
    public function compteResultat(Request $request)
    {
        try {
            [$debut, $fin, $periode] = $this->resolveCompteResultatPeriod(
                $request->input('periode', 'annee'),
                $request->input('date_debut'),
                $request->input('date_fin')
            );

            $donnees = $this->buildCompteResultatData($debut, $fin, $periode);

            return view('comptabilite.rapports.compte-resultat', compact('donnees'));
        } catch (\Exception $e) {
            // Logger l'erreur pour le débogage
            Log::error('Erreur compte de résultat: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Retourner une vue avec des données minimales au lieu de rediriger
            $donnees = [
                'total_produits' => 0,
                'total_charges' => 0,
                'marge_brute' => 0,
                'resultat_exploitation' => 0,
                'resultat_financier' => 0,
                'resultat_net' => 0,
                'produits' => [],
                'charges' => [],
                'periode' => 'annee',
                'debut' => now(),
                'fin' => now(),
                'resultat_precedent' => 0,
                'variation' => 0,
                'variation_pourcentage' => 0,
                'graphique_mensuel' => [],
                'evolution_mensuelle' => [],
                'chiffre_affaires' => 0,
                'marge_brute_pourcentage' => 0,
                'taux_rentabilite' => 0,
                'resultat_net_pourcentage' => 0,
                'type_affichage' => 'annuel',
                'base_comptable' => 'tresorerie',
            ];

            return view('comptabilite.rapports.compte-resultat', compact('donnees'))
                ->with('error', 'Erreur lors du chargement: ' . $e->getMessage());
        }
    }

    /**
     * Calculer le résultat net pour une période donnée
     */
    private function calculerResultatNet($debut, $fin)
    {
        $data = $this->computeCompteResultatTotals(Carbon::parse($debut), Carbon::parse($fin));

        return $data['resultat_net'];
    }

    /**
     * Obtenir les données pour le graphique mensuel
     */
    private function getGraphiqueMensuel($debut, $fin)
    {
        try {
            $data = [];
            $current = clone $debut;

            while ($current <= $fin) {
                $debutMois = $current->copy()->startOfMonth();
                $finMois = $current->copy()->endOfMonth();
                $donneesMois = $this->computeCompteResultatTotals($debutMois, $finMois);

                $data[] = [
                    'mois' => $current->format('M Y'),
                    'produits' => $donneesMois['total_produits'],
                    'charges' => $donneesMois['total_charges'],
                    'resultat' => $donneesMois['resultat_net']
                ];

                $current->addMonth();
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Rapport compte de résultat (alias pour la route)
     */
    public function rapportCompteResultat(Request $request)
    {
        return $this->compteResultat($request);
    }

    public function rapportIndicateursFinanciers(Request $request)
    {
        $donnees = $this->buildIndicateursFinanciersData($request);

        return view('comptabilite.rapports.indicateurs-financiers-metier', compact('donnees'));
    }

    /**
     * Analyse de l'activite: SIG + CAF (N et N-1)
     */
    public function rapportAnalyseActivite(Request $request)
    {
        try {
            $donnees = $this->buildAnalyseActiviteData($request);
            return view('comptabilite.rapports.analyse-activite', compact('donnees'));
        } catch (\Exception $e) {
            Log::error('Erreur rapportAnalyseActivite: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $anneeN = (int) $request->input('annee', Carbon::now()->year);
            $zero = array_fill_keys(['chiffre_affaires','consommations_intermediaires','valeur_ajoutee','charges_personnel','impots_taxes_sociales','ebe','dotations','reprises','resultat_net','caf'], 0);
            $donnees = [
                'annee_n' => $anneeN, 'annee_n1' => $anneeN - 1,
                'debut_n' => Carbon::create($anneeN, 1, 1), 'fin_n' => Carbon::create($anneeN, 12, 31),
                'debut_n1' => Carbon::create($anneeN - 1, 1, 1), 'fin_n1' => Carbon::create($anneeN - 1, 12, 31),
                'n' => $zero, 'n1' => $zero,
                'lignes' => [],
            ];
            return view('comptabilite.rapports.analyse-activite', compact('donnees'))
                ->with('error', 'Erreur lors du chargement: ' . $e->getMessage());
        }
    }

    public function rapportAnalyseRentabilite(Request $request)
    {
        try {
            $donnees = $this->buildAnalyseRentabiliteData($request);
            return view('comptabilite.rapports.analyse-rentabilite', compact('donnees'));
        } catch (\Exception $e) {
            Log::error('Erreur rapportAnalyseRentabilite: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $anneeN = (int) $request->input('annee', Carbon::now()->year);
            $zero = array_fill_keys(['chiffre_affaires','charges_totales','ebe','resultat_net','marge_nette','rentabilite_exploitation','caf'], 0);
            $donnees = [
                'annee_n' => $anneeN, 'annee_n1' => $anneeN - 1,
                'n' => $zero, 'n1' => $zero, 'lignes' => [],
            ];
            return view('comptabilite.rapports.analyse-rentabilite', compact('donnees'))
                ->with('error', 'Erreur lors du chargement: ' . $e->getMessage());
        }
    }

    public function rapportAnalyseVariationTreso(Request $request)
    {
        try {
            $donnees = $this->buildAnalyseVariationTresoData($request);
            return view('comptabilite.rapports.analyse-variation-treso', compact('donnees'));
        } catch (\Exception $e) {
            Log::error('Erreur rapportAnalyseVariationTreso: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $anneeN = (int) $request->input('annee', Carbon::now()->year);
            $zero = array_fill_keys(['encaissements','decaissements','flux_net_tresorerie','tresorerie_disponible','couverture_dettes'], 0);
            $donnees = [
                'annee_n' => $anneeN, 'annee_n1' => $anneeN - 1,
                'n' => $zero, 'n1' => $zero, 'lignes' => [],
            ];
            return view('comptabilite.rapports.analyse-variation-treso', compact('donnees'))
                ->with('error', 'Erreur lors du chargement: ' . $e->getMessage());
        }
    }

    public function rapportAnalyseVariationDette(Request $request)
    {
        try {
            $donnees = $this->buildAnalyseVariationDetteData($request);
            return view('comptabilite.rapports.analyse-variation-dette', compact('donnees'));
        } catch (\Exception $e) {
            Log::error('Erreur rapportAnalyseVariationDette: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $anneeN = (int) $request->input('annee', Carbon::now()->year);
            $zero = array_fill_keys(['dettes_fournisseurs','creances_clients','bfr','ratio_dette_ca','ecart_creances_dettes'], 0);
            $donnees = [
                'annee_n' => $anneeN, 'annee_n1' => $anneeN - 1,
                'n' => $zero, 'n1' => $zero, 'lignes' => [],
            ];
            return view('comptabilite.rapports.analyse-variation-dette', compact('donnees'))
                ->with('error', 'Erreur lors du chargement: ' . $e->getMessage());
        }
    }

    public function exportAnalyseActiviteExcel(Request $request)
    {
        $donnees = $this->buildAnalyseActiviteData($request);
        $filename = 'analyse-activite-sig-caf-' . $donnees['annee_n'] . '.xlsx';

        return Excel::download(new AnalyseActiviteExport($donnees), $filename);
    }

    public function exportAnalyseActivitePdf(Request $request)
    {
        $donnees = $this->buildAnalyseActiviteData($request);

        $pdf = Pdf::loadView('comptabilite.rapports.analyse-activite-pdf', [
            'donnees' => $donnees,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('analyse-activite-sig-caf-' . $donnees['annee_n'] . '.pdf');
    }

    public function exportAnalyseRentabiliteExcel(Request $request)
    {
        $donnees = $this->buildAnalyseRentabiliteData($request);
        $filename = 'analyse-rentabilite-' . $donnees['annee_n'] . '.xlsx';

        return Excel::download(new AnalyseComparativeExport($donnees), $filename);
    }

    public function exportAnalyseRentabilitePdf(Request $request)
    {
        $donnees = $this->buildAnalyseRentabiliteData($request);

        $pdf = Pdf::loadView('comptabilite.rapports.analyse-comparative-pdf', [
            'donnees' => $donnees,
            'titre' => 'ANALYSE DE RENTABILITE',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('analyse-rentabilite-' . $donnees['annee_n'] . '.pdf');
    }

    public function exportAnalyseVariationTresoExcel(Request $request)
    {
        $donnees = $this->buildAnalyseVariationTresoData($request);
        $filename = 'analyse-variation-treso-' . $donnees['annee_n'] . '.xlsx';

        return Excel::download(new AnalyseComparativeExport($donnees), $filename);
    }

    public function exportAnalyseVariationTresoPdf(Request $request)
    {
        $donnees = $this->buildAnalyseVariationTresoData($request);

        $pdf = Pdf::loadView('comptabilite.rapports.analyse-comparative-pdf', [
            'donnees' => $donnees,
            'titre' => 'ANALYSE VARIATION DE LA TRESO',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('analyse-variation-treso-' . $donnees['annee_n'] . '.pdf');
    }

    public function exportAnalyseVariationDetteExcel(Request $request)
    {
        $donnees = $this->buildAnalyseVariationDetteData($request);
        $filename = 'analyse-variation-dette-' . $donnees['annee_n'] . '.xlsx';

        return Excel::download(new AnalyseComparativeExport($donnees), $filename);
    }

    public function exportAnalyseVariationDettePdf(Request $request)
    {
        $donnees = $this->buildAnalyseVariationDetteData($request);

        $pdf = Pdf::loadView('comptabilite.rapports.analyse-comparative-pdf', [
            'donnees' => $donnees,
            'titre' => 'ANALYSE VARIATION DE LA DETTE',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('analyse-variation-dette-' . $donnees['annee_n'] . '.pdf');
    }

    public function exportIndicateursFinanciersExcel(Request $request)
    {
        $donnees = $this->buildIndicateursFinanciersData($request);
        $filename = 'indicateurs-financiers-' . $donnees['debut']->format('Ymd') . '-' . $donnees['fin']->format('Ymd') . '.xlsx';

        return Excel::download(new IndicateursFinanciersExport($donnees), $filename);
    }

    public function exportIndicateursFinanciersPdf(Request $request)
    {
        $donnees = $this->buildIndicateursFinanciersData($request);

        $pdf = Pdf::loadView('comptabilite.rapports.indicateurs-financiers-pdf', [
            'donnees' => $donnees,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('indicateurs-financiers-' . $donnees['debut']->format('Ymd') . '-' . $donnees['fin']->format('Ymd') . '.pdf');
    }

    private function resolveCompteResultatPeriod(string $periode, ?string $dateDebut, ?string $dateFin): array
    {
        return match ($periode) {
            'mois' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), 'mois'],
            'trimestre' => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter(), 'trimestre'],
            'personnalise' => [
                $dateDebut ? Carbon::parse($dateDebut)->startOfDay() : Carbon::now()->startOfMonth(),
                $dateFin ? Carbon::parse($dateFin)->endOfDay() : Carbon::now()->endOfMonth(),
                'personnalise'
            ],
            default => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear(), 'annee'],
        };
    }

    private function buildCompteResultatData(Carbon $debut, Carbon $fin, string $periode): array
    {
        $totaux = $this->computeCompteResultatTotals($debut, $fin);

        $previousStart = $debut->copy()->subDays($debut->diffInDays($fin) + 1);
        $previousEnd = $debut->copy()->subDay()->endOfDay();
        $resultatPrecedent = $previousEnd->lt($previousStart)
            ? 0
            : $this->buildCompteResultatDataSnapshot($previousStart, $previousEnd);

        $variation = $totaux['resultat_net'] - $resultatPrecedent;
        $variationPourcentage = $this->computeVariationPercentage(
            (float) $totaux['resultat_net'],
            (float) $resultatPrecedent
        );
        $graphiqueMensuel = $this->getGraphiqueMensuel($debut->copy()->startOfMonth(), $fin->copy()->endOfMonth());

        return [
            'total_produits' => $totaux['total_produits'],
            'total_charges' => $totaux['total_charges'],
            'marge_brute' => $totaux['marge_brute'],
            'resultat_exploitation' => $totaux['resultat_exploitation'],
            'resultat_financier' => 0,
            'resultat_net' => $totaux['resultat_net'],
            'produits' => $totaux['produits'],
            'charges' => $totaux['charges'],
            'periode' => $periode,
            'debut' => $debut,
            'fin' => $fin,
            'resultat_precedent' => $resultatPrecedent,
            'variation' => $variation,
            'variation_pourcentage' => $variationPourcentage,
            'graphique_mensuel' => $graphiqueMensuel,
            'evolution_mensuelle' => $graphiqueMensuel,
            'chiffre_affaires' => $totaux['total_produits'],
            'marge_brute_pourcentage' => $totaux['total_produits'] != 0 ? ($totaux['marge_brute'] / $totaux['total_produits']) * 100 : 0,
            'taux_rentabilite' => $totaux['total_produits'] != 0 ? ($totaux['resultat_net'] / $totaux['total_produits']) * 100 : 0,
            'resultat_net_pourcentage' => $totaux['total_produits'] != 0 ? ($totaux['resultat_net'] / $totaux['total_produits']) * 100 : 0,
            'type_affichage' => $periode,
            'base_comptable' => 'tresorerie',
        ];
    }

    private function buildIndicateursFinanciersData(Request $request): array
    {
        [$debut, $fin, $periode] = $this->resolveCompteResultatPeriod(
            $request->input('periode', 'annee'),
            $request->input('date_debut'),
            $request->input('date_fin')
        );

        $totaux = $this->computeCompteResultatTotals($debut, $fin);
        $encaissements = $this->sumEncaissements($debut, $fin);
        $tresorerieDisponible = $this->getTresorerieDisponible($debut, $fin);
        $creancesClients = $this->getCreancesClients($debut, $fin);
        $dettesFournisseurs = $this->getDettesFournisseurs($debut, $fin);
        $graphiqueMensuel = $this->getGraphiqueMensuel($debut->copy()->startOfMonth(), $fin->copy()->endOfMonth());
        $masseSalariale = $this->sumPersonnelPaies($debut, $fin, 'salaire_brut');
        $chargesSocialesEtFiscales = $this->sumPersonnelPaies($debut, $fin, 'cnps')
            + $this->sumPersonnelPaies($debut, $fin, 'impot')
            + $this->sumPersonnelPaies($debut, $fin, 'autres_retenues');
        $tvaCollectee = $this->calculateTvaCollecteeEstimate($debut, $fin);
        $tvaDeductible = $this->calculateTvaDeductibleEstimate($debut, $fin);
        $tfpEstimee = round($masseSalariale * 0.012, 2);
        $apprentissageEstime = round($masseSalariale * 0.004, 2);

        $previousStart = $debut->copy()->subDays($debut->diffInDays($fin) + 1);
        $previousEnd = $debut->copy()->subDay()->endOfDay();
        $previousTotals = $previousEnd->lt($previousStart)
            ? ['total_produits' => 0, 'resultat_net' => 0]
            : $this->computeCompteResultatTotals($previousStart, $previousEnd);
        
    // Déterminer si on a des données année N-1
    $hasPreviousData = $previousEnd->gte($previousStart) && 
              ($previousTotals['total_produits'] ?? 0) > 0;

        $chiffreAffaires = $totaux['total_produits'];
        $resultatNet = $totaux['resultat_net'];
        $margeNette = $chiffreAffaires != 0 ? ($resultatNet / $chiffreAffaires) * 100 : 0;
        $poidsCharges = $chiffreAffaires != 0 ? ($totaux['total_charges'] / $chiffreAffaires) * 100 : 0;
        $liquiditeImmediate = $dettesFournisseurs != 0 ? ($tresorerieDisponible / $dettesFournisseurs) * 100 : 0;
        $tauxRecouvrement = $chiffreAffaires != 0 ? ($encaissements / $chiffreAffaires) * 100 : 0;
        $besoinFondsRoulement = $creancesClients - $dettesFournisseurs;
        $variationCa = $chiffreAffaires - ($previousTotals['total_produits'] ?? 0);
        $variationResultat = $resultatNet - ($previousTotals['resultat_net'] ?? 0);
        $dureePeriode = max(1, $debut->diffInDays($fin) + 1);
        $delaiEncaissement = $chiffreAffaires != 0 ? ($creancesClients / $chiffreAffaires) * $dureePeriode : 0;
        $couvertureDettesCourtes = $dettesFournisseurs != 0 ? (($tresorerieDisponible + $creancesClients) / $dettesFournisseurs) * 100 : 0;
        $autonomieTresorerie = $totaux['total_charges'] != 0 ? ($tresorerieDisponible / $totaux['total_charges']) * 100 : 0;
        $productiviteSalariale = $masseSalariale != 0 ? ($chiffreAffaires / $masseSalariale) * 100 : 0;
        $soldeTva = $tvaCollectee - $tvaDeductible;
        $pressionFiscale = $chiffreAffaires != 0
            ? (($chargesSocialesEtFiscales + max(0, $soldeTva) + $tfpEstimee + $apprentissageEstime) / $chiffreAffaires) * 100
            : 0;
        $isEstime = max(0, $resultatNet) * 0.25;

        return [
            'periode' => $periode,
            'debut' => $debut,
            'fin' => $fin,
            'chiffre_affaires' => $chiffreAffaires,
            'encaissements' => $encaissements,
            'total_charges' => $totaux['total_charges'],
            'resultat_net' => $resultatNet,
            'ca_locations' => $totaux['ca_locations'] ?? 0,
            'ca_ventes' => $totaux['ca_ventes'] ?? 0,
            'ebe' => $totaux['ebe'] ?? 0,
            'total_charges_exploitation' => $totaux['total_charges_exploitation'] ?? 0,
            'has_previous_data' => $hasPreviousData,
            'ca_locations_n1' => $hasPreviousData ? ($previousTotals['ca_locations'] ?? 0) : null,
            'ca_ventes_n1' => $hasPreviousData ? ($previousTotals['ca_ventes'] ?? 0) : null,
            'ebe_n1' => $hasPreviousData ? ($previousTotals['ebe'] ?? 0) : null,
            'variation_ca_locations' => $hasPreviousData ? (($totaux['ca_locations'] ?? 0) - ($previousTotals['ca_locations'] ?? 0)) : null,
            'variation_ca_ventes' => $hasPreviousData ? (($totaux['ca_ventes'] ?? 0) - ($previousTotals['ca_ventes'] ?? 0)) : null,
            'variation_ebe' => $hasPreviousData ? (($totaux['ebe'] ?? 0) - ($previousTotals['ebe'] ?? 0)) : null,
            'tresorerie_disponible' => $tresorerieDisponible,
            'creances_clients' => $creancesClients,
            'dettes_fournisseurs' => $dettesFournisseurs,
            'besoin_fonds_roulement' => $besoinFondsRoulement,
            'marge_nette' => $margeNette,
            'poids_charges' => $poidsCharges,
            'liquidite_immediate' => $liquiditeImmediate,
            'taux_recouvrement' => $tauxRecouvrement,
            'variation_ca' => $variationCa,
            'variation_resultat' => $variationResultat,
            'produits' => $totaux['produits'],
            'charges' => $totaux['charges'],
            'graphique_mensuel' => $graphiqueMensuel,
            'masse_salariale' => $masseSalariale,
            'charges_sociales_fiscales' => $chargesSocialesEtFiscales,
            'tva_collectee_estimee' => $tvaCollectee,
            'tva_deductible_estimee' => $tvaDeductible,
            'solde_tva_estime' => $soldeTva,
            'tfp_estimee' => $tfpEstimee,
            'apprentissage_estime' => $apprentissageEstime,
            'is_estime' => $isEstime,
            'pression_fiscale' => $pressionFiscale,
            'delai_encaissement_jours' => $delaiEncaissement,
            'couverture_dettes_courtes' => $couvertureDettesCourtes,
            'autonomie_tresorerie' => $autonomieTresorerie,
            'productivite_salariale' => $productiviteSalariale,
            'ratios_dgi' => [
                [
                    'libelle' => 'TVA collectée estimée',
                    'valeur' => $tvaCollectee,
                    'format' => 'currency',
                    'explication' => 'Montant estimatif de TVA sur ventes et recettes de la période au taux standard de 18 %.',
                ],
                [
                    'libelle' => 'TVA déductible estimée',
                    'valeur' => $tvaDeductible,
                    'format' => 'currency',
                    'explication' => 'TVA potentiellement récupérable sur les dépenses marquées comme déductibles.',
                ],
                [
                    'libelle' => 'Solde TVA à reverser',
                    'valeur' => max(0, $soldeTva),
                    'format' => 'currency',
                    'explication' => 'Estimation du montant de TVA restant à reverser à la DGI si le solde est positif.',
                ],
                [
                    'libelle' => 'IS estimatif',
                    'valeur' => $isEstime,
                    'format' => 'currency',
                    'explication' => 'Projection d\'impôt sur les sociétés à 25 % appliquée au résultat net positif de la période.',
                ],
                [
                    'libelle' => 'TFP estimative',
                    'valeur' => $tfpEstimee,
                    'format' => 'currency',
                    'explication' => 'Taxe de formation professionnelle continue calculée à 1,2 % de la masse salariale.',
                ],
                [
                    'libelle' => 'Taxe apprentissage estimative',
                    'valeur' => $apprentissageEstime,
                    'format' => 'currency',
                    'explication' => 'Taxe d\'apprentissage calculée à 0,4 % de la masse salariale.',
                ],
                [
                    'libelle' => 'Pression fiscale et sociale',
                    'valeur' => $pressionFiscale,
                    'format' => 'percent',
                    'explication' => 'Poids estimatif des charges sociales, TVA nette et taxes formation rapporté au chiffre d\'affaires.',
                ],
            ],
            'ratios_syscohada' => [
                [
                    'libelle' => 'Marge nette',
                    'valeur' => $margeNette,
                    'format' => 'percent',
                    'explication' => 'Résultat net rapporté au chiffre d\'affaires, indicateur clé de rentabilité SYSCOHADA.',
                ],
                [
                    'libelle' => 'Poids des charges',
                    'valeur' => $poidsCharges,
                    'format' => 'percent',
                    'explication' => 'Part des charges dans le chiffre d\'affaires sur la période observée.',
                ],
                [
                    'libelle' => 'Liquidité immédiate',
                    'valeur' => $liquiditeImmediate,
                    'format' => 'percent',
                    'explication' => 'Capacité de trésorerie disponible à couvrir les dettes fournisseurs de court terme.',
                ],
                [
                    'libelle' => 'Couverture des dettes CT',
                    'valeur' => $couvertureDettesCourtes,
                    'format' => 'percent',
                    'explication' => 'Couverture des dettes court terme par la trésorerie disponible et les créances clients.',
                ],
                [
                    'libelle' => 'Autonomie de trésorerie',
                    'valeur' => $autonomieTresorerie,
                    'format' => 'percent',
                    'explication' => 'Niveau de trésorerie disponible comparé aux charges totales de la période.',
                ],
                [
                    'libelle' => 'Délai moyen d\'encaissement',
                    'valeur' => $delaiEncaissement,
                    'format' => 'days',
                    'explication' => 'Nombre de jours estimé pour convertir les créances clients en encaissements.',
                ],
                [
                    'libelle' => 'Productivité salariale',
                    'valeur' => $productiviteSalariale,
                    'format' => 'percent',
                    'explication' => 'Chiffre d\'affaires généré rapporté à la masse salariale brute.',
                ],
            ],
            'points_attention' => [
                'Les ratios DGI sont présentés comme des estimations opérationnelles et ne remplacent pas une liasse fiscale.',
                'Les ratios SYSCOHADA synthétisent la rentabilité, la liquidité et la structure court terme sur base de trésorerie.',
                'Les montants TVA dépendent de la qualité de saisie des colonnes de TVA et des dépenses déductibles.',
            ],
        ];
    }

    private function buildAnalyseActiviteData(Request $request): array
    {
        $anneeN = (int) $request->input('annee', Carbon::now()->year);
        if ($anneeN < 2000 || $anneeN > 2100) {
            $anneeN = Carbon::now()->year;
        }

        $debutN = Carbon::create($anneeN, 1, 1)->startOfDay();
        $finN = Carbon::create($anneeN, 12, 31)->endOfDay();
        $anneeN1 = $anneeN - 1;
        $debutN1 = Carbon::create($anneeN1, 1, 1)->startOfDay();
        $finN1 = Carbon::create($anneeN1, 12, 31)->endOfDay();

        $sigN = $this->buildSigCafMetrics($debutN, $finN);
        $sigN1 = $this->buildSigCafMetrics($debutN1, $finN1);

        $lignes = [
            ['key' => 'chiffre_affaires', 'libelle' => 'Chiffre d\'affaires'],
            ['key' => 'consommations_intermediaires', 'libelle' => 'Consommations intermediaires'],
            ['key' => 'valeur_ajoutee', 'libelle' => 'Valeur ajoutee'],
            ['key' => 'charges_personnel', 'libelle' => 'Charges de personnel'],
            ['key' => 'impots_taxes_sociales', 'libelle' => 'Impots, taxes et charges sociales'],
            ['key' => 'ebe', 'libelle' => 'EBE (Excedent Brut d\'Exploitation)'],
            ['key' => 'dotations', 'libelle' => 'Dotations (amortissements/provisions)'],
            ['key' => 'reprises', 'libelle' => 'Reprises sur provisions'],
            ['key' => 'resultat_net', 'libelle' => 'Resultat net'],
            ['key' => 'caf', 'libelle' => 'Capacite d\'Auto Financement (CAF)'],
        ];

        return [
            'annee_n' => $anneeN,
            'annee_n1' => $anneeN1,
            'debut_n' => $debutN,
            'fin_n' => $finN,
            'debut_n1' => $debutN1,
            'fin_n1' => $finN1,
            'n' => $sigN,
            'n1' => $sigN1,
            'lignes' => $lignes,
        ];
    }

    private function resolveAnnualComparison(Request $request): array
    {
        $requestedYear = $request->input('annee');

        if ($requestedYear === null || $requestedYear === '') {
            $anneeN = $this->resolveLatestActivityYear();
        } else {
            $anneeN = (int) $requestedYear;
        }

        if ($anneeN < 2000 || $anneeN > 2100) {
            $anneeN = $this->resolveLatestActivityYear();
        }

        $anneeN1 = $anneeN - 1;
        $debutN = Carbon::create($anneeN, 1, 1)->startOfDay();
        $finN = Carbon::create($anneeN, 12, 31)->endOfDay();
        $debutN1 = Carbon::create($anneeN1, 1, 1)->startOfDay();
        $finN1 = Carbon::create($anneeN1, 12, 31)->endOfDay();

        return [$anneeN, $anneeN1, $debutN, $finN, $debutN1, $finN1];
    }

    private function resolveLatestActivityYear(): int
    {
        $years = [];

        if (Schema::hasTable('recettes') && Schema::hasColumn('recettes', 'date_recette')) {
            $years[] = (int) DB::table('recettes')->selectRaw('MAX(YEAR(date_recette)) as y')->value('y');
        }

        if (Schema::hasTable('depenses') && Schema::hasColumn('depenses', 'date_depense')) {
            $years[] = (int) DB::table('depenses')->selectRaw('MAX(YEAR(date_depense)) as y')->value('y');
        }

        if (Schema::hasTable('depense_caisses')) {
            $dateColumn = Schema::hasColumn('depense_caisses', 'date_depense')
                ? 'date_depense'
                : (Schema::hasColumn('depense_caisses', 'created_at') ? 'created_at' : null);

            if ($dateColumn !== null) {
                $years[] = (int) DB::table('depense_caisses')->selectRaw("MAX(YEAR({$dateColumn})) as y")->value('y');
            }
        }

        if (Schema::hasTable('factures') && Schema::hasColumn('factures', 'date_facture')) {
            $years[] = (int) DB::table('factures')->selectRaw('MAX(YEAR(date_facture)) as y')->value('y');
        }

        if (Schema::hasTable('encaissements') && Schema::hasColumn('encaissements', 'date_encaissement')) {
            $years[] = (int) DB::table('encaissements')->selectRaw('MAX(YEAR(date_encaissement)) as y')->value('y');
        }

        $years = array_values(array_filter($years, fn ($value) => $value >= 2000 && $value <= 2100));

        return !empty($years) ? max($years) : Carbon::now()->year;
    }

    private function buildAnalyseRentabiliteData(Request $request): array
    {
        [$anneeN, $anneeN1, $debutN, $finN, $debutN1, $finN1] = $this->resolveAnnualComparison($request);

        $sigN = $this->buildSigCafMetrics($debutN, $finN);
        $sigN1 = $this->buildSigCafMetrics($debutN1, $finN1);
        $caN = (float) ($sigN['chiffre_affaires'] ?? 0);
        $caN1 = (float) ($sigN1['chiffre_affaires'] ?? 0);
        $resultatN = (float) ($sigN['resultat_net'] ?? 0);
        $resultatN1 = (float) ($sigN1['resultat_net'] ?? 0);
        $ebeN = (float) ($sigN['ebe'] ?? 0);
        $ebeN1 = (float) ($sigN1['ebe'] ?? 0);

        return [
            'annee_n' => $anneeN,
            'annee_n1' => $anneeN1,
            'n' => [
                'chiffre_affaires' => $caN,
                'charges_totales' => (float) (($sigN['consommations_intermediaires'] ?? 0) + ($sigN['charges_personnel'] ?? 0) + ($sigN['impots_taxes_sociales'] ?? 0)),
                'ebe' => $ebeN,
                'resultat_net' => $resultatN,
                'marge_nette' => $caN != 0 ? ($resultatN / $caN) * 100 : 0,
                'rentabilite_exploitation' => $caN != 0 ? ($ebeN / $caN) * 100 : 0,
                'caf' => (float) ($sigN['caf'] ?? 0),
            ],
            'n1' => [
                'chiffre_affaires' => $caN1,
                'charges_totales' => (float) (($sigN1['consommations_intermediaires'] ?? 0) + ($sigN1['charges_personnel'] ?? 0) + ($sigN1['impots_taxes_sociales'] ?? 0)),
                'ebe' => $ebeN1,
                'resultat_net' => $resultatN1,
                'marge_nette' => $caN1 != 0 ? ($resultatN1 / $caN1) * 100 : 0,
                'rentabilite_exploitation' => $caN1 != 0 ? ($ebeN1 / $caN1) * 100 : 0,
                'caf' => (float) ($sigN1['caf'] ?? 0),
            ],
            'lignes' => [
                ['key' => 'chiffre_affaires', 'libelle' => 'Chiffre d\'affaires', 'format' => 'currency'],
                ['key' => 'charges_totales', 'libelle' => 'Charges totales', 'format' => 'currency'],
                ['key' => 'ebe', 'libelle' => 'EBE', 'format' => 'currency'],
                ['key' => 'resultat_net', 'libelle' => 'Resultat net', 'format' => 'currency'],
                ['key' => 'marge_nette', 'libelle' => 'Marge nette', 'format' => 'percent'],
                ['key' => 'rentabilite_exploitation', 'libelle' => 'Rentabilite d\'exploitation', 'format' => 'percent'],
                ['key' => 'caf', 'libelle' => 'CAF', 'format' => 'currency'],
            ],
        ];
    }

    private function buildAnalyseVariationTresoData(Request $request): array
    {
        [$anneeN, $anneeN1, $debutN, $finN, $debutN1, $finN1] = $this->resolveAnnualComparison($request);

        // Les flux d'encaissement réels proviennent à la fois des encaissements et des recettes.
        $encN = $this->sumEncaissements($debutN, $finN) + $this->sumRecettes($debutN, $finN);
        $decN = $this->sumDepenses($debutN, $finN) + $this->sumVehicleEntries('expense', $debutN, $finN) + $this->sumPersonnelPaies($debutN, $finN, 'salaire_brut');
        $encN1 = $this->sumEncaissements($debutN1, $finN1) + $this->sumRecettes($debutN1, $finN1);
        $decN1 = $this->sumDepenses($debutN1, $finN1) + $this->sumVehicleEntries('expense', $debutN1, $finN1) + $this->sumPersonnelPaies($debutN1, $finN1, 'salaire_brut');

        $dettesN = $this->getDettesFournisseurs($debutN, $finN);
        $dettesN1 = $this->getDettesFournisseurs($debutN1, $finN1);

        return [
            'annee_n' => $anneeN,
            'annee_n1' => $anneeN1,
            'n' => [
                'encaissements' => $encN,
                'decaissements' => $decN,
                'flux_net_tresorerie' => $encN - $decN,
                'tresorerie_disponible' => $this->getTresorerieDisponible($debutN, $finN),
                'couverture_dettes' => $dettesN != 0 ? (($encN - $decN) / $dettesN) * 100 : 0,
            ],
            'n1' => [
                'encaissements' => $encN1,
                'decaissements' => $decN1,
                'flux_net_tresorerie' => $encN1 - $decN1,
                'tresorerie_disponible' => $this->getTresorerieDisponible($debutN1, $finN1),
                'couverture_dettes' => $dettesN1 != 0 ? (($encN1 - $decN1) / $dettesN1) * 100 : 0,
            ],
            'lignes' => [
                ['key' => 'encaissements', 'libelle' => 'Encaissements', 'format' => 'currency'],
                ['key' => 'decaissements', 'libelle' => 'Decaissements', 'format' => 'currency'],
                ['key' => 'flux_net_tresorerie', 'libelle' => 'Flux net de tresorerie', 'format' => 'currency'],
                ['key' => 'tresorerie_disponible', 'libelle' => 'Tresorerie disponible', 'format' => 'currency'],
                ['key' => 'couverture_dettes', 'libelle' => 'Couverture des dettes', 'format' => 'percent'],
            ],
        ];
    }

    private function computeVariationPercentage(float $currentValue, float $previousValue): float
    {
        if ($previousValue == 0.0) {
            if ($currentValue > 0.0) {
                return 100.0;
            }

            if ($currentValue < 0.0) {
                return -100.0;
            }

            return 0.0;
        }

        return (($currentValue - $previousValue) / abs($previousValue)) * 100;
    }

    private function buildAnalyseVariationDetteData(Request $request): array
    {
        [$anneeN, $anneeN1, $debutN, $finN, $debutN1, $finN1] = $this->resolveAnnualComparison($request);

        $caN = (float) ($this->computeCompteResultatTotals($debutN, $finN)['total_produits'] ?? 0);
        $caN1 = (float) ($this->computeCompteResultatTotals($debutN1, $finN1)['total_produits'] ?? 0);
        $dettesN = $this->getDettesFournisseurs($debutN, $finN);
        $dettesN1 = $this->getDettesFournisseurs($debutN1, $finN1);
        $creancesN = $this->getCreancesClients($debutN, $finN);
        $creancesN1 = $this->getCreancesClients($debutN1, $finN1);

        return [
            'annee_n' => $anneeN,
            'annee_n1' => $anneeN1,
            'n' => [
                'dettes_fournisseurs' => $dettesN,
                'creances_clients' => $creancesN,
                'bfr' => $creancesN - $dettesN,
                'ratio_dette_ca' => $caN != 0 ? ($dettesN / $caN) * 100 : 0,
                'ecart_creances_dettes' => $creancesN - $dettesN,
            ],
            'n1' => [
                'dettes_fournisseurs' => $dettesN1,
                'creances_clients' => $creancesN1,
                'bfr' => $creancesN1 - $dettesN1,
                'ratio_dette_ca' => $caN1 != 0 ? ($dettesN1 / $caN1) * 100 : 0,
                'ecart_creances_dettes' => $creancesN1 - $dettesN1,
            ],
            'lignes' => [
                ['key' => 'dettes_fournisseurs', 'libelle' => 'Dettes fournisseurs', 'format' => 'currency'],
                ['key' => 'creances_clients', 'libelle' => 'Creances clients', 'format' => 'currency'],
                ['key' => 'bfr', 'libelle' => 'Besoin en fonds de roulement', 'format' => 'currency'],
                ['key' => 'ratio_dette_ca', 'libelle' => 'Ratio dette / CA', 'format' => 'percent'],
                ['key' => 'ecart_creances_dettes', 'libelle' => 'Ecart creances - dettes', 'format' => 'currency'],
            ],
        ];
    }

    private function buildSigCafMetrics(Carbon $debut, Carbon $fin): array
    {
        $totaux = $this->computeCompteResultatTotals($debut, $fin);

        $consommationsIntermediaires = $this->sumDepenses($debut, $fin) + $this->sumVehicleEntries('expense', $debut, $fin);
        $chiffreAffaires = (float) ($totaux['total_produits'] ?? 0);
        $valeurAjoutee = $chiffreAffaires - $consommationsIntermediaires;
        $chargesPersonnel = $this->sumPersonnelPaies($debut, $fin, 'salaire_brut');
        $impotsTaxesSociales = $this->sumPersonnelPaies($debut, $fin, 'cnps')
            + $this->sumPersonnelPaies($debut, $fin, 'impot')
            + $this->sumPersonnelPaies($debut, $fin, 'autres_retenues');
        $ebe = $valeurAjoutee - $chargesPersonnel - $impotsTaxesSociales;

        $dotations = $this->estimateNonCashCharges($debut, $fin);
        $reprises = $this->estimateNonCashProducts($debut, $fin);

        $resultatNet = (float) ($totaux['resultat_net'] ?? 0);
        $caf = $resultatNet + $dotations - $reprises;

        return [
            'chiffre_affaires' => round($chiffreAffaires, 2),
            'consommations_intermediaires' => round($consommationsIntermediaires, 2),
            'valeur_ajoutee' => round($valeurAjoutee, 2),
            'charges_personnel' => round($chargesPersonnel, 2),
            'impots_taxes_sociales' => round($impotsTaxesSociales, 2),
            'ebe' => round($ebe, 2),
            'dotations' => round($dotations, 2),
            'reprises' => round($reprises, 2),
            'resultat_net' => round($resultatNet, 2),
            'caf' => round($caf, 2),
        ];
    }

    private function estimateNonCashCharges(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('depenses')) {
            return 0;
        }

        $keywords = ['amort', 'provision', 'depreciation', 'depreciat'];
        $query = $this->buildDepensesPeriodQuery($debut, $fin);

        if (!Schema::hasColumn('depenses', 'libelle') && !Schema::hasColumn('depenses', 'notes')) {
            return 0;
        }

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                if (Schema::hasColumn('depenses', 'libelle')) {
                    $q->orWhereRaw('LOWER(libelle) LIKE ?', ['%' . $keyword . '%']);
                }
                if (Schema::hasColumn('depenses', 'notes')) {
                    $q->orWhereRaw('LOWER(notes) LIKE ?', ['%' . $keyword . '%']);
                }
            }
        });

        return (float) $query->sum('montant');
    }

    private function estimateNonCashProducts(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('recettes')) {
            return 0;
        }

        $query = DB::table('recettes')->whereBetween('date_recette', [$debut, $fin]);
        $keywords = ['reprise', 'provision'];

        if (!Schema::hasColumn('recettes', 'libelle') && !Schema::hasColumn('recettes', 'description')) {
            return 0;
        }

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                if (Schema::hasColumn('recettes', 'libelle')) {
                    $q->orWhereRaw('LOWER(libelle) LIKE ?', ['%' . $keyword . '%']);
                }
                if (Schema::hasColumn('recettes', 'description')) {
                    $q->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%']);
                }
            }
        });

        return (float) $query->sum('montant');
    }

    private function buildCompteResultatDataSnapshot(Carbon $debut, Carbon $fin): float
    {
        return $this->computeCompteResultatTotals($debut, $fin)['resultat_net'];
    }

    private function computeCompteResultatTotals(Carbon $debut, Carbon $fin): array
    {
        // === REVENUS DÉTAILLÉS PAR MÉTIER ===
        $revenusLocations = $this->sumRevenusLocations($debut, $fin);
        $revenusVentes = $this->sumRevenusVentes($debut, $fin);
        
        // === REVENUS AUXILIAIRES ===
        $encaissements = $this->sumEncaissements($debut, $fin);
        $revenusVehicules = $this->sumVehicleEntries('revenue', $debut, $fin);
        // Recettes (table recettes) — source principale d'encaissements réels
        $revenusRecettes = $this->sumRecettes($debut, $fin);
        $facturesFallback = ($encaissements + $revenusVehicules + $revenusLocations + $revenusVentes + $revenusRecettes) === 0 ? $this->sumFactures($debut, $fin) : 0;

        $depensesGenerales = $this->sumDepenses($debut, $fin);
        $chargesVehicules = $this->sumVehicleEntries('expense', $debut, $fin);
        $chargesSalariales = $this->sumPersonnelPaies($debut, $fin, 'salaire_brut');
        $chargesSocialesEtFiscales = $this->sumPersonnelPaies($debut, $fin, 'cnps')
            + $this->sumPersonnelPaies($debut, $fin, 'impot')
            + $this->sumPersonnelPaies($debut, $fin, 'autres_retenues');

        $produits = array_filter([
            'Location d\'engins et véhicules' => $revenusLocations,
            'Ventes quincaillerie et magasin' => $revenusVentes,
            'Recettes encaissées' => $revenusRecettes,
            'Encaissements validés' => $encaissements,
            'Revenus d\'exploitation véhicules' => $revenusVehicules,
            'Factures émises (fallback)' => $facturesFallback,
        ], fn ($value) => (float) $value > 0);

        $charges = array_filter([
            'Dépenses générales' => $depensesGenerales,
            'Charges d\'exploitation véhicules' => $chargesVehicules,
            'Charges salariales' => $chargesSalariales,
            'Charges sociales et fiscales' => $chargesSocialesEtFiscales,
        ], fn ($value) => (float) $value > 0);

        $totalProduits = array_sum($produits);
            $totalChargesExploitation = $depensesGenerales + $chargesVehicules + $chargesSalariales + $chargesSocialesEtFiscales;
            $ebe = $totalProduits - $totalChargesExploitation;
        
        $totalCharges = array_sum($charges);
        $margeBrute = $totalProduits - ($depensesGenerales + $chargesVehicules);
        $resultatExploitation = $totalProduits - $totalCharges;

        return [
            'produits' => $produits,
            'charges' => $charges,
                        'ca_locations' => $revenusLocations,
                        'ca_ventes' => $revenusVentes,
            'total_produits' => $totalProduits,
                        'total_charges_exploitation' => $totalChargesExploitation,
                        'ebe' => $ebe,
            'total_charges' => $totalCharges,
            'marge_brute' => $margeBrute,
            'resultat_exploitation' => $resultatExploitation,
            'resultat_net' => $resultatExploitation,
        ];
    }

    private function sumRecettes(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('recettes')) {
            return 0;
        }

        try {
            $query = DB::table('recettes')
                ->whereBetween('date_recette', [$debut->toDateString(), $fin->toDateString()]);

            // Exclure les recettes déjà liées à une facture pour éviter les doublons
            if (Schema::hasColumn('recettes', 'facture_id')) {
                $query->whereNull('facture_id');
            }

            return (float) $query->sum('montant');
        } catch (\Exception $e) {
            Log::warning('Erreur calcul recettes', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    private function sumEncaissements(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('encaissements')) {
            return 0;
        }

        return (float) DB::table('encaissements')
            ->whereBetween('date_encaissement', [$debut, $fin])
            ->where(function ($query) {
                $query->where('statut', 'valide')
                      ->orWhereNull('statut');
            })
            ->sum('montant');
    }

    private function sumVehicleEntries(string $type, Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('vehicle_financial_entries')) {
            return 0;
        }

        $typeColumn = $this->resolveVehicleEntryTypeColumn();
        $dateColumn = $this->resolveVehicleEntryDateColumn();
        if ($typeColumn === null || $dateColumn === null) {
            return 0;
        }

        $entryType = $this->mapVehicleEntryTypeValue($type, $typeColumn);
        return (float) DB::table('vehicle_financial_entries')
            ->where($typeColumn, $entryType)
            ->whereBetween($dateColumn, [$debut, $fin])
            ->sum('amount');
    }

    private function resolveVehicleEntryTypeColumn(): ?string
    {
        if (!Schema::hasTable('vehicle_financial_entries')) {
            return null;
        }

        if (Schema::hasColumn('vehicle_financial_entries', 'entry_type')) {
            return 'entry_type';
        }

        if (Schema::hasColumn('vehicle_financial_entries', 'type')) {
            return 'type';
        }

        return null;
    }

    private function resolveVehicleEntryDateColumn(): ?string
    {
        if (!Schema::hasTable('vehicle_financial_entries')) {
            return null;
        }

        if (Schema::hasColumn('vehicle_financial_entries', 'entry_date')) {
            return 'entry_date';
        }

        if (Schema::hasColumn('vehicle_financial_entries', 'transaction_date')) {
            return 'transaction_date';
        }

        return null;
    }

    private function mapVehicleEntryTypeValue(string $type, string $typeColumn): string
    {
        if ($typeColumn === 'entry_type') {
            return $type === 'expense' ? 'charge' : $type;
        }

        // Schéma legacy: values are usually revenue/expense in `type`.
        return $type === 'charge' ? 'expense' : $type;
    }

    private function sumFactures(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('factures')) {
            return 0;
        }

        return (float) DB::table('factures')
            ->whereBetween('date_facture', [$debut, $fin])
            ->sum('montant_ttc');
    }

    private function sumDepenses(Carbon $debut, Carbon $fin): float
    {
        $total = 0.0;

        if (Schema::hasTable('depenses')) {
            $total += (float) $this->buildDepensesPeriodQuery($debut, $fin)->sum('montant');
        }

        if (Schema::hasTable('depense_caisses')) {
            $dateColumn = Schema::hasColumn('depense_caisses', 'date_depense')
                ? 'date_depense'
                : (Schema::hasColumn('depense_caisses', 'created_at') ? 'created_at' : null);

            if ($dateColumn !== null) {
                $query = DB::table('depense_caisses')
                    ->whereBetween($dateColumn, [$debut, $fin]);

                if (Schema::hasColumn('depense_caisses', 'statut')) {
                    $query->whereNotIn('statut', ['rejetee', 'annulee']);
                }

                // Si une dépense de trésorerie est déjà liée à une ligne `depenses`,
                // on l'exclut pour éviter le double comptage.
                if (Schema::hasColumn('depense_caisses', 'expense_id') && Schema::hasTable('depenses')) {
                    $query->whereNull('expense_id');
                }

                if (Schema::hasColumn('depense_caisses', 'deleted_at')) {
                    $query->whereNull('deleted_at');
                }

                $total += (float) $query->sum('montant');
            }
        }

        return $total;
    }

    private function sumPersonnelPaies(Carbon $debut, Carbon $fin, string $column): float
    {
        if (!Schema::hasTable('personnel_paies') || !Schema::hasColumn('personnel_paies', $column)) {
            return 0;
        }

        return (float) DB::table('personnel_paies')
            ->whereBetween('date_paie', [$debut, $fin])
            ->sum($column);
    }

    private function getTresorerieDisponible(Carbon $debut, Carbon $fin): float
    {
        $soldeBancaires = Schema::hasTable('compte_bancaires') ? (float) DB::table('compte_bancaires')->sum('solde') : 0;
        $soldeCaisses = Schema::hasTable('caisses') ? (float) DB::table('caisses')->sum('solde_actuel') : 0;

        return $soldeBancaires + $soldeCaisses + $this->sumEncaissements($debut, $fin);
    }

    private function getCreancesClients(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('factures')) {
            return 0;
        }

        $baseQuery = DB::table('factures')
            ->whereBetween('date_facture', [$debut, $fin]);

        if (Schema::hasColumn('factures', 'deleted_at')) {
            $baseQuery->whereNull('deleted_at');
        }

        if (Schema::hasColumn('factures', 'montant_restant')) {
            $restant = (float) (clone $baseQuery)
                ->where('montant_restant', '>', 0)
                ->sum('montant_restant');

            if ($restant > 0) {
                return $restant;
            }
        }

        if (Schema::hasColumn('factures', 'statut')) {
            $creancesParStatut = (float) (clone $baseQuery)
                ->whereIn('statut', ['impayee', 'en_retard', 'en_attente', 'partielle', 'partiellement_payee'])
                ->sum('montant_ttc');

            if ($creancesParStatut > 0) {
                return $creancesParStatut;
            }
        }

        if (Schema::hasColumn('factures', 'montant_paye')) {
            $creancesCalculees = (float) (clone $baseQuery)
                ->whereRaw('COALESCE(montant_ttc, 0) > COALESCE(montant_paye, 0)')
                ->selectRaw('SUM(COALESCE(montant_ttc, 0) - COALESCE(montant_paye, 0)) as total')
                ->value('total');

            return max(0, $creancesCalculees);
        }

        return 0;
    }

    private function getDettesFournisseurs(Carbon $debut, Carbon $fin): float
    {
        $depensesImpayees = $this->getDepensesEnAttente($debut, $fin);

        $chargesVehicules = 0;
        if (Schema::hasTable('vehicle_financial_entries')) {
            $typeColumn = $this->resolveVehicleEntryTypeColumn();
            $dateColumn = $this->resolveVehicleEntryDateColumn();
            if ($typeColumn === null || $dateColumn === null) {
                return $depensesImpayees;
            }

            $chargesVehicules = (float) DB::table('vehicle_financial_entries')
                ->where($typeColumn, $this->mapVehicleEntryTypeValue('expense', $typeColumn))
                ->whereBetween($dateColumn, [$debut, $fin])
                ->sum('amount');
        }

        return $depensesImpayees + $chargesVehicules;
    }

    private function buildDepensesPeriodQuery(Carbon $debut, Carbon $fin)
    {
        $query = DB::table('depenses')
            ->whereBetween('date_depense', [$debut, $fin]);

        if (Schema::hasColumn('depenses', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query;
    }

    private function getDepensesEnAttente(Carbon $debut, Carbon $fin): float
    {
        $total = 0.0;

        if (Schema::hasTable('depenses')) {
            $query = $this->buildDepensesPeriodQuery($debut, $fin);

            if (Schema::hasColumn('depenses', 'statut')) {
                $total += (float) (clone $query)
                    ->whereIn('statut', ['impayee', 'en_attente', 'en attente', 'non_payee', 'partiellement_payee'])
                    ->sum('montant');
            } elseif (Schema::hasColumn('depenses', 'est_justifie')) {
                $total += (float) (clone $query)
                    ->where('est_justifie', false)
                    ->sum('montant');
            }
        }

        if (Schema::hasTable('depense_caisses')) {
            $dateColumn = Schema::hasColumn('depense_caisses', 'date_depense')
                ? 'date_depense'
                : (Schema::hasColumn('depense_caisses', 'created_at') ? 'created_at' : null);

            if ($dateColumn !== null) {
                $query = DB::table('depense_caisses')
                    ->whereBetween($dateColumn, [$debut, $fin]);

                if (Schema::hasColumn('depense_caisses', 'deleted_at')) {
                    $query->whereNull('deleted_at');
                }

                if (Schema::hasColumn('depense_caisses', 'statut')) {
                    $query->whereIn('statut', ['en_attente', 'en attente', 'brouillon', 'soumise']);
                }

                if (Schema::hasColumn('depense_caisses', 'expense_id') && Schema::hasTable('depenses')) {
                    $query->whereNull('expense_id');
                }

                $total += (float) $query->sum('montant');
            }
        }

        return $total;
    }

    private function getDepensesPrevisionnelles(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('depenses') || !Schema::hasColumn('depenses', 'statut')) {
            return 0;
        }

        return (float) $this->buildDepensesPeriodQuery($debut, $fin)
            ->where('statut', 'previsionnel')
            ->sum('montant');
    }

    private function calculateTvaCollecteeEstimate(Carbon $debut, Carbon $fin): float
    {
        $tva = 0.0;

        if (Schema::hasTable('factures') && Schema::hasColumn('factures', 'tva')) {
            $tva += (float) DB::table('factures')
                ->whereBetween('date_facture', [$debut, $fin])
                ->sum('tva');
        }

        if (Schema::hasTable('recettes')) {
            $ttc = (float) DB::table('recettes')
                ->whereBetween('date_recette', [$debut, $fin])
                ->when(Schema::hasColumn('recettes', 'facture_id'), fn ($query) => $query->whereNull('facture_id'))
                ->sum('montant');
            $tva += $ttc * (18 / 118);
        }

        return round($tva, 2);
    }

    private function calculateTvaDeductibleEstimate(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('depenses')) {
            return 0;
        }

        $query = DB::table('depenses')
            ->whereBetween('date_depense', [$debut, $fin]);

        if (Schema::hasColumn('depenses', 'tva_deductible')) {
            $query->where('tva_deductible', true);
        }

        return round((float) $query->sum('montant') * 0.18, 2);
    }

    /**
     * Rapport bilan
     */
    public function bilan()
    {
        try {
            // Vérifier les tables existantes et utiliser celles disponibles
            $tablesExist = [
                'factures' => Schema::hasTable('factures'),
                'operations' => Schema::hasTable('operations'),
                'depenses' => Schema::hasTable('depenses'),
                'encaissements' => Schema::hasTable('encaissements'),
                'vehicle_financial_entries' => Schema::hasTable('vehicle_financial_entries'),
                'caisses' => Schema::hasTable('caisses'),
                'immobilisations' => Schema::hasTable('immobilisations'),
                'stocks' => Schema::hasTable('stocks'),
                'reserves' => Schema::hasTable('reserves'),
                'emprunts' => Schema::hasTable('emprunts'),
            ];

            // Période par défaut - utiliser l'année en cours pour inclure toutes les données
            $periode = request('periode', 'annee');
            $dateDebut = request('date_debut');
            $dateFin = request('date_fin');

            if ($periode === 'personnalise' && $dateDebut && $dateFin) {
                $debut = Carbon::parse($dateDebut);
                $fin = Carbon::parse($dateFin);
            } elseif ($periode === 'mois') {
                $debut = Carbon::now()->startOfMonth();
                $fin = Carbon::now()->endOfMonth();
            } else {
                // Par défaut, utiliser une période large pour inclure les données historiques
                $debut = Carbon::create(2025, 1, 1); // Depuis janvier 2025
                $fin = Carbon::now()->endOfYear();
            }

            // === ACTIF ===

            // Actif immobilisé (valeurs réelles de la BDD)
            $totalImmobilisations = $tablesExist['immobilisations'] ? DB::table('immobilisations')->sum('valeur_actuelle') : 0;
            $immobilisationsCorporelles = $tablesExist['immobilisations'] ? DB::table('immobilisations')->where('type', 'corporelle')->sum('valeur_actuelle') : 0;
            $materielMobilier = $tablesExist['immobilisations'] ? DB::table('immobilisations')->where('type', 'mobilier')->sum('valeur_actuelle') : 0;

            // Actif circulant
            $stocks = $tablesExist['stocks'] ? DB::table('stocks')->sum('valeur_totale') : 0;

            // Créances clients (factures impayées)
            $creancesClients = 0;
            if ($tablesExist['factures']) {
                $creancesClients = (float) DB::table('factures')
                    ->where('statut', 'en_retard')
                    ->whereBetween('date_facture', [$debut, $fin])
                    ->sum('montant_ttc');
            }

            // Disponibilités
            $soldeBancaires = Schema::hasTable('compte_bancaires') ? (float) DB::table('compte_bancaires')->sum('solde') : 0;
            $soldeCaisses = $tablesExist['caisses'] ? (float) DB::table('caisses')->sum('solde_actuel') : 0;
            $disponibilites = $soldeBancaires + $soldeCaisses;

            // Ajouter les encaissements comme trésorerie disponible
            if ($tablesExist['encaissements']) {
                $encaissementsDisponibles = DB::table('encaissements')
                    ->whereBetween('date_encaissement', [$debut, $fin])
                    ->sum('montant') ?? 0;
                $disponibilites += $encaissementsDisponibles;
            }

            // Ajouter les recettes encaissées comme trésorerie
            if (Schema::hasTable('recettes')) {
                $recettesDisponibles = (float) DB::table('recettes')
                    ->whereBetween('date_recette', [$debut->toDateString(), $fin->toDateString()])
                    ->when(Schema::hasColumn('recettes', 'facture_id'), fn ($q) => $q->whereNull('facture_id'))
                    ->sum('montant');
                $disponibilites += $recettesDisponibles;
            }

            $totalActifCirculant = $stocks + $creancesClients + $disponibilites;
            $totalActif = $totalImmobilisations + $totalActifCirculant;

            // === PASSIF ===

            // Capitaux propres (données réelles)
            $capitalSocial = 0;
            if (Schema::hasTable('entreprise_settings')) {
                $entreprise = DB::table('entreprise_settings')->first();
                $capitalSocial = $entreprise && isset($entreprise->capital_social) ? (float) $entreprise->capital_social : 0;
            }
            $resultat = $this->calculerResultatExercice($debut, $fin);
            $reserves = $tablesExist['reserves'] ? DB::table('reserves')->sum('montant') : 0;
            $totalCapitauxPropres = $capitalSocial + $resultat + $reserves;

            // Dettes
            $dettesFournisseurs = 0;
            if ($tablesExist['depenses']) {
                $dettesFournisseurs = $this->getDepensesEnAttente($debut, $fin);
            }

            // Ajouter les dépenses de vehicle_financial_entries comme dettes
            if ($tablesExist['vehicle_financial_entries']) {
                $typeColumn = $this->resolveVehicleEntryTypeColumn();
                $dateColumn = $this->resolveVehicleEntryDateColumn();
                $vehicleExpenses = ($typeColumn && $dateColumn)
                    ? (DB::table('vehicle_financial_entries')
                        ->where($typeColumn, $this->mapVehicleEntryTypeValue('expense', $typeColumn))
                        ->whereBetween($dateColumn, [$debut, $fin])
                        ->sum('amount') ?? 0)
                    : 0;
                $dettesFournisseurs += $vehicleExpenses;
            }

            // Emprunts bancaires (données réelles)
            $empruntsBancaires = $tablesExist['emprunts'] ? DB::table('emprunts')->where('statut', 'en_cours')->sum('montant_restant') : 0;
            $totalDettes = $dettesFournisseurs + $empruntsBancaires;
            $totalPassif = $totalCapitauxPropres + $totalDettes;

            // Statut de l'exercice
            $statut = $resultat >= 0 ? 'beneficiaire' : 'deficitaire';

            $bilan = [
                'annee' => $debut->format('Y'),
                'periode' => $periode,
                'date_debut' => $debut,
                'date_fin' => $fin,
                'resultat' => $resultat,
                'statut' => $statut,
                'actif' => [
                    'immobilisations' => [
                        'total_immobilisations' => $totalImmobilisations,
                        'immobilisations_corporelles' => $immobilisationsCorporelles,
                        'materiel_mobilier' => $materielMobilier,
                    ],
                    'actif_circulant' => [
                        'total_actif_circulant' => $totalActifCirculant,
                        'stocks' => $stocks,
                        'creances_clients' => $creancesClients,
                        'disponibilites' => $disponibilites,
                    ],
                    'total_actif' => $totalActif,
                ],
                'passif' => [
                    'capitaux_propres' => [
                        'capital_social' => $capitalSocial,
                        'reserves' => $reserves,
                    ],
                    'dettes' => [
                        'total_dettes' => $totalDettes,
                        'dettes_fournisseurs' => $dettesFournisseurs,
                        'emprunts_bancaires' => $empruntsBancaires,
                    ],
                    'total_passif' => $totalPassif,
                ],
            ];

            return view('comptabilite.bilan', compact('bilan'));
        } catch (\Exception $e) {
            return view('comptabilite.bilan', [
                'bilan' => $this->getBilanStructureVide(),
                'error' => 'Erreur lors du chargement du bilan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtenir la structure de bilan vide pour éviter les erreurs
     */
    private function getBilanStructureVide()
    {
        return [
            'annee' => date('Y'),
            'periode' => 'mois',
            'resultat' => 0,
            'statut' => 'neutre',
            'actif' => [
                'immobilisations' => [
                    'total_immobilisations' => 0,
                    'immobilisations_corporelles' => 0,
                    'materiel_mobilier' => 0,
                ],
                'actif_circulant' => [
                    'total_actif_circulant' => 0,
                    'stocks' => 0,
                    'creances_clients' => 0,
                    'disponibilites' => 0,
                ],
                'total_actif' => 0,
            ],
            'passif' => [
                'capitaux_propres' => [
                    'capital_social' => 0,
                    'reserves' => 0,
                ],
                'dettes' => [
                    'total_dettes' => 0,
                    'dettes_fournisseurs' => 0,
                    'emprunts_bancaires' => 0,
                ],
                'total_passif' => 0,
            ],
        ];
    }

    /**
     * Calculer le résultat de l'exercice
     */
    private function calculerResultatExercice($debut, $fin)
    {
        try {
            // Produits
            $produits = 0;

            // Revenus des factures payées
            if (Schema::hasTable('factures')) {
                $produits += DB::table('factures')
                    ->where('statut', 'payee')
                    ->whereBetween('date_facture', [$debut, $fin])
                    ->sum('montant_ttc') ?? 0;
            }

            // Revenus des encaissements
            if (Schema::hasTable('encaissements')) {
                $produits += DB::table('encaissements')
                    ->whereBetween('date_encaissement', [$debut, $fin])
                    ->sum('montant') ?? 0;
            }

            // Revenus des recettes
            if (Schema::hasTable('recettes')) {
                $qRecettes = DB::table('recettes')
                    ->whereBetween('date_recette', [Carbon::parse($debut)->toDateString(), Carbon::parse($fin)->toDateString()]);
                if (Schema::hasColumn('recettes', 'facture_id')) {
                    $qRecettes->whereNull('facture_id');
                }
                $produits += (float) $qRecettes->sum('montant');
            }

            // Revenus des vehicle_financial_entries (revenues)
            if (Schema::hasTable('vehicle_financial_entries')) {
                $typeColumn = $this->resolveVehicleEntryTypeColumn();
                $dateColumn = $this->resolveVehicleEntryDateColumn();
                if ($typeColumn && $dateColumn) {
                    $produits += DB::table('vehicle_financial_entries')
                        ->where($typeColumn, $this->mapVehicleEntryTypeValue('revenue', $typeColumn))
                        ->whereBetween($dateColumn, [$debut, $fin])
                        ->sum('amount') ?? 0;
                }
            }

            // Charges
            $charges = 0;

            // Dépenses
            if (Schema::hasTable('depenses')) {
                $charges += $this->sumDepenses($debut, $fin);
            }

            // Dépenses des vehicle_financial_entries (expenses)
            if (Schema::hasTable('vehicle_financial_entries')) {
                $typeColumn = $this->resolveVehicleEntryTypeColumn();
                $dateColumn = $this->resolveVehicleEntryDateColumn();
                if ($typeColumn && $dateColumn) {
                    $charges += DB::table('vehicle_financial_entries')
                        ->where($typeColumn, $this->mapVehicleEntryTypeValue('expense', $typeColumn))
                        ->whereBetween($dateColumn, [$debut, $fin])
                        ->sum('amount') ?? 0;
                }
            }

            return $produits - $charges;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Rapport trésorerie
     */
    public function rapportTresorerie()
    {
        try {
            // Période par défaut
            $periode = request('periode', 'mois');
            $dateDebut = request('date_debut');
            $dateFin = request('date_fin');

            if ($periode === 'personnalise' && $dateDebut && $dateFin) {
                $debut = Carbon::parse($dateDebut);
                $fin = Carbon::parse($dateFin);
            } elseif ($periode === 'annee') {
                $debut = Carbon::now()->startOfYear();
                $fin = Carbon::now()->endOfYear();
            } else {
                $debut = Carbon::now()->startOfMonth();
                $fin = Carbon::now()->endOfMonth();
            }

            // === SOLDES DE TRÉSORERIE ===

            // Solde des comptes bancaires
            $soldeBancaires = DB::table('compte_bancaires')
                ->sum('solde') ?? 0;

            // Solde des caisses
            $soldeCaisses = DB::table('caisses')
                ->sum('solde') ?? 0;

            // Total trésorerie disponible
            $totalTresorerie = $soldeBancaires + $soldeCaisses;

            // === MOUVEMENTS DE LA PÉRIODE ===

            // Encaissements (recettes + paiements reçus)
            $encaissements = DB::table('recettes')
                ->whereBetween('date_recette', [$debut, $fin])
                ->sum('montant') ?? 0;

            $encaissements += DB::table('paiements')
                ->whereBetween('created_at', [$debut, $fin])
                ->where('type', 'recette')
                ->sum('montant') ?? 0;

            // Décaissements (dépenses + paiements effectués)
            $decaissements = $this->sumDepenses($debut, $fin);

            $decaissements += DB::table('paiements')
                ->whereBetween('created_at', [$debut, $fin])
                ->where('type', 'depense')
                ->sum('montant') ?? 0;

            // Flux net de trésorerie
            $fluxNet = $encaissements - $decaissements;

            // === PRÉVISIONS ===

            // Factures en attente de paiement
            $facturesEnAttente = DB::table('factures')
                ->where('statut', 'impayee')
                ->sum('montant') ?? 0;

            // Dépenses prévisionnelles
            $depensesPrevisionnelles = $this->getDepensesPrevisionnelles($debut, $fin);

            // === DÉTAILS PAR BANQUE ===
            $detailsBanques = DB::table('compte_bancaires')
                ->leftJoin('banques', 'compte_bancaires.banque_id', '=', 'banques.id')
                ->select('compte_bancaires.*', 'banques.nom as banque_nom')
                ->get();

            // === DÉTAILS PAR CAISSE ===
            $detailsCaisses = DB::table('caisses')
                ->get();

            // === GRAPHIQUES ===
            $graphiqueMensuel = $this->getGraphiqueTresorerie($debut, $fin);

            $donnees = [
                'solde_bancaires' => $soldeBancaires,
                'solde_caisses' => $soldeCaisses,
                'total_tresorerie' => $totalTresorerie,
                'encaissements' => $encaissements,
                'decaissements' => $decaissements,
                'flux_net' => $fluxNet,
                'factures_en_attente' => $facturesEnAttente,
                'depenses_previsionnelles' => $depensesPrevisionnelles,
                'details_banques' => $detailsBanques,
                'details_caisses' => $detailsCaisses,
                'graphique_mensuel' => $graphiqueMensuel,
                'periode' => $periode,
                'debut' => $debut,
                'fin' => $fin,
            ];

            return view('comptabilite.rapports.tresorerie', compact('donnees'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données par défaut
            $donnees = [
                'solde_bancaires' => 0,
                'solde_caisses' => 0,
                'total_tresorerie' => 0,
                'encaissements' => 0,
                'decaissements' => 0,
                'flux_net' => 0,
                'factures_en_attente' => 0,
                'depenses_previsionnelles' => 0,
                'details_banques' => collect([]),
                'details_caisses' => collect([]),
                'graphique_mensuel' => [],
                'periode' => 'mois',
                'debut' => now(),
                'fin' => now(),
            ];

            return view('comptabilite.rapports.tresorerie', compact('donnees'))
                ->with('warning', 'Certaines données ne sont pas disponibles: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les données pour le graphique de trésorerie
     */
    private function getGraphiqueTresorerie($debut, $fin)
    {
        try {
            $data = [];
            $current = clone $debut;

            while ($current <= $fin) {
                $mois = $current->format('Y-m');

                $encaissementsMois = DB::table('recettes')
                    ->whereYear('date_recette', $current->year)
                    ->whereMonth('date_recette', $current->month)
                    ->sum('montant') ?? 0;

                $decaissementsMois = (float) DB::table('depenses')
                    ->when(Schema::hasColumn('depenses', 'deleted_at'), fn ($query) => $query->whereNull('deleted_at'))
                    ->whereYear('date_depense', $current->year)
                    ->whereMonth('date_depense', $current->month)
                    ->sum('montant');

                $data[] = [
                    'mois' => $current->format('M Y'),
                    'encaissements' => $encaissementsMois,
                    'decaissements' => $decaissementsMois,
                    'flux_net' => $encaissementsMois - $decaissementsMois
                ];

                $current->addMonth();
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Récupérer les factures par mois pour les graphiques
     */
    private function getFacturesParMois()
    {
        try {
            return DB::table('factures')
                ->selectRaw('MONTH(date_facture) as mois, YEAR(date_facture) as annee, COUNT(*) as count, SUM(montant) as total')
                ->whereYear('date_facture', now()->year)
                ->groupBy('mois', 'annee')
                ->orderBy('mois')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Récupérer les dépenses par mois pour les graphiques
     */
    private function getDepensesParMois()
    {
        try {
            return DB::table('depenses')
                ->selectRaw('MONTH(date_depense) as mois, YEAR(date_depense) as annee, COUNT(*) as count, SUM(montant) as total')
                ->when(Schema::hasColumn('depenses', 'deleted_at'), fn ($query) => $query->whereNull('deleted_at'))
                ->whereYear('date_depense', now()->year)
                ->groupBy('mois', 'annee')
                ->orderBy('mois')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Récupérer les recettes par mois pour les graphiques
     */
    private function getRecettesParMois()
    {
        try {
            return DB::table('recettes')
                ->selectRaw('MONTH(date_recette) as mois, YEAR(date_recette) as annee, COUNT(*) as count, SUM(montant) as total')
                ->whereYear('date_recette', now()->year)
                ->groupBy('mois', 'annee')
                ->orderBy('mois')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Récupérer les opérations par mois pour les graphiques
     */
    private function getOperationsParMois()
    {
        try {
            return DB::table('operations')
                ->selectRaw('MONTH(date_operation) as mois, YEAR(date_operation) as annee, COUNT(*) as count, SUM(montant) as total')
                ->whereYear('date_operation', now()->year)
                ->where('statut_courant', 'payee')
                ->groupBy('mois', 'annee')
                ->orderBy('mois')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Filtrer les factures (AJAX)
     */
    public function filterFactures(Request $request)
    {
        $query = DB::table('factures')
            ->join('clients', 'factures.client_id', '=', 'clients.id')
            ->select('factures.*', 'clients.nom as client_nom');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('periode')) {
            // Exemple: filtrer par année/mois
            $query->whereYear('date_facture', $request->periode);
        }
        if ($request->filled('tiers')) {
            $query->where('client_id', $request->tiers);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('factures.numero', 'like', '%' . $request->search . '%')
                  ->orWhere('clients.nom', 'like', '%' . $request->search . '%');
            });
        }

        $factures = $query->orderBy('factures.created_at', 'desc')->limit(50)->get();
        return response()->json(['factures' => $factures]);
    }

    /**
     * Importer des factures depuis un fichier Excel
     */
    public function importFactures(Request $request)
    {
        $request->validate([
            'factures_excel' => 'required|file|mimes:xlsx,xls',
        ]);

        // Utilise Laravel Excel (maatwebsite/excel)
        try {
            $file = $request->file('factures_excel');
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $file);

            // Traitement des données...

            return back()->with('success', 'Factures importées avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire d'import des parametres comptables.
     */
    public function importParamComptaForm()
    {
        return view('comptabilite.import-param');
    }

    /**
     * Importer les parametres comptables depuis un fichier Excel (en ligne).
     */
    public function importParamComptaStore(Request $request, AccountingWorkbookUploadService $service)
    {
        $request->validate([
            'param_compta_excel' => 'required|file|mimes:xlsx,xls',
            'import_type' => 'required|in:param_compta,creances_clients,dettes_fournisseurs,caisse_logistique',
            'dry_run' => 'nullable|boolean',
        ]);

        $file = $request->file('param_compta_excel');
        $importType = (string) $request->input('import_type');
        $dryRun = (bool) $request->boolean('dry_run');

        try {
            $stats = $service->import($importType, $file->getRealPath(), $dryRun);

            $labels = [
                'param_compta' => 'PARAM COMPTA',
                'creances_clients' => 'CREANCES CLIENTS',
                'dettes_fournisseurs' => 'DETTES FOURNISSEURS',
                'caisse_logistique' => 'CAISSE LOGISTIQUE',
            ];

            $label = $labels[$importType] ?? strtoupper($importType);

            $message = $dryRun
                ? 'Simulation terminee pour ' . $label . '. Aucune ecriture n\'a ete enregistree.'
                : 'Import effectif termine avec succes pour ' . $label . '.';

            return back()
                ->with('success', $message)
                ->with('import_param_type', $importType)
                ->with('import_param_stats', $stats)
                ->with('import_param_warnings', $stats['warnings'] ?? []);
        } catch (\Throwable $e) {
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function operationsToEcritures()
    {
        $journaux = collect();

        try {
            if (Schema::hasTable('journal_comptables')) {
                $journaux = JournalComptable::query()
                    ->where('actif', true)
                    ->orderBy('code')
                    ->get(['id', 'code', 'libelle']);
            }
        } catch (\Throwable $e) {
            $journaux = collect();
        }

        return view('comptabilite.operations-to-ecritures', compact('journaux'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('comptabilite.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('comptabilite.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Calculer les revenus de location d'engins et véhicules
     */
    private function sumRevenusLocations(Carbon $debut, Carbon $fin): float
    {
        if (!Schema::hasTable('devis')) {
            return 0;
        }

        try {
            // Locations validées (statut accepte ou termine)
            $locationsPrincipales = (float) DB::table('devis')
                ->where('type_devis', 'location')
                ->whereIn('statut', ['accepte', 'termine', 'facture'])
                ->whereBetween('location_date_debut', [$debut, $fin])
                ->where(function ($query) {
                    $query->whereNotNull('location_duree_jours')
                          ->whereNotNull('location_tarif_journalier_ht');
                })
                ->selectRaw('SUM(location_duree_jours * location_tarif_journalier_ht) as total')
                ->value('total') ?? 0;

            // Frais additionnels de location
            $fraisLocations = (float) DB::table('devis')
                ->where('type_devis', 'location')
                ->whereIn('statut', ['accepte', 'termine', 'facture'])
                ->whereBetween('location_date_debut', [$debut, $fin])
                ->selectRaw('SUM(
                    COALESCE(frais_livraison_ht, 0) +
                    COALESCE(frais_mise_disposition_ht, 0) +
                    COALESCE(frais_nettoyage_ht, 0) +
                    COALESCE(location_frais_kilometrage_supplementaire, 0)
                ) as total')
                ->value('total') ?? 0;

            return round((float) $locationsPrincipales + (float) $fraisLocations, 2);
        } catch (\Exception $e) {
            Log::warning('Erreur calcul revenus locations', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Calculer les revenus de ventes quincaillerie et magasin
     */
    private function sumRevenusVentes(Carbon $debut, Carbon $fin): float
    {
        $totalVentes = 0;

        // Ventes depuis table sales
        if (Schema::hasTable('sales')) {
            try {
                $totalVentes += (float) DB::table('sales')
                    ->whereIn('payment_status', ['paid', 'completed', 'confirmed'])
                    ->whereBetween('created_at', [$debut, $fin])
                    ->sum('total_amount') ?? 0;
            } catch (\Exception $e) {
                Log::warning('Erreur calcul ventes depuis sales', ['error' => $e->getMessage()]);
            }
        }

        // Ventes depuis factures de vente
        if (Schema::hasTable('invoices')) {
            try {
                $totalVentes += (float) DB::table('invoices')
                    ->where('type', 'vente')
                    ->whereIn('status', ['paid', 'validated', 'confirmed'])
                    ->whereBetween('created_at', [$debut, $fin])
                    ->sum('total_amount') ?? 0;
            } catch (\Exception $e) {
                Log::warning('Erreur calcul ventes depuis invoices', ['error' => $e->getMessage()]);
            }
        }

        // Sorties stock (alternative si sales/invoices insuffisantes)
        if (Schema::hasTable('stock_sorties') && $totalVentes === 0) {
            try {
                $totalVentes += (float) DB::table('stock_sorties')
                    ->where('motif', 'vente')
                    ->whereBetween('created_at', [$debut, $fin])
                    ->selectRaw('SUM(quantite * prix_unitaire) as total')
                    ->value('total') ?? 0;
            } catch (\Exception $e) {
                Log::warning('Erreur calcul ventes depuis stock_sorties', ['error' => $e->getMessage()]);
            }
        }

        return round((float) $totalVentes, 2);
    }

    /**
     * Afficher le détail d'un poste du bilan
     */
    public function bilanDetail($poste)
    {
        try {
            $tablesExist = [
                'immobilisations' => Schema::hasTable('immobilisations'),
                'stocks' => Schema::hasTable('stocks'),
                'factures' => Schema::hasTable('factures'),
                'compte_bancaires' => Schema::hasTable('compte_bancaires'),
                'caisses' => Schema::hasTable('caisses'),
                'encaissements' => Schema::hasTable('encaissements'),
                'recettes' => Schema::hasTable('recettes'),
                'depenses' => Schema::hasTable('depenses'),
                'emprunts' => Schema::hasTable('emprunts'),
                'reserve' => Schema::hasTable('reserves'),
                'entreprise_settings' => Schema::hasTable('entreprise_settings'),
            ];

            $details = [];
            $titre = '';

            switch ($poste) {
                case 'immobilisations_corporelles':
                    $titre = 'Immobilisations Corporelles';
                    if ($tablesExist['immobilisations']) {
                        $details = DB::table('immobilisations')
                            ->where('type', 'corporelle')
                            ->select('designation', 'valeur_actuelle', 'date_acquisition', 'type')
                            ->get();
                    }
                    break;

                case 'materiel_mobilier':
                    $titre = 'Matériel et Mobilier';
                    if ($tablesExist['immobilisations']) {
                        $details = DB::table('immobilisations')
                            ->where('type', 'mobilier')
                            ->select('designation', 'valeur_actuelle', 'date_acquisition', 'type')
                            ->get();
                    }
                    break;

                case 'stocks':
                    $titre = 'Stocks';
                    if ($tablesExist['stocks']) {
                        $details = DB::table('stocks')
                            ->select('designation', 'quantite', 'valeur_unitaire', 'valeur_totale')
                            ->get();
                    }
                    break;

                case 'creances_clients':
                    $titre = 'Créances Clients';
                    if ($tablesExist['factures']) {
                        $details = DB::table('factures')
                            ->leftJoin('clients', 'factures.client_id', '=', 'clients.id')
                            ->where('factures.statut', 'en_retard')
                            ->select('factures.numero as numero_facture', 'clients.nom as client_nom', 
                                    'factures.date_facture', 'factures.montant_ttc', 'factures.statut')
                            ->get();
                    }
                    break;

                case 'disponibilites':
                    $titre = 'Disponibilités (Trésorerie)';
                    $disponibilites = [];
                    if ($tablesExist['compte_bancaires']) {
                        $disponibilites = array_merge($disponibilites, DB::table('compte_bancaires')
                            ->select('numero_compte', 'solde', 'banque')
                            ->get()
                            ->toArray());
                    }
                    if ($tablesExist['caisses']) {
                        $caisses = DB::table('caisses')
                            ->select(DB::raw("'Caisse' as numero_compte"), 'solde_actuel as solde', DB::raw("'Caisse' as banque"))
                            ->get()
                            ->toArray();
                        $disponibilites = array_merge($disponibilites, $caisses);
                    }
                    $details = collect($disponibilites);
                    break;

                case 'capital_social':
                    $titre = 'Capital Social';
                    if ($tablesExist['entreprise_settings']) {
                        $details = collect([
                            (object) [
                                'designation' => 'Capital Social',
                                'montant' => DB::table('entreprise_settings')->value('capital_social') ?? 0
                            ]
                        ]);
                    }
                    break;

                case 'reserves':
                    $titre = 'Réserves';
                    if ($tablesExist['reserve']) {
                        $details = DB::table('reserves')
                            ->select('designation', 'montant', 'date_constitution')
                            ->get();
                    }
                    break;

                case 'dettes_fournisseurs':
                    $titre = 'Dettes Fournisseurs';
                    if ($tablesExist['depenses']) {
                        $details = DB::table('depenses')
                            ->where('statut', 'en_attente')
                            ->select('designation', 'montant', 'date_facture', 'fournisseur_nom', 'statut')
                            ->get();
                    }
                    break;

                case 'emprunts_bancaires':
                    $titre = 'Emprunts Bancaires';
                    if ($tablesExist['emprunts']) {
                        $details = DB::table('emprunts')
                            ->where('statut', 'en_cours')
                            ->select('banque', 'montant_initial', 'montant_restant', 'taux_interet', 'date_fin')
                            ->get();
                    }
                    break;

                case 'dettes_fiscales_sociales':
                    $titre = 'Dettes Fiscales et Sociales';
                    $details = collect([
                        (object) ['designation' => 'TVA à payer', 'montant' => 0],
                        (object) ['designation' => 'Cotisations sociales', 'montant' => 0],
                    ]);
                    break;

                default:
                    return back()->with('error', 'Poste bilan non reconnu');
            }

            return view('comptabilite.bilan-detail', compact('titre', 'details', 'poste'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du chargement du détail: ' . $e->getMessage());
        }
    }
}