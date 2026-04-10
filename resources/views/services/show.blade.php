@extends('layouts.app')

@section('title', 'Detail service operationnel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-building me-2 text-primary"></i>Detail service</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('services.edit', $serviceItem) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Nom</div>
                    <div class="fw-semibold">{{ $serviceItem->nom }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Code</div>
                    <div class="fw-semibold">{{ $serviceItem->code ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Email</div>
                    <div class="fw-semibold">{{ $serviceItem->email ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Telephone</div>
                    <div class="fw-semibold">{{ $serviceItem->telephone ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Responsable</div>
                    <div class="fw-semibold">{{ $serviceItem->responsable ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Statut</div>
                    <div>
                        <span class="badge bg-{{ $serviceItem->actif ? 'success' : 'warning' }}">
                            {{ $serviceItem->actif ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Couleur</div>
                    <div class="fw-semibold">{{ $serviceItem->couleur ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Icone</div>
                    <div class="fw-semibold">{{ $serviceItem->icone ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
