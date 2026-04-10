@extends('layouts.app')

@section('title', 'Detail type operation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-info-circle me-2 text-primary"></i>Detail type de requete</h1>
            <p class="text-muted mb-0">Consultation du type d'operation</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('types-operations.edit', $typeOperation) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('types-operations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Code</div>
                    <div class="fw-semibold">{{ $typeOperation->code }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Libelle</div>
                    <div class="fw-semibold">{{ $typeOperation->libelle }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Statut</div>
                    <div>
                        <span class="badge bg-{{ $typeOperation->actif ? 'success' : 'secondary' }}">
                            {{ $typeOperation->actif ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Couleur</div>
                    <div class="fw-semibold">{{ $typeOperation->couleur ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Icone</div>
                    <div class="fw-semibold">{{ $typeOperation->icone ?? '-' }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Description</div>
                    <div>{{ $typeOperation->description ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
