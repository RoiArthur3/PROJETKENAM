<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ServiceOperationnel;

class ServiceConfigurationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $service;
    public $typeNotification;

    /**
     * Create a new message instance.
     */
    public function __construct(ServiceOperationnel $service, string $typeNotification = 'configuration')
    {
        $this->service = $service;
        $this->typeNotification = $typeNotification;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->typeNotification) {
            'configuration' => '🔧 Configuration Service - KENAM SERVICES',
            'test' => '✅ Test Email - KENAM SERVICES',
            'reinitialisation' => '🔄 Réinitialisation Mot de Passe - KENAM SERVICES',
            default => '📧 Notification Service - KENAM SERVICES'
        };

        return new Envelope(
            subject: $subject,
            from: 'noreply@kenamservices.com',
            replyTo: $this->service->email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.service-configuration',
            with: [
                'service' => $this->service,
                'typeNotification' => $this->typeNotification,
                'titre' => $this->getTitre(),
                'message' => $this->getMessage(),
                'instructions' => $this->getInstructions(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get the title based on notification type.
     */
    private function getTitre(): string
    {
        return match($this->typeNotification) {
            'configuration' => 'Configuration du Service',
            'test' => 'Test de Configuration Email',
            'reinitialisation' => 'Réinitialisation du Mot de Passe',
            default => 'Notification du Service'
        };
    }

    /**
     * Get the message based on notification type.
     */
    private function getMessage(): string
    {
        return match($this->typeNotification) {
            'configuration' => "Votre service <strong>{$this->service->nom}</strong> a été configuré avec succès dans le système KENAM SERVICES.",
            'test' => "Ceci est un email de test pour vérifier la configuration du service <strong>{$this->service->nom}</strong>.",
            'reinitialisation' => "Le mot de passe du service <strong>{$this->service->nom}</strong> a été réinitialisé.",
            default => "Une notification a été envoyée pour le service <strong>{$this->service->nom}</strong>."
        };
    }

    /**
     * Get instructions based on notification type.
     */
    private function getInstructions(): array
    {
        return match($this->typeNotification) {
            'configuration' => [
                'Email du service : ' . $this->service->email,
                'Responsable : ' . ($this->service->responsable ?? 'Non défini'),
                'Téléphone : ' . ($this->service->telephone ?? 'Non défini'),
                'Le service peut maintenant recevoir et envoyer des requêtes.'
            ],
            'test' => [
                'Email de configuration : ' . $this->service->email,
                'Test réussi : La configuration SMTP est correcte',
                'Le service est prêt à recevoir les notifications.'
            ],
            'reinitialisation' => [
                'Nouveau mot de passe : 12345678',
                'Email du service : ' . $this->service->email,
                'Veuillez changer ce mot de passe lors de la première connexion.'
            ],
            default => [
                'Service : ' . $this->service->nom,
                'Email : ' . $this->service->email,
                'Contactez le support si nécessaire.'
            ]
        };
    }
}
