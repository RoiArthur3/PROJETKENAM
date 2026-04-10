<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ValidationStepNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
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
        $stepText = '';
        if ($this->data['step'] == 1) {
            $stepText = 'Première validation';
        } elseif ($this->data['step'] == 2) {
            $stepText = 'Deuxième validation';
        } elseif ($this->data['step'] == 3) {
            $stepText = 'Validation finale';
        }

        // Récupérer les paramètres de l'entreprise
        try {
            $entrepriseSettings = \App\Models\EntrepriseSettings::getActive();
            $entrepriseName = $entrepriseSettings->nom_entreprise ?? 'KENAM SERVICES';
        } catch (\Throwable $e) {
            $entrepriseSettings = null;
            $entrepriseName = 'KENAM SERVICES';
        }

        return $this->subject($entrepriseName . ' - ' . $stepText . ' requise pour l\'opération ' . $this->data['operationRef'])
                    ->view('emails.validation-step-notification')
                    ->with([
                        'operation' => $this->data['operation'],
                        'step' => $this->data['step'],
                        'stepText' => $stepText,
                        'service' => $this->data['service'] ?? null,
                        'validatorName' => $this->data['validatorName'],
                        'operationTitle' => $this->data['operationTitle'],
                        'operationAmount' => $this->data['operationAmount'],
                        'operationRef' => $this->data['operationRef'],
                        'validationUrl' => $this->data['validationUrl'],
                        'entrepriseSettings' => $entrepriseSettings,
                        'previousComment' => $this->data['previousComment'] ?? ''
                    ]);
    }
}
