<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OperationPaidNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $demandeur;

    public function __construct($operation, $demandeur)
    {
        $this->operation = $operation;
        $this->demandeur = $demandeur;
    }

    public function build()
    {
        return $this->subject('Requête exécutée : Opération #' . $this->operation->id . ' - ' . $this->operation->titre)
            ->view('emails.operations.paid-notification')
            ->with([
                'operation' => $this->operation,
                'demandeur' => $this->demandeur,
            ]);
    }
}
