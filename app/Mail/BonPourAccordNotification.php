<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BonPourAccordNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $commentaire;

    public function __construct($operation, $commentaire = null)
    {
        $this->operation = $operation;
        $this->commentaire = $commentaire;
    }

    public function build()
    {
        return $this->subject('BON POUR ACCORD - Opération #' . $this->operation->id)
                    ->view('emails.operations.bon-pour-accord')
                    ->with([
                        'operation' => $this->operation,
                        'commentaire' => $this->commentaire,
                    ]);
    }
}
