@extends('layouts.app')

@section('title', 'Opérations Validées - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-check-circle text-success me-2"></i>
            Opérations Validées
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Toutes les Opérations
            </a>
            <a href="{{ route('valider.index') }}" class="btn btn-outline-warning">
                <i class="fas fa-clock me-2"></i>En Attente
            </a>
            <a href="{{ route('validations.approved') }}" class="btn btn-outline-success">
                <i class="fas fa-check me-2"></i>Validations
            </a>
            <a href="{{ route('validations.rejected') }}" class="btn btn-outline-danger">
                <i class="fas fa-times me-2"></i>Rejetées
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Validées</h6>
                            <h3 class="mb-0">{{ $validatedOperations->total() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Ce Mois</h6>
                            <h3 class="mb-0">{{ $validatedOperations->where('updated_at', '>=', now()->startOfMonth())->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">En Cours</h6>
                            <h3 class="mb-0">{{ \App\Models\Operation::where('statut_courant', 'en_cours')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-spinner"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des opérations validées -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-success">
                <i class="fas fa-list me-2"></i>Liste des Opérations Validées
            </h6>
        </div>
        <div class="card-body">
            @if($validatedOperations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Titre</th>
                            <th>Type d'opération</th>
                            <th>Demandeur</th>
                            <th>Montant</th>
                            <th>Date Validation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($validatedOperations as $operation)
                        <tr>
                            <td>
                                <span class="badge bg-success text-white">
                                    #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <h6 class="mb-1">{{ $operation->titre }}</h6>
                                    <small class="text-muted">{{ $operation->reference ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $operation->typeOperation->libelle ?? 'Standard' }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold">{{ $operation->demandeur_name ?? 'Non assigné' }}</div>
                                    <small class="text-muted">{{ $operation->demandeur_email ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success">
                                    {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $operation->updated_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $operation->updated_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('operations.execution', $operation->id) }}" class="btn btn-outline-success" title="Exécuter">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $validatedOperations->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune opération validée</h5>
                <p class="text-muted">Vous n'avez aucune opération validée à afficher.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Styles pour les avatars -->
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.table {
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.table th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    color: #495057;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #f1f3f4;
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.08);
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn-group .btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}
</style>
@endsection
