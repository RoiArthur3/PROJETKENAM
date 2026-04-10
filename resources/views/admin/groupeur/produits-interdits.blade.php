@extends('layouts.app')

@section('title', 'Configuration - Produits interdits')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Configuration</h1>
        <p class="text-gray-600 mt-1">Gestion des produits interdits par pays</p>
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
            <a href="{{ route('admin.groupeur.product-catalog.index') }}" class="py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                Produits interdits
            </a>
            <a href="{{ route('admin.groupeur.users.index') }}" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
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
        <div class="text-sm font-semibold text-gray-900">Ajouter une catégorie</div>
        <form class="mt-4 flex flex-col sm:flex-row gap-3" method="POST" action="{{ route('admin.groupeur.product-catalog.categories.store') }}">
            @csrf
            <input type="text" name="name" class="input-text" placeholder="Ex: cereal" required>
            <button type="submit" class="btn-primary rounded-xl px-4 py-3">Ajouter</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="text-sm font-semibold text-gray-900">Ajouter des mots-clés</div>
        <div class="text-xs text-gray-500 mt-1">Vous pouvez en ajouter plusieurs en les séparant par des virgules</div>
        <form class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3" method="POST" action="{{ route('admin.groupeur.product-catalog.keywords.store') }}">
            @csrf
            <select name="category_id" class="input-select">
                <option value="">Sans catégorie</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <input type="text" name="keyword" class="input-text" placeholder="Ex: fonio, sorgho, mil" required>

            <select name="status" class="input-select" required>
                <option value="allowed">Autorisé (connu)</option>
                <option value="forbidden">Interdit</option>
            </select>

            <button type="submit" class="btn-primary rounded-xl px-4 py-3">Enregistrer</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="text-sm font-semibold text-gray-900">Gérer les restrictions par pays de destination</div>
        <div class="text-xs text-gray-500 mt-1">Définissez si un produit est autorisé ou interdit dans un pays de destination spécifique</div>

        <form class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3" method="POST" action="{{ route('admin.groupeur.product-catalog.destination-restrictions.store') }}">
            @csrf
            <select name="country_id" class="input-select" required>
                <option value="">Sélectionner un pays de destination</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->code }})</option>
                @endforeach
            </select>

            <select name="product_keyword_id" class="input-select" required>
                <option value="">Sélectionner un produit</option>
                @foreach($allKeywords as $keyword)
                    <option value="{{ $keyword->id }}">{{ $keyword->keyword }} ({{ $keyword->status }})</option>
                @endforeach
            </select>

            <select name="restriction_type" class="input-select" required>
                <option value="allowed">Autorisé vers ce pays</option>
                <option value="forbidden">Interdit vers ce pays</option>
            </select>

            <button type="submit" class="btn-primary rounded-xl px-4 py-3">Ajouter restriction</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="text-sm font-semibold text-gray-900">Gérer les restrictions par pays</div>
        <div class="text-xs text-gray-500 mt-1">Définissez si un produit est autorisé ou interdit dans un pays spécifique</div>

        <form class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3" method="POST" action="{{ route('admin.groupeur.product-catalog.country-restrictions.store') }}">
            @csrf
            <select name="country_id" class="input-select" required>
                <option value="">Sélectionner un pays</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->code }})</option>
                @endforeach
            </select>

            <select name="product_keyword_id" class="input-select" required>
                <option value="">Sélectionner un produit</option>
                @foreach($allKeywords as $keyword)
                    <option value="{{ $keyword->id }}">{{ $keyword->keyword }} ({{ $keyword->status }})</option>
                @endforeach
            </select>

            <select name="restriction_type" class="input-select" required>
                <option value="allowed">Autorisé dans ce pays</option>
                <option value="forbidden">Interdit dans ce pays</option>
            </select>

            <button type="submit" class="btn-primary rounded-xl px-4 py-3">Ajouter restriction</button>
        </form>
    </div>

    <div class="space-y-4">
        @foreach($categories as $category)
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="text-sm font-semibold text-gray-900">{{ $category->name }}</div>
                </div>
                <div class="p-6">
                    @if($category->keywords->isEmpty())
                        <div class="text-sm text-gray-500">Aucun mot-clé</div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($category->keywords as $kw)
                                <div class="rounded-xl border border-gray-200 p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $kw->keyword }}</div>
                                            <div class="text-xs text-gray-600">{{ $kw->status }}{{ $kw->is_active ? '' : ' (désactivé)' }}</div>
                                        </div>
                                        <form method="POST" action="{{ route('admin.groupeur.product-catalog.keywords.toggle', $kw->id) }}">
                                            @csrf
                                            <button type="submit" class="btn-outline-primary rounded-xl px-3 py-2 text-xs">{{ $kw->is_active ? 'Désactiver' : 'Activer' }}</button>
                                        </form>
                                    </div>

                                    <!-- Restrictions par pays de destination -->
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="text-xs font-semibold text-gray-700 mb-2">Restrictions par pays de destination:</div>
                                        @if($kw->countries->isEmpty())
                                            <div class="text-xs text-gray-500">Aucune restriction spécifique (utilise le statut par défaut)</div>
                                        @else
                                            <div class="space-y-1">
                                                @foreach($kw->countries as $country)
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="text-gray-600">{{ $country->name }} ({{ $country->code }})</span>
                                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $country->pivot->restriction_type === 'allowed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $country->pivot->restriction_type === 'allowed' ? 'Autorisé' : 'Interdit' }}
                                                        </span>
                                                        <form method="POST" action="{{ route('admin.groupeur.product-catalog.destination-restrictions.destroy', $country->pivot->id) }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs">Supprimer</button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
