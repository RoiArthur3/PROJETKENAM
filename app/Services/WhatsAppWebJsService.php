<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppWebJsService
{
    public function sendMessage(string $to, string $message, string $channel = 'default'): array
    {
        $channelConfig = $this->resolveChannelConfig($channel);

        $enabled = (bool) ($channelConfig['enabled'] ?? false);
        if (!$enabled) {
            return [
                'success' => false,
                'error' => 'WhatsApp Web JS disabled',
                'provider' => $channelConfig['provider'],
                'recipient' => $to,
            ];
        }

        $apiUrl = (string) ($channelConfig['api_url'] ?? '');
        if ($apiUrl === '') {
            return [
                'success' => false,
                'error' => 'WhatsApp Web JS API URL not configured',
                'provider' => $channelConfig['provider'],
                'recipient' => $to,
            ];
        }

        $recipient = $this->formatPhoneNumber($to);
        $payload = [
            'to' => $recipient,
            'message' => $message,
            'session' => (string) ($channelConfig['session'] ?? 'default'),
        ];

        try {
            $request = Http::timeout((int) ($channelConfig['timeout'] ?? 20));

            $token = (string) ($channelConfig['token'] ?? '');
            if ($token !== '') {
                $request = $request->withToken($token);
            }

            $response = $request->post($apiUrl, $payload);
            if ($response->successful()) {
                return [
                    'success' => true,
                    'error' => null,
                    'provider' => $channelConfig['provider'],
                    'recipient' => $recipient,
                    'response' => $response->json() ?: $response->body(),
                ];
            }

            Log::warning('WhatsApp Web JS send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'recipient' => $recipient,
            ]);

            return [
                'success' => false,
                'error' => $response->body() ?: 'whatsapp-webjs request failed',
                'provider' => $channelConfig['provider'],
                'recipient' => $recipient,
                'status_code' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp Web JS exception: ' . $e->getMessage(), [
                'recipient' => $recipient,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $channelConfig['provider'],
                'recipient' => $recipient,
            ];
        }
    }

    private function resolveChannelConfig(string $channel): array
    {
        if ($channel === 'whatsapp_appro') {
            return [
                'enabled' => (bool) config('services.whatsapp_appro.enabled', config('whatsapp_webjs.enabled', false)),
                'api_url' => (string) config('services.whatsapp_appro.api_url', config('whatsapp_webjs.api_url', '')),
                'token' => (string) config('services.whatsapp_appro.token', config('whatsapp_webjs.token', '')),
                'session' => (string) config('services.whatsapp_appro.session', 'whatsapp-appro'),
                'timeout' => (int) config('services.whatsapp_appro.timeout', config('whatsapp_webjs.timeout', 20)),
                'provider' => 'whatsapp-appro',
            ];
        }

        return [
            'enabled' => (bool) config('whatsapp_webjs.enabled', false),
            'api_url' => (string) config('whatsapp_webjs.api_url', ''),
            'token' => (string) config('whatsapp_webjs.token', ''),
            'session' => (string) config('whatsapp_webjs.session', 'default'),
            'timeout' => (int) config('whatsapp_webjs.timeout', 20),
            'provider' => 'whatsapp-webjs',
        ];
    }

    private function formatPhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone) ?: '';
    }
}
