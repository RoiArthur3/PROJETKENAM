<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendToCashierNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $caisse;

    public function __construct($operation, $caisse)
    {
        $this->operation = $operation;
        $this->caisse = $caisse;
    }

    public function build()
    {
        return $this->subject('Bon pour Execution - Opération #' . $this->operation->numero_operation)
                    ->view('emails.send-to-cashier')
                    ->with([
                        'operation' => $this->operation,
                        'caisse' => $this->caisse
                    ]);
    }
}
