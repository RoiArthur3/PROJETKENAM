@extends('layouts.app')

@section('title', 'Détails Opération Validée - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-check-circle text-success me-2"></i>
            Détails Opération Validée
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('operations.validated') }}" class="btn btn-outline-success">
                <i class="fas fa-list me-2"></i>Retour aux Validées
            </a>
            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-eye me-2"></i>Voir Opération
            </a>
            <a href="{{ route('operations.execution', $operation->id) }}" class="btn btn-outline-success">
                <i class="fas fa-play me-2"></i>Exécuter
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card">
        <div class="card-header bg-success text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ $operation->titre }}
                    </h5>
                    <div class="text-white-50">
                        <small>
                            Référence: {{ $operation->reference ?? 'N/A' }} | 
                            Créée le: {{ $operation->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-white text-success fs-6">
                        <i class="fas fa-check me-1"></i>
                        {{ $operation->statut_courant == 'approuvee' ? 'Approuvée' : 'Terminée' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Informations générales -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informations Générales</h6>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-sm-4">
                                <label class="form-label">Type d'opération</label>
                                <div class="form-control bg-light">
                                    {{ $operation->typeOperation->libelle ?? 'Standard' }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Montant</label>
                                <div class="form-control bg-light">
                                    {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Priorité</label>
                                <div class="form-control bg-light">
                                    <span class="badge bg-{{ getPriorityColor($operation->priorite ?? 'moyenne') }}">
                                        {{ ucfirst($operation->priorite ?? 'moyenne') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <div class="form-control bg-light" style="min-height: 100px; white-space: pre-wrap; word-wrap: break-word; line-height: 1.6;">
                            {{ $operation->description ?? 'Aucune description' }}
                        </div>
                    </div>
                </div>

                <!-- Demandeur -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Demandeur</h6>
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle bg-primary text-white me-3" style="width: 50px; height: 50px;">
                                {{ strtoupper(substr($operation->demandeur_name ?? 'ND', 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $operation->demandeur_name ?? 'Non assigné' }}</div>
                                <div class="text-muted">{{ $operation->demandeur_email ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de Création</label>
                        <div class="form-control bg-light">
                            {{ $operation->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de Validation</label>
                        <div class="form-control bg-light">
                            {{ $operation->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique de validation -->
            <div class="col-12">
                <h6 class="text-muted mb-3">Historique de Validation</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Étape</th>
                                <th>Service</th>
                                <th>Validateur</th>
                                <th>Date</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validationHistory as $index => $validation)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">V{{ $validation->ordre_validation }}</span>
                                    </td>
                                    <td>{{ $validation->serviceOperationnel->nom ?? 'N/A' }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $validation->validateur_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $validation->validateur_email ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $validation->date_validation ? $validation->date_validation->format('d/m/Y H:i') : 'N/A' }}</div>
                                        <small class="text-muted">{{ $validation->date_validation ? $validation->date_validation->format('H:i') : '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $validation->statut === 'APPROUVE' ? 'success' : 'danger' }}">
                                            {{ $validation->statut === 'APPROUVE' ? 'Approuvé' : 'Rejeté' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
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

.table-sm {
    font-size: 0.875rem;
}

.table-sm th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    padding: 0.5rem;
}

.table-sm td {
    padding: 0.5rem;
    vertical-align: middle;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
    background-color: #f8f9fa;
    color: #495057;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    text-transform: uppercase;
}

.bg-success {
    background-color: #28a745;
    color: white;
}

.bg-primary {
    background-color: #007bff;
    color: white;
}

.bg-danger {
    background-color: #dc3545;
    color: white;
}
</style>
@endsection
