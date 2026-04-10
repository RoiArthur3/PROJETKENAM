@extends('layouts.app')

@section('title', 'Catalogue produits')
@section('subtitle', 'Mots-clés autorisés / interdits')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="text-sm font-semibold text-gray-900">Ajouter une catégorie</div>
        <form class="mt-4 flex flex-col sm:flex-row gap-3" method="POST" action="{{ route('admin.product-catalog.categories.store') }}">
            @csrf
            <input type="text" name="name" class="input-text" placeholder="Ex: cereal" required>
            <button type="submit" class="btn-primary rounded-xl px-4 py-3">Ajouter</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="text-sm font-semibold text-gray-900">Ajouter un mot-clé</div>
        <form class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3" method="POST" action="{{ route('admin.product-catalog.keywords.store') }}">
            @csrf
            <select name="category_id" class="input-select">
                <option value="">Sans catégorie</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <input type="text" name="keyword" class="input-text" placeholder="Ex: fonio" required>

            <select name="status" class="input-select" required>
                <option value="allowed">Autorisé (connu)</option>
                <option value="forbidden">Interdit</option>
            </select>

            <button type="submit" class="btn-primary rounded-xl px-4 py-3">Enregistrer</button>
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
                                <div class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $kw->keyword }}</div>
                                        <div class="text-xs text-gray-600">{{ $kw->status }}{{ $kw->is_active ? '' : ' (désactivé)' }}</div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.product-catalog.keywords.toggle', $kw->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-outline-primary rounded-xl px-3 py-2">{{ $kw->is_active ? 'Désactiver' : 'Activer' }}</button>
                                    </form>
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
