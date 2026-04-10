@extends('layouts.app')

@section('title', 'Détails du Décaissement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Succès !</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreur !</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4">Décaissement : {{ $decaissement->reference }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.decaissements.imprimer', $decaissement->id) }}" target="_blank" class="btn btn-success">
                <i class="fas fa-print me-2"></i>Imprimer le Reçu
            </a>
            <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Libellé / Objet :</div>
                        <div class="col-sm-8">{{ $decaissement->libelle }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Montant :</div>
                        <div class="col-sm-8 h5 text-success">{{ number_format($decaissement->montant, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Bénéficiaire :</div>
                        <div class="col-sm-8">{{ $decaissement->beneficiaire ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Caisse :</div>
                        <div class="col-sm-8">{{ $decaissement->caisse->nom ?? 'N/A' }}</div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Date :</div>
                        <div class="col-sm-8">{{ \Carbon\Carbon::parse($decaissement->date_depense)->format('d/m/Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Statut :</div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ $decaissement->statut == 'validé' ? 'success' : 'warning' }}">
                                {{ strtoupper($decaissement->statut) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if($decaissement->operation)
            <div class="card shadow-sm border-primary mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="card-title mb-0">Opération Liée</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Réf:</strong> {{ $decaissement->operation->numero_ordre ?? '#OP-'.$decaissement->operation->id }}</p>
                    <p class="mb-1"><strong>Titre:</strong> {{ $decaissement->operation->titre }}</p>
                    <p class="mb-3"><strong>Demandeur:</strong> {{ $decaissement->operation->demandeur_name }}</p>
                    <a href="{{ route('operations.tracking', $decaissement->operation->id) }}" class="btn btn-sm btn-outline-primary w-100">
                        Voir le suivi complet
                    </a>
                </div>
            </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0 text-muted">Audit</h6>
                </div>
                <div class="card-body small text-muted">
                    <p class="mb-1">Enregistré par : {{ $decaissement->createur->name ?? 'Système' }}</p>
                    <p class="mb-1">Le : {{ $decaissement->created_at->format('d/m/Y à H:i') }}</p>
                    @if($decaissement->notes)
                    <hr>
                    <p class="mb-0 fw-bold">Notes :</p>
                    <p class="mb-0 text-dark italic">{!! nl2br(e($decaissement->notes)) !!}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
