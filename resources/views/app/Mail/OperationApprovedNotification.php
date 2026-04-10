<?php

namespace App\Mail;

use App\Models\Operation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OperationApprovedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $comment;

    /**
     * Create a new message instance.
     *
     * @param Operation $operation
     * @param string|null $comment
     */
    public function __construct(Operation $operation, $comment = null)
    {
        $this->operation = $operation;
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Récupérer les paramètres de l'entreprise (peut être null)
        $entrepriseSettings = null;
        try {
            $entrepriseSettings = \App\Models\EntrepriseSettings::getActive();
        } catch (\Exception $e) {}
        
        $entrepriseName = $entrepriseSettings->nom_entreprise ?? 'KENAM SERVICES';

        return $this->subject($entrepriseName . ' - Opération Approuvée : ' . ($this->operation->objet ?? 'Sans titre'))
                    ->view('emails.operation-approved-notification')
                    ->with([
                        'operation' => $this->operation,
                        'recipientName' => $this->operation->demandeur_name ?? 'Collaborateur',
                        'operationRef' => $this->operation->numero_ordre ?? ('#OP-' . $this->operation->id),
                        'operationAmount' => number_format($this->operation->montant, 0, ',', ' ') . ' FCFA',
                        'operationTitle' => $this->operation->objet ?? 'Sans titre',
                        'operationUrl' => url('/operations/' . $this->operation->id),
                        'finalComment' => $this->comment,
                        'recipientService' => $this->operation->operationalService?->nom ?? 'N/A',
                        'validationCompleted' => true,
                        'entrepriseSettings' => $entrepriseSettings
                    ]);
    }
}
