@extends('layouts.app')

@section('title', 'Configuration - Carte de visite')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Configuration</h1>
        <p class="text-gray-600 mt-1">Gestion de la carte de visite du groupeur</p>
    </div>

    <!-- Navigation -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('admin.groupeur.profile') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Profil
            </a>
            <a href="{{ route('admin.groupeur.business-card') }}" class="py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
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

    <!-- Carte de visite -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Carte de visite</h2>
            <p class="text-sm text-gray-500 mt-1">Téléchargez la carte de visite du groupeur (format: JPEG, PNG, PDF)</p>
        </div>

        <div class="p-6">
            @if($groupeur->business_card_path)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Carte de visite actuelle</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if(str_ends_with(strtolower($groupeur->business_card_path), '.pdf'))
                                <div class="w-24 h-24 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @else
                                <img src="{{ $groupeur->business_card_url }}" alt="Carte de visite" class="w-24 h-24 object-cover rounded-lg">
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">
                                Fichier: {{ basename($groupeur->business_card_path) }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Téléchargé le: {{ $groupeur->business_card_uploaded_at ? $groupeur->business_card_uploaded_at->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                            <div class="mt-2">
                                <a href="{{ $groupeur->business_card_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                    Voir le fichier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-900">Aucune carte de visite téléchargée</p>
                            <p class="text-sm text-gray-500">Téléchargez une carte de visite pour le groupeur</p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.groupeur.business-card.upload') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="business_card" class="block text-sm font-medium text-gray-700">
                        {{ $groupeur->business_card_path ? 'Remplacer la carte de visite' : 'Télécharger une carte de visite' }}
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="business_card" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Télécharger un fichier</span>
                                    <input id="business_card" name="business_card" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG, PDF jusqu'à 5MB</p>
                        </div>
                    </div>
                    @error('business_card')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ $groupeur->business_card_path ? 'Remplacer' : 'Télécharger' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
