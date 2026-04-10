<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Operation;
use App\Models\ServiceOperationnel;

class OperationHighAmountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $destinataireType;
    public $servicesAdditionnels;
    public $serviceConcerne;

    /**
     * Create a new message instance.
     */
    public function __construct(Operation $operation, string $destinataireType, array $servicesAdditionnels = [], ServiceOperationnel $serviceConcerne = null)
    {
        $this->operation = $operation;
        $this->destinataireType = $destinataireType;
        $this->servicesAdditionnels = $servicesAdditionnels;
        $this->serviceConcerne = $serviceConcerne;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->destinataireType) {
            'dg' => '🔴 URGENT - Opération Montant Élevé - Validation DG Requise',
            'manager' => '⚠️ VALIDATION REQUISE - Opération Montant Élevé',
            'service' => '📋 Information Opération - Montant Élevé',
            default => '📧 Notification Opération - KENAM SERVICES'
        };

        return new Envelope(
            subject: $subject,
            from: 'noreply@kenamservices.com',
            replyTo: config('app.mail_contact', 'contact@kenamservices.com')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.operation-high-amount',
            with: [
                'operation' => $this->operation,
                'destinataireType' => $this->destinataireType,
                'servicesAdditionnels' => $this->servicesAdditionnels,
                'serviceConcerne' => $this->serviceConcerne,
                'titre' => $this->getTitre(),
                'messagePrincipal' => $this->getMessagePrincipal(),
                'instructions' => $this->getInstructions(),
                'niveauUrgence' => $this->getNiveauUrgence(),
                'couleurUrgence' => $this->getCouleurUrgence(),
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
     * Get the title based on recipient type.
     */
    private function getTitre(): string
    {
        return match($this->destinataireType) {
            'dg' => 'VALIDATION DG - Opération Montant Élevé',
            'manager' => 'VALIDATION MANAGER - Opération Montant Élevé',
            'service' => 'INFORMATION SERVICE - Opération Montant Élevé',
            default => 'NOTIFICATION OPÉRATION'
        };
    }

    /**
     * Get the main message based on recipient type.
     */
    private function getMessagePrincipal(): string
    {
        $montant = number_format($this->operation->montant, 0, ',', ' ') . ' FCFA';

        return match($this->destinataireType) {
            'dg' => "Une opération d'un montant de <strong>{$montant}</strong> requiert votre validation immédiate. Ce montant dépasse le seuil autorisé de 1.000.000 FCFA.",
            'manager' => "Une opération d'un montant de <strong>{$montant}</strong> requiert votre validation. Cette opération a également été transmise au DG pour validation finale.",
            'service' => "Une opération d'un montant de <strong>{$montant}</strong> a été initiée et requiert une validation spéciale. Cette information vous est transmise pour suivi.",
            default => "Une opération d'un montant de <strong>{$montant}</strong> requiert une attention particulière."
        };
    }

    /**
     * Get instructions based on recipient type.
     */
    private function getInstructions(): array
    {
        $baseInstructions = [
            'Référence opération : ' . $this->operation->reference,
            'Montant : ' . number_format($this->operation->montant, 0, ',', ' ') . ' FCFA',
            'Date : ' . $this->operation->date_operation->format('d/m/Y H:i'),
            'Initiateur : ' . ($this->operation->initiateur ?? 'Non spécifié'),
        ];

        $specificInstructions = match($this->destinataireType) {
            'dg' => [
                'ACTION REQUISE : Veuillez valider ou rejeter cette opération',
                'Vous pouvez accéder au système via le lien ci-dessous',
                'Cette opération requiert une attention immédiate',
                'Les services additionnels ont été notifiés'
            ],
            'manager' => [
                'ACTION REQUISE : Veuillez examiner cette opération',
                'Préparez les documents justificatifs nécessaires',
                'Le DG a été notifié pour validation finale',
                'Suivez l\'évolution via le tableau de bord'
            ],
            'service' => [
                'INFORMATION : Cette opération est en cours de validation',
                'Veuillez préparer les éléments nécessaires pour intervention',
                'Vous serez notifié de la décision finale',
                'Consulter les détails via votre espace'
            ],
            default => [
                'Veuillez consulter les détails de cette opération',
                'Suivez son évolution dans le système',
                'Contactez le support si nécessaire'
            ]
        };

        return array_merge($baseInstructions, $specificInstructions);
    }

    /**
     * Get urgency level.
     */
    private function getNiveauUrgence(): string
    {
        return match($this->destinataireType) {
            'dg' => 'URGENTE',
            'manager' => 'HAUTE',
            'service' => 'MOYENNE',
            default => 'NORMALE'
        };
    }

    /**
     * Get urgency color.
     */
    private function getCouleurUrgence(): string
    {
        return match($this->destinataireType) {
            'dg' => '#dc3545',    // Rouge
            'manager' => '#fd7e14', // Orange
            'service' => '#ffc107',  // Jaune
            default => '#007bff'     // Bleu
        };
    }
}
