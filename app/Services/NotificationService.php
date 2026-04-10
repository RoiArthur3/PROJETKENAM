<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\Parcel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ParcelNotification;

class NotificationService
{
    private $whatsappService;

    public function __construct()
    {
        // Initialiser les services de notification
        $this->whatsappService = new \App\Services\WhatsAppService();
    }

    /**
     * Envoyer une notification pour un événement de colis
     */
    public function notifyParcelEvent(Parcel $parcel, string $event)
    {
        $user = $parcel->user;
        if (!$user) {
            return;
        }

        $preferences = $user->notificationPreference;
        if (!$preferences) {
            return;
        }

        $messages = $this->getEventMessages($event, $parcel);

        // Envoyer SMS si activé
        if ($preferences->isSmsEventEnabled($event)) {
            $this->sendSmsNotification($user, $parcel, $event, $messages['sms']);
        }

        // Envoyer WhatsApp si activé
        if ($preferences->isWhatsAppEventEnabled($event)) {
            $this->sendWhatsAppNotification($user, $parcel, $event, $messages['whatsapp'] ?? $messages['sms']);
        }

        // Envoyer email si activé
        if ($preferences->isEmailEventEnabled($event)) {
            $this->sendEmailNotification($user, $parcel, $event, $messages['email']);
        }
    }

