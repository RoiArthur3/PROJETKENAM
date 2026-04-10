@extends('layouts.app')

@section('title', 'Préférences de Notification')

@section('content')
<div class="space-y-6">
    <div class="flex items-center">
        <a href="{{ route('client.client.notifications.index') }}" class="text-gray-600 hover:text-gray-900 mr-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Préférences de Notification</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <form action="{{ route('client.client.notifications.update') }}" method="POST" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <!-- Notifications SMS -->
            <div class="border-b pb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Notifications SMS</h3>
                            <p class="text-sm text-gray-500">Recevez des alertes SMS pour suivre vos colis en temps réel</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="sms_enabled" name="sms_enabled" value="1" {{ $preference->sms_enabled ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="sms_enabled" class="ml-2 block text-sm text-gray-900">
                            Activer les SMS
                        </label>
                    </div>
                </div>

                <div id="sms_settings" class="{{ $preference->sms_enabled ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Numéro de téléphone *</label>
                            <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number', $preference->phone_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="+33 6 12 34 56 78">
                            <p class="mt-1 text-xs text-gray-500">Format international (ex: +33 6 12 34 56 78)</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Événements SMS</label>
                        <div class="space-y-2">
                            @foreach($availableEvents as $event => $label)
                                <div class="flex items-center">
                                    <input type="checkbox" id="sms_event_{{ $event }}" name="sms_events[]" value="{{ $event }}"
                                           {{ in_array($event, $preference->sms_events ?? []) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="sms_event_{{ $event }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Email -->
            <div class="border-b pb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Notifications Email</h3>
                            <p class="text-sm text-gray-500">Recevez des emails détaillés sur l'évolution de vos colis</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="email_enabled" name="email_enabled" value="1" {{ $preference->email_enabled ? 'checked' : '' }}
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="email_enabled" class="ml-2 block text-sm text-gray-900">
                            Activer les emails
                        </label>
                    </div>
                </div>

                <div id="email_settings" class="{{ $preference->email_enabled ? '' : 'hidden' }}">
                    <div class="mt-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-700">
                                <strong>Email de destination :</strong> {{ Auth::user()->email }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Événements Email</label>
                        <div class="space-y-2">
                            @foreach($availableEvents as $event => $label)
                                <div class="flex items-center">
                                    <input type="checkbox" id="email_event_{{ $event }}" name="email_events[]" value="{{ $event }}"
                                           {{ in_array($event, $preference->email_events ?? []) ? 'checked' : '' }}
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label for="email_event_{{ $event }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations importantes -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-800">Informations importantes</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Les SMS sont envoyés pour les événements que vous sélectionnez</li>
                                <li>Les frais SMS sont inclus dans votre abonnement</li>
                                <li>Vous pouvez tester les SMS en activant l'option et en cliquant sur "Envoyer un test"</li>
                                <li>Les emails contiennent plus de détails que les SMS</li>
                                <li>Vous pouvez modifier vos préférences à tout moment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t pt-6 flex justify-end space-x-3">
                <a href="{{ route('client.client.notifications.index') }}"
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Enregistrer les préférences
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const smsEnabled = document.getElementById('sms_enabled');
    const smsSettings = document.getElementById('sms_settings');
    const emailEnabled = document.getElementById('email_enabled');
    const emailSettings = document.getElementById('email_settings');

    smsEnabled.addEventListener('change', function() {
        if (this.checked) {
            smsSettings.classList.remove('hidden');
        } else {
            smsSettings.classList.add('hidden');
        }
    });

    emailEnabled.addEventListener('change', function() {
        if (this.checked) {
            emailSettings.classList.remove('hidden');
        } else {
            emailSettings.classList.add('hidden');
        }
    });
});
</script>
@endsection
