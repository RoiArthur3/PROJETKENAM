<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Operation;

class HighAmountOperationNotification extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public $operation;
    public $demandeur;

    public function __construct(Operation $operation, $demandeur)
    {
        $this->operation = $operation;
        $this->demandeur = $demandeur;
    }

    public function build()
    {
        return $this->subject('🚨 Opération de Montant Élevé - ' . $this->operation->titre)
                    ->view('emails.operations.high-amount')
                    ->with([
                        'operation' => $this->operation,
                        'demandeur' => $this->demandeur,
                    ]);
    }
}
