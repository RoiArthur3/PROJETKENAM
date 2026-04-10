@extends('layouts.app')

@section('title', 'Détails Entrepôt - ' . $warehouse->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">
                <i class="fas fa-warehouse text-primary me-2"></i>
                {{ $warehouse->name }}
            </h4>
            <p class="text-muted mb-0">
                <code>{{ $warehouse->code }}</code> •
                {{ $warehouse->city ?? 'Non spécifiée' }} •
                {{ ucfirst($warehouse->type) }}
            </p>
        </div>
        <div>
            <a href="{{ route('stock.warehouses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('stock.warehouses.edit', $warehouse) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="{{ route('stock.warehouses.stock', $warehouse) }}" class="btn btn-info">
                <i class="fas fa-boxes me-2"></i>Voir Stock
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations générales -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informations générales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Code</label>
                        <div class="fw-bold"><code>{{ $warehouse->code }}</code></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Nom</label>
                        <div class="fw-bold">{{ $warehouse->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Type</label>
                        <div>
                            <span class="badge bg-{{
                                $warehouse->type === 'principal' ? 'primary' :
                                ($warehouse->type === 'secondaire' ? 'info' : 'secondary')
                            }}">
                                {{ ucfirst($warehouse->type) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Adresse</label>
                        <div>{{ $warehouse->address ?? 'Non spécifiée' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Ville</label>
                        <div>{{ $warehouse->city ?? 'Non spécifiée' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Téléphone</label>
                        <div>{{ $warehouse->phone ?? 'Non spécifié' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <div>{{ $warehouse->email ?? 'Non spécifié' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Responsable</label>
                        <div>{{ $warehouse->manager ?? 'Non spécifié' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Statut</label>
                        <div>
                            <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'secondary' }}">
                                {{ $warehouse->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Capacité -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie text-info me-2"></i>
                        Capacité et Occupation
                    </h6>
                </div>
                <div class="card-body">
                    @if($warehouse->capacity)
                        <div class="mb-3">
                            <label class="form-label text-muted">Capacité totale</label>
                            <div class="fw-bold">{{ number_format($warehouse->capacity, 0, ',', ' ') }} m³</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Taux d'occupation</label>
                            <div class="progress mb-2" style="height: 20px;">
                                @php
                                    $occupationRate = $warehouse->capacity > 0 ?
                                        min(($warehouse->stock_levels_sum_current_stock / $warehouse->capacity * 100), 100) : 0;
                                @endphp
                                <div class="progress-bar bg-{{
                                    $occupationRate > 80 ? 'danger' :
                                    ($occupationRate > 60 ? 'warning' : 'success')
                                }}"
                                     style="width: {{ $occupationRate }}%">
                                    {{ round($occupationRate, 1) }}%
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-muted">Aucune capacité définie</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stock actuel -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-boxes text-success me-2"></i>
                        Stock actuel
                    </h6>
                    <div>
                        <span class="badge bg-info">{{ $stockLevels->count() }} matériels</span>
                        <span class="badge bg-success">{{ number_format($stockLevels->sum('current_stock'), 0, ',', ' ') }} unités</span>
                        <span class="badge bg-primary">{{ number_format($stockLevels->sum('total_value'), 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Matériel</th>
                                    <th>Référence</th>
                                    <th>Stock Actuel</th>
                                    <th>Stock Réservé</th>
                                    <th>Stock Disponible</th>
                                    <th>Valeur Totale</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockLevels as $stockLevel)
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
                                            <div class="fw-semibold">{{ $stockLevel->product->name }}</div>
                                            <small class="text-muted">{{ $stockLevel->product->category->name ?? 'Non catégorisé' }}</small>
                                        </td>
                                        <td><code>{{ $stockLevel->product->reference }}</code></td>
                                        <td>
                                            <span class="fw-bold {{ $stockStatus === 'empty' ? 'text-danger' : ($stockStatus === 'critical' ? 'text-warning' : '') }}">
                                                {{ number_format($stockLevel->current_stock, 2, ',', ' ') }}
                                            </span>
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
                                            <span class="fw-bold text-primary">
                                                {{ number_format($stockLevel->total_value ?? 0, 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $stockBadge }}">{{ $stockText }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-success"
                                                        onclick="createEntry({{ $stockLevel->product_id }}, {{ $warehouse->id }})"
                                                        title="Entrée stock">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-warning"
                                                        onclick="createExit({{ $stockLevel->product_id }}, {{ $warehouse->id }})"
                                                        title="Sortie stock">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-boxes fa-2x text-muted mb-2"></i>
                                            <div class="text-muted">Aucun produit dans cet entrepôt</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-history text-warning me-2"></i>
                        Activité récente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Matériel</th>
                                    <th>Quantité</th>
                                    <th>Utilisateur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMovements as $movement)
                                    <tr>
                                        <td>
                                            <small>{{ $movement->created_at->format('d/m H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{
                                                $movement->type === 'entree' ? 'success' :
                                                ($movement->type === 'sortie' ? 'danger' : 'info')
                                            }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->product->name }}</td>
                                        <td>
                                            <span class="fw-bold {{
                                                $movement->type === 'entree' ? 'text-success' :
                                                ($movement->type === 'sortie' ? 'text-danger' : '')
                                            }}">
                                                {{ $movement->type === 'entree' ? '+' : ($movement->type === 'sortie' ? '-' : '') }}{{ $movement->quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $movement->user->name ?? 'Système' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">
                                            <small class="text-muted">Aucune activité récente</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createEntry(productId, warehouseId) {
    window.location.href = `/stock/entries/create?product_id=${productId}&warehouse_id=${warehouseId}`;
}

function createExit(productId, warehouseId) {
    window.location.href = `/stock/exits/create?product_id=${productId}&warehouse_id=${warehouseId}`;
}
</script>
@endsection
