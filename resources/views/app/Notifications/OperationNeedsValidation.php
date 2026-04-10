<?php

namespace App\Notifications;

use App\Models\Operation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OperationNeedsValidation extends Notification
{
    use Queueable;

    public function __construct(
        public Operation $operation,
        public int $step,
        public string $signedUrl,
        public ?string $serviceName = null,
        public ?string $destName = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'operation_validation',
            'operation_id' => $this->operation->id,
            'titre' => $this->operation->titre,
            'priorite' => $this->operation->priorite,
            'step' => $this->step,
            'service_name' => $this->serviceName,
            'destinataire_name' => $this->destName,
            'message' => 'Nouvelle requête d\'opération nécessitant votre validation: ' . $this->operation->titre,
            'url' => $this->signedUrl,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $serviceLine = $this->serviceName ? 'Service destinataire: '.$this->serviceName : null;
        $destLine = $this->destName ? 'Destinataire: '.$this->destName : null;

        // Déterminer le rôle du validateur selon le niveau
        if ($this->step == 1) {
            $validatorRole = "1er validateur";
            $validationInfo = "Cette opération suit un processus de validation à 3 niveaux. Vous êtes le 1er validateur.";
        } elseif ($this->step == 2) {
            $validatorRole = "2e validateur";
            $validationInfo = "Cette opération suit un processus de validation à 3 niveaux. Vous êtes le 2e validateur.";
        } else {
            $validatorRole = "destinataire final";
            $validationInfo = "Cette opération suit un processus de validation à 3 niveaux. Vous êtes le destinataire final (validateur principal).";
        }

        $mail = (new MailMessage)
            ->subject('Validation requise • Opération #'.$this->operation->id.' — '.$this->operation->titre)
            ->greeting('Bonjour,')
            ->line('Une opération nécessite votre validation (étape S'.$this->step.').')
            ->line('**Rôle dans le processus de validation : '.$validatorRole.'**')
            ->line($validationInfo)
            ->line('')
            ->line('**Détails de l\'opération :**')
            ->line('• Titre: '.$this->operation->titre)
            ->line('• Priorité: '.ucfirst($this->operation->priorite))
            ->line('• Votre niveau: Niveau '.$this->step.' sur 3')
            ->line('')
            ->line('**Processus de validation :**')
            ->line('• 1er validateur (Niveau 1) : Peut valider en premier')
            ->line('• 2e validateur (Niveau 2) : Peut valider après le 1er')
            ->line('• Destinataire final (Niveau 3) : Valide en dernier - PRIORITAIRE')
            ->line('')
            ->action('Ouvrir la fiche de validation', $this->signedUrl)
            ->line("Si vous n'êtes pas connecté, vous serez invité à vous connecter avant d'accéder à la fiche.")
            ->salutation('KENAM SERVICES');

        if ($serviceLine) { $mail->line($serviceLine); }
        if ($destLine) { $mail->line($destLine); }

        return $mail;
    }
}
