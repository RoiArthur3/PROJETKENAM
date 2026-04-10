<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OperationValidationRequest extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public $operation;
    public $typeNotification;

    public function __construct($operation, $typeNotification = 'validation')
    {
        $this->operation = $operation;
        $this->typeNotification = $typeNotification;
    }

    public function build()
    {
        if ($this->typeNotification === 'validation') {
            $subject = '📋 Nouvelle Opération à Valider - ' . $this->operation->titre;
        } else {
            $subject = 'ℹ️ Information: Opération - ' . $this->operation->titre;
        }

        return $this->subject($subject)
                    ->view('emails.operations.validation-request')
                    ->with([
                        'operation' => $this->operation,
                        'url' => url('/login?redirect=' . urlencode('/operations/' . $this->operation->id)),
                    ]);
    }
}
