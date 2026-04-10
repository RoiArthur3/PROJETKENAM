<?php

namespace App\Services;

use App\Models\SmsSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoyer un SMS générique en utilisant la configuration sms_settings.
     */
    public function sendSMS(string $to, string $message): bool
    {
        $config = SmsSetting::current();

        if (!$config || !$config->sms_enabled) {
            Log::info('[SMS] SMS désactivé, message non envoyé', [
                'to' => $to,
                'message' => $message,
            ]);
            return false;
        }

        // Mode sandbox : ne pas appeler l’API réelle, juste logguer
        if ($config->sandbox_mode) {
            Log::info('[SMS][SANDBOX] SMS simulé', [
                'to' => $to,
                'message' => $message,
            ]);
            return true;
        }

        // Implémentation générique basée sur api_url
        if (!$config->api_url) {
            Log::warning('[SMS] api_url non configurée, SMS non envoyé', [
                'to' => $to,
            ]);
            return false;
        }

        try {
            $provider = strtolower($config->provider ?? '');

            // Cas spécifique : API SMSECO / netsmspro (JSON={...})
            if ($provider === 'smseco' || str_contains($config->api_url, 'netsmspro.net')) {
                $msgId = uniqid('op_', true);

                $jsonPayload = [
                    'compte' => [
                        'login'    => $config->api_username ?? env('SMSECO_LOGIN', ''),
                        'password' => $config->api_password ?? env('SMSECO_PASSWORD', ''),
                    ],
                    'message' => [
                        'expediteur' => $config->sender_id ?? 'KENAM-SERVICES',
                        'msgid'      => substr($msgId, 0, 30),
                        'msg'        => $message,
                        // options par défaut : envoi immédiat, non flash, non unicode
                        'datesend'   => '',
                        'flash'      => 0,
                        'unicode'    => 0,
                        'binaire'    => 0,
                    ],
                    'destinataires' => [
                        ['numero' => $to],
                    ],
                ];

                // Format attendu par l’API : corps = "JSON={...}" en application/json
                $rawBody = 'JSON=' . json_encode($jsonPayload, JSON_UNESCAPED_UNICODE);

                $response = Http::withHeaders([
                        'Accept'       => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->withBody($rawBody, 'application/json')
                    ->post($config->api_url);
            } else {
                // Provider générique : payload simple
                $payload = [
                    'to'      => $to,
                    'sender'  => $config->sender_id ?? 'KENAM-SERVICES',
                    'message' => $message,
                ];

                $response = Http::withBasicAuth($config->api_username ?? '', $config->api_password ?? '')
                    ->post($config->api_url, $payload);
            }

            if ($response->successful()) {
                Log::info('[SMS] Envoyé avec succès', [
                    'to' => $to,
                    'provider' => $config->provider,
                ]);
                return true;
            }

            Log::error('[SMS] Échec envoi SMS', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('[SMS] Exception lors de l’envoi', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Helper statique pratique.
     */
    public static function send(string $to, string $message): bool
    {
        return app(self::class)->sendSMS($to, $message);
    }
}
