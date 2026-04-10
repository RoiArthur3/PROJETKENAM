<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OperationHighAmountNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $montant;
    public $demandeur;

    /**
     * Create a new message instance.
     */
    public function construct($emailData)
    {
        $this->operation = $emailData['operation'];
        $this->montant = $emailData['montant'];
        $this->demandeur = $emailData['demandeur'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        return new \Illuminate\Mail\Mailable\Envelope(
            subject: 'ALERTE: Opération avec montant élevé - ' . $this->operation->titre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new \Illuminate\Mail\Mailable\Content(
            view: 'emails.operation-high-amount',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailable\Attachment>
     */
    public function attachments()
    {
        return [];
    }
}
