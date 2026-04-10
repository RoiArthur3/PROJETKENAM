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

@section('title', 'Réception des colis')
@section('subtitle', 'Gestion des réceptions')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">📦 Réception des colis</h1>
                <p class="mt-1 text-sm text-blue-700">Confirmez l'arrivée, vérifiez le contenu, pesez/mesurez et mettez à jour le statut des colis.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.scan') }}" class="btn-outline-primary rounded-xl px-4 py-3">
                    📷 Scanner un colis
                </a>
                            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">🔍 Filtres de recherche</div>
            <div class="text-xs text-blue-700">Recherchez et filtrez les colis pour trouver celui à traiter.</div>
        </div>

        <div class="p-6">
            <form method="GET" action="{{ route('admin.receptions.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">📮 Numéro de suivi</label>
                    <input type="text" name="tracking_number" value="{{ $filters['tracking_number'] ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="GRP123...">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">👤 Client (nom/email)</label>
                    <input type="text" name="client" value="{{ $filters['client'] ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Nom ou email du client">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">📅 Date d'arrivée</label>
                    <input type="date" name="arrival_date" value="{{ $filters['arrival_date'] ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">🚚 Mode de transport</label>
                    <select name="transport_mode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les modes</option>
                        <option value="air_express" @selected(($filters['transport_mode'] ?? '') === 'air_express')>✈️ Avion - Express</option>
                        <option value="air_normal" @selected(($filters['transport_mode'] ?? '') === 'air_normal')>✈️ Avion - Normal</option>
                        <option value="sea" @selected(($filters['transport_mode'] ?? '') === 'sea')>🚢 Bateau - Groupage</option>
                        <option value="road" @selected(($filters['transport_mode'] ?? '') === 'road')>🚛 Route</option>
                        <option value="rail" @selected(($filters['transport_mode'] ?? '') === 'rail')>🚂 Rail</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">📊 Statut du colis</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="announced" @selected(($filters['status'] ?? '') === 'announced')>📢 Annoncé</option>
                        <option value="declared" @selected(($filters['status'] ?? '') === 'declared')>📝 Déclaré</option>
                        <option value="received" @selected(($filters['status'] ?? '') === 'received')>✅ Reçu</option>
                        <option value="inspected" @selected(($filters['status'] ?? '') === 'inspected')>🔍 Inspecté</option>
                        <option value="grouped" @selected(($filters['status'] ?? '') === 'grouped')>📦 Groupé</option>
                        <option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>❌ Refusé</option>
                        <option value="in_transit" @selected(($filters['status'] ?? '') === 'in_transit')>🚚 En transit</option>
                        <option value="arrived" @selected(($filters['status'] ?? '') === 'arrived')>🏁 Arrivé</option>
                        <option value="fees_calculated" @selected(($filters['status'] ?? '') === 'fees_calculated')>💰 Frais calculés</option>
                        <option value="paid" @selected(($filters['status'] ?? '') === 'paid')>💳 Payé</option>
                        <option value="delivered" @selected(($filters['status'] ?? '') === 'delivered')>🎯 Livré</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" class="btn-primary rounded-xl px-4 py-3">
                        🔍 Filtrer
                    </button>
                    <a href="{{ route('admin.receptions.index') }}" class="btn-outline-primary rounded-xl px-4 py-3">
                        🔄 Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des colis -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-900">📋 Liste des colis</div>
                    <div class="text-xs text-blue-700">{{ $parcels->total() }} colis trouvés</div>
                </div>
                @if($filters && array_filter($filters))
                    <a href="{{ route('admin.receptions.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        ❌ Effacer les filtres
                    </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">📮 Suivi</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">👤 Client</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">🚚 Transport</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">📏 Dimensions</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">📊 Statut</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">📅 Date</th>
                        <th class="text-right text-xs font-semibold text-gray-600 px-6 py-3">⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($parcels as $parcel)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $parcel->tracking_number }}</div>
                                <div class="text-xs text-blue-700">
                                    🌍 {{ $parcel->origin_country ?? 'Inconnu' }} → {{ $parcel->destination_country ?? 'Inconnu' }}
                                </div>
                                @if($parcel->content_description)
                                    <div class="text-xs text-gray-500 mt-1">📦 {{ Str::limit($parcel->content_description, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $parcel->user?->name ?? 'Client inconnu' }}</div>
                                <div class="text-xs text-gray-500">{{ $parcel->user?->email ?? '-' }}</div>
                                @if($parcel->user?->phone)
                                    <div class="text-xs text-gray-500">📱 {{ $parcel->user->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ getTransportModeText($parcel->transport_mode) }}
                                </div>
                                @if($parcel->declared_value)
                                    <div class="text-xs text-gray-500">💰 {{ number_format($parcel->declared_value, 2) }} €</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($parcel->estimated_weight)
                                        ⚖️ {{ $parcel->estimated_weight }} kg
                                    @else
                                        ❓ Poids inconnu
                                    @endif
                                </div>
                                @if($parcel->length && $parcel->width && $parcel->height)
                                    <div class="text-xs text-gray-500">
                                        📏 {{ $parcel->length }}×{{ $parcel->width }}×{{ $parcel->height }} cm
                                    </div>
                                @endif
                                @if($parcel->actual_weight)
                                    <div class="text-xs text-green-600 font-semibold">
                                        ✅ Réel: {{ $parcel->actual_weight }} kg
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ getStatusColor($parcel->status) }}">
                                    {{ getStatusText($parcel->status) }}
                                </span>
                                @if($parcel->received_at_warehouse_date)
                                    <div class="text-xs text-gray-500 mt-1">
                                        📅 Reçu: {{ $parcel->received_at_warehouse_date->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $parcel->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $parcel->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.receptions.show', $parcel->id) }}"
                                       class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800 transition-colors">
                                        👁️ Voir
                                    </a>
                                    @if($parcel->status !== 'received' && $parcel->status !== 'inspected')
                                        <a href="{{ route('admin.receptions.show', $parcel->id) }}#reception"
                                           class="inline-flex items-center justify-center rounded-xl bg-green-700 px-3 py-2 text-sm font-semibold text-white hover:bg-green-800 transition-colors">
                                            ✅ Réceptionner
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center">
                                <div class="text-lg font-semibold text-gray-900 mb-2">📦 Aucun colis trouvé</div>
                                <div class="text-sm text-blue-700">
                                    @if($filters && array_filter($filters))
                                        Essayez de modifier les filtres ou de réinitialiser la recherche.
                                    @else
                                        Aucun colis n'est actuellement en attente de réception.
                                    @endif
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('admin.receptions.index') }}" class="btn-outline-primary rounded-xl px-4 py-3">
                                        🔄 Réinitialiser les filtres
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($parcels->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Affichage de {{ $parcels->firstItem() }} à {{ $parcels->lastItem() }}
                        sur {{ $parcels->total() }} colis
                    </div>
                    {{ $parcels->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
