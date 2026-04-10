@extends('layouts.app')

@section('title', 'Modifier une Adresse de Réception')

@section('content')
<div class="space-y-6">
    <div class="flex items-center">
        <a href="{{ route('admin.receiving-addresses.index') }}" class="text-gray-600 hover:text-gray-900 mr-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier l'Adresse de Réception</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <form action="{{ route('admin.receiving-addresses.update', $receivingAddress) }}" method="POST" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informations générales -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Informations générales</h3>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'adresse *</label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $receivingAddress->name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="warehouse_code" class="block text-sm font-medium text-gray-700">Code entrepôt</label>
                        <input type="text" id="warehouse_code" name="warehouse_code" value="{{ old('warehouse_code', $receivingAddress->warehouse_code) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700">Personne de contact</label>
                        <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $receivingAddress->contact_person) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $receivingAddress->phone) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $receivingAddress->email) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Adresse</h3>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Adresse *</label>
                        <textarea id="address" name="address" rows="3" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address', $receivingAddress->address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Code postal *</label>
                            <input type="text" id="postal_code" name="postal_code" required value="{{ old('postal_code', $receivingAddress->postal_code) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">Ville *</label>
                            <input type="text" id="city" name="city" required value="{{ old('city', $receivingAddress->city) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Pays *</label>
                        <input type="text" id="country" name="country" required value="{{ old('country', $receivingAddress->country) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="instructions" class="block text-sm font-medium text-gray-700">Instructions spéciales</label>
                        <textarea id="instructions" name="instructions" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('instructions', $receivingAddress->instructions) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Options</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_default" name="is_default" value="1" {{ $receivingAddress->is_default ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_default" class="ml-2 block text-sm text-gray-900">
                            Définir comme adresse par défaut
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ $receivingAddress->is_active ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Adresse active
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t pt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.receiving-addresses.index') }}"
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
