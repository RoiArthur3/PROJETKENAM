@extends('layouts.app')

@section('title', 'Inventaire du Stock')

@section('content')
<x-list-layout 
    title="Inventaire du Stock" 
    icon="fa-clipboard-list"
    exportRoute="stock.inventory.export"
>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Entrepôt</label>
                <select class="form-select" id="warehouseFilter">
                    <option value="">Tous les entrepôts</option>
                    @foreach(App\Models\Warehouse::active()->get() as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Catégorie</label>
                <select class="form-select" id="categoryFilter">
                    <option value="">Toutes les catégories</option>
                    <option value="matériel">Matériel</option>
                    <option value="consommable">Consommable</option>
                    <option value="outillage">Outillage</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut Stock</label>
                <select class="form-select" id="stockStatusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="normal">Normal</option>
                    <option value="low">Stock bas</option>
                    <option value="critical">Stock critique</option>
                    <option value="empty">Rupture</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Résumé de l'inventaire -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stockLevels->count() }}</h4>
                            <small>Lignes d'inventaire</small>
                        </div>
                        <i class="fas fa-list fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($stockLevels->sum('current_stock'), 0, ',', ' ') }}</h4>
                            <small>Quantité totale</small>
                        </div>
                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($stockLevels->sum('total_value'), 0, ',', ' ') }}</h4>
                            <small>Valeur totale (FCFA)</small>
                        </div>
                        <i class="fas fa-coins fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stockLevels->where('low_stock_alert', true)->count() }}</h4>
                            <small>Alertes stock</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Produit</th>
                            <th>Référence</th>
                            <th>Entrepôt</th>
                            <th>Stock Actuel</th>
                            <th>Stock Réservé</th>
                            <th>Stock Disponible</th>
                            <th>Stock Min</th>
                            <th>Valeur Unitaire</th>
                            <th>Valeur Totale</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockLevels as $stockLevel)
                            @php
                                $stockStatus = 'normal';
                                $stockBadge = 'success';
                                $stockText = 'Normal';
                                
                                if ($stockLevel->current_stock == 0) {
                                    $stockStatus = 'empty';
                                    $stockBadge = 'danger';
                                    $stockText = 'Rupture';
                                } elseif ($stockLevel->critical_stock_alert) {
                                    $stockStatus = 'critical';
                                    $stockBadge = 'danger';
                                    $stockText = 'Critique';
                                } elseif ($stockLevel->low_stock_alert) {
                                    $stockStatus = 'low';
                                    $stockBadge = 'warning';
                                    $stockText = 'Bas';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $stockLevel->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $stockLevel->product->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $stockLevel->product->category->name ?? 'Non catégorisé' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $stockLevel->product->reference ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-warehouse text-primary me-2"></i>
                                        {{ $stockLevel->warehouse->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $stockStatus === 'empty' ? 'text-danger' : ($stockStatus === 'critical' ? 'text-warning' : '') }}">
                                        {{ number_format($stockLevel->current_stock, 2, ',', ' ') }}
                                    </span>
                                    <small class="text-muted">{{ $stockLevel->product->unit ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-warning">
                                        {{ number_format($stockLevel->reserved_stock, 2, ',', ' ') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        {{ number_format($stockLevel->available_stock, 2, ',', ' ') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $stockLevel->product->min_stock_level ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">
                                        {{ number_format($stockLevel->average_cost ?? 0, 0, ',', ' ') }} FCFA
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">
                                        {{ number_format($stockLevel->total_value ?? 0, 0, ',', ' ') }} FCFA
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $stockBadge }}">{{ $stockText }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="viewStockDetails({{ $stockLevel->id }})"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="createEntry({{ $stockLevel->product_id }}, {{ $stockLevel->warehouse_id }})"
                                                title="Entrée stock">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" 
                                                onclick="createExit({{ $stockLevel->product_id }}, {{ $stockLevel->warehouse_id }})"
                                                title="Sortie stock">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="adjustStock({{ $stockLevel->id }})"
                                                title="Ajuster">
                                            <i class="fas fa-sliders-h"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucune donnée d'inventaire trouvée</div>
                                    <button type="button" class="btn btn-primary mt-2" onclick="initializeInventory()">
                                        <i class="fas fa-plus"></i> Initialiser l'inventaire
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($stockLevels->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $stockLevels->firstItem() }} à {{ $stockLevels->lastItem() }} 
                        sur {{ $stockLevels->total() }} lignes
                    </div>
                    {{ $stockLevels->links() }}
                </div>
            @endif
        </div>
    </div>

</x-list-layout>

<script>
function applyFilters() {
    const warehouse = document.getElementById('warehouseFilter').value;
    const category = document.getElementById('categoryFilter').value;
    const stockStatus = document.getElementById('stockStatusFilter').value;
    
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        let show = true;
        
        if (warehouse) {
            const warehouseText = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase();
            show = show && warehouseText?.includes(warehouse.toLowerCase());
        }
        
        if (category) {
            const categoryText = row.querySelector('td:nth-child(1) small')?.textContent.toLowerCase();
            show = show && categoryText?.includes(category.toLowerCase());
        }
        
        if (stockStatus) {
            const statusBadge = row.querySelector('td:nth-child(11) .badge')?.textContent.toLowerCase();
            show = show && statusBadge?.includes(
                stockStatus === 'normal' ? 'normal' : 
                stockStatus === 'low' ? 'bas' : 
                stockStatus === 'critical' ? 'critique' : 'rupture'
            );
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('warehouseFilter').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('stockStatusFilter').value = '';
    applyFilters();
}

function viewStockDetails(id) {
    console.log('View stock details:', id);
}

function createEntry(productId, warehouseId) {
    window.location.href = `/stock/entries/create?product_id=${productId}&warehouse_id=${warehouseId}`;
}

function createExit(productId, warehouseId) {
    window.location.href = `/stock/exits/create?product_id=${productId}&warehouse_id=${warehouseId}`;
}

function adjustStock(id) {
    console.log('Adjust stock:', id);
}

function initializeInventory() {
    if (confirm('Êtes-vous sûr de vouloir initialiser l\'inventaire ? Cela créera des lignes pour tous les produits dans tous les entrepôts.')) {
        console.log('Initialize inventory');
    }
}

document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
