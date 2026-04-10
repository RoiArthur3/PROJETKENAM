<?php

namespace App\Services;

use App\Models\SmsSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private function getNetSmsProDefaults(): array
    {
        return [
            'api_url' => (string) config('sms.providers.netsmspro.base_url', 'http://www.netsmspro.net/client/2656631451/fr/api/json/sendsms/'),
            'api_username' => (string) config('sms.providers.netsmspro.username', ''),
            'api_password' => (string) config('sms.providers.netsmspro.password', ''),
            'sender_id' => (string) config('sms.providers.netsmspro.sender_id', 'KENAM'),
            'reseller_code' => (string) config('sms.providers.netsmspro.reseller_code', '2656631451'),
        ];
    }

    /**
     * Envoyer un SMS et retourner un résultat détaillé.
     */
    public function sendMessage(string $to, string $message): array
    {
        // 1. Essayer de récupérer la config via EntrepriseSettings (ma nouvelle implémentation)
        try {
            $entreprise = \App\Models\EntrepriseSettings::getActive();
        } catch (\Throwable $e) {
            Log::warning('[SMS] EntrepriseSettings indisponible, fallback .env/SmsSetting', [
                'error' => $e->getMessage(),
            ]);
            $entreprise = null;
        }
        $netSmsProDefaults = $this->getNetSmsProDefaults();

        if ($entreprise && $entreprise->sms_is_active) {
            $legacyApiKey = $entreprise->sms_api_key ?? null;
            $legacyApiSecret = $entreprise->sms_api_secret ?? null;

            $config = (object)[
                'sms_enabled'  => true,
                'provider'     => $entreprise->sms_provider ?: 'netsmspro',
                'sender_id'    => $entreprise->sms_sender_id ?: $netSmsProDefaults['sender_id'],
                'api_url'      => $entreprise->sms_api_url ?: $netSmsProDefaults['api_url'],
                'api_username' => $entreprise->sms_username ?: $legacyApiKey ?: $netSmsProDefaults['api_username'],
                'api_password' => $entreprise->sms_password ?: $legacyApiSecret ?: $netSmsProDefaults['api_password'],
                'reseller_code'=> $entreprise->sms_reseller_code ?: $netSmsProDefaults['reseller_code'],
                'sandbox_mode' => false,
            ];

            $hasDatabaseSmsCredentials = !empty($entreprise->sms_username)
                || !empty($entreprise->sms_password)
                || !empty($legacyApiKey)
                || !empty($legacyApiSecret);
        } else {
            // 2. Fallback sur l'ancien modèle SmsSetting pour ne pas casser l'existant
            try {
                $oldConfig = SmsSetting::current();
            } catch (\Throwable $e) {
                Log::warning('[SMS] sms_settings indisponible, fallback config .env', [
                    'error' => $e->getMessage(),
                ]);

                $oldConfig = (object) [
                    'sms_enabled' => true,
                    'provider' => 'netsmspro',
                    'sender_id' => $netSmsProDefaults['sender_id'],
                    'api_url' => $netSmsProDefaults['api_url'],
                    'api_username' => $netSmsProDefaults['api_username'],
                    'api_password' => $netSmsProDefaults['api_password'],
                    'reseller_code' => $netSmsProDefaults['reseller_code'],
                    'sandbox_mode' => false,
                ];
            }

            if (!$oldConfig || !$oldConfig->sms_enabled) {
                Log::info('[SMS] SMS désactivé (ni EntrepriseSettings ni SmsSetting actif)');

                return [
                    'success' => false,
                    'error' => 'SMS disabled or not configured.',
                    'provider' => null,
                    'recipient' => $to,
                ];
            }
            $config = $oldConfig;
        }

        // Mode sandbox
        if (isset($config->sandbox_mode) && $config->sandbox_mode) {
            Log::info('[SMS][SANDBOX] Message simulé : ' . $message, ['to' => $to]);

            return [
                'success' => true,
                'error' => null,
                'provider' => strtolower($config->provider ?? 'sandbox'),
                'recipient' => $to,
                'sandbox' => true,
            ];
        }

        if (!$config->api_url) {
            Log::warning('[SMS] api_url non configurée');

            return [
                'success' => false,
                'error' => 'SMS api_url not configured.',
                'provider' => strtolower($config->provider ?? ''),
                'recipient' => $to,
            ];
        }

        try {
            $provider = strtolower($config->provider ?? '');
            $recipient = $this->formatPhoneNumber($to);
            $netSmsProApiUrl = (string) $config->api_url;
            $hasConfiguredCredentials = !empty($config->api_username) || !empty($config->api_password);

            if ($provider === 'netsmspro' && (empty($config->api_username) || empty($config->api_password))) {
                Log::warning('[SMS] Identifiants NetSMSPro manquants');

                return [
                    'success' => false,
                    'error' => 'NetSMSPro credentials are not configured.',
                    'provider' => $provider,
                    'recipient' => $recipient,
                ];
            }

            if ($provider === 'netsmspro' && trim($netSmsProApiUrl) === '' && !$hasConfiguredCredentials) {
                $netSmsProApiUrl = $netSmsProDefaults['api_url'];
            }

            if ($provider === 'netsmspro') {
                $jsonEndpoint = $this->resolveNetSmsProJsonEndpoint(
                    $netSmsProApiUrl,
                    (string) ($config->reseller_code ?? $netSmsProDefaults['reseller_code'])
                );

                $jsonPayload = $this->buildNetSmsProJsonPayload(
                    $recipient,
                    $message,
                    (string) $config->api_username,
                    (string) $config->api_password,
                    (string) ($config->sender_id ?: $netSmsProDefaults['sender_id'])
                );

                $response = Http::timeout(30)
                    ->send('POST', $jsonEndpoint, [
                        'headers' => ['Content-Type' => 'application/json'],
                        'body' => 'JSON=' . json_encode($jsonPayload, JSON_UNESCAPED_UNICODE),
                    ]);

                if (!$response->successful() || $this->netSmsProJsonResponseIndicatesFailure($response->body())) {
                    $fallbackResponse = $this->sendViaNetSmsProUrl(
                        $jsonEndpoint,
                        (string) $config->api_username,
                        (string) $config->api_password,
                        (string) ($config->sender_id ?: $netSmsProDefaults['sender_id']),
                        $recipient,
                        $message
                    );

                    if ($fallbackResponse !== null) {
                        $response = $fallbackResponse;
                    }
                }
            } elseif ($provider === 'smseco') {
                // Configuration selon la documentation SMSECO officielle
                $data = [
                    'api_key' => $config->api_username,
                    'api_secret' => $config->api_password,
                    'sender' => $config->sender_id ?: 'KENAM',
                    'to' => $recipient,
                    'message' => $message,
                    'type' => 'text', // SMS standard
                ];

                $response = Http::timeout(30)
                    ->asForm()
                    ->post($config->api_url . '/sms/send', $data);
            } else {
                // Autre Provider générique
                $response = Http::withBasicAuth($config->api_username ?? '', $config->api_password ?? '')
                    ->post($config->api_url, [
                        'to'      => $recipient,
                        'sender'  => $config->sender_id,
                        'message' => $message,
                    ]);
            }

            if ($response->successful()) {
                Log::info("[SMS] Succès : Envoyé à {$to}");

                return [
                    'success' => true,
                    'error' => null,
                    'provider' => $provider,
                    'recipient' => $recipient,
                    'response' => $response->json() ?: $response->body(),
                ];
            }

            Log::error("[SMS] Échec : " . $response->body());

            return [
                'success' => false,
                'error' => $response->body() ?: 'SMS provider request failed.',
                'provider' => $provider,
                'recipient' => $recipient,
                'status_code' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('[SMS] Erreur d\'exception : ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => strtolower($config->provider ?? ''),
                'recipient' => $to,
            ];
        }
    }

    /**
     * Envoyer un SMS générique en utilisant la configuration sms_settings.
     */
    public function sendSMS(string $to, string $message): bool
    {
        return $this->sendMessage($to, $message)['success'];
    }

    /**
     * Nettoyer et formater le numéro (enlever les espaces, +, etc.)
     */
    private function formatPhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    private function resolveNetSmsProJsonEndpoint(string $configuredUrl, string $resellerCode): string
    {
        $url = trim($configuredUrl);

        if ($url !== '' && str_contains($url, '/json/sendsms')) {
            return $url;
        }

        if ($url !== '' && str_contains($url, 'netsmspro.net')) {
            return rtrim($url, '/') . '/client/' . $resellerCode . '/fr/api/json/sendsms/';
        }

        return 'http://www.netsmspro.net/client/' . $resellerCode . '/fr/api/json/sendsms/';
    }

    private function buildNetSmsProJsonPayload(string $recipient, string $message, string $login, string $password, string $senderId): array
    {
        return [
            'compte' => [
                'login' => $login,
                'password' => $password,
            ],
            'message' => [
                'expediteur' => $senderId,
                'msgid' => uniqid(),
                'msg' => $message,
                'flash' => 0,
                'unicode' => 0,
                'binaire' => 0,
            ],
            'destinataires' => [
                ['numero' => $recipient],
            ],
        ];
    }

    private function netSmsProJsonResponseIndicatesFailure(string $responseBody): bool
    {
        $lower = strtolower($responseBody);

        return str_contains($lower, '"statut":"-1"')
            || str_contains($lower, '"status":"error"')
            || str_contains($lower, '"success":false')
            || str_contains($lower, 'erreur');
    }

    private function buildNetSmsProPayload(string $recipient, string $message, string $login, string $password, string $senderId): string
    {
        $xml = '<SMS>';
        $xml .= '<compte>';
        $xml .= '<login>' . htmlspecialchars($login, ENT_XML1) . '</login>';
        $xml .= '<password>' . htmlspecialchars($password, ENT_XML1) . '</password>';
        $xml .= '</compte>';
        $xml .= '<message>';
        $xml .= '<msgid>KS' . date('YmdHis') . rand(100, 999) . '</msgid>';
        $xml .= '<expediteur>' . htmlspecialchars($senderId, ENT_XML1) . '</expediteur>';
        $xml .= '<msg>' . htmlspecialchars($message, ENT_XML1) . '</msg>';
        $xml .= '<flash>0</flash>';
        $xml .= '<unicode>0</unicode>';
        $xml .= '<binaire>0</binaire>';
        $xml .= '<datesend></datesend>';
        $xml .= '</message>';
        $xml .= '<destinataires><numero>' . htmlspecialchars($recipient, ENT_XML1) . '</numero></destinataires>';
        $xml .= '</SMS>';

        return $xml;
    }

    private function sendViaNetSmsProUrl(string $baseUrl, string $login, string $password, string $senderId, string $recipient, string $message): ?\Illuminate\Http\Client\Response
    {
        $payload = 'JSON=' . json_encode(
            $this->buildNetSmsProJsonPayload($recipient, $message, $login, $password, $senderId),
            JSON_UNESCAPED_UNICODE
        );

        return Http::timeout(30)
            ->send('POST', $baseUrl, [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => 'JSON=' . json_encode(
                    $this->buildNetSmsProJsonPayload($recipient, $message, $login, $password, $senderId),
                    JSON_UNESCAPED_UNICODE
                ),
            ]);
    }

    /**
     * Helper statique pratique.
     */
    public static function send(string $to, string $message): array
    {
        return app(self::class)->sendMessage($to, $message);
    }
}
