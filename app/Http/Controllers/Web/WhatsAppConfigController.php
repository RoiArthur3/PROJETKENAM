<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WhatsAppConfigController extends Controller
{
    public function index()
    {
        return view('admin.groupeur.whatsapp-config');
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'whatsapp_provider' => 'required|in:meta,twilio,infobip,business',
                'whatsapp_enabled' => 'nullable|boolean',

                // Meta fields
                'meta_access_token' => 'nullable|required_if:whatsapp_provider,meta|string',
                'meta_phone_number_id' => 'nullable|required_if:whatsapp_provider,meta|string',
                'meta_base_url' => 'nullable|string',
                'meta_version' => 'nullable|string',
                'meta_webhook_verify_token' => 'nullable|string',
                'meta_webhook_url' => 'nullable|string',

                // Twilio fields
                'twilio_sid' => 'nullable|required_if:whatsapp_provider,twilio|string',
                'twilio_token' => 'nullable|required_if:whatsapp_provider,twilio|string',
                'twilio_whatsapp_from' => 'nullable|required_if:whatsapp_provider,twilio|string',

                // Infobip fields
                'infobip_api_key' => 'nullable|required_if:whatsapp_provider,infobip|string',
                'infobip_base_url' => 'nullable|string',

                // Business fields
                'business_api_key' => 'nullable|required_if:whatsapp_provider,business|string',
                'business_phone_number_id' => 'nullable|required_if:whatsapp_provider,business|string',
                'business_base_url' => 'nullable|string',

                // Global settings
                'timeout' => 'nullable|integer|min:5|max:120',
                'retry_attempts' => 'nullable|integer|min:1|max:10',
                'rate_limit' => 'nullable|integer|min:1|max:100',
                'start_hour' => 'nullable|integer|min:0|max:23',
                'end_hour' => 'nullable|integer|min:0|max:23',
                'timezone' => 'nullable|string',
            ]);

            // Update .env file
            $this->updateEnvFile($validated);

            return redirect()->back()->with('success', 'Configuration WhatsApp mise à jour avec succès !');
        } catch (\Exception $e) {
            Log::error('WhatsApp config update error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function testConnection(Request $request)
    {
        try {
            $validated = $request->validate([
                'provider' => 'required|in:meta,twilio,infobip,business',
                'config' => 'required|array',
            ]);

            $provider = $validated['provider'];
            $config = $validated['config'];

            // Temporarily update config for testing
            $this->temporarilyUpdateConfig($provider, $config);

            // Test the connection
            $whatsappService = new WhatsAppService();

            // For testing, we'll just validate the configuration
            $result = $this->validateProviderConfig($provider, $config);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'error' => $result['error'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp connection test error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function testMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'phone_number' => 'required|string',
                'message' => 'required|string',
            ]);

            $phoneNumber = $validated['phone_number'];
            $message = $validated['message'];

            $whatsappService = new WhatsAppService();
            $result = $whatsappService->send($phoneNumber, $message);

            return response()->json([
                'success' => $result['success'],
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp test message error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function updateEnvFile(array $data)
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        // Update provider
        $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_PROVIDER', $data['whatsapp_provider']);
        $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_ENABLED', $data['whatsapp_enabled'] ?? 'false');

        // Update Meta config
        if ($data['whatsapp_provider'] === 'meta') {
            $envContent = $this->updateEnvValue($envContent, 'META_WHATSAPP_ACCESS_TOKEN', $data['meta_access_token'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'META_WHATSAPP_PHONE_NUMBER_ID', $data['meta_phone_number_id'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'META_WHATSAPP_BASE_URL', $data['meta_base_url'] ?? 'https://graph.facebook.com');
            $envContent = $this->updateEnvValue($envContent, 'META_WHATSAPP_VERSION', $data['meta_version'] ?? 'v18.0');
            $envContent = $this->updateEnvValue($envContent, 'META_WHATSAPP_WEBHOOK_VERIFY_TOKEN', $data['meta_webhook_verify_token'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'META_WHATSAPP_WEBHOOK_URL', $data['meta_webhook_url'] ?? '/webhooks/whatsapp');
        }

        // Update Twilio config
        if ($data['whatsapp_provider'] === 'twilio') {
            $envContent = $this->updateEnvValue($envContent, 'TWILIO_SID', $data['twilio_sid'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'TWILIO_TOKEN', $data['twilio_token'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'TWILIO_WHATSAPP_FROM', $data['twilio_whatsapp_from'] ?? '');
        }

        // Update Infobip config
        if ($data['whatsapp_provider'] === 'infobip') {
            $envContent = $this->updateEnvValue($envContent, 'INFOBIP_WHATSAPP_API_KEY', $data['infobip_api_key'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'INFOBIP_WHATSAPP_BASE_URL', $data['infobip_base_url'] ?? 'https://api.infobip.com');
        }

        // Update Business config
        if ($data['whatsapp_provider'] === 'business') {
            $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_BUSINESS_API_KEY', $data['business_api_key'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_BUSINESS_PHONE_NUMBER_ID', $data['business_phone_number_id'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_BUSINESS_BASE_URL', $data['business_base_url'] ?? '');
        }

        // Update global settings
        $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_TIMEOUT', $data['timeout'] ?? '30');
        $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_RETRY_ATTEMPTS', $data['retry_attempts'] ?? '3');
        $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_RATE_LIMIT', $data['rate_limit'] ?? '10');
        $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_START_HOUR', $data['start_hour'] ?? '9');
        $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_END_HOUR', $data['end_hour'] ?? '20');
        $envContent = $this->updateEnvValue($envContent, 'WHATSAPP_TIMEZONE', $data['timezone'] ?? 'Europe/Paris');

        File::put($envPath, $envContent);

        // Clear configuration cache
        Artisan::call('config:clear');
    }

    private function updateEnvValue(string $envContent, string $key, string $value): string
    {
        // If the key exists, update it
        if (preg_match("/^{$key}=/m", $envContent)) {
            return preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        }

        // If the key doesn't exist, add it
        return $envContent . "\n{$key}={$value}";
    }

    private function temporarilyUpdateConfig(string $provider, array $config)
    {
        // This would temporarily update the config for testing
        // Implementation depends on your specific needs
        config(['whatsapp.default_provider' => $provider]);

        switch ($provider) {
            case 'meta':
                config(['whatsapp.meta.access_token' => $config['access_token'] ?? '']);
                config(['whatsapp.meta.phone_number_id' => $config['phone_number_id'] ?? '']);
                config(['whatsapp.meta.base_url' => $config['base_url'] ?? 'https://graph.facebook.com']);
                config(['whatsapp.meta.version' => $config['version'] ?? 'v18.0']);
                break;
            case 'twilio':
                config(['whatsapp.twilio.sid' => $config['sid'] ?? '']);
                config(['whatsapp.twilio.token' => $config['token'] ?? '']);
                config(['whatsapp.twilio.from' => $config['from'] ?? '']);
                break;
            case 'infobip':
                config(['whatsapp.infobip.api_key' => $config['api_key'] ?? '']);
                config(['whatsapp.infobip.base_url' => $config['base_url'] ?? 'https://api.infobip.com']);
                break;
            case 'business':
                config(['whatsapp.business.api_key' => $config['api_key'] ?? '']);
                config(['whatsapp.business.phone_number_id' => $config['phone_number_id'] ?? '']);
                config(['whatsapp.business.base_url' => $config['base_url'] ?? '']);
                break;
        }
    }

    private function validateProviderConfig(string $provider, array $config): array
    {
        switch ($provider) {
            case 'meta':
                if (empty($config['access_token']) || empty($config['phone_number_id'])) {
                    return ['success' => false, 'error' => 'Access Token et Phone Number ID sont requis'];
                }
                break;
            case 'twilio':
                if (empty($config['sid']) || empty($config['token']) || empty($config['from'])) {
                    return ['success' => false, 'error' => 'SID, Auth Token et From sont requis'];
                }
                break;
            case 'infobip':
                if (empty($config['api_key'])) {
                    return ['success' => false, 'error' => 'API Key est requis'];
                }
                break;
            case 'business':
                if (empty($config['api_key']) || empty($config['phone_number_id'])) {
                    return ['success' => false, 'error' => 'API Key et Phone Number ID sont requis'];
                }
                break;
        }

        return ['success' => true, 'message' => 'Configuration valide'];
    }
}
