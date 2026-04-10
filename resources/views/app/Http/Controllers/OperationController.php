<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Operation;
use App\Models\OperationStatusLog;
use App\Models\ServiceOperationnel;
use App\Models\TypeOperation;
use App\Services\EmailNotificationService;
use App\Mail\SendEmail;
use App\Mail\ValidationStepNotification;
use App\Mail\OperationApprovedNotification;
use App\Mail\OperationProfessionalMail;


class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Operation::with(['client', 'typeOperation', 'operationalService']);

        // Filtrage par utilisateur si non admin
        $user = Auth::user();
        $userRole = $user->role ?? 'user';

        if (!in_array($userRole, ['admin', 'superadmin'])) {
            $query->where(function($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id)
                        ->orWhere('demandeur_email', $user->email)
                        ->orWhere('demandeur_name', $user->name);
            });
        }

        // Filtres de recherche
        if ($request->has('q') && $request->q) {
            $query->where(function($q) use ($request) {
                $q->where('titre', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%')
                  ->orWhere('demandeur_name', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('statut') && $request->statut) {
            $query->where('statut_courant', $request->statut);
        }

        if ($request->has('priorite') && $request->priorite) {
            $query->where('priorite', $request->priorite);
        }

        $operations = $query->latest()->paginate(15);

        return view('operations.index', compact('operations', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = TypeOperation::orderBy('libelle')->get();
        $services = ServiceOperationnel::whereNotNull('nom')->orderBy('nom')->get();
        return view('operations.create', compact('types', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'nullable|numeric|min:0',
            'priorite' => 'required|in:urgente,haute,moyenne,basse',
            'echeance' => 'nullable|date|after_or_equal:today',
            'destinataire_principal' => 'required|integer|exists:services_operationnels,id',
            'type_operation_id' => 'required|integer|exists:types_operations,id',
            'validateur_1' => 'nullable|integer|exists:services_operationnels,id',
            'validateur_2' => 'nullable|integer|exists:services_operationnels,id',
            'validateur_3' => 'nullable|integer|exists:services_operationnels,id',
            'services_cc' => 'nullable|string',
            'fichiers.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'draft' => 'sometimes|boolean',
        ]);

        // Validation personnalisée pour les services CC
        if (!empty($validated['services_cc'])) {
            $ccServiceIds = explode(',', $validated['services_cc']);
            foreach ($ccServiceIds as $serviceId) {
                $serviceId = trim($serviceId);
                if (!empty($serviceId) && !is_numeric($serviceId)) {
                    return back()
                        ->withInput()
                        ->withErrors(['services_cc' => 'Les services CC doivent être des IDs valides']);
                }

                if (!empty($serviceId) && !ServiceOperationnel::where('id', $serviceId)->exists()) {
                    return back()
                        ->withInput()
                        ->withErrors(['services_cc' => "Le service CC avec l'ID $serviceId n'existe pas"]);
                }
            }
        }

        try {
            $user = Auth::user();
            $isDraft = $request->has('draft');

            #Enregistrement de l'opération
            $operation = new Operation();
            $operation->type = 'operation';
            $operation->titre = $validated['titre'];
            $operation->description = $validated['description'] ?? null;
            $operation->montant = $validated['montant'] ?? null;
            $operation->date_operation = now();
            $operation->priorite = $validated['priorite'];
            $operation->echeance = $validated['echeance'] ?? null;
            $operation->type_operation_id = $validated['type_operation_id'];
            $operation->operational_service_id = $validated['destinataire_principal'];
            $operation->statut_courant = $isDraft ? 'brouillon' : 'pending_validation';
            $operation->demandeur_name = $user?->name ?? 'Démo';
            $operation->demandeur_email = $user?->email ?? config('mail.from.address');
            $operation->user_id = $user?->id;
            $operation->save();

            // Gestion des pièces jointes
            if ($request->hasFile('fichiers')) {
                $fichiers = [];
                foreach ($request->file('fichiers') as $file) {
                    $path = $file->store('operations/' . $operation->id . '/fichiers', 'public');
                    $fichiers[] = [
                        'operation_id' => $operation->id,
                        'nom_original' => $file->getClientOriginalName(),
                        'chemin' => $path,
                        'type_fichier' => $file->getClientMimeType(),
                        'taille' => $file->getSize(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $operation->fichiers()->createMany($fichiers);
            }

            // Enregistrement du statut
            \App\Models\OperationStatusLog::create([
                'operation_id' => $operation->id,
                'from_status' => null,
                'to_status' => $isDraft ? 'brouillon' : 'pending_validation',
                'user_name' => $user ? $user->name : 'Système',
                'user_id' => $user ? $user->id : null,
                'commentaire' => $isDraft ? 'Enregistré comme brouillon' : 'Soumis pour validation',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Créer le circuit de validation complet
            if (!$isDraft) {
                $customValidators = [
                    $validated['validateur_1'] ?? null,
                    $validated['validateur_2'] ?? null,
                    $validated['validateur_3'] ?? null,
                ];
                $this->setupValidationChain($operation, $validated['destinataire_principal'], array_filter($customValidators));
            }

            # Récupération de l'url de validation
            $url = request()->getSchemeAndHttpHost() . "/operations/{$operation->id}/validate";

            # Envoie de mail au PREMIER validateur (seulement si ce n'est pas un brouillon)
            # Envoi du mail de validation
            if (!$isDraft) {
                $this->sendValidationRequestMail($operation, $validated['services_cc'] ?? '');
            }


            return redirect()->route('operations.index')
                ->with('success', $isDraft ? 'Opération enregistrée comme brouillon.' : 'Opération créée avec succès');

        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }


    /**
     * Review operation for validation (temporary signed route)
     */
    public function reviewOperation(Operation $operation, $step)
    {
        $user = Auth::user();

        // Récupérer les étapes de validation
        $validationSteps = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->orderBy('ordre_validation')
            ->get();

        // Trouver l'étape spécifique demandée
        $targetStep = $validationSteps->where('ordre_validation', (int)$step)->first();

        // Vérifier si l'utilisateur appartient au service qui doit valider (par ID ou par Email)
        $isValidator = false;
        if ($targetStep) {
            $isValidator = ($user->service_id == $targetStep->service_operationnel_id);

            if (!$isValidator) {
                // Vérification par email si l'ID ne correspond pas
                $serviceEmail = \DB::table('services_operationnels')
                    ->where('id', $targetStep->service_operationnel_id)
                    ->value('email');

                if ($serviceEmail && $serviceEmail === $user->email) {
                    $isValidator = true;
                }
            }
        }

        // Autoriser UNIQUEMENT si Validateur de l'étape OU Admin
        $canAct = $isValidator || in_array($user->role ?? 'user', ['admin', 'superadmin']);

        if (!$canAct) {
            abort(403, "Accès refusé. Seul le service destiné à valider cette étape peut accéder à cette page.");
        }

        // Vérifier que l'étape est bien celle en cours (sauf pour admin/superadmin)
        $isAdmin = in_array($user->role ?? 'user', ['admin', 'superadmin']);
        if ($targetStep && $targetStep->statut !== 'EN_COURS' && !$isAdmin) {
            abort(403, "Cette étape n'est pas ouverte à la validation actuellement (Statut: {$targetStep->statut}).");
        }

        // Vérifier que l'opération est bien chargée avec ses relations essentielles
    try {
        $operation->load(['typeOperation', 'operationalService', 'fichiers']);
    } catch (\Exception $e) {
        \Log::error("Erreur chargement relations opération {$operation->id}: " . $e->getMessage());
    }

    return view('operations.review', compact('operation', 'validationSteps', 'step'));
    }

    /**
     * Submit review for validation
     */
    public function submitReview(Operation $operation, $step, Request $request)
    {
        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:500',
            'action' => 'required|in:approve,reject'
        ]);

        if ($validated['action'] === 'approve') {
            return $this->approveOperation($request, $operation, $step);
        } elseif ($validated['action'] === 'reject') {
            // Ensure comment is present for rejection if strictly required by rejectOperation
            if (empty($validated['commentaire'])) {
                 return back()->withErrors(['commentaire' => 'Le commentaire est obligatoire pour rejeter une opération.'])->withInput();
            }
            return $this->rejectOperation($request, $operation, $step);
        }

        return back();
    }

    /**
     * Page d'exécution d'une opération
     */
    public function execution(Operation $operation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de voir cette opération
        $canView = $operation->user_id === $user->id ||
                   $operation->demandeur_email === $user->email ||
                   $operation->demandeur_name === $user->name ||
                   in_array($user->role, ['admin', 'superadmin']);

        if (!$canView) {
            abort(403, 'Accès non autorisé à cette opération');
        }

        $operation->load(['client', 'typeOperation', 'operationalService', 'fichiers']);

        // Récupérer les étapes de validation
        $steps = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        $user = Auth::user();
        // Vérifier si l'utilisateur est la comptabilité ou la trésorerie (mail payeur)
        // On suppose que le mail payeur est stocké dans $operation->mail_payeur
        $isPayeur = false;
        if (property_exists($operation, 'mail_payeur') && $operation->mail_payeur) {
            $isPayeur = ($user->email === $operation->mail_payeur);
        } else {
            // Fallback : vérifier si le service de l'utilisateur est "comptabilité" ou "trésorerie"
            $isPayeur = in_array(strtolower($user->role), ['tresorerie', 'comptabilite', 'comptabilité']);
        }

        if ($isPayeur && !$operation->is_paid) {
            $operation->forceFill([
                'is_paid' => true,
                'paid_at' => now(),
                'paid_by' => $user->id,
                'statut_courant' => 'payee',
            ])->save();

            // Ajouter un log dans OperationStatusLog
            \App\Models\OperationStatusLog::create([
                'operation_id' => $operation->id,
                'from_status' => $operation->statut_courant,
                'to_status' => 'payee',
                'user_name' => $user->name,
                'user_id' => $user->id,
                'commentaire' => 'Opération marquée comme payée par la comptabilité/trésorerie',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return view('operations.execution', compact('operation', 'steps'));
    }

    /**
     * Page de contrôle d'une opération
     */
    public function controle(Operation $operation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de voir cette opération
        $canView = $operation->user_id === $user->id ||
                   $operation->demandeur_email === $user->email ||
                   $operation->demandeur_name === $user->name ||
                   in_array($user->role, ['admin', 'superadmin']);

        if (!$canView) {
            abort(403, 'Accès non autorisé à cette opération');
        }

        $operation->load(['client', 'typeOperation', 'operationalService', 'fichiers']);

        // Récupérer les étapes de validation
        $steps = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        return view('operations.controle', compact('operation', 'steps'));
    }

    /**
     * Page des coûts d'une opération
     */
    public function couts(Operation $operation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de voir cette opération
        $canView = $operation->user_id === $user->id ||
                   $operation->demandeur_email === $user->email ||
                   $operation->demandeur_name === $user->name ||
                   in_array($user->role, ['admin', 'superadmin']);

        if (!$canView) {
            abort(403, 'Accès non autorisé à cette opération');
        }

        $operation->load(['client', 'typeOperation', 'operationalService', 'fichiers']);

        // Récupérer les étapes de validation
        $steps = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        return view('operations.couts', compact('operation', 'steps'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Operation $operation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de voir cette opération
        $canView = $operation->user_id === $user->id ||
                   $operation->demandeur_email === $user->email ||
                   $operation->demandeur_name === $user->name ||
                   in_array($user->role, ['admin', 'superadmin']);

        if (!$canView) {
            abort(403, 'Accès non autorisé à cette opération');
        }

        $operation->load(['client', 'typeOperation', 'operationalService', 'fichiers']);

        // Récupérer l'étape de validation actuelle
        $currentStep = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->where('statut', 'EN_COURS')
            ->first();

        // S'il n'y a pas d'étape en cours, récupérer la première étape EN_ATTENTE
        if (!$currentStep) {
            $currentStep = \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('statut', 'EN_ATTENTE')
                ->orderBy('ordre_validation')
                ->first();
        }

        // Récupérer toutes les étapes de validation
        $steps = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        // Historique des statuts
        $statusHistory = $operation->statusLogs()->with('user')->orderBy('created_at', 'desc')->get();

        return view('operations.show', compact('operation', 'currentStep', 'steps', 'statusHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operation $operation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de modifier cette opération
        $canEdit = $operation->user_id === $user->id ||
                   $operation->demandeur_email === $user->email ||
                   in_array($user->role, ['admin', 'superadmin']);

        if (!$canEdit) {
            abort(403, 'Accès non autorisé à cette opération');
        }

    $services = \App\Models\ServiceOperationnel::orderBy('nom')->get();
    $operationalServices = $services; // Pour la compatibilité avec la vue
    $types = \App\Models\TypeOperation::orderBy('libelle')->get();

    // Récupérer les services déjà sélectionnés pour cette opération
    $defaultChain = $operation->services->pluck('service_name')->toArray();

    return view('operations.edit', compact('operation', 'services', 'operationalServices', 'types', 'defaultChain'));
    }

    public function update(Request $request, Operation $operation)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'nullable|numeric|min:0',
            'priorite' => 'required|in:urgente,haute,moyenne,basse',
            'echeance' => 'nullable|date',
            'destinataire_principal' => 'required|integer|exists:services_operationnels,id',
            'type_operation_id' => 'required|integer|exists:types_operations,id',
        ]);

        try {
            $operation->update([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'montant' => $validated['montant'],
                'priorite' => $validated['priorite'],
                'echeance' => $validated['echeance'],
                'operational_service_id' => $validated['destinataire_principal'],
                'type_operation_id' => $validated['type_operation_id'],
            ]);

            return redirect()->route('operations.show', $operation)
                ->with('success', 'Opération mise à jour avec succès.');

        } catch (\Throwable $e) {
            return redirect()->route('operations.edit', $operation)
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operation $operation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de supprimer cette opération
        $canDelete = $operation->user_id === $user->id ||
                   in_array($user->role, ['admin', 'superadmin']);

        if (!$canDelete) {
            abort(403, 'Accès non autorisé à cette opération');
        }

        try {
            $operation->delete();

            return redirect()->route('operations.index')
                ->with('success', 'Opération supprimée avec succès.');

        } catch (\Throwable $e) {
            return redirect()->route('operations.index')
                ->with('error', 'Une erreur est survenue lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard des opérations
     */
    public function dashboard()
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'user';

        // Statistiques personnelles de l'utilisateur
        $userOperations = Operation::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('demandeur_email', $user->email)
                  ->orWhere('demandeur_name', $user->name);
        });

        $stats = [
            'total_operations' => $userOperations->count(),
            'operations_en_cours' => $userOperations->where('statut_courant', 'en_cours')->count(),
            'operations_terminees' => $userOperations->where('statut_courant', 'termine')->count(),
            'operations_en_retard' => $userOperations->where('echeance', '<', now())
                                         ->where('statut_courant', '!=', 'termine')->count(),
        ];

        // Opérations récentes de l'utilisateur
        $recentOperations = $userOperations->with(['client', 'typeOperation', 'operationalService'])
                                   ->latest()
                                   ->limit(10)
                                   ->get();

        return view('operations.dashboard', compact('stats', 'recentOperations'));
    }

    /**
     * Afficher les requêtes personnelles de l'utilisateur
     */
    public function myRequests(Request $request)
    {
        $user = Auth::user();

        $operations = Operation::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('demandeur_email', $user->email)
                  ->orWhere('demandeur_name', $user->name);
        })
        ->with(['typeOperation', 'operationalService'])
        ->latest()
        ->paginate(15);

        return view('operations.my-requests', compact('operations'));
    }

    /**
     * Afficher les opérations assignées à l'utilisateur
     */
    public function myOperations(Request $request)
    {
        $user = Auth::user();

        $operations = Operation::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('demandeur_email', $user->email)
                  ->orWhere('demandeur_name', $user->name);
        })
        ->with(['client', 'typeOperation', 'operationalService'])
        ->latest()
        ->paginate(15);

        return view('operations.my-operations', compact('operations'));
    }

    /**
     * Suivi détaillé d'une opération
     */
    public function tracking(Operation $operation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de voir cette opération
        $canView = $operation->user_id === $user->id ||
                   $operation->demandeur_email === $user->email ||
                   $operation->demandeur_name === $user->name ||
                   in_array($user->role, ['admin', 'superadmin']);

        if (!$canView) {
            abort(403, 'Accès non autorisé à cette opération');
        }

        $operation->load(['client', 'typeOperation', 'operationalService', 'fichiers']);

        // Historique des statuts
        $statusHistory = $operation->statusLogs()->with('user')->orderBy('created_at', 'desc')->get();

        return view('operations.show', compact('operation', 'statusHistory'));
    }

    /**
     * Opérations en attente de validation
     */
    public function pendingValidation()
    {
        $user = Auth::user();
        $query = Operation::with(['initiateur', 'services'])
            ->whereIn('statut_courant', ['en_attente', 'pending_validation', 'en_validation']);

        // Si l'utilisateur n'est pas admin/superadmin, il ne voit que ce qu'il doit valider
        if ($user && !in_array($user->role, ['admin', 'superadmin'])) {
            $query->whereExists(function ($q) use ($user) {
                $q->select(\DB::raw(1))
                  ->from('operation_service_validation')
                  ->whereColumn('operation_service_validation.operation_id', 'operations.id')
                  ->where('operation_service_validation.statut', 'EN_COURS')
                  ->where(function($sub) use ($user) {
                      $sub->where('operation_service_validation.service_operationnel_id', $user->service_id)
                          ->orWhereIn('operation_service_validation.service_operationnel_id', function($sq) use ($user) {
                              $sq->select('id')->from('services_operationnels')->where('email', $user->email);
                          });
                  });
            });
        }

        $validations = $query->latest()->paginate(15);

        return view('validations.pending', compact('validations'));
    }

    /**
     * Opérations approuvées
     */
    public function approvedValidation()
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'user';

        // Si l'utilisateur est admin ou superadmin, voir toutes les validations approuvées
        if (in_array($userRole, ['admin', 'superadmin'])) {
            $operations = Operation::where('statut_courant', 'approuvee')
                ->with(['client', 'typeOperation', 'operationalService', 'initiateur'])
                ->latest()
                ->paginate(15);
        } else {
            // Pour les autres utilisateurs, voir uniquement leurs propres requêtes approuvées
            // ET les opérations où ils sont validateurs
            $operations = Operation::where('statut_courant', 'approuvee')
                ->where(function($query) use ($user) {
                    // Opérations créées par l'utilisateur
                    $query->where('user_id', $user->id)
                          ->orWhere('demandeur_email', $user->email)
                          ->orWhere('demandeur_name', $user->name);
                })
                ->orWhere(function($query) use ($user) {
                    // Opérations où l'utilisateur est validateur
                    $query->where('statut_courant', 'approuvee')
                          ->whereHas('validations', function($subQuery) use ($user) {
                              $subQuery->where('statut', 'approved')
                                      ->whereHas('serviceOperationnel', function($serviceQuery) use ($user) {
                                          $serviceQuery->where('email', $user->email);
                                      });
                          });
                })
                ->with(['client', 'typeOperation', 'operationalService', 'initiateur'])
                ->latest()
                ->paginate(15);
        }

        // Ajouter les informations de validation pour chaque opération
        foreach ($operations as $operation) {
            // Si le nom du demandeur est vide, utiliser le nom de l'initiateur
            if (empty($operation->demandeur_name) && $operation->initiateur) {
                $operation->demandeur_name = $operation->initiateur->name;
            }
            // Récupérer le PREMIER validateur (celui qui a reçu l'email en premier)
            $firstValidation = \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('statut', 'APPROUVE')
                ->orderBy('ordre_validation', 'asc')
                ->first();

            if ($firstValidation) {
                // Récupérer le nom du validateur
                $validator = \App\Models\User::find($firstValidation->validateur_id);
                $operation->validator_name = $validator ? $validator->name : 'Non assigné';

                $operation->date_approbation = $firstValidation->date_validation;

                // Calculer le temps de traitement
                if ($firstValidation->date_validation && $operation->created_at) {
                    $createdAt = \Carbon\Carbon::parse($operation->created_at);
                    $validatedAt = \Carbon\Carbon::parse($firstValidation->date_validation);
                    $diffInSeconds = $createdAt->diffInSeconds($validatedAt);

                    // Convertir en format hh:mm:ss
                    $hours = floor($diffInSeconds / 3600);
                    $minutes = floor(($diffInSeconds % 3600) / 60);
                    $seconds = $diffInSeconds % 60;

                    $operation->processing_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                } else {
                    $operation->processing_time = '-';
                }
            } else {
                // Tenter de récupérer depuis l'historique des statuts si pas trouvé dans les validations
                $lastLog = \App\Models\OperationStatusLog::where('operation_id', $operation->id)
                    ->where('to_status', 'approuvee')
                    ->latest()
                    ->first();

                if ($lastLog) {
                    $operation->validator_name = $lastLog->user_name ?? 'Inconnu';
                    $operation->date_approbation = $lastLog->created_at;

                    // Calculer le temps de traitement
                    if ($operation->created_at) {
                        $createdAt = \Carbon\Carbon::parse($operation->created_at);
                        $validatedAt = \Carbon\Carbon::parse($lastLog->created_at);
                        $diffInSeconds = $createdAt->diffInSeconds($validatedAt);

                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $seconds = $diffInSeconds % 60;

                        $operation->processing_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                    } else {
                        $operation->processing_time = '-';
                    }
                } else {
                    $operation->validator_name = 'Non assigné';
                    $operation->processing_time = '-';
                }
            }
        }

        return view('operations.approved-validation', compact('operations'));
    }

    /**
     * Opérations rejetées
     */
    public function rejectedValidation()
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'user';

        // Si l'utilisateur est admin ou superadmin, voir toutes les validations rejetées
        if (in_array($userRole, ['admin', 'superadmin'])) {
            $operations = Operation::where('statut_courant', 'rejetee')
                ->with(['client', 'typeOperation', 'operationalService'])
                ->latest()
                ->paginate(15);
        } else {
            // Pour les autres utilisateurs, voir uniquement leurs propres requêtes rejetées
            // ET les opérations où ils sont validateurs
            $operations = Operation::where('statut_courant', 'rejetee')
                ->where(function($query) use ($user) {
                    // Opérations créées par l'utilisateur
                    $query->where('user_id', $user->id)
                          ->orWhere('demandeur_email', $user->email)
                          ->orWhere('demandeur_name', $user->name);
                })
                ->orWhere(function($query) use ($user) {
                    // Opérations où l'utilisateur est validateur
                    $query->where('statut_courant', 'rejetee')
                          ->whereHas('validations', function($subQuery) use ($user) {
                              $subQuery->where('statut', 'rejected')
                                      ->whereHas('serviceOperationnel', function($serviceQuery) use ($user) {
                                          $serviceQuery->where('email', $user->email);
                                      });
                          });
                })
                ->with(['client', 'typeOperation', 'operationalService'])
                ->latest()
                ->paginate(15);
        }

        return view('operations.rejected-validation', compact('operations'));
    }

    /**
     * Historique de toutes les validations (approuvées et rejetées)
     */
    public function validationHistory()
    {
        $user = Auth::user();

        // On récupère l'historique des actions de validation (le pivot)
        $query = \App\Models\OperationServiceValidation::join('operations', 'operation_service_validation.operation_id', '=', 'operations.id')
            ->select(
                'operation_service_validation.*',
                'operations.titre as operation_titre',
                'operations.statut_courant as operation_statut_courant'
            )
            ->with(['serviceOperationnel', 'validateur', 'operation']);

        // On ne s'intéresse qu'aux validations qui ont été traitées (Approuvées ou Rejetées)
        $query->whereIn('operation_service_validation.statut', ['APPROUVE', 'REJETE']);

        // Si l'utilisateur n'est pas admin, il ne voit que ses propres validations ou celles de son service
        if (!$user->isAdmin()) {
            $query->where(function($q) use ($user) {
                $q->where('operation_service_validation.validateur_id', $user->id)
                  ->orWhere('operation_service_validation.service_operationnel_id', $user->service_id);
            });
        }

        $validations = $query->orderBy('operation_service_validation.date_validation', 'desc')
            ->paginate(15);

        return view('validations.history', compact('validations'));
    }

    /**
     * Opérations payées
     */
    public function paidOperations(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'user';

        $search = $request->input('q', '');
        $serviceId = $request->input('service_id', '');

        $query = Operation::where('statut_courant', 'payee')
            ->with(['client', 'typeOperation', 'operationalService', 'initiateur', 'paidBy', 'invoice']);

        if (!in_array($userRole, ['admin', 'superadmin'])) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('demandeur_email', $user->email)
                  ->orWhere('demandeur_name', $user->name);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($serviceId) {
            $query->where('operational_service_id', $serviceId);
        }

        $operations = $query->latest()->paginate(15);
        $services = \App\Models\ServiceOperationnel::actif()->ordre()->get();

        return view('validations.paid', compact('operations', 'search', 'serviceId', 'services'));
    }

    /**
     * Page de validation spécifique pour une opération
     */
    public function validateOperation(Request $request, Operation $operation)
    {
        // Gérer la soumission du formulaire (POST)
        if ($request->isMethod('post')) {
            $step = $request->input('step', 1);
            $action = $request->input('action');

            if ($action === 'reject') {
                return $this->rejectOperation($request, $operation, $step);
            }

            return $this->approveOperation($request, $operation, $step);
        }

        // Vérifier que l'utilisateur a le droit de valider cette opération
        $user = Auth::user();
        $userRole = $user->role ?? 'user';

        // Vérifier si l'utilisateur fait partie des services de validation
        $validationSteps = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->where('services_operationnels.email', $user->email)
            ->orderBy('operation_service_validation.ordre_validation')
            ->get();

        // Initialiser ou compléter la chaîne de validation si nécessaire
        $this->setupValidationChain($operation, $operation->operational_service_id, []);

        if ($validationSteps->isEmpty() && !in_array($userRole, ['admin', 'superadmin'])) {
            abort(403, 'Vous n\'êtes pas autorisé à valider cette opération');
        }

        // Récupérer toutes les étapes de validation
        $allSteps = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        // Trouver l'étape actuelle de l'utilisateur
        $currentStep = $validationSteps->first();

        // Récupérer l'historique des statuts
        $statusHistory = \App\Models\OperationStatusLog::where('operation_id', $operation->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer tous les services actifs pour le champ CC
        $services = \App\Models\ServiceOperationnel::actif()->ordre()->get(['id', 'nom']);

        return view('operations.validate', compact('operation', 'currentStep', 'allSteps', 'statusHistory', 'services', 'validationSteps'));
    }

    /**
     * Valider une opération (méthode POST)
     */
    public function processValidation(Operation $operation)
    {
        // Logique de validation simple
        $operation->statut_courant = 'approuvee';
        $operation->save();

        // Enregistrer le log de statut
        \App\Models\OperationStatusLog::create([
            'operation_id' => $operation->id,
            'from_status' => 'pending_validation',
            'to_status' => 'approuvee',
            'user_name' => Auth::user()->name,
            'user_id' => Auth::user()->id,
            'commentaire' => 'Opération validée avec succès',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('operations.index')->with('success', 'Opération validée avec succès !');
    }

    /**
     * Envoyer la notification d'approbation avec services CC
     */
    private function sendApprovalNotification($operation, $step, $ccServices = '')
    {
        try {
            // Récupérer le service de l'étape suivante
            $nextStep = \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', '>', $step)
                ->where('statut', 'EN_ATTENTE')
                ->orderBy('ordre_validation')
                ->first();

            if ($nextStep) {
                $service = \DB::table('services_operationnels')
                    ->where('id', $nextStep->service_id)
                    ->first();

                if ($service) {
                    // Préparer les destinataires CC
                    $ccEmails = [];
                    if ($ccServices) {
                        $ccServiceIds = explode(',', $ccServices);
                        $ccServicesData = \DB::table('services_operationnels')
                            ->whereIn('id', $ccServiceIds)
                            ->pluck('email')
                            ->toArray();
                        $ccEmails = array_filter($ccServicesData);
                    }

                    // Envoyer l'email au validateur suivant avec les CC
                    $url = request()->getSchemeAndHttpHost() . "/operations/{$operation->id}/validate";
                    $mail = new SendEmail($operation, $url);

                    if (!empty($ccEmails)) {
                        $mail->cc($ccEmails);
                    }

                    Mail::to($service->email)->send($mail);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de l\'email de notification: ' . $e->getMessage());
        }
    }

    /**
     * Approuver une opération
     */
    public function approveOperation(Request $request, Operation $operation, $step)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est admin/superadmin
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        // Vérification de sécurité : Seul le validateur (par ID ou Email) ou l'admin peut approuver
        $isValidator = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->where('ordre_validation', (int)$step)
            ->when(!$isAdmin, function($query) {
                // Pour les non-admins, l'étape doit être EN_COURS
                $query->where('statut', 'EN_COURS');
            })
            ->where(function($q) use ($user) {
                $q->where('service_operationnel_id', $user->service_id)
                  ->orWhereIn('service_operationnel_id', function($sq) use ($user) {
                      $sq->select('id')->from('services_operationnels')->where('email', $user->email);
                  });
            })
            ->exists();

        if (!$isValidator && !$isAdmin) {
            \Log::info('Validation refusée', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'isAdmin' => $isAdmin,
                'isValidator' => $isValidator,
                'operation_id' => $operation->id,
                'step' => $step
            ]);
            abort(403, "Vous n'avez pas le droit de valider cette étape.");
        }

        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:500',
            'next_service_id' => 'nullable|exists:services_operationnels,id',
            'cc_services' => 'nullable|string',
            'circuit_cc_services' => 'nullable|string',
            'pieces_jointes' => 'nullable|array',
            'pieces_jointes.*' => 'file|mimes:pdf,xlsx,xls,csv|max:10240', // 10MB max
        ]);

        $user = Auth::user();

        \DB::transaction(function() use ($operation, $step, $validated, $user, $request, $isAdmin) {
            $forceApprove = $isAdmin && $request->input('force_approve') == '1';

            if ($forceApprove) {
                // APPROBATION DIRECTE (ADMIN) : On valide toutes les étapes restantes
                \DB::table('operation_service_validation')
                    ->where('operation_id', $operation->id)
                    ->where('statut', '!=', 'APPROUVE')
                    ->update([
                        'statut' => 'APPROUVE',
                        'validateur_id' => $user->id,
                        'date_validation' => now(),
                        'commentaire' => ($validated['commentaire'] ?? '') . ' (Approbation directe par Admin)',
                    ]);

                // On s'assure que le statut est bien APPROUVE pour toutes (au cas où)
                \DB::table('operation_service_validation')
                    ->where('operation_id', $operation->id)
                    ->update(['statut' => 'APPROUVE']);

                $previousStatus = $operation->statut_courant;
                $operation->update(['statut_courant' => 'approuvee']);

                // Log d'approbation directe
                \App\Models\OperationStatusLog::create([
                    'operation_id' => $operation->id,
                    'from_status' => $previousStatus,
                    'to_status' => 'approuvee',
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'commentaire' => 'Approbation directe effectuée par l\'administrateur. ' . ($validated['commentaire'] ?? ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Notifier le demandeur
                if ($operation->demandeur_email) {
                    try {
                        \Mail::to($operation->demandeur_email)->send(new \App\Mail\OperationApprovedNotification($operation, $validated['commentaire'] ?? 'Approbation directe par Admin'));
                    } catch (\Throwable $e) {
                        \Log::error('Erreur mail approbation directe admin: ' . $e->getMessage());
                    }
                }

                return; // Fin de la transaction pour le force approve
            }

            // LOGIQUE STANDARD (Étape par étape)
            // Mettre à jour l'étape de validation actuelle
            \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', $step)
                ->update([
                    'statut' => 'APPROUVE',
                    'validateur_id' => $user->id,
                    'date_validation' => now(),
                    'commentaire' => $validated['commentaire'] ?? null,
                ]);

            // Gérer les pièces jointes supplémentaires
            if ($request->hasFile('pieces_jointes')) {
                foreach ($request->file('pieces_jointes') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('operations/' . $operation->id . '/pieces_jointes', $filename, 'public');

                    \DB::table('fichiers_operations')->insert([
                        'operation_id' => $operation->id,
                        'nom_original' => $file->getClientOriginalName(),
                        'chemin' => $path,
                        'type_fichier' => $file->getClientOriginalExtension(),
                        'taille' => $file->getSize(),
                        'uploaded_by' => $user->email,
                        'created_at' => now(),
                    ]);
                }
            }

            // Combiner les services CC des deux sources
            $allCcServices = [];
            if (!empty($validated['cc_services'])) {
                $allCcServices[] = $validated['cc_services'];
            }
            if (!empty($validated['circuit_cc_services'])) {
                $allCcServices[] = $validated['circuit_cc_services'];
            }
            $combinedCcServices = implode(',', array_filter($allCcServices));

            // Envoyer l'email de notification d'avancement au demandeur systématiquement
            if ($operation->demandeur_email) {
                try {
                    \Mail::to($operation->demandeur_email)->send(
                        new \App\Mail\OperationStepApprovedMail($operation, $user, $validated['commentaire'] ?? null, null)
                    );
                } catch (\Exception $e) {
                    \Log::error('Erreur mail avancement demandeur: ' . $e->getMessage());
                }
            }

            // Gérer le passage à l'étape suivante
            $nextStep = \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', '>', $step)
                ->where('statut', 'EN_ATTENTE')
                ->orderBy('ordre_validation')
                ->first();

            if ($nextStep) {
                // Récupérer le service suivant pour la notification
                $nextService = \App\Models\ServiceOperationnel::find($nextStep->service_operationnel_id);

                // Passer à l'étape suivante
                \DB::table('operation_service_validation')
                    ->where('id', $nextStep->id)
                    ->update(['statut' => 'EN_COURS']);

                // Mettre à jour le statut de l'opération (en cours de validation)
                $operation->update(['statut_courant' => 'en_validation']);

                // Envoyer l'email au service suivant via la méthode centralisée
                $this->sendValidationRequestMail($operation, $combinedCcServices ?? '');

                // Enregistrer le log de validation d'étape
                \App\Models\OperationStatusLog::create([
                    'operation_id' => $operation->id,
                    'from_status' => 'en_validation',
                    'to_status' => 'en_validation',
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'commentaire' => "Étape $step validée par " . ($user->name ?? 'Validateur') . ". " . ($validated['commentaire'] ?? ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // AUCUNE ÉTAPE SUIVANTE : La dernière approbation est donnée
                // Marquer comme approuvée (prête pour paiement)
                $operation->update([
                    'statut_courant' => 'approuvee',
                ]);

                // Enregistrer le log d'approbation finale
                \App\Models\OperationStatusLog::create([
                    'operation_id' => $operation->id,
                    'from_status' => 'en_validation',
                    'to_status' => 'approuvee',
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'commentaire' => 'Validation terminée : L\'opération est approuvée. ' . ($validated['commentaire'] ?? ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // NOTIFIER LE DEMANDEUR : Requête approuvée
                if ($operation->demandeur_email) {
                    try {
                        \Mail::to($operation->demandeur_email)->send(new \App\Mail\OperationApprovedNotification($operation, $validated['commentaire'] ?? null));
                    } catch (\Throwable $e) {
                        \Log::error('Erreur envoi mail approbation finale: ' . $e->getMessage());
                    }
                }
            }
        });

        return redirect()->route('validations.pending')
            ->with('success', 'Opération approuvée avec succès');
    }

    /**
     * Rejeter une opération
     */
    public function rejectOperation(Request $request, Operation $operation, $step)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est admin/superadmin
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        // Vérification de sécurité : Seul le validateur (par ID ou Email) ou l'admin peut rejeter
        $isValidator = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->where('ordre_validation', (int)$step)
            ->when(!$isAdmin, function($query) {
                // Pour les non-admins, l'étape doit être EN_COURS
                $query->where('statut', 'EN_COURS');
            })
            ->where(function($q) use ($user) {
                $q->where('service_operationnel_id', $user->service_id)
                  ->orWhereIn('service_operationnel_id', function($sq) use ($user) {
                      $sq->select('id')->from('services_operationnels')->where('email', $user->email);
                  });
            })
            ->exists();

        if (!$isValidator && !$isAdmin) {
            abort(403, "Vous n'avez pas le droit de rejeter cette étape.");
        }

        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        \DB::transaction(function() use ($operation, $step, $validated, $user) {
            // Mettre à jour l'étape de validation
            \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', $step)
                ->update([
                    'statut' => 'REJETE',
                    'validateur_id' => $user->id,
                    'date_validation' => now(),
                    'commentaire' => $validated['commentaire'],
                ]);

            // Mettre à jour le statut de l'opération
            $operation->update(['statut_courant' => 'rejetee']);

            // Enregistrer dans l'historique
            \App\Models\OperationStatusLog::create([
                'operation_id' => $operation->id,
                'from_status' => 'pending_validation',
                'to_status' => 'rejetee',
                'user_name' => $user->name,
                'user_id' => $user->id,
                'commentaire' => $validated['commentaire'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Notifier le demandeur
            if ($operation->demandeur_email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($operation->demandeur_email)->send(
                        new \App\Mail\OperationRejectedMail($operation, $validated['commentaire'])
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur envoi mail rejet (OperationController): ' . $e->getMessage());
                }
            }
        });

        return redirect()->route('validations.rejected')
            ->with('error', 'Opération rejetée');
    }
    /**
     * Envoyer l'email d'exécution pour une opération approuvée
     */
    public function sendExecutionEmail(Request $request, Operation $operation)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Vérification de sécurité
        if ($operation->statut_courant !== 'approuvee') {
            return back()->with('error', "Cette opération n'est pas encore approuvée.");
        }

        // Seul le demandeur peut envoyer cet email
        $isRequester = ($operation->user_id === $user->id || $operation->demandeur_email === $user->email);

        if (!$isRequester) {
            return back()->with('error', "Seul le demandeur est autorisé à envoyer cet email pour exécution.");
        }

        try {
            $operation->load(['typeOperation', 'operationalService', 'client']);
            Mail::to($request->email)->send(new \App\Mail\OperationExecutionMail($operation, $request->message));

            // Enregistrer l'action dans l'historique
            \App\Models\OperationStatusLog::create([
                'operation_id' => $operation->id,
                'from_status' => 'approuvee',
                'to_status' => 'approuvee',
                'user_name' => $user->name,
                'user_id' => $user->id,
                'commentaire' => 'Email de Bon pour accord envoyé à : ' . $request->email . ($request->message ? ' | Message : ' . $request->message : ''),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', "L'email de bon pour accord a été envoyé avec succès à " . $request->email);
        } catch (\Exception $e) {
            Log::error("Erreur envoi mail exécution pour opération {$operation->id}: " . $e->getMessage());
            return back()->with('error', "Une erreur est survenue lors de l'envoi de l'email : " . $e->getMessage());
        }
    }
    /**
     * Marquer une opération comme payée (admin ou modérateur avec module trésorerie)
     */
    public function markAsPaid(Request $request, $operationId)
    {
        $user = Auth::user();
        $operation = Operation::findOrFail($operationId);

        // Mail spécifique de la comptabilité
        $comptaEmail = 'tossaviama@kenamservices.net';

        // Restriction STRICTE à l'email de la comptabilité
        if ($user->email !== $comptaEmail) {
            abort(403, "Seul le compte comptabilité ({$comptaEmail}) est autorisé à effectuer le paiement.");
        }

        if ($operation->is_paid) {
            return back()->with('info', 'Cette opération est déjà marquée comme payée.');
        }

        $operation->forceFill([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $user->id,
            'statut_courant' => 'payee',
        ])->save();

        // Log du paiement
        \App\Models\OperationStatusLog::create([
            'operation_id' => $operation->id,
            'from_status' => 'approuvee',
            'to_status' => 'payee',
            'user_name' => $user->name,
            'user_id' => $user->id,
            'commentaire' => 'Paiement effectué et validé par la comptabilité.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Opération payée avec succès.');
    }

    /**
     * Liste des opérations prêtes pour le paiement (Page /payer)
     */
    public function toPayOperations(Request $request)
    {
        $user = Auth::user();
        $comptaEmail = 'tossaviama@kenamservices.net';

        // Restriction STRICTE à l'email de la comptabilité
        if ($user->email !== $comptaEmail) {
            abort(403, "Accès réservé exclusivement au compte comptabilité ({$comptaEmail}).");
        }

        $query = Operation::where('statut_courant', 'approuvee')
            ->where('is_paid', false)
            ->with(['client', 'typeOperation', 'operationalService', 'initiateur']);

        $search = $request->input('q');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('demandeur_name', 'like', "%{$search}%");
            });
        }

        $operations = $query->latest()->paginate(20);
        $services = \App\Models\ServiceOperationnel::actif()->ordre()->get();

        return view('validations.to-pay', compact('operations', 'search', 'services'));
    }

    /**
     * Centralise l'envoi des mails de demande de validation avec vérification des conditions
     */
    private function sendValidationRequestMail(Operation $operation, $ccServices = '')
    {
        try {
            Log::info("Tentative d'envoi de mail de validation pour l'opération #{$operation->id}");

            // 1. Trouver l'étape actuellement "EN_COURS"
            $currentStep = \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('statut', 'EN_COURS')
                ->first();

            if (!$currentStep) {
                Log::warning("Échec envoi mail : Aucune étape 'EN_COURS' trouvée pour l'opération #{$operation->id}");
                return false;
            }

            // 2. Récupérer le service associé
            $service = ServiceOperationnel::find($currentStep->service_operationnel_id);
            if (!$service) {
                Log::error("Échec envoi mail : Service #" . $currentStep->service_operationnel_id . " introuvable.");
                return false;
            }

            // 3. Vérifier l'adresse email
            if (empty($service->email) || !filter_var($service->email, FILTER_VALIDATE_EMAIL)) {
                Log::warning("Échec envoi mail : L'adresse email du service '{$service->nom}' est invalide ou vide.");
                return false;
            }

            // 4. Préparer les CC
            $ccEmails = [];
            if ($ccServices) {
                $ccServiceIds = is_array($ccServices) ? $ccServices : explode(',', $ccServices);
                foreach ($ccServiceIds as $id) {
                    $sc = ServiceOperationnel::find(trim($id));
                    if ($sc && $sc->email && filter_var($sc->email, FILTER_VALIDATE_EMAIL)) {
                        $ccEmails[] = $sc->email;
                    }
                }
            }

            // 5. Générer l'URL et envoyer
            $url = request()->getSchemeAndHttpHost() . "/operations/{$operation->id}/validate";
            $mail = new SendEmail($operation, $url);

            if (!empty($ccEmails)) {
                $mail->cc(array_unique($ccEmails));
            }

            Mail::to($service->email)->send($mail);

            Log::info("Mail de validation envoyé avec succès à {$service->email} (Opération #{$operation->id})");
            return true;

        } catch (\Exception $e) {
            Log::error("Erreur technique lors de l'envoi du mail (Opération #{$operation->id}) : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Configure le circuit de validation d'une opération
     * ORDRE : Validateurs Optionnels (1,2,3) -> Destinataire Principal -> DG (> 250k) -> Compta
     */
    private function setupValidationChain(Operation $operation, $destinatairePrincipalId, array $customValidators = [])
    {
        // Vérifier si des étapes de validation existent déjà pour cette opération
        $exists = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->exists();

        if ($exists) {
            return;
        }

        $steps = [];

        // 1. Ajouter les validateurs optionnels choisis par l'utilisateur
        foreach ($customValidators as $vId) {
            if ($vId && !in_array($vId, $steps)) {
                $steps[] = $vId;
            }
        }

        // 2. Destinataire Principal (Responsable Opérationnel)
        if ($destinatairePrincipalId && !in_array($destinatairePrincipalId, $steps)) {
            $steps[] = $destinatairePrincipalId;
        }

        // 3. Direction Générale (Hautes Dépenses) UNIQUEMENT si montant > 250 000 FCFA
        if ($operation->montant > 250000) {
            $dgEmail = config('app.dg_email', 'f.tourefatim@kenamsholding.net');
            $dgService = \DB::table('services_operationnels')
                ->where('email', $dgEmail)
                ->first();
            if ($dgService && !in_array($dgService->id, $steps)) {
                $steps[] = $dgService->id;
            }
        }

        // 4. Comptabilité (Finances) systématiquement si montant > 0 FCFA
        if ($operation->montant > 0) {
            $comptaService = \DB::table('services_operationnels')
                ->where('email', 'tossaviama@kenamservices.net')
                ->first();
            if ($comptaService && !in_array($comptaService->id, $steps)) {
                $steps[] = $comptaService->id;
            }
        }

        // Insertion des étapes dans la base de données
        foreach ($steps as $index => $serviceId) {
            \DB::table('operation_service_validation')->insert([
                'operation_id' => $operation->id,
                'service_operationnel_id' => $serviceId,
                'ordre_validation' => $index + 1,
                'statut' => ($index === 0) ? 'EN_COURS' : 'EN_ATTENTE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
