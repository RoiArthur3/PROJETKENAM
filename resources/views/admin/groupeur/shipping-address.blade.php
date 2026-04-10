@extends('layouts.app')

@section('title', 'Configuration - Adresse de livraison')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Configuration</h1>
        <p class="text-gray-600 mt-1">Gestion de l'adresse de livraison à remettre au vendeur</p>
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
            <a href="{{ route('admin.groupeur.shipping-address') }}" class="py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
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

    <!-- Adresse de livraison -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Adresse de livraison</h2>
            <p class="text-sm text-gray-500 mt-1">Adresse à remettre au vendeur pour la livraison des colis</p>
        </div>

        <form method="POST" action="{{ route('admin.groupeur.shipping-address.update') }}" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="shipping_address" class="block text-sm font-medium text-gray-700">
                    Adresse de livraison *
                </label>
                <textarea id="shipping_address" name="shipping_address" rows="3" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('shipping_address', $groupeur->shipping_address) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Adresse complète où le vendeur doit livrer les colis</p>
                @error('shipping_address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700">
                        Code postal *
                    </label>
                    <input type="text" id="shipping_postal_code" name="shipping_postal_code" value="{{ old('shipping_postal_code', $groupeur->shipping_postal_code) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('shipping_postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_city" class="block text-sm font-medium text-gray-700">
                        Ville *
                    </label>
                    <input type="text" id="shipping_city" name="shipping_city" value="{{ old('shipping_city', $groupeur->shipping_city) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('shipping_city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_country" class="block text-sm font-medium text-gray-700">
                        Pays *
                    </label>
                    <input type="text" id="shipping_country" name="shipping_country" value="{{ old('shipping_country', $groupeur->shipping_country) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('shipping_country')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($groupeur->full_shipping_address)
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Adresse actuelle</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>{{ $groupeur->full_shipping_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Enregistrer l'adresse
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
