@extends('layouts.app')

@section('title', 'Détails Opération Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails de l'Opération Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
            <a href="{{ route('tresorerie.banque.edit', $operation->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Générales</h6>
                    <span class="badge bg-{{ $operation->type_mouvement == 'entree' ? 'success' : 'danger' }}">
                        {{ $operation->type_mouvement == 'entree' ? 'CRÉDIT' : 'DÉBIT' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Référence :</div>
                        <div class="col-sm-8">{{ $operation->reference }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Date de l'opération :</div>
                        <div class="col-sm-8">{{ \Carbon\Carbon::parse($operation->date_mouvement)->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Banque / Caisse :</div>
                        <div class="col-sm-8">{{ $operation->caisse->nom ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Libellé :</div>
                        <div class="col-sm-8 text-primary fw-bold">{{ $operation->libelle }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Montant :</div>
                        <div class="col-sm-8">
                            <h4 class="{{ $operation->type_mouvement == 'entree' ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ number_format($operation->montant, 0, ',', ' ') }} FCFA
                            </h4>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Statut :</div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ ($operation->statut ?? 'validé') == 'validé' ? 'success' : 'warning' }}">
                                {{ ucfirst($operation->statut ?? 'validé') }}
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="fw-bold mb-2">Description / Notes :</div>
                            <div class="p-3 bg-light border rounded">
                                {{ $operation->description ?: 'Aucune description fournie.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Traçabilité</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Créé par</div>
                        <div class="h6 mb-0">{{ $operation->createur->name ?? 'Système' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Date de création</div>
                        <div class="h6 mb-0">{{ $operation->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($operation->updated_at != $operation->created_at)
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Dernière modification</div>
                        <div class="h6 mb-0">{{ $operation->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4 bg-light border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Impact Solde Caisse</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $operation->type_mouvement == 'entree' ? '+' : '-' }} {{ number_format($operation->montant, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
