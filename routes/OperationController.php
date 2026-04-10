<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\ServiceOperationnel;
use App\Models\TypeOperation;
use App\Models\OperationStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// ...existing code...
use Illuminate\Support\Facades\Log;
use App\Mail\ValidationStepNotification;
use App\Mail\OperationApprovedNotification;
use App\Mail\OperationProfessionalMail;
use App\Mail\SendEmail;

class OperationController extends Controller
{
    // ...existing code...

    /**
     * Lien sécurisé pour marquer une opération comme payée (depuis email)
     */
    public function markAsPaidByLink(Request $request, $operationId)
    {
        $operation = Operation::findOrFail($operationId);
        // Vérification du token signé
        if (! $request->hasValidSignature()) {
            abort(403, 'Lien de paiement invalide ou expiré.');
        }
        // Si déjà payée, on ne refait rien
        if ($operation->is_paid) {
            return view('operations.paid-already', ['operation' => $operation]);
        }
        // Marquer comme payée
        $operation->forceFill([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => null, // On ne connaît pas l'utilisateur (lien public)
            'statut_courant' => 'payee',
        ])->save();
        // Notifier le demandeur
        $demandeur = $operation->demandeur_email ? (object)['email' => $operation->demandeur_email, 'name' => $operation->demandeur_name] : null;
        if ($demandeur && filter_var($demandeur->email, FILTER_VALIDATE_EMAIL)) {
            \Mail::to($demandeur->email)->send(new \App\Mail\OperationPaidNotification($operation, $demandeur));
        }
        return view('operations.paid-success', ['operation' => $operation]);
    }
use Illuminate\Support\Facades\Log;
use App\Mail\ValidationStepNotification;
use App\Mail\OperationApprovedNotification;
use App\Mail\OperationProfessionalMail;
use App\Mail\SendEmail;

class OperationController extends Controller
{
    /**
     * Vérifie si l'utilisateur a le droit d'accéder à une opération.
     * Admin/superadmin : accès total.
     * Autres : uniquement leurs opérations ou celles où ils sont validateurs.
     */
    private function canAccessOperation(Operation $operation, $user): bool
    {
        if (in_array($user->role, ['admin', 'superadmin'])) {
            return true;
        }

        // L'utilisateur est le créateur
        if ($operation->user_id == $user->id) {
            return true;
        }

        // L'utilisateur est validateur assigné
        $isValidator = DB::table('operation_service_validation')
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->where('operation_service_validation.operation_id', $operation->id)
            ->where(function ($q) use ($user) {
                $q->where('services_operationnels.email', $user->email);
                if ($user->service_id) {
                    $q->orWhere('operation_service_validation.service_operationnel_id', $user->service_id);
                }
            })
            ->exists();

        return $isValidator;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Operation::with(['client', 'typeOperation', 'operationalService', 'validations.serviceOperationnel']);

        // Admin/superadmin voient tout
        // Les autres voient : leurs propres opérations + celles où ils sont validateurs assignés
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->where(function($q) use ($user) {
                $q->where('operations.user_id', $user->id)
                  ->orWhereIn('operations.id', function($sub) use ($user) {
                      $sub->select('osv.operation_id')
                          ->from('operation_service_validation as osv')
                          ->join('services_operationnels as so', 'osv.service_operationnel_id', '=', 'so.id')
                          ->where(function($w) use ($user) {
                              $w->where('so.email', $user->email);
                              if ($user->service_id) {
                                  $w->orWhere('osv.service_operationnel_id', $user->service_id);
                              }
                          });
                  });
            });
        }