    /**
     * Envoyer une notification WhatsApp
     */
    private function sendWhatsAppNotification(User $user, Parcel $parcel, string $event, array $message)
    {
        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'parcel_id' => $parcel->id,
                'type' => 'whatsapp',
                'event' => $event,
                'title' => $message['title'],
                'message' => $message['body'],
                'recipient' => $user->notificationPreference->whatsapp_number,
                'status' => 'pending',
            ]);

            $result = $this->whatsappService->send(
                $user->notificationPreference->whatsapp_number,
                $message['body']
            );

            $result = $this->normalizeDeliveryResult($result, 'WhatsApp provider returned an invalid response.');

            if ($result['success']) {
                $notification->markAsSent();
                Log::info('WhatsApp sent successfully', [
                    'user_id' => $user->id,
                    'parcel_id' => $parcel->id,
                    'event' => $event,
                ]);
            } else {
                $notification->markAsFailed($result['error']);
                Log::error('WhatsApp sending failed', [
                    'user_id' => $user->id,
                    'parcel_id' => $parcel->id,
                    'event' => $event,
                    'error' => $result['error'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification error', [
                'user_id' => $user->id,
                'parcel_id' => $parcel->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification SMS
     */
    private function sendSmsNotification(User $user, Parcel $parcel, string $event, array $message)
    {
        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'parcel_id' => $parcel->id,
                'type' => 'sms',
                'event' => $event,
                'title' => $message['title'],
                'message' => $message['body'],
                'recipient' => $user->notificationPreference->phone_number,
                'status' => 'pending',
            ]);

            $result = SmsService::send(
                $user->notificationPreference->phone_number,
                $message['body']
            );

            $result = $this->normalizeDeliveryResult($result, 'SMS provider rejected the message.');

            if ($result['success']) {
                $notification->markAsSent();
                Log::info('SMS sent successfully', [
                    'user_id' => $user->id,
                    'parcel_id' => $parcel->id,
                    'event' => $event,
                ]);
            } else {
                $notification->markAsFailed($result['error']);
                Log::error('SMS sending failed', [
                    'user_id' => $user->id,
                    'parcel_id' => $parcel->id,
                    'event' => $event,
                    'error' => $result['error'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SMS notification error', [
                'user_id' => $user->id,
                'parcel_id' => $parcel->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification email
     */
    private function sendEmailNotification(User $user, Parcel $parcel, string $event, array $message)
    {
        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'parcel_id' => $parcel->id,
                'type' => 'email',
                'event' => $event,
                'title' => $message['title'],
                'message' => $message['body'],
                'recipient' => $user->email,
                'status' => 'pending',
            ]);

            // Utiliser le système d'email Laravel
            Mail::to($user->email)->send(new ParcelNotification($parcel, $event, $message));

            $notification->markAsSent();
            Log::info('Email sent successfully', [
                'user_id' => $user->id,
                'parcel_id' => $parcel->id,
                'event' => $event,
            ]);
        } catch (\Exception $e) {
            if (isset($notification)) {
                $notification->markAsFailed($e->getMessage());
            }
            Log::error('Email notification error', [
                'user_id' => $user->id,
                'parcel_id' => $parcel->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Uniformiser les retours des fournisseurs SMS/WhatsApp.
     */
    private function normalizeDeliveryResult(bool|array $result, string $defaultError): array
    {
        if (is_array($result)) {
            return [
                'success' => (bool) ($result['success'] ?? false),
                'error' => $result['error'] ?? $defaultError,
            ];
        }

        return [
            'success' => $result,
            'error' => $result ? null : $defaultError,
        ];
    }

    /**
     * Obtenir les messages pour chaque événement
     */
    private function getEventMessages(string $event, Parcel $parcel): array
    {
        $trackingNumber = $parcel->tracking_number;
        $messages = [
            'parcel_declared' => [
                'sms' => [
                    'title' => 'Colis déclaré',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} a été déclaré. Suivez son évolution en ligne.",
                ],
                'whatsapp' => [
                    'title' => 'Colis déclaré',
                    'body' => "📦 GROUPAGE PRO\n\nVotre colis {$trackingNumber} a été déclaré avec succès.\n\nSuivez son évolution en temps réel sur votre espace client.",
                ],
                'email' => [
                    'title' => 'Votre colis a été déclaré',
                    'body' => "Votre colis {$trackingNumber} a été déclaré avec succès. Vous pouvez suivre son évolution dans votre espace client.",
                ],
            ],
            'parcel_received' => [
                'sms' => [
                    'title' => 'Colis reçu',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} a été reçu à notre entrepôt. Préparation en cours.",
                ],
                'whatsapp' => [
                    'title' => 'Colis reçu',
                    'body' => "📦 GROUPAGE PRO\n\n✅ Bonne nouvelle !\n\nVotre colis {$trackingNumber} a été reçu à notre entrepôt.\n\n🔄 Préparation en cours pour l'expédition.",
                ],
                'email' => [
                    'title' => 'Votre colis a été reçu',
                    'body' => "Votre colis {$trackingNumber} a été reçu à notre entrepôt et est en cours de préparation pour l'expédition.",
                ],
            ],
            'parcel_inspected' => [
                'sms' => [
                    'title' => 'Colis inspecté',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} a passé l'inspection avec succès.",
                ],
                'whatsapp' => [
                    'title' => 'Colis inspecté',
                    'body' => "📦 GROUPAGE PRO\n\n✅ Inspection validée !\n\nVotre colis {$trackingNumber} a passé l'inspection qualité avec succès.\n\n📋 Prochaine étape: Groupage pour expédition.",
                ],
                'email' => [
                    'title' => 'Votre colis a été inspecté',
                    'body' => "Votre colis {$trackingNumber} a passé l'inspection qualité avec succès. Il sera bientôt groupé pour expédition.",
                ],
            ],
            'parcel_grouped' => [
                'sms' => [
                    'title' => 'Colis groupé',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} est groupé pour expédition. Départ imminent.",
                ],
                'whatsapp' => [
                    'title' => 'Colis groupé',
                    'body' => "📦 GROUPAGE PRO\n\n🚀 Bientôt parti !\n\nVotre colis {$trackingNumber} est groupé avec d'autres colis pour l'expédition.\n\n✈️ Départ imminent vers {$parcel->destination_country}.",
                ],
                'email' => [
                    'title' => 'Votre colis est groupé pour expédition',
                    'body' => "Votre colis {$trackingNumber} a été groupé avec d'autres colis pour l'expédition. Il partira prochainement.",
                ],
            ],
            'parcel_shipped' => [
                'sms' => [
                    'title' => 'Colis expédié',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} a été expédié! Suivez son trajet en ligne.",
                ],
                'whatsapp' => [
                    'title' => 'Colis expédié',
                    'body' => "📦 GROUPAGE PRO\n\n✈️ EN ROUTE !\n\nVotre colis {$trackingNumber} a été expédié !\n\n📍 Suivez son trajet en temps réel sur votre espace client.",
                ],
                'email' => [
                    'title' => 'Votre colis a été expédié',
                    'body' => "Votre colis {$trackingNumber} a été expédié et est en route vers sa destination. Vous pouvez suivre son évolution en temps réel.",
                ],
            ],
            'parcel_in_transit' => [
                'sms' => [
                    'title' => 'Colis en transit',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} est en transit vers {$parcel->destination_country}.",
                ],
                'whatsapp' => [
                    'title' => 'Colis en transit',
                    'body' => "📦 GROUPAGE PRO\n\n🌍 En transit !\n\nVotre colis {$trackingNumber} est actuellement en transit vers {$parcel->destination_country}.\n\n📊 Suivez sa progression en ligne.",
                ],
                'email' => [
                    'title' => 'Votre colis est en transit',
                    'body' => "Votre colis {$trackingNumber} est actuellement en transit vers {$parcel->destination_country}. Suivez sa progression en ligne.",
                ],
            ],
            'parcel_arrived' => [
                'sms' => [
                    'title' => 'Colis arrivé',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} est arrivé à destination! En attente de douane.",
                ],
                'whatsapp' => [
                    'title' => 'Colis arrivé',
                    'body' => "📦 GROUPAGE PRO\n\n🎉 Arrivé à destination !\n\nVotre colis {$trackingNumber} est arrivé dans le pays de destination.\n\n📋 En attente de passage en douane.",
                ],
                'email' => [
                    'title' => 'Votre colis est arrivé à destination',
                    'body' => "Votre colis {$trackingNumber} est arrivé dans le pays de destination. Il est actuellement en attente de passage en douane.",
                ],
            ],
            'parcel_customs' => [
                'sms' => [
                    'title' => 'Colis en douane',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} est en douane. Frais calculés: XX€.",
                ],
                'whatsapp' => [
                    'title' => 'Colis en douane',
                    'body' => "📦 GROUPAGE PRO\n\n📋 Passage en douane\n\nVotre colis {$trackingNumber} est actuellement en douane.\n\n💰 Les frais de douane ont été calculés et sont disponibles dans votre espace client.",
                ],
                'email' => [
                    'title' => 'Votre colis est en douane',
                    'body' => "Votre colis {$trackingNumber} est actuellement en douane. Les frais de douane ont été calculés et sont disponibles dans votre espace client.",
                ],
            ],
            'parcel_delivered' => [
                'sms' => [
                    'title' => 'Colis livré',
                    'body' => "GROUPAGE PRO: Votre colis {$trackingNumber} a été livré! Merci de votre confiance.",
                ],
                'whatsapp' => [
                    'title' => 'Colis livré',
                    'body' => "📦 GROUPAGE PRO\n\n🎉 LIVRÉ !\n\nVotre colis {$trackingNumber} a été livré avec succès !\n\n🙏 Merci d'avoir utilisé nos services. N'hésitez pas à nous laisser votre avis !",
                ],
                'email' => [
                    'title' => 'Votre colis a été livré',
                    'body' => "Votre colis {$trackingNumber} a été livré avec succès. Merci d'avoir utilisé nos services. N'hésitez pas à nous laisser votre avis!",
                ],
            ],
            'payment_required' => [
                'sms' => [
                    'title' => 'Paiement requis',
                    'body' => "GROUPAGE PRO: Paiement requis pour colis {$trackingNumber}. Montant: XX€. Connectez-vous.",
                ],
                'whatsapp' => [
                    'title' => 'Paiement requis',
                    'body' => "📦 GROUPAGE PRO\n\n💰 Paiement requis\n\nUn paiement est requis pour votre colis {$trackingNumber}.\n\n👉 Connectez-vous à votre espace client pour régler les frais.",
                ],
                'email' => [
                    'title' => 'Paiement requis pour votre colis',
                    'body' => "Un paiement est requis pour votre colis {$trackingNumber}. Connectez-vous à votre espace client pour régler les frais.",
                ],
            ],
            'payment_confirmed' => [
                'sms' => [
                    'title' => 'Paiement confirmé',
                    'body' => "GROUPAGE PRO: Paiement confirmé pour colis {$trackingNumber}. Livraison en cours.",
                ],
                'whatsapp' => [
                    'title' => 'Paiement confirmé',
                    'body' => "📦 GROUPAGE PRO\n\n✅ Paiement confirmé !\n\nVotre paiement pour le colis {$trackingNumber} a été reçu.\n\n🚚 La livraison sera finalisée prochainement.",
                ],
                'email' => [
                    'title' => 'Votre paiement a été confirmé',
                    'body' => "Votre paiement pour le colis {$trackingNumber} a été confirmé. La livraison sera finalisée prochainement.",
                ],
            ],
        ];

        return $messages[$event] ?? [
            'sms' => [
                'title' => 'Mise à jour colis',
                'body' => "GROUPAGE PRO: Mise à jour de votre colis {$trackingNumber}. Connectez-vous pour plus de détails.",
            ],
            'whatsapp' => [
                'title' => 'Mise à jour colis',
                'body' => "📦 GROUPAGE PRO\n\n📋 Mise à jour\n\nUne mise à jour est disponible pour votre colis {$trackingNumber}.\n\n👉 Connectez-vous à votre espace client pour plus de détails.",
            ],
            'email' => [
                'title' => 'Mise à jour de votre colis',
                'body' => "Une mise à jour est disponible pour votre colis {$trackingNumber}. Connectez-vous à votre espace client pour plus de détails.",
            ],
        ];
    }
}
