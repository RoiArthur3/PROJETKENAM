<?php

namespace App\Mail;

use App\Models\Operation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $commentaire;

    /**
     * Create a new message instance.
     */
    public function __construct(Operation $operation, $commentaire)
    {
        $this->operation = $operation;
        $this->commentaire = $commentaire;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Opération #' . $this->operation->id . ' rejetée',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.operations.rejected',
        );
    }
}
