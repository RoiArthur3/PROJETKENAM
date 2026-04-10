@extends('layouts.app')

@section('title', 'Validation d\'Opération - KENAM SERVICES')

@php
    // Fallbacks pour les helpers au cas où ils ne seraient pas chargés (ex: prod sans dump-autoload)
    if (!function_exists('getFileIconColor')) {
        function getFileIconColor($type) {
            $type = strtolower($type ?? '');
            if (str_contains($type, 'pdf')) return 'danger';
            if (str_contains($type, 'word') || str_contains($type, 'doc')) return 'primary';
            if (str_contains($type, 'excel') || str_contains($type, 'csv')) return 'success';
            if (str_contains($type, 'image') || str_contains($type, 'jpg') || str_contains($type, 'png')) return 'info';
            return 'secondary';
        }
    }

    if (!function_exists('formatFileSize')) {
        function formatFileSize($bytes) {
            if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' Mo';
            if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' ko';
            return $bytes . ' octets';
        }
    }

    // Vérifier que $validationSteps n'est pas null
    if (!$validationSteps || $validationSteps->isEmpty()) {
        $validationSteps = collect();
    }

    // Déterminer l'étape actuelle (si $step n'est pas défini, prendre la première étape en cours)
    $step = $step ?? 1;
    if (!$currentStep) {
        // Si pas d'étape actuelle définie, chercher la première étape en cours
        $currentStep = $validationSteps->where('statut', 'EN_COURS')->first();
        if ($currentStep) {
            $step = $currentStep->ordre_validation;
        } else {
            // Sinon prendre la première étape
            $currentStep = $validationSteps->first();
            $step = $currentStep ? $currentStep->ordre_validation : 1;
        }
    }

    $totalSteps = $validationSteps->count();
    $isLastStep = ($step == $totalSteps);
    $isFirstStep = ($step == 1);

    // Déterminer le rôle du validateur
    $validatorRole = '';
    $validatorDescription = '';
    $validatorIcon = '';
    $validatorColor = '';

    if ($step == 1) {
        $validatorRole = '1er Validateur';
        $validatorDescription = 'Premier niveau de validation - Vous recevez cette requête en premier';
        $validatorIcon = 'fa-user-check';
        $validatorColor = 'primary';
    } elseif ($step == 2) {
        $validatorRole = '2e Validateur';
        $validatorDescription = 'Deuxième niveau de validation - Vous intervenez après validation du 1er niveau';
        $validatorIcon = 'fa-user-shield';
        $validatorColor = 'info';
    } elseif ($step == 3) {
        $validatorRole = 'Validateur Final (Destinataire Principal)';
        $validatorDescription = 'Dernier niveau de validation - Votre décision est finale et prioritaire';
        $validatorIcon = 'fa-user-crown';
        $validatorColor = 'success';
    }

    // Vérifier que $validationSteps n'est pas null
    if (!$validationSteps || $validationSteps->isEmpty()) {
        // Si pas d'étapes de validation, afficher un message d'erreur
        $validationSteps = collect();
    }

    // Calculer le niveau maximum atteint
    $maxStepReached = 0;
    $maxStepDate = null;
    $maxStepValidator = null;

    foreach($validationSteps as $valStep) {
        if ($valStep->statut === 'APPROUVE' && $valStep->ordre_validation > $maxStepReached) {
            $maxStepReached = $valStep->ordre_validation;
            $maxStepDate = $valStep->date_validation;
            $maxStepValidator = $valStep->validateur_name ?? 'Validateur';
        }
    }

    // Calculer le temps total de traitement
    $totalProcessingTime = null;
    $timeColor = 'secondary';
    $timeIcon = 'clock';

    if ($maxStepDate && $operation->created_at) {
        $createdDate = \Carbon\Carbon::parse($operation->created_at);
        $validatedDate = \Carbon\Carbon::parse($maxStepDate);
        $totalProcessingTime = $createdDate->diff($validatedDate);

        // Déterminer la couleur selon la durée
        $totalHours = $totalProcessingTime->days * 24 + $totalProcessingTime->h;
        if ($totalHours > 72) {
            $timeColor = 'danger';
            $timeIcon = 'exclamation-triangle';
        } elseif ($totalHours > 48) {
            $timeColor = 'warning';
            $timeIcon = 'exclamation-circle';
        } elseif ($totalHours > 24) {
            $timeColor = 'info';
            $timeIcon = 'info-circle';
        } else {
            $timeColor = 'success';
            $timeIcon = 'check-circle';
        }
    }
