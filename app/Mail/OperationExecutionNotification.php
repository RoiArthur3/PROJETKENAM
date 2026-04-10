<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OperationExecutionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Récupérer les paramètres de l'entreprise (peut être null)
        $entrepriseSettings = $this->data['entrepriseSettings'] ?? null;
        $entrepriseName = $entrepriseSettings->nom_entreprise ?? 'KENAM SERVICES';

        return $this->subject('BON POUR ACCORD - ' . $entrepriseName . ' : ' . $this->data['operationTitle'])
                    ->view('emails.operations.execution')
                    ->with([
                        'operation' => $this->data['operation'],
                        'messageCustom' => $this->data['message'] ?? null,
                        'recipientEmail' => $this->data['recipientEmail'],
                        'operationUrl' => $this->data['operationUrl'] ?? '#',
                        'operationRef' => $this->data['operationRef'],
                        'operationTitle' => $this->data['operationTitle'],
                        'operationAmount' => $this->data['operationAmount'],
                        'entrepriseSettings' => $entrepriseSettings
                    ]);
    }
}
