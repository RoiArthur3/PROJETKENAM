@extends('layouts.app')

@section('title', 'Tableau de Bord des Validations - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt me-2"></i>Tableau de Bord des Validations
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('validations.history') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-history me-1"></i>Historique
            </a>
            <a href="{{ route('validations.pending') }}" class="btn btn-outline-warning btn-sm">
                <i class="fas fa-clock me-1"></i>En Attente
            </a>
            <a href="{{ route('validations.approved') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-check me-1"></i>Approuvées
            </a>
            <a href="{{ route('validations.rejected') }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-times me-1"></i>Rejetées
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_attente'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approuvées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approuvees'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejetées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejetees'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opérations récentes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Opérations Récentes
            </h6>
        </div>
        <div class="card-body">
            @forelse($recent_operations as $operation)
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">{{ $operation->titre ?? 'Sans titre' }}</h6>
                        <small class="text-muted">
                            {{ $operation->created_at->format('d/m/Y H:i') }} |
                            {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                        </small>
                    </div>
                    <span class="badge bg-{{ $operation->statut_courant == 'terminee' ? 'success' : ($operation->statut_courant == 'rejetee' ? 'danger' : 'warning') }}">
                        {{ ucfirst(str_replace('_', ' ', $operation->statut_courant ?? 'En attente')) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-3">
                <p class="text-muted">Aucune opération récente</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
