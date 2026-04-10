<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\PersonnelConge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PersonnelController extends Controller
{
    /**
     * Afficher le tableau de bord global RH avec données de tous les modules
     */
    public function dashboard()
    {
        // ===== STATS RH =====
        try {
            if (Schema::hasTable('personnel')) {
                // Vérifier les colonnes existantes
                $hasFinEssai = Schema::hasColumn('personnel', 'fin_periode_essai');
                $hasDateFinContrat = Schema::hasColumn('personnel', 'date_fin_contrat');
                $hasTypeContrat = Schema::hasColumn('personnel', 'type_contrat');
                $hasStatut = Schema::hasColumn('personnel', 'statut');
                $hasSalaireBase = Schema::hasColumn('personnel', 'salaire_base');

                $rhStats = [
                    'total' => Personnel::count(),
                    'actifs' => $hasStatut ? Personnel::where('statut', 'ACTIF')->count() : 0,
                    'inactifs' => $hasStatut ? Personnel::where('statut', 'INACTIF')->count() : 0,
                    'en_essai' => $hasStatut ? Personnel::where('statut', 'EN_ESSAI')->count() : 0,
                    'realties' => $hasStatut ? Personnel::where('statut', 'RESILIE')->count() : 0,
                    'contrats_cdi' => $hasTypeContrat ? Personnel::where('type_contrat', 'CDI')->count() : 0,
                    'contrats_cdd' => $hasTypeContrat ? Personnel::where('type_contrat', 'CDD')->count() : 0,
                    'contrats_expiring' => $hasDateFinContrat ? Personnel::where('date_fin_contrat', '<=', now()->addDays(30))
                        ->where('date_fin_contrat', '>=', now())
                        ->count() : 0,
                    'fin_essai_expiring' => $hasFinEssai ? Personnel::whereNotNull('fin_periode_essai')
                        ->where('fin_periode_essai', '<=', now()->addDays(30))
                        ->where('fin_periode_essai', '>=', now())
                        ->count() : 0,
                    'conges_en_attente' => class_exists('App\Models\PersonnelConge') ? PersonnelConge::where('statut', 'en_attente')
                        ->where('date_debut', '>=', now())
                        ->count() : 0,
                    'salaire_moyen' => $hasSalaireBase && $hasStatut ? DB::table('personnel')
                        ->where('statut', 'ACTIF')
                        ->avg('salaire_base') ?? 0 : 0,
                ];
            } else {
                // Fallback: utiliser la table users
                $rhStats = [
                    'total' => \App\Models\User::where('role', '!=', 'admin')->where('role', '!=', 'superadmin')->count(),
                    'actifs' => \App\Models\User::where('role', '!=', 'admin')->where('role', '!=', 'superadmin')->where('est_actif', 1)->count(),
                    'inactifs' => \App\Models\User::where('role', '!=', 'admin')->where('role', '!=', 'superadmin')->where('est_actif', 0)->count(),
                    'en_essai' => 0, // Pas applicable pour users
                    'realties' => 0, // Pas applicable pour users
                    'contrats_cdi' => 0, // Pas applicable pour users
                    'contrats_cdd' => 0, // Pas applicable pour users
                    'contrats_expiring' => 0, // Pas applicable pour users
                    'fin_essai_expiring' => 0, // Pas applicable pour users
                    'conges_en_attente' => 0, // Pas applicable pour users
                    'salaire_moyen' => 0, // Pas applicable pour users
                ];
            }
        } catch (\Exception $e) {
            $rhStats = [
                'total' => 0, 'actifs' => 0, 'inactifs' => 0, 'en_essai' => 0,
                'realties' => 0, 'contrats_cdi' => 0, 'contrats_cdd' => 0,
                'contrats_expiring' => 0, 'fin_essai_expiring' => 0,
                'conges_en_attente' => 0, 'salaire_moyen' => 0
            ];
        }

        // ===== STATS FOURNISSEURS =====
        $fournisseursStats = [
            'total' => DB::table('fournisseurs')->count() ?? 0,
            'actifs' => DB::table('fournisseurs')->where('est_actif', true)->count() ?? 0,
            'contrats_actifs' => DB::table('contrat_fournisseurs')->where('statut', 'en_cours')->count() ?? 0,
            'commandes_livrees' => DB::table('commande_fournisseurs')->where('statut', 'livree')->count() ?? 0,
        ];

        // ===== STATS OPÉRATIONS =====
        $operationsStats = [
            'total' => DB::table('operations')->count() ?? 0,
            'encaissees' => DB::table('operations')->where('statut', 'ENCAISSEE')->count() ?? 0,
            'validees' => DB::table('operations')->where('statut', 'VALIDEE')->count() ?? 0,
            'ca_total' => DB::table('operations')->sum('montant') ?? 0,
        ];

        // ===== STATS CLIENTS =====
        $clientsStats = [
            'total' => DB::table('clients')->count() ?? 0,
            'actifs' => DB::table('clients')->where('actif', true)->count() ?? 0,
            'commandes' => DB::table('commande_clients')->count() ?? 0,
        ];

        // ===== STATS COMPTABILITÉ =====
        $comptabiliteStats = [
            'total_factures' => DB::table('factures')->count() ?? 0,
            'factures_payees' => DB::table('factures')->where('statut', 'payee')->count() ?? 0,
            'total_encaissements' => DB::table('encaissements')->sum('montant') ?? 0,
        ];

        // ===== STATS VÉHICULES =====
        $vehiculesStats = [
            'total' => DB::table('vehicules')->count() ?? 0,
            'disponibles' => DB::table('vehicules')->where('disponible', true)->count() ?? 0,
            'en_mission' => DB::table('vehicules')->where('disponible', false)->count() ?? 0,
        ];

        return view('rh.dashboard', compact(
            'rhStats',
            'fournisseursStats',
            'operationsStats',
            'clientsStats',
            'comptabiliteStats',
            'vehiculesStats'
        ));
    }

    public function index()
    {
        try {
            // Vérifier si la table personnel existe
            if (!Schema::hasTable('personnel')) {
                // Fallback: utiliser la table users
                $personnels = \App\Models\User::where('role', '!=', 'admin')
                    ->where('role', '!=', 'superadmin')
                    ->orderBy('name')
                    ->paginate(20);

                $stats = [
                    'total' => \App\Models\User::where('role', '!=', 'admin')->where('role', '!=', 'superadmin')->count(),
                    'actifs' => \App\Models\User::where('role', '!=', 'admin')->where('role', '!=', 'superadmin')->where('est_actif', 1)->count(),
                    'enEssai' => 0, // Pas applicable pour users
                    'contratsExpirants' => 0, // Pas applicable pour users
                    'parService' => collect([]) // Pas applicable pour users
                ];
            } else {
                // Utiliser la table personnel normalement
                $query = Personnel::with(['user', 'createdBy']);
                
                // Vérifier quelle colonne de tri utiliser - CORRECTION PRINCIPALE
                if (Schema::hasColumn('personnel', 'nom')) {
                    $query->orderBy('nom');
                } elseif (Schema::hasColumn('personnel', 'name')) {
                    $query->orderBy('name');
                } elseif (Schema::hasColumn('personnel', 'matricule')) {
                    $query->orderBy('matricule');
                } elseif (Schema::hasColumn('personnel', 'prenom')) {
                    $query->orderBy('prenom');
                } else {
                    $query->orderBy('id');
                }
                
                $personnels = $query->paginate(20);

                // Vérifier les colonnes existantes pour les stats
                $hasStatut = Schema::hasColumn('personnel', 'statut');
                $hasService = Schema::hasColumn('personnel', 'service');

                $stats = [
                    'total' => Personnel::count(),
                    'actifs' => $hasStatut ? Personnel::where('statut', 'ACTIF')->count() : 0,
                    'enEssai' => $hasStatut ? Personnel::where('statut', 'EN_ESSAI')->count() : 0,
                    'contratsExpirants' => 0, // Nécessite date_fin_contrat, mis à 0 pour éviter l'erreur
                    'parService' => $hasService ? Personnel::select('service', DB::raw('count(*) as nb'))
                        ->groupBy('service')
                        ->get() : collect([])
                ];
            }

            return view('rh.personnel.index', compact('personnels', 'stats'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données vides
            return view('rh.personnel.index', [
                'personnels' => collect([]),
                'stats' => [
                    'total' => 0,
                    'actifs' => 0,
                    'enEssai' => 0,
                    'contratsExpirants' => 0,
                    'parService' => collect([])
                ]
            ]);
        }
    }

    public function create()
    {
        $suggestedMatricule = $this->generateSuggestedMatricule();

        // Liste des services disponibles
        $services = [
            'Direction Générale',
            'Ressources Humaines',
            'Finance et Comptabilité',
            'Commercial',
            'Production',
            'Logistique',
            'Maintenance',
            'Qualité',
            'Informatique',
            'Administration'
        ];

        return view('rh.personnel.create', compact('suggestedMatricule', 'services'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:personnel,email',
                'telephone' => 'nullable|string|max:20',
                'matricule' => 'required|string|max:50|unique:personnel,matricule',
                'poste' => 'nullable|string|max:255',
                'departement' => 'nullable|string|max:255',
                'service' => 'nullable|string|max:255',
                'date_embauche' => 'nullable|date',
                'salaire_base' => 'nullable|numeric|min:0',
                'type_contrat' => 'nullable|string|max:50',
                'statut' => 'nullable|string|max:50',
            ]);

            $personnel = new Personnel();
            $personnel->nom = $request->nom;
            $personnel->prenom = $request->prenom;
            $personnel->email = $request->email;
            $personnel->telephone = $request->telephone;
            $personnel->matricule = $request->matricule;
            $personnel->poste = $request->poste;
            $personnel->departement = $request->departement;
            $personnel->service = $request->service;
            $personnel->date_embauche = $request->date_embauche;
            $personnel->salaire_base = $request->salaire_base;
            $personnel->type_contrat = $request->type_contrat;
            $personnel->statut = $request->statut ?? 'ACTIF';
            $personnel->created_by = Auth::id();
            $personnel->save();

            return redirect()->route('rh.personnel.index')
                ->with('success', 'Employé ajouté avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generateSuggestedMatricule()
    {
        $prefix = 'EMP';
        $year = date('Y');
        $sequence = Personnel::whereYear('created_at', $year)->count() + 1;
        return $prefix . $year . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
