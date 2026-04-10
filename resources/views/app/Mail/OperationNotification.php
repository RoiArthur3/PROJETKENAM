<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $emetteur;
    public $serviceEmetteur;
    public $serviceDestinataire;
    public $type;

    public function __construct($data, string $type = 'nouvelle')
    {
        $this->operation = $data['operation'];
        $this->emetteur = $data['emetteur'];
        $this->serviceEmetteur = $data['serviceEmetteur'];
        $this->serviceDestinataire = $data['serviceDestinataire'];
        $this->type = $type;
    }

    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'nouvelle' => 'Nouvelle requête reçue',
            'transfert' => 'Requête transférée',
            'cloture' => 'Requête clôturée',
            default => 'Notification requête'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.operation-notification',
            with: [
                'operation' => $this->operation,
                'emetteur' => $this->emetteur,
                'serviceEmetteur' => $this->serviceEmetteur,
                'serviceDestinataire' => $this->serviceDestinataire,
                'type' => $this->type,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
