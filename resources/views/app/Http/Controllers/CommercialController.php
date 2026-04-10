<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Service;

class CommercialController extends Controller
{
    /**
     * Déterminer dynamiquement le nom de colonne pour le nom du client
     */
    private function getClientNameColumn()
    {
        static $col = null;
        if ($col === null) {
            $schema = DB::getSchemaBuilder();
            if ($schema->hasColumn('clients', 'nom')) {
                $col = 'nom';
            } elseif ($schema->hasColumn('clients', 'raison_sociale')) {
                $col = 'raison_sociale';
            } elseif ($schema->hasColumn('clients', 'company_name')) {
                $col = 'company_name';
            } else {
                $col = 'contact_nom';
            }
        }
        return $col;
    }

    /**
     * Déterminer dynamiquement le nom de colonne pour le tri des clients
     */
    private function getClientSortColumn()
    {
        static $col = null;
        if ($col === null) {
            $col = DB::getSchemaBuilder()->hasColumn('clients', 'raison_sociale') ? 'raison_sociale' : $this->getClientNameColumn();
        }
        return $col;
    }

    /**
     * Mapper les champs du formulaire vers les colonnes réelles de la table clients
     * Le formulaire envoie 'nom' et 'contact', mais la table peut avoir
     * 'company_name'/'contact_person' ou 'nom'/'contact'
     */
    private function mapClientFields(array $fields)
    {
        $schema = DB::getSchemaBuilder();
        $data = [];

        foreach ($fields as $key => $value) {
            if ($key === 'nom') {
                // Mapper 'nom' vers la bonne colonne
                if ($schema->hasColumn('clients', 'nom')) {
                    $data['nom'] = $value;
                } elseif ($schema->hasColumn('clients', 'company_name')) {
                    $data['company_name'] = $value;
                } elseif ($schema->hasColumn('clients', 'raison_sociale')) {
                    $data['raison_sociale'] = $value;
                }
            } elseif ($key === 'contact') {
                // Mapper 'contact' vers la bonne colonne
                if ($schema->hasColumn('clients', 'contact')) {
                    $data['contact'] = $value;
                } elseif ($schema->hasColumn('clients', 'contact_person')) {
                    $data['contact_person'] = $value;
                } elseif ($schema->hasColumn('clients', 'contact_nom')) {
                    $data['contact_nom'] = $value;
                }
            } elseif ($key === 'categorie') {
                if ($schema->hasColumn('clients', 'categorie')) {
                    $data['categorie'] = $value;
                }
            } elseif ($key === 'ville') {
                if ($schema->hasColumn('clients', 'ville')) {
                    $data['ville'] = $value;
                } elseif ($schema->hasColumn('clients', 'city')) {
                    $data['city'] = $value;
                }
            } else {
                // Pour les autres champs, vérifier qu'ils existent dans la table
                if ($schema->hasColumn('clients', $key)) {
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Afficher le tableau de bord commercial
     */
    public function dashboard()
    {
        $stats = ['total_clients' => 0, 'total_contrats' => 0, 'total_devis' => 0, 'total_factures' => 0];
        $recentContracts = collect([]);
        $pendingQuotes = collect([]);
        $dbError = null;

        try {
            $stats = [
                'total_clients' => DB::table('clients')->count(),
                'total_contrats' => DB::table('contrats')->count(),
                'total_devis' => DB::table('devis')->count(),
                'total_factures' => DB::table('factures')->count(),
            ];

            $clientNameCol = $this->getClientNameColumn();

            $recentContracts = DB::table('contrats')
                ->join('clients', 'contrats.client_id', '=', 'clients.id')
                ->select('contrats.*', "clients.{$clientNameCol} as client_nom")
                ->orderBy('contrats.created_at', 'desc')
                ->limit(5)
                ->get();

            $pendingQuotes = DB::table('devis')
                ->join('clients', 'devis.client_id', '=', 'clients.id')
                ->select('devis.*', "clients.{$clientNameCol} as client_nom")
                ->where('devis.statut', 'en_attente')
                ->orderBy('devis.created_at', 'desc')
                ->limit(5)
                ->get();

        } catch (\Exception $e) {
            Log::warning('Commercial dashboard DB error: ' . $e->getMessage());
            $dbError = 'Les tables commerciales ne sont pas encore disponibles. Veuillez exécuter les migrations.';
        }

        return view('commercial.dashboard', compact('stats', 'recentContracts', 'pendingQuotes', 'dbError'));
    }

    /**
     * Page d'index commercial
     */
    public function index()
    {
        return $this->dashboard();
    }

    /**
     * Afficher la liste des clients
     */
    public function clients(Request $request)
    {
        $query = DB::table('clients');

        $nameCol = $this->getClientNameColumn();
        if ($request->filled('search')) {
            $query->where($nameCol, 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('commercial.clients', compact('clients'));
    }

    /**
     * Afficher la liste des devis
     */
    public function devis(Request $request)
    {
        $nameCol = $this->getClientNameColumn();
        $query = DB::table('devis')
            ->join('clients', 'devis.client_id', '=', 'clients.id')
            ->select('devis.*', "clients.{$nameCol} as client_nom");

        if ($request->filled('search')) {
            $query->where('devis.numero', 'like', '%' . $request->search . '%')
                  ->orWhere("clients.{$nameCol}", 'like', '%' . $request->search . '%');
        }

        $devis = $query->orderBy('devis.created_at', 'desc')->paginate(15);

        return view('commercial.devis', compact('devis'));
    }

    /**
     * Afficher la liste des commandes
     */
    public function commandes(Request $request)
    {
        $nameCol = $this->getClientNameColumn();
        $query = DB::table('commandes')
            ->join('clients', 'commandes.client_id', '=', 'clients.id')
            ->select('commandes.*', "clients.{$nameCol} as client_nom");

        if ($request->filled('search')) {
            $query->where('commandes.numero', 'like', '%' . $request->search . '%')
                  ->orWhere("clients.{$nameCol}", 'like', '%' . $request->search . '%');
        }

        $commandes = $query->orderBy('commandes.created_at', 'desc')->paginate(15);

        return view('commercial.commandes', compact('commandes'));
    }

    /**
     * Afficher la liste des factures
     */
    public function factures(Request $request)
    {
        $nameCol = $this->getClientNameColumn();
        $query = DB::table('factures')
            ->join('clients', 'factures.client_id', '=', 'clients.id')
            ->select('factures.*', "clients.{$nameCol} as client_nom");

        if ($request->filled('search')) {
            $query->where('factures.numero', 'like', '%' . $request->search . '%')
                  ->orWhere("clients.{$nameCol}", 'like', '%' . $request->search . '%');
        }

        $factures = $query->orderBy('factures.created_at', 'desc')->paginate(15);

        return view('commercial.factures', compact('factures'));
    }

    /**
     * Afficher la liste des contrats
     */
    public function contratsIndex(Request $request)
    {
        $query = DB::table('contrats')
            ->join('clients', 'contrats.client_id', '=', 'clients.id')
            ->select('contrats.*', "clients.{$this->getClientNameColumn()} as client_nom", 'clients.email as client_email');

        // Filtres
        if ($request->filled('search')) {
            $nameCol = $this->getClientNameColumn();
            $query->where(function($q) use ($request, $nameCol) {
                $q->where('contrats.numero', 'like', '%' . $request->search . '%')
                  ->orWhere("clients.{$nameCol}", 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('contrats.statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->where('contrats.date_debut', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('contrats.date_fin', '<=', $request->date_fin);
        }

        $contrats = $query->orderBy('contrats.created_at', 'desc')->paginate(10);

        return view('commercial.contrats.index', compact('contrats'));
    }

    /**
     * Afficher le formulaire de création de contrat
     */
    public function contratsCreate()
    {
        $clients = DB::table('clients')->orderBy($this->getClientSortColumn())->get();
        $services = Service::where('actif', true)->orderBy('nom')->get();

        return view('commercial.contrats.create', compact('clients', 'services'));
    }

    /**
     * Enregistrer un nouveau contrat
     */
    public function contratsStore(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'numero' => 'required|string|max:50|unique:contrats,numero',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0|max:100',
            'statut' => 'required|in:en_attente,actif,termine,annule',
            'description' => 'nullable|string',
        ]);

        $validated['montant_ttc'] = $validated['montant_ht'] * (1 + $validated['tva'] / 100);
        $validated['created_by'] = Auth::id();

        DB::table('contrats')->insert($validated);

        return redirect()->route('commercial.contrats.index')
            ->with('success', 'Contrat créé avec succès');
    }

    /**
     * Afficher les détails d'un contrat
     */
    public function contratsShow($id)
    {
        $contrat = DB::table('contrats')
            ->join('clients', 'contrats.client_id', '=', 'clients.id')
            ->join('services', 'contrats.service_id', '=', 'services.id')
            ->select('contrats.*', "clients.{$this->getClientNameColumn()} as client_nom", 'clients.email as client_email', 'services.nom as service_nom')
            ->where('contrats.id', $id)
            ->first();

        if (!$contrat) {
            abort(404);
        }

        return view('commercial.contrats.show', compact('contrat'));
    }

    /**
     * Afficher le formulaire d'édition de contrat
     */
    public function contratsEdit($id)
    {
        $contrat = DB::table('contrats')->find($id);
        $clients = DB::table('clients')->orderBy($this->getClientSortColumn())->get();
        $services = Service::where('actif', true)->orderBy('nom')->get();

        if (!$contrat) {
            abort(404);
        }

        return view('commercial.contrats.edit', compact('contrat', 'clients', 'services'));
    }

    /**
     * Mettre à jour un contrat
     */
    public function contratsUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'numero' => 'required|string|max:50|unique:contrats,numero,' . $id,
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0|max:100',
            'statut' => 'required|in:en_attente,actif,termine,annule',
            'description' => 'nullable|string',
        ]);

        $validated['montant_ttc'] = $validated['montant_ht'] * (1 + $validated['tva'] / 100);
        $validated['updated_by'] = Auth::id();

        DB::table('contrats')->where('id', $id)->update($validated);

        return redirect()->route('commercial.contrats.index')
            ->with('success', 'Contrat mis à jour avec succès');
    }

    /**
     * Supprimer un contrat
     */
    public function contratsDestroy($id)
    {
        DB::table('contrats')->where('id', $id)->delete();

        return redirect()->route('commercial.contrats.index')
            ->with('success', 'Contrat supprimé avec succès');
    }

    /**
     * Afficher la liste des clients
     */
    public function clientsIndex(Request $request)
    {
        $query = DB::table('clients');

        // Filtres
        if ($request->filled('search')) {
            $nameCol = $this->getClientNameColumn();
            $query->where(function($q) use ($request, $nameCol) {
                $q->where($nameCol, 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('telephone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('commercial.clients.index', compact('clients'));
    }

    /**
     * Afficher le formulaire de création de client
     */
    public function clientsCreate()
    {
        return view('commercial.clients.create');
    }

    /**
     * Enregistrer un nouveau client
     */
    public function clientsStore(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telephone' => 'nullable|string|max:20',
            'ville' => 'nullable|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'type' => 'nullable|in:entreprise,particulier,administration',
            'statut' => 'nullable|in:actif,prospect,inactif',
            'notes' => 'nullable|string',
        ]);

        // Mapper les champs du formulaire vers les colonnes réelles de la table
        $data = $this->mapClientFields($validated);
        $data['created_by'] = Auth::id();

        DB::table('clients')->insert($data);

        return redirect()->route('commercial.clients.index')
            ->with('success', 'Client créé avec succès');
    }

    /**
     * Afficher les détails d'un client
     */
    public function clientsShow($id)
    {
        $client = DB::table('clients')->find($id);

        if (!$client) {
            abort(404);
        }

        // Récupérer les contrats du client
        $contrats = DB::table('contrats')
            ->where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('commercial.clients.show', compact('client', 'contrats'));
    }

    /**
     * Afficher le formulaire d'édition de client
     */
    public function clientsEdit($id)
    {
        $client = DB::table('clients')->find($id);

        if (!$client) {
            abort(404);
        }

        return view('commercial.clients.edit', compact('client'));
    }

    /**
     * Mettre à jour un client
     */
    public function clientsUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $id,
            'telephone' => 'nullable|string|max:20',
            'ville' => 'nullable|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'type' => 'nullable|in:entreprise,particulier,administration',
            'statut' => 'nullable|in:actif,prospect,inactif',
            'notes' => 'nullable|string',
        ]);

        // Mapper les champs du formulaire vers les colonnes réelles de la table
        $data = $this->mapClientFields($validated);
        $data['updated_by'] = Auth::id();

        DB::table('clients')->where('id', $id)->update($data);

        return redirect()->route('commercial.clients.index')
            ->with('success', 'Client mis à jour avec succès');
    }

    /**
     * Supprimer un client
     */
    public function clientsDestroy($id)
    {
        DB::table('clients')->where('id', $id)->delete();

        return redirect()->route('commercial.clients.index')
            ->with('success', 'Client supprimé avec succès');
    }

    /**
     * Afficher la liste des bons de commande
     */
    public function bonsCommandeIndex(Request $request)
    {
        $query = DB::table('bons_commande')
            ->join('clients', 'bons_commande.client_id', '=', 'clients.id')
            ->leftJoin('contrats', 'bons_commande.contrat_id', '=', 'contrats.id')
            ->select('bons_commande.*', "clients.{$this->getClientNameColumn()} as client_nom", 'clients.email as client_email', 'contrats.numero as contrat_numero');

        // Filtres
        if ($request->filled('search')) {
            $nameCol = $this->getClientNameColumn();
            $query->where(function($q) use ($request, $nameCol) {
                $q->where('bons_commande.numero', 'like', '%' . $request->search . '%')
                  ->orWhere("clients.{$nameCol}", 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('bons_commande.statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->where('bons_commande.date_commande', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('bons_commande.date_commande', '<=', $request->date_fin);
        }

        $bonsCommande = $query->orderBy('bons_commande.created_at', 'desc')->paginate(10);

        return view('commercial.bons-commande.index', compact('bonsCommande'));
    }

    /**
     * Afficher le formulaire de création de bon de commande
     */
    public function bonsCommandeCreate()
    {
        $clients = DB::table('clients')->orderBy($this->getClientSortColumn())->get();
        $contrats = DB::table('contrats')->orderBy('created_at', 'desc')->get();

        return view('commercial.bons-commande.create', compact('clients', 'contrats'));
    }

    /**
     * Enregistrer un nouveau bon de commande
     */
    public function bonsCommandeStore(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'contrat_id' => 'nullable|exists:contrats,id',
            'numero' => 'required|string|max:50|unique:bons_commande,numero',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0|max:100',
            'statut' => 'required|in:brouillon,envoye,valide,en_preparation,livre,annule',
            'notes' => 'nullable|string',
            'conditions_livraison' => 'nullable|string',
        ]);

        $validated['montant_ttc'] = $validated['montant_ht'] * (1 + $validated['tva'] / 100);
        $validated['created_by'] = Auth::id();

        DB::table('bons_commande')->insert($validated);

        return redirect()->route('commercial.bons-commande.index')
            ->with('success', 'Bon de commande créé avec succès');
    }

    /**
     * Afficher les détails d'un bon de commande
     */
    public function bonsCommandeShow($id)
    {
        $bonCommande = DB::table('bons_commande')
            ->join('clients', 'bons_commande.client_id', '=', 'clients.id')
            ->leftJoin('contrats', 'bons_commande.contrat_id', '=', 'contrats.id')
            ->leftJoin('users as creator', 'bons_commande.created_by', '=', 'creator.id')
            ->leftJoin('users as validator', 'bons_commande.validated_by', '=', 'validator.id')
            ->select('bons_commande.*', "clients.{$this->getClientNameColumn()} as client_nom", 'clients.email as client_email',
                    'contrats.numero as contrat_numero', 'creator.name as created_by_name',
                    'validator.name as validated_by_name')
            ->where('bons_commande.id', $id)
            ->first();

        if (!$bonCommande) {
            abort(404);
        }

        // Récupérer les lignes du bon de commande
        $lignes = DB::table('lignes_bons')
            ->where('bon_commande_id', $id)
            ->get();

        return view('commercial.bons-commande.show', compact('bonCommande', 'lignes'));
    }

    /**
     * Afficher le formulaire d'édition de bon de commande
     */
    public function bonsCommandeEdit($id)
    {
        $bonCommande = DB::table('bons_commande')->find($id);
        $clients = DB::table('clients')->orderBy($this->getClientSortColumn())->get();
        $contrats = DB::table('contrats')->orderBy('created_at', 'desc')->get();

        if (!$bonCommande) {
            abort(404);
        }

        return view('commercial.bons-commande.edit', compact('bonCommande', 'clients', 'contrats'));
    }

    /**
     * Mettre à jour un bon de commande
     */
    public function bonsCommandeUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'contrat_id' => 'nullable|exists:contrats,id',
            'numero' => 'required|string|max:50|unique:bons_commande,numero,' . $id,
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'required|numeric|min:0|max:100',
            'statut' => 'required|in:brouillon,envoye,valide,en_preparation,livre,annule',
            'notes' => 'nullable|string',
            'conditions_livraison' => 'nullable|string',
        ]);

        $validated['montant_ttc'] = $validated['montant_ht'] * (1 + $validated['tva'] / 100);
        $validated['updated_by'] = Auth::id();

        DB::table('bons_commande')->where('id', $id)->update($validated);

        return redirect()->route('commercial.bons-commande.index')
            ->with('success', 'Bon de commande mis à jour avec succès');
    }

    /**
     * Supprimer un bon de commande
     */
    public function bonsCommandeDestroy($id)
    {
        DB::table('bons_commande')->where('id', $id)->delete();

        return redirect()->route('commercial.bons-commande.index')
            ->with('success', 'Bon de commande supprimé avec succès');
    }

    /**
     * Afficher la liste des bons de livraison
     */
    public function bonsLivraisonIndex(Request $request)
    {
        $query = DB::table('bons_livraison')
            ->join('bons_commande', 'bons_livraison.bon_commande_id', '=', 'bons_commande.id')
            ->join('clients', 'bons_livraison.client_id', '=', 'clients.id')
            ->select('bons_livraison.*', 'bons_commande.numero as bon_commande_numero',
                    "clients.{$this->getClientNameColumn()} as client_nom", 'clients.email as client_email');

        // Filtres
        if ($request->filled('search')) {
            $nameCol = $this->getClientNameColumn();
            $query->where(function($q) use ($request, $nameCol) {
                $q->where('bons_livraison.numero', 'like', '%' . $request->search . '%')
                  ->orWhere('bons_commande.numero', 'like', '%' . $request->search . '%')
                  ->orWhere("clients.{$nameCol}", 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('bons_livraison.statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->where('bons_livraison.date_livraison', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('bons_livraison.date_livraison', '<=', $request->date_fin);
        }

        $bonsLivraison = $query->orderBy('bons_livraison.created_at', 'desc')->paginate(10);

        return view('commercial.bons-livraison.index', compact('bonsLivraison'));
    }

    /**
     * Afficher le formulaire de création de bon de livraison
     */
    public function bonsLivraisonCreate()
    {
        $bonsCommande = DB::table('bons_commande')
            ->join('clients', 'bons_commande.client_id', '=', 'clients.id')
            ->select('bons_commande.*', "clients.{$this->getClientNameColumn()} as client_nom")
                ->where('bons_commande.statut', 'valide')
                ->orderBy('bons_commande.created_at', 'desc')
                ->get();

        return view('commercial.bons-livraison.create', compact('bonsCommande'));
    }

    /**
     * Enregistrer un nouveau bon de livraison
     */
    public function bonsLivraisonStore(Request $request)
    {
        $validated = $request->validate([
            'bon_commande_id' => 'required|exists:bons_commande,id',
            'client_id' => 'required|exists:clients,id',
            'numero' => 'required|string|max:50|unique:bons_livraison,numero',
            'date_livraison' => 'required|date',
            'livreur' => 'nullable|string|max:255',
            'adresse_livraison' => 'nullable|string',
            'statut' => 'required|in:en_preparation,en_transit,livre,retourne,annule',
            'notes' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        DB::table('bons_livraison')->insert($validated);

        return redirect()->route('commercial.bons-livraison.index')
            ->with('success', 'Bon de livraison créé avec succès');
    }

    /**
     * Afficher les détails d'un bon de livraison
     */
    public function bonsLivraisonShow($id)
    {
        $bonLivraison = DB::table('bons_livraison')
            ->join('bons_commande', 'bons_livraison.bon_commande_id', '=', 'bons_commande.id')
            ->join('clients', 'bons_livraison.client_id', '=', 'clients.id')
            ->leftJoin('users as creator', 'bons_livraison.created_by', '=', 'creator.id')
            ->leftJoin('users as deliverer', 'bons_livraison.delivered_by', '=', 'deliverer.id')
            ->select('bons_livraison.*', 'bons_commande.numero as bon_commande_numero',
                    "clients.{$this->getClientNameColumn()} as client_nom", 'clients.email as client_email',
                    'creator.name as created_by_name', 'deliverer.name as delivered_by_name')
            ->where('bons_livraison.id', $id)
            ->first();

        if (!$bonLivraison) {
            abort(404);
        }

        return view('commercial.bons-livraison.show', compact('bonLivraison'));
    }

    /**
     * Afficher le formulaire d'édition de bon de livraison
     */
    public function bonsLivraisonEdit($id)
    {
        $bonLivraison = DB::table('bons_livraison')->find($id);
        $bonsCommande = DB::table('bons_commande')
            ->join('clients', 'bons_commande.client_id', '=', 'clients.id')
            ->select('bons_commande.*', "clients.{$this->getClientNameColumn()} as client_nom")
                ->where('bons_commande.statut', 'valide')
                ->orderBy('bons_commande.created_at', 'desc')
                ->get();

        if (!$bonLivraison) {
            abort(404);
        }

        return view('commercial.bons-livraison.edit', compact('bonLivraison', 'bonsCommande'));
    }

    /**
     * Mettre à jour un bon de livraison
     */
    public function bonsLivraisonUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'bon_commande_id' => 'required|exists:bons_commande,id',
            'client_id' => 'required|exists:clients,id',
            'numero' => 'required|string|max:50|unique:bons_livraison,numero,' . $id,
            'date_livraison' => 'required|date',
            'livreur' => 'nullable|string|max:255',
            'adresse_livraison' => 'nullable|string',
            'statut' => 'required|in:en_preparation,en_transit,livre,retourne,annule',
            'notes' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        DB::table('bons_livraison')->where('id', $id)->update($validated);

        return redirect()->route('commercial.bons-livraison.index')
            ->with('success', 'Bon de livraison mis à jour avec succès');
    }

    /**
     * Supprimer un bon de livraison
     */
    public function bonsLivraisonDestroy($id)
    {
        DB::table('bons_livraison')->where('id', $id)->delete();

        return redirect()->route('commercial.bons-livraison.index')
            ->with('success', 'Bon de livraison supprimé avec succès');
    }

    /**
     * API pour les statistiques
     */
    public function apiStats()
    {
        $stats = [
            'clients' => DB::table('clients')->count(),
            'contrats' => DB::table('contrats')->count(),
            'devis' => DB::table('devis')->count(),
            'factures' => DB::table('factures')->count(),
            'chiffre_affaires' => DB::table('contrats')->sum('montant_ttc'),
        ];

        return response()->json($stats);
    }

    /**
     * API pour les données des graphiques
     */
    public function apiChartData()
    {
        $monthlyContracts = DB::table('contrats')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthlyContracts);
    }

    // Méthodes pour les devis et factures (similaires aux contrats)
    public function devisIndex()
    {
        $devis = DB::table('devis')
            ->join('clients', 'devis.client_id', '=', 'clients.id')
            ->select('devis.*', "clients.{$this->getClientNameColumn()} as client_nom", 'clients.email as client_email')
            ->orderBy('devis.created_at', 'desc')
            ->paginate(15);

        return view('commercial.devis.index', compact('devis'));
    }

    public function devisCreate()
    {
        $clients = DB::table('clients')->orderBy($this->getClientSortColumn())->get();
        $vehicules = \App\Models\Vehicule::where('disponible', true)->orderBy('immatriculation')->get();
        return view('commercial.devis.create', compact('clients', 'vehicules'));
    }

    public function devisStore(Request $request)
    {
        // Validation et création du devis
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'reference' => 'nullable|string|max:50',
            'objet' => 'nullable|string|max:255',
            'montant_ht' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,accepte,rejete',
            'notes' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        if (empty($data['reference'])) {
            $data['reference'] = 'DEV-' . date('Ymd') . '-' . rand(100, 999);
        }

        $data['tva'] = 18;
        $data['total_ttc'] = $data['montant_ht'] * 1.18;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('devis')->insert($data);

        return redirect()->route('commercial.devis.index')->with('success', 'Devis créé avec succès');
    }

    public function devisShow($id)
    {
        $devis = DB::table('devis')
            ->join('clients', 'devis.client_id', '=', 'clients.id')
            ->select('devis.*', "clients.{$this->getClientNameColumn()} as client_nom", 'clients.email as client_email')
            ->where('devis.id', $id)
            ->first();

        return view('commercial.devis.show', compact('devis'));
    }

    public function devisEdit($id)
    {
        $devis = DB::table('devis')->find($id);
        $clients = DB::table('clients')->orderBy($this->getClientSortColumn())->get();
        return view('commercial.devis.edit', compact('devis', 'clients'));
    }

    public function devisUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'reference' => 'required|string|max:50',
            'objet' => 'nullable|string|max:255',
            'montant_ht' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,accepte,rejete',
            'notes' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        $data['total_ttc'] = $data['montant_ht'] * 1.18;
        $data['updated_at'] = now();

        DB::table('devis')->where('id', $id)->update($data);

        return redirect()->route('commercial.devis.index')->with('success', 'Devis mis à jour avec succès');
    }

    /**
     * Convertir un devis accepté en facture
     */
    public function convertDevisToFacture($id)
    {
        $devis = DB::table('devis')->where('id', $id)->first();

        if (!$devis) {
            return back()->with('error', 'Devis introuvable.');
        }

        if ($devis->statut !== 'accepte') {
            return back()->with('error', 'Seul un devis accepté peut être converti en facture.');
        }

        // Vérifier si une facture existe déjà pour ce devis
        $existingFacture = DB::table('factures')->where('devis_id', $id)->first();
        if ($existingFacture) {
            return redirect()->route('commercial.factures.show', $existingFacture->id)
                ->with('info', 'Une facture existe déjà pour ce devis.');
        }

        try {
            DB::beginTransaction();

            $factureId = DB::table('factures')->insertGetId([
                'numero' => 'FAC-' . strtoupper(uniqid()),
                'client_id' => $devis->client_id,
                'devis_id' => $devis->id,
                'date_facture' => now(),
                'montant_ht' => $devis->montant_ht ?? $devis->montant ?? 0,
                'tva' => $devis->tva ?? 18,
                'montant_ttc' => $devis->total_ttc ?? (($devis->montant ?? 0) * 1.18),
                'statut' => 'en_attente',
                'description' => 'Facture générée à partir du devis ' . $devis->reference,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('commercial.factures.show', $factureId)
                ->with('success', 'Facture générée avec succès depuis le devis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la conversion : ' . $e->getMessage());
        }
    }

    public function devisDestroy($id)
    {
        DB::table('devis')->delete($id);
        return redirect()->route('commercial.devis.index')->with('success', 'Devis supprimé avec succès');
    }

    // Méthodes pour les bons de commande et livraison
    public function bonCommandeIndex()
    {
        try {
            $bonsCommande = DB::table('bon_commandes')
                ->join('clients', 'bon_commandes.client_id', '=', 'clients.id')
                ->select('bon_commandes.*', "clients.{$this->getClientNameColumn()} as client_nom")
                ->orderBy('bon_commandes.created_at', 'desc')
                ->paginate(15);
        } catch (\Exception $e) {
            $bonsCommande = collect();
        }

        return view('commercial.bon-commande.index', compact('bonsCommande'));
    }

    public function bonCommandeCreate()
    {
        $clients = DB::table('clients')->orderBy($this->getClientSortColumn())->get();
        return view('commercial.bon-commande.create', compact('clients'));
    }

    public function bonLivraisonIndex()
    {
        try {
            $bonsLivraison = DB::table('bon_livraisons')
                ->join('clients', 'bon_livraisons.client_id', '=', 'clients.id')
                ->select('bon_livraisons.*', "clients.{$this->getClientNameColumn()} as client_nom")
                ->orderBy('bon_livraisons.created_at', 'desc')
                ->paginate(15);
        } catch (\Exception $e) {
            $bonsLivraison = collect();
        }

        return view('commercial.bon-livraison.index', compact('bonsLivraison'));
    }

    public function bonLivraisonCreate()
    {
        $clients = DB::table('clients')->orderBy($this->getClientSortColumn())->get();
        return view('commercial.bon-livraison.create', compact('clients'));
    }
}
