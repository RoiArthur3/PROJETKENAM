<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OperationClosureNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;

    public function __construct($operation)
    {
        $this->operation = $operation;
    }

    public function build()
    {
        return $this->subject('Opération Clôturée - #' . $this->operation->numero_operation)
                    ->view('emails.operation-closure')
                    ->with([
                        'operation' => $this->operation,
                        'user' => $this->operation->user
                    ]);
    }
}
