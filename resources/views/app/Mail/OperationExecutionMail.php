<?php

namespace App\Mail;

use App\Models\Operation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationExecutionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $messageCustom;
    public $markAsPaidUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Operation $operation, $messageCustom = null, $markAsPaidUrl = null)
    {
        $this->operation = $operation;
        $this->messageCustom = $messageCustom;
        $this->markAsPaidUrl = $markAsPaidUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'BON POUR ACCORD : Opération #' . $this->operation->id . ' - ' . $this->operation->titre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.operations.execution',
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
}
