@extends('layouts.app')

@section('title', 'Clients')
@section('subtitle', 'Gestion des clients')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Clients</h1>
                <p class="mt-1 text-sm text-blue-700">Rechercher, consulter et activer/désactiver les comptes clients.</p>
            </div>
            <div class="flex items-center gap-2">
                            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">Liste des clients</div>
            <div class="text-xs text-blue-700">Filtrer par nom/email et statut d’activation.</div>
        </div>

        <div class="p-6">
            <form method="GET" action="{{ route('admin.clients.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Recherche</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="input-text" placeholder="Nom ou email">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Statut</label>
                    <select name="active" class="input-select">
                        <option value="">Tous</option>
                        <option value="1" @selected(($filters['active'] ?? '') === '1')>Actifs</option>
                        <option value="0" @selected(($filters['active'] ?? '') === '0')>Désactivés</option>
                    </select>
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Rôle</label>
                    <select name="role" class="input-select">
                        <option value="client" @selected(($filters['role'] ?? 'client') === 'client')>Client</option>
                        <option value="warehouse" @selected(($filters['role'] ?? '') === 'warehouse')>Entrepôt</option>
                        <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Admin</option>
                    </select>
                </div>

                <div class="md:col-span-6 flex items-end gap-2">
                    <button type="submit" class="btn-primary rounded-xl px-4 py-3">Filtrer</button>
                    <a href="{{ route('admin.clients.index') }}" class="btn-outline-primary rounded-xl px-4 py-3">Réinitialiser</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto border-t border-gray-200">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Client</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Type</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Rôle</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Statut</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Colis</th>
                        <th class="text-right text-xs font-semibold text-gray-600 px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $client->name }}</div>
                                <div class="text-xs text-gray-500">{{ $client->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700">
                                    {{ ($client->client_type ?? 'individual') === 'business' ? 'Entreprise' : 'Particulier' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700">{{ $client->role }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php($status = $client->account_status ?? ($client->is_active ? 'active' : 'blocked'))
                                @if($status === 'active')
                                    <span class="inline-flex items-center rounded-full border border-green-200 bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Actif</span>
                                @elseif($status === 'suspended')
                                    <span class="inline-flex items-center rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">Suspendu</span>
                                @else
                                    <span class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Bloqué</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $client->parcels_count }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.clients.show', $client->id) }}" class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Ouvrir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="text-sm font-semibold text-gray-900">Aucun client trouvé</div>
                                <div class="text-sm text-blue-700 mt-1">Modifie les filtres ou réinitialise la recherche.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $clients->links() }}
        </div>
    </div>
</div>
@endsection
