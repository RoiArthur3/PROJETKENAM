@extends('layouts.app')

@section('title', 'Validations - KENAM SERVICES')

@include('validations._helpers')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-check-circle me-2 text-primary"></i>Validations
        </h1>
    </div>

    <!-- Navigation -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('validations.pending') }}" class="btn btn-outline-warning w-100 mb-2">
                        <i class="fas fa-clock me-2"></i>En Attente
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('validations.approved') }}" class="btn btn-outline-success w-100 mb-2">
                        <i class="fas fa-check me-2"></i>Approuvées
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('validations.rejected') }}" class="btn btn-outline-danger w-100 mb-2">
                        <i class="fas fa-times me-2"></i>Rejetées
                    </a>
                </div>
                <div class="col-md-3">
                    <!-- Lien Historique temporairement désactivé -->
                    <button class="btn btn-outline-secondary w-100 mb-2" disabled>
                        <i class="fas fa-history me-2"></i>Historique (Désactivé)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Operation::where('statut_courant', 'pending_validation')->count() }}
                            </div>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x"></i>
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
                                {{ \App\Models\Operation::where('statut_courant', 'approuvee')->count() }}
                            </div>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check fa-2x"></i>
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
                                {{ \App\Models\Operation::where('statut_courant', 'rejetee')->count() }}
                            </div>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times fa-2x"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Operation::count() }}
                            </div>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des validations récentes -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Validations Récentes
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Demandeur</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Échéance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $recentValidations = \App\Models\Operation::latest()->take(10)->get();
                        @endphp
                        @foreach($recentValidations as $validation)
                        <tr>
                            <td>
                                <span class="badge bg-primary">#{{ str_pad($validation->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $validation->titre }}</strong>
                                    @if($validation->description)
                                    <br><small class="text-muted">{{ Str::limit($validation->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                        {{ substr($validation->demandeur_name ?? 'ND', 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $validation->demandeur_name ?? 'Non spécifié' }}</div>
                                        <small class="text-muted">{{ $validation->demandeur_email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ getPriorityColor($validation->priorite ?? 'moyenne') }}">
                                    {{ ucfirst($validation->priorite ?? 'moyenne') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ getOperationStatusColor($validation->statut_courant) }}">
                                    <i class="fas fa-{{ getOperationStatusIcon($validation->statut_courant) }} me-1"></i>
                                    {{ getOperationStatusText($validation->statut_courant) }}
                                </span>
                            </td>
                            <td>
                                @if($validation->echeance)
                                    <span class="{{ $validation->echeance->isPast() ? 'text-danger' : 'text-muted' }}">
                                        {{ $validation->echeance->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Non définie</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('operations.show', $validation) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($validation->statut_courant === 'pending_validation')
                                    <a href="{{ route('operations.validate', $validation->id) }}" class="btn btn-sm btn-outline-success" title="Valider">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
