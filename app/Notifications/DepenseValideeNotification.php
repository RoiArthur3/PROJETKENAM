<?php

namespace App\Notifications;

use App\Models\DepenseCaisse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepenseValideeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public DepenseCaisse $depense)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Votre dépense a été validée.')
            ->line('Montant: ' . $this->depense->montant . ' GHS')
            ->action('Voir détails', url('/depenses-caisse/' . $this->depense->id))
            ->line('Merci!');
    }
}
