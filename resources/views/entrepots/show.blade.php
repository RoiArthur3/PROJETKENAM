@extends('layouts.app')

@section('title', 'Détails Entrepôt - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-warehouse me-2"></i>Détails de l'Entrepôt
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('stock.entrepots') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('stock.entrepots.edit', $entrepot) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">
                        <i class="fas fa-info-circle me-2"></i>Informations générales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Code :</strong> {{ $entrepot->code }}</p>
                            <p><strong>Nom :</strong> {{ $entrepot->nom }}</p>
                            <p><strong>Adresse :</strong> {{ $entrepot->adresse ?? 'Non spécifiée' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Responsable :</strong> {{ $entrepot->responsable ?? 'Non spécifié' }}</p>
                            <p><strong>Téléphone :</strong> {{ $entrepot->telephone ?? 'Non spécifié' }}</p>
                            <p><strong>Capacité :</strong> {{ $entrepot->capacite ?? 'Non spécifiée' }}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge {{ $entrepot->actif ? 'bg-success' : 'bg-danger' }}">
                            {{ $entrepot->actif ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-primary">{{ $entrepot->stocks_count ?? 0 }}</h2>
                        <p class="text-muted">Produits en stock</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
