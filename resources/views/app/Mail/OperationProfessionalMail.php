<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationProfessionalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $type;
    public $recipientName;
    public $senderName;
    public $senderTitle;
    public $senderEmail;
    public $senderPhone;
    public $showActionBtn;
    public $actionUrl;
    public $actionButtonText;

    /**
     * Créer une nouvelle instance de mail professionnel.
     */
    public function __construct($operation, string $type = 'nouvelle', string $recipientName = 'Utilisateur')
    {
        $this->operation = $operation;
        $this->type = $type;
        $this->recipientName = $recipientName;

        // Informations de l'expéditeur
        $this->senderName = optional($operation->user)->name ?? 'Système KENAM';
        $this->senderTitle = optional($operation->serviceEmetteur)->nom ?? 'Service Émetteur';
        $this->senderEmail = optional($operation->user)->email ?? 'noreply@kenamservices.com';
        $this->senderPhone = '+225 XX XX XX XX XX'; // À configurer

        // Configuration du bouton d'action
        $this->showActionBtn = in_array($type, ['nouvelle', 'transfert']);
        $this->actionUrl = route('operations.show', $operation->id);
        $this->actionButtonText = $type === 'nouvelle' ? 'Voir l\'opération' : 'Consulter l\'opération';
    }

    /**
     * Obtenir le sujet de l'email selon le type.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'nouvelle' => '🔔 Nouvelle Requête Reçue - KENAM SERVICES',
            'transfert' => '🔄 Requête Transférée - KENAM SERVICES',
            'cloture' => '✅ Requête Clôturée - KENAM SERVICES',
            'validation' => '📋 Requête en Attente de Validation - KENAM SERVICES',
            'approuvee' => '✅ Requête Approuvée - KENAM SERVICES',
            'rejetee' => '❌ Requête Rejetée - KENAM SERVICES',
            default => '📧 Notification Requête - KENAM SERVICES'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Obtenir le contenu de l'email.
     */
    public function content(): Content
    {
        $subtitle = match($this->type) {
            'nouvelle' => 'Une nouvelle requête nécessite votre attention',
            'transfert' => 'Une requête a été transférée vers votre service',
            'cloture' => 'Une requête a été clôturée',
            'validation' => 'Une requête attend votre validation',
            'approuvee' => 'Une requête a été approuvée',
            'rejetee' => 'Une requête a été rejetée',
            default => 'Mise à jour d\'une requête'
        };

        $introMessage = match($this->type) {
            'nouvelle' => 'Une nouvelle requête a été soumise et requiert votre intervention. Veuillez examiner les détails ci-dessous et prendre les mesures nécessaires.',
            'transfert' => 'Une requête a été transférée vers votre service pour traitement. Merci de votre collaboration.',
            'cloture' => 'La requête référencée ci-dessous a été clôturée. Nous vous remercions pour votre contribution.',
            'validation' => 'Une requête est en attente de votre validation. Votre expertise est requise pour la suite du traitement.',
            'approuvee' => 'La requête a été approuvée et peut maintenant passer à l\'étape suivante.',
            'rejetee' => 'La requête a été rejetée. Veuillez consulter les motifs ci-dessous.',
            default => 'Une requête a fait l\'objet d\'une mise à jour.'
        };

        return new Content(
            view: 'emails.operation-professional',
            with: [
                'operation' => $this->operation,
                'type' => $this->type,
                'recipientName' => $this->recipientName,
                'senderName' => $this->senderName,
                'senderTitle' => $this->senderTitle,
                'senderEmail' => $this->senderEmail,
                'senderPhone' => $this->senderPhone,
                'showActionBtn' => $this->showActionBtn,
                'actionUrl' => $this->actionUrl,
                'actionButtonText' => $this->actionButtonText,
                'subject' => $this->envelope()->subject,
                'subtitle' => $subtitle,
                'introMessage' => $introMessage,
            ]
        );
    }

    /**
     * Pièces jointes éventuelles.
     */
    public function attachments(): array
    {
        $attachments = [];

        // Ajouter des pièces jointes si disponibles
        if ($this->operation && isset($this->operation->piece_jointe)) {
            $attachments[] = [
                'path' => storage_path('app/public/' . $this->operation->piece_jointe),
                'name' => basename($this->operation->piece_jointe),
                'mime' => mime_content_type(storage_path('app/public/' . $this->operation->piece_jointe)),
            ];
        }

        return $attachments;
    }
}
