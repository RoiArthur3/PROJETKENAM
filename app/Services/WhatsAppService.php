<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $apiKey;
    private $phoneNumberId;
    private $baseUrl;
    private $accessToken;
    private $provider;
    private $version;

    public function __construct()
    {
        // Configuration du service WhatsApp (adapter selon votre fournisseur)
        $provider = config('services.whatsapp.default', 'twilio');
        $this->provider = $provider;

        switch ($provider) {
            case 'twilio':
                $this->apiKey = config('services.whatsapp.twilio.sid');
                $this->accessToken = config('services.whatsapp.twilio.token');
                $this->phoneNumberId = config('services.whatsapp.twilio.from');
                $this->baseUrl = 'https://api.twilio.com/2010-04-01/Accounts/' . $this->apiKey;
                break;
            case 'meta':
                $this->accessToken = config('services.whatsapp.meta.access_token');
                $this->phoneNumberId = config('services.whatsapp.meta.phone_number_id');
                $this->baseUrl = config('services.whatsapp.meta.base_url', 'https://graph.facebook.com');
                $this->version = config('services.whatsapp.meta.version', 'v18.0');
                break;
            case 'whatsapp_business':
                $this->apiKey = config('services.whatsapp.business.api_key');
                $this->phoneNumberId = config('services.whatsapp.business.phone_number_id');
                $this->baseUrl = config('services.whatsapp.business.base_url');
                break;
            case 'infobip':
                $this->apiKey = config('services.whatsapp.infobip.api_key');
                $this->baseUrl = config('services.whatsapp.infobip.base_url');
                break;
            default:
                $this->apiKey = config('services.whatsapp.api_key');
                $this->phoneNumberId = config('services.whatsapp.phone_number_id');
                $this->baseUrl = config('services.whatsapp.base_url', 'https://api.whatsapp-service.com');
        }
    }

    /**
     * Envoyer un message WhatsApp
     */
    public function send(string $phoneNumber, string $message): array
    {
        try {
            // Nettoyer le numéro de téléphone
            $phoneNumber = $this->cleanPhoneNumber($phoneNumber);

            if ($this->provider === 'meta') {
                return $this->sendViaMeta($phoneNumber, $message);
            }

            if ($this->provider === 'infobip') {
                return $this->sendViaInfobip($phoneNumber, $message);
            }

            // Utiliser Twilio par défaut
            return $this->sendViaTwilio($phoneNumber, $message);

        } catch (\Exception $e) {
            Log::error('WhatsApp Service Error', [
                'phone' => $phoneNumber,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Envoyer via Meta WhatsApp Business API
     */
    private function sendViaMeta(string $phoneNumber, string $message): array
    {
        if (!$this->accessToken || !$this->phoneNumberId) {
            return [
                'success' => false,
                'error' => 'Meta WhatsApp credentials not configured',
            ];
        }

        $url = $this->baseUrl . '/' . $this->version . '/' . $this->phoneNumberId . '/messages';

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phoneNumber,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])
            ->post($url, $payload);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'HTTP Error: ' . $response->status(),
                'details' => $response->json(),
            ];
        }

        $result = $response->json();

        if (isset($result['messages'][0]['id'])) {
            return [
                'success' => true,
                'message_id' => $result['messages'][0]['id'],
            ];
        }

        return [
            'success' => false,
            'error' => $result['error']['message'] ?? 'Unknown error',
            'details' => $result,
        ];
    }

    /**
     * Envoyer via Twilio WhatsApp
     */
    private function sendViaTwilio(string $phoneNumber, string $message): array
    {
        if (!$this->apiKey || !$this->accessToken) {
            return [
                'success' => false,
                'error' => 'Twilio WhatsApp credentials not configured',
            ];
        }

        $url = $this->baseUrl . '/Messages.json';

        $data = [
            'To' => 'whatsapp:' . $phoneNumber,
            'From' => 'whatsapp:' . $this->phoneNumberId,
            'Body' => $message,
        ];

        $response = Http::timeout(30)
            ->asForm()
            ->withBasicAuth($this->apiKey, $this->accessToken)
            ->post($url, $data);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'HTTP Error: ' . $response->status(),
            ];
        }

        $result = $response->json();

        if (isset($result['sid'])) {
            return [
                'success' => true,
                'message_id' => $result['sid'],
            ];
        }

        return [
            'success' => false,
            'error' => $result['message'] ?? 'Unknown error',
        ];
    }

    /**
     * Envoyer via Infobip WhatsApp
     */
    private function sendViaInfobip(string $phoneNumber, string $message): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'error' => 'Infobip WhatsApp API key not configured',
            ];
        }

        $url = $this->baseUrl . '/whatsapp/1/message';

        $payload = [
            'from' => $this->phoneNumberId,
            'to' => $phoneNumber,
            'content' => [
                'text' => $message
            ]
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($url, $payload);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'HTTP Error: ' . $response->status(),
                'details' => $response->json(),
            ];
        }

        $result = $response->json();

        if (isset($result['messageId'])) {
            return [
                'success' => true,
                'message_id' => $result['messageId'],
            ];
        }

        return [
            'success' => false,
            'error' => $result['error']['errorMessage'] ?? 'Unknown error',
            'details' => $result,
        ];
    }

    /**
     * Envoyer un message template (pour les messages marketing)
     */
    public function sendTemplate(string $phoneNumber, string $templateName, array $parameters = []): array
    {
        try {
            $phoneNumber = $this->cleanPhoneNumber($phoneNumber);

            if ($this->provider === 'meta') {
                return $this->sendTemplateViaMeta($phoneNumber, $templateName, $parameters);
            }

            return [
                'success' => false,
                'error' => 'Template sending not supported for this provider',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Template Error', [
                'phone' => $phoneNumber,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Envoyer template via Meta
     */
    private function sendTemplateViaMeta(string $phoneNumber, string $templateName, array $parameters): array
    {
        $url = $this->baseUrl . '/' . $this->version . '/' . $this->phoneNumberId . '/messages';

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phoneNumber,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => 'fr'
                ],
                'components' => []
            ]
        ];

        // Ajouter les paramètres si fournis
        if (!empty($parameters)) {
            $payload['template']['components'][] = [
                'type' => 'body',
                'parameters' => array_map(function($param) {
                    return [
                        'type' => 'text',
                        'text' => $param
                    ];
                }, $parameters)
            ];
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])
            ->post($url, $payload);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'HTTP Error: ' . $response->status(),
                'details' => $response->json(),
            ];
        }

        $result = $response->json();

        if (isset($result['messages'][0]['id'])) {
            return [
                'success' => true,
                'message_id' => $result['messages'][0]['id'],
            ];
        }

        return [
            'success' => false,
            'error' => $result['error']['message'] ?? 'Unknown error',
            'details' => $result,
        ];
    }

    /**
     * Nettoyer le numéro de téléphone
     */
    private function cleanPhoneNumber(string $phoneNumber): string
    {
        // Supprimer tous les caractères non numériques sauf le +
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Ajouter le préfixe international si nécessaire
        if (strlen($phoneNumber) === 10 && str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '+33' . substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }

    /**
     * Vérifier le statut d'un message
     */
    public function getMessageStatus(string $messageId): array
    {
        try {
            if ($this->provider === 'meta') {
                return $this->getMetaMessageStatus($messageId);
            }

            return [
                'success' => false,
                'error' => 'Status checking not supported for this provider',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtenir le statut via Meta
     */
    private function getMetaMessageStatus(string $messageId): array
    {
        $url = $this->baseUrl . '/' . $this->version . '/' . $messageId;

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
            ])
            ->get($url);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'HTTP Error: ' . $response->status(),
            ];
        }

        $result = $response->json();

        return [
            'success' => true,
            'status' => $result['message_status'] ?? 'unknown',
            'timestamp' => $result['timestamp'] ?? null,
        ];
    }
}
