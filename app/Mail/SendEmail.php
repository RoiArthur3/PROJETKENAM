<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $url;

    public function __construct($operation, $url)
    {
        $this->operation = $operation;
        $this->url = $url;
    }

    public function build()
    {
        // Récupérer les paramètres de l'entreprise sans bloquer si la table n'existe pas
        try {
            $entrepriseSettings = \App\Models\EntrepriseSettings::getActive();
        } catch (\Throwable $e) {
            \Log::warning('EntrepriseSettings introuvable pour SendEmail: ' . $e->getMessage());
            $entrepriseSettings = null;
        }

        return $this->subject('Nouvelle Opération à Valider')
            ->view('emails.operations.validation-request')
            ->with([
                'operation' => $this->operation,
                'url' => $this->url,
                'fichiers' => $this->operation->fichiers ?? collect([]),
                'entrepriseSettings' => $entrepriseSettings,
            ]);
    }
}

