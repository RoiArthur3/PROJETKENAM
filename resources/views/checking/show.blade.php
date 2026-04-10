@extends('layouts.app')

@section('title', 'Détails de la Vérification')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Vérification #{{ $checking->id }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('fleet.checking.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('fleet.checking.edit', $checking->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Titre</div>
                        <div class="col-sm-8">{{ $checking->title }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Description</div>
                        <div class="col-sm-8">{{ $checking->description ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Type</div>
                        <div class="col-sm-8"><span class="badge bg-info">{{ $checking->type }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Statut</div>
                        <div class="col-sm-8"><span class="badge bg-secondary">{{ $checking->formatted_status }}</span></div>
                    </div>
                </div>
            </div>

            @if($checking->checklist)
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Checklist : {{ $checking->checklist->name }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Éléments à vérifier selon la checklist.</p>
                    <ul class="list-group">
                        @foreach($checking->checklist->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->name }}
                            <span class="badge bg-light text-dark">{{ $item->formatted_type }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Affectation</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold d-block">Inspecteur</label>
                        <span>{{ optional($checking->inspector)->name ?? 'Non assigné' }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold d-block">Véhicule / Matériel</label>
                        @if($checking->checkable && \Illuminate\Support\Str::contains($checking->checkable_type, 'Vehicule'))
                            <a href="{{ route('fleet.vehicules.show', $checking->checkable->id) }}">
                                {{ $checking->checkable->immatriculation }} - {{ $checking->checkable->marque }}
                            </a>
                        @else
                            <span class="text-muted">Aucun matériel lié</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold d-block">Planifié le</label>
                        <span>{{ optional($checking->scheduled_at)->format('d/m/Y H:i') ?? 'Non planifié' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
