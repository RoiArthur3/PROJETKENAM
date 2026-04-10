@extends('layouts.app')

@section('title', 'Pointages à Facturer | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    {{-- ── En-tête ────────────────────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar me-2 text-warning"></i>Pointages à Facturer
            </h1>
            <p class="text-muted mb-0">Sélectionnez et facturez les pointages au client</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('materiel.cost-control.plateau.chrono.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Retour Dashboard
            </a>
        </div>
    </div>

    {{-- ── Résumé à facturer ────────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Pointages</h6>
                    <h3 class="text-primary">{{ $summary['total_pointages'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Heures</h6>
                    <h3 class="text-info">{{ number_format($summary['total_hours'] ?? 0, 1, ',', '') }} h</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">À Facturer</h6>
                    <h3 class="text-warning">{{ number_format($summary['total_client_amount'] ?? 0, 0, ',', ' ') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Marge</h6>
                    <h3 class="text-success">{{ number_format($summary['total_margin'] ?? 0, 0, ',', ' ') }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filtres ────────────────────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('materiel.cost-control.plateau.chrono.to-invoice') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date début</label>
                    <input type="date" id="date_from" name="date_from" class="form-control"
                           value="{{ $dateFrom ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date fin</label>
                    <input type="date" id="date_to" name="date_to" class="form-control"
                           value="{{ $dateTo ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="vehicle_id" class="form-label">Véhicule</label>
                    <select id="vehicle_id" name="vehicle_id" class="form-select">
                        <option value="">-- Tous les véhicules --</option>
                        @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ $vehicleId == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->immatriculation }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" id="search" name="search" class="form-control"
                           placeholder="Tâche, localisation..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('materiel.cost-control.plateau.chrono.to-invoice') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tableau des pointages ────────────────────────────────────────── --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Liste des pointages
                <span class="badge bg-primary">{{ $summary['total_pointages'] ?? 0 }}</span>
            </h5>
            @if($summary['total_pointages'] > 0)
            <button type="button" class="btn btn-sm btn-success" id="invoiceBtn">
                <i class="fas fa-check-circle me-1"></i>Marquer comme facturés
            </button>
            @endif
        </div>
        <div class="card-body">
            @forelse($pointages as $pointage)
            <div class="card mb-3 pointage-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <input type="checkbox" class="form-check-input pointage-checkbox" 
                                   value="{{ $pointage->id }}" data-amount="{{ $pointage->total_client_amount }}">
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1">{{ $pointage->task_label }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $pointage->departure_location }}
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>{{ $pointage->date_pointage->format('d/m/Y') }}
                                <i class="fas fa-clock me-1 ms-2"></i>{{ \Carbon\Carbon::createFromTimeString($pointage->heure_depart)->format('H:i') }}
                            </small>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="h6 mb-1">{{ $pointage->vehicle?->immatriculation }}</div>
                                <small class="text-muted">{{ $pointage->driver?->nom }}</small>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="text-center">
                                <strong>{{ number_format($pointage->quantity, 2, ',', '') }}</strong><br>
                                <small>heures</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-end">
                                <h6 class="mb-0">{{ number_format($pointage->total_client_amount, 0, ',', ' ') }}</h6>
                                <small class="text-muted">À facturer</small><br>
                                <small class="text-success">
                                    Marge: {{ number_format($pointage->total_client_amount - $pointage->total_supplier_cost, 0, ',', ' ') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" 
                                    data-bs-target="#detailModal{{ $pointage->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Détails -->
            <div class="modal fade" id="detailModal{{ $pointage->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $pointage->task_label }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Véhicule</strong><br>
                                    {{ $pointage->vehicle?->immatriculation }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Chauffeur</strong><br>
                                    {{ $pointage->driver?->nom }} {{ $pointage->driver?->prenoms }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>De</strong><br>
                                    {{ $pointage->departure_location }}
                                </div>
                                <div class="col-md-6">
                                    <strong>À</strong><br>
                                    {{ $pointage->arrival_location }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Heures</strong><br>
                                    <h5>{{ number_format($pointage->quantity, 2, ',', '') }} h</h5>
                                </div>
                                <div class="col-md-4">
                                    <strong>Coût/heure</strong><br>
                                    <h5>{{ number_format($pointage->supplier_unit_cost, 0, ',', ' ') }}</h5>
                                </div>
                                <div class="col-md-4">
                                    <strong>Prix/heure</strong><br>
                                    <h5>{{ number_format($pointage->client_unit_price, 0, ',', ' ') }}</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Coût Fournisseur</strong><br>
                                    <h6>{{ number_format($pointage->total_supplier_cost, 0, ',', ' ') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <strong>Montant Client</strong><br>
                                    <h5 class="text-success">{{ number_format($pointage->total_client_amount, 0, ',', ' ') }}</h5>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3 mb-0">
                                <strong>Marge:</strong> {{ number_format($pointage->total_client_amount - $pointage->total_supplier_cost, 0, ',', ' ') }}
                                ({{ round(($pointage->total_client_amount - $pointage->total_supplier_cost) / $pointage->total_client_amount * 100, 1) }}%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>Aucun pointage à facturer pour le moment.
            </div>
            @endforelse

            {{-- Pagination --}}
            @if($pointages->count() > 0)
            <div class="mt-4">
                {{ $pointages->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.getElementById('invoiceBtn').addEventListener('click', async function() {
    const selected = Array.from(document.querySelectorAll('.pointage-checkbox:checked'))
        .map(el => parseInt(el.value));

    if (selected.length === 0) {
        alert('Veuillez sélectionner au moins un pointage');
        return;
    }

    if (!confirm(`Marquer ${selected.length} pointage(s) comme facturés ?`)) {
        return;
    }

    try {
        const response = await fetch('{{ route("materiel.cost-control.plateau.chrono.mark-invoiced") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value || 
                                document.querySelector('[name="_token"]')?.content
            },
            body: JSON.stringify({ pointage_ids: selected })
        });

        const data = await response.json();

        if (data.success) {
            // Rafraîchir la page
            window.location.reload();
        } else {
            alert('Erreur: ' + (data.error || 'Impossible de facturer'));
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur réseau');
    }
});

// Sélectionner tous les pointages
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            // Marquer globalement
        });
    }
});
</script>
@endsection
