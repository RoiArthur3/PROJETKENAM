@extends('layouts.app')

@section('title', 'Créer une Étiquette d\'Expédition')

@section('content')
<div class="space-y-6">
    <div class="flex items-center">
        <a href="{{ route('client.shipping-labels.index') }}" class="text-gray-600 hover:text-gray-900 mr-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Créer une Étiquette d\'Expédition</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <form action="{{ route('client.shipping-labels.store') }}" method="POST" class="space-y-6 p-6">
            @csrf

            <!-- Adresse de réception -->
            @if($defaultAddress)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-blue-800">Adresse de réception par défaut</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p class="font-medium">{{ $defaultAddress->name }}</p>
                                <p>{{ $defaultAddress->address }}</p>
                                <p>{{ $defaultAddress->postal_code }} {{ $defaultAddress->city }}, {{ $defaultAddress->country }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informations expéditeur -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Informations de l'expéditeur</h3>

                    <div>
                        <label for="sender_name" class="block text-sm font-medium text-gray-700">Nom complet *</label>
                        <input type="text" id="sender_name" name="sender_name" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Jean Dupont">
                    </div>

                    <div>
                        <label for="sender_address" class="block text-sm font-medium text-gray-700">Adresse *</label>
                        <textarea id="sender_address" name="sender_address" rows="2" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="123 Rue de la République"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="sender_postal_code" class="block text-sm font-medium text-gray-700">Code postal *</label>
                            <input type="text" id="sender_postal_code" name="sender_postal_code" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="75001">
                        </div>
                        <div>
                            <label for="sender_city" class="block text-sm font-medium text-gray-700">Ville *</label>
                            <input type="text" id="sender_city" name="sender_city" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Paris">
                        </div>
                    </div>

                    <div>
                        <label for="sender_country" class="block text-sm font-medium text-gray-700">Pays *</label>
                        <input type="text" id="sender_country" name="sender_country" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="France">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="sender_phone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
                            <input type="tel" id="sender_phone" name="sender_phone" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="+33 6 12 34 56 78">
                        </div>
                        <div>
                            <label for="sender_email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="sender_email" name="sender_email" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="jean.dupont@email.com">
                        </div>
                    </div>
                </div>

                <!-- Informations colis -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Informations du colis</h3>

                    <div>
                        <label for="parcel_contents" class="block text-sm font-medium text-gray-700">Description du contenu *</label>
                        <textarea id="parcel_contents" name="parcel_contents" rows="3" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Électronique, vêtements, livres..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="declared_value" class="block text-sm font-medium text-gray-700">Valeur déclarée (€)</label>
                            <input type="number" id="declared_value" name="declared_value" step="0.01" min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="50.00">
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700">Poids (kg)</label>
                            <input type="number" id="weight" name="weight" step="0.01" min="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="2.5">
                        </div>
                    </div>

                    <div>
                        <label for="purchase_store" class="block text-sm font-medium text-gray-700">Magasin d'achat</label>
                        <input type="text" id="purchase_store" name="purchase_store"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Amazon, eBay, AliExpress...">
                    </div>

                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700">Date d'achat</label>
                        <input type="date" id="purchase_date" name="purchase_date"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    @if($addresses->count() > 1)
                        <div>
                            <label for="receiving_address_id" class="block text-sm font-medium text-gray-700">Adresse de réception</label>
                            <select id="receiving_address_id" name="receiving_address_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Utiliser l'adresse par défaut</option>
                                @foreach($addresses as $address)
                                    @if(!$address->is_default)
                                        <option value="{{ $address->id }}">{{ $address->name }} - {{ $address->city }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Instructions -->
            <div class="border-t pt-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">Instructions importantes</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Imprimez cette étiquette et collez-la sur votre colis</li>
                                    <li>Donnez cette étiquette à la boutique marchande pour l'expédition</li>
                                    <li>Conservez une copie pour votre suivi</li>
                                    <li>Le colis sera envoyé à l'adresse de réception indiquée</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t pt-6 flex justify-end space-x-3">
                <a href="{{ route('client.shipping-labels.index') }}"
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Créer l'étiquette
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
