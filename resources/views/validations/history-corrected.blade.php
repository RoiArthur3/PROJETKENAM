@extends('layouts.app')

@section('title', 'Historique des Validations - KENAM SERVICES')

@php
    // Définir une collection vide si $validations n'est pas défini
    $validations = $validations ?? collect();
    $operations = $operations ?? collect();
    $operationalServices = $operationalServices ?? collect();
    $errors = $errors ?? new \Illuminate\Support\MessageBag();
@endphp

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Historique des Validations</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('validations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Toutes les Validations
            </a>
            <a href="{{ route('validations.pending') }}" class="btn btn-outline-warning">
                <i class="fas fa-clock me-2"></i>En Attente
            </a>
            <a href="{{ route('validations.approved') }}" class="btn btn-outline-success">
                <i class="fas fa-check me-2"></i>Approuvées
            </a>
            <a href="{{ route('validations.rejected') }}" class="btn btn-outline-danger">
                <i class="fas fa-times me-2"></i>Rejetées
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="approuvee" {{ request('statut') == 'approuvee' ? 'selected' : '' }}>Approuvée</option>
                        <option value="rejetee" {{ request('statut') == 'rejetee' ? 'selected' : '' }}>Rejetée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="standard" {{ request('type') == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="urgent" {{ request('type') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="exceptionnelle" {{ request('type') == 'exceptionnelle' ? 'selected' : '' }}>Exceptionnelle</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select name="periode" class="form-select">
                        <option value="">Toutes périodes</option>
                        <option value="7" {{ request('periode') == '7' ? 'selected' : '' }}>7 derniers jours</option>
                        <option value="30" {{ request('periode') == '30' ? 'selected' : '' }}>30 derniers jours</option>
                        <option value="90" {{ request('periode') == '90' ? 'selected' : '' }}>90 derniers jours</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="Titre ou référence..." value="{{ request('search') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('validations.history') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques des Validations</h5>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-warning">
                                        @php
                                            try {
                                                echo $validations->where('statut_courant', 'en_attente')->count();
                                            } catch (\Exception $e) {
                                                echo '0';
                                            }
                                        @endphp
                                    </h3>
                                    <p class="mb-0">En Attente</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-success">
                                        @php
                                            try {
                                                echo $validations->where('statut_courant', 'approuvee')->count();
                                            } catch (\Exception $e) {
                                                echo '0';
                                            }
                                        @endphp
                                    </h3>
                                    <p class="mb-0">Approuvées</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-danger">
                                        @php
                                            try {
                                                echo $validations->where('statut_courant', 'rejetee')->count();
                                            } catch (\Exception $e) {
                                                echo '0';
                                            }
                                        @endphp
                                    </h3>
                                    <p class="mb-0">Rejetées</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $validations->count() }}</h3>
                                    <p class="mb-0">Total</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Chronologie des Validations</h5>
            <div class="timeline">
                @forelse($validations as $validation)
                <div class="timeline-item">
                    <div class="timeline-marker bg-{{ getOperationStatusColor($validation->statut_courant) }}">
                        <i class="fas fa-{{ getOperationStatusIcon($validation->statut_courant) }}"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $validation->titre ?? 'Validation sans titre' }}</h6>
                                        <small class="text-muted">
                                            Type: {{ $validation->type ?? 'Standard' }} |
                                            Réf: {{ $validation->reference ?? 'N/A' }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ getOperationStatusColor($validation->statut_courant) }}">
                                        {{ ucfirst($validation->statut_courant ?? 'En attente') }}
                                    </span>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Initiateur:</strong></p>
                                        @if($validation->demandeur_name)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-secondary text-white me-2" style="width: 25px; height: 25px; font-size: 10px;">
                                                    {{ strtoupper(substr($validation->demandeur_name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $validation->demandeur_name }}</div>
                                                    <small class="text-muted">{{ $validation->demandeur_email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Service:</strong></p>
                                        @if($validation->service)
                                            <span class="badge bg-info">{{ $validation->service->nom ?? 'N/A' }}</span>
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <small class="text-muted">Création: {{ $validation->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Modification: {{ $validation->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="col-md-4">
                                        @php
                                            $processingTime = $validation->created_at->diffInDays($validation->updated_at);
                                        @endphp
                                        <small class="text-muted">Temps: {{ $processingTime }} jour(s)</small>
                                    </div>
                                </div>

                                @if($validation->description)
                                <div class="mt-3">
                                    <p class="mb-0"><strong>Description:</strong></p>
                                    <p class="text-muted">{{ $validation->description }}</p>
                                </div>
                                @endif

                                @if($validation->statut_courant == 'rejetee' && $validation->raison)
                                <div class="mt-3">
                                    <p class="mb-0"><strong>Raison du rejet:</strong></p>
                                    <div class="alert alert-danger mt-2">
                                        {{ $validation->raison }}
                                    </div>
                                </div>
                                @endif

                                <div class="mt-3">
                                    <a href="{{ route('validations.show', $validation->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-2"></i>Voir Détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune validation trouvée</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    @if($validations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        Affichage de {{ $validations->firstItem() }} à {{ $validations->lastItem() }}
                        sur {{ $validations->total() }} validations
                    @else
                        {{ $validations->count() }} validation(s) trouvée(s)
                    @endif
                </div>
                @if($validations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $validations->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

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

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    margin-left: 30px;
}
</style>

<?php
function getStatusColor($status) {
    switch($status) {
        case 'en_attente': return 'warning';
        case 'approuvee': return 'success';
        case 'rejetee': return 'danger';
        default: return 'secondary';
    }
}

function getStatusIcon($statut) {
    switch($statut) {
        case 'en_attente': return 'clock';
        case 'en_cours': return 'spinner';
        case 'approuvee': return 'check';
        case 'rejetee': return 'times';
        default: return 'question';
    }
}

function getOperationStatusColor($statut) {
    switch($statut) {
        case 'en_attente': return 'warning';
        case 'pending_validation': return 'warning';
        case 'en_cours': return 'info';
        case 'approuvee': return 'success';
        case 'rejetee': return 'danger';
        case 'termine': return 'success';
        default: return 'secondary';
    }
}

function getOperationStatusIcon($statut) {
    switch($statut) {
        case 'en_attente': return 'clock';
        case 'pending_validation': return 'clock';
        case 'en_cours': return 'spinner';
        case 'approuvee': return 'check';
        case 'rejetee': return 'times';
        case 'termine': return 'check';
        default: return 'question';
    }
}
?>

@endsection
