@extends('layouts.app')
@include('validations._helpers')

@section('title', 'Validations en Attente - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clock me-2 text-warning"></i>Mes Validations en Attente
        </h1>
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
                                {{ $validations->where('statut_courant', 'pending_validation')->count() }}
                            </div>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des validations -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste de Mes Validations en Attente
            </h6>
        </div>
        <div class="card-body">
            @if(isset($validations) && $validations->count() > 0)
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
                        @foreach($validations as $validation)
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
                                <span class="badge bg-{{ getOpPriorityColor($validation->priorite ?? 'moyenne') }}">
                                    {{ ucfirst($validation->priorite ?? 'moyenne') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ getOpStatusColor($validation->statut_courant) }}">
                                    <i class="fas fa-{{ getOpStatusIcon($validation->statut_courant) }} me-1"></i>
                                    {{ getOpStatusText($validation->statut_courant) }}
                                </span>
                            </td>
                            <td>
                                @if(isset($validation->echeance) && $validation->echeance instanceof \Carbon\Carbon)
                                    <span class="{{ $validation->echeance->isPast() ? 'text-danger' : 'text-muted' }}">
                                        {{ $validation->echeance->format('d/m/Y') }}
                                    </span>
                                @elseif(isset($validation->echeance) && is_string($validation->echeance))
                                    <span class="text-muted">
                                        {{ date('d/m/Y', strtotime($validation->echeance)) }}
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
                                    <form action="{{ route('operations.validate', $validation->id) }}" method="GET" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $validations->links() }}
            </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h5 class="text-muted">Aucune validation en attente</h5>
            <p class="text-muted">Vous n'avez aucune opération en attente de validation.</p>
        </div>
    @endif
        </div>
    </div>
</div>

@endsection
