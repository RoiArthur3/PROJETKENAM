@extends('layouts.app')

@section('title', 'Client')
@section('subtitle', 'Fiche client')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="text-xs font-semibold text-blue-700">Client</div>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ $client->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $client->email }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700">Rôle: {{ $client->role }}</span>
                    @php($status = $client->account_status ?? ($client->is_active ? 'active' : 'blocked'))
                    @if($status === 'active')
                        <span class="inline-flex items-center rounded-full border border-green-200 bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Actif</span>
                    @elseif($status === 'suspended')
                        <span class="inline-flex items-center rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">Suspendu</span>
                    @else
                        <span class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Bloqué</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.clients.index') }}" class="btn-outline-primary rounded-xl px-4 py-3">Retour liste</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
        <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="text-sm font-semibold text-gray-900">Informations</div>
                    <div class="text-xs text-blue-700">Coordonnées et profil</div>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.clients.update', $client->id) }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nom / Raison sociale</label>
                                <input type="text" name="name" value="{{ old('name', $client->name) }}" class="input-text" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $client->email) }}" class="input-text" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Téléphone</label>
                                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="input-text">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Entreprise (optionnel)</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" class="input-text">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Type de client</label>
                                <select name="client_type" class="input-select" required>
                                    <option value="individual" @selected(old('client_type', $client->client_type ?? 'individual') === 'individual')>Particulier</option>
                                    <option value="business" @selected(old('client_type', $client->client_type ?? '') === 'business')>Entreprise</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Mode de notification préféré</label>
                                <select name="notification_preference" class="input-select" required>
                                    <option value="email" @selected(old('notification_preference', $client->notification_preference ?? 'email') === 'email')>Email</option>
                                    <option value="sms" @selected(old('notification_preference', $client->notification_preference ?? '') === 'sms')>SMS</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Adresse (optionnelle)</label>
                                <textarea name="address" rows="2" class="input-text">{{ old('address', $client->address) }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ville</label>
                                    <input type="text" name="city" value="{{ old('city', $client->city) }}" class="input-text">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pays</label>
                                    <input type="text" name="country" value="{{ old('country', $client->country) }}" class="input-text">
                                </div>
                            </div>
                        </div>

                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="auto_billing" value="1" class="rounded border-gray-300" @checked(old('auto_billing', (bool) ($client->auto_billing ?? false)))>
                            <span>Facturation automatique</span>
                        </label>

                        <button type="submit" class="btn-primary rounded-xl px-4 py-3 w-full">Enregistrer les informations</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="text-sm font-semibold text-gray-900">Statistiques</div>
                    <div class="text-xs text-blue-700">Finances et activité</div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-xs font-semibold text-gray-500 uppercase">Total facturé</div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($stats['total_invoiced'] ?? 0, 2) }} €</div>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-xs font-semibold text-gray-500 uppercase">Total payé</div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($stats['total_paid'] ?? 0, 2) }} €</div>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-xs font-semibold text-gray-500 uppercase">Total dû</div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($stats['total_due'] ?? 0, 2) }} €</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="text-sm font-semibold text-gray-900">Statut du compte</div>
                    <div class="text-xs text-blue-700">Actif / Bloqué / Suspendu</div>
                </div>
                <div class="p-6 space-y-3">
                    <form method="POST" action="{{ route('admin.clients.setStatus', $client->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Statut</label>
                            <select name="account_status" class="input-select" required>
                                <option value="active" @selected(($client->account_status ?? 'active') === 'active')>Actif</option>
                                <option value="blocked" @selected(($client->account_status ?? '') === 'blocked')>Bloqué</option>
                                <option value="suspended" @selected(($client->account_status ?? '') === 'suspended')>Suspendu</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary rounded-xl px-4 py-3 w-full">Mettre à jour le statut</button>
                        <div class="text-xs text-gray-500">Un client bloqué/suspendu ne peut pas se connecter.</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">Colis du client</div>
            <div class="text-xs text-blue-700">Dernières déclarations et statuts</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Suivi</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Statut</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Transport</th>
                        <th class="text-left text-xs font-semibold text-gray-600 px-6 py-3">Créé</th>
                        <th class="text-right text-xs font-semibold text-gray-600 px-6 py-3">Voir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($client->parcels as $parcel)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $parcel->tracking_number }}</div>
                                <div class="text-xs text-blue-700">{{ $parcel->origin_country }} → {{ $parcel->destination_country }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700">{{ $parcel->status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $parcel->transport_mode ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $parcel->created_at }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.receptions.show', $parcel->id) }}" class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Ouvrir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="text-sm font-semibold text-gray-900">Aucun colis</div>
                                <div class="text-sm text-blue-700 mt-1">Ce client n'a pas encore déclaré de colis.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
