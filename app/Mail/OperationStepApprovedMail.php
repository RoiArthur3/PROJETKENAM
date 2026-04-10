<?php

namespace App\Mail;

use App\Models\Operation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationStepApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $validator;
    public $commentaire;
    public $nextService;

    /**
     * Create a new message instance.
     */
    public function __construct(Operation $operation, $validator, $commentaire, $nextService = null)
    {
        $this->operation = $operation;
        $this->validator = $validator;
        $this->commentaire = $commentaire;
        $this->nextService = $nextService;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Évolution de votre opération #' . $this->operation->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.operations.step-approved',
        );
    }
}
