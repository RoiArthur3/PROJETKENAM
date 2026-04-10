<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Service;
use App\Models\User;
use App\Models\OperationHistorique;
use App\Models\OperationService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @property User $user Utilisateur authentifié
 */
class OperationRequeteController extends Controller
{
    /**
     * @var User
     */
    protected $currentUser;
    /**
     * Afficher la liste des requêtes
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        /* @var $user User */

        $query = Operation::with([
            'serviceEmetteur',
            'serviceDestinataire',
            'user',
            'destinataire',
            'historiques.user'
        ]);

        // Visibilité par défaut : limiter aux requêtes liées au service / utilisateur courant
        // Sauf si scope=all est explicitement demandé (réservé aux profils autorisés)
        if ($user && $request->input('scope') !== 'all') {
            $serviceId = $user->service_id ?? null;

            $query->where(function($q) use ($user, $serviceId) {
                if ($serviceId) {
                    $q->where('service_emetteur_id', $serviceId)
                      ->orWhere('service_destinataire_id', $serviceId);
                }

                $q->orWhere('demandeur_email', $user->email);
            });
        }

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut_requete', $request->statut);
        }

        if ($request->filled('service')) {
            $query->where(function($q) use ($request) {
                $q->where('service_emetteur_id', $request->service)
                  ->orWhere('service_destinataire_id', $request->service);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_requete', 'like', "%{$search}%");
            });
        }

        // Séparer les opérations en fonction du contexte
        $operations = $query->latest('created_at')->paginate(15);

        // Statistiques simplifiées (la colonne statut_requete n'existe pas sur la table operations actuelle)
        $stats = [
            'total' => Operation::count(),
            'en_attente' => 0,
            'en_cours' => 0,
            'cloturees' => 0,
        ];

        $services = Service::actif()->get();

        return view('requetes.index', compact('operations', 'stats', 'services'));
    }

    public function create()
    {
        $services = Service::actif()->get();

        // Si un service est pré-sélectionné (old input), récupérer ses utilisateurs
        $selectedServiceUsers = [];
        if (old('service_destinataire_id')) {
            $service = Service::find(old('service_destinataire_id'));
            if ($service) {
                $selectedServiceUsers = $service->users()
                    ->select('id', 'name', 'email', 'role')
                    ->where('statut', 'actif')
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('requetes.create', compact('services', 'selectedServiceUsers'));
    }

    /**
     * Enregistrer une nouvelle requête
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'service_emetteur_id' => 'required|exists:services,id',
            'service_destinataire_id' => 'required|exists:services,id',
            'personnes_destinataires' => 'nullable|array',
            'personnes_destinataires.*' => 'nullable|exists:users,id',
            'type_requete' => 'required|string|max:100',
            'priorite' => 'required|in:BASSE,MOYENNE,HAUTE,URGENTE',
        ]);

        $currentUser = Auth::user();
        /* @var $currentUser User */

        $operation = DB::transaction(function () use ($validated, $request, $currentUser) {
            $operation = new Operation();
            // La table operations n'a pas de colonne 'nom' : utiliser 'titre' pour stocker l'objet
            $operation->titre = $validated['nom'];
            $operation->description = $validated['description'];
            // Service émetteur choisi dans le formulaire
            $operation->service_emetteur_id = $validated['service_emetteur_id'];
            $operation->service_destinataire_id = $validated['service_destinataire_id'];
            $operation->statut_requete = 'ENREGISTREE';
            $operation->type_requete = $validated['type_requete'];
            $operation->priorite = $validated['priorite'];
            $operation->reference_requete = $this->generateReference();
            $operation->save();

            // Récupérer le service destinataire
            $service = Service::find($validated['service_destinataire_id']);

            // Récupérer les personnes sélectionnées
            $personnesIds = $validated['personnes_destinataires'] ?? [];

            if (!empty($personnesIds)) {
                // Si des personnes sont spécifiées, créer une entrée par personne
                foreach ($personnesIds as $index => $personneId) {
                    $personne = User::find($personneId);

                    OperationService::create([
                        'operation_id' => $operation->id,
                        'service_name' => $service->nom,
                        'service_email' => $service->email,
                        'destinataire_nom' => $personne->name,
                        'destinataire_email' => $personne->email,
                        'ordre' => $index,
                        'statut' => 'EN_ATTENTE',
                    ]);
                }
            } else {
                // Sinon, créer une entrée pour le service entier
                OperationService::create([
                    'operation_id' => $operation->id,
                    'service_name' => $service->nom,
                    'service_email' => $service->email,
                    'destinataire_nom' => null,
                    'destinataire_email' => null,
                    'ordre' => 0,
                    'statut' => 'EN_ATTENTE',
                ]);
            }

            // Enregistrer l'historique
            OperationHistorique::create([
                'operation_id' => $operation->id,
                'user_id' => $currentUser->id,
                'action' => 'CREATION',
                'nouveau_statut' => 'ENREGISTREE',
                'commentaire' => 'Requête créée',
            ]);

            return $operation;
        });

        return redirect()->route('requetes.show', $operation)
            ->with('success', 'Requête créée avec succès.');
    }

    /**
     * Afficher les détails d'une requête
     *
     * @param Operation $operation
     * @return \Illuminate\View\View
     */
    public function show(Operation $operation)
    {
        $operation->load([
            'serviceEmetteur',
            'serviceDestinataire',
            'user',
            'destinataire',
            'historiques.user'
        ]);

        $services = Service::actif()->get();

        return view('requetes.show', compact('operation', 'services'));
    }

    /**
     * Envoyer une requête au service destinataire
     *
     * @param Operation $operation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function envoyer(Operation $operation)
    {
        DB::transaction(function () use ($operation) {
            $currentUser = Auth::user();
            /* @var $currentUser User */

            $operation->statut_requete = 'ENVOYEE';
            $operation->date_envoi = now();
            $operation->save();

            $operation->historiques()->create([
                'user_id' => $currentUser->id,
                'action' => 'ENVOI',
                'ancien_statut' => 'ENREGISTREE',
                'nouveau_statut' => 'ENVOYEE',
                'commentaire' => 'Requête envoyée au service destinataire',
            ]);

            // Créer (ou retrouver) une validation liée pour le tableau récapitulatif
            $existing = \App\Models\Validation::where('module_source', 'requetes')
                ->where('record_id', $operation->id)
                ->first();
            if (!$existing) {
                // Déterminer le validateur cible (utilisateur destinataire si défini)
                $destinataire = $operation->destinataire
                    ?? User::where('service_id', $operation->service_destinataire_id)->first();
                $validateurId = $destinataire?->id;

                \App\Services\WorkflowService::createValidation(
                    module: 'requetes',
                    recordId: $operation->id,
                    type: 'requete',
                    titre: $operation->nom ?? $operation->titre ?? 'Requête opération',
                    description: $operation->description,
                    validateurId: $validateurId
                );
            }

            // Envoyer l'email de notification
            $this->envoyerNotification($operation);
        });

        return redirect()->back()->with('success', 'Requête envoyée avec succès.');
    }

    /**
     * Clôturer une requête
     *
     * @param Request $request
     * @param Operation $operation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cloturer(Request $request, Operation $operation)
    {
        $this->authorize('update', $operation);

        $request->validate([
            'commentaire' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($operation, $request) {
            $currentUser = Auth::user();
            /* @var $currentUser User */

            $operation->statut_requete = 'CLOTUREE';
            $operation->date_cloture = now();
            $operation->save();

            $operation->historiques()->create([
                'user_id' => $currentUser->id,
                'action' => 'CLOTURE',
                'ancien_statut' => $operation->statut_requete,
                'nouveau_statut' => 'CLOTUREE',
                'commentaire' => $request->commentaire,
            ]);
        });

        // Notification de clôture (email + SMS)
        $this->envoyerNotification($operation, 'cloture');

        return redirect()->back()->with('success', 'Requête clôturée avec succès.');
    }

    /**
     * Transférer une requête vers un autre service
     *
     * @param Request $request
     * @param Operation $operation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function transferer(Request $request, Operation $operation)
    {
        $this->authorize('update', $operation);

        $request->validate([
            'service_destinataire_id' => 'required|exists:services,id',
            'destinataire_id' => 'nullable|exists:users,id',
            'commentaire' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($operation, $request) {
            $currentUser = Auth::user();
            /* @var $currentUser User */

            $ancienService = $operation->serviceDestinataire->nom;

            $operation->service_destinataire_id = $request->service_destinataire_id;
            $operation->destinataire_id = $request->destinataire_id;
            $operation->statut_requete = 'TRANSFERE';
            $operation->save();

            $operation->historiques()->create([
                'user_id' => $currentUser->id,
                'action' => 'TRANSFERT',
                'commentaire' => "Transférée de {$ancienService} vers " .
                    Service::find($request->service_destinataire_id)->nom . ". " . $request->commentaire,
            ]);

            // Envoyer la notification de transfert
            $this->envoyerNotification($operation, 'transfert');
        });

        return redirect()->back()->with('success', 'Requête transférée avec succès.');
    }

    /**
     * Ajouter un commentaire à une requête
     *
     * @param Request $request
     * @param Operation $operation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function commenter(Request $request, Operation $operation)
    {
        $this->authorize('update', $operation);

        $request->validate([
            'commentaire' => 'required|string|max:1000',
        ]);

        $currentUser = Auth::user();
        /* @var $currentUser User */

        $operation->historiques()->create([
            'user_id' => $currentUser->id,
            'action' => 'COMMENTAIRE',
            'commentaire' => $request->commentaire,
        ]);

        return redirect()->back()->with('success', 'Commentaire ajouté avec succès.');
    }

    private function generateReference(): string
    {
        $prefix = 'REQ';
        $date = now()->format('Ymd');
        $count = Operation::whereDate('created_at', now())->count() + 1;

        return $prefix . $date . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Envoyer une notification email pour une requête
     *
     * @param Operation $operation
     * @param string $type Type de notification (nouvelle, transfert, etc.)
     * @return void
     */
    private function envoyerNotification(Operation $operation, string $type = 'nouvelle')
    {
        $destinataire = $operation->destinataire ??
                       User::where('service_id', $operation->service_destinataire_id)->first();

        if (!$destinataire) {
            return;
        }

        $currentUser = Auth::user();
        /* @var $currentUser User */

        $data = [
            'operation' => $operation,
            'emetteur' => $currentUser,
            'serviceEmetteur' => $operation->serviceEmetteur,
            'serviceDestinataire' => $operation->serviceDestinataire,
        ];

        // Email existant
        Mail::to($destinataire->email)
            ->send(new \App\Mail\OperationNotification($data, $type));

        // SMS (si numéro disponible)
        $phone = $destinataire->phone
            ?? optional($operation->serviceDestinataire)->phone
            ?? null;

        if ($phone) {
            $numero = $operation->reference_requete ?? ($operation->id ? ('OP-'.$operation->id) : '');
            $lien = route('requetes.show', $operation);
            $serviceNom = optional($operation->serviceDestinataire)->nom ?? 'Service destinataire';
            $demandeurNom = $currentUser->name ?? 'Un utilisateur';
            $serviceEmetteurNom = optional($operation->serviceEmetteur)->nom;

            $demandeurAffiche = $serviceEmetteurNom
                ? $demandeurNom . " ({$serviceEmetteurNom})"
                : $demandeurNom;

            $message = "[KENAM SERVICES] Requête {$numero}\n" .
                "Demandeur : {$demandeurAffiche}\n" .
                "Service : {$serviceNom}\n" .
                "Statut : " . strtoupper($type) . "\n" .
                "Voir la demande : {$lien}";

            SmsService::send($phone, $message);
        }
    }

    /**
     * API pour récupérer les utilisateurs d'un service
     *
     * @param Service $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceUsers(Service $service)
    {
        try {
            $users = $service->users()
                ->select('id', 'name', 'email', 'role')
                ->where('statut', 'actif')
                ->orderBy('name')
                ->get();

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Service non trouvé'], 404);
        }
    }
}
