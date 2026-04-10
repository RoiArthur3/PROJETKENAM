@extends('layouts.app')

@section('title', 'Configuration - Profil')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Configuration</h1>
        <p class="text-gray-600 mt-1">Gestion du profil du groupeur</p>
    </div>

    <!-- Navigation -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('admin.groupeur.profile') }}" class="py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                Profil
            </a>
            <a href="{{ route('admin.groupeur.business-card') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Carte de visite
            </a>
            <a href="{{ route('admin.groupeur.shipping-address') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Adresse de livraison
            </a>
            <a href="{{ route('admin.groupeur.sms-config') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
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

    <!-- Profil Form -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Informations du groupeur</h2>
            <p class="text-sm text-gray-500 mt-1">Informations générales et contact</p>
        </div>

        <form method="POST" action="{{ route('admin.groupeur.profile.update') }}" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700">
                        Nom de l'entreprise *
                    </label>
                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name', $groupeur->business_name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700">
                        Personne à contacter *
                    </label>
                    <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $groupeur->contact_person) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('contact_person')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Téléphone *
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $groupeur->phone) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email *
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $groupeur->email) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">
                    Notes
                </label>
                <textarea id="notes" name="notes" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $groupeur->notes) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Informations supplémentaires sur le groupeur</p>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $groupeur->is_active) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Groupeur actif
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
<div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900">Configuration des prix d'envoi</h2>
        <p class="mt-1 text-sm text-gray-500">Définissez les tarifs pour chaque type de transport.</p>

        <form action="{{ route('admin.groupeur.shipping-prices.update') }}" method="POST" class="mt-6 space-y-6">
            @csrf

            @foreach(['air_normal', 'air_express', 'sea'] as $mode)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-base font-medium text-gray-900 mb-4">
                        {{ match($mode) {
                            'air_normal' => '✈️ Avion Normal',
                            'air_express' => '✈️ Avion Express',
                            'sea' => '🚢 Bateau (Cargo)',
                            default => $mode
                        }; }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Prix par kg (XOF)
                            </label>
                            <input type="number" name="prices[{{ $mode }}][price_per_kg]"
                                value="{{ $shippingPrices[$mode]->price_per_kg ?? 0 }}"
                                step="0.01" min="0" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Prix minimum (XOF)
                            </label>
                            <input type="number" name="prices[{{ $mode }}][minimum_price]"
                                value="{{ $shippingPrices[$mode]->minimum_price ?? 0 }}"
                                step="0.01" min="0" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Taux d'assurance (%)
                            </label>
                            <input type="number" name="prices[{{ $mode }}][insurance_rate]"
                                value="{{ $shippingPrices[$mode]->insurance_rate ?? 0 }}"
                                step="0.01" min="0" max="100" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Taux de douane (%)
                            </label>
                            <input type="number" name="prices[{{ $mode }}][customs_rate]"
                                value="{{ $shippingPrices[$mode]->customs_rate ?? 0 }}"
                                step="0.01" min="0" max="100" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Frais de manutention (XOF)
                            </label>
                            <input type="number" name="prices[{{ $mode }}][handling_fee]"
                                value="{{ $shippingPrices[$mode]->handling_fee ?? 0 }}"
                                step="0.01" min="0" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Frais d'emballage par carton (XOF)
                            </label>
                            <input type="number" name="prices[{{ $mode }}][packaging_fee_per_carton]"
                                value="{{ $shippingPrices[$mode]->packaging_fee_per_carton ?? 0 }}"
                                step="0.01" min="0" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="prices[{{ $mode }}][is_active]" value="1"
                                {{ ($shippingPrices[$mode]->is_active ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label class="ml-2 block text-sm text-gray-900">
                                Actif
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Notes
                        </label>
                        <textarea name="prices[{{ $mode }}][notes]" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $shippingPrices[$mode]->notes ?? '' }}</textarea>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Enregistrer les prix
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
