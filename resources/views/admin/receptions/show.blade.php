@extends('layouts.app')

@php
function getTransportModeText($mode) {
    $modes = [
        'air_express' => 'Avion - Express',
        'air_normal' => 'Avion - Normal',
        'sea' => 'Bateau - Groupage',
        'road' => 'Route',
        'rail' => 'Rail',
    ];
    return $modes[$mode] ?? $mode;
}

function getStatusText($status) {
    $statuses = [
        'announced' => 'Annoncé',
        'declared' => 'Déclaré',
        'received' => 'Reçu',
        'inspected' => 'Inspecté',
        'rejected' => 'Refusé',
        'grouped' => 'Groupé',
        'in_transit' => 'En transit',
        'arrived' => 'Arrivé',
        'fees_calculated' => 'Frais calculés',
        'paid' => 'Payé',
        'delivered' => 'Livré',
    ];
    return $statuses[$status] ?? $status;
}

function getStatusColor($status) {
    $colors = [
        'announced' => 'bg-gray-100 text-gray-800',
        'declared' => 'bg-blue-100 text-blue-800',
        'received' => 'bg-green-100 text-green-800',
        'inspected' => 'bg-yellow-100 text-yellow-800',
        'rejected' => 'bg-red-100 text-red-800',
        'grouped' => 'bg-purple-100 text-purple-800',
        'in_transit' => 'bg-indigo-100 text-indigo-800',
        'arrived' => 'bg-teal-100 text-teal-800',
        'fees_calculated' => 'bg-orange-100 text-orange-800',
        'paid' => 'bg-emerald-100 text-emerald-800',
        'delivered' => 'bg-green-100 text-green-800',
    ];
    return $colors[$status] ?? 'bg-gray-100 text-gray-800';
}
@endphp

