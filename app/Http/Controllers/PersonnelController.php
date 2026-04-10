<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\PersonnelConge;
use App\Models\PersonnelDocument;
use App\Services\HikvisionFaceSyncService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PersonnelController extends Controller
{
    private HikvisionFaceSyncService $hikvisionFaceSyncService;

    public function __construct(HikvisionFaceSyncService $hikvisionFaceSyncService)
    {
        $this->hikvisionFaceSyncService = $hikvisionFaceSyncService;
    }

    /**
        * Afficher le tableau de bord RH (uniquement données RH)
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

        return view('rh.personnel.dashboard-rh', compact('rhStats'));
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
                $personnels = Personnel::with(['user', 'createdBy'])
                    ->orderBy('nom')
                    ->paginate(20);

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
            'Informatique',
            'Marketing',
            'Juridique',
            'Qualité',
            'Administration',
            'Maintenance',
            'Sécurité',
            'Autre'
        ];

        return view('rh.personnel.create', compact('services', 'suggestedMatricule'));
    }

    public function createOnCamera(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matricule' => 'required|string|max:20',
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:150',
            'date_naissance' => 'required|date|before:today',
            'sexe' => 'required|in:M,F',
            'photo_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez renseigner correctement les champs requis pour la caméra.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if (Schema::hasTable('personnel') && Personnel::where('matricule', $validated['matricule'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce matricule existe déjà dans la plateforme.',
            ], 422);
        }

        $cameraCreation = $this->hikvisionFaceSyncService->createEmployeeFromForm([
            'matricule' => $validated['matricule'],
            'nom_complet' => trim($validated['nom'] . ' ' . $validated['prenoms']),
            'sexe' => $validated['sexe'],
            'date_naissance' => $validated['date_naissance'],
            'camera_person_id' => $validated['matricule'],
        ], $request->file('photo_profil'));

        if (!($cameraCreation['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $cameraCreation['message'] ?? 'La création sur la caméra a échoué.',
                'camera_person_id' => $cameraCreation['camera_person_id'] ?? null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $cameraCreation['message'] ?? 'Employé créé sur la caméra.',
            'camera_person_id' => $cameraCreation['camera_person_id'] ?? $validated['matricule'],
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->filled('numero_compte') && $request->filled('numero_compte_bancaire')) {
            $request->merge(['numero_compte' => $request->input('numero_compte_bancaire')]);
        }

        if (!$request->filled('matricule')) {
            $request->merge([
                'matricule' => $this->generateSuggestedMatricule(),
            ]);
        }

        $validated = $request->validate([
            // Informations personnelles
            'matricule' => 'required|string|max:20|unique:personnel',
            'camera_person_id' => 'nullable|string|max:100',
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:150',
            'date_naissance' => 'required|date|before:today',
            'lieu_naissance' => 'required|string|max:100',
            'nationalite' => 'required|string|max:50',
            'sexe' => 'required|in:M,F',
            'situation_matrimoniale' => 'required|string|max:30',
            'nb_enfants_charge' => 'required|integer|min:0',
            'photo_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Coordonnées
            'telephone_principal' => 'required|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'email_personnel' => 'nullable|email|max:100',
            'adresse_residence' => 'required|string',
            'ville' => 'required|string|max:50',
            'quartier' => 'nullable|string|max:50',

            // Pièce d'identité
            'type_piece' => 'required|in:CNI,PASSEPORT,CARTE_SEJOUR,AUTRE',
            'numero_piece' => 'required|string|max:50|unique:personnel',
            'date_delivrance_piece' => 'required|date',
            'expiration_piece' => 'nullable|date|after:date_delivrance_piece',
            'lieu_delivrance_piece' => 'required|string|max:100',
            'photo_cni_base64' => 'nullable|string',

            // Professionnel
            'poste' => 'required|string|max:100',
            'service' => 'required|string|max:100',
            'departement' => 'nullable|string|max:100',
            'categorie' => 'required|string|max:50',
            'echelon' => 'nullable|string|max:20',
            'indice' => 'nullable|string|max:20',

            // Contrat
            'type_contrat' => 'required|in:CDI,CDD,STAGE,INTERIM,CONSULTANT',
            'date_embauche' => 'required|date',
            'date_fin_contrat' => 'nullable|date|after:date_embauche',
            'duree_essai_jours' => 'nullable|integer|min:1',
            'salaire_base' => 'required|numeric|min:0',
            'devise' => 'required|string|size:3',
            'mode_paiement' => 'nullable|in:VIREMENT,ESPECE,CHEQUE,MOBILE_MONEY',
            'frequence_paiement' => 'required|in:MENSUEL,HEBDOMADAIRE,QUINZOMADAIRE',

            // CNPS
            'numero_cnps' => 'nullable|string|max:20|unique:personnel',
            'date_affiliation_cnps' => 'nullable|date',
            'categorie_cnps' => 'nullable|in:A,B,C,D,E,F,G',

            // Fiscalité
            'numero_contribuable' => 'nullable|string|max:30|unique:personnel',
            'nb_parts_fiscales' => 'required|integer|min:1',
            'situation_fiscale' => 'required|in:IMPOSABLE,NON_IMPOSABLE,EXONERE',

            // Banque
            'banque' => 'nullable|string|max:100',
            'agence_bancaire' => 'nullable|string|max:100',
            'numero_compte' => 'nullable|string|max:30',
            'rib' => 'nullable|string|max:30',

            // Contact d'urgence
            'nom_urgence' => 'required|string|max:100',
            'telephone_urgence' => 'required|string|max:20',
            'lien_parente' => 'nullable|string|max:50',
            'adresse_urgence' => 'nullable|string',

            // Santé
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'medecin_traitant' => 'nullable|string|max:100',
            'telephone_medecin' => 'nullable|string|max:20',

            // Documents
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'lettre_motivation' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'contrat' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'casier_judiciaire' => 'nullable|file|mimes:pdf,jpg,jpeg|max:5120',
            'certificat_medical' => 'nullable|file|mimes:pdf,jpg,jpeg|max:5120',
            'diplomes' => 'nullable|file|mimes:pdf,zip|max:10240',
            'attestations' => 'nullable|file|mimes:pdf,zip|max:10240',

            // Liaison utilisateur
            'user_id' => 'nullable|exists:users,id',
            'observations' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Gestion des fichiers uploadés
            $documentsPaths = [];
            $documentFields = [
                'cv', 'lettre_motivation', 'contrat', 'casier_judiciaire',
                'certificat_medical', 'diplomes', 'attestations'
            ];

            foreach ($documentFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = 'personnel_' . $validated['matricule'] . '_' . $field . '_' . time() . '.' . $file->extension();
                    $path = $file->storeAs('documents/personnel', $filename, 'public');
                    $documentsPaths[$field . '_path'] = $path;
                }
            }

            // Photo de profil
            if ($request->hasFile('photo_profil')) {
                $photo = $request->file('photo_profil');
                $photoName = 'personnel_' . $validated['matricule'] . '_photo_' . time() . '.' . $photo->extension();
                $photoPath = $photo->storeAs('photos/personnel', $photoName, 'public');
                $validated['photo_profil'] = $photoPath;
            }

            $photoCniBase64 = $validated['photo_cni_base64'] ?? null;
            unset($validated['photo_cni_base64']);

            // Calcul de la fin de période d'essai
            $validated['fin_periode_essai'] = Carbon::parse($validated['date_embauche'])
                ->addDays((int)$validated['duree_essai_jours']);

            // Ajouter les chemins des documents
            $validated = array_merge($validated, $documentsPaths);
            $validated['created_by'] = Auth::id();

            // Générer automatiquement l'ID camera si photo uploadée et ID vide
            if (isset($validated['photo_profil']) && empty($validated['camera_person_id'])) {
                $validated['camera_person_id'] = 'CAM_' . str_replace(['KSL', 'EMP'], '', $validated['matricule']);
            }

            if (!empty($validated['camera_person_id'])) {
                if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                    $validated['facial_sync_status'] = 'synced';
                }

                if (Schema::hasColumn('personnel', 'facial_sync_at')) {
                    $validated['facial_sync_at'] = now();
                }
            }

            // Vérifier si la table personnel existe
            if (!Schema::hasTable('personnel')) {
                DB::rollBack();
                return redirect()->back()->with('error', 'La table personnel n\'existe pas. Veuillez contacter l\'administrateur.');
            }

            $personnel = Personnel::create($this->filterPersonnelPayload($validated));

            if (!empty($photoCniBase64) && preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $photoCniBase64, $matches)) {
                $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                $binary = base64_decode(substr($photoCniBase64, strpos($photoCniBase64, ',') + 1), true);

                if ($binary !== false) {
                    $cniFilename = 'personnel_' . $validated['matricule'] . '_cni_' . time() . '.' . $extension;
                    $cniPath = 'documents/personnel/' . $personnel->id . '/' . $cniFilename;
                    Storage::disk('public')->put($cniPath, $binary);

                    PersonnelDocument::create([
                        'personnel_id' => $personnel->id,
                        'nom_fichier' => $cniFilename,
                        'chemin_fichier' => $cniPath,
                        'type_document' => 'CNI',
                        'taille_fichier' => strlen($binary),
                        'date_expiration' => $validated['expiration_piece'] ?? null,
                        'description' => 'Photo CNI importée en base64 depuis le formulaire de création du personnel',
                        'statut' => 'EN_ATTENTE',
                        'upload_par' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('rh.personnel.index')
                ->with('success', 'Personnel créé avec succès après validation caméra');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Garde uniquement les champs existants dans la table personnel.
     * Cela evite les erreurs SQL quand le schema local est partiellement migre.
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

    private function generateSuggestedMatricule(): string
    {
        $year = date('Y');
        $latestPersonnel = Personnel::query()
            ->where('matricule', 'like', 'KSL' . $year . '%')
            ->orderByDesc('id')
            ->first();

        if ($latestPersonnel && preg_match('/^(?:KSL' . $year . ')(\d{4})$/', (string) $latestPersonnel->matricule, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        do {
            $matricule = 'KSL' . $year . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = Personnel::query()->where('matricule', $matricule)->exists();
            $nextNumber++;
        } while ($exists);

        return $matricule;
    }

    public function show(Personnel $personnel)
    {
        $personnel->load(['user', 'createdBy', 'updatedBy', 'conges', 'paies']);

        return view('rh.personnel.show', compact('personnel'));
    }

    public function edit(Personnel $personnel)
    {
        return view('rh.personnel.edit', compact('personnel'));
    }

    public function update(Request $request, Personnel $personnel)
    {
        $originalEssentialData = [
            'matricule' => $personnel->matricule,
            'nom' => $personnel->nom,
            'prenoms' => $personnel->prenoms,
            'sexe' => $personnel->sexe,
            'date_naissance' => optional($personnel->date_naissance)->format('Y-m-d'),
            'photo_profil' => $personnel->photo_profil,
        ];

        $validated = $request->validate([
            'matricule' => 'required|string|max:20|unique:personnel,matricule,' . $personnel->id,
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:150',
            'date_naissance' => 'required|date|before:today',
            'lieu_naissance' => 'required|string|max:100',
            'nationalite' => 'required|string|max:50',
            'sexe' => 'required|in:M,F',
            'situation_matrimoniale' => 'required|string|max:30',
            'nb_enfants_charge' => 'required|integer|min:0',
            'photo_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // ... (autres règles de validation similaires au store)

            'mode_paiement' => 'nullable|in:VIREMENT,ESPECE,CHEQUE,MOBILE_MONEY',
            'frequence_paiement' => 'nullable|in:MENSUEL,HEBDOMADAIRE,QUINZOMADAIRE',

            'statut' => 'required|in:ACTIF,CONGE,MALADIE,SUSPENDU,DEMISSION,LICENCIE,RETRAITE',
            'date_depart' => 'nullable|date',
            'motif_depart' => 'required_if:statut,DEMISSION,LICENCIE,RETRAITE|string',
            'observations' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Gestion des fichiers uploadés
            $documentsPaths = [];
            $documentFields = [
                'cv', 'lettre_motivation', 'contrat', 'casier_judiciaire',
                'certificat_medical', 'diplomes', 'attestations'
            ];

            foreach ($documentFields as $field) {
                if ($request->hasFile($field)) {
                    // Supprimer l'ancien fichier s'il existe
                    if ($personnel->{$field . '_path'}) {
                        Storage::disk('public')->delete($personnel->{$field . '_path'});
                    }

                    $file = $request->file($field);
                    $filename = 'personnel_' . $validated['matricule'] . '_' . $field . '_' . time() . '.' . $file->extension();
                    $path = $file->storeAs('documents/personnel', $filename, 'public');
                    $documentsPaths[$field . '_path'] = $path;
                }
            }

            // Photo de profil
            if ($request->hasFile('photo_profil')) {
                if ($personnel->photo_profil) {
                    Storage::disk('public')->delete($personnel->photo_profil);
                }

                $photo = $request->file('photo_profil');
                $photoName = 'personnel_' . $validated['matricule'] . '_photo_' . time() . '.' . $photo->extension();
                $photoPath = $photo->storeAs('photos/personnel', $photoName, 'public');
                $validated['photo_profil'] = $photoPath;
            }

            // Recalculer la fin de période d'essai si la date d'embauche change
            if (isset($validated['date_embauche']) && $validated['date_embauche'] != $personnel->date_embauche) {
                $validated['fin_periode_essai'] = Carbon::parse($validated['date_embauche'])
                        ->addDays((int) ($validated['duree_essai_jours'] ?? $personnel->duree_essai_jours ?? 0));
            }

            // Gérer le départ
            if (in_array($validated['statut'], ['DEMISSION', 'LICENCIE', 'RETRAITE'])) {
                $validated['date_depart'] = $validated['date_depart'] ?? now();
            }

            foreach ($validated as $field => $value) {
                if (($value === null || $value === '') && $field !== 'photo_profil' && $personnel->{$field} !== null) {
                    $validated[$field] = $personnel->{$field};
                }
            }

            $validated = array_merge($validated, $documentsPaths);
            $validated['updated_by'] = Auth::id();

            // Générer automatiquement l'ID camera si photo uploadée et ID vide
            if (isset($validated['photo_profil']) && empty($personnel->camera_person_id)) {
                $validated['camera_person_id'] = 'CAM_' . str_replace(['KSL', 'EMP'], '', $personnel->matricule);
            }

            $personnel->update($this->filterPersonnelPayload($validated));

            DB::commit();

            $essentialDataChanged =
                $personnel->matricule !== $originalEssentialData['matricule'] ||
                $personnel->nom !== $originalEssentialData['nom'] ||
                $personnel->prenoms !== $originalEssentialData['prenoms'] ||
                $personnel->sexe !== $originalEssentialData['sexe'] ||
                optional($personnel->date_naissance)->format('Y-m-d') !== $originalEssentialData['date_naissance'] ||
                $personnel->photo_profil !== $originalEssentialData['photo_profil'];

            $cameraSyncSuccess = null;
            if ($essentialDataChanged && !empty($personnel->photo_profil)) {
                $cameraSyncSuccess = $this->hikvisionFaceSyncService->syncEmployeeToCamera($personnel);

                if (!$cameraSyncSuccess && Schema::hasColumn('personnel', 'facial_sync_status')) {
                    $syncFailurePayload = ['facial_sync_status' => 'failed'];

                    if (Schema::hasColumn('personnel', 'facial_sync_at')) {
                        $syncFailurePayload['facial_sync_at'] = null;
                    }

                    if (Schema::hasColumn('personnel', 'facial_device_id')) {
                        $syncFailurePayload['facial_device_id'] = null;
                    }

                    $personnel->update($syncFailurePayload);
                }
            } elseif ($essentialDataChanged) {
                Log::info('Resynchronisation caméra ignorée après modification: aucune photo de profil disponible.', [
                    'personnel_id' => $personnel->id,
                    'matricule' => $personnel->matricule,
                ]);
            }

            $successMessage = 'Informations du personnel mises à jour avec succès';

            if ($cameraSyncSuccess === true) {
                $successMessage .= ' et resynchronisées avec la caméra';
            } elseif ($cameraSyncSuccess === false) {
                $successMessage .= '. La mise à jour a réussi, mais la synchronisation caméra a échoué';
            }

            return redirect()
                ->route('rh.personnel.show', $personnel->id)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function destroy(Personnel $personnel)
    {
        try {
            // Supprimer les fichiers associés
            $filesToDelete = [
                $personnel->photo_profil,
                $personnel->cv_path,
                $personnel->lettre_motivation_path,
                $personnel->contrat_path,
                $personnel->casier_judiciaire_path,
                $personnel->certificat_medical_path,
                $personnel->diplomes_path,
                $personnel->attestations_path,
            ];

            foreach ($filesToDelete as $file) {
                if ($file) {
                    Storage::disk('public')->delete($file);
                }
            }

            $personnel->delete();

            return redirect()
                ->route('rh.personnel.index')
                ->with('success', 'Personnel supprimé avec succès');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // Méthodes supplémentaires
    public function export()
    {
        $personnels = Personnel::with(['user', 'createdBy'])->get();

        return response()->json([
            'personnels' => $personnels,
            'exported_at' => now(),
            'total' => $personnels->count()
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('search');

        if (empty($query)) {
            return response()->json([]);
        }

        try {
            // Vérifier les colonnes existantes
            $hasNom = Schema::hasColumn('personnel', 'nom');
            $hasPrenoms = Schema::hasColumn('personnel', 'prenoms');
            $hasMatricule = Schema::hasColumn('personnel', 'matricule');
            $hasPoste = Schema::hasColumn('personnel', 'poste');
            $hasService = Schema::hasColumn('personnel', 'service');
            $hasStatut = Schema::hasColumn('personnel', 'statut');

            // Construire la requête avec les colonnes disponibles
            $personnelQuery = Personnel::where('id', '>', 0); // Base query

            if ($hasNom) {
                $personnelQuery->orWhere('nom', 'LIKE', "%{$query}%");
            }
            if ($hasPrenoms) {
                $personnelQuery->orWhere('prenoms', 'LIKE', "%{$query}%");
            }
            if ($hasMatricule) {
                $personnelQuery->orWhere('matricule', 'LIKE', "%{$query}%");
            }
            if ($hasPoste) {
                $personnelQuery->orWhere('poste', 'LIKE', "%{$query}%");
            }
            if ($hasService) {
                $personnelQuery->orWhere('service', 'LIKE', "%{$query}%");
            }

            // Sélectionner seulement les colonnes qui existent
            $columns = ['id'];
            if ($hasMatricule) $columns[] = 'matricule';
            if ($hasNom) $columns[] = 'nom';
            if ($hasPrenoms) $columns[] = 'prenoms';
            if ($hasPoste) $columns[] = 'poste';
            if ($hasService) $columns[] = 'service';
            if ($hasStatut) $columns[] = 'statut';

            $personnels = $personnelQuery->limit(10)->get($columns);

            return response()->json($personnels);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Générer la fiche PDF d'un personnel
     */
    public function fichePdf(Personnel $personnel)
    {
        $this->authorize('view', $personnel);

        // Utiliser la vue fiche-pdf.blade.php
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rh.personnel.fiche-pdf', compact('personnel'));

        $filename = 'fiche_personnel_' . $personnel->matricule . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Générer le contrat de travail PDF d'un personnel
     */
    public function contratPdf(Personnel $personnel)
    {
        $this->authorize('view', $personnel);

        // Vérifier si le personnel a un contrat
        if (!$personnel->contrat_path) {
            return back()->with('error', 'Aucun contrat de travail trouvé pour ce personnel');
        }

        // Utiliser la vue contrat-pdf.blade.php si elle existe, sinon télécharger le fichier existant
        if (View::exists('rh.personnel.contrat-pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rh.personnel.contrat-pdf', compact('personnel'));
            $filename = 'contrat_travail_' . $personnel->matricule . '_' . now()->format('Y-m-d') . '.pdf';
            return $pdf->download($filename);
        } else {
            // Télécharger le contrat existant
            $contratPath = storage_path('app/public/' . $personnel->contrat_path);
            if (file_exists($contratPath)) {
                return response()->download($contratPath, 'contrat_travail_' . $personnel->matricule . '.pdf');
            } else {
                return back()->with('error', 'Le fichier de contrat est introuvable');
            }
        }
    }
}
