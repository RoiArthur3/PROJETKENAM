@extends('layouts.app')

@section('title', 'Entrées de Stock')

@section('content')
<x-list-layout
    title="Entrées de Stock"
    icon="fa-plus-circle"
    createRoute="stock.entries.create"
    createText="Nouvelle Entrée"
    exportRoute="stock.entries.export"
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
                <label class="form-label">Fournisseur</label>
                <select class="form-select" id="supplierFilter">
                    <option value="">Tous les fournisseurs</option>
                    @foreach(App\Models\Fournisseur::withTrashed()->orderBy('raison_sociale')->get() as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->raison_sociale }}</option>
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

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>
                <input type="checkbox" class="form-check-input" id="selectAll">
            </th>
            <th>Référence</th>
            <th>Date</th>
            <th>Matériel</th>
            <th>Entrepôt</th>
            <th>Fournisseur</th>
            <th>Quantité</th>
            <th>Prix Unitaire</th>
            <th>Valeur Totale</th>
            <th>Utilisateur</th>
            <th>Statut</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($entries as $entry)
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $entry->id }}">
                </td>
                <td>
                    <code class="bg-light px-2 py-1 rounded">{{ $entry->reference }}</code>
                </td>
                <td>
                    <div>
                        <div>{{ $entry->movement_date->format('d/m/Y') }}</div>
                        <small class="text-muted">{{ $entry->movement_date->format('H:i') }}</small>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $entry->product->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $entry->product->reference ?? '' }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-warehouse text-primary me-2"></i>
                        {{ $entry->warehouse->name ?? 'N/A' }}
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-truck text-warning me-2"></i>
                        {{ $entry->supplier->name ?? 'Non spécifié' }}
                    </div>
                </td>
                <td>
                    <span class="fw-bold text-success">+{{ number_format($entry->quantity, 2, ',', ' ') }}</span>
                    <small class="text-muted">{{ $entry->product->unit ?? '' }}</small>
                </td>
                <td>
                    <span class="fw-bold">{{ number_format($entry->unit_price ?? 0, 0, ',', ' ') }} FCFA</span>
                </td>
                <td>
                    <span class="fw-bold text-success">
                        {{ number_format($entry->total_value ?? 0, 0, ',', ' ') }} FCFA
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            {{ strtoupper(substr($entry->user->name ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-small">{{ $entry->user->name ?? 'Système' }}</div>
                            <small class="text-muted">{{ $entry->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-{{
                        $entry->is_validated ? 'success' :
                        ($entry->is_pending ? 'warning' : 'secondary')
                    }}">
                        {{ $entry->is_validated ? 'Validé' : ($entry->is_pending ? 'En attente' : 'Brouillon') }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary"
                                onclick="viewEntry({{ $entry->id }})"
                                title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if(!$entry->is_validated)
                            <button type="button" class="btn btn-outline-success"
                                    onclick="validateEntry({{ $entry->id }})"
                                    title="Valider">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning"
                                    onclick="editEntry({{ $entry->id }})"
                                    title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline-info"
                                onclick="printEntry({{ $entry->id }})"
                                title="Imprimer">
                            <i class="fas fa-print"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger"
                                onclick="confirmDelete({{ $entry->id }})"
                                title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center py-4">
                    <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucune entrée de stock trouvée</div>
                    <button type="button" class="btn btn-primary mt-2"
                            onclick="window.location.href='{{ route('stock.entries.create') }}'">
                        <i class="fas fa-plus"></i> Enregistrer une entrée
                    </button>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

<script>
function applyFilters() {
    const period = document.getElementById('periodFilter').value;
    const warehouse = document.getElementById('warehouseFilter').value;
    const supplier = document.getElementById('supplierFilter').value;

    // Build URL with filters
    const params = new URLSearchParams();
    if (period && period !== 'all') params.append('period', period);
    if (warehouse) params.append('warehouse_id', warehouse);
    if (supplier) params.append('supplier_id', supplier);

    const url = params.toString() ? `{{ route('stock.entries') }}?${params.toString()}` : '{{ route('stock.entries') }}';
    window.location.href = url;
}

function resetFilters() {
    window.location.href = '{{ route('stock.entries') }}';
}

function viewEntry(id) {
    // Implement view functionality
    console.log('View entry:', id);
}

function validateEntry(id) {
    if (confirm('Êtes-vous sûr de vouloir valider cette entrée ?')) {
        // Implement validation via AJAX
        console.log('Validate entry:', id);
    }
}

function editEntry(id) {
    window.location.href = `/stock/entries/${id}/edit`;
}

function printEntry(id) {
    window.open(`/stock/entries/${id}/print`, '_blank');
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?')) {
        // Implement delete functionality
        console.log('Delete entry:', id);
    }
}

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
