@extends('layouts.app')

@section('title', 'Detail ecriture financiere | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-wallet me-2 text-success"></i>Detail ecriture financiere</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('materiel.cost-control.financial-entries.edit', $entry) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Date</div>
                    <div class="fw-semibold">{{ optional($entry->transaction_date)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Type</div>
                    <div>
                        <span class="badge bg-{{ $entry->type === 'expense' ? 'danger' : 'success' }}">
                            {{ $entry->type === 'expense' ? 'Charge' : 'Revenu' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Montant</div>
                    <div class="fw-bold {{ $entry->type === 'expense' ? 'text-danger' : 'text-success' }}">
                        {{ number_format($entry->amount, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Categorie</div>
                    <div class="fw-semibold">{{ $entry->category ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Reference externe</div>
                    <div class="fw-semibold">{{ $entry->external_reference ?? '-' }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Libelle</div>
                    <div class="fw-semibold">{{ $entry->label ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Mission</div>
                    <div class="fw-semibold">{{ $entry->mission->reference ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Vehicule</div>
                    <div class="fw-semibold">{{ $entry->vehicle->immatriculation ?? $entry->mission->vehicle->immatriculation ?? '-' }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Description</div>
                    <div>{{ $entry->description ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
