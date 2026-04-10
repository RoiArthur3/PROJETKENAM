<?php

namespace App\Jobs;

use App\Models\ApprovisionnementDemande;
use App\Models\User;
use App\Services\SmsService;
use App\Services\WhatsAppWebJsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SendApproNotificationFallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $demandeId,
        private int $recipientUserId,
        private string $stage,
        private string $channel = 'sms'
    ) {
    }

    public function handle(): void
    {
        $demande = ApprovisionnementDemande::with(['demandeur', 'caisseDestination'])->find($this->demandeId);
        $recipient = User::find($this->recipientUserId);

        if (!$demande || !$recipient) {
            return;
        }

        if (!$this->shouldSendFallback($demande)) {
            Log::info('Fallback appro ignore: demande deja traitee', [
                'demande_id' => $this->demandeId,
                'stage' => $this->stage,
                'statut' => $demande->statut,
            ]);
            return;
        }

        $phone = $this->getUserPhone($recipient);
        if (empty($phone)) {
            return;
        }

        $caisseDestination = $demande->caisseDestination?->nom ?? 'Non definie';

        $message = $this->stage === 'pending_dg'
            ? "KENAM | Rappel validation DG {$demande->numero_demande}\nDemandeur : {$demande->demandeur?->name}\nCaisse : {$caisseDestination}\nAction attendue : valider ou refuser."
            : "KENAM | Rappel traitement Compta {$demande->numero_demande}\nDemandeur : {$demande->demandeur?->name}\nCaisse : {$caisseDestination}\nAction attendue : faire suivre a la DG.";

        $channel = $this->normalizeChannel($this->channel);
        if ($channel === 'whatsapp_appro') {
            $result = app(WhatsAppWebJsService::class)->sendMessage($phone, $message, 'whatsapp_appro');
            if (!($result['success'] ?? false)) {
                SmsService::send($phone, $message);
            }
            return;
        }

        SmsService::send($phone, $message);
    }

    private function normalizeChannel(string $channel): string
    {
        return in_array($channel, ['sms', 'whatsapp_appro'], true) ? $channel : 'sms';
    }

    private function shouldSendFallback(ApprovisionnementDemande $demande): bool
    {
        return match ($this->stage) {
            'pending' => $demande->isPending(),
            'pending_dg' => $demande->isPendingDg(),
            default => false,
        };
    }

    private function getUserPhone(User $user): ?string
    {
        $phone = null;

        if (Schema::hasColumn('users', 'phone')) {
            $phone = $user->phone;
        }

        if (empty($phone) && Schema::hasColumn('users', 'telephone')) {
            $phone = $user->telephone;
        }

        return !empty($phone) ? (string) $phone : null;
    }
}