@endphp

@section('content')
<div class="container-fluid">
    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- En-tête intelligent selon le niveau -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas {{ $validatorIcon }} me-2 text-{{ $validatorColor }}"></i>
            Validation d'Opération #{{ $operation->id }}
        </h1>
        <div class="d-flex gap-2">
            <span class="badge bg-{{ $validatorColor }} text-white fs-6">
                {{ $validatorRole }}
            </span>
            <span class="badge bg-secondary text-white fs-6">
                Étape {{ $step }}/{{ $totalSteps }}
            </span>
            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>Voir détails
            </a>
            <a href="{{ route('validations.history') }}" class="btn btn-outline-info">
                <i class="fas fa-history me-2"></i>Historique
            </a>
        </div>
    </div>

    <!-- Carte d'information du validateur -->
    <div class="card shadow mb-4 border-{{ $validatorColor }}">
        <div class="card-header bg-{{ $validatorColor }} text-white py-3">
            <h6 class="m-0 fw-bold">
                <i class="fas {{ $validatorIcon }} me-2"></i>
                {{ $validatorRole }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="text-{{ $validatorColor }} mb-2">{{ $validatorDescription }}</h6>

                    @php
                        // Calculer le niveau maximum atteint
                        $maxStepReached = 0;
                        $maxStepDate = null;
                        $maxStepValidator = null;

                        foreach($validationSteps as $valStep) {
                            if ($valStep->statut === 'APPROUVE' && $valStep->ordre_validation > $maxStepReached) {
                                $maxStepReached = $valStep->ordre_validation;
                                $maxStepDate = $valStep->date_validation;
                                $maxStepValidator = $valStep->validateur_name ?? 'Validateur';
                            }
                        }

                        // Calculer le temps total de traitement
                        $totalProcessingTime = null;
                        $timeColor = 'secondary';
                        $timeIcon = 'clock';

                        if ($maxStepDate && $operation->created_at) {
                            $createdDate = \Carbon\Carbon::parse($operation->created_at);
                            $validatedDate = \Carbon\Carbon::parse($maxStepDate);
                            $totalProcessingTime = $createdDate->diff($validatedDate);

                            // Déterminer la couleur selon la durée
                            $totalHours = $totalProcessingTime->days * 24 + $totalProcessingTime->h;
                            if ($totalHours > 72) {
                                $timeColor = 'danger';
                                $timeIcon = 'exclamation-triangle';
                            } elseif ($totalHours > 48) {
                                $timeColor = 'warning';
                                $timeIcon = 'exclamation-circle';
                            } elseif ($totalHours > 24) {
                                $timeColor = 'info';
                                $timeIcon = 'info-circle';
                            } else {
                                $timeColor = 'success';
                                $timeIcon = 'check-circle';
                            }
                        }
                    @endphp

                    <div class="alert alert-{{ $maxStepReached > 0 ? 'success' : 'info' }} border-{{ $validatorColor }} mb-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-trophy me-2"></i>
                            Niveau de Validation Atteint
                        </h6>
                        @if($maxStepReached > 0)
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Niveau {{ $maxStepReached }} atteint</strong><br>
                                    <small class="text-muted">par {{ $maxStepValidator }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success text-white fs-6">
                                        <i class="fas fa-award me-1"></i>
                                        {{ $maxStepReached }}/{{ $totalSteps }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <p class="mb-0">
                                <strong>Aucun niveau validé pour le moment.</strong><br>
                                <small class="text-muted">En attente de la première validation...</small>
                            </p>
                        @endif
                    </div>

                    <!-- Durée -->
                    @if($totalProcessingTime)
                        <div class="alert alert-{{ $timeColor }} mb-3">
                            <h6 class="alert-heading">
                                <i class="fas fa-{{ $timeIcon }} me-2"></i>
                                Durée
                            </h6>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="time-metric">
                                        <div class="metric-number">{{ $totalProcessingTime->days }}</div>
                                        <div class="metric-label">Jours</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="time-metric">
                                        <div class="metric-number">{{ $totalProcessingTime->h }}</div>
                                        <div class="metric-label">Heures</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="time-metric">
                                        <div class="metric-number">{{ $totalProcessingTime->i }}</div>
                                        <div class="metric-label">Minutes</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                <small class="text-muted">
                                    Du {{ \Carbon\Carbon::parse($operation->created_at)->format('d/m/Y H:i') }}
                                    au {{ \Carbon\Carbon::parse($maxStepDate)->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            @if($totalProcessingTime->days > 0)
                                <div class="mt-2 text-center">
                                    <small class="badge bg-{{ $timeColor }} text-white">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ formatDuration($totalProcessingTime) }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Progression de validation -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Progression de validation</small>
                            <small class="text-muted">{{ $step }}/{{ $totalSteps }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @foreach($validationSteps as $index => $valStep)
                                @php
                                    $stepNumber = $index + 1;
                                    $stepProgress = ($stepNumber <= $step) ? 100 : 0;
                                    $stepStatus = 'bg-' . ($valStep->statut === 'APPROUVE' ? 'success' : ($valStep->statut === 'EN_COURS' ? $validatorColor : 'secondary'));
                                    $stepWidth = (100 / $totalSteps);
                                @endphp
                                <div class="progress-bar {{ $stepStatus }}"
                                     style="width: {{ $stepWidth }}%; opacity: {{ $stepProgress / 100 }};"
                                     title="Étape {{ $stepNumber }}: {{ $valStep->statut }}">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Chaîne de validation -->
                    <div class="alert alert-{{ $isLastStep ? 'success' : 'info' }} border-{{ $validatorColor }}">
                        <h6 class="alert-heading">
                            <i class="fas fa-link me-2"></i>
                            Chaîne de Validation
                        </h6>
                        <div class="validation-chain">
                            @foreach($validationSteps as $index => $valStep)
                                @php
                                    $stepNumber = $index + 1;
                                    $isCompleted = ($valStep->statut === 'APPROUVE');
                                    $isCurrent = ($stepNumber == $step);
                                    $isPending = ($valStep->statut === 'EN_ATTENTE');
                                @endphp
                                <div class="chain-step d-flex align-items-center mb-2">
                                    <div class="step-indicator">
                                        @if($isCompleted)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @elseif($isCurrent)
                                            <i class="fas fa-clock text-{{ $validatorColor }}"></i>
                                        @else
                                            <i class="fas fa-circle text-muted"></i>
                                        @endif
                                    </div>
                                    <div class="step-content flex-grow-1">
                                        <div class="fw-bold">
                                            Étape {{ $stepNumber }}:
                                            @if($stepNumber == 1)
                                                1er Validateur
                                            @elseif($stepNumber == 2)
                                                2e Validateur
                                            @else
                                                Validateur Final
                                            @endif
                                        </div>
                                        <div class="small text-muted">
                                            @if($isCompleted)
                                                ✅ Validé le {{ \Carbon\Carbon::parse($valStep->date_validation)->format('d/m/Y H:i') }}
                                            @elseif($isCurrent)
                                                🔄 En cours de validation
                                            @else
                                                ⏳ En attente
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($isLastStep)
                            <div class="mt-3">
                                <strong><i class="fas fa-star me-2"></i>Validateur Final</strong><br>
                                Votre décision est finale et prioritaire. Après votre validation, l'opération sera considérée comme approuvée.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Informations sur l'opération -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-info-circle me-2"></i>
                                Détails Opération
                            </h6>
                            <div class="small">
                                <div class="mb-2">
                                    <strong>Référence:</strong> #{{ $operation->id }}
                                </div>
                                <div class="mb-2">
                                    <strong>Titre:</strong> {{ $operation->titre }}
                                </div>
                                <div class="mb-2">
                                    <strong>Priorité:</strong>
                                    <span class="badge bg-{{ getPriorityColor($operation->priorite) }}">
                                        {{ ucfirst($operation->priorite) }}
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <strong>Type:</strong> {{ $operation->typeOperation->libelle ?? 'N/A' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Échéance:</strong>
                                    {{ $operation->echeance ? \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') : 'N/A' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Montant:</strong>
                                    {{ $operation->montant ? number_format($operation->montant, 0, ',', ' ') . ' FCFA' : 'N/A' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Demandeur:</strong> {{ $operation->demandeur_name }}
                                </div>
                                <div>
                                    <strong>Email:</strong> {{ $operation->demandeur_email }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description complète -->
    @if($operation->description)
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-file-alt me-2"></i>
                Description
            </h6>
        </div>
        <div class="card-body">
            <div class="description-content">
                {!! nl2br(e($operation->description)) !!}
            </div>
        </div>
    </div>
    @endif

    <!-- Formulaire de validation intelligent -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-{{ $validatorColor }}">
                <i class="fas fa-gavel me-2"></i>
                @if($isLastStep)
                    Décision Finale
                @else
                    Validation de l'Étape {{ $step }}
                @endif
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('operations.validate.submit', ['operation' => $operation->id, 'step' => $step]) }}">
                @csrf

                <!-- Commentaire obligatoire -->
                <div class="mb-4">
                    <label for="commentaire" class="form-label">
                        <i class="fas fa-comment me-2"></i>
                        Commentaire de validation
                        <span class="text-danger">*</span>
                        @if($isLastStep)
                            <small class="text-muted">(Décision finale - sera visible par le demandeur)</small>
                        @endif
                    </label>
                    <textarea name="commentaire" id="commentaire" rows="4"
                              class="form-control @error('commentaire') is-invalid @enderror"
                              placeholder="@if($isLastStep)
                                Expliquez votre décision finale (obligatoire)...
                            @else
                                Ajoutez votre commentaire de validation (obligatoire)...
                            @endif"
                              required minlength="5" maxlength="500"></textarea>
                    @error('commentaire')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        @if($isLastStep)
                            Votre commentaire sera transmis au demandeur avec la notification finale.
                        @else
                            Votre commentaire sera visible par les validateurs suivants.
                        @endif
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <button type="button"
                                onclick="openValidationModal({{ $operation->id }}, {{ $step }}, 'approve', {
                                    ref: '#{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}',
                                    amount: '{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA',
                                    title: '{{ $operation->titre }}',
                                    validatorName: '{{ Auth::user()->name }}',
                                    validatorEmail: '{{ Auth::user()->email }}'
                                })"
                                class="btn btn-{{ $isLastStep ? 'success' : $validatorColor }} w-100">
                            <i class="fas fa-{{ $isLastStep ? 'check-double' : 'check' }} me-2"></i>
                            @if($isLastStep)
                                Approuver Définitivement
                            @else
                                Approuver Étape {{ $step }}
                            @endif
                        </button>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button type="button"
                                onclick="openValidationModal({{ $operation->id }}, {{ $step }}, 'reject', {
                                    ref: '#{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}',
                                    amount: '{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA',
                                    title: '{{ $operation->titre }}',
                                    validatorName: '{{ Auth::user()->name }}',
                                    validatorEmail: '{{ Auth::user()->email }}'
                                })"
                                class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>
                            @if($isLastStep)
                                Rejeter Définitivement
                            @else
                                Rejeter Étape {{ $step }}
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="alert alert-{{ $isLastStep ? 'success' : 'info' }} mt-3">
                    <h6 class="alert-heading">
                        <i class="fas fa-lightbulb me-2"></i>
                        @if($isLastStep)
                            Information Importante - Validateur Final
                        @else
                            Information - Étape {{ $step }}
                        @endif
                    </h6>
                    @if($isLastStep)
                        <p class="mb-2">
                            <strong>Vous êtes le validateur final.</strong> Votre décision sera considérée comme décision finale pour cette opération.
                        </p>
                        <ul class="mb-0">
                            <li>✅ Si vous approuvez, l'opération sera validée et le demandeur notifié</li>
                            <li>❌ Si vous rejetez, l'opération sera rejetée et le demandeur notifié</li>
                            <li>📧 Votre commentaire sera transmis au demandeur</li>
                            <li>🔒 Cette action est irréversible</li>
                        </ul>
                    @else
                        <p class="mb-2">
                            <strong>Validation séquentielle en cours.</strong> Après votre validation, l'étape suivante sera activée automatiquement.
                        </p>
                        <ul class="mb-0">
                            <li>✅ Si vous approuvez, le validateur suivant recevra une notification</li>
                            <li>❌ Si vous rejetez, l'opération sera rejetée</li>
                            <li>📧 Votre commentaire sera visible par les validateurs suivants</li>
                            <li>🔄 Le demandeur sera notifié de l'avancement</li>
                        </ul>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Pièces jointes -->
    @if($operation->fichiers && $operation->fichiers->count() > 0)
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-paperclip me-2"></i>
                Pièces Jointes ({{ $operation->fichiers->count() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($operation->fichiers as $fichier)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center p-3 border rounded bg-light">
                            <i class="fas fa-file me-3 text-{{ getFileIconColor($fichier->type_fichier) }}"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $fichier->nom_original }}</div>
                                <div class="small text-muted">
                                    {{ formatFileSize($fichier->taille) }} •
                                    {{ \Carbon\Carbon::parse($fichier->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $fichier->chemin) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.validation-chain {
    font-size: 0.9rem;
}

.chain-step {
    position: relative;
}

.step-indicator {
    width: 30px;
    text-align: center;
    margin-right: 15px;
}

.step-content {
    border-left: 2px solid #e9ecef;
    padding-left: 15px;
}

.description-content {
    line-height: 1.6;
    white-space: pre-wrap;
}

.progress-bar {
    transition: all 0.3s ease;
}

.border-primary {
    border-left: 4px solid #007bff !important;
}

.border-info {
    border-left: 4px solid #17a2b8 !important;
}

.border-success {
    border-left: 4px solid #28a745 !important;
}

/* Styles pour les métriques de temps */
.time-metric {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    padding: 15px 10px;
    text-align: center;
    border: 1px solid #dee2e6;
    transition: transform 0.2s ease;
}

.time-metric:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.metric-number {
    font-size: 1.8rem;
    font-weight: bold;
    line-height: 1;
    color: #495057;
}

.metric-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Animation pour le niveau atteint */
.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left: 4px solid #28a745 !important;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left: 4px solid #17a2b8 !important;
}

/* Responsive pour les métriques */
@media (max-width: 768px) {
    .time-metric {
        padding: 10px 5px;
        margin-bottom: 10px;
    }

    .metric-number {
        font-size: 1.5rem;
    }

    .metric-label {
        font-size: 0.7rem;
    }
}
</style>

@endphp
@endsection

<!-- Inclure le pop-up de validation -->
@include('operations.validation-popup')
