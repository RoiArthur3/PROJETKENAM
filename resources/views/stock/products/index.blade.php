@extends('layouts.app')

@section('title', 'Produits - Stock | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Gestion des Produits"
    icon="fa-box-open"
    :createRoute="auth()->user()->hasRole(['stock', 'admin']) ? route('stock.products.create') : null"
    createText="Nouveau Produit"
    exportRoute="{{ route('stock.products.export') }}"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total Produits"
            :value="$totalProducts"
            icon="fa-boxes"
            color="primary"
            subtitle="Dans le catalogue"
        />
        <x-kpi-card
            title="Valeur Totale Stock"
            :value="number_format($totalValue, 0, ',', ' ')"
            icon="fa-euro-sign"
            color="success"
            subtitle="FCFA"
        />
        <x-kpi-card
            title="Produits Actifs"
            :value="$activeProducts"
            icon="fa-check-circle"
            color="info"
            subtitle="{{ round($activeProducts / max($totalProducts, 1) * 100, 1) }}% du total"
        />
        <x-kpi-card
            title="Stock Faible"
            :value="$lowStockProducts"
            icon="fa-exclamation-triangle"
            color="warning"
            subtitle="À réapprovisionner"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Recherche</label>
                <input type="text" name="search" class="form-control" placeholder="Nom, référence..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Catégorie</label>
                <select name="category" class="form-select">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('stock.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Référence</th>
            <th>Produit</th>
            <th>Catégorie</th>
            <th>Stock Total</th>
            <th>Valeur HT</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            <tr>
                <td>
                    <code class="bg-light px-2 py-1 rounded">{{ $product->reference }}</code>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <small class="text-muted">{{ $product->unit }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary">{{ $product->category->name ?? '-' }}</span>
                </td>
                <td>
                    <span class="fw-bold">{{ number_format($product->stock_levels_sum_current_stock ?? 0, 0, ',', ' ') }}</span>
                    @if(($product->stock_levels_sum_current_stock ?? 0) <= ($product->min_stock_level ?? 0))
                        <i class="fas fa-exclamation-triangle text-warning ms-1" title="Stock faible"></i>
                    @endif
                </td>
                <td>
                    <span class="fw-bold text-success">
                        {{ number_format($product->stock_levels_sum_total_value ?? 0, 0, ',', ' ') }} FCFA
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                        {{ $product->status === 'active' ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('stock.products.show', $product) }}" class="btn btn-outline-primary" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()->hasRole(['stock', 'admin']))
                            <a href="{{ route('stock.products.edit', $product) }}" class="btn btn-outline-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-outline-danger" title="Supprimer" onclick="confirmDelete({{ $product->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucun produit trouvé</div>
                    <p class="text-muted small">Le catalogue de produits est vide</p>
                    @if(auth()->user()->hasRole(['stock', 'admin']))
                        <a href="{{ route('stock.products.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> Créer le premier produit
                        </a>
                    @endif
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

@push('scripts')
<script>
function confirmDelete(productId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
        // Implement delete functionality
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('stock/products') }}/${productId}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
