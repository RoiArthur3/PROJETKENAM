@extends('layouts.app')

@section('title', 'Détails Dépense')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-receipt text-primary me-2"></i>
                Détails de la dépense
            </h4>
            <small class="text-muted">Informations de la dépense de caisse.</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('tresorerie.depenses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Retour
            </a>
        </div>
    </div>

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
                            <div class="fw-semibold">{{ $depense->reference ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Date</div>
                            <div class="fw-semibold">{{ optional($depense->date_depense)->format('d/m/Y') ?? ($depense->date_depense ?? '—') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Caisse</div>
                            <div class="fw-semibold">{{ $depense->caisse?->nom ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Montant</div>
                            <div class="fw-semibold">{{ number_format((float)$depense->montant, 0, ',', ' ') }} {{ $depense->caisse?->devise ?? 'XOF' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Bénéficiaire</div>
                            <div class="fw-semibold">{{ $depense->beneficiaire ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Mode de paiement</div>
                            <div class="fw-semibold">{{ $depense->mode_paiement ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="text-muted small">Description</div>
                        <div>{{ $depense->description ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-check-circle text-primary me-2"></i>
                        Statut
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="badge bg-secondary">{{ $depense->statut ?? ($depense->etat ?? '—') }}</span>
                    </div>
                    <div class="small text-muted">Créée par: {{ $depense->createur?->name ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
