@extends('layouts.app')

@section('title', 'Détails de l\'approvisionnement')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-money-bill-transfer text-primary me-2"></i>
                Détails de l'approvisionnement
            </h4>
            <small class="text-muted">Consultez les informations de la demande d'approvisionnement.</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('tresorerie.approvisionnements.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Référence</div>
                            <div class="fw-semibold">{{ $approvisionnement->numero_operation ?? $approvisionnement->reference ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Statut</div>
                            <div class="fw-semibold">{{ $approvisionnement->statut ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Caisse source</div>
                            <div class="fw-semibold">{{ $approvisionnement->source->nom ?? $approvisionnement->source->libelle ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Caisse destination</div>
                            <div class="fw-semibold">{{ $approvisionnement->destination->nom ?? $approvisionnement->destination->libelle ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Montant</div>
                            <div class="fw-semibold">{{ number_format($approvisionnement->montant ?? 0, 2, ',', ' ') }} {{ $approvisionnement->devise ?? 'XOF' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Demandeur</div>
                            <div class="fw-semibold">{{ $approvisionnement->demandeur->name ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="text-muted small">Motif</div>
                        <div class="fw-semibold">{{ $approvisionnement->motif ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Actions
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('tresorerie.approvisionnements.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-list me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
