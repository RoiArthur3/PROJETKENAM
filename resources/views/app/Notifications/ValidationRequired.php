<?php

namespace App\Notifications;

use App\Models\Validation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ValidationRequired extends Notification
{
    use Queueable;

    public function __construct(public Validation $validation) {}

    public function via($notifiable): array
    {
        // Canal 'database' désactivé car la table notifications n'est pas utilisée dans cette application
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Nouvelle demande de validation - ' . $this->validation->titre)
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Une nouvelle demande requiert votre validation.')
            ->line('Module : ' . ucfirst($this->validation->module_source))
            ->line('Type : ' . $this->validation->type)
            ->line('Titre : ' . $this->validation->titre)
            ->line('Description : ' . ($this->validation->description ?? 'Aucune'))
            ->action('Voir la demande', url('/validations/' . $this->validation->id))
            ->line('Merci de traiter cette demande dans les plus brefs délais.');
    }

    public function toArray($notifiable): array
    {
        return [
            'validation_id' => $this->validation->id,
            'titre' => $this->validation->titre,
            'module' => $this->validation->module_source,
            'message' => 'Nouvelle demande de validation requiert votre attention',
        ];
    }
}
