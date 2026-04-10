@extends('layouts.app')

@section('title', 'Transferts de Stock')

@section('content')
<x-list-layout 
    title="Transferts de Stock" 
    icon="fa-exchange-alt"
    createRoute="stock.transfers.create"
    createText="Nouveau Transfert"
    exportRoute="stock.transfers.export"
>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Période</label>
                <select class="form-select" id="periodFilter">
                    <option value="today">Aujourd'hui</option>
                    <option value="week" selected>Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="year">Cette année</option>
                    <option value="all">Toute période</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Entrepôt source</label>
                <select class="form-select" id="fromWarehouseFilter">
                    <option value="">Tous les entrepôts</option>
                    @foreach(App\Models\Warehouse::active()->get() as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Entrepôt destination</label>
                <select class="form-select" id="toWarehouseFilter">
                    <option value="">Tous les entrepôts</option>
                    @foreach(App\Models\Warehouse::active()->get() as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Origine</th>
                            <th>Destination</th>
                            <th>Quantité</th>
                            <th>Valeur</th>
                            <th>Utilisateur</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transfers as $transfer)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $transfer->id }}">
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $transfer->reference }}</code>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $transfer->movement_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $transfer->movement_date->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $transfer->product->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $transfer->product->reference ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-warehouse text-danger me-2"></i>
                                        <div>
                                            <div class="fw-small">{{ $transfer->warehouse->name ?? 'N/A' }}</div>
                                            <small class="text-muted">Source</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-warehouse text-success me-2"></i>
                                        <div>
                                            <div class="fw-small" id="destWarehouse{{ $transfer->id }}">Chargement...</div>
                                            <small class="text-muted">Destination</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-info">{{ number_format($transfer->quantity, 2, ',', ' ') }}</span>
                                    <small class="text-muted">{{ $transfer->product->unit ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold">
                                        {{ number_format($transfer->total_value ?? 0, 0, ',', ' ') }} FCFA
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($transfer->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-small">{{ $transfer->user->name ?? 'Système' }}</div>
                                            <small class="text-muted">{{ $transfer->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $transfer->is_validated ? 'success' : 
                                        ($transfer->is_pending ? 'warning' : 'secondary') 
                                    }}">
                                        {{ $transfer->is_validated ? 'Validé' : ($transfer->is_pending ? 'En attente' : 'Brouillon') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="viewTransfer({{ $transfer->id }})"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(!$transfer->is_validated)
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="validateTransfer({{ $transfer->id }})"
                                                    title="Valider">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" 
                                                    onclick="editTransfer({{ $transfer->id }})"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="printTransfer({{ $transfer->id }})"
                                                title="Imprimer">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDelete({{ $transfer->id }})"
                                                title="Annuler">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucun transfert de stock trouvé</div>
                                    <button type="button" class="btn btn-primary mt-2" 
                                            onclick="window.location.href='{{ route('stock.transfers.create') }}'">
                                        <i class="fas fa-plus"></i> Effectuer un transfert
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($transfers->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $transfers->firstItem() }} à {{ $transfers->lastItem() }} 
                        sur {{ $transfers->total() }} transferts
                    </div>
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>
    </div>

</x-list-layout>

<script>
// Load transfer destinations (simulated - in real app, this would come from related entry movement)
document.addEventListener('DOMContentLoaded', function() {
    @forelse ($transfers as $transfer)
        // Simulate finding the destination warehouse
        const destElement = document.getElementById('destWarehouse{{ $transfer->id }}');
        if (destElement) {
            destElement.textContent = 'Entrepôt B';
            destElement.classList.add('text-success');
        }
    @empty
    @endforelse
});

function applyFilters() {
    const period = document.getElementById('periodFilter').value;
    const fromWarehouse = document.getElementById('fromWarehouseFilter').value;
    const toWarehouse = document.getElementById('toWarehouseFilter').value;
    
    const params = new URLSearchParams();
    if (period && period !== 'all') params.append('period', period);
    if (fromWarehouse) params.append('from_warehouse_id', fromWarehouse);
    if (toWarehouse) params.append('to_warehouse_id', toWarehouse);
    
    const url = params.toString() ? `{{ route('stock.transfers') }}?${params.toString()}` : '{{ route('stock.transfers') }}';
    window.location.href = url;
}

function resetFilters() {
    window.location.href = '{{ route('stock.transfers') }}';
}

function viewTransfer(id) {
    console.log('View transfer:', id);
}

function validateTransfer(id) {
    if (confirm('Êtes-vous sûr de vouloir valider ce transfert ?')) {
        console.log('Validate transfer:', id);
    }
}

function editTransfer(id) {
    window.location.href = `/stock/transfers/${id}/edit`;
}

function printTransfer(id) {
    window.open(`/stock/transfers/${id}/print`, '_blank');
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir annuler ce transfert ?')) {
        console.log('Cancel transfer:', id);
    }
}

document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
