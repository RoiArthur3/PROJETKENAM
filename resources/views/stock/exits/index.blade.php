@extends('layouts.app')

@section('title', 'Sorties de Stock')

@section('content')
<x-list-layout 
    title="Sorties de Stock" 
    icon="fa-minus-circle"
    createRoute="stock.exits.create"
    createText="Nouvelle Sortie"
    exportRoute="stock.exits.export"
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
                <label class="form-label">Entrepôt</label>
                <select class="form-select" id="warehouseFilter">
                    <option value="">Tous les entrepôts</option>
                    @foreach(App\Models\Warehouse::active()->get() as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Opération</label>
                <select class="form-select" id="operationFilter">
                    <option value="">Toutes les opérations</option>
                    <option value="assigned">Assignée</option>
                    <option value="internal">Interne</option>
                    <option value="loss">Perte</option>
                    <option value="damage">Dégât</option>
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
                            <th>Entrepôt</th>
                            <th>Destination</th>
                            <th>Quantité</th>
                            <th>Valeur</th>
                            <th>Utilisateur</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($exits as $exit)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $exit->id }}">
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $exit->reference }}</code>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $exit->movement_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $exit->movement_date->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $exit->product->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $exit->product->reference ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-warehouse text-primary me-2"></i>
                                        {{ $exit->warehouse->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    @if($exit->operation)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-tasks text-info me-2"></i>
                                            <div>
                                                <div class="fw-small">{{ $exit->operation->titre }}</div>
                                                <small class="text-muted">Opération #{{ $exit->operation->id }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">{{ $exit->reason ?? 'Non spécifiée' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-danger">-{{ number_format($exit->quantity, 2, ',', ' ') }}</span>
                                    <small class="text-muted">{{ $exit->product->unit ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-danger">
                                        {{ number_format($exit->total_value ?? 0, 0, ',', ' ') }} FCFA
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($exit->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-small">{{ $exit->user->name ?? 'Système' }}</div>
                                            <small class="text-muted">{{ $exit->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $exit->is_validated ? 'success' : 
                                        ($exit->is_pending ? 'warning' : 'secondary') 
                                    }}">
                                        {{ $exit->is_validated ? 'Validée' : ($exit->is_pending ? 'En attente' : 'Brouillon') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="viewExit({{ $exit->id }})"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(!$exit->is_validated)
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="validateExit({{ $exit->id }})"
                                                    title="Valider">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" 
                                                    onclick="editExit({{ $exit->id }})"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="printExit({{ $exit->id }})"
                                                title="Imprimer">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDelete({{ $exit->id }})"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="fas fa-minus-circle fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucune sortie de stock trouvée</div>
                                    <button type="button" class="btn btn-primary mt-2" 
                                            onclick="window.location.href='{{ route('stock.exits.create') }}'">
                                        <i class="fas fa-plus"></i> Enregistrer une sortie
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($exits->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $exits->firstItem() }} à {{ $exits->lastItem() }} 
                        sur {{ $exits->total() }} sorties
                    </div>
                    {{ $exits->links() }}
                </div>
            @endif
        </div>
    </div>

</x-list-layout>

<script>
function applyFilters() {
    const period = document.getElementById('periodFilter').value;
    const warehouse = document.getElementById('warehouseFilter').value;
    const operation = document.getElementById('operationFilter').value;
    
    const params = new URLSearchParams();
    if (period && period !== 'all') params.append('period', period);
    if (warehouse) params.append('warehouse_id', warehouse);
    if (operation) params.append('operation_type', operation);
    
    const url = params.toString() ? `{{ route('stock.exits') }}?${params.toString()}` : '{{ route('stock.exits') }}';
    window.location.href = url;
}

function resetFilters() {
    window.location.href = '{{ route('stock.exits') }}';
}

function viewExit(id) {
    console.log('View exit:', id);
}

function validateExit(id) {
    if (confirm('Êtes-vous sûr de vouloir valider cette sortie ?')) {
        console.log('Validate exit:', id);
    }
}

function editExit(id) {
    window.location.href = `/stock/exits/${id}/edit`;
}

function printExit(id) {
    window.open(`/stock/exits/${id}/print`, '_blank');
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette sortie ?')) {
        console.log('Delete exit:', id);
    }
}

document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
