<?php

namespace App\Notifications;

use App\Models\Validation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ValidationCompleted extends Notification
{
    use Queueable;

    public function __construct(public Validation $validation, public string $decision) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $statusText = match($this->decision) {
            'approve' => 'approuvée',
            'reject' => 'rejetée',
            'correct' => 'retournée pour correction',
            default => 'traitée'
        };

        $message = (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Votre demande a été ' . $statusText . ' - ' . $this->validation->titre)
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Votre demande de validation a été ' . $statusText . '.')
            ->line('Module : ' . ucfirst($this->validation->module_source))
            ->line('Type : ' . $this->validation->type)
            ->line('Titre : ' . $this->validation->titre)
            ->line('Décision : ' . ucfirst($statusText));

        if ($this->validation->commentaire) {
            $message->line('Commentaire : ' . $this->validation->commentaire);
        }

        $message->action('Voir les détails', url('/validations/' . $this->validation->id));

        return $message;
    }

    public function toArray($notifiable): array
    {
        $statusText = match($this->decision) {
            'approve' => 'approuvée',
            'reject' => 'rejetée',
            'correct' => 'retournée pour correction',
            default => 'traitée'
        };

        return [
            'validation_id' => $this->validation->id,
            'titre' => $this->validation->titre,
            'module' => $this->validation->module_source,
            'decision' => $this->decision,
            'message' => 'Votre demande a été ' . $statusText,
        ];
    }
}
