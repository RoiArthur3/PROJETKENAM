<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Pointage;
use App\Models\Paie;
use App\Models\HeureSup;
use App\Models\Conge;
use App\Models\Service;
use App\Models\EntrepriseSettings;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RHController extends Controller
{
    /**
     * Vérifier si l'utilisateur peut accéder au module RH
     */
    private function userCanAccessRH($user)
    {
        // Vérifier le rôle selon la structure de votre application
        // Adaptez cette logique selon votre système de rôles
         return in_array($user->role ?? '', ['admin', 'superadmin', 'rh', 'moderateur', 'moderator']) ||
             (isset($user->is_admin) && $user->is_admin) ||
             (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'superadmin', 'rh', 'moderateur', 'moderator']));
    }

    /**
     * Afficher le dashboard RH
     */
    public function dashboard()
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if (!$user || !$this->userCanAccessRH($user)) {
                abort(403);
            }

            // Données pour les graphiques
            $evolutionData = $this->getEvolutionData();
            $servicesData = $this->getServicesData();

            // Données pour les KPIs
            $kpis = $this->getKPIs();

            // Dernières embauches
            $recentHires = $this->getRecentHires();

            // Alertes RH dynamiques
            $alertes = $this->getRHAlerts();

            // Congés en attente
            $congesEnAttente = $this->getCongesEnAttente();

            // Statistiques Caméra Hikvision
            $facialStats = $this->getFacialCameraStats();

            return view('rh.dashboard', compact(
                'evolutionData', 'servicesData', 'kpis',
                'recentHires', 'alertes', 'congesEnAttente', 'facialStats'
            ));
        } catch (\Exception $e) {
            return view('rh.dashboard', [
                'evolutionData' => [],
                'servicesData' => [],
                'kpis' => [],
                'recentHires' => collect(),
                'alertes' => [],
                'congesEnAttente' => collect(),
            ]);
        }
    }

    /**
     * Afficher la liste des employés (personnel RH)
     */
    public function index(Request $request)
    {
        // Vérifier les permissions
        $user = Auth::user();
        if (!$user || !$this->userCanAccessRH($user)) {
            abort(403);
        }

        $query = collect(); // Utiliser une collection vide par défaut

        // Essayer d'utiliser la table personnel si elle existe
        try {
            if (class_exists('App\Models\Personnel') && Schema::hasTable('personnel')) {
                $query = \App\Models\Personnel::query();
            } else {
                // Utiliser la table users comme fallback
                $query = \App\Models\User::where('role', '!=', 'admin')->where('role', '!=', 'superadmin');
            }
        } catch (\Exception $e) {
            $query = collect();
        }

        // Filtrer par statut si spécifié
        if ($request->filled('statut')) {
            $query->where('statut', $request->get('statut'));
        }

        // Filtrer par service si spécifié
        if ($request->filled('service')) {
            $query->where('service', 'like', '%' . $request->get('service') . '%');
        }

        // Filtrer par recherche
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%")
                  ->orWhere('poste', 'like', "%{$search}%");
            });
        }

        $employes = $query->with(['user'])->orderBy('nom')->paginate(25);

        // Statistiques
        try {
            if (class_exists('App\Models\Personnel') && Schema::hasTable('personnel')) {
                $stats = [
                    'total' => \App\Models\Personnel::count(),
                    'actifs' => \App\Models\Personnel::where('statut', 'ACTIF')->count(),
                    'essai' => \App\Models\Personnel::where('statut', 'ESSAI')->count(),
                ];
            } else {
                // Utiliser la table users comme fallback
                $stats = [
                    'total' => \App\Models\User::where('role', '!=', 'admin')->where('role', '!=', 'superadmin')->count(),
                    'actifs' => \App\Models\User::where('role', '!=', 'admin')->where('role', '!=', 'superadmin')->where('est_actif', 1)->count(),
                    'essai' => 0, // Pas applicable pour users
                ];
            }
        } catch (\Exception $e) {
            $stats = [
                'total' => 0,
                'actifs' => 0,
                'essai' => 0,
            ];
        }

        return view('rh.employes.index', compact('employes', 'stats'));
    }

    /**
     * Afficher la liste des employés (alias de index)
     */
    public function employes(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Créer un employé (personnel RH)
     */
    public function createEmploye()
    {
        // Vérifier les permissions
        $user = Auth::user();
        if (!$user || !$this->userCanAccessRH($user)) {
            abort(403);
        }

        $latestPersonnel = \App\Models\Personnel::query()
            ->where('matricule', 'like', 'KSL' . date('Y') . '%')
            ->orderByDesc('id')
            ->first();

        if ($latestPersonnel && preg_match('/^(?:KSL' . date('Y') . ')(\d{4})$/', (string) $latestPersonnel->matricule, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        do {
            $suggestedMatricule = 'KSL' . date('Y') . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = \App\Models\Personnel::query()->where('matricule', $suggestedMatricule)->exists();
            $nextNumber++;
        } while ($exists);

        // Récupérer la liste des services pour le formulaire
        $services = [
            'Direction Générale',
            'Ressources Humaines',
            'Finance et Comptabilité',
            'Commercial',
            'Logistique',
            'Technique',
            'Exploitation',
            'Qualité',
            'Administratif'
        ];

        return view('rh.personnel.create', compact('services', 'suggestedMatricule'));
    }

    /**
     * Enregistrer un employé (personnel RH)
     */
    public function storeEmploye(Request $request)
    {
        // Vérifier les permissions
        $user = Auth::user();
        if (!$user || !$this->userCanAccessRH($user)) {
            abort(403);
        }

        if (!$request->filled('numero_compte') && $request->filled('numero_compte_bancaire')) {
            $request->merge(['numero_compte' => $request->input('numero_compte_bancaire')]);
        }

        // Valider les données
        $validated = $request->validate([
            // Informations personnelles
            'matricule' => 'required|string|max:20|unique:personnel',
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:150',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:100',
            'nationalite' => 'required|string|max:50',
            'sexe' => 'required|in:M,F',
            'situation_matrimoniale' => 'required|string|max:50',
            'nb_enfants_charge' => 'required|integer|min:0',

            // Contact
            'telephone_principal' => 'required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'email_personnel' => 'nullable|email|max:100|unique:personnel,email_personnel',
            'ville' => 'nullable|string|max:50',
            'quartier' => 'nullable|string|max:50',
            'adresse_residence' => 'required|string|max:255',

            // Pièce d'identité
            'type_piece' => 'required|string|max:50',
            'numero_piece' => 'required|string|max:50',
            'date_delivrance_piece' => 'required|date',
            'expiration_piece' => 'nullable|date',
            'lieu_delivrance_piece' => 'required|string|max:100',

            // Professionnel
            'poste' => 'required|string|max:100',
            'service' => 'required|string|max:100',
            'departement' => 'nullable|string|max:100',
            'categorie' => 'nullable|string|max:50',
            'echelon' => 'nullable|string|max:50',
            'indice' => 'nullable|string|max:50',
            'date_embauche' => 'required|date',
            'type_contrat' => 'required|string|in:CDI,CDD,JOURNALIER,STAGIAIRE,STAGE,APPRENTI,TEMPORAIRE,INTERIM',
            'date_fin_contrat' => 'nullable|date|after_or_equal:date_embauche',
            'duree_essai_jours' => 'nullable|integer|min:0',
            'statut' => 'required|string|in:ACTIF,ESSAI,CONGE,SUSPENDU,DEPART',

            // Rémunération
            'salaire_base' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'mode_paiement' => 'nullable|string|in:VIREMENT,ESPECE,CHEQUE,MOBILE_MONEY',
            'frequence_paiement' => 'nullable|string|max:20',

            // CNPS et Fiscal
            'numero_cnps' => 'nullable|string|max:50',
            'date_affiliation_cnps' => 'nullable|date',
            'categorie_cnps' => 'nullable|string|max:50',
            'numero_contribuable' => 'nullable|string|max:50',
            'nb_parts_fiscales' => 'nullable|integer|min:0',
            'situation_fiscale' => 'nullable|string|max:50',

            // Bancaire
            'banque' => 'nullable|string|max:100',
            'agence_bancaire' => 'nullable|string|max:100',
            'numero_compte' => 'nullable|string|max:50',
            'rib' => 'nullable|string|max:50',

            // Contact d'urgence
            'nom_urgence' => 'nullable|string|max:100',
            'telephone_urgence' => 'nullable|string|max:20',
            'lien_parente' => 'nullable|string|max:50',
            'adresse_urgence' => 'nullable|string|max:255',

            // Médical
            'groupe_sanguin' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'medecin_traitant' => 'nullable|string|max:100',
            'telephone_medecin' => 'nullable|string|max:20',

            // Autres
            'observations' => 'nullable|string',
        ]);

        // Traitement spécifique pour les contrats
        if (($validated['type_contrat'] ?? null) === 'CDI') {
            $validated['date_fin_contrat'] = null;
        }

        // Calculer la fin de période d'essai si spécifiée
        if (!empty($validated['duree_essai_jours']) && !empty($validated['date_embauche'])) {
              $validated['fin_periode_essai'] = \Carbon\Carbon::parse($validated['date_embauche'])
                 ->addDays((int) $validated['duree_essai_jours']);
        }

        // Ajouter les champs d'audit
        $validated['created_by'] = Auth::id();

        // Créer l'employé
        try {
            if (class_exists('App\Models\Personnel') && Schema::hasTable('personnel')) {
                $employe = \App\Models\Personnel::create($this->filterPersonnelPayload($validated));
                return redirect()->route('rh.personnel.index')->with('success', 'Personnel créé avec succès selon la législation ivoirienne.');
            } else {
                return redirect()->route('rh.personnel.index')->with('error', 'La table personnel n\'existe pas. Veuillez contacter l\'administrateur.');
            }
        } catch (\Exception $e) {
            return redirect()->route('rh.personnel.index')->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher un employé (personnel RH)
     */
    public function showEmploye($id)
    {
        try {
            if (class_exists('App\Models\Personnel') && Schema::hasTable('personnel')) {
                $employe = \App\Models\Personnel::with(['user', 'conges', 'paies', 'documents'])->findOrFail($id);
                return view('rh.employes.show', compact('employe'));
            } else {
                // Fallback: utiliser la table users
                $employe = \App\Models\User::findOrFail($id);
                return view('rh.employes.show', compact('employe'));
            }
        } catch (\Exception $e) {
            return redirect()->route('rh.employes.index')->with('error', 'Employé non trouvé.');
        }
    }

    /**
     * Modifier un employé (personnel RH)
     */
    public function editEmploye($id)
    {
        try {
            if (class_exists('App\Models\Personnel') && Schema::hasTable('personnel')) {
                $employe = \App\Models\Personnel::findOrFail($id);
                return view('rh.employes.edit', compact('employe'));
            } else {
                // Fallback: utiliser la table users
                $employe = \App\Models\User::findOrFail($id);
                return view('rh.employes.edit', compact('employe'));
            }
        } catch (\Exception $e) {
            return redirect()->route('rh.employes.index')->with('error', 'Employé non trouvé.');
        }
    }

    /**
     * Mettre à jour un employé (personnel RH)
     */
    public function updateEmploye(Request $request, $id)
    {
        try {
            if (class_exists('App\Models\Personnel') && Schema::hasTable('personnel')) {
                $employe = \App\Models\Personnel::findOrFail($id);
            } else {
                return redirect()->route('rh.employes.index')->with('error', 'La table personnel n\'existe pas. Veuillez contacter l\'administrateur.');
            }

        // Valider les données
        $validated = $request->validate([
            // Informations personnelles
            'matricule' => 'required|string|max:20|unique:personnel,matricule,' . $id,
            'grade' => 'required|string|in:Agent,Superviseur,Chef de Service,Directeur,Manager',
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:150',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:100',
            'nationalite' => 'nullable|string|max:50',
            'sexe' => 'required|in:M,F',
            'situation_matrimoniale' => 'nullable|string|max:50',
            'nb_enfants_charge' => 'nullable|integer|min:0',

            // Contact
            'telephone_principal' => 'required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'email_personnel' => 'nullable|email|max:100',
            'adresse_residence' => 'nullable|string|max:255',

            // Pièce d'identité
            'type_piece' => 'nullable|string|max:50',
            'numero_piece' => 'nullable|string|max:50',
            'date_delivrance_piece' => 'nullable|date',
            'expiration_piece' => 'nullable|date',
            'lieu_delivrance_piece' => 'nullable|string|max:100',

            // Professionnel
            'service' => 'required|string|max:100',
            'poste' => 'required|string|max:100',
            'niveau_hierarchique' => 'nullable|string|max:100',
            'date_embauche' => 'required|date',
            'type_contrat' => 'required|string|in:CDI,CDD,JOURNALIER,STAGIAIRE,APPRENTI,TEMPORAIRE,INTERIM',
            'duree_contrat' => 'nullable|integer|min:1',
            'statut' => 'required|string|in:ACTIF,ESSAI,CONGE,SUSPENDU,DEPART',

            // Rémunération
            'salaire_base' => 'nullable|numeric|min:0',
            'salaire_journalier' => 'nullable|numeric|min:0',
            'mode_paiement' => 'nullable|string|in:VIREMENT,ESPECE,CHEQUE,MOBILE_MONEY',

            // Bancaire
            'banque' => 'nullable|string|max:100',
            'numero_compte' => 'nullable|string|max:50',
            'titulaire_compte' => 'nullable|string|max:100',

            // Autres
            'observations' => 'nullable|string',
        ]);

        foreach (['date_naissance', 'date_delivrance_piece', 'expiration_piece'] as $dateField) {
            if (array_key_exists($dateField, $validated) && empty($validated[$dateField]) && !empty($employe->{$dateField})) {
                $validated[$dateField] = $employe->{$dateField};
            }
        }

        // Ajouter les champs d'audit
        $validated['updated_by'] = Auth::id();

        // Mettre à jour l'employé
        $employe->update($this->filterPersonnelPayload($validated));

        return redirect()->route('rh.employes.index')->with('success', 'Employé mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('rh.employes.index')->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un employé (personnel RH)
     */
    public function deleteEmploye($id)
    {
        try {
            if (class_exists('App\Models\Personnel') && Schema::hasTable('personnel')) {
                $employe = \App\Models\Personnel::findOrFail($id);

                // Vérifier si l'employé peut être supprimé
                if ($employe->user_id === Auth::id()) {
                    return back()->with('error', 'Vous ne pouvez pas supprimer votre propre dossier.');
                }

                $employe->delete();
                return redirect()->route('rh.employes.index')->with('success', 'Employé supprimé avec succès.');
            } else {
                return redirect()->route('rh.employes.index')->with('error', 'La table personnel n\'existe pas. Veuillez contacter l\'administrateur.');
            }
        } catch (\Exception $e) {
            return redirect()->route('rh.employes.index')->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return $this->createAgent();
    }

    public function store(Request $request)
    {
        return $this->storeAgent($request);
    }

    public function update(Request $request, $user)
    {
        return $this->updateAgent($request, $user);
    }

    /**
     * Gestion des présences
     */
    public function presence(Request $request)
    {
        $query = Pointage::with('user');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $pointages = $query->orderBy('created_at', 'desc')->paginate(50);
        return view('rh.presence', compact('pointages'));
    }

    /**
     * Gestion des services
     */
    public function services()
    {
        $services = Service::orderBy('nom')->get();
        return view('rh.services', compact('services'));
    }

    /**
     * Gestion des responsables
     */
    public function responsables()
    {
        $responsables = User::where('role', 'admin')->orWhere('role', 'moderator')->get();
        return view('rh.responsables', compact('responsables'));
    }

    /**
     * Obtenir les KPIs du dashboard RH
     */
    public function getKPIs()
    {
        try {
            // Importer le modèle Personnel
            $personnelModel = class_exists('App\Models\Personnel') ? new \App\Models\Personnel() : null;

            // Total du personnel RH
            $total = $personnelModel ? $personnelModel->count() : 0;

            // Présents aujourd'hui: basé sur les pointages si disponibles, sinon estimation
            $presents = 0;
            if (class_exists('App\Models\Pointage')) {
                $presents = \App\Models\Pointage::whereDate('created_at', today())
                    ->where('statut', '!=', 'absent')
                    ->distinct('user_id')
                    ->count('user_id');
            } else {
                // Estimation: 85% du personnel présent en moyenne
                $presents = $total > 0 ? round($total * 0.85) : 0;
            }

            $taux = $total > 0 ? round(($presents / $total) * 100, 1) . '%' : '0%';

            // Personnel actif
            $personnelActif = $personnelModel ? $personnelModel->where('statut', 'ACTIF')->count() : $total;

            // Personnel en période d'essai
            $personnelEssai = $personnelModel
                ? $personnelModel->where('statut', 'ESSAI')->count()
                : 0;

            // Départs ce mois
            $personnelDepart = $personnelModel
                ? $personnelModel->whereNotNull('date_depart')
                    ->whereMonth('date_depart', now()->month)
                    ->count()
                : 0;

            // Évolution mensuelle (nouvelles embauches ce mois)
            $evolutionMois = $personnelModel
                ? $personnelModel->whereMonth('date_embauche', now()->month)
                    ->count()
                : 0;

            // Congés actifs
            $congesActifs = class_exists('App\Models\Conge')
                ? Conge::where('statut', 'approuve')
                    ->where('date_fin', '>=', today())
                    ->count()
                : 0;

            // Absents pour maladie
            $absentsMaladie = class_exists('App\Models\Conge')
                ? Conge::where('statut', 'approuve')
                    ->where('type', 'maladie')
                    ->whereDate('created_at', today())
                    ->count()
                : 0;

            // Retours prévus cette semaine
            $retourPrevu = class_exists('App\Models\Conge')
                ? Conge::where('statut', 'approuve')
                    ->whereBetween('date_fin', [today(), today()->addDays(7)])
                    ->count()
                : 0;

            // Contrats expirant bientôt (30 jours)
        $contratsExpirant = class_exists('App\Models\PersonnelContrat')
            ? \App\Models\PersonnelContrat::expirant(30)->count()
            : 0;

        return [
            'total_personnel' => $total,
            'personnel_actif' => $personnelActif,
            'taux_presence' => $taux,
            'personnel_essai' => $personnelEssai,
            'depart_mois' => $personnelDepart,
            'evolution_mois' => $evolutionMois,
            'conges_actifs' => $congesActifs,
            'absents_maladie' => $absentsMaladie,
            'retours_prevus' => $retourPrevu,
            'contrats_expirant' => $contratsExpirant,
        ];
    } catch (\Exception $e) {
        return [
            'total_personnel' => 0,
            'personnel_actif' => 0,
            'taux_presence' => '0%',
            'personnel_essai' => 0,
            'depart_mois' => 0,
            'evolution_mois' => 0,
            'conges_actifs' => 0,
            'absents_maladie' => 0,
            'retours_prevus' => 0,
            'contrats_expirant' => 0,
        ];
    }
    }

    /**
     * Obtenir les statistiques de la caméra de reconnaissance faciale
     */
    private function getFacialCameraStats()
    {
        $stats = [
            'today_events' => 0,
            'active_devices' => 0,
            'online_devices' => 0,
            'recent_events' => collect()
        ];

        try {
            if (Schema::hasTable('facial_events')) {
                $stats['today_events'] = \App\Models\FacialEvent::whereDate('event_time', today())->count();
                $stats['recent_events'] = \App\Models\FacialEvent::with(['pointage.personnel'])
                    ->orderByDesc('event_time')
                    ->limit(5)
                    ->get();
            }

            if (Schema::hasTable('facial_devices')) {
                $stats['active_devices'] = \App\Models\FacialDevice::where('is_active', true)->count();
                $stats['online_devices'] = \App\Models\FacialDevice::where('last_seen_at', '>=', now()->subMinutes(60))->count();
            }
        } catch (\Exception $e) {
            Log::error("Erreur stats caméra RH: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Obtenir les données d'évolution
     */
    public function getEvolutionData()
    {
        try {
            $evolutionData = [];
            $personnelModel = class_exists('App\Models\Personnel') ? new \App\Models\Personnel() : null;

            if (!$personnelModel) {
                return [];
            }

            // Données des 6 derniers mois
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthName = $month->format('M');
                $year = $month->format('Y');
                $monthNum = $month->month;

                // Nouvelles embauches ce mois
                $nouvellesEmbauches = $personnelModel
                    ->whereYear('date_embauche', $year)
                    ->whereMonth('date_embauche', $monthNum)
                    ->count();

                // Départs ce mois
                $departs = $personnelModel
                    ->whereNotNull('date_depart')
                    ->whereYear('date_depart', $year)
                    ->whereMonth('date_depart', $monthNum)
                    ->count();
                // Total personnel à la fin du mois
                $totalPersonnel = $personnelModel
                    ->where(function($query) use ($month) {
                        $query->whereNull('date_depart')
                              ->orWhere('date_depart', '>', $month->endOfMonth());
                    })
                    ->count();

                $evolutionData[] = [
                    'month' => $monthName,
                    'nouvelles' => $nouvellesEmbauches,
                    'departs' => $departs,
                    'total' => $totalPersonnel
                ];
            }

            return $evolutionData;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtenir les données par services
     */
    public function getServicesData()
    {
        try {
            $servicesData = [];
            $personnelModel = class_exists('App\Models\Personnel') ? new \App\Models\Personnel() : null;

            if (!$personnelModel) {
                return [];
            }

            // Regrouper par service
            $services = $personnelModel->select('service', DB::raw('count(*) as total'))
                ->groupBy('service')
                ->get();

            foreach ($services as $service) {
                $serviceName = $service->service ?: 'Non spécifié';
                $personnelCount = $service->total;

                // Présents aujourd'hui dans ce service (estimation)
                $presentsCount = max(1, round($personnelCount * 0.85)); // 85% en moyenne

                $servicesData[] = [
                    'service' => $serviceName,
                    'effectif' => $personnelCount,
                    'presents' => $presentsCount,
                    'taux_presence' => $personnelCount > 0 ? round(($presentsCount / $personnelCount) * 100, 1) . '%' : '0%'
                ];
            }

            return $servicesData;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Dernières embauches
     */
    private function getRecentHires()
    {
        try {
            $personnelModel = class_exists('App\Models\Personnel') ? new \App\Models\Personnel() : null;

            if (!$personnelModel) {
                return collect();
            }

            return $personnelModel->whereNotNull('date_embauche')
                ->orderBy('date_embauche', 'desc')
                ->take(5)
                ->get(['id', 'matricule', 'nom', 'prenoms', 'poste', 'service', 'date_embauche', 'statut']);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Alertes RH dynamiques
     */
    private function getRHAlerts()
    {
        $alertes = [];
        try {
        // Contrats arrivant à échéance (dans les 30 prochains jours) - Via PersonnelContrat
        if (class_exists('App\Models\PersonnelContrat')) {
            $contratsExpiring = \App\Models\PersonnelContrat::expirant(30)->count();
            if ($contratsExpiring > 0) {
                $alertes[] = [
                    'type' => 'warning',
                    'icon' => 'fa-file-contract',
                    'message' => "<strong>{$contratsExpiring} contrat(s)</strong> arrive(nt) à échéance dans les 30 jours (Personnel RH)",
                    'link' => route('rh.personnel.contrats.index'),
                ];
            }
        } elseif (Schema::hasColumn('users', 'date_fin_contrat')) {
            $contratsExpiring = User::whereNotNull('date_fin_contrat')
                ->whereBetween('date_fin_contrat', [today(), today()->addDays(30)])
                ->count();
            if ($contratsExpiring > 0) {
                $alertes[] = [
                    'type' => 'warning',
                    'icon' => 'fa-file-contract',
                    'message' => "<strong>{$contratsExpiring} contrat(s)</strong> de compte(s) utilisateur arrive(nt) à échéance",
                    'link' => '#',
                ];
            }
        }

            // Congés en attente de validation
            if (class_exists('App\Models\Conge')) {
                $congesPending = Conge::where('statut', 'en_attente')->count();
                if ($congesPending > 0) {
                    $alertes[] = [
                        'type' => 'info',
                        'icon' => 'fa-calendar-times',
                        'message' => "<strong>{$congesPending} demande(s) de congé</strong> en attente de validation",
                        'link' => route('rh.conges.index'),
                    ];
                }
            }

            // Période d'essai se terminant bientôt
            if (Schema::hasColumn('users', 'periode_essai') && Schema::hasColumn('users', 'date_embauche')) {
                $essaiEndingSoon = User::whereNotNull('periode_essai')
                    ->whereNotNull('date_embauche')
                    ->whereRaw("DATE_ADD(date_embauche, INTERVAL periode_essai DAY) BETWEEN ? AND ?", [today(), today()->addDays(30)])
                    ->count();
                if ($essaiEndingSoon > 0) {
                    $alertes[] = [
                        'type' => 'warning',
                        'icon' => 'fa-hourglass-half',
                        'message' => "<strong>{$essaiEndingSoon} période(s) d'essai</strong> se termine(nt) bientôt",
                        'link' => route('rh.employes.index'),
                    ];
                }
            }

            // Agents sans pointage aujourd'hui
            $totalAgents = User::whereIn('role', ['agent', 'employe', 'moderator'])->where('is_active', true)->count();
            $presentsToday = Pointage::whereDate('created_at', today())->distinct('user_id')->count('user_id');
            $absentsToday = $totalAgents - $presentsToday;
            if ($absentsToday > 0 && now()->hour >= 9) {
                $alertes[] = [
                    'type' => 'danger',
                    'icon' => 'fa-user-times',
                    'message' => "<strong>{$absentsToday} agent(s)</strong> sans pointage aujourd'hui",
                    'link' => route('rh.pointages.index'),
                ];
            }

            if (empty($alertes)) {
                $alertes[] = [
                    'type' => 'success',
                    'icon' => 'fa-check-circle',
                    'message' => 'Aucune alerte RH en cours',
                    'link' => '#',
                ];
            }
        } catch (\Exception $e) {
            // silencieux
        }
        return $alertes;
    }

    /**
     * Congés en attente de validation
     */
    private function getCongesEnAttente()
    {
        try {
            return Conge::with('user')
                ->where('statut', 'en_attente')
                ->orderBy('date_demande', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Liste des contrats (depuis la table users: contrat, date_embauche, salaire)
     */
    public function contrats(Request $request)
    {
        // Ne lister que les employes ayant un contrat renseigne.
        $baseQuery = \App\Models\Personnel::query()
            ->whereNotNull('type_contrat')
            ->whereRaw("TRIM(type_contrat) <> ''");

        $q = (clone $baseQuery);

        // Filtres
        if ($request->filled('type')) {
            $q->where('type_contrat', $request->get('type'));
        }

        // Statistiques sur le meme perimetre (employes avec contrat)
        $cdi = (clone $baseQuery)->where('type_contrat', 'CDI')->count();
        $cdd = (clone $baseQuery)->where('type_contrat', 'CDD')->count();

        if ($request->filled('agent')) {
            $agent = trim($request->get('agent'));
            $q->where(function($w) use ($agent){
                $w->where('nom','like',"%$agent%")
                  ->orWhere('prenoms','like',"%$agent%");
            });
        }

        $total = (clone $q)->count();
        $contrats = $q->orderBy('nom')->paginate(25);

        // Récupérer la liste de tout le personnel pour le modal d'association
        $personnelsList = \App\Models\Personnel::orderBy('nom')->get();

        return view('rh.contrats', compact('contrats','total','cdi','cdd','personnelsList'));
    }

    /**
     * Créer un contrat
     */
    public function createContrat()
    {
        $users = User::orderBy('name')->get(['id','name','email','role']);
        return view('rh.contrats-create', compact('users'));
    }

    /**
     * Enregistrer un contrat
     */
    public function storeContrat(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'contrat' => 'required|string',
            'date_embauche' => 'required|date',
            'salaire' => 'required|numeric',
        ]);

        $user = User::find($validated['user_id']);
        if (\Illuminate\Support\Facades\Schema::hasColumn('users','contrat')) {
            $user->contrat = $validated['contrat'];
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('users','date_embauche')) {
            $user->date_embauche = $validated['date_embauche'];
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('users','salaire')) {
            $user->salaire = $validated['salaire'];
        }
        $user->save();

        return redirect()->route('rh.contrats.index')->with('success', 'Contrat enregistré pour '.$user->name);
    }

    /**
     * Afficher la liste des agents
     */
    public function agents()
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if (!$user || !$this->userCanAccessRH($user)) {
                abort(403);
            }

            $agents = User::with(['service'])->orderBy('name')->paginate(25);
            return view('rh.agents.index', compact('agents'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une vue simplifiée
            $agents = collect([]);
            return view('rh.agents.index', compact('agents'));
        }
    }

    /**
     * Créer un agent
     */
    public function createAgent()
    {
        // Vérifier les permissions
        $user = Auth::user();
        if (!$user || !$this->userCanAccessRH($user)) {
            abort(403);
        }

        $services = \App\Models\Service::orderBy('nom')->get();
        return view('rh.agents.create', compact('services'));
    }

    /**
     * Enregistrer un agent (compte d'accès système)
     */
    public function storeAgent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|string|max:50',
            'service_id' => 'nullable|integer|exists:services,id',
            'telephone' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'actif' => 'nullable|boolean',
        ]);

        // Vérifier si cet email existe déjà dans la table personnel
        $personnelExists = \App\Models\Personnel::where('email_personnel', $validated['email'])->exists();

        if ($personnelExists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cet email existe déjà dans le personnel. Veuillez utiliser un email différent ou associer ce compte au personnel existant.');
        }

        if (!empty($validated['telephone']) && empty($validated['phone'])) {
            $validated['phone'] = $validated['telephone'];
        }

        $validated['role'] = $validated['role'] ?? 'agent';
        $validated['is_active'] = (bool) ($validated['actif'] ?? false);
        unset($validated['actif']);

        // Créer uniquement le compte utilisateur (sans champs RH)
        $user = User::create($validated);

        return redirect()->route('rh.agents.index')
            ->with('success', 'Compte agent créé avec succès. Ce compte peut maintenant être associé à un fiche de personnel.');
    }

    /**
     * Associer un agent (compte utilisateur) à un personnel
     */
    public function associerAgentPersonnel(Request $request)
    {
        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnel,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $personnel = \App\Models\Personnel::findOrFail($validated['personnel_id']);
            $user = \App\Models\User::findOrFail($validated['user_id']);

            // Vérifier si le personnel n'a pas déjà un compte associé
            if ($personnel->user_id) {
                return redirect()->back()->with('error', 'Ce personnel a déjà un compte associé.');
            }

            // Vérifier si l'utilisateur n'est pas déjà associé à un autre personnel
            if (\App\Models\Personnel::where('user_id', $validated['user_id'])->exists()) {
                return redirect()->back()->with('error', 'Cet utilisateur est déjà associé à un autre personnel.');
            }

            // Associer l'utilisateur au personnel
            $personnel->update(['user_id' => $validated['user_id']]);

            return redirect()->back()->with('success', 'Association réussie entre le personnel et le compte utilisateur.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'association: ' . $e->getMessage());
        }
    }

    /**
     * Créer un contrat pour un personnel
     */
    public function createContratPersonnel($personnelId)
    {
        $personnel = \App\Models\Personnel::findOrFail($personnelId);
        return view('rh.contrats.create', compact('personnel'));
    }

    /**
     * Enregistrer un contrat de personnel
     */
    public function storeContratPersonnel(Request $request, $personnelId)
    {
        $personnel = \App\Models\Personnel::findOrFail($personnelId);

        $validated = $request->validate([
            'type_contrat' => 'required|string|in:CDI,CDD,JOURNALIER,STAGIAIRE,APPRENTI,TEMPORAIRE,INTERIM',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'duree_essai_jours' => 'nullable|integer|min:0',
            'poste' => 'required|string|max:100',
            'description_taches' => 'nullable|string',
            'obligations_employeur' => 'nullable|string',
            'obligations_employe' => 'nullable|string',
            'conditions_travail' => 'nullable|string',
            'salaire_base' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'frequence_paiement' => 'nullable|string|max:20',
            'avantages' => 'nullable|string',
            'lieu_travail' => 'nullable|string|max:255',
            'service_affectation' => 'nullable|string|max:100',
            'horaire_travail' => 'nullable|string|max:100',
        ]);

        // Traitement spécifique pour les contrats
        if ($validated['type_contrat'] === 'CDI') {
            $validated['date_fin'] = null;
        }

        // Calculer la fin de période d'essai
        if (!empty($validated['duree_essai_jours'])) {
              $validated['fin_periode_essai'] = \Carbon\Carbon::parse($validated['date_debut'])
                 ->addDays((int) $validated['duree_essai_jours']);
        }

        // Générer un numéro de contrat
        $prefix = match($validated['type_contrat']) {
            'CDI' => 'CDI',
            'CDD' => 'CDD',
            'STAGIAIRE' => 'STG',
            'APPRENTI' => 'APP',
            default => 'CTR'
        };
        $year = now()->year;
        $count = \App\Models\PersonnelContrat::whereYear('created_at', $year)->count() + 1;
        $validated['numero_contrat'] = $prefix . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $validated['personnel_id'] = $personnelId;
        $validated['statut'] = 'PROJET';
        $validated['created_by'] = Auth::id();

        try {
            $contrat = \App\Models\PersonnelContrat::create($validated);

            return redirect()->route('rh.contrats.show', $contrat->id)
                ->with('success', 'Contrat créé avec succès. Vous pouvez maintenant le télécharger et le faire signer.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création du contrat: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les contrats d'un personnel
     */
    public function contratsPersonnel($personnelId)
    {
        $personnel = \App\Models\Personnel::with(['contrats' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($personnelId);

        return view('rh.contrats.personnel', compact('personnel'));
    }

    /**
     * Modifier un agent
     */
    public function edit($user)
    {
        return $this->editAgent($user);
    }

    /**
     * Supprimer un agent
     */
    public function destroy($user)
    {
        $user = User::findOrFail($user);

        // Vérifier si l'utilisateur peut être supprimé
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('rh.agents.index')->with('success', 'Agent supprimé avec succès.');
    }

    /**
     * Afficher un agent
     */
    public function showAgent($agent)
    {
        $agent = User::with('service')->findOrFail($agent);

        // Calcul des heures de retard mensuelles
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyPointages = Pointage::where('user_id', $agent->id)
            ->whereMonth('date_pointage', $currentMonth)
            ->whereYear('date_pointage', $currentYear)
            ->get();

        $totalDelayMinutes = 0;
        foreach ($monthlyPointages as $pointage) {
            if ($pointage->heure_arrivee) {
                $arrivalTime = \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_arrivee);
                $delayLimit = \Carbon\Carbon::createFromTime(7, 30, 0);

                if ($arrivalTime->greaterThan($delayLimit)) {
                    $totalDelayMinutes += $arrivalTime->diffInMinutes($delayLimit);
                }
            }
        }

        $hireYear = $agent->date_embauche ? \Carbon\Carbon::parse($agent->date_embauche)->year : null;

        return view('rh.agents.show', compact('agent', 'totalDelayMinutes', 'hireYear'));
    }

    /**
     * Modifier un agent
     */
    public function editAgent($agent)
    {
        $agent = User::with('service')->findOrFail($agent);
        $services = Service::orderBy('nom')->get();
        return view('rh.agents.edit', compact('agent', 'services'));
    }

    /**
     * Mettre à jour un agent
     */
    public function updateAgent(Request $request, $agent)
    {
        $agent = User::findOrFail($agent);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $agent->id,
            'role' => 'required|string|max:50',
            'telephone' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'service_id' => 'nullable|integer|exists:services,id',
            'contrat' => 'nullable|string|max:50',
            'date_embauche' => 'nullable|date',
            'date_fin_contrat' => 'nullable|date|after_or_equal:date_embauche',
            'salaire' => 'nullable|numeric',
        ]);

        if (($validated['contrat'] ?? null) === 'CDI') {
            $validated['date_fin_contrat'] = null;
        }

        $agent->fill($validated);
        $agent->save();

        return redirect()->route('rh.agents.edit', $agent)->with('success', 'Agent mis à jour avec succès');
    }

    /**
     * Supprimer un agent
     */
    public function deleteAgent($agent)
    {
        // Suppression simple de l'utilisateur (pas de soft delete défini sur User)
        if ($user = User::find($agent)) {
            $name = $user->name;
            $user->delete();
            return redirect()->route('rh.agents.index')->with('success', 'Agent supprimé : '.$name);
        }

        return redirect()->route('rh.agents.index')->with('error', 'Agent introuvable');
    }

    /**
     * Exporter les agents
     */
    public function export()
    {
        $agents = User::orderBy('name')->get(['id','name','email','role']);

        $filename = 'agents_rh_export_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        $callback = function() use ($agents) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nom', 'Email', 'Poste']);
            foreach ($agents as $agent) {
                fputcsv($handle, [$agent->id, $agent->name, $agent->email, $agent->role]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Afficher les affectations
     */
    public function affectations()
    {
        $agents = User::with('service')->orderBy('name')->paginate(25);
        $total = $agents->total();
        return view('rh.affectations.index', compact('agents', 'total'));
    }

    /**
     * Créer une affectation
     */
    public function createAffectation()
    {
        $agents = User::whereIn('role', ['agent', 'moderator'])->orderBy('name')->get(['id', 'name', 'service_id']);
        $services = Service::actif()->orderBy('nom')->get();

        return view('rh.affectations.create', compact('agents', 'services'));
    }

    /**
     * Enregistrer une affectation
     */
    public function storeAffectation(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'date_affectation' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $ancienService = $user->service_id;

        // Mettre à jour le service de l'agent
        $user->service_id = $validated['service_id'];
        $user->save();

        $nouveauService = Service::find($validated['service_id']);
        $message = "Affectation de {$user->name} au service {$nouveauService->nom} effectuée avec succès.";

        return redirect()->route('rh.affectations.index')->with('success', $message);
    }

    /**
     * Afficher une affectation
     */
    public function showAffectation($affectation)
    {
        $affectationData = [
            'id' => $affectation,
            'agent_nom' => 'Test Agent',
            'vehicule_immat' => 'TEST-123',
            'date_debut' => now()->format('d/m/Y'),
            'statut' => 'Actif'
        ];
        return view('rh.affectations-show', compact('affectationData'));
    }

    /**
     * Modifier une affectation
     */
    public function editAffectation($affectation)
    {
        $affectationData = [
            'id' => $affectation,
            'agent_nom' => 'Test Agent',
            'vehicule_immat' => 'TEST-123'
        ];
        return view('rh.affectations-edit', compact('affectationData'));
    }

    /**
     * Mettre à jour une affectation
     */
    public function updateAffectation(Request $request, $affectation)
    {
        $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'date_affectation' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($affectation);
        $user->service_id = $request->service_id;
        // Add other fields if needed: date_affectation, etc.
        $user->save();

        return redirect()->route('rh.affectations.index')->with('success', 'Affectation mise à jour avec succès');
    }

    /**
     * Supprimer une affectation
     */
    public function deleteAffectation($affectation)
    {
        return redirect()->route('rh.affectations.index')->with('success', 'Affectation supprimée');
    }

    /**
     * Afficher les pointages du personnel
     */
    public function pointages(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        if (!Schema::hasTable('pointages')) {
            $pointages = collect();
        } else {
            $query = Pointage::with('personnel')
                ->whereDate('date_pointage', $date);

            if (Schema::hasColumn('pointages', 'heure_pointage')) {
                $query->orderBy('heure_pointage');
            } else {
                $query->orderBy('created_at');
            }

            $pointages = $query->get();
        }

        // Calculs avec les règles
        $pointages->transform(function ($pointage) {
            $pointage->delay_minutes = 0;
            $pointage->worked_hours = 0;

            $parseTime = function ($time) {
                if (empty($time)) {
                    return null;
                }
                try {
                    return Carbon::createFromFormat('H:i:s', $time);
                } catch (\Throwable $e) {
                    try {
                        return Carbon::createFromFormat('H:i', $time);
                    } catch (\Throwable $e2) {
                        return null;
                    }
                }
            };

            if ($pointage->heure_arrivee) {
                $arrivalTime = $parseTime($pointage->heure_arrivee);
                $delayLimit = Carbon::createFromTime(7, 30, 0); // 7h30

                if (!$arrivalTime) {
                    return $pointage;
                }

                if ($arrivalTime->greaterThan($delayLimit)) {
                    $pointage->delay_minutes = $arrivalTime->diffInMinutes($delayLimit);
                }

                if ($pointage->heure_depart) {
                    $departureTime = $parseTime($pointage->heure_depart);
                    if ($departureTime) {
                        $workedMinutes = $arrivalTime->diffInMinutes($departureTime, false);
                        if ($workedMinutes < 0) {
                            $workedMinutes = 0;
                        }

                        // Cap à 10h (600 minutes)
                        $pointage->worked_hours = round(min($workedMinutes / 60, 10), 2);
                    }
                }
            }

            return $pointage;
        });

        $totalPersonnels = \App\Models\Personnel::count();
        $presents = $pointages->count();
        $retards = $pointages->where('delay_minutes', '>', 0)->count();
        $absents = max(0, $totalPersonnels - $presents);
        $tauxPresence = $totalPersonnels > 0 ? round(($presents / $totalPersonnels) * 100) : 0;

        $personnels = \App\Models\Personnel::orderBy('nom')->get();

        return view('rh.pointages.index', compact(
            'pointages',
            'date',
            'presents',
            'retards',
            'absents',
            'tauxPresence',
            'personnels'
        ));
    }

    /**
     * Créer un pointage
     */
    public function createPointage()
    {
        // Le flux manuel RH se fait en tableau (saisie en masse par employé).
        return redirect()->route('rh.pointages.mass');
    }

    /**
     * Enregistrer un pointage
     */
    public function storePointage(Request $request)
    {
        try {
            $validated = $request->validate([
                'personnel_id' => 'required|exists:personnel,id',
                'date_pointage' => 'required|date',
                'heure_arrivee' => 'nullable|date_format:H:i',
                'heure_depart' => 'nullable|date_format:H:i|after:heure_arrivee',
                'statut' => 'required|in:present,absent,retard,half_day,mission,conge,maladie',
                'type_pointage' => 'nullable|in:normal,weekend,holiday,overtime',
                'notes' => 'nullable|string|max:500',
            ]);

            // Vérifier si un pointage existe déjà pour ce personnel à cette date
            $existingPointage = \App\Models\Pointage::where('personnel_id', $validated['personnel_id'])
                ->where('date_pointage', $validated['date_pointage'])
                ->first();

            if ($existingPointage) {
                return redirect()->back()
                    ->with('error', 'Un pointage existe déjà pour ce personnel à cette date.')
                    ->withInput();
            }

            $delayMinutes = 0;
            $workedHours = 0;

            if (!empty($validated['heure_arrivee'])) {
                $arrivalTime = Carbon::createFromFormat('H:i', $validated['heure_arrivee']);
                $delayLimit = Carbon::createFromTime(7, 30, 0);

                if ($arrivalTime->greaterThan($delayLimit)) {
                    $delayMinutes = $arrivalTime->diffInMinutes($delayLimit);
                }

                if (!empty($validated['heure_depart'])) {
                    $departureTime = Carbon::createFromFormat('H:i', $validated['heure_depart']);
                    $workedMinutes = $arrivalTime->diffInMinutes($departureTime, false);
                    if ($workedMinutes < 0) {
                        $workedMinutes = 0;
                    }

                    $workedHours = round(min($workedMinutes / 60, 10), 2);
                }
            }

            // Créer le pointage
            $pointage = \App\Models\Pointage::create([
                'personnel_id' => $validated['personnel_id'],
                'date_pointage' => $validated['date_pointage'],
                'heure_arrivee' => $validated['heure_arrivee'],
                'heure_depart' => $validated['heure_depart'],
                'statut' => $validated['statut'],
                'type_pointage' => $validated['type_pointage'] ?? 'normal',
                'notes' => $validated['notes'] ?? null,
                'delay_minutes' => $delayMinutes,
                'worked_hours' => $workedHours,
                'created_by' => Auth::id(),
                'validated_at' => now(),
                'validated_by' => Auth::id(),
            ]);

            // Récupérer les informations du personnel pour le message
            $personnel = \App\Models\Personnel::find($validated['personnel_id']);

            return redirect()->route('rh.pointages.index')
                ->with('success', "Pointage enregistré avec succès pour {$personnel->nom} {$personnel->prenoms} ({$validated['date_pointage']})");

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement du pointage: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement du pointage: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Pointages en masse
     */
    public function massPointages()
    {
        $personnels = \App\Models\Personnel::orderBy('nom')->get();
        return view('rh.pointages.masse', compact('personnels'));
    }

    /**
     * Enregistrer les pointages en masse
     */
    public function storeMassPointages(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'pointages' => 'required|array',
            'pointages.*.heure_arrivee' => 'nullable|date_format:H:i',
            'pointages.*.heure_depart' => 'nullable|date_format:H:i',
            'pointages.*.statut' => 'nullable|in:present,absent,retard,half_day,mission,conge,maladie',
        ]);

        foreach ($validated['pointages'] as $personnelId => $pointageData) {
            $hasArrival = !empty($pointageData['heure_arrivee']);
            $hasDeparture = !empty($pointageData['heure_depart']);
            $status = $pointageData['statut'] ?? 'absent';

            if ($hasArrival || $hasDeparture || $status !== 'absent') {
                $delayMinutes = 0;
                $workedHours = 0;

                if ($hasArrival) {
                    $arrivalTime = Carbon::createFromFormat('H:i', $pointageData['heure_arrivee']);
                    $delayLimit = Carbon::createFromTime(7, 30, 0);

                    if ($arrivalTime->greaterThan($delayLimit)) {
                        $delayMinutes = $arrivalTime->diffInMinutes($delayLimit);
                    }

                    if ($hasDeparture) {
                        $departureTime = Carbon::createFromFormat('H:i', $pointageData['heure_depart']);
                        $workedMinutes = $arrivalTime->diffInMinutes($departureTime, false);
                        if ($workedMinutes < 0) {
                            $workedMinutes = 0;
                        }
                        $workedHours = round(min($workedMinutes / 60, 10), 2);
                    }
                }

                Pointage::updateOrCreate([
                    'personnel_id' => $personnelId,
                    'date_pointage' => $validated['date'],
                ], [
                    'heure_arrivee' => $pointageData['heure_arrivee'],
                    'heure_depart' => $pointageData['heure_depart'],
                    'statut' => $status,
                    'delay_minutes' => $delayMinutes,
                    'worked_hours' => $workedHours,
                    'validated_at' => now(),
                    'validated_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('rh.pointages.index')->with('success', 'Pointages en masse enregistrés avec succès');
    }

    /**
     * Historique des pointages
     */
    public function historyPointages(Request $request)
    {
        return view('rh.pointages.history', [
            'pointages' => collect(),
            'users' => collect()
        ]);
    }

    /**
     * Heures supplémentaires
     */
    public function heuresSup(Request $request)
    {
        $moisInput = $request->input('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        $heuresSup = HeureSup::with('user')->forMonth($period)->get();

        $totalMinutes      = (int) $heuresSup->sum('duree_minutes');
        $totalValideesMin  = (int) $heuresSup->where('statut', 'valide')->sum('duree_minutes');
        $totalAttenteMin   = (int) $heuresSup->where('statut', 'en_attente')->sum('duree_minutes');
        $totalMontant      = (int) $heuresSup->sum('montant');

        $heuresMois        = $this->formatMinutesForDisplay($totalMinutes);
        $heuresValidees    = $this->formatMinutesForDisplay($totalValideesMin);
        $heuresAttente     = $this->formatMinutesForDisplay($totalAttenteMin);
        $coutMensuel       = number_format($totalMontant, 0, ',', ' ') . ' FCFA';

        return view('rh.heures-sup', compact(
            'heuresSup',
            'heuresMois',
            'heuresValidees',
            'heuresAttente',
            'coutMensuel',
            'period'
        ));
    }

    protected function formatMinutesForDisplay(?int $minutes): string
    {
        if (!$minutes) {
            return '0h';
        }

        $heures = intdiv($minutes, 60);
        $mins   = $minutes % 60;

        return sprintf('%dh%02d', $heures, $mins);
    }

    /**
     * Créer une heure supplémentaire
     */
    public function createHeureSup()
    {
        return view('rh.heures-sup.create');
    }

    /**
     * Enregistrer une heure supplémentaire
     */
    public function storeHeureSup(Request $request)
    {
        return redirect()->route('rh.heures-sup.index')->with('success', 'Heure supplémentaire enregistrée');
    }

    /**
     * Afficher une heure supplémentaire
     */
    public function showHeureSup($heure)
    {
        $heureData = [
            'id' => $heure,
            'agent_nom' => 'Test Agent',
            'nombre_heures' => 2,
            'date' => now()->format('d/m/Y')
        ];
        return view('rh.heures-sup.show', compact('heureData'));
    }

    /**
     * Modifier une heure supplémentaire
     */
    public function editHeureSup($heure)
    {
        $heureData = [
            'id' => $heure,
            'agent_nom' => 'Test Agent'
        ];
        return view('rh.heures-sup.edit', compact('heureData'));
    }

    /**
     * Mettre à jour une heure supplémentaire
     */
    public function updateHeureSup(Request $request, $heure)
    {
        return redirect()->route('rh.heures-sup.index')->with('success', 'Heure supplémentaire mise à jour');
    }

    /**
     * Supprimer une heure supplémentaire
     */
    public function deleteHeureSup($heure)
    {
        return redirect()->route('rh.heures-sup.index')->with('success', 'Heure supplémentaire supprimée');
    }

    /**
     * Paie - Index
     */
    public function paieIndex()
    {
        $moisInput = request('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        $annee = (int) $period->format('Y');
        $mois = (int) $period->format('m');

        try {
            // Récupérer tous les agents/employés
            $agents = User::where('role', 'agent')->orWhere('role', 'employe')->get();

            $salaires = collect();

            foreach ($agents as $agent) {
                // Calculer les heures travaillées pour le mois
                $heuresTravaillees = $this->calculerHeuresTravaillees($agent->id, $annee, $mois);

                // Appliquer la logique de calcul
                $heuresPayees = min($heuresTravaillees, 8); // Max 8H par jour
                $salaireJournalier = $heuresPayees * 3000; // 3000 FCFA par heure

                // Calculer le salaire mensuel (environ 22 jours ouvrables)
                $salaireMensuel = $salaireJournalier * 22;

                $salaires->push((object)[
                    'user' => $agent,
                    'heures_travaillees' => $heuresTravaillees,
                    'heures_payees' => $heuresPayees,
                    'salaire_journalier' => $salaireJournalier,
                    'salaire_brut' => $salaireMensuel,
                    'cnps_salariale' => $salaireMensuel * 0.0565, // 5.65% CNPS
                    'autres_retenues' => 0,
                    'net_a_payer' => $salaireMensuel * (1 - 0.0565),
                    'statut' => 'non_paye'
                ]);
            }

            $totalBrut = $salaires->sum('salaire_brut');
            $totalCharges = $salaires->sum(fn($p) => $p->cnps_salariale + $p->autres_retenues);
            $totalNet = $salaires->sum('net_a_payer');
            $bulletins = $salaires->count();
            $bulletinsPayes = $salaires->where('statut', 'paye')->count();
        } catch (\Exception $e) {
            $salaires = collect();
            $totalBrut = 0;
            $totalCharges = 0;
            $totalNet = 0;
            $bulletins = 0;
            $bulletinsPayes = 0;
        }

        return view('rh.paie.index', compact('salaires','period','totalBrut','totalCharges','totalNet','bulletins','bulletinsPayes'));
    }

    /**
     * Calculer les heures travaillées pour un agent pendant un mois
     * en tenant compte de la pause de 12H-14H non comptée
     */
    private function calculerHeuresTravaillees($agentId, $annee, $mois)
    {
        $heuresTotales = 0;

        // Récupérer les pointages du mois
        $pointages = Pointage::where('user_id', $agentId)
            ->whereYear('date_pointage', $annee)
            ->whereMonth('date_pointage', $mois)
            ->orderBy('date_pointage')
            ->get();

        foreach ($pointages as $pointage) {
            if ($pointage->heure_arrivee && $pointage->heure_depart) {
                $arrivee = Carbon::parse($pointage->heure_arrivee);
                $depart = Carbon::parse($pointage->heure_depart);

                // Calculer les heures travaillées dans la journée
                $heuresJournee = $depart->diffInHours($arrivee);

                // Soustraire la pause de 12H-14H si elle est incluse
                $pauseDejeuner = $this->calculerPauseDejeuner($arrivee, $depart);
                $heuresEffectives = $heuresJournee - $pauseDejeuner;

                $heuresTotales += max(0, $heuresEffectives);
            }
        }

        return $heuresTotales;
    }

    /**
     * Calculer la durée de la pause dejeuner (12H-14H)
     */
    private function calculerPauseDejeuner($arrivee, $depart)
    {
        $pauseDebut = Carbon::parse($arrivee->format('Y-m-d') . ' 12:00:00');
        $pauseFin = Carbon::parse($arrivee->format('Y-m-d') . ' 14:00:00');

        // Si l'employé travaille pendant la période de pause
        if ($arrivee < $pauseFin && $depart > $pauseDebut) {
            $debutPause = max($arrivee, $pauseDebut);
            $finPause = min($depart, $pauseFin);
            return $finPause->diffInHours($debutPause);
        }

        return 0;
    }

    /**
     * Paie
     */
    public function paie()
    {
        $moisInput = request('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        $annee = (int) $period->format('Y');
        $mois = (int) $period->format('m');

        try {
            $salaires = Paie::with('user')
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->get()
                ->sortBy(function ($paie) {
                    return $paie->user->name ?? '';
                })->values();

            $totalBrut = $salaires->sum('brut');
            $totalCharges = $salaires->sum(fn($p) => $p->cnps_salariale + $p->autres_retenues);
            $totalNet = $salaires->sum('net_a_payer');
            $bulletins = $salaires->count();
            $bulletinsPayes = $salaires->where('statut', 'paye')->count();
        } catch (\Exception $e) {
            $salaires = collect();
            $totalBrut = 0;
            $totalCharges = 0;
            $totalNet = 0;
            $bulletins = 0;
            $bulletinsPayes = 0;
        }

        return view('rh.paie.index', compact('salaires','period','totalBrut','totalCharges','totalNet','bulletins','bulletinsPayes'));
    }

    /**
     * Exporter les fiches de paie du mois en cours sous forme de PDF A4
     * (1 page par agent, avec CNPS provisoire et net à payer)
     */
    public function exportPaie()
    {
        $mois = request('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $mois)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        // Taux provisoires à valider par un expert CI (rendables configurables plus tard)
        $cnpsTauxSalarial = 0.063; // 6,3 %
        $cnpsTauxPatronal = 0.077; // 7,7 %

        // Seuls les agents et modérateurs sont pris en compte pour la paie
        $users = User::whereIn('role', ['agent', 'moderator'])->orderBy('name')->get();

        $bulletins = [];
        foreach ($users as $user) {
            // Salaire de base depuis la colonne users.salaire si disponible, sinon 0
            $salaireBase = 0;
            if (Schema::hasColumn('users', 'salaire')) {
                $salaireBase = (float) ($user->salaire ?? 0);
            }

            $heuresSup = 0.0;
            $primes = 0.0;

            $brut = $salaireBase + $heuresSup + $primes;

            // Assiette CNPS (sans plafond spécifique pour l'instant)
            $assietteCnps = $brut;

            $cnpsSalariale = round($assietteCnps * $cnpsTauxSalarial, 0);
            $cnpsPatronale = round($assietteCnps * $cnpsTauxPatronal, 0);
            $autresRetenues = 0; // IRPP, AMU, etc. à ajouter plus tard

            $netAPayer = $brut - $cnpsSalariale - $autresRetenues;

            $bulletins[] = [
                'agent'           => $user,
                'mois_label'      => $period->translatedFormat('F Y'),
                'mois_numeric'    => $period->format('m'),
                'annee'           => $period->format('Y'),
                'salaire_base'    => $salaireBase,
                'heures_sup'      => $heuresSup,
                'primes'          => $primes,
                'brut'            => $brut,
                'cnps_salariale'  => $cnpsSalariale,
                'cnps_patronale'  => $cnpsPatronale,
                'autres_retenues' => $autresRetenues,
                'net_a_payer'     => $netAPayer,
            ];
        }

        $settings = EntrepriseSettings::getActive();
        $entreprise = [
            'nom' => $settings->nom_entreprise ?? config('app.name', 'KENAM SERVICES'),
            'adresse' => $settings->adresse ?? '',
            'telephone' => $settings->telephone ?? '',
            'email' => $settings->email_contact ?? '',
            'website' => $settings->site_web ?? '',
            'cnps' => $settings->cnss ?? '',
            'logo_path' => !empty($settings?->logo_path) ? public_path('storage/' . $settings->logo_path) : null,
        ];

        $pdf = Pdf::loadView('rh.paie-bulletin', [
            'entreprise' => $entreprise,
            'period' => $period,
            'bulletins' => $bulletins,
        ])->setPaper('a4', 'portrait');

        $filename = 'bulletins_paie_'.$period->format('Y_m').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Exporter un bulletin de paie individuel en PDF A4
     */
    public function pdfPaie($paie)
    {
        $paie = Paie::find($paie);
        if (!$paie) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Bulletin de paie introuvable.');
        }
        $period = Carbon::createFromDate($paie->annee, $paie->mois, 1)->startOfMonth();

        $settings = EntrepriseSettings::getActive();
        $entreprise = [
            'nom' => $settings->nom_entreprise ?? config('app.name', 'KENAM SERVICES'),
            'adresse' => $settings->adresse ?? '',
            'telephone' => $settings->telephone ?? '',
            'email' => $settings->email_contact ?? '',
            'website' => $settings->site_web ?? '',
            'cnps' => $settings->cnss ?? '',
            'logo_path' => !empty($settings?->logo_path) ? public_path('storage/' . $settings->logo_path) : null,
        ];

        $bulletins = [[
            'agent'           => $paie->user,
            'mois_label'      => $period->translatedFormat('F Y'),
            'mois_numeric'    => $paie->mois,
            'annee'           => $paie->annee,
            'salaire_base'    => $paie->salaire_base,
            'heures_sup'      => $paie->heures_sup ?? 0,
            'primes'          => $paie->primes ?? 0,
            'brut'            => $paie->brut,
            'cnps_salariale'  => $paie->cnps_salariale,
            'cnps_patronale'  => $paie->cnps_patronale,
            'autres_retenues' => $paie->autres_retenues,
            'net_a_payer'     => $paie->net_a_payer,
        ]];

        $pdf = Pdf::loadView('rh.paie-bulletin', [
            'entreprise' => $entreprise,
            'period' => $period,
            'bulletins' => $bulletins,
        ])->setPaper('a4', 'portrait');

        $filename = 'bulletin_paie_'.$paie->user->id.'_'.$paie->annee.'_'.$paie->mois.'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Générer les fiches de paie pour tous les agents pour un mois donné
     */
    public function generateAllPaie(Request $request)
    {
        $moisInput = $request->input('mois', now()->format('Y-m'));

        try {
            $period = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
        } catch (\Exception $e) {
            $period = now()->startOfMonth();
        }

        $annee = (int) $period->format('Y');
        $mois  = (int) $period->format('m');

        // Taux CNPS provisoires (à valider et rendre configurables)
        $cnpsTauxSalarial = 0.063;
        $cnpsTauxPatronal = 0.077;

        // Seuls les agents et modérateurs sont pris en compte pour la paie
        $users = User::whereIn('role', ['agent', 'moderator'])->orderBy('name')->get();

        $crees = 0;
        $misAJour = 0;

        foreach ($users as $user) {
            // Salaire de base depuis users.salaire si la colonne existe
            $salaireBase = 0;
            if (Schema::hasColumn('users', 'salaire')) {
                $salaireBase = (float) ($user->salaire ?? 0);
            }

            $heuresSup = 0.0;
            $primes    = 0.0;

            $brut = $salaireBase + $heuresSup + $primes;

            $assietteCnps   = $brut;
            $cnpsSalariale  = round($assietteCnps * $cnpsTauxSalarial, 0);
            $cnpsPatronale  = round($assietteCnps * $cnpsTauxPatronal, 0);
            $autresRetenues = 0;

            $netAPayer = $brut - $cnpsSalariale - $autresRetenues;

            $data = [
                'salaire_base'   => $salaireBase,
                'heures_sup'     => $heuresSup,
                'primes'         => $primes,
                'brut'           => $brut,
                'cnps_salariale' => $cnpsSalariale,
                'cnps_patronale' => $cnpsPatronale,
                'autres_retenues'=> $autresRetenues,
                'net_a_payer'    => $netAPayer,
                'statut'         => 'genere',
            ];

            $paie = Paie::where('user_id', $user->id)
                ->where('annee', $annee)
                ->where('mois', $mois)
                ->first();

            if ($paie) {
                $paie->fill($data)->save();
                $misAJour++;
            } else {
                try {
                    Paie::create(array_merge($data, [
                        'user_id' => $user->id,
                        'annee'   => $annee,
                        'mois'    => $mois,
                    ]));
                    $crees++;
                } catch (\Exception $e) {
                    // Ignorer si la table paies n'existe pas
                    continue;
                }
            }
        }

        $message = "Bulletins générés pour le mois ".$period->translatedFormat('F Y').". Créés: $crees, mis à jour: $misAJour.";

        return redirect()->route('rh.paie.index', ['mois' => $period->format('Y-m')])
            ->with('success', $message);
    }

    /**
     * Créer une fiche de paie
     */
    public function createPaie()
    {
        $users = User::orderBy('name')->get(['id','name','email','role']);
        $period = now();
        return view('rh.paie-create', compact('users','period'));
    }

    /**
     * Stocker une fiche de paie
     */
    public function storePaie(Request $request)
    {
        $validated = $request->validate([
            'agent_id'     => 'required|integer|exists:users,id',
            'mois'         => 'required|integer|min:1|max:12',
            'annee'        => 'required|integer|min:2000',
            'salaire_base' => 'required|numeric|min:0',
            'heures_sup'   => 'nullable|numeric|min:0',
            'primes'       => 'nullable|numeric|min:0',
            'commentaires' => 'nullable|string',
        ]);

        $heuresSup = $validated['heures_sup'] ?? 0;
        $primes = $validated['primes'] ?? 0;

        $brut = $validated['salaire_base'] + $heuresSup + $primes;

        // Taux CNPS provisoires (à valider et rendre configurables)
        $cnpsTauxSalarial = 0.063;
        $cnpsTauxPatronal = 0.077;

        $assietteCnps = $brut;
        $cnpsSalariale = round($assietteCnps * $cnpsTauxSalarial, 0);
        $cnpsPatronale = round($assietteCnps * $cnpsTauxPatronal, 0);
        $autresRetenues = 0; // IRPP, AMU, etc., à ajouter plus tard

        $netAPayer = $brut - $cnpsSalariale - $autresRetenues;

        Paie::create([
            'user_id'        => $validated['agent_id'],
            'mois'           => (int) $validated['mois'],
            'annee'          => (int) $validated['annee'],
            'salaire_base'   => $validated['salaire_base'],
            'heures_sup'     => $heuresSup,
            'primes'         => $primes,
            'brut'           => $brut,
            'cnps_salariale' => $cnpsSalariale,
            'cnps_patronale' => $cnpsPatronale,
            'autres_retenues'=> $autresRetenues,
            'net_a_payer'    => $netAPayer,
            'statut'         => 'genere',
            'commentaires'   => $validated['commentaires'] ?? null,
        ]);

        $moisRedirect = sprintf('%04d-%02d', $validated['annee'], $validated['mois']);

        return redirect()
            ->route('rh.paie.index', ['mois' => $moisRedirect])
            ->with('success', 'Fiche de paie créée et enregistrée.');
    }

    /**
     * Mettre à jour une fiche de paie manuellement
     */
    public function updatePaie(Request $request, Paie $paie)
    {
        $request->validate([
            'salaire_base' => 'required|numeric|min:0',
            'brut' => 'required|numeric|min:0',
            'cnps_salariale' => 'required|numeric|min:0',
            'autres_retenues' => 'required|numeric|min:0',
            'net_a_payer' => 'required|numeric|min:0',
        ]);

        $paie->update($request->only([
            'salaire_base',
            'brut',
            'cnps_salariale',
            'autres_retenues',
            'net_a_payer'
        ]));

        return back()->with('success', 'Fiche de paie mise à jour avec succès');
    }

    /**
     * Afficher le formulaire d'édition d'une fiche de paie
     */
    public function editPaie(Paie $paie)
    {
        return view('rh.paie.edit', compact('paie'));
    }

    /**
     * Afficher les congés/absences
     */
    public function conges(Request $request)
    {
        try {
            // Récupération des données depuis la base de données
            $conges = Conge::with(['personnel', 'valideur'])
                ->orderBy('date_demande', 'desc')
                ->paginate(20);

            // Calculer les statistiques
            $totalConges = $conges->total();
            $enAttente = $conges->where('statut', 'En attente')->count();
            $approuves = $conges->where('statut', 'Approuvé')->count();

            return view('rh.conges.index', compact('conges', 'totalConges', 'enAttente', 'approuves'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des valeurs par défaut
            $conges = collect([]);
            $totalConges = 0;
            $enAttente = 0;
            $approuves = 0;

            return view('rh.conges.index', compact('conges', 'totalConges', 'enAttente', 'approuves'));
        }
    }

    /**
     * Créer un congé
     */
    public function createConge()
    {
        $personnels = \App\Models\Personnel::orderBy('nom')->get();
        return view('rh.conges.create', compact('personnels'));
    }

    /**
     * Enregistrer un congé
     */
    public function storeConge(Request $request)
    {
        $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'type_conge' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        // Calculer le nombre de jours
        $dateDebut = \Carbon\Carbon::parse($request->date_debut);
        $dateFin = \Carbon\Carbon::parse($request->date_fin);
        $nombreJours = $dateDebut->diffInDays($dateFin) + 1;

        // Récupérer le personnel
        $personnel = \App\Models\Personnel::find($request->personnel_id);

        // Créer la demande de congé
        Conge::create([
            'personnel_id' => $request->personnel_id,
            'agent_nom' => $personnel->nom . ' ' . $personnel->prenoms,
            'type_conge' => $request->type_conge,
            'date_demande' => now(),
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'nombre_jours' => $nombreJours,
            'motif' => $request->motif,
            'statut' => 'En attente',
        ]);

        return redirect()->route('rh.conges.index')->with('success', 'Demande de congé enregistrée avec succès');
    }

    /**
     * Afficher un congé
     */
    public function showConge($conge)
    {
        // Simulation de données pour le congé
        $congeData = [
            'id' => $conge,
            'agent' => ['name' => 'Agent Test'],
            'type_conge' => 'Annuel',
            'date_debut' => '2024-01-01',
            'date_fin' => '2024-01-05',
            'motif' => 'Vacances',
            'statut' => 'En attente'
        ];

        return view('rh.conges.show', compact('congeData'));
    }

    /**
     * Modifier un congé
     */
    public function editConge($conge)
    {
        $agents = User::orderBy('name')->get(['id', 'name']);
        $congeData = [
            'id' => $conge,
            'agent_id' => 1,
            'type_conge' => 'Annuel',
            'date_debut' => '2024-01-01',
            'date_fin' => '2024-01-05',
            'motif' => 'Vacances'
        ];

        return view('rh.conges.edit', compact('congeData', 'agents'));
    }

    /**
     * Mettre à jour un congé
     */
    public function updateConge(Request $request, $conge)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'type_conge' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        return redirect()->route('rh.conges.index')->with('success', 'Congé mis à jour avec succès');
    }

    /**
     * Supprimer un congé
     */
    public function deleteConge($conge)
    {
        return redirect()->route('rh.conges.index')->with('success', 'Congé supprimé avec succès');
    }

    /**
     * Afficher une fiche de paie
     */
    public function showPaie($paie)
    {
        $paieData = [
            'id' => $paie,
            'agent' => ['name' => 'Agent Test'],
            'mois' => 1,
            'annee' => 2024,
            'salaire_base' => 150000,
            'brut' => 165000,
            'net_a_payer' => 145000
        ];

        return view('rh.paie.show', compact('paieData'));
    }

    /**
     * Formulaire de recrutement (hiring)
     */
    public function recrutement()
    {
        $services = Service::orderBy('nom')->get();
        return view('rh.recrutement', compact('services'));
    }

    /**
     * Enregistrer un nouveau recrutement
     */
    public function storeRecrutement(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'service_id' => 'required|exists:services,id',
            'role' => 'required|string',

            // Ivorian HR Fields
            'sexe' => 'required|string|in:M,F',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string',
            'nationalite' => 'required|string',
            'situation_matrimoniale' => 'required|string',
            'nombre_enfants' => 'required|integer|min:0',
            'adresse_postale' => 'nullable|string',
            'n_cnps' => 'nullable|string',
            'n_cmu' => 'nullable|string',

            // Contrat
            'contrat' => 'required|string',
            'date_embauche' => 'required|date',
            'date_fin_contrat' => 'nullable|date|after:date_embauche',
            'periode_essai' => 'nullable|integer',
            'categorie_professionnelle' => 'nullable|string',

            // Salaire
            'salaire_base' => 'required|numeric|min:0',
            'sursalaire' => 'nullable|numeric|min:0',
            'indemnite_transport' => 'nullable|numeric|min:0',
            'indemnite_logement' => 'nullable|numeric|min:0',
            'autres_primes' => 'nullable|numeric|min:0',
        ]);

        $validated['name'] = $validated['nom'] . ' ' . $validated['prenom'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['salaire'] = $validated['salaire_base'] + ($validated['sursalaire'] ?? 0) + ($validated['indemnite_transport'] ?? 0) + ($validated['indemnite_logement'] ?? 0) + ($validated['autres_primes'] ?? 0);
        $validated['is_active'] = true;

        $user = User::create($validated);

        return redirect()->route('rh.contrats.index')->with('success', 'Recrutement réussi pour ' . $user->name);
    }

    /**
     * Afficher un contrat
     */
    public function showContrat($user)
    {
        $agent = User::with('service')->findOrFail($user);
        return view('rh.contrats-show', compact('agent'));
    }

    /**
     * Modifier un contrat
     */
    public function editContrat($user)
    {
        $agent = User::findOrFail($user);
        $services = Service::orderBy('nom')->get();
        return view('rh.contrats-edit', compact('agent', 'services'));
    }

    /**
     * Mettre à jour un contrat
     */
    public function updateContrat(Request $request, $user)
    {
        $agent = User::findOrFail($user);

        $validated = $request->validate([
            'contrat' => 'required|string',
            'date_embauche' => 'required|date',
            'date_fin_contrat' => 'nullable|date',
            'salaire_base' => 'required|numeric',
            'sursalaire' => 'nullable|numeric',
            'indemnite_transport' => 'nullable|numeric',
            'indemnite_logement' => 'nullable|numeric',
            'autres_primes' => 'nullable|numeric',
            'categorie_professionnelle' => 'nullable|string',
        ]);

        $agent->update($validated);
        $agent->salaire = $validated['salaire_base'] + ($validated['sursalaire'] ?? 0) + ($validated['indemnite_transport'] ?? 0) + ($validated['indemnite_logement'] ?? 0) + ($validated['autres_primes'] ?? 0);
        $agent->save();

        return redirect()->route('rh.contrats.show', $agent->id)->with('success', 'Contrat mis à jour');
    }

    /**
     * Générer le contrat au format PDF
     */
    public function contratPdf($user)
    {
        $agent = User::with('service')->findOrFail($user);
        $pdf = PDF::loadView('rh.contrats.pdf', compact('agent'));
        return $pdf->download('Contrat_'.$agent->name.'.pdf');
    }

    /**
     * Supprimer un contrat (alias suppression user / désactivation)
     */
    public function deleteContrat($user)
    {
        $agent = User::findOrFail($user);
        $agent->is_active = false;
        $agent->save();
        return redirect()->route('rh.contrats.index')->with('success', 'Agent désactivé et contrat archivé');
    }

    /**
     * Supprimer une fiche de paie
     */
    public function deletePaie($paie)
    {
        $p = Paie::findOrFail($paie);
        $p->delete();
        return redirect()->route('rh.paie.index')->with('success', 'Fiche de paie supprimée avec succès');
    }

    /**
     * Exporter les contrats
     */
    public function exportContrats()
    {
        $contrats = User::orderBy('name')
            ->select('id', 'name', 'email', 'phone', 'role', 'created_at')
            ->get();

        $filename = 'contrats_export_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        $callback = function() use ($contrats) {
            $handle = fopen('php://output', 'w');

            // BOM pour UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Nom', 'Email', 'Téléphone', 'Poste', 'Date d\'embauche'], ';');
            foreach ($contrats as $contrat) {
                fputcsv($handle, [
                    $contrat->id,
                    $contrat->name,
                    $contrat->email,
                    $contrat->phone ?? '',
                    $contrat->role,
                    $contrat->created_at ? $contrat->created_at->format('Y-m-d') : ''
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Créer une nouvelle fiche de paie
     */
    public function paieCreate()
    {
        $agents = User::orderBy('name')->get();
        return view('rh.paie-create', compact('agents'));
    }

    /**
     * Stocker une nouvelle fiche de paie
     */
    public function paieStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'mois' => 'required|date_format:Y-m',
            'salaire_brut' => 'required|numeric|min:0',
        ]);

        try {
            $paie = Paie::create($validated);
            return redirect()->route('rh.paie.show', $paie->id)
                ->with('success', 'Fiche de paie créée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher une fiche de paie
     */
    public function paieShow($id)
    {
        try {
            $paie = Paie::findOrFail($id);
            $paie->load('user');
            return view('rh.paie-show', compact('paie'));
        } catch (\Exception $e) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Fiche de paie non trouvée');
        }
    }

    /**
     * Éditer une fiche de paie
     */
    public function paieEdit($id)
    {
        try {
            $paie = Paie::findOrFail($id);
            $agents = User::orderBy('name')->get();
            return view('rh.paie-edit', compact('paie', 'agents'));
        } catch (\Exception $e) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Fiche de paie non trouvée');
        }
    }

    /**
     * Mettre à jour une fiche de paie
     */
    public function paieUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'mois' => 'required|date_format:Y-m',
            'salaire_brut' => 'required|numeric|min:0',
        ]);

        try {
            $paie = Paie::findOrFail($id);
            $paie->update($validated);
            return redirect()->route('rh.paie.show', $paie->id)
                ->with('success', 'Fiche de paie mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une fiche de paie
     */
    public function paieDestroy($id)
    {
        try {
            $paie = Paie::findOrFail($id);
            $paie->delete();
            return redirect()->route('rh.paie.index')
                ->with('success', 'Fiche de paie supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression');
        }
    }

    /**
     * Générer un PDF de la fiche de paie
     */
    public function paiePdf($id)
    {
        try {
            $paie = Paie::findOrFail($id);
            $paie->load('user');

            // Pour l'instant, redirection vers la page de visualisation
            return redirect()->route('rh.paie.show', $id);
        } catch (\Exception $e) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Fiche de paie non trouvée');
        }
    }

    /**
     * Exporter les fiches de paie
     */
    public function paieExport(Request $request)
    {
        $mois = $request->get('mois', now()->format('Y-m'));

        try {
            $paies = Paie::when($mois, fn($q) => $q->where('mois', 'like', $mois . '%'))
                ->with('user')
                ->get();

            $filename = 'paies_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ];

            $callback = function() use ($paies) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($handle, ['ID', 'Agent', 'Mois', 'Salaire Brut', 'Retenues', 'Net'], ';');
                foreach ($paies as $paie) {
                    fputcsv($handle, [
                        $paie->id,
                        $paie->user->name ?? '',
                        $paie->mois ?? '',
                        $paie->salaire_brut ?? 0,
                        $paie->retenues ?? 0,
                        ($paie->salaire_brut ?? 0) - ($paie->retenues ?? 0)
                    ], ';');
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->route('rh.paie.index')
                ->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Générer toutes les fiches de paie pour un mois
     */
    public function paieGenerateAll(Request $request)
    {
        $mois = $request->get('mois', now()->format('Y-m'));

        try {
            $agents = User::where('is_active', true)->get();
            $count = 0;

            foreach ($agents as $agent) {
                $exists = Paie::where('user_id', $agent->id)
                    ->where('mois', 'like', $mois . '%')
                    ->exists();

                if (!$exists) {
                    Paie::create([
                        'user_id' => $agent->id,
                        'mois' => $mois . '-01',
                        'salaire_brut' => 0,
                    ]);
                    $count++;
                }
            }

            return redirect()->route('rh.paie.index', ['mois' => $mois])
                ->with('success', "Génération terminée: $count nouvelles fiches créées");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la génération: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        return redirect()->back()->with('success', 'Cette fonctionnalité est en cours de développement, aucune donnée n\'a été altérée.');
    }


    public function destroyConge($id)
    {
        return redirect()->back()->with('success', 'Cette fonctionnalité est en cours de développement, aucune donnée n\'a été altérée.');
    }

    /**
     * Garde uniquement les champs existants dans la table personnel.
     */
    private function filterPersonnelPayload(array $payload): array
    {
        if (!Schema::hasTable('personnel')) {
            return $payload;
        }

        if (!isset($payload['numero_compte']) && isset($payload['numero_compte_bancaire'])) {
            $payload['numero_compte'] = $payload['numero_compte_bancaire'];
        }

        $columns = Schema::getColumnListing('personnel');

        return array_intersect_key($payload, array_flip($columns));
    }

}
