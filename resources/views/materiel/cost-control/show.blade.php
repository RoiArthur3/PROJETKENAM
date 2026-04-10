@extends('layouts.app')

@section('title', 'Detail pointage engin | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-stopwatch me-2 text-info"></i>Detail du pointage</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('materiel.cost-control.edit', $pointage) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('materiel.cost-control.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Date</div>
                    <div class="fw-semibold">{{ optional($pointage->date_pointage)->format('d/m/Y') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Mission</div>
                    <div class="fw-semibold">{{ $pointage->mission->reference ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Vehicule</div>
                    <div class="fw-semibold">{{ $pointage->vehicle->immatriculation ?? $pointage->mission->vehicle->immatriculation ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Chauffeur</div>
                    <div class="fw-semibold">{{ $pointage->driver->nom ?? $pointage->driver->name ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Sous-module</div>
                    <div class="fw-semibold">{{ $pointage->submodule ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Quantite</div>
                    <div class="fw-semibold">{{ number_format((float) $pointage->quantity, 2, ',', ' ') }} {{ $pointage->unit_type === 'jour' ? 'jour(s)' : 'heure(s)' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Cout fournisseur</div>
                    <div class="fw-bold text-danger">{{ number_format((float) $pointage->total_supplier_cost, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Montant client</div>
                    <div class="fw-bold text-success">{{ number_format((float) $pointage->total_client_amount, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Marge</div>
                    @php $marge = (float) $pointage->total_client_amount - (float) $pointage->total_supplier_cost; @endphp
                    <div class="fw-bold {{ $marge >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($marge, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Description / Notes</div>
                    <div>{{ $pointage->description ?: ($pointage->notes ?: '-') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection