<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $serviceOperationnel;
    public $demandeur;

    public function __construct($emailData)
    {
        $this->operation = $emailData['operation'];
        $this->serviceOperationnel = $emailData['serviceOperationnel'];
        $this->demandeur = $emailData['demandeur'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle opération assignée - ' . $this->operation->titre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.operation-created-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
