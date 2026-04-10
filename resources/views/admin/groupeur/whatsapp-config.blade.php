@extends('layouts.app')

@section('title', 'Configuration WhatsApp Business')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Configuration WhatsApp Business</h1>
                <p class="mt-1 text-sm text-blue-700">Configurez les paramètres pour envoyer des notifications WhatsApp automatiques.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="testWhatsAppConnection()" class="btn-outline-primary rounded-xl px-4 py-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Tester la connexion
                </button>
            </div>
        </div>
    </div>

    <form id="whatsappConfigForm" method="POST" action="{{ route('admin.groupeur.whatsapp-config.update') }}" class="space-y-6">
        @csrf

        <!-- Provider Selection -->
        <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Provider WhatsApp</h2>
                <p class="mt-1 text-sm text-blue-700">Choisissez votre fournisseur de services WhatsApp.</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Fournisseur WhatsApp</label>
                        <select name="whatsapp_provider" id="whatsappProvider" class="input-select" onchange="toggleProviderFields()">
                            <option value="meta" {{ config('whatsapp.default_provider') === 'meta' ? 'selected' : '' }}>Meta WhatsApp Business API (Recommandé)</option>
                            <option value="twilio" {{ config('whatsapp.default_provider') === 'twilio' ? 'selected' : '' }}>Twilio WhatsApp</option>
                            <option value="infobip" {{ config('whatsapp.default_provider') === 'infobip' ? 'selected' : '' }}>Infobip WhatsApp</option>
                            <option value="business" {{ config('whatsapp.default_provider') === 'business' ? 'selected' : '' }}>WhatsApp Business API Direct</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta WhatsApp Business Configuration -->
        <div id="metaConfig" class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden {{ config('whatsapp.default_provider') !== 'meta' ? 'hidden' : '' }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Meta WhatsApp Business API</h2>
                <p class="mt-1 text-sm text-blue-700">Configuration pour l'API officielle de Meta WhatsApp Business.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Access Token *</label>
                        <input type="password" name="meta_access_token" value="{{ env('META_WHATSAPP_ACCESS_TOKEN') }}" class="input-text" placeholder="EAAJZC...">
                        <p class="text-xs text-gray-500">Token d'accès obtenu depuis developers.facebook.com</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Phone Number ID *</label>
                        <input type="text" name="meta_phone_number_id" value="{{ env('META_WHATSAPP_PHONE_NUMBER_ID') }}" class="input-text" placeholder="123456789012345678">
                        <p class="text-xs text-gray-500">ID du numéro WhatsApp Business</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Base URL</label>
                        <input type="url" name="meta_base_url" value="{{ env('META_WHATSAPP_BASE_URL', 'https://graph.facebook.com') }}" class="input-text">
                        <p class="text-xs text-gray-500">URL de l'API Meta (généralement inchangée)</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Version API</label>
                        <select name="meta_version" class="input-select">
                            <option value="v18.0" {{ env('META_WHATSAPP_VERSION', 'v18.0') === 'v18.0' ? 'selected' : '' }}>v18.0 (Recommandé)</option>
                            <option value="v17.0" {{ env('META_WHATSAPP_VERSION') === 'v17.0' ? 'selected' : '' }}>v17.0</option>
                            <option value="v16.0" {{ env('META_WHATSAPP_VERSION') === 'v16.0' ? 'selected' : '' }}>v16.0</option>
                        </select>
                    </div>
                </div>

                <!-- Webhook Configuration -->
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-md font-medium text-gray-700 mb-4">Configuration Webhook (Optionnel)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Webhook Verify Token</label>
                            <input type="text" name="meta_webhook_verify_token" value="{{ env('META_WHATSAPP_WEBHOOK_VERIFY_TOKEN') }}" class="input-text" placeholder="token_securise_12345">
                            <p class="text-xs text-gray-500">Token pour vérifier les webhooks entrants</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Webhook URL</label>
                            <input type="text" name="meta_webhook_url" value="{{ env('META_WHATSAPP_WEBHOOK_URL', '/webhooks/whatsapp') }}" class="input-text">
                            <p class="text-xs text-gray-500">URL pour recevoir les réponses WhatsApp</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Twilio WhatsApp Configuration -->
        <div id="twilioConfig" class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden {{ config('whatsapp.default_provider') !== 'twilio' ? 'hidden' : '' }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Twilio WhatsApp</h2>
                <p class="mt-1 text-sm text-blue-700">Configuration pour Twilio WhatsApp API.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Account SID *</label>
                        <input type="text" name="twilio_sid" value="{{ env('TWILIO_SID') }}" class="input-text" placeholder="ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
                        <p class="text-xs text-gray-500">SID de votre compte Twilio</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Auth Token *</label>
                        <input type="password" name="twilio_token" value="{{ env('TWILIO_TOKEN') }}" class="input-text" placeholder="your_auth_token">
                        <p class="text-xs text-gray-500">Token d'authentification Twilio</p>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">WhatsApp Number *</label>
                        <input type="text" name="twilio_whatsapp_from" value="{{ env('TWILIO_WHATSAPP_FROM') }}" class="input-text" placeholder="whatsapp:+14155238886">
                        <p class="text-xs text-gray-500">Numéro WhatsApp Twilio (format: whatsapp:+14155238886)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Infobip WhatsApp Configuration -->
        <div id="infobipConfig" class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden {{ config('whatsapp.default_provider') !== 'infobip' ? 'hidden' : '' }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Infobip WhatsApp</h2>
                <p class="mt-1 text-sm text-blue-700">Configuration pour Infobip WhatsApp API.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">API Key *</label>
                        <input type="password" name="infobip_api_key" value="{{ env('INFOBIP_WHATSAPP_API_KEY') }}" class="input-text" placeholder="votre_api_key_infobip">
                        <p class="text-xs text-gray-500">Clé API Infobip</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Base URL</label>
                        <input type="url" name="infobip_base_url" value="{{ env('INFOBIP_WHATSAPP_BASE_URL', 'https://api.infobip.com') }}" class="input-text">
                        <p class="text-xs text-gray-500">URL de l'API Infobip</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- WhatsApp Business Direct Configuration -->
        <div id="businessConfig" class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden {{ config('whatsapp.default_provider') !== 'business' ? 'hidden' : '' }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">WhatsApp Business API Direct</h2>
                <p class="mt-1 text-sm text-blue-700">Configuration directe via l'API WhatsApp Business.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">API Key *</label>
                        <input type="password" name="business_api_key" value="{{ env('WHATSAPP_BUSINESS_API_KEY') }}" class="input-text" placeholder="votre_api_key_business">
                        <p class="text-xs text-gray-500">Clé API WhatsApp Business</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Phone Number ID *</label>
                        <input type="text" name="business_phone_number_id" value="{{ env('WHATSAPP_BUSINESS_PHONE_NUMBER_ID') }}" class="input-text" placeholder="123456789012345678">
                        <p class="text-xs text-gray-500">ID du numéro WhatsApp Business</p>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Base URL</label>
                        <input type="url" name="business_base_url" value="{{ env('WHATSAPP_BUSINESS_BASE_URL') }}" class="input-text" placeholder="https://waba-api.example.com">
                        <p class="text-xs text-gray-500">URL de l'API WhatsApp Business</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Settings -->
        <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Paramètres globaux</h2>
                <p class="mt-1 text-sm text-blue-700">Configuration générale applicable à tous les providers.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Timeout (secondes)</label>
                        <input type="number" name="timeout" value="{{ config('whatsapp.settings.timeout', 30) }}" class="input-text" min="5" max="120">
                        <p class="text-xs text-gray-500">Délai d'attente max pour les requêtes</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Tentatives max</label>
                        <input type="number" name="retry_attempts" value="{{ config('whatsapp.settings.retry_attempts', 3) }}" class="input-text" min="1" max="10">
                        <p class="text-xs text-gray-500">Nombre de tentatives en cas d'échec</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Rate Limit (msg/sec)</label>
                        <input type="number" name="rate_limit" value="{{ config('whatsapp.settings.rate_limit', 10) }}" class="input-text" min="1" max="100">
                        <p class="text-xs text-gray-500">Messages maximum par seconde</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Heure de début</label>
                        <input type="number" name="start_hour" value="{{ config('whatsapp.settings.allowed_hours.start', 9) }}" class="input-text" min="0" max="23">
                        <p class="text-xs text-gray-500">Heure de début d'envoi autorisé</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Heure de fin</label>
                        <input type="number" name="end_hour" value="{{ config('whatsapp.settings.allowed_hours.end', 20) }}" class="input-text" min="0" max="23">
                        <p class="text-xs text-gray-500">Heure de fin d'envoi autorisé</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Fuseau horaire</label>
                        <select name="timezone" class="input-select">
                            <option value="Europe/Paris" {{ config('whatsapp.settings.timezone') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                            <option value="Africa/Abidjan" {{ config('whatsapp.settings.timezone') === 'Africa/Abidjan' ? 'selected' : '' }}>Africa/Abidjan</option>
                            <option value="Africa/Dakar" {{ config('whatsapp.settings.timezone') === 'Africa/Dakar' ? 'selected' : '' }}>Africa/Dakar</option>
                            <option value="UTC" {{ config('whatsapp.settings.timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>
                </div>

                <!-- Enable/Disable WhatsApp -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="whatsapp_enabled" id="whatsappEnabled" class="form-checkbox" {{ env('WHATSAPP_ENABLED', true) ? 'checked' : '' }}>
                        <label for="whatsappEnabled" class="text-sm font-medium text-gray-700">
                            Activer les notifications WhatsApp
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Cochez cette case pour activer l'envoi de notifications WhatsApp</p>
                </div>
            </div>
        </div>

        <!-- Test Section -->
        <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Tester la configuration</h2>
                <p class="mt-1 text-sm text-blue-700">Envoyez un message de test pour vérifier que tout fonctionne correctement.</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Numéro de téléphone de test</label>
                        <input type="tel" id="testPhoneNumber" class="input-text" placeholder="+33612345678">
                        <p class="text-xs text-gray-500">Format international (+33...)</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Message de test</label>
                        <textarea id="testMessage" rows="3" class="input-text" placeholder="📦 GROUPAGE PRO\n\n✅ Test de configuration WhatsApp réussi !">📦 GROUPAGE PRO

✅ Test de configuration WhatsApp réussi !</textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" onclick="sendTestMessage()" class="btn-primary rounded-xl px-4 py-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Envoyer le message de test
                    </button>
                </div>
                <div id="testResult" class="mt-4 hidden">
                    <!-- Result will be displayed here -->
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end gap-3">
            <button type="button" onclick="resetForm()" class="btn-outline-secondary rounded-xl px-6 py-3">
                Annuler
            </button>
            <button type="submit" class="btn-primary rounded-xl px-6 py-3">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Enregistrer la configuration
            </button>
        </div>
    </form>
</div>

<script>
function toggleProviderFields() {
    const provider = document.getElementById('whatsappProvider').value;

    // Hide all provider configs
    document.getElementById('metaConfig').classList.add('hidden');
    document.getElementById('twilioConfig').classList.add('hidden');
    document.getElementById('infobipConfig').classList.add('hidden');
    document.getElementById('businessConfig').classList.add('hidden');

    // Show selected provider config
    document.getElementById(provider + 'Config').classList.remove('hidden');
}

function testWhatsAppConnection() {
    const provider = document.getElementById('whatsappProvider').value;

    // Get provider-specific fields
    let config = {};

    switch(provider) {
        case 'meta':
            config = {
                access_token: document.querySelector('input[name="meta_access_token"]').value,
                phone_number_id: document.querySelector('input[name="meta_phone_number_id"]').value,
                base_url: document.querySelector('input[name="meta_base_url"]').value,
                version: document.querySelector('select[name="meta_version"]').value
            };
            break;
        case 'twilio':
            config = {
                sid: document.querySelector('input[name="twilio_sid"]').value,
                token: document.querySelector('input[name="twilio_token"]').value,
                from: document.querySelector('input[name="twilio_whatsapp_from"]').value
            };
            break;
        case 'infobip':
            config = {
                api_key: document.querySelector('input[name="infobip_api_key"]').value,
                base_url: document.querySelector('input[name="infobip_base_url"]').value
            };
            break;
        case 'business':
            config = {
                api_key: document.querySelector('input[name="business_api_key"]').value,
                phone_number_id: document.querySelector('input[name="business_phone_number_id"]').value,
                base_url: document.querySelector('input[name="business_base_url"]').value
            };
            break;
    }

    // Send test request
    fetch('{{ route("admin.groupeur.whatsapp.test.connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            provider: provider,
            config: config
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Connexion WhatsApp réussie !', 'success');
        } else {
            showNotification('Erreur de connexion: ' + data.error, 'error');
        }
    })
    .catch(error => {
        showNotification('Erreur: ' + error.message, 'error');
    });
}

function sendTestMessage() {
    const phoneNumber = document.getElementById('testPhoneNumber').value;
    const message = document.getElementById('testMessage').value;

    if (!phoneNumber || !message) {
        showNotification('Veuillez remplir le numéro de téléphone et le message', 'error');
        return;
    }

    fetch('{{ route("admin.groupeur.whatsapp.test.message") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            phone_number: phoneNumber,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('testResult');
        resultDiv.classList.remove('hidden');

        if (data.success) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-green-800">Message envoyé avec succès !</span>
                    </div>
                    <p class="text-xs text-green-700 mt-1">Message ID: ${data.message_id}</p>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-red-800">Échec de l'envoi</span>
                    </div>
                    <p class="text-xs text-red-700 mt-1">Erreur: ${data.error}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        const resultDiv = document.getElementById('testResult');
        resultDiv.classList.remove('hidden');
        resultDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-red-800">Erreur</span>
                </div>
                <p class="text-xs text-red-700 mt-1">${error.message}</p>
            </div>
        `;
    });
}

function showNotification(message, type = 'info') {
    // Simple notification implementation
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir annuler les modifications ?')) {
        window.location.reload();
    }
}

// Initialize provider fields on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleProviderFields();
});
</script>
@endsection
