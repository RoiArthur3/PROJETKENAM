<?php

namespace App\Mail;

use App\Models\Operation;
use App\Models\Validation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public Operation $operation;
    public Validation $validation;
    public string $validationUrl;
    public $demandeur;

    public function __construct(Operation $operation, Validation $validation, $demandeur)
    {
        $this->operation = $operation;
        $this->validation = $validation;
        $this->demandeur = $demandeur;
        $this->validationUrl = route('operations.valider.show', $validation);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle Requête d\'Opération - ' . $this->operation->titre,
            from: $this->demandeur->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.operation-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}