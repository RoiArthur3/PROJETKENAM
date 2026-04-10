@extends('layouts.app')

@section('title', 'Configuration - SMS')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Configuration</h1>
        <p class="text-gray-600 mt-1">Configuration des API SMS pour les notifications</p>
    </div>

    <!-- Navigation -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('admin.groupeur.profile') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Profil
            </a>
            <a href="{{ route('admin.groupeur.business-card') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Carte de visite
            </a>
            <a href="{{ route('admin.groupeur.shipping-address') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Adresse de livraison
            </a>
            <a href="{{ route('admin.groupeur.sms-config') }}" class="py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                Configuration SMS
            </a>
            <a href="{{ route('admin.groupeur.product-catalog.index') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Produits interdits
            </a>
            <a href="{{ route('admin.groupeur.users.index') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Utilisateurs
            </a>
        </nav>
    </div>

    <!-- Configuration SMS -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Configuration SMS</h2>
            <p class="text-sm text-gray-500 mt-1">Paramètres des fournisseurs SMS pour les notifications automatiques</p>
        </div>

        <form method="POST" action="{{ route('admin.groupeur.sms-config.update') }}" class="p-6 space-y-6">
            @csrf

            <!-- Fournisseur SMS -->
            <div>
                <label for="sms_provider" class="block text-sm font-medium text-gray-700">
                    Fournisseur SMS *
                </label>
                <select id="sms_provider" name="sms_provider" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="twilio" {{ old('sms_provider', $smsConfig['provider']) == 'twilio' ? 'selected' : '' }}>Twilio</option>
                    <option value="ovh" {{ old('sms_provider', $smsConfig['provider']) == 'ovh' ? 'selected' : '' }}>OVH</option>
                    <option value="smsc" {{ old('sms_provider', $smsConfig['provider']) == 'smsc' ? 'selected' : '' }}>SMSC</option>
                    <option value="infobip" {{ old('sms_provider', $smsConfig['provider']) == 'infobip' ? 'selected' : '' }}>Infobip</option>
                    <option value="smseco" {{ old('sms_provider', $smsConfig['provider']) == 'smseco' ? 'selected' : '' }}>SMSECO</option>
                </select>
                @error('sms_provider')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Configuration SMSECO -->
            <div id="smseco-config" class="space-y-6 {{ old('sms_provider', $smsConfig['provider']) != 'smseco' ? 'hidden' : '' }}">
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Configuration SMSECO</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="smseco_sender" class="block text-sm font-medium text-gray-700">
                                Nom de l'expéditeur *
                            </label>
                            <input type="text" id="smseco_sender" name="smseco_sender"
                                value="{{ old('smseco_sender', $smsConfig['smseco']['sender']) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">Nom qui apparaît sur le SMS (max 11 caractères)</p>
                            @error('smseco_sender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Note de sécurité</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Le mot de passe du compte SMSECO est configuré directement dans le fichier <code class="bg-yellow-100 px-1 rounded">.env</code> avec la variable <code class="bg-yellow-100 px-1 rounded">SMSECO_PASSWORD</code>.</p>
                                    <p class="mt-1">Pour des raisons de sécurité, il n'est pas affiché ici. Modifiez-le directement dans le fichier si nécessaire.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test SMS -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tester la configuration</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-3">Pour tester l'envoi de SMS, accédez à vos préférences de notification.</p>
                    <a href="{{ url('/client/client/notifications/edit') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Tester l'envoi de SMS
                    </a>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Enregistrer la configuration
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const smsProvider = document.getElementById('sms_provider');
    const smsecoConfig = document.getElementById('smseco-config');

    smsProvider.addEventListener('change', function() {
        if (this.value === 'smseco') {
            smsecoConfig.classList.remove('hidden');
        } else {
            smsecoConfig.classList.add('hidden');
        }
    });
});
</script>
@endsection
