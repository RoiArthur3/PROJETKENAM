@extends('layouts.app')

@section('title', 'Demande ' . $demande->numero_demande)

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                {{ $demande->numero_demande }}
            </h4>
            <small class="text-muted">Demande d'approvisionnement — créée le {{ $demande->created_at->format('d/m/Y à H:i') }}</small>
        </div>
        <a href="{{ route('tresorerie.approvisionnement-demandes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Infos principales --}}
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle me-2"></i>Détails de la demande</span>
                    <span class="badge bg-{{ $demande->statutColor() }} fs-6">
                        <i class="{{ $demande->statutIcon() }} me-1"></i>
                        {{ $demande->statutLabel() }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Montant demandé</label>
                            <div class="fs-4 fw-bold text-success">
                                {{ number_format($demande->montant, 0, ',', ' ') }}
                                <small class="fs-6">{{ $demande->devise }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Demandeur</label>
                            <div class="fw-semibold">
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $demande->demandeur->name ?? '—' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Caisse source</label>
                            <div>
                                <i class="fas fa-cash-register me-1 text-muted"></i>
                                {{ $demande->caisseSource->nom ?? '—' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Caisse destination</label>
                            <div>
                                <i class="fas fa-wallet me-1 text-muted"></i>
                                {{ $demande->caisseDestination->nom ?? '—' }}
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Raison / Justification</label>
                            <div class="border rounded p-3 bg-light">
                                {{ $demande->raison ?? '—' }}
                            </div>
                        </div>
                        @if($demande->commentaire_dg)
                        <div class="col-12">
                            <label class="text-muted small">Commentaire de la DG</label>
                            <div class="border rounded p-3 bg-info bg-opacity-10">
                                <i class="fas fa-comment-dots me-1"></i>
                                {{ $demande->commentaire_dg }}
                            </div>
                        </div>
                        @endif
                        @if($demande->isRejected() && $demande->rejection_reason)
                        <div class="col-12">
                            <label class="text-muted small text-danger">Motif de refus</label>
                            <div class="border border-danger rounded p-3 bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                {{ $demande->rejection_reason }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Approvisionnement lié --}}
            @if($demande->isExecuted() && $demande->approvisionnementCaisse)
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-check-double me-2"></i>Approvisionnement exécuté
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Référence approvisionnement</label>
                            <div class="fw-semibold">{{ $demande->approvisionnementCaisse->numero_operation }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Exécuté le</label>
                            <div>{{ $demande->executed_at?->format('d/m/Y à H:i') ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Timeline statut --}}
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fas fa-history me-2"></i>Suivi de la demande
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        {{-- Créée --}}
                        <li class="d-flex gap-3 mb-3">
                            <div class="text-success mt-1"><i class="fas fa-circle fs-6"></i></div>
                            <div>
                                <div class="fw-semibold">Demande créée</div>
                                <small class="text-muted">{{ $demande->created_at->format('d/m/Y H:i') }}</small>
                                <div class="small">Par {{ $demande->demandeur->name ?? '—' }}</div>
                            </div>
                        </li>
                        {{-- SMS envoyé --}}
                        <li class="d-flex gap-3 mb-3">
                            <div class="text-info mt-1"><i class="fas fa-envelope fs-6"></i></div>
                            <div>
                                <div class="fw-semibold">Comptabilité notifiée par email</div>
                                <small class="text-muted">Puis transfert email à la DG pour validation finale</small>
                            </div>
                        </li>
                        {{-- Décision --}}
                        @if($demande->isApproved() || $demande->isExecuted())
                        <li class="d-flex gap-3 mb-3">
                            <div class="text-primary mt-1"><i class="fas fa-check-circle fs-6"></i></div>
                            <div>
                                <div class="fw-semibold">Approuvée</div>
                                <small class="text-muted">{{ $demande->approved_at?->format('d/m/Y H:i') ?? '—' }}</small>
                                <div class="small">Par {{ $demande->approuveur->name ?? '—' }}</div>
                            </div>
                        </li>
                        @elseif($demande->isRejected())
                        <li class="d-flex gap-3 mb-3">
                            <div class="text-danger mt-1"><i class="fas fa-times-circle fs-6"></i></div>
                            <div>
                                <div class="fw-semibold text-danger">Refusée</div>
                                <small class="text-muted">{{ $demande->rejected_at?->format('d/m/Y H:i') ?? '—' }}</small>
                                <div class="small">Par {{ $demande->approuveur->name ?? '—' }}</div>
                            </div>
                        </li>
                        @else
                        <li class="d-flex gap-3 mb-3 opacity-50">
                            <div class="text-muted mt-1"><i class="fas fa-circle fs-6"></i></div>
                            <div>
                                <div class="fw-semibold">En attente de validation</div>
                                <small class="text-muted">Comptabilité</small>
                            </div>
                        </li>
                        @endif
                        {{-- Exécuté --}}
                        @if($demande->isExecuted())
                        <li class="d-flex gap-3 mb-0">
                            <div class="text-success mt-1"><i class="fas fa-check-double fs-6"></i></div>
                            <div>
                                <div class="fw-semibold text-success">Exécuté</div>
                                <small class="text-muted">{{ $demande->executed_at?->format('d/m/Y H:i') ?? '—' }}</small>
                            </div>
                        </li>
                        @else
                        <li class="d-flex gap-3 mb-0 opacity-50">
                            <div class="text-muted mt-1"><i class="fas fa-play-circle fs-6"></i></div>
                            <div>
                                <div class="fw-semibold">Exécution approvisionnement</div>
                                <small class="text-muted">Par le comptable</small>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