@section('title', 'Détails de la réception')
@section('subtitle', 'Fiche de réception du colis')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="text-xs font-semibold text-blue-700">📦 Fiche de réception</div>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ $parcel->tracking_number }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ getStatusColor($parcel->status) }}">
                        📊 {{ getStatusText($parcel->status) }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-xs font-semibold">
                        🚚 {{ getTransportModeText($parcel->transport_mode) }}
                    </span>
                </div>
                <p class="mt-2 text-sm text-blue-700">
                    👤 Client: <span class="font-semibold text-gray-900">{{ $parcel->user?->name ?? 'Client inconnu' }}</span>
                    <span class="text-gray-500">({{ $parcel->user?->email ?? 'Email inconnu' }})</span>
                </p>
                @if($parcel->user?->phone)
                    <p class="text-sm text-gray-600">📱 {{ $parcel->user->phone }}</p>
                @endif
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <a href="{{ route('admin.receptions.index') }}" class="btn-outline-primary rounded-xl px-4 py-3">
                    📋 Retour à la liste
                </a>
                <a href="{{ route('admin.receptions.receipt', $parcel->id) }}" class="btn-primary rounded-xl px-4 py-3">
                    🖨️ Imprimer le reçu
                </a>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-4 text-sm text-green-800">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-800">
            ❌ {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails déclarés -->
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="text-sm font-semibold text-gray-900">📝 Informations déclarées</div>
                    <div class="text-xs text-blue-700">Informations fournies par le client lors de la déclaration</div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold text-gray-500 uppercase">📦 Contenu déclaré</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">{{ $parcel->content_description ?? 'Non spécifié' }}</div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold text-gray-500 uppercase">💰 Valeur déclarée</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $parcel->declared_value ? number_format($parcel->declared_value, 2) . ' €' : 'Non spécifiée' }}
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold text-gray-500 uppercase">⚖️ Poids estimé</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $parcel->estimated_weight ? $parcel->estimated_weight . ' kg' : 'Non spécifié' }}
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-semibold text-gray-500 uppercase">📏 Dimensions estimées</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $parcel->length && $parcel->width && $parcel->height ? $parcel->length . '×' . $parcel->width . '×' . $parcel->height . ' cm' : 'Non spécifiées' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photos -->
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="text-sm font-semibold text-gray-900">📸 Photos du colis</div>
                    <div class="text-xs text-blue-700">Photos fournies par le client et photos de réception à l'entrepôt</div>
                </div>
                <div class="p-6">
                    @php
                        $photos = $parcel->photos ?? collect();
                    @endphp

                    @if($photos->count() === 0)
                        <div class="text-center py-8">
                            <div class="text-lg font-semibold text-gray-900 mb-2">📷 Aucune photo disponible</div>
                            <div class="text-sm text-blue-700">Aucune photo n'a été ajoutée pour ce colis</div>
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($photos as $photo)
                                <a href="{{ asset('storage/' . $photo->file_path) }}" target="_blank" class="block rounded-xl overflow-hidden border border-gray-200 bg-gray-50 hover:shadow-md transition-shadow">
                                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="Photo du colis" class="h-32 w-full object-cover" />
                                    <div class="px-3 py-2">
                                        <div class="text-xs font-semibold text-gray-600">
                                            {{ $photo->type === 'client' ? '👤 Client' : '🏢 Entrepôt' }}
                                        </div>
                                        @if($photo->caption)
                                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($photo->caption, 20) }}</div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Formulaire de validation -->
            <div id="reception" class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="text-sm font-semibold text-gray-900">✅ Validation de la réception</div>
                    <div class="text-xs text-blue-700">Saisissez les informations réelles et confirmez la conformité du colis</div>
                </div>
                <div class="p-6">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <form method="POST" action="{{ route('admin.receptions.update', $parcel->id) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">⚖️ Poids réel (kg)</label>
                                        <input type="number" step="0.01" min="0" name="actual_weight" value="{{ old('actual_weight', $parcel->actual_weight) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">📏 Longueur (cm)</label>
                                        <input type="number" step="0.01" min="0" name="length" value="{{ old('length', $parcel->length) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">📏 Largeur (cm)</label>
                                        <input type="number" step="0.01" min="0" name="width" value="{{ old('width', $parcel->width) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">📏 Hauteur (cm)</label>
                                        <input type="number" step="0.01" min="0" name="height" value="{{ old('height', $parcel->height) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="md:col-span-1">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">✅ Conformité</label>
                                        <select name="conformity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <option value="">Sélectionner...</option>
                                            <option value="conforme" @selected(old('conformity') === 'conforme')>✅ Colis conforme</option>
                                            <option value="non_conforme" @selected(old('conformity') === 'non_conforme')>⚠️ Colis non conforme</option>
                                            <option value="refuse" @selected(old('conformity') === 'refuse')>❌ Colis refusé</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">📝 Motif de refus (si applicable)</label>
                                        <input type="text" name="rejection_reason" value="{{ old('rejection_reason', $parcel->rejection_reason) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Ex: contenu interdit, colis endommagé, surpoids...">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">📋 Remarques internes</label>
                                    <textarea name="notes" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Remarques, anomalies, actions à suivre...">{{ old('notes', $parcel->notes) }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">📸 Photos de réception (optionnel)</label>
                                    <input type="file" name="photos[]" multiple accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <div class="mt-1 text-xs text-gray-500">Formats acceptés: JPG/PNG. Maximum 5MB par image.</div>
                                </div>

                                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="text-xs text-gray-500">
                                        🔒 Cette action sera enregistrée dans l'historique du colis
                                    </div>
                                    <button type="submit" class="btn-primary rounded-xl px-5 py-3">
                                        ✅ Enregistrer la réception
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-sm font-semibold text-gray-900">🔒 Accès restreint</div>
                                <div class="mt-1 text-sm text-blue-700">Seul un administrateur peut valider la réception d'un colis.</div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">
                <!-- Résumé -->
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="text-sm font-semibold text-gray-900">📋 Résumé du colis</div>
                        <div class="text-xs text-blue-700">Informations clés</div>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-xs font-semibold text-gray-500 uppercase">🌍 Trajet</div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $parcel->origin_country ?? 'Inconnue' }} → {{ $parcel->destination_country ?? 'Inconnue' }}
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase">⚖️ Poids réel</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $parcel->actual_weight ? $parcel->actual_weight . ' kg' : 'Non pesé' }}
                                </div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase">📦 Volumétrique</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ number_format($parcel->calculateVolumetricWeight(), 2) }} kg
                                </div>
                            </div>
                        </div>
                        @if($parcel->received_at_warehouse_date)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase">📅 Date de réception</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $parcel->received_at_warehouse_date->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Historique -->
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="text-sm font-semibold text-gray-900">📜 Historique des statuts</div>
                        <div class="text-xs text-blue-700">Traçabilité complète</div>
                    </div>
                    <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                        @forelse($parcel->statusHistory as $h)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs text-gray-500">{{ $h->created_at->format('d/m/Y H:i') }}</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ getStatusText($h->old_status) }} → {{ getStatusText($h->new_status) }}
                                </div>
                                @if($h->comment)
                                    <div class="mt-1 text-xs text-blue-700">{{ $h->comment }}</div>
                                @endif
                                @if($h->changedBy)
                                    <div class="mt-1 text-xs text-gray-500">Par: {{ $h->changedBy->name }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <div class="text-sm text-gray-700">📋 Aucun historique disponible</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
