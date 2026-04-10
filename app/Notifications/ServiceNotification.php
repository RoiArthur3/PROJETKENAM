<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $message, string $type = 'info')
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match($this->type) {
            'info' => '📢 Information Service - KENAM SERVICES',
            'warning' => '⚠️ Attention Service - KENAM SERVICES',
            'error' => '❌ Erreur Service - KENAM SERVICES',
            'success' => '✅ Succès Service - KENAM SERVICES',
            default => '📧 Notification Service - KENAM SERVICES'
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour ' . ($notifiable->name ?? 'Utilisateur'))
            ->line($this->message)
            ->line('Ceci est une notification automatique du service.')
            ->action('Accéder à la plateforme', url('/dashboard'))
            ->line('Merci d\'utiliser la plateforme KENAM SERVICES!')
            ->salutation('Cordialement, L\'équipe KENAM SERVICES');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'service' => class_basename($notifiable),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'created_at' => now(),
        ];
    }

    /**
     * Get icon based on notification type.
     */
    private function getIcon(): string
    {
        return match($this->type) {
            'info' => 'fas fa-info-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'error' => 'fas fa-times-circle',
            'success' => 'fas fa-check-circle',
            default => 'fas fa-bell'
        };
    }

    /**
     * Get color based on notification type.
     */
    private function getColor(): string
    {
        return match($this->type) {
            'info' => '#007bff',
            'warning' => '#ffc107',
            'error' => '#dc3545',
            'success' => '#28a745',
            default => '#6c757d'
        };
    }
}
