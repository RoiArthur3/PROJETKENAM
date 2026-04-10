<?php

namespace App\Notifications;

use App\Models\DepenseCaisse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepenseRejeteeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public DepenseCaisse $depense, public string $raison = '')
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->line('Votre dépense a été rejetée.')
            ->line('Montant: ' . $this->depense->montant . ' GHS');
        
        if ($this->raison) {
            $message->line('Raison: ' . $this->raison);
        }
        
        return $message
            ->action('Voir détails', url('/depenses-caisse/' . $this->depense->id))
            ->line('Veuillez corriger et réessayer.');
    }
}