        // Recherche textuelle
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($sq) use ($search) {
                $sq->where('titre', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%")
                   ->orWhere('demandeur_name', 'like', "%{$search}%")
                   ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut_courant', $request->statut);
        }

        // Filtrage par priorité
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        $operations = $query->latest()->paginate(15)->appends($request->query());

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

    /**
     * Display the specified resource.
     */
    public function show(Operation $operation)
    {
        $user = Auth::user();

        if (!$this->canAccessOperation($operation, $user)) {
            return redirect()->route('operations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à consulter cette opération.');
        }

        // Récupérer les étapes de validation avec les informations des services
        $validationSteps = \DB::table('operation_service_validation')
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->where('operation_service_validation.operation_id', $operation->id)
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        // Récupérer les fichiers associés
        $fichiers = $operation->fichiers ?? collect([]);

        // Récupérer les logs de statut
        $statusLogs = \App\Models\OperationStatusLog::where('operation_id', $operation->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Créer la variable steps pour compatibilité avec la vue
        $steps = $validationSteps;

        // Trouver l'étape actuelle pour la vue show
        $currentStep = $validationSteps->where('statut', 'EN_COURS')->first();

        // Créer l'historique des statuts pour la vue
        $statusHistory = $statusLogs;

        return view('operations.show', compact('operation', 'user', 'validationSteps', 'fichiers', 'statusLogs', 'statusHistory', 'steps', 'currentStep'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operation $operation)
    {
        $user = Auth::user();

        if (!$this->canAccessOperation($operation, $user)) {
            return redirect()->route('operations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette opération.');
        }

        // Vérifier si l'opération peut être éditée (uniquement si pas encore validée)
        if (in_array($operation->statut_courant, ['approuvee', 'rejetee', 'terminee'])) {
            return redirect()->route('operations.index')
                ->with('error', 'Cette opération ne peut plus être modifiée car elle a déjà été validée (statut: ' . $operation->statut_courant . ')');
        }

        $types = TypeOperation::orderBy('libelle')->get();
        $services = ServiceOperationnel::whereNotNull('nom')->orderBy('nom')->get();

        return view('operations.edit', compact('operation', 'types', 'services', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Operation $operation)
    {
        $user = Auth::user();

        if (!$this->canAccessOperation($operation, $user)) {
            return redirect()->route('operations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette opération.');
        }

        // Vérifier si l'opération peut être éditée
        if (in_array($operation->statut_courant, ['approuvee', 'rejetee', 'terminee'])) {
            return redirect()->route('operations.index')
                ->with('error', 'Impossible de modifier cette opération car elle a déjà été validée. Veuillez contacter l\'administrateur pour plus d\'informations.');
        }

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'nullable|numeric|min:0',
            'priorite' => 'required|in:urgente,haute,moyenne,basse',
            'echeance' => 'nullable|date|after_or_equal:today',
            'destinataire_principal' => 'required|integer|exists:services_operationnels,id',
            'type_operation_id' => 'required|integer|exists:types_operations,id',
            'services_cc' => 'nullable|string',
            'fichiers.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        // Récupérer le type d'opération
        $typeOperation = TypeOperation::find($validated['type_operation_id']);

        // Mettre à jour l'opération
        $operation->update([
            'type' => $typeOperation ? $typeOperation->libelle : 'Standard',
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'montant' => $validated['montant'],
            'priorite' => $validated['priorite'],
            'echeance' => $validated['echeance'],
            'operational_service_id' => $validated['destinataire_principal'],
            'type_operation_id' => $validated['type_operation_id'],
        ]);

        // Gérer les fichiers
        if ($request->hasFile('fichiers')) {
            $fichiers = [];
            foreach ($request->file('fichiers') as $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('operations', $filename, 'public');
                    $fichiers[] = [
                        'nom' => $filename,
                        'chemin' => $path,
                        'taille' => $file->getSize(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            $operation->fichiers()->createMany($fichiers);
        }

        return redirect()->route('operations.index')
            ->with('success', 'Opération mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operation $operation)
    {
        $user = Auth::user();

        if (!$this->canAccessOperation($operation, $user)) {
            return redirect()->route('operations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette opération.');
        }

        // Vérifier si l'opération peut être supprimée (uniquement si pas encore validée)
        if (in_array($operation->statut_courant, ['approuvee', 'terminee'])) {
            return redirect()->route('operations.index')
                ->with('error', 'Cette opération ne peut plus être supprimée car elle a déjà été validée (statut: ' . $operation->statut_courant . ')');
        }

        try {
            // Supprimer les fichiers associés
            $operation->fichiers()->delete();

            // Supprimer les logs de statut associés
            \App\Models\OperationStatusLog::where('operation_id', $operation->id)->delete();

            // Supprimer les étapes de validation associées
            \DB::table('operation_service_validation')->where('operation_id', $operation->id)->delete();

            // Supprimer l'opération
            $operation->delete();

            return redirect()->route('operations.index')
                ->with('success', 'Opération supprimée avec succès');

        } catch (\Exception $e) {
            return redirect()->route('operations.index')
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            // La colonne `description` dans la table `operations` est un string (255)
            // On limite donc la longueur pour éviter l'erreur SQLSTATE[22001]
            'description' => 'nullable|string|max:255',
            'montant' => 'nullable|numeric|min:0',
            'priorite' => 'required|in:urgente,haute,moyenne,basse',
            'echeance' => 'nullable|date|after_or_equal:today',
            'destinataire_principal' => 'required|integer|exists:services_operationnels,id',
            'type_operation_id' => 'required|integer|exists:types_operations,id',
            'services_cc' => 'nullable|string',
            'fichiers.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'draft' => 'sometimes|boolean',
            // Nouveaux champs pour la chaîne de validation
            'validateur_1' => 'nullable|integer|exists:services_operationnels,id',
            'validateur_2' => 'nullable|integer|exists:services_operationnels,id',
            'validateur_3' => 'nullable|integer|exists:services_operationnels,id',
        ]);

        // Validation personnalisée pour les services CC
        if (!empty($validated['services_cc'])) {
            $ccServiceIds = explode(',', $validated['services_cc']);
            foreach ($ccServiceIds as $serviceId) {
                $serviceId = trim($serviceId);
                if (!empty($serviceId) && !is_numeric($serviceId)) {
                    return back()->withInput()->withErrors(['services_cc' => 'Les services CC doivent être des IDs valides séparés par des virgules']);
                }
            }
        }

        $user = Auth::user();
        $isDraft = $validated['draft'] ?? false;

        // Récupérer le type d'opération
        $typeOperation = TypeOperation::find($validated['type_operation_id']);

        // Créer l'opération
        $operation = Operation::create([
            'type' => $typeOperation ? $typeOperation->libelle : 'Standard',
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? '',
            'montant' => $validated['montant'] ?? 0,
            'date_operation' => now(),
            'priorite' => $validated['priorite'],
            'echeance' => $validated['echeance'],
            'operational_service_id' => $validated['destinataire_principal'],
            'type_operation_id' => $validated['type_operation_id'],
            'statut_courant' => $isDraft ? 'brouillon' : 'pending_validation',
            'user_id' => $user?->id,
            'demandeur_name' => $user?->name ?? 'Démo',
            'demandeur_email' => $user?->email ?? config('mail.from.address'),
        ]);

        // Gestion des fichiers
        if ($request->hasFile('fichiers')) {
            $fichiers = [];
            foreach ($request->file('fichiers') as $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('operations', $filename, 'public');
                    $fichiers[] = [
                        'nom' => $filename,
                        'chemin' => $path,
                        'taille' => $file->getSize(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
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

        // Créer la chaîne de validation séquentielle (totalement optionnelle)
        if (!$isDraft) {
            $validateurs = [];

            // Validateur 1 (optionnel)
            if (!empty($validated['validateur_1'])) {
                $validateurs[] = $validated['validateur_1'];
            }

            // Validateur 2 (optionnel)
            if (!empty($validated['validateur_2'])) {
                $validateurs[] = $validated['validateur_2'];
            }

            // Validateur 3 (optionnel)
            if (!empty($validated['validateur_3'])) {
                $validateurs[] = $validated['validateur_3'];
            }

            // Utiliser une transaction pour garantir l'intégrité
            \DB::transaction(function () use ($operation, $validateurs) {
                // Supprimer d'abord les étapes de validation existantes pour éviter les doublons
                \DB::table('operation_service_validation')->where('operation_id', $operation->id)->delete();

                // Créer les étapes de validation dans l'ordre
                foreach ($validateurs as $index => $validateurId) {
                    try {
                        \DB::table('operation_service_validation')->insert([
                            'operation_id' => $operation->id,
                            'service_operationnel_id' => $validateurId,
                            'ordre_validation' => $index + 1,
                            'statut' => $index === 0 ? 'EN_COURS' : 'EN_ATTENTE',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Si l'insertion échoue à cause d'un doublon, utiliser insertOrIgnore
                        if ($e->getCode() == 23000) {
                            \DB::table('operation_service_validation')->insertOrIgnore([
                                'operation_id' => $operation->id,
                                'service_operationnel_id' => $validateurId,
                                'ordre_validation' => $index + 1,
                                'statut' => $index === 0 ? 'EN_COURS' : 'EN_ATTENTE',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            throw $e;
                        }
                    }
                }
            });

            // Mettre à jour le statut de l'opération
            if (!empty($validateurs)) {
                // Cas 1 : au moins un validateur (V1, V2 ou V3)
                // → workflow multi-niveaux avec chaîne de validation
                $operation->statut_courant = 'pending_validation';

                // Récupération de l'URL de validation principale
                $url = request()->getSchemeAndHttpHost() . "/operations/{$operation->id}/validate";

                // Envoi de mail au PREMIER validateur réel (V1, sinon V2 ou V3) + destinataire principal
                $premierValidateurId = $validateurs[0] ?? null;
                try {
                    $premierValidateur = $premierValidateurId ? ServiceOperationnel::find($premierValidateurId) : null;
                    $destinatairePrincipal = ServiceOperationnel::find($validated['destinataire_principal']);

                    // Récupérer les emails des services en CC
                    $ccEmails = [];
                    if (!empty($validated['services_cc'])) {
                        $serviceIds = array_map('trim', explode(',', $validated['services_cc']));
                        foreach ($serviceIds as $serviceId) {
                            $service = ServiceOperationnel::find($serviceId);
                            if ($service && !empty($service->email)) {
                                $ccEmails[] = $service->email;
                            }
                        }
                    }

                    // Préparer le mail commun
                    $mail = new SendEmail($operation, $url);
                    if (!empty($ccEmails)) {
                        $mail->cc($ccEmails);
                    }

                    // Envoi au premier validateur
                    if ($premierValidateur && !empty($premierValidateur->email) && filter_var($premierValidateur->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($premierValidateur->email)->send(clone $mail);
                        \Log::info("✅ Email de validation envoyé avec succès pour l'opération {$operation->id} à {$premierValidateur->email}" .
                            (empty($ccEmails) ? "" : " (CC: " . implode(', ', $ccEmails) . ")"));
                    } else {
                        \Log::warning("⚠️ Email du validateur invalide ou manquant pour l'opération {$operation->id}");
                    }

                    // Envoi au destinataire principal (pour information immédiate)
                    if ($destinatairePrincipal && !empty($destinatairePrincipal->email) && filter_var($destinatairePrincipal->email, FILTER_VALIDATE_EMAIL)) {
                        // Eviter double envoi si identique au validateur
                        if (!$premierValidateur || $destinatairePrincipal->email !== $premierValidateur->email) {
                            Mail::to($destinatairePrincipal->email)->send(clone $mail);
                            \Log::info("✅ Email d'information envoyé au destinataire principal pour l'opération {$operation->id} à {$destinatairePrincipal->email}" .
                                (empty($ccEmails) ? "" : " (CC: " . implode(', ', $ccEmails) . ")"));
                        }
                    } else {
                        \Log::warning("⚠️ Email destinataire principal invalide ou manquant pour l'opération {$operation->id}");
                    }
                } catch (\Exception $e) {
                    // Ne pas bloquer la création si l'envoi d'email échoue
                    \Log::error("❌ Erreur lors de l'envoi de l'email pour l'opération {$operation->id}: " . $e->getMessage());
                }

                // Persister le nouveau statut si modifié en mémoire
                $operation->save();
            } else {
                // Cas 2 : aucun validateur sélectionné (pas de V1/V2/V3)
                // → opération à approuver ou rejeter dans le circuit simple
                // On laisse le statut "pending_validation" défini lors de la création,
                // sans l'approuver automatiquement et sans envoyer de mail immédiat.
                // (Les écrans de validations utiliseront ce statut pour la suite.)
                $operation->statut_courant = 'pending_validation';
                $operation->save();
            }
        }

        return redirect()->route('operations.index')
            ->with('success', $isDraft ? 'Opération enregistrée comme brouillon.' : 'Opération créée avec succès');
    }

    /**
     * Review operation for validation (temporary signed route)
     */
    public function reviewOperation(Operation $operation, $step)
    {
        $user = Auth::user();

        // Vérifier si l'opération est déjà validée (approuvée ou rejetée)
        $isAlreadyValidated = in_array($operation->statut_courant, ['approuvee', 'rejetee', 'termine']);

        // Si l'opération est déjà validée, rediriger avec un message
        if ($isAlreadyValidated) {
            $statusText = match($operation->statut_courant) {
                'approuvee' => 'approuvée',
                'rejetee' => 'rejetée',
                'termine' => 'terminée',
                default => 'traitée'
            };

            return redirect()->route('operations.index')
                ->with('info', "Cette opération a déjà été {$statusText} et ne peut plus être validée.");
        }

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

        // Si l'utilisateur n'est pas autorisé à valider cette étape
        if (!$isValidator) {
            return redirect()->route('operations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à valider cette opération.');
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
     * Traiter la validation via AJAX
     */
    private function processValidation(Request $request, Operation $operation, $user)
    {
        try {
            $action = $request->input('action');
            $step = $request->input('step');
            $commentaire = $request->input('commentaire', '');

            // Vérifier que l'opération n'est pas déjà approuvée ou rejetée
            if (in_array($operation->statut_courant, ['approuvee', 'rejetee', 'terminee'])) {
                $statusText = match($operation->statut_courant) {
                    'approuvee' => 'approuvée',
                    'rejetee' => 'rejetée',
                    'terminee' => 'terminée',
                    default => 'traitée'
                };
                return response()->json([
                    'success' => false,
                    'message' => "Cette opération a déjà été {$statusText}. Impossible de la valider à nouveau.",
                    'redirect' => route('operations.show', $operation->id)
                ], 409);
            }

            // Vérifier que l'étape est bien EN_COURS (pas déjà traitée)
            $stepData = \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', $step)
                ->first();

            if (!$stepData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette étape de validation n\'existe pas.'
                ], 404);
            }

            if ($stepData->statut !== 'EN_COURS') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette étape a déjà été traitée (' . $stepData->statut . '). Veuillez rafraîchir la page.',
                    'redirect' => route('operations.show', $operation->id)
                ], 409);
            }

            // Vérifier que l'utilisateur a le droit de valider cette étape
            $isAdmin = in_array($user->role, ['admin', 'superadmin']);
            if (!$isAdmin) {
                $isValidator = ($user->service_id == $stepData->service_operationnel_id);
                if (!$isValidator) {
                    $serviceEmail = \DB::table('services_operationnels')
                        ->where('id', $stepData->service_operationnel_id)
                        ->value('email');
                    $isValidator = ($serviceEmail && $serviceEmail === $user->email);
                }

                if (!$isValidator) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous n\'êtes pas autorisé à valider cette étape. Seul le service destinataire peut valider.'
                    ], 403);
                }
            }

            // Mettre à jour le statut de l'étape actuelle
            \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', $step)
                ->update([
                    'statut' => $action === 'approve' ? 'APPROUVE' : 'REJETE',
                    'validateur_id' => $user->id,
                    'date_validation' => now(),
                    'commentaire' => $commentaire,
                ]);

            // Enregistrer le changement de statut
            \App\Models\OperationStatusLog::create([
                'operation_id' => $operation->id,
                'from_status' => 'EN_VALIDATION',
                'to_status' => $action === 'approve' ? 'EN_COURS' : 'REJETE',
                'user_name' => $user->name,
                'user_id' => $user->id,
                'commentaire' => $commentaire,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Nombre total d'étapes de validation
            $totalSteps = \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->count();

            if ($action === 'approve') {
                // APPROBATION
                if ($step >= $totalSteps) {
                    // Dernière étape validée : on approuve définitivement l'opération
                    $operation->update(['statut_courant' => 'approuvee']);

                    // Envoyer un email au destinataire principal
                    $this->sendApprovalNotificationToRecipient($operation, $commentaire);

                    return response()->json([
                        'success' => true,
                        'message' => 'Opération approuvée définitivement',
                        'redirect' => route('validations.approved')
                    ]);
                }

                // Il reste au moins une étape : activer l'étape suivante et prévenir son validateur
                $nextStep = $step + 1;
                \DB::table('operation_service_validation')
                    ->where('operation_id', $operation->id)
                    ->where('ordre_validation', $nextStep)
                    ->update(['statut' => 'EN_COURS']);

                // Envoyer un email au validateur suivant (V2, V3, ...)
                $this->sendValidationEmail($operation, $nextStep, $commentaire);

                return response()->json([
                    'success' => true,
                    'message' => 'Étape validée avec succès. Le validateur suivant a été notifié.',
                    'redirect' => route('validations.pending')
                ]);
            } else {
                // REJET : marquer l'opération comme rejetée
                $operation->update(['statut_courant' => 'rejetee']);

                // Notifier le destinataire principal qu'une opération a été rejetée
                try {
                    $recipientService = \DB::table('services_operationnels')
                        ->where('id', $operation->operational_service_id)
                        ->first();

                    if ($recipientService && $recipientService->email) {
                        Mail::to($recipientService->email)
                            ->send(new OperationProfessionalMail($operation, 'rejetee', $recipientService->nom ?? 'Destinataire'));
                    }
                } catch (\Exception $ex) {
                    Log::error("Erreur envoi email rejet opération {$operation->id}: " . $ex->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Opération rejetée avec succès.',
                    'redirect' => route('validations.rejected')
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Erreur validation opération {$operation->id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la validation'
            ], 500);
        }
    }

    /**
     * Envoyer un email de notification au validateur suivant
     */
    private function sendValidationEmail(Operation $operation, $step, $previousComment = '')
    {
        try {
            $nextValidation = \DB::table('operation_service_validation')
                ->where('operation_id', $operation->id)
                ->where('ordre_validation', $step)
                ->first();

            if (!$nextValidation) {
                return;
            }

            $service = \DB::table('services_operationnels')
                ->where('id', $nextValidation->service_operationnel_id)
                ->first();

            if (!$service || !$service->email) {
                return;
            }

            $data = [
                'operation' => $operation,
                'step' => $step,
                'previousComment' => $previousComment,
                'validatorName' => $service->nom ?? 'Validateur',
                'operationTitle' => $operation->titre,
                'operationAmount' => number_format($operation->montant ?? 0, 0, ',', ' ') . ' FCFA',
                'operationRef' => '#' . str_pad($operation->id, 5, '0', STR_PAD_LEFT),
                'validationUrl' => route('validations.pending')
            ];

            \Mail::to($service->email)->send(new \App\Mail\ValidationStepNotification($data));

            // Log de succès
            \Log::info("✅ Email de validation étape {$step} envoyé avec succès pour l'opération {$operation->id} à {$service->email}");

        } catch (\Exception $e) {
            \Log::error("❌ Erreur envoi email validation étape {$step} opération {$operation->id}: " . $e->getMessage());
        }
    }

    /**
     * Envoyer un email de notification au destinataire principal après validation finale
     */
    private function sendApprovalNotificationToRecipient(Operation $operation, $finalComment = '')
    {
        try {
            // Récupérer le destinataire principal
            $recipientService = \DB::table('services_operationnels')
                ->where('id', $operation->operational_service_id)
                ->first();

            if (!$recipientService || !$recipientService->email) {
                return;
            }

            $data = [
                'operation' => $operation,
                'recipientService' => $recipientService,
                'finalComment' => $finalComment,
                'recipientName' => $recipientService->nom ?? 'Destinataire',
                'operationTitle' => $operation->titre,
                'operationAmount' => number_format($operation->montant ?? 0, 0, ',', ' ') . ' FCFA',
                'operationRef' => '#' . str_pad($operation->id, 5, '0', STR_PAD_LEFT),
                'operationUrl' => route('operations.show', $operation->id),
                'validationCompleted' => true
            ];

            \Mail::to($recipientService->email)->send(new \App\Mail\OperationApprovedNotification($data));

            // Log de succès
            \Log::info("✅ Email d'approbation envoyé avec succès pour l'opération {$operation->id} à {$recipientService->email}");

        } catch (\Exception $e) {
            \Log::error("❌ Erreur envoi email destinataire principal opération {$operation->id}: " . $e->getMessage());
        }
    }

    /**
     * Page Bon Pour Accord - Formulaire pour envoyer l'email au comptable/trésorerie
     */
    public function bonPourAccord(Operation $operation)
    {
        // Vérifier que l'opération est bien approuvée
        if ($operation->statut_courant !== 'approuvee') {
            return redirect()->route('operations.show', $operation->id)
                ->with('error', 'Cette opération n\'est pas encore approuvée.');
        }

        return view('operations.bon-pour-accord', compact('operation'));
    }

    /**
     * Envoyer un email d'exécution pour une opération
     */
    public function sendExecutionEmail(Request $request, Operation $operation)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            // Récupérer les paramètres de l'entreprise (optionnel, peut être null)
            $entrepriseSettings = null;
            try {
                $entrepriseSettings = \App\Models\EntrepriseSettings::getActive();
            } catch (\Exception $settingsEx) {
                // Table peut ne pas exister, continuer sans
            }

            // Générer un lien sécurisé pour marquer comme payée
            $signedUrl = \URL::signedRoute('operations.markAsPaidByLink', ['operationId' => $operation->id]);

            // Préparer les données pour l'email
            $data = [
                'operation' => $operation,
                'message' => $request->message,
                'recipientEmail' => $request->email,
                'entrepriseSettings' => $entrepriseSettings,
                'operationUrl' => route('operations.show', $operation->id),
                'operationRef' => '#' . str_pad($operation->id, 5, '0', STR_PAD_LEFT),
                'operationTitle' => $operation->titre,
                'operationAmount' => number_format($operation->montant ?? 0, 0, ',', ' ') . ' FCFA',
                'markAsPaidUrl' => $signedUrl,
            ];

            // Envoyer l'email Bon Pour Accord avec le lien
            \Mail::to($request->email)->send(new \App\Mail\OperationExecutionMail($operation, $request->message, $signedUrl));

            \Log::info("✅ Bon Pour Accord envoyé pour l'opération {$operation->id} à {$request->email}");

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bon Pour Accord envoyé avec succès à ' . $request->email
                ]);
            }

            return back()->with('success', 'Bon Pour Accord envoyé avec succès à ' . $request->email);
        } catch (\Exception $e) {
            \Log::error("❌ Erreur envoi Bon Pour Accord opération {$operation->id}: " . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }

    /**
     * Page de validation spécifique pour une opération
     */
    public function validateOperation(Request $request, Operation $operation)
    {
        $user = Auth::user();

        // Si c'est une requête POST (validation via pop-up)
        if ($request->isMethod('post')) {
            return $this->processValidation($request, $operation, $user);
        }

        // Si c'est une requête GET (affichage de la page)
        $user = Auth::user();

        // Vérifier si l'opération est déjà validée (approuvée ou rejetée)
        $isAlreadyValidated = in_array($operation->statut_courant, ['approuvee', 'rejetee', 'termine', 'terminee']);

        // Si l'opération est déjà validée, rediriger avec un message
        if ($isAlreadyValidated) {
            $statusText = match($operation->statut_courant) {
                'approuvee' => 'approuvée',
                'rejetee' => 'rejetée',
                'termine', 'terminee' => 'terminée',
                default => 'traitée'
            };

            return redirect()->route('operations.show', $operation->id)
                ->with('info', "Cette opération a déjà été {$statusText} et ne peut plus être validée.");
        }

        // Récupérer les étapes de validation pour cette opération uniquement
        $validationSteps = \DB::table('operation_service_validation')
            ->where('operation_service_validation.operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        // Trouver l'étape actuelle
        $currentStep = null;
        $isValidator = false;

        foreach ($validationSteps as $step) {
            if ($step->statut === 'EN_COURS') {
                $currentStep = $step;
                break;
            }
        }

        // Vérifier si l'utilisateur actuel peut valider cette étape
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        if ($currentStep) {
            // Vérifier par service_id
            $isValidator = ($user->service_id == $currentStep->service_operationnel_id);

            // Si pas par service_id, vérifier par email
            if (!$isValidator) {
                $serviceEmail = \DB::table('services_operationnels')
                    ->where('id', $currentStep->service_operationnel_id)
                    ->value('email');

                if ($serviceEmail && $serviceEmail === $user->email) {
                    $isValidator = true;
                }
            }
        }

        // Bloquer l'accès si l'utilisateur n'est ni validateur ni admin/superadmin
        if (!$isValidator && !$isAdmin) {
            return redirect()->route('operations.show', $operation->id)
                ->with('error', 'Vous n\'êtes pas autorisé à valider cette opération. Seul le service destinataire ou un administrateur peut valider.');
        }

        // Récupérer toutes les étapes de validation (pour l'affichage du circuit complet)
        $allSteps = \DB::table('operation_service_validation')
            ->where('operation_id', $operation->id)
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->orderBy('operation_service_validation.ordre_validation')
            ->get([
                'operation_service_validation.*',
                'services_operationnels.nom as service_name',
                'services_operationnels.email as service_email'
            ]);

        // Récupérer l'historique des statuts
        $statusHistory = \App\Models\OperationStatusLog::where('operation_id', $operation->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer tous les services actifs pour le champ CC
        $services = \App\Models\ServiceOperationnel::actif()->ordre()->get(['id', 'nom']);

        return view('operations.validate-simple', compact('operation', 'validationSteps', 'currentStep', 'allSteps', 'statusHistory', 'services'));
    }

    /**
     * List pending validations for current user
     */
    public function pendingValidation(Request $request)
    {
        $user = Auth::user();

        // Récupérer les opérations en attente de validation
        // Exclure les opérations déjà approuvées ou rejetées
        $query = DB::table('operations')
            ->join('operation_service_validation', 'operations.id', '=', 'operation_service_validation.operation_id')
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id')
            ->where('operation_service_validation.statut', 'EN_COURS')
            ->whereNotIn('operations.statut_courant', ['approuvee', 'approuve', 'rejetee', 'terminee']);

        // Admin/superadmin voient tout
        // Les autres voient : leurs propres opérations + celles où ils sont validateurs assignés
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->where(function($q) use ($user) {
                $q->where('operations.user_id', $user->id)
                  ->orWhere('services_operationnels.email', $user->email);
                if ($user->service_id) {
                    $q->orWhere('operation_service_validation.service_operationnel_id', $user->service_id);
                }
            });
        }

        $operations = $query->select('operations.*', 'operation_service_validation.ordre_validation as step')
            ->distinct()
            ->latest('operations.created_at')
            ->paginate(15);

        return view('validations.pending', compact('operations', 'user'));
    }

    /**
     * List approved validations for current user
     */
    public function approvedValidation(Request $request)
    {
        $user = Auth::user();

        // Récupérer les opérations approuvées
        $query = DB::table('operations')
            ->whereIn('operations.statut_courant', ['approuvee', 'approuve']);

        // Admin/superadmin voient tout
        // Les autres voient : leurs propres opérations + celles où ils sont validateurs assignés
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->where(function($q) use ($user) {
                $q->where('operations.user_id', $user->id)
                  ->orWhereIn('operations.id', function($sub) use ($user) {
                      $sub->select('osv.operation_id')
                          ->from('operation_service_validation as osv')
                          ->join('services_operationnels as so', 'osv.service_operationnel_id', '=', 'so.id')
                          ->where(function($w) use ($user) {
                              $w->where('so.email', $user->email);
                              if ($user->service_id) {
                                  $w->orWhere('osv.service_operationnel_id', $user->service_id);
                              }
                          });
                  });
            });
        }

        $operations = $query->select('operations.*')
            ->distinct()
            ->latest('operations.created_at')
            ->paginate(15);

        return view('validations.approved', compact('operations', 'user'));
    }

    /**
     * Liste des opérations payées (réservé admin / superadmin)
     */
    public function paidOperations(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        $search = $request->input('q');
        $serviceId = $request->input('service_id');

        $query = Operation::with(['initiateur', 'operationalService', 'invoice', 'paidBy'])
            ->where(function ($q) {
                $q->where('is_paid', true)
                    ->orWhere('statut_courant', 'payee');
            });

        if ($serviceId) {
            $query->where('operational_service_id', $serviceId);
        }

        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('titre', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $operations = $query->orderByDesc('paid_at')
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $services = ServiceOperationnel::orderBy('nom')->get(['id', 'nom']);

        return view('validations.paid', compact('operations', 'services', 'search', 'serviceId'));
    }

    /**
     * Marquer une opération comme payée.
     */
    public function markAsPaid(Request $request, Operation $operation)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Seuls les administrateurs peuvent marquer une opération payée.');
        }

        if ($operation->is_paid) {
            return back()->with('info', 'Cette opération est déjà marquée payée.');
        }

        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:500',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $previousStatus = $operation->statut_courant ?? 'en_attente';

        $operation->forceFill([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $user->id,
            'payment_reference' => $validated['payment_reference'] ?? $operation->payment_reference,
            'statut_courant' => 'payee',
        ])->save();

        OperationStatusLog::create([
            'operation_id' => $operation->id,
            'from_status' => $previousStatus,
            'to_status' => 'payee',
            'user_name' => $user->name,
            'user_id' => $user->id,
            'commentaire' => $validated['commentaire'] ?? 'Opération marquée comme payée',
        ]);

        if ($operation->invoice) {
            $operation->invoice->update(['status' => 'paid']);
        }

        return back()->with('success', 'Opération marquée comme payée.');
    }

    /**
     * List rejected validations for current user
     */
    public function rejectedValidation(Request $request)
    {
        $user = Auth::user();

        // Récupérer les opérations rejetées
        $query = DB::table('operations')
            ->where('operations.statut_courant', 'rejetee');

        // Admin/superadmin voient tout
        // Les autres voient : leurs propres opérations + celles où ils sont validateurs assignés
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->where(function($q) use ($user) {
                $q->where('operations.user_id', $user->id)
                  ->orWhereIn('operations.id', function($sub) use ($user) {
                      $sub->select('osv.operation_id')
                          ->from('operation_service_validation as osv')
                          ->join('services_operationnels as so', 'osv.service_operationnel_id', '=', 'so.id')
                          ->where(function($w) use ($user) {
                              $w->where('so.email', $user->email);
                              if ($user->service_id) {
                                  $w->orWhere('osv.service_operationnel_id', $user->service_id);
                              }
                          });
                  });
            });
        }

        $operations = $query->select('operations.*')
            ->distinct()
            ->latest('operations.created_at')
            ->paginate(15);

        return view('validations.rejected', compact('operations', 'user'));
    }

    /**
     * View validation history for current user
     */
    public function validationHistory(Request $request)
    {
        $user = Auth::user();

        // Récupérer l'historique complet de validation
        $query = DB::table('operation_service_validation')
            ->join('operations', 'operation_service_validation.operation_id', '=', 'operations.id')
            ->join('services_operationnels', 'operation_service_validation.service_operationnel_id', '=', 'services_operationnels.id');

        // Admin/superadmin voient tout
        // Les autres voient : leurs propres opérations + celles où ils sont validateurs assignés
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->where(function($q) use ($user) {
                $q->where('operations.user_id', $user->id)
                  ->orWhere('services_operationnels.email', $user->email);
                if ($user->service_id) {
                    $q->orWhere('operation_service_validation.service_operationnel_id', $user->service_id);
                }
            });
        }

        $validations = $query->select('operation_service_validation.*', 'operations.titre as operation_titre', 'operations.montant', 'operations.statut_courant')
            ->orderBy('operation_service_validation.date_validation', 'desc')
            ->paginate(20);

        return view('validations.history', compact('validations', 'user'));
    }
}
