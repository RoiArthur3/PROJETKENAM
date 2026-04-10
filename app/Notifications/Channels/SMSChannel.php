<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\SMS\SMSManager;
use Illuminate\Support\Facades\Log;

class SMSChannel
{
    /**
     * Envoyer la notification via SMS
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$notifiable->telephone) {
            Log::warning('SMSChannel: Notifiable n\'a pas de numéro de téléphone', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id ?? null
            ]);
            return;
        }

        try {
            // Récupérer le contenu SMS depuis la notification
            $message = $notification->toSMS($notifiable);
            
            if (!$message) {
                Log::warning('SMSChannel: La notification n\'a pas de méthode toSMS()');
                return;
            }

            // Envoyer via le SMSManager
            $result = app(SMSManager::class)->sendTemplate(
                $notifiable->telephone,
                $message['template'] ?? 'notification',
                $message['data'] ?? []
            );

            if (($result['success'] ?? false) === true) {
                Log::info('SMS envoyé avec succès', [
                    'to' => $notifiable->telephone,
                    'template' => $message['template'] ?? 'notification',
                    'notification_type' => get_class($notification)
                ]);
            } else {
                Log::error('Échec envoi SMS', [
                    'to' => $notifiable->telephone,
                    'error' => $result['error'] ?? 'Erreur inconnue',
                    'notification_type' => get_class($notification)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erreur SMSChannel: ' . $e->getMessage(), [
                'to' => $notifiable->telephone,
                'notification_type' => get_class($notification)
            ]);
        }
    }
}
