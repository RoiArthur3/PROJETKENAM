@extends('layouts.app')

@section('title', 'Historique de Validation - Opération #' . $operation->id . ' - KENAM SERVICES')

@php
    // Récupérer toutes les étapes de validation avec leurs statuts
    $validationSteps = $validationSteps->sortBy('ordre_validation');
    $totalSteps = $validationSteps->count();
    $currentStep = $validationSteps->where('statut', 'EN_COURS')->first()->ordre_validation ?? null;
    $completedSteps = $validationSteps->where('statut', 'APPROUVE');
    $pendingSteps = $validationSteps->where('statut', 'EN_ATTENTE');
    $rejectedSteps = $validationSteps->where('statut', 'REJETE');

    // Déterminer si la validation est terminée
    $isValidationComplete = ($completedSteps->count() === $totalSteps) || ($rejectedSteps->count() > 0);
    $validationStatus = $isValidationComplete ?
        ($rejectedSteps->count() > 0 ? 'REJETEE' : 'APPROUVEE') :
        'EN_COURS';
@endphp

@section('content')
<div class="container-fluid">
    <!-- En-tête intelligent -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history me-2 text-primary"></i>
            Historique de Validation - Opération #{{ $operation->id }}
        </h1>
        <div class="d-flex gap-2">
            <span class="badge bg-{{ getValidationStatusColor($validationStatus) }} text-white fs-6">
                @if($validationStatus === 'APPROUVEE')
                    <i class="fas fa-check-circle me-1"></i>Approuvée
                @elseif($validationStatus === 'REJETEE')
                    <i class="fas fa-times-circle me-1"></i>Rejetée
                @else
                    <i class="fas fa-clock me-1"></i>En cours
                @endif
            </span>
            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>Voir détails
            </a>
        </div>
    </div>

    <!-- Carte de synthèse -->
    <div class="card shadow mb-4 border-{{ getValidationStatusColor($validationStatus) }}">
        <div class="card-header bg-{{ getValidationStatusColor($validationStatus) }} text-white py-3">
            <h6 class="m-0 fw-bold">
                <i class="fas fa-chart-line me-2"></i>
                Synthèse de Validation
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <div class="counter-box">
                        <div class="counter-number text-primary">{{ $totalSteps }}</div>
                        <div class="counter-label">Total Étapes</div>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="counter-box">
                        <div class="counter-number text-success">{{ $completedSteps->count() }}</div>
                        <div class="counter-label">Complétées</div>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="counter-box">
                        <div class="counter-number text-warning">{{ $pendingSteps->count() }}</div>
                        <div class="counter-label">En Attente</div>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="counter-box">
                        <div class="counter-number text-danger">{{ $rejectedSteps->count() }}</div>
                        <div class="counter-label">Rejetées</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline de validation -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-timeline me-2"></i>
                Timeline de Validation
            </h6>
        </div>
        <div class="card-body">
            <div class="validation-timeline">
                @foreach($validationSteps as $index => $step)
                    @php
                        $stepNumber = $index + 1;
                        $isCompleted = ($step->statut === 'APPROUVE');
                        $isCurrent = ($stepNumber == $currentStep);
                        $isPending = ($step->statut === 'EN_ATTENTE');
                        $isRejected = ($step->statut === 'REJETE');

                        // Déterminer le rôle et l'icône selon le niveau
                        if ($stepNumber == 1) {
                            $validatorRole = '1er Validateur';
                            $validatorIcon = 'fa-user-check';
                            $validatorColor = 'primary';
                        } elseif ($stepNumber == 2) {
                            $validatorRole = '2e Validateur';
                            $validatorIcon = 'fa-user-shield';
                            $validatorColor = 'info';
                        } elseif ($stepNumber == 3) {
                            $validatorRole = 'Validateur Final';
                            $validatorIcon = 'fa-user-crown';
                            $validatorColor = 'success';
                        } else {
                            $validatorRole = 'Validateur ' . $stepNumber;
                            $validatorIcon = 'fa-user';
                            $validatorColor = 'secondary';
                        }
                    @endphp

                    <div class="timeline-item">
                        <div class="timeline-marker">
                            @if($isCompleted)
                                <div class="marker-icon bg-success">
                                    <i class="fas fa-check"></i>
                                </div>
                            @elseif($isCurrent)
                                <div class="marker-icon bg-{{ $validatorColor }} pulse">
                                    <i class="fas {{ $validatorIcon }}"></i>
                                </div>
                            @elseif($isRejected)
                                <div class="marker-icon bg-danger">
                                    <i class="fas fa-times"></i>
                                </div>
                            @else
                                <div class="marker-icon bg-secondary">
                                    <i class="fas fa-clock"></i>
                                </div>
                            @endif
                        </div>

                        <div class="timeline-content">
                            <div class="timeline-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge bg-{{ $validatorColor }} text-white me-2">
                                                {{ $validatorRole }}
                                            </span>
                                            Étape {{ $stepNumber }}
                                        </h6>
                                        <div class="text-muted small">
                                            Service: {{ $step->service_name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ getStepStatusColor($step->statut) }} text-white">
                                            {{ getStepStatusText($step->statut) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-body">
                                @if($step->date_validation)
                                    <div class="mb-2">
                                        <strong>Date de validation:</strong>
                                        {{ \Carbon\Carbon::parse($step->date_validation)->format('d/m/Y H:i') }}
                                    </div>
                                @endif

                                @if($step->validateur_id)
                                    @php
                                        $validatorName = $step->validateur_name ?? 'Validateur';
                                    @endphp
                                    <div class="mb-2">
                                        <strong>Validé par:</strong>
                                        <span class="badge bg-light text-dark">{{ $validatorName }}</span>
                                    </div>
                                @endif

                                @if($step->commentaire)
                                    <div class="mb-2">
                                        <strong>Commentaire:</strong>
                                        <div class="comment-box p-2 bg-light rounded">
                                            {{ $step->commentaire }}
                                        </div>
                                    </div>
                                @endif

                                @if($isCurrent)
                                    <div class="alert alert-{{ $validatorColor }} border-{{ $validatorColor }} mt-3">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-info-circle me-2"></i>
                                            En cours de validation
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Cette étape est actuellement en cours de validation.</strong>
                                            @if($stepNumber == $totalSteps)
                                                Il s'agit de la décision finale pour cette opération.
                                            @else
                                                Après validation, l'étape {{ $stepNumber + 1 }} sera activée automatiquement.
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Carte d'informations sur l'opération -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-info-circle me-2"></i>
                Détails de l'Opération
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Informations générales</h6>
                    <div class="info-list">
                        <div class="info-item">
                            <strong>Référence:</strong> #{{ $operation->id }}
                        </div>
                        <div class="info-item">
                            <strong>Titre:</strong> {{ $operation->titre }}
                        </div>
                        <div class="info-item">
                            <strong>Priorité:</strong>
                            <span class="badge bg-{{ getPriorityColor($operation->priorite) }}">
                                {{ ucfirst($operation->priorite) }}
                            </span>
                        </div>
                        <div class="info-item">
                            <strong>Type:</strong> {{ $operation->typeOperation->libelle ?? 'N/A' }}
                        </div>
                        <div class="info-item">
                            <strong>Échéance:</strong>
                            {{ $operation->echeance ? \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') : 'N/A' }}
                        </div>
                        <div class="info-item">
                            <strong>Montant:</strong>
                            {{ $operation->montant ? number_format($operation->montant, 0, ',', ' ') . ' FCFA' : 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Demandeur et suivi</h6>
                    <div class="info-list">
                        <div class="info-item">
                            <strong>Demandeur:</strong> {{ $operation->demandeur_name }}
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong> {{ $operation->demandeur_email }}
                        </div>
                        <div class="info-item">
                            <strong>Date de création:</strong>
                            {{ \Carbon\Carbon::parse($operation->created_at)->format('d/m/Y H:i') }}
                        </div>
                        <div class="info-item">
                            <strong>Statut actuel:</strong>
                            <span class="badge bg-{{ getOpStatusColor($operation->statut_courant) }} text-white">
                                {{ strtoupper($operation->statut_courant ?? 'N/A') }}
                            </span>
                        </div>
                        <div class="info-item">
                            <strong>Dernière mise à jour:</strong>
                            {{ \Carbon\Carbon::parse($operation->updated_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-bolt me-2"></i>
                Actions Rapides
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-eye me-2"></i>
                        Voir Détails
                    </a>
                </div>
                @if($operation->statut_courant === 'approuvee')
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('operations.execution', $operation->id) }}" class="btn btn-success w-100">
                            <i class="fas fa-play me-2"></i>
                            Exécuter
                        </a>
                    </div>
                @endif
                <div class="col-md-3 mb-3">
                    <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-list me-2"></i>
                        Liste Opérations
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <button onclick="window.print()" class="btn btn-outline-info w-100">
                        <i class="fas fa-print me-2"></i>
                        Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Compteurs */
.counter-box {
    padding: 20px;
    border-radius: 8px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    transition: transform 0.2s ease;
}

.counter-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.counter-number {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
}

.counter-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 5px;
}

/* Timeline */
.validation-timeline {
    position: relative;
    padding-left: 30px;
}

.validation-timeline::before {
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
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.marker-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}

.timeline-content {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left: 4px solid #e9ecef;
}

.timeline-header {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.timeline-body {
    line-height: 1.6;
}

.comment-box {
    background: #f8f9fa;
    border-left: 3px solid #007bff;
    font-style: italic;
}

/* Informations */
.info-list {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}

.info-item {
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .counter-box {
        margin-bottom: 15px;
    }

    .timeline-content {
        padding: 15px;
    }

    .info-list {
        padding: 10px;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .timeline-item {
        break-inside: avoid;
    }

    .card {
        break-inside: avoid;
    }
}
</style>

@php
function getValidationStatusColor($status) {
    switch($status) {
        case 'APPROUVEE': return 'success';
        case 'REJETEE': return 'danger';
        case 'EN_COURS': return 'primary';
        default: return 'secondary';
    }
}

function getStepStatusColor($status) {
    switch($status) {
        case 'APPROUVE': return 'success';
        case 'REJETE': return 'danger';
        case 'EN_COURS': return 'primary';
        case 'EN_ATTENTE': return 'secondary';
        default: return 'light';
    }
}

function getStepStatusText($status) {
    switch($status) {
        case 'APPROUVE': return 'APPROUVÉE';
        case 'REJETE': return 'REJETÉE';
        case 'EN_COURS': return 'EN COURS';
        case 'EN_ATTENTE': return 'EN ATTENTE';
        default: return $status;
    }
}


function getOpStatusColor($status) {
    switch($status) {
        case 'approuvee': return 'success';
        case 'rejetee': return 'danger';
        case 'en_validation': return 'primary';
        case 'pending_validation': return 'warning';
        default: return 'secondary';
    }
}
@endphp
@endsection
