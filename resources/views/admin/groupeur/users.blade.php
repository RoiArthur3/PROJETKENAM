@extends('layouts.app')

@section('title', 'Configuration - Utilisateurs')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Configuration</h1>
        <p class="text-gray-600 mt-1">Gestion des utilisateurs</p>
    </div>

    <div class="bg-white rounded-lg shadow border border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('admin.groupeur.profile') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
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
            <a href="{{ route('admin.groupeur.users.index') }}" class="py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                Utilisateurs
            </a>
        </nav>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="text-sm font-semibold text-gray-900">Créer un utilisateur</div>
        <form class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3" method="POST" action="{{ route('admin.groupeur.users.store') }}">
            @csrf
            <input type="text" name="name" class="input-text" placeholder="Nom complet" required>
            <input type="email" name="email" class="input-text" placeholder="Email" required>
            <input type="password" name="password" class="input-text" placeholder="Mot de passe" required>
            <input type="password" name="password_confirmation" class="input-text" placeholder="Confirmer mot de passe" required>
            <div class="flex gap-2">
                <select name="role" class="input-select flex-1" required>
                    <option value="">Rôle</option>
                    <option value="client">Client</option>
                    <option value="admin">Admin</option>
                    <option value="agent">Agent</option>
                    <option value="warehouse">Warehouse</option>
                </select>
                <button type="submit" class="btn-primary rounded-xl px-4 py-3">Créer</button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">Liste des utilisateurs</div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé le</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.groupeur.users.update.role', $user->id) }}" class="inline">
                                    @csrf
                                    <select name="role" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Client</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="agent" {{ $user->role === 'agent' ? 'selected' : '' }}>Agent</option>
                                        <option value="warehouse" {{ $user->role === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.groupeur.users.toggle', $user->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form method="POST" action="{{ route('admin.groupeur.users.toggle', $user->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
