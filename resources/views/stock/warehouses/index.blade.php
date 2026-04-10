@extends('layouts.app')

@section('title', 'Entrepôts - Stock')

@section('content')
<x-list-layout
    title="Gestion des Entrepôts"
    icon="fa-warehouse"
    createRoute="stock.warehouses.create"
    createText="Nouvel Entrepôt"
    exportRoute="stock.warehouses.export"
>

    <x-slot name="filters">
        <form method="GET" action="{{ route('stock.warehouses.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Recherche</label>
                <input type="text" name="search" class="form-control" placeholder="Nom, code, ville..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    <option value="principal" {{ request('type') == 'principal' ? 'selected' : '' }}>Principal</option>
                    <option value="secondaire" {{ request('type') == 'secondaire' ? 'selected' : '' }}>Secondaire</option>
                    <option value="depot" {{ request('type') == 'depot' ? 'selected' : '' }}>Dépôt</option>
                    <option value="boutique" {{ request('type') == 'boutique' ? 'selected' : '' }}>Boutique</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('stock.warehouses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>
                <input type="checkbox" class="form-check-input" id="selectAll">
            </th>
            <th>Code</th>
            <th>Nom</th>
            <th>Type</th>
            <th>Ville</th>
            <th>Capacité</th>
            <th>Occupation</th>
            <th>Produits</th>
            <th>Valeur Stock</th>
            <th>Statut</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($warehouses as $warehouse)
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $warehouse->id }}">
                </td>
                <td>
                    <code class="bg-light px-2 py-1 rounded">{{ $warehouse->code }}</code>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $warehouse->name }}</div>
                            <small class="text-muted">{{ $warehouse->address }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-{{
                        $warehouse->type === 'principal' ? 'primary' :
                        ($warehouse->type === 'secondaire' ? 'info' : 'secondary')
                    }}">
                        {{ ucfirst($warehouse->type) }}
                    </span>
                </td>
                <td>{{ $warehouse->city ?? '-' }}</td>
                <td>
                    @if($warehouse->capacity)
                        {{ number_format($warehouse->capacity, 0, ',', ' ') }} m³
                    @else
                        -
                    @endif
                </td>
                <td>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-{{
                            $warehouse->capacity > 0 && ($warehouse->balances_sum_current_stock / $warehouse->capacity * 100) > 80 ? 'danger' :
                            (($warehouse->capacity > 0 && ($warehouse->balances_sum_current_stock / $warehouse->capacity * 100) > 60) ? 'warning' : 'success')
                        }}"
                             style="width: {{ $warehouse->capacity > 0 ? min(($warehouse->balances_sum_current_stock / $warehouse->capacity * 100), 100) : 0 }}%"></div>
                    </div>
                    <small>{{ $warehouse->capacity > 0 ? round($warehouse->balances_sum_current_stock / $warehouse->capacity * 100, 1) : 0 }}%</small>
                </td>
                <td>
                    <span class="badge bg-info">{{ $warehouse->balances_count ?? 0 }}</span>
                </td>
                <td>
                    <span class="fw-bold text-success">
                        {{ number_format($warehouse->balances_sum_total_value ?? 0, 0, ',', ' ') }} FCFA
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'secondary' }}">
                        {{ $warehouse->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary"
                                onclick="window.location.href='{{ route('stock.warehouses.show', $warehouse) }}'"
                                title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning"
                                onclick="window.location.href='{{ route('stock.warehouses.edit', $warehouse) }}'"
                                title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info"
                                onclick="window.location.href='{{ route('stock.warehouses.stock', $warehouse) }}'"
                                title="Voir stock">
                            <i class="fas fa-boxes"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger"
                                onclick="confirmDelete({{ $warehouse->id }})"
                                title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center py-4">
                    <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucun entrepôt trouvé</div>
                    <button type="button" class="btn btn-primary mt-2"
                            onclick="window.location.href='{{ route('stock.warehouses.create') }}'">
                        <i class="fas fa-plus"></i> Créer le premier entrepôt
                    </button>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet entrepôt ?')) {
        // Implement delete functionality
        console.log('Delete warehouse:', id);
    }
}

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
