@extends('layouts.app')

@section('title', 'Historique des Validations - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history me-2"></i>Historique des Validations
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('validations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list me-1"></i>Tableau de bord
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
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $operations->where('statut_courant', 'pending_validation')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approuvées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $operations->where('statut_courant', 'terminee')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejetées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $operations->where('statut_courant', 'rejetee')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $operations->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des validations -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-stream me-2"></i>Liste des Validations
            </h6>
        </div>
        <div class="card-body">
            @forelse($operations as $operation)
            <div class="border-bottom pb-3 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-1">
                            <a href="#" class="text-primary">{{ $operation->titre ?? 'Sans titre' }}</a>
                        </h6>
                        <p class="mb-1 text-muted">{{ $operation->description ?? 'Aucune description' }}</p>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>{{ $operation->initiateur->name ?? 'Non assigné' }} |
                            <i class="fas fa-calendar me-1"></i>{{ $operation->created_at->format('d/m/Y H:i') }} |
                            <i class="fas fa-dollar-sign me-1"></i>{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-{{ $operation->statut_courant == 'terminee' ? 'success' : ($operation->statut_courant == 'rejetee' ? 'danger' : 'warning') }}">
                            {{ ucfirst(str_replace('_', ' ', $operation->statut_courant ?? 'En attente')) }}
                        </span>
                        <div class="mt-2">
                            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Voir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Aucune validation trouvée</h5>
                <p class="text-gray-500">Aucune validation ne correspond aux critères de recherche</p>
            </div>
            @endforelse

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $operations->firstItem() }} à {{ $operations->lastItem() }}
                    sur {{ $operations->total() }} validations
                </div>
                {{ $operations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
