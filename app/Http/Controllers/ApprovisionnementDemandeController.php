<?php

namespace App\Http\Controllers;

use App\Jobs\SendApproNotificationFallbackJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ApprovisionnementDemande;
use App\Models\ApprovisionnementCaisse;
use App\Models\Caisse;
use App\Models\User;
use App\Mail\DynamicMail;
use App\Services\SmsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class ApprovisionnementDemandeController extends Controller
{
    private const EMAIL_ACTION_LINK_TTL_HOURS = 48;
    private const FALLBACK_DELAY_MINUTES = 10;

    // =========================================================================
    //  TRÉSORERIE — Création et suivi des demandes
    // =========================================================================

    /**
     * Liste des demandes du trésorier connecté.
     */
    public function index()
    {
        $demandes = ApprovisionnementDemande::with(['demandeur', 'caisseSource', 'caisseDestination', 'approuveur'])
            ->where('demandeur_id', Auth::id())
            ->latest()
            ->paginate(15);

        $stats = [
            'total'    => ApprovisionnementDemande::where('demandeur_id', Auth::id())->count(),
            'pending'  => ApprovisionnementDemande::where('demandeur_id', Auth::id())->where('statut', 'pending')->count(),
            'approved' => ApprovisionnementDemande::where('demandeur_id', Auth::id())->where('statut', 'approved')->count(),
            'executed' => ApprovisionnementDemande::where('demandeur_id', Auth::id())->where('statut', 'executed')->count(),
            'rejected' => ApprovisionnementDemande::where('demandeur_id', Auth::id())->where('statut', 'rejected')->count(),
        ];

        return view('tresorerie.approvisionnement-demandes.index', compact('demandes', 'stats'));
    }

    /**
     * Formulaire de création d'une demande.
     */
    public function create()
    {
        $caisses = Caisse::where('est_active', true)->orderBy('nom')->get();
        return view('tresorerie.approvisionnement-demandes.create', compact('caisses'));
    }

    /**
     * Enregistrer la demande et notifier comptable + DG par SMS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'montant'              => 'required|numeric|min:1',
            'devise'               => 'nullable|string|max:10',
            'raison'               => 'required|string|max:2000',
            'caisse_source_id'     => 'nullable|exists:caisses,id',
            'caisse_destination_id'=> 'required|exists:caisses,id',
            'alert_channel'        => 'nullable|in:sms,whatsapp,whatsapp_appro',
        ]);

        $demande = ApprovisionnementDemande::create([
            'montant'               => $validated['montant'],
            'devise'                => $validated['devise'] ?? 'XOF',
            'raison'                => $validated['raison'],
            'caisse_source_id'      => $validated['caisse_source_id'] ?? null,
            'caisse_destination_id' => $validated['caisse_destination_id'] ?? null,
            'demandeur_id'          => Auth::id(),
            'statut'                => 'pending',
        ]);

        // Priorite email; fallback SMS/WhatsApp 10 min plus tard si pas de reaction.
        $this->notifierCreation($demande, $validated['alert_channel'] ?? 'sms');

        return redirect()->route('tresorerie.approvisionnement-demandes.show', $demande)
            ->with('success', 'Demande d\'approvisionnement créée avec succès. Le comptable a été notifié.');
    }

    /**
     * Détails d'une demande (vue trésorier).
     */
    public function show(ApprovisionnementDemande $demande)
    {
        // Trésorier ne voit que ses propres demandes
        if ($demande->demandeur_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $demande->load(['demandeur', 'caisseSource', 'caisseDestination', 'approuveur', 'approvisionnementCaisse']);
        return view('tresorerie.approvisionnement-demandes.show', compact('demande'));
    }

    /**
     * Réception d'une demande d'approvisionnement depuis un webhook email entrant.
     */
    public function receiveFromEmail(Request $request)
    {
        $secret = config('services.email_inbound.secret', env('EMAIL_INBOUND_SECRET'));
        if (!empty($secret) && $request->input('secret') !== $secret) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized inbound secret'], 401);
        }

        $validated = $request->validate([
            'from_email' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'text' => 'nullable|string|max:10000',
            'html' => 'nullable|string',
            'devise' => 'nullable|string|max:10',
            'caisse_destination_id' => 'nullable|exists:caisses,id',
            'caisse_source_id' => 'nullable|exists:caisses,id',
        ]);

        $demandeur = User::where('email', $validated['from_email'])->first();
        if (!$demandeur) {
            return response()->json([
                'ok' => false,
                'message' => 'Aucun utilisateur interne ne correspond a cet email expediteur.',
            ], 422);
        }

        $rawText = trim((string) ($validated['text'] ?? ''));
        $subject = trim((string) ($validated['subject'] ?? 'Demande d\'approvisionnement'));

        $montant = $this->extractMontantFromText($rawText);
        $raison = trim($subject . "\n" . ($rawText ?: 'Demande recue par email.'));

        $caisseDestinationId = $validated['caisse_destination_id']
            ?? Caisse::where('est_active', true)->orderBy('id')->value('id');

        if (!$caisseDestinationId) {
            return response()->json([
                'ok' => false,
                'message' => 'Aucune caisse destination active disponible.',
            ], 422);
        }

        $demande = ApprovisionnementDemande::create([
            'montant' => $montant,
            'devise' => $validated['devise'] ?? 'XOF',
            'raison' => \Illuminate\Support\Str::limit($raison, 2000),
            'caisse_source_id' => $validated['caisse_source_id'] ?? null,
            'caisse_destination_id' => $caisseDestinationId,
            'demandeur_id' => $demandeur->id,
            'statut' => 'pending',
        ]);

        Log::info('Demande d\'approvisionnement creee via email entrant', [
            'demande_id' => $demande->id,
            'numero' => $demande->numero_demande,
            'from_email' => $validated['from_email'],
        ]);

        $this->notifierCreation($demande, 'sms');

        return response()->json([
            'ok' => true,
            'message' => 'Demande creee avec succes depuis email.',
            'demande_id' => $demande->id,
            'numero_demande' => $demande->numero_demande,
        ]);
    }

    // =========================================================================
    //  COMPTABILITÉ — Validation et exécution des demandes
    // =========================================================================

    /**
     * Liste des demandes en attente (vue comptable).
     */
    public function pending()
    {
        $this->authorizeComptable();

        $demandes = ApprovisionnementDemande::with(['demandeur', 'caisseSource', 'caisseDestination'])
            ->whereIn('statut', ['pending', 'pending_dg', 'approved'])
            ->latest()
            ->paginate(15);

        $stats = [
            'pending'  => ApprovisionnementDemande::where('statut', 'pending')->count(),
            'pending_dg' => ApprovisionnementDemande::where('statut', 'pending_dg')->count(),
            'approved' => ApprovisionnementDemande::where('statut', 'approved')->count(),
            'total'    => ApprovisionnementDemande::count(),
            'executed' => ApprovisionnementDemande::where('statut', 'executed')->count(),
        ];

        return view('comptabilite.approvisionnement-demandes.pending', compact('demandes', 'stats'));
    }

    /**
     * Approuver une demande.
     */
    public function approve(Request $request, ApprovisionnementDemande $demande)
    {
        $this->authorizeComptable();

        $currentUser = Auth::user();

        // Étape 1 : Comptabilité transfère à la DG.
        if ($demande->isPending() && $this->isComptabiliteRole($currentUser)) {
            $demande->update([
                'statut' => 'pending_dg',
            ]);

            $this->notifierTransfertDg($demande);

            return back()->with('success', "Demande {$demande->numero_demande} transférée à la DG pour validation finale.");
        }

        // Étape 2 : DG approuve définitivement.
        if (($demande->isPendingDg() || $demande->isPending()) && $this->isDgRole($currentUser)) {
            $demande->update([
                'statut'          => 'approved',
                'approuve_par_id' => Auth::id(),
                'approved_at'     => now(),
            ]);

            $this->notifierDecision($demande, 'approuvée');

            return back()->with('success', "Demande {$demande->numero_demande} approuvée par la DG. Vous pouvez maintenant l'exécuter.");
        }

        return back()->with('error', 'Cette demande ne peut pas être traitée dans son état actuel pour votre rôle.');
    }

    /**
     * Action email: la comptabilité fait suivre la demande a la DG via lien signe.
     */
    public function emailForwardToDg(Request $request, ApprovisionnementDemande $demande)
    {
        $actor = User::find($request->query('actor'));
        if (!$actor || !$this->isComptabiliteRole($actor)) {
            return response('Lien invalide ou utilisateur non autorise.', 403);
        }

        if (!$demande->isPending()) {
            return response('Cette demande a deja ete traitee.', 200);
        }

        $demande->update(['statut' => 'pending_dg']);
        $this->notifierTransfertDg($demande->fresh(['demandeur', 'caisseDestination']));

        return response("La demande {$demande->numero_demande} a ete transferee a la DG.", 200);
    }

    /**
     * Action email: validation DG via lien signe.
     */
    public function emailApproveByDg(Request $request, ApprovisionnementDemande $demande)
    {
        $actor = User::find($request->query('actor'));
        if (!$actor || !$this->isDgRole($actor)) {
            return response('Lien invalide ou utilisateur non autorise.', 403);
        }

        if (!($demande->isPendingDg() || $demande->isPending())) {
            return response('Cette demande ne peut plus etre approuvee.', 200);
        }

        if ($request->isMethod('get')) {
            // Afficher le formulaire de validation DG
            return response(view('tresorerie.approvisionnement-demandes.approve-dg', compact('demande')));
        }

        // POST: Traitement de l'approbation avec modification
        $validated = $request->validate([
            'montant' => 'required|numeric|min:1',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $demande->update([
            'statut' => 'approved',
            'approuve_par_id' => $actor->id,
            'approved_at' => now(),
            'montant' => $validated['montant'],
            'commentaire_dg' => $validated['commentaire'],
        ]);

        $this->notifierDecision($demande->fresh('demandeur'), 'approuvée');

        return response("La demande {$demande->numero_demande} est maintenant approuvee.", 200);
    }

    /**
     * Refuser une demande.
     */
    public function reject(Request $request, ApprovisionnementDemande $demande)
    {
        $this->authorizeComptable();

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if (!$demande->isPending()) {
            return back()->with('error', 'Cette demande ne peut pas être refusée dans son état actuel.');
        }

        $demande->update([
            'statut'           => 'rejected',
            'approuve_par_id'  => Auth::id(),
            'rejected_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notifier le demandeur
        $this->notifierDecision($demande, 'refusée');

        return back()->with('success', "Demande {$demande->numero_demande} refusée.");
    }

    /**
     * Exécuter un approvisionnement (demande approuvée → ApprovisionnementCaisse).
     */
    public function execute(Request $request, ApprovisionnementDemande $demande)
    {
        $this->authorizeComptable();

        if (!$demande->canExecute()) {
            return back()->with('error', 'Cette demande doit être approuvée avant exécution.');
        }

        try {
            DB::beginTransaction();

            // Créer l'approvisionnement réel
            $appro = ApprovisionnementCaisse::create([
                'caisse_source_id'      => $demande->caisse_source_id,
                'caisse_destination_id' => $demande->caisse_destination_id,
                'montant'               => $demande->montant,
                'devise'                => $demande->devise,
                'motif'                 => 'Demande ' . $demande->numero_demande . ' — ' . $demande->raison,
                'mode'                  => 'virement',
                'demandeur_id'          => $demande->demandeur_id,
                'valideur_id'           => Auth::id(),
                'statut'                => 'valide',
                'date_validation'       => now(),
                'date_decaissement'     => now(),
            ]);

            // Mettre à jour la demande
            $demande->update([
                'statut'                   => 'executed',
                'approuve_par_id'          => Auth::id(),
                'executed_at'              => now(),
                'approvisionnement_caisse_id' => $appro->id,
            ]);

            // Mettre à jour solde caisse destination si définie
            if ($demande->caisse_destination_id) {
                Caisse::where('id', $demande->caisse_destination_id)
                    ->increment('solde_actuel', (float) $demande->montant);
            }

            // Déduire solde caisse source si définie
            if ($demande->caisse_source_id) {
                Caisse::where('id', $demande->caisse_source_id)
                    ->decrement('solde_actuel', (float) $demande->montant);
            }

            DB::commit();

            // Notifier le demandeur
            $this->notifierDecision($demande->fresh(), 'exécutée');

            return back()->with('success', "Approvisionnement exécuté avec succès (réf: {$appro->numero_operation}).");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'exécution: ' . $e->getMessage());
        }
    }

    // =========================================================================
    //  Helpers privés
    // =========================================================================

    private function authorizeComptable(): void
    {
        $user = Auth::user();
        if (!in_array($user->role, ['comptabilite', 'dg', 'admin', 'superadmin'])) {
            abort(403, 'Accès réservé au service Comptabilité.');
        }
    }

    private function isComptabiliteRole(User $user): bool
    {
        return in_array($user->role, ['comptabilite'], true);
    }

    private function isDgRole(User $user): bool
    {
        return in_array($user->role, ['dg', 'admin', 'superadmin'], true);
    }

    private function notifierCreation(ApprovisionnementDemande $demande, string $channel = 'sms'): void
    {
        $caisseDestination = $demande->caisseDestination?->nom ?? 'Non définie';
        $fallbackChannel = 'sms';

        // Notification email immediate a la comptabilite (fallback admin).
        $emailRecipients = User::where('role', 'comptabilite')
            ->whereNotNull('email')
            ->get(['id', 'email']);

        if ($emailRecipients->isEmpty()) {
            $emailRecipients = User::where('role', 'admin')
                ->whereNotNull('email')
                ->get(['id', 'email']);
        }

        foreach ($emailRecipients as $recipient) {
            try {
                $forwardUrl = URL::temporarySignedRoute(
                    'approvisionnement-demandes.email.forward',
                    now()->addHours(self::EMAIL_ACTION_LINK_TTL_HOURS),
                    ['demande' => $demande->id, 'actor' => $recipient->id]
                );

                Mail::to($recipient->email)->send(new DynamicMail('dynamic', [
                    'subject' => "[KENAM] Nouvelle demande d'approvisionnement {$demande->numero_demande}",
                    'message' => "Une nouvelle demande d'approvisionnement a ete soumise et necessite votre traitement.",
                    'details' => "Demandeur: {$demande->demandeur->name}\nCaisse a approvisionner: {$caisseDestination}\nMontant: " . number_format((float) $demande->montant, 0, ',', ' ') . " {$demande->devise}\nRaison: " . str($demande->raison)->limit(300),
                    'action_url' => $forwardUrl,
                    'action_text' => "Faire suivre a la DG",
                ]));

                // Fallback alerte apres 10 min si statut toujours pending.
                $this->dispatchFallback($demande, $recipient->id, 'pending', $fallbackChannel);
            } catch (\Throwable) {
            }
        }
    }

    private function notifierDecision(ApprovisionnementDemande $demande, string $action): void
    {
        $demandeur = $demande->demandeur;
        $demandeurPhone = $this->getUserPhone($demandeur);
        if (!$demandeur || !$demandeurPhone) {
            return;
        }

        $montantFormate = number_format((float) $demande->montant, 0, ',', ' ') . ' ' . $demande->devise;
        $message = "KENAM | Demande Appro {$demande->numero_demande}\n"
            . "Statut : {$action} par " . Auth::user()->name . "\n"
            . "Montant : {$montantFormate}";

        if ($demande->isRejected() && $demande->rejection_reason) {
            $message .= "\nMotif : " . str($demande->rejection_reason)->limit(80);
        }

        try {
            // Les retours de décision restent en SMS pour compatibilité immédiate.
            SmsService::send($demandeurPhone, $message);
        } catch (\Throwable) {
        }
    }

    private function notifierTransfertDg(ApprovisionnementDemande $demande): void
    {
        $caisseDestination = $demande->caisseDestination?->nom ?? 'Non définie';
        $fallbackChannel = 'sms';

        $dgUsers = User::whereIn('role', ['dg', 'admin', 'superadmin'])->get();

        foreach ($dgUsers as $dgUser) {
            if (!$dgUser instanceof User) {
                continue;
            }

            try {
                if (!empty($dgUser->email)) {
                    $approveUrl = URL::temporarySignedRoute(
                        'approvisionnement-demandes.email.approve-dg',
                        now()->addHours(self::EMAIL_ACTION_LINK_TTL_HOURS),
                        ['demande' => $demande->id, 'actor' => $dgUser->id]
                    );

                    Mail::to($dgUser->email)->send(new DynamicMail('dynamic', [
                        'subject' => "[KENAM] Validation DG requise - {$demande->numero_demande}",
                        'message' => "Une demande d'approvisionnement a ete transferee par la comptabilite et attend votre validation finale.",
                        'details' => "Demandeur: {$demande->demandeur->name}\nCaisse a approvisionner: {$caisseDestination}\nMontant: " . number_format((float) $demande->montant, 0, ',', ' ') . " {$demande->devise}\nRaison: " . str($demande->raison)->limit(300),
                        'action_url' => $approveUrl,
                        'action_text' => "Valider en tant que DG",
                    ]));

                    // Fallback SMS apres 10 min si statut toujours pending_dg.
                    $this->dispatchFallback($demande, $dgUser->id, 'pending_dg', $fallbackChannel);
                }
            } catch (\Throwable) {
            }
        }
    }

    private function extractMontantFromText(string $text): float
    {
        if (empty($text)) {
            return 0.0;
        }

        if (preg_match('/(\d[\d\s\.,]{1,20})\s*(xof|fcfa|eur|usd)?/i', $text, $matches)) {
            $value = str_replace([' ', ','], ['', '.'], trim($matches[1]));
            if (substr_count($value, '.') > 1) {
                $parts = explode('.', $value);
                $decimal = array_pop($parts);
                $value = implode('', $parts) . '.' . $decimal;
            }

            return (float) $value;
        }

        return 0.0;
    }

    private function normalizeAlertChannel(string $channel): string
    {
        return in_array($channel, ['whatsapp', 'whatsapp_appro'], true)
            ? 'whatsapp_appro'
            : 'sms';
    }

    private function dispatchFallback(ApprovisionnementDemande $demande, int $recipientUserId, string $stage, string $channel): void
    {
        SendApproNotificationFallbackJob::dispatch($demande->id, $recipientUserId, $stage, $channel)
            ->delay(now()->addMinutes(self::FALLBACK_DELAY_MINUTES));
    }

    private function getUserPhone(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        $phone = null;

        if (Schema::hasColumn('users', 'phone')) {
            $phone = $user->phone;
        }

        if (empty($phone) && Schema::hasColumn('users', 'telephone')) {
            $phone = $user->telephone;
        }

        return !empty($phone) ? (string) $phone : null;
    }

    private function collectRecipientPhonesByRoles(array $roles): array
    {
        $phoneColumn = Schema::hasColumn('users', 'phone')
            ? 'phone'
            : (Schema::hasColumn('users', 'telephone') ? 'telephone' : null);

        if (!$phoneColumn) {
            return [];
        }

        return User::whereIn('role', $roles)
            ->whereNotNull($phoneColumn)
            ->pluck($phoneColumn)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
