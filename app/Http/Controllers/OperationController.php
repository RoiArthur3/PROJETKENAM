<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Operation;
use App\Models\OperationStatusLog;
use App\Models\TypeOperation;
use App\Models\ServiceOperationnel;
use App\Models\OperationServiceValidation;
use App\Models\Vehicule;
use App\Models\Fournisseur;
use App\Models\User;
use App\Services\OperationCreationService;
use App\Services\OperationValidationService;
use App\Services\OperationNotificationService;
use App\Services\OperationWorkflowService;
use App\Services\SMSStatisticsService;
use App\Services\EmailStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OperationController extends Controller
{
    private OperationCreationService $operationCreationService;
    private OperationValidationService $validationService;
    private OperationNotificationService $notificationService;
    private OperationWorkflowService $workflowService;
    private SMSStatisticsService $smsStatisticsService;
    private EmailStatisticsService $emailStatisticsService;

    public function __construct()
    {
        $this->operationCreationService = app(OperationCreationService::class);
        $this->validationService = app(OperationValidationService::class);
        $this->notificationService = app(OperationNotificationService::class);
        $this->workflowService = app(OperationWorkflowService::class);
        $this->smsStatisticsService = app(SMSStatisticsService::class);
        $this->emailStatisticsService = app(EmailStatisticsService::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();
        $this->ensureModuleAccess('operations');
        $canCreateOperation = $user !== null;

        // Récupérer les opérations selon le rôle et permissions
        $operations = Operation::with(['typeOperation', 'operationalService', 'user'])
            ->when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                // Les agents voient seulement leurs opérations
                $query->where('user_id', $user->id);
            })
            ->when($user->role === 'manager', function($query) use ($user) {
                // Les managers voient les opérations de leur service
                $query->where('operational_service_id', $user->service_id);
            })
            ->when(in_array($user->role, ['admin', 'superadmin']), function($query) {
                // Les admins voient tout
                // Pas de filtre
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('operations.index', compact('operations', 'user', 'canCreateOperation'));
    }

    /**
     * Dashboard des opérations pour agents et modérateurs
     */
    public function dashboard()
    {
        /** @var User|null $user */
        $user = Auth::user();
        $this->ensureModuleAccess('operations');

        // Statistiques selon le rôle
        $stats = [
            'total_operations' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
            'en_attente_validation' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereIn('statut_courant', ['pending_validation', 'en_validation'])->count(),
            'approuvees' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereIn('statut_courant', ['approuvee', 'Approuvé_en_attente_paiement'])->count(),
            'terminees' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereIn('statut_courant', ['terminee', 'payee'])->count(),
            'rejetees' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->where('statut_courant', 'rejetee')->count(),
            'decaissement_total' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereNotNull('montant')->sum('montant') ?? 0,
            'operations_en_cours' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereIn('statut_courant', ['en_cours', 'en_execution'])->count(),
            'operations_terminees' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereIn('statut_courant', ['terminee', 'payee'])->count(),
            'operations_en_retard' => Operation::when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->where('echeance', '<', now())
                ->whereNotIn('statut_courant', ['terminee', 'payee', 'rejetee'])
                ->count(),
        ];

        // Récupérer les opérations récentes selon le rôle
        $recentOperations = Operation::with(['typeOperation', 'operationalService'])
            ->when(in_array($user->role, ['agent', 'user'], true), function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Statistiques SMS et Email vides pour éviter les erreurs
        $smsStats = [
            'today' => ['count' => 0, 'success_rate' => 0],
            'week' => ['count' => 0, 'success_rate' => 0],
            'month' => ['count' => 0, 'success_rate' => 0],
            'total' => ['count' => 0, 'success_rate' => 0]
        ];

        $emailStats = [
            'today' => ['count' => 0, 'success_rate' => 0],
            'week' => ['count' => 0, 'success_rate' => 0],
            'month' => ['count' => 0, 'success_rate' => 0],
            'total' => ['count' => 0, 'success_rate' => 0],
            'by_type' => [],
            'recent' => [],
            'trend' => []
        ];

        return view('operations.dashboard', compact('stats', 'recentOperations', 'user', 'smsStats', 'emailStats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->ensureModuleAccess('operations');

        $services = ServiceOperationnel::orderBy('nom')->get();
        $operationalServices = $services; // Pour la compatibilité avec la vue

        if (Schema::hasTable('types_operations')) {
            $types = TypeOperation::orderBy('libelle')->get();
        } elseif (Schema::hasTable('operation_types')) {
            $types = DB::table('operation_types')
                ->select([
                    'id',
                    DB::raw('name as libelle'),
                    'code',
                    DB::raw('active as actif'),
                ])
                ->orderBy('name')
                ->get();
        } else {
            $types = collect([]);
        }

        $engins = Schema::hasTable('vehicules')
            ? Vehicule::query()->orderBy('immatriculation')->get()
            : collect([]);

        $fournisseurs = Schema::hasTable('fournisseurs')
            ? Fournisseur::query()->orderBy('raison_sociale')->get()
            : collect([]);

        return view('operations.create', compact('services', 'operationalServices', 'types', 'engins', 'fournisseurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->ensureModuleAccess('operations');

        $typeOperationExistsRule = 'nullable';
        if (Schema::hasTable('types_operations')) {
            $typeOperationExistsRule = 'required|integer|exists:types_operations,id';
        } elseif (Schema::hasTable('operation_types')) {
            $typeOperationExistsRule = 'required|integer|exists:operation_types,id';
        }

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:50000',
            'montant' => 'nullable|numeric|min:0',
            'priorite' => 'required|in:urgente,haute,moyenne,basse',
            'echeance' => 'nullable|date',
            'destinataire_principal' => 'required|integer|exists:services_operationnels,id',
            'type_operation_id' => $typeOperationExistsRule,
            'engin_id' => 'nullable|integer|exists:vehicules,id',
            'fournisseur_id' => 'nullable|integer|exists:fournisseurs,id',
            'fichiers' => 'required|array|min:1',
            'fichiers.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        try {
            $user = Auth::user();
            $isDraft = $request->has('save_as_draft');

            $engin = Vehicule::find($validated['engin_id']);
            $fournisseur = Fournisseur::find($validated['fournisseur_id']);

            $description = trim((string) ($validated['description'] ?? ''));
            $metaLines = [
                'Engin: ' . ($engin?->immatriculation ?? ('#' . $validated['engin_id'])),
                'Fournisseur: ' . ($fournisseur?->raison_sociale ?? ('#' . $validated['fournisseur_id'])),
            ];
            $validated['description'] = trim($description . "\n\n" . implode("\n", $metaLines));

            $result = $this->operationCreationService->createFromMainForm($validated, $user, [
                'is_draft' => $isDraft,
                'files' => $request->file('fichiers', []),
                'custom_validators' => [
                    $request->input('validateur_1'),
                    $request->input('validateur_2'),
                    $request->input('validateur_3'),
                ],
                'cc_services' => $request->input('services_cc', ''),
            ]);

            $notificationWarning = $result['warning'] ?? null;

            $redirect = redirect()->route('operations.index')
                ->with('success', $isDraft ? 'Opération enregistrée comme brouillon.' : 'Opération créée avec succès');

            if (isset($notificationWarning)) {
                $redirect->with('warning', $notificationWarning);
            }

            return $redirect;

        } catch (\Throwable $e) {
            Log::error('Erreur lors de la création de l\'opération', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'opération: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($operation)
    {
        $this->ensureModuleAccess('operations');
        $operation = $this->resolveOperationOrFail($operation, 'show');
        $this->authorizeOperationAccess($operation);

        // Charger les relations nécessaires
        $operation->load([
            'typeOperation',
            'operationalService',
            'fichiers',
            'user',
            'services',
            'client',
            'paidBy'
        ]);

        $user = Auth::user();

        $validationSteps = DB::table('operation_service_validation')
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->where('operation_service_validation.operation_id', $operation->id)
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        $statusHistory = \App\Models\OperationStatusLog::where('operation_id', $operation->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $steps = $validationSteps;
        $currentStep = $validationSteps->first(function ($step) {
            return in_array($step->statut, ['EN_COURS', 'pending'], true);
        });

        return view('operations.show', compact('operation', 'user', 'validationSteps', 'statusHistory', 'steps', 'currentStep'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($operation)
    {
        $this->ensureModuleAccess('operations');
        $operation = $this->resolveOperationOrFail($operation, 'edit');
        $this->authorizeOperationAccess($operation);
        /** @var User|null $user */
        $user = Auth::user();

        // Règle métier: seul le superadmin peut modifier une opération
        if (strtolower((string) ($user->role ?? '')) !== 'superadmin') {
            abort(403, 'Accès non autorisé à cette opération');
        }

        $services = \App\Models\ServiceOperationnel::orderBy('nom')->get();
        $operationalServices = $services; // Pour la compatibilité avec la vue
        $types = \App\Models\TypeOperation::orderBy('libelle')->get();

        // Récupérer les services déjà sélectionnés pour cette opération
        $defaultChain = $operation->services->pluck('service_name')->toArray();

        return view('operations.edit', compact('operation', 'services', 'operationalServices', 'types', 'defaultChain'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $operation)
    {
        $this->ensureModuleAccess('operations');
        $operation = $this->resolveOperationOrFail($operation, 'update');
        $this->authorizeOperationAccess($operation);
        /** @var User|null $user */
        $user = Auth::user();

        // Règle métier: seul le superadmin peut modifier une opération
        if (strtolower((string) ($user->role ?? '')) !== 'superadmin') {
            abort(403, 'Accès non autorisé à cette opération');
        }

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:50000',
            'montant' => 'nullable|numeric|min:0',
            'priorite' => 'required|in:urgente,haute,moyenne,basse',
            'echeance' => 'nullable|date',
            'destinataire_principal' => 'required|integer|exists:services_operationnels,id',
            'type_operation_id' => 'required|integer|exists:types_operations,id',
            'fichiers.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        try {
            $user = Auth::user();

            // Statuts finaux (validé, approuvé, payé, rejeté...)
            $finalStatus = ['approuvee', 'approuve', 'Approuvé_en_attente_paiement', 'bon_pour_accord', 'pret_execution', 'payee', 'payée', 'rejetee', 'rejetée', 'rejected'];
            // Statuts "en attente" à bloquer si déjà final
            $pendingStatus = ['pending_validation', 'en_attente', 'EN_ATTENTE', 'en_validation', 'en_validation_responsable', 'en_validation_dg'];

            // Empêcher le retour à un statut en attente si déjà validé/approuvé/rejeté
            if (in_array($operation->statut_courant, $finalStatus)) {
                if (isset($validated['statut_courant']) && in_array($validated['statut_courant'], $pendingStatus)) {
                    return back()->with('error', 'Impossible de repasser une opération validée ou rejetée en attente.');
                }
            }

            $operation->update([
                'titre' => $validated['titre'],
                'description' => $validated['description'] ?? '',
                'montant' => (float) $validated['montant'],
                'priorite' => $validated['priorite'],
                'echeance' => $validated['echeance'],
                'operational_service_id' => $validated['destinataire_principal'],
                'type_operation_id' => $validated['type_operation_id'],
                // statut_courant déjà géré ci-dessus
            ]);

            // Gestion des pièces jointes (ajout) lors de la mise à jour
            if ($request->hasFile('fichiers')) {
                $fichiers = [];
                foreach ($request->file('fichiers') as $file) {
                    $path = $file->store('operations/' . $operation->id . '/fichiers', 'public');
                    $fichiers[] = [
                        'operation_id' => $operation->id,
                        'nom'          => $file->getClientOriginalName(),
                        'chemin'       => $path,
                        'type_mime'    => $file->getMimeType(),
                        'taille'       => $file->getSize(),
                        'extension'    => $file->getClientOriginalExtension(),
                        'uploaded_by'  => $user?->id,
                    ];
                }

                $operation->fichiers()->createMany($fichiers);
            }

            return redirect()->route('operations.show', ['operationId' => $operation->id])
                ->with('success', 'Opération mise à jour avec succès.');

        } catch (\Throwable $e) {
            return redirect()->route('operations.edit', ['operationId' => $operation->id])
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($operation)
    {
        $this->ensureModuleAccess('operations');
        $operation = $this->resolveOperationOrFail($operation, 'destroy');
        $this->authorizeOperationAccess($operation);
        /** @var User|null $user */
        $user = Auth::user();

        // Règles de suppression - TOUS les comptes peuvent supprimer selon conditions
        $canDelete = false;

        // Admins et superadmins peuvent supprimer n'importe quelle opération
        if (in_array($user->role, ['admin', 'superadmin'])) {
            $canDelete = true;
        }
        // Manager peut supprimer les opérations de son service
        elseif ($user->role === 'manager' && $operation->operational_service_id == $user->service_id) {
            $canDelete = true;
        }
        // Agent peut supprimer ses propres opérations (tous statuts)
        elseif (in_array($user->role, ['agent', 'user'], true) && $operation->user_id === $user->id) {
            $canDelete = true;
        }
        // Manager général peut supprimer n'importe quelle opération
        elseif ($user->role === 'manager_general') {
            $canDelete = true;
        }
        // Directeur peut supprimer n'importe quelle opération
        elseif ($user->role === 'directeur') {
            $canDelete = true;
        }
        // DG peut supprimer n'importe quelle opération
        elseif ($user->role === 'dg') {
            $canDelete = true;
        }
        // Modérateur avec permissions
        elseif (in_array($user->role, ['moderator', 'moderateur']) && $user->canAccessModule('operations')) {
            $canDelete = true;
        }
        // Comptable peut supprimer les opérations financières
        elseif ($user->role === 'comptable') {
            $canDelete = true;
        }
        // Trésorier peut supprimer les opérations payées
        elseif ($user->role === 'tresorier') {
            $canDelete = true;
        }
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
     * Page de validation spécifique pour une opération
     * Route: GET|POST /operations/{operation}/validate → operations.validate
     */
    public function validateOperation(Request $request, $operationId)
    {
        try {
            $this->ensureModuleAccess('validations');

            // Récupérer l'opération
            $operation = Operation::find($operationId);
            if (!$operation) {
                return redirect()->route('operations.index')->with('error', 'Opération non trouvée.');
            }

            $this->authorizeOperationAccess($operation, 'validations');

            // ─── POST : traitement du formulaire de validation ───────────────────
            if ($request->isMethod('post')) {
                /** @var User|null $user */
                $user = Auth::user();
                if (!$user) {
                    return redirect()->route('login')->with('error', 'Vous devez être connecté.');
                }

                $hasExistingAttachment = $operation->fichiers()->exists();
                $attachmentRule = $hasExistingAttachment ? 'nullable' : 'required';

                $validated = $request->validate([
                    'action' => 'required|in:approve,reject',
                    'step' => 'nullable|integer|min:1',
                    'commentaire' => 'nullable|string|max:500',
                    'force_approve' => 'nullable|in:0,1',
                    'caisse_email' => 'nullable|email',
                    'caisse_id' => 'nullable|integer|exists:caisses,id',
                    'validation_attachment' => $attachmentRule . '|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv|max:10240',
                ]);

                if ($validated['action'] === 'approve' && !$hasExistingAttachment && !$request->hasFile('validation_attachment')) {
                    return redirect()->back()
                        ->withErrors(['validation_attachment' => 'Une pièce jointe est obligatoire avant validation de l\'opération.'])
                        ->withInput();
                }

                if ($request->hasFile('validation_attachment')) {
                    $file = $request->file('validation_attachment');
                    $path = $file->store('operations/' . $operation->id . '/validations', 'public');

                    $operation->fichiers()->create([
                        'nom' => $file->getClientOriginalName(),
                        'chemin' => $path,
                        'type_mime' => $file->getMimeType() ?: 'application/octet-stream',
                        'taille' => $file->getSize() ?: 0,
                        'extension' => strtolower($file->getClientOriginalExtension() ?: ''),
                        'uploaded_by' => $user->id,
                        'description' => 'Pièce jointe ajoutée lors de la validation de l\'opération',
                    ]);
                }

                $validationSteps = DB::table('operation_service_validation')
                    ->where('operation_id', $operation->id)
                    ->orderBy('ordre_validation')
                    ->get();

                if ($validationSteps->isEmpty()) {
                    return redirect()->route('operations.show', ['operationId' => $operation->id])
                        ->with('error', 'Aucun circuit de validation n\'est configuré pour cette opération.');
                }

                $currentStep = $validationSteps->firstWhere('statut', 'EN_COURS') ?? $validationSteps->first();
                $step = (int) ($validated['step'] ?? ($currentStep->ordre_validation ?? 1));

                if ($validated['action'] === 'reject') {
                    $this->workflowService->rejectOperation($operation, $step, $validated, $user);

                    return redirect()->route('validations.rejected')
                        ->with('success', 'Opération rejetée avec succès.');
                }

                $newStatus = $this->workflowService->approveStep($operation, $step, $validated, $user);

                $message = in_array($newStatus, ['Approuvé_en_attente_paiement', 'approuvee'], true)
                    ? 'Opération approuvée. Elle est maintenant en attente de traitement par le destinataire final.'
                    : 'Opération validée et transmise au validateur suivant.';

                return redirect()->route('operations.show', ['operationId' => $operation->id])
                    ->with('success', $message);
            }

            // ─── GET : affichage de la page de validation ─────────────────────────
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            // Charger les relations nécessaires
            $operation->load(['typeOperation', 'operationalService', 'fichiers', 'user']);

            $validationSteps = DB::table('operation_service_validation')
                ->leftJoin('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
                ->leftJoin('users as validateurs', 'operation_service_validation.validateur_id', '=', 'validateurs.id')
                ->where('operation_service_validation.operation_id', $operation->id)
                ->orderBy('operation_service_validation.ordre_validation')
                ->get([
                    'operation_service_validation.*',
                    'services_operationnels.nom as service_name',
                    'services_operationnels.email as service_email',
                    'validateurs.name as validateur_name',
                ]);

            $currentStep = $validationSteps->first(function ($validationStep) {
                return ($validationStep->statut ?? null) === 'EN_COURS';
            }) ?? $validationSteps->first();

            $step = $currentStep->ordre_validation ?? 1;
            $services = ServiceOperationnel::query()->orderBy('nom')->get(['id', 'nom', 'email']);

            return view('operations.validate', compact('operation', 'user', 'validationSteps', 'currentStep', 'step', 'services'));

        } catch (\Exception $e) {
            Log::error('validateOperation error: ' . $e->getMessage());
            return redirect()->route('operations.index')->with('error', 'Erreur lors du chargement de la validation.');
        }
    }

    /**
     * Opérations payées.
     */
    public function paidOperations(Request $request)
    {
        $user = $this->ensureAnyModuleAccess(['validations', 'tresorerie']);
        $search = trim((string) $request->input('q', ''));
        $serviceId = (string) $request->input('service_id', '');

        $query = Operation::query()
            ->whereIn('statut_courant', ['payee', 'payée'])
            ->with(['client', 'typeOperation', 'operationalService', 'initiateur', 'paidBy', 'invoice', 'caisseExecutante']);

        if (!in_array($user->role, ['admin', 'superadmin'], true)) {
            $query->where(function ($builder) use ($user) {
                $builder->where('user_id', $user->id)
                    ->orWhere('demandeur_email', $user->email)
                    ->orWhere('demandeur_name', $user->name)
                    ->orWhere('paid_by', $user->id);

                if (!empty($user->email)) {
                    $builder->orWhere('caisse_email', $user->email);
                }

                if (!empty($user->service_id)) {
                    $builder->orWhere('caisse_executante_id', $user->service_id);
                }
            });
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('titre', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('numero_operation', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($serviceId !== '') {
            $query->where('operational_service_id', $serviceId);
        }

        $operations = $query->latest()->paginate(15);
        $services = ServiceOperationnel::actif()->ordre()->get();

        return view('validations.paid', compact('operations', 'search', 'serviceId', 'services'));
    }

    /**
     * Opérations approuvées en attente de désignation de caisse.
     */
    public function toPayOperations(Request $request)
    {
        $this->ensureAnyModuleAccess(['validations']);
        $search = trim((string) $request->input('q', ''));

        $query = Operation::query()
            ->whereIn('statut_courant', ['approuvee', 'Approuvé_en_attente_paiement', 'bon_pour_accord'])
            ->where(function ($builder) {
                $builder->whereNull('is_paid')->orWhere('is_paid', false);
            })
            ->with(['client', 'typeOperation', 'operationalService', 'initiateur', 'caisseExecutante']);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('titre', 'like', "%{$search}%")
                    ->orWhere('numero_operation', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('demandeur_name', 'like', "%{$search}%");
            });
        }

        $totalEnAttente = (clone $query)->sum('montant');
        $operations = $query->latest()->paginate(20);
        $caisses = Caisse::query()
            ->with('responsable:id,email')
            ->where('est_active', true)
            ->orderBy('nom')
            ->get();

        return view('validations.to-pay', compact('operations', 'search', 'caisses', 'totalEnAttente'));
    }

    /**
     * Opérations désignées à une caisse pour exécution.
     */
    public function caisseExecution(Request $request)
    {
        $user = $this->ensureAnyModuleAccess(['validations', 'tresorerie']);
        $search = trim((string) $request->input('q', ''));

        $query = Operation::query()
            ->where(function ($builder) {
                $builder->whereIn('statut_courant', ['pret_execution', 'bon_pour_accord'])
                    ->orWhere(function ($nested) {
                        $nested->where('statut_courant', 'Approuvé_en_attente_paiement')
                            ->where(function ($assigned) {
                                $assigned->whereNotNull('caisse_executante_id')
                                    ->orWhereNotNull('caisse_email');
                            });
                    });
            })
            ->where(function ($builder) {
                $builder->whereNull('is_paid')->orWhere('is_paid', false);
            })
            ->with(['client', 'typeOperation', 'operationalService', 'initiateur', 'caisseExecutante']);

        if (!in_array($user->role, ['admin', 'superadmin'], true)) {
            $query->where(function ($builder) use ($user) {
                if (!empty($user->email)) {
                    $builder->where('caisse_email', $user->email);
                }

                if (!empty($user->service_id)) {
                    $builder->orWhere('caisse_executante_id', $user->service_id);
                }
            });
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('titre', 'like', "%{$search}%")
                    ->orWhere('numero_operation', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('demandeur_name', 'like', "%{$search}%");
            });
        }

        $totalEnAttente = (clone $query)->sum('montant');
        $operations = $query->latest()->paginate(20);

        return view('validations.caisse-execution', compact('operations', 'search', 'totalEnAttente'));
    }

    /**
     * Émet le bon pour exécution vers une caisse.
     */
    public function emitBonPourExecution(Request $request, $operationId)
    {
        $operation = $this->resolveOperationOrFail($operationId, 'emitBonPourExecution');

        return $this->dispatchOperationToCaisse($request, $operation);
    }

    /**
     * Marque une opération comme payée ou, si une caisse est fournie, émet le bon pour exécution.
     */
    public function markAsPaid(Request $request, $operationId)
    {
        $operation = $this->resolveOperationOrFail($operationId, 'markAsPaid');

        if ($this->isExecutionDispatchRequest($request)) {
            return $this->dispatchOperationToCaisse($request, $operation);
        }

        $user = $this->ensureAnyModuleAccess(['validations', 'tresorerie']);

        if ($operation->is_paid) {
            return back()->with('info', 'Cette opération est déjà marquée comme payée.');
        }

        $canPay = in_array($user->role, ['admin', 'superadmin'], true)
            || $user->canAccessModule('validations')
            || (!empty($operation->caisse_email) && !empty($user->email) && strcasecmp((string) $operation->caisse_email, (string) $user->email) === 0)
            || (!empty($operation->caisse_executante_id) && !empty($user->service_id) && (int) $operation->caisse_executante_id === (int) $user->service_id);

        if (!$canPay) {
            abort(403, 'Vous n\'êtes pas autorisé à exécuter ce paiement.');
        }

        if (!in_array($operation->statut_courant, ['pret_execution', 'bon_pour_accord', 'Approuvé_en_attente_paiement', 'approuvee'], true)) {
            return back()->with('error', 'Cette opération n\'est pas dans un état compatible avec le paiement.');
        }

        $validated = $request->validate([
            'mode_paiement' => 'nullable|in:especes,cheque,virement,mobile_money,autre',
            'payment_reference' => 'nullable|string|max:100',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $this->workflowService->markAsPaid($operation, [
            'mode_paiement' => $validated['mode_paiement'] ?? ($operation->mode_paiement ?: 'autre'),
            'payment_reference' => $validated['payment_reference'] ?? null,
            'commentaire' => $validated['commentaire'] ?? null,
        ], $user);
        $user = Auth::user();
        $this->ensureAnyModuleAccess(['validations']);
        $search = trim((string) $request->input('q', ''));
        $serviceId = (string) $request->input('service_id', '');
        $montantMin = $request->input('montant_min');
        $montantMax = $request->input('montant_max');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        $query = Operation::query()
            ->whereIn('statut_courant', ['pending_validation', 'en_validation', 'EN_ATTENTE'])
            ->with(['client', 'typeOperation', 'operationalService', 'initiateur']);

        if (!in_array($user->role, ['admin', 'superadmin'], true)) {
            $query->where(function ($builder) use ($user) {
                $builder->where('user_id', $user->id)
                    ->orWhere('demandeur_email', $user->email)
                    ->orWhere('demandeur_name', $user->name);

                if (!empty($user->email)) {
                    $builder->orWhere('caisse_email', $user->email);
                }

                if (!empty($user->service_id)) {
                    $builder->orWhere('caisse_executante_id', $user->service_id);
                }
            });
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('titre', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('numero_operation', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($serviceId !== '') {
            $query->where('operational_service_id', $serviceId);
        }

        if ($montantMin !== null && $montantMin !== '') {
            $query->where('montant', '>=', $montantMin);
        }
        if ($montantMax !== null && $montantMax !== '') {
            $query->where('montant', '<=', $montantMax);
        }
        if ($dateDebut !== null && $dateDebut !== '') {
            $query->whereDate('created_at', '>=', $dateDebut);
        }
        if ($dateFin !== null && $dateFin !== '') {
            $query->whereDate('created_at', '<=', $dateFin);
        }

        $operations = $query->latest()->paginate(15);
        $services = ServiceOperationnel::actif()->ordre()->get();

        return view('validations.pending', compact('operations', 'search', 'serviceId', 'services', 'montantMin', 'montantMax', 'dateDebut', 'dateFin'));
        // ...existing code...
    }

    /**
     * Lien public sécurisé pour confirmer un paiement depuis un email.
     */
    public function markAsPaidByLink(Request $request, $operationId)
    {
        $operation = $this->resolveOperationOrFail($operationId, 'markAsPaidByLink');

        if (!$request->hasValidSignature()) {
            abort(403, 'Lien de paiement invalide ou expiré.');
        }

        if ($operation->is_paid) {
            return response('Cette opération a déjà été marquée comme payée.', 200);
        }

        $previousStatus = $operation->statut_courant;
        $operation->forceFill([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => null,
            'statut_courant' => 'payee',
            'mode_paiement' => $operation->mode_paiement ?: 'autre',
        ])->save();

        OperationStatusLog::create([
            'operation_id' => $operation->id,
            'from_status' => $previousStatus,
            'to_status' => 'payee',
            'user_name' => 'Lien sécurisé',
            'user_id' => null,
            'commentaire' => 'Paiement confirmé depuis un lien sécurisé.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->notificationService->notifyPayment($operation);

        return response('Paiement confirmé avec succès.', 200);
    }

    /**
     * Méthodes utilitaires
     */
    private function resolveOperationOrFail($operation, string $action)
    {
        if (is_numeric($operation)) {
            $operation = Operation::find($operation);
        }

        if (!$operation) {
            abort(404, 'Opération non trouvée');
        }

        return $operation;
    }

    private function ensureModuleAccess(string $module): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->canAccessModule($module)) {
            abort(403, 'Accès non autorisé à ce module');
        }
    }

    private function ensureAnyModuleAccess(array $modules): User
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Accès non autorisé à ce module');
        }

        if (in_array($user->role, ['admin', 'superadmin'], true)) {
            return $user;
        }

        foreach ($modules as $module) {
            if ($user->canAccessModule($module)) {
                return $user;
            }
        }

        abort(403, 'Accès non autorisé à ce module');
    }

    private function isExecutionDispatchRequest(Request $request): bool
    {
        return $request->filled('caisse_id')
            || $request->filled('caisse_email')
            || $request->filled('caisse_email_manual');
    }

    private function dispatchOperationToCaisse(Request $request, Operation $operation)
    {
        $user = $this->ensureAnyModuleAccess(['validations']);

        if ($operation->is_paid) {
            return back()->with('info', 'Cette opération est déjà marquée comme payée.');
        }

        if (!in_array($operation->statut_courant, ['approuvee', 'Approuvé_en_attente_paiement', 'bon_pour_accord', 'pret_execution'], true)) {
            return back()->with('error', 'Cette opération n\'est pas dans un état compatible avec un bon pour exécution.');
        }

        $validated = $request->validate([
            'caisse_id' => 'nullable|integer|exists:caisses,id',
            'caisse_email' => 'nullable|email',
            'caisse_email_manual' => 'nullable|email',
            'mode_paiement' => 'required|in:especes,cheque,virement,mobile_money,autre',
            'payment_reference' => 'nullable|string|max:100',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $caisse = !empty($validated['caisse_id'])
            ? Caisse::with('responsable:id,email')->find($validated['caisse_id'])
            : null;
        $caisseEmail = $validated['caisse_email']
            ?? $validated['caisse_email_manual']
            ?? $caisse?->email
            ?? $caisse?->responsable?->email
            ?? null;

        if (!$caisseEmail || !filter_var($caisseEmail, FILTER_VALIDATE_EMAIL)) {
            return back()->withInput()->with('error', 'Veuillez renseigner une adresse email valide pour la caisse.');
        }

        $previousStatus = $operation->statut_courant;

        $operation->forceFill([
            'caisse_executante_id' => $validated['caisse_id'] ?? $operation->caisse_executante_id,
            'caisse_email' => $caisseEmail,
            'bon_pour_accord_at' => now(),
            'bon_pour_accord_by' => $user->id,
            'mode_paiement' => $validated['mode_paiement'],
            'payment_reference' => $validated['payment_reference'] ?? $operation->payment_reference,
            'statut_courant' => 'pret_execution',
        ])->save();

        $this->notificationService->notifyCaisse($operation, $caisseEmail, $validated['commentaire'] ?? '');

        OperationStatusLog::create([
            'operation_id' => $operation->id,
            'from_status' => $previousStatus,
            'to_status' => 'pret_execution',
            'user_name' => $user->name,
            'user_id' => $user->id,
            'commentaire' => 'Bon pour exécution émis vers la caisse : ' . ($caisse?->nom ?? $caisseEmail)
                . (!empty($validated['commentaire']) ? ' | ' . $validated['commentaire'] : ''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('validations.caisse-execution')
            ->with('success', 'Le bon pour exécution a été envoyé à la caisse sélectionnée.');
    }

    private function authorizeOperationAccess(Operation $operation, string $module = 'operations'): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Accès non autorisé');
        }

        if (in_array($user->role, ['admin', 'superadmin'], true)) {
            return;
        }

        if (in_array($user->role, ['agent', 'user'], true)) {
            if ((int) $operation->user_id !== (int) $user->id) {
                abort(403, 'Vous ne pouvez consulter que vos propres opérations');
            }

            return;
        }

        if (in_array($user->role, ['moderator', 'moderateur'], true) && !$user->canAccessModule($module)) {
            abort(403, 'Accès non autorisé à cette opération');
        }
    }

    private function generateOperationNumber(): string
    {
        $prefix = 'OP-' . date('Y');
        $lastNumber = Operation::where('numero_operation', 'like', $prefix . '%')
            ->orderBy('numero_operation', 'desc')
            ->value('numero_operation');

        if ($lastNumber) {
            $sequence = (int) substr($lastNumber, -4) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Télécharger l'opération au format PDF
     */
    public function downloadPDF($operationId)
    {
        try {
            $this->ensureModuleAccess('operations');
            $operation = $this->resolveOperationOrFail($operationId, 'download-pdf');
            $this->authorizeOperationAccess($operation);

            // Charger la relation avec les fichiers
            $operation->load('fichiers');

            // Si l'opération n'a pas de fichiers, retourner une erreur
            if ($operation->fichiers->isEmpty()) {
                return redirect()->route('operations.show', $operation->id)
                    ->with('warning', 'Cette opération n\'a pas de fichiers à télécharger.');
            }

            // Si un seul fichier, le télécharger directement
            if ($operation->fichiers->count() === 1) {
                $fichier = $operation->fichiers->first();
                return Storage::download($fichier->chemin, $fichier->nom);
            }

            // Si plusieurs fichiers, les zipper
            $zip = new \ZipArchive();
            $zipPath = storage_path('temp/operation_' . $operation->id . '_' . time() . '.zip');
            
            if (!is_dir(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($operation->fichiers as $fichier) {
                    $filePath = storage_path('app/' . $fichier->chemin);
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, $fichier->nom);
                    }
                }
                $zip->close();

                // Télécharger le zip
                $response = response()->download($zipPath, 'operation_' . $operation->numero_operation . '.zip');
                
                // Supprimer le fichier zip après téléchargement
                register_shutdown_function(function() use ($zipPath) {
                    if (file_exists($zipPath)) {
                        unlink($zipPath);
                    }
                });

                return $response;
            }

            return redirect()->route('operations.show', $operation->id)
                ->with('error', 'Erreur lors de la création de l\'archive ZIP.');
        } catch (\Exception $e) {
            Log::error('Error downloading operation PDF:', ['error' => $e->getMessage()]);
            return redirect()->route('operations.show', $operationId ?? 0)
                ->with('error', 'Erreur lors du téléchargement: ' . $e->getMessage());
        }
    }
}
