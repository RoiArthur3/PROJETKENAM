@extends('layouts.app')

@section('title', 'Validation d\'Opération - KENAM SERVICES')

@php
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

if (!function_exists('getPriorityColor')) {
    function getPriorityColor($priority) {
        return match ($priority) {
            'urgente' => 'danger',
            'haute' => 'warning',
            'moyenne' => 'info',
            default => 'secondary',
        };
    }
}

if (!function_exists('formatDuration')) {
    function formatDuration($duration) {
        $days = (int) ($duration->days ?? 0);
        $hours = (int) ($duration->h ?? 0);
        $minutes = (int) ($duration->i ?? 0);

        if ($days > 0) return "{$days}j {$hours}h {$minutes}min";
        if ($hours > 0) return "{$hours}h {$minutes}min";
        return "{$minutes}min";
    }
}

if (!function_exists('getValidationStepBadgeColor')) {
    function getValidationStepBadgeColor($status) {
        return match (strtoupper((string) $status)) {
            'APPROUVE', 'VALIDEE', 'APPROUVEE' => 'success',
            'EN_COURS', 'PENDING' => 'warning',
            'REJETE', 'REJETEE', 'REJECTED' => 'danger',
            default => 'secondary',
        };
    }
}

if (!function_exists('getValidationStepLabel')) {
    function getValidationStepLabel($status) {
        return match (strtoupper((string) $status)) {
            'APPROUVE', 'VALIDEE', 'APPROUVEE' => 'Validée',
            'EN_COURS', 'PENDING' => 'En cours',
            'REJETE', 'REJETEE', 'REJECTED' => 'Rejetée',
            'EN_ATTENTE' => 'En attente',
            default => ucfirst(strtolower((string) $status)),
        };
    }
}

if (!function_exists('getValidationStepIcon')) {
    function getValidationStepIcon($status) {
        return match (strtoupper((string) $status)) {
            'APPROUVE', 'VALIDEE', 'APPROUVEE' => 'fa-check-circle',
            'EN_COURS', 'PENDING' => 'fa-clock',
            'REJETE', 'REJETEE', 'REJECTED' => 'fa-times-circle',
            default => 'fa-circle',
        };
    }
}

$validationSteps = collect($validationSteps ?? []);
$currentStep = $currentStep ?? null;
$step = isset($step) ? (int) $step : 1;

if (!$currentStep) {
    $currentStep = $validationSteps->firstWhere('statut', 'EN_COURS') ?? $validationSteps->first();
    $step = $currentStep->ordre_validation ?? $step;
}

$totalSteps = max($validationSteps->count(), 1);
$approvedSteps = $validationSteps->filter(function ($validationStep) {
    return in_array(strtoupper((string) ($validationStep->statut ?? '')), ['APPROUVE', 'VALIDEE', 'APPROUVEE'], true);
})->count();
$progress = (int) floor(($approvedSteps / $totalSteps) * 100);
$isLastStep = ($step === $totalSteps);

$validatorRole = 'Validateur';
$validatorDescription = 'Niveau de validation en cours';
$validatorIcon = 'fa-user-shield';
$validatorColor = 'info';

$currentStepRow = $validationSteps->firstWhere('ordre_validation', $step);
$currentRoleLabel = $currentStepRow->role_label ?? null;

if (str_starts_with((string) $currentRoleLabel, 'validateur')) {
    $validatorRole = 'Validateur';
    $validatorDescription = 'Votre approbation transmet l\'opération à l\'étape suivante.';
    $validatorIcon = 'fa-user-check';
    $validatorColor = 'primary';
} elseif ($currentRoleLabel === 'responsable') {
    $validatorRole = 'Responsable';
    $validatorDescription = 'Vous validez au nom du service responsable.';
    $validatorIcon = 'fa-user-tie';
    $validatorColor = 'info';
} elseif ($currentRoleLabel === 'dg') {
    $validatorRole = 'Direction Générale';
    $validatorDescription = 'Validation finale de la direction générale.';
    $validatorIcon = 'fa-star';
    $validatorColor = 'warning';
} elseif ($currentRoleLabel === 'compta') {
    $validatorRole = 'Comptabilité / Trésorerie';
    $validatorDescription = 'Choisissez la caisse qui exécutera le paiement.';
    $validatorIcon = 'fa-calculator';
    $validatorColor = 'success';
} elseif ($currentRoleLabel === 'caisse') {
    $validatorRole = 'Caisse';
    $validatorDescription = 'Vous confirmez l\'exécution effective du paiement.';
    $validatorIcon = 'fa-cash-register';
    $validatorColor = 'danger';
}

$maxStepReached = 0;
$maxStepDate = null;
$maxStepValidator = null;

foreach ($validationSteps as $validationStep) {
    if (in_array(strtoupper((string) ($validationStep->statut ?? '')), ['APPROUVE', 'VALIDEE', 'APPROUVEE'], true)
        && (int) $validationStep->ordre_validation > $maxStepReached) {
        $maxStepReached = (int) $validationStep->ordre_validation;
        $maxStepDate = $validationStep->validated_at ?? $validationStep->date_validation ?? null;
        $maxStepValidator = $validationStep->validated_by ?? $validationStep->validateur_name ?? 'Validateur';
    }
}

$totalProcessingTime = null;
$timeColor = 'secondary';
$timeIcon = 'clock';

if ($maxStepDate && $operation->created_at) {
    $createdDate = \Carbon\Carbon::parse($operation->created_at);
    $validatedDate = \Carbon\Carbon::parse($maxStepDate);
    $totalProcessingTime = $createdDate->diff($validatedDate);

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

$servicesLabel = $validationSteps->pluck('service_name')->filter()->unique()->join(', ');
@endphp

@section('content')
<div class="container-fluid validation-page">
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

    <div class="mb-3">
        <ul class="nav nav-pills flex-wrap gap-2">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('operations.show', ['operationId' => $operation->id]) }}#infos">Informations</a>
            </li>
            <li class="nav-item">
                <span class="nav-link active">Validation</span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#historique-validation">Historique</a>
            </li>
        </ul>
    </div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas {{ $validatorIcon }} me-2 text-{{ $validatorColor }}"></i>
                Validation d'Opération #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="text-muted mb-0">{{ $operation->titre }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-{{ $validatorColor }} fs-6">{{ $validatorRole }}</span>
            <span class="badge bg-secondary fs-6">Étape {{ $step }}/{{ $totalSteps }}</span>
            <a href="{{ route('operations.show', ['operationId' => $operation->id]) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>Voir la fiche
            </a>
            <a href="{{ route('validations.history') }}" class="btn btn-outline-info">
                <i class="fas fa-history me-2"></i>Historique global
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="m-0 fw-bold text-primary">Évolution de l'opération</h6>
                <span class="badge bg-{{ $operation->statut_courant === 'rejetee' ? 'danger' : ($progress === 100 ? 'success' : $validatorColor) }}">
                    {{ $progress }}% complété
                </span>
            </div>
            <div class="progress shadow-sm mb-3 validation-progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $operation->statut_courant === 'rejetee' ? 'danger' : ($progress === 100 ? 'success' : $validatorColor) }}"
                     role="progressbar"
                     style="width: {{ $progress }}%;"
                     aria-valuenow="{{ $progress }}"
                     aria-valuemin="0"
                     aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between overflow-auto gap-3 validation-steps-strip">
                <div class="text-center strip-step">
                    <i class="fas fa-file-invoice text-success"></i><br>
                    <small class="fw-bold text-success">Création</small>
                </div>
                @foreach($validationSteps as $validationStep)
                    @php
                        $stepStatus = strtoupper((string) ($validationStep->statut ?? ''));
                        $stepColor = getValidationStepBadgeColor($stepStatus);
                        $stepIcon = getValidationStepIcon($stepStatus);
                    @endphp
                    <div class="text-center strip-step">
                        <i class="fas {{ $stepIcon }} text-{{ $stepColor }}"></i><br>
                        <small class="fw-bold text-{{ $stepColor }}">S{{ $validationStep->ordre_validation }}</small>
                    </div>
                @endforeach
                <div class="text-center strip-step">
                    <i class="fas {{ $progress === 100 ? 'fa-flag-checkered text-success' : 'fa-flag text-muted' }}"></i><br>
                    <small class="fw-bold {{ $progress === 100 ? 'text-success' : 'text-muted' }}">Clôture</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8">
            <div id="infos" class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Informations générales</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Titre :</strong> {{ $operation->titre }}</p>
                            <p><strong>Montant :</strong>
                                @php
                                    $montant = is_numeric($operation->montant ?? null) ? $operation->montant : 0;
                                    $montantFormate = number_format($montant, 0, ',', ' ') . ' FCFA';
                                    try {
                                        $montantEnLettres = \App\Helpers\OperationHelper::numberToWords($montant);
                                        if (empty($montantEnLettres)) {
                                            $montantEnLettres = 'zéro';
                                        }
                                    } catch (\Exception $e) {
                                        $montantEnLettres = 'zéro';
                                    }
                                @endphp
                                {{ $montantFormate }}
                                <br>
                                <small class="text-muted">({{ ucfirst($montantEnLettres) }} Francs CFA)</small>
                            </p>
                            <p><strong>Services concernés :</strong> {{ $servicesLabel ?: 'Non défini' }}</p>
                            <p><strong>Priorité :</strong>
                                <span class="badge bg-{{ getPriorityColor($operation->priorite) }}">{{ ucfirst($operation->priorite ?? 'normale') }}</span>
                            </p>
                            <p><strong>Date de création :</strong> {{ $operation->created_at ? \Carbon\Carbon::parse($operation->created_at)->format('d/m/Y H:i') : 'Non définie' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Statut actuel :</strong>
                                <span class="badge bg-{{ getValidationStepBadgeColor($operation->statut_courant) }}">{{ ucfirst(str_replace('_', ' ', (string) $operation->statut_courant)) }}</span>
                            </p>
                            <p><strong>Échéance :</strong> {{ $operation->echeance ? \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') : 'Non définie' }}</p>
                            <p><strong>Créé par :</strong> {{ $operation->demandeur_name ?? ($operation->user->name ?? 'Non défini') }}</p>
                            <p><strong>Contact :</strong> {{ $operation->demandeur_email ?? ($operation->user->email ?? 'Non défini') }}</p>
                            <p><strong>Type :</strong> {{ $operation->typeOperation->libelle ?? 'Non défini' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($operation->description)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Description</h6>
                    </div>
                    <div class="card-body">
                        <div class="description-content">{!! nl2br(e($operation->description)) !!}</div>
                    </div>
                </div>
            @endif

            @if($operation->fichiers && $operation->fichiers->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-paperclip me-2"></i>Pièces jointes ({{ $operation->fichiers->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($operation->fichiers as $fichier)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded bg-light p-3 h-100">
                                        @if($fichier->estImage())
                                            <div class="mb-3 text-center bg-white rounded p-2 attachment-preview">
                                                <a href="{{ $fichier->url }}" class="image-preview-trigger"
                                                   data-file-id="{{ $fichier->id }}"
                                                   data-file-name="{{ $fichier->nom }}">
                                                    <img src="{{ $fichier->url }}" alt="{{ $fichier->nom }}" class="img-fluid preview-image">
                                                </a>
                                            </div>
                                        @endif
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-file fa-2x text-{{ getFileIconColor($fichier->type_fichier ?? $fichier->type_mime ?? '') }}"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="text-truncate fw-bold" title="{{ $fichier->nom_original ?? $fichier->nom }}">
                                                    {{ $fichier->nom_original ?? $fichier->nom }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $fichier->taille_formatee ?? number_format(((int) ($fichier->taille ?? 0)) / 1024, 1, ',', ' ') . ' Ko' }}
                                                </small>
                                            </div>
                                            <div class="ms-2 d-flex">
                                                @if(!empty($fichier->id) && \Illuminate\Support\Facades\Route::has('operations.files.download'))
                                                    <a href="{{ route('operations.files.download', $fichier->id) }}" class="btn btn-sm btn-outline-success me-1" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ $fichier->url }}" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Ouvrir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($fichier->estImage() && !empty($fichier->id) && \Illuminate\Support\Facades\Route::has('operations.files.download-as-pdf'))
                                                    <a href="{{ route('operations.files.download-as-pdf', $fichier->id) }}" class="btn btn-sm btn-outline-danger" title="Convertir en PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div id="historique-validation" class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Historique de validation
                    </h6>
                    <span class="badge bg-primary rounded-pill">{{ $validationSteps->count() }} étape(s)</span>
                </div>
                <div class="card-body p-0">
                    @forelse($validationSteps as $validationStep)
                        @php
                            $stepStatus = strtoupper((string) ($validationStep->statut ?? ''));
                            $stepColor = getValidationStepBadgeColor($stepStatus);
                            $stepLabel = getValidationStepLabel($stepStatus);
                            $stepIcon = getValidationStepIcon($stepStatus);
                            $roleName = match ($validationStep->role_label ?? null) {
                                'responsable' => 'Responsable',
                                'dg' => 'Direction Générale',
                                'compta' => 'Comptabilité / Trésorerie',
                                'caisse' => 'Caisse',
                                default => 'Validation ' . ($validationStep->ordre_validation ?? '?'),
                            };
                        @endphp
                        <div class="px-4 py-3 border-start border-4 border-{{ $stepColor }} {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex align-items-start">
                                <div class="me-3 mt-1">
                                    <i class="fas {{ $stepIcon }} text-{{ $stepColor }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <span class="fw-bold">Étape {{ $validationStep->ordre_validation }}</span>
                                            <small class="text-muted ms-2">{{ $roleName }}</small>
                                        </div>
                                        <span class="badge bg-{{ $stepColor }}">{{ $stepLabel }}</span>
                                    </div>
                                    <div class="small text-muted">
                                        {{ $validationStep->service_name ?? 'Service non défini' }}
                                        @if(!empty($validationStep->validateur_name))
                                            <span class="mx-1">•</span>{{ $validationStep->validateur_name }}
                                        @endif
                                    </div>
                                    @if(!empty($validationStep->date_validation) || !empty($validationStep->validated_at))
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($validationStep->date_validation ?? $validationStep->validated_at)->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox mb-2 fa-2x"></i>
                            <p class="mb-0">Aucune étape de validation disponible.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4 border-top border-4 border-{{ $validatorColor }} validation-sidebar-card">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-{{ $validatorColor }}">
                        <i class="fas {{ $validatorIcon }} me-2"></i>{{ $validatorRole }}
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ $validatorDescription }}</p>

                    <div class="alert alert-light border mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="small text-muted">Niveau atteint</div>
                                <div class="fw-bold">{{ $maxStepReached > 0 ? 'Étape ' . $maxStepReached : 'Aucune validation encore' }}</div>
                                @if($maxStepValidator)
                                    <small class="text-muted">par {{ $maxStepValidator }}</small>
                                @endif
                            </div>
                            <span class="badge bg-success">{{ $maxStepReached }}/{{ $totalSteps }}</span>
                        </div>
                    </div>

                    @if($totalProcessingTime)
                        <div class="alert alert-{{ $timeColor }} border-0 mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-{{ $timeIcon }} me-2"></i>
                                <strong>Temps de traitement</strong>
                            </div>
                            <div>{{ formatDuration($totalProcessingTime) }}</div>
                            <small class="text-muted">
                                Depuis le {{ \Carbon\Carbon::parse($operation->created_at)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('operations.validate', ['operationId' => $operation->id]) }}" id="validationForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="step" value="{{ $step }}">
                        <input type="hidden" name="action" value="approve" id="actionField">
                        <input type="hidden" name="caisse_email" id="caisseEmailField">

                        @php
                            $hasValidationAttachment = $operation->fichiers->isNotEmpty();
                        @endphp

                        @if(!$hasValidationAttachment)
                            <div class="alert alert-warning d-flex align-items-start" role="alert">
                                <i class="fas fa-paperclip me-2 mt-1"></i>
                                <div>
                                    <strong>Pièce jointe obligatoire</strong><br>
                                    Cette opération doit avoir au moins un fichier joint avant validation.
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="commentaire" class="form-label fw-bold">Commentaire</label>
                            <textarea name="commentaire" id="commentaire" rows="4"
                                      class="form-control @error('commentaire') is-invalid @enderror"
                                      placeholder="Ajoutez un commentaire de validation..."
                                      maxlength="500"></textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="validation_attachment" class="form-label fw-bold">Pièce jointe de validation</label>
                            <input type="file"
                                   name="validation_attachment"
                                   id="validation_attachment"
                                   class="form-control @error('validation_attachment') is-invalid @enderror"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.csv"
                                   {{ !$hasValidationAttachment ? 'required' : '' }}>
                            <small class="text-muted">Formats acceptés : PDF, image, Word, Excel (max 10 Mo).</small>
                            @error('validation_attachment')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <div class="form-check form-switch admin-force-approve p-3 rounded border border-danger bg-light shadow-sm mb-3">
                                <input class="form-check-input ms-0 me-3" type="checkbox" name="force_approve" value="1" id="forceApprove">
                                <label class="form-check-label fw-bold text-danger" for="forceApprove">
                                    <i class="fas fa-bolt me-2"></i>Approbation directe
                                </label>
                                <p class="small text-muted mb-0 mt-2">Valide immédiatement les étapes restantes.</p>
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            @if($currentRoleLabel === 'compta')
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#caisseModal">
                                    <i class="fas fa-cash-register me-2"></i>Bon pour exécution
                                </button>
                            @else
                                <button type="submit" class="btn btn-{{ $isLastStep ? 'success' : $validatorColor }}">
                                    <i class="fas fa-{{ $isLastStep ? 'check-double' : 'check' }} me-2"></i>
                                    @if($currentRoleLabel === 'caisse')
                                        Confirmer le décaissement
                                    @elseif($isLastStep)
                                        Approuver définitivement
                                    @else
                                        Approuver l'étape {{ $step }}
                                    @endif
                                </button>
                            @endif

                            <button type="button" class="btn btn-danger"
                                    onclick="document.getElementById('actionField').value='reject'; document.getElementById('validationForm').submit();">
                                <i class="fas fa-times me-2"></i>
                                @if($isLastStep)
                                    Rejeter définitivement
                                @else
                                    Rejeter l'étape {{ $step }}
                                @endif
                            </button>

                            <a href="{{ route('operations.show', ['operationId' => $operation->id]) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la fiche
                            </a>
                        </div>
                    </form>

                    <div class="alert alert-{{ $isLastStep ? 'success' : 'info' }} mt-3 mb-0">
                        <h6 class="alert-heading">{{ $isLastStep ? 'Décision finale' : 'Validation séquentielle' }}</h6>
                        <p class="mb-0">
                            @if($isLastStep)
                                Votre décision clôture le circuit de validation de cette opération.
                            @else
                                Après votre validation, l'étape suivante sera activée automatiquement.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($currentRoleLabel) && $currentRoleLabel === 'compta')
        <div class="modal fade" id="caisseModal" tabindex="-1" aria-labelledby="caisseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="caisseModalLabel">
                            <i class="fas fa-cash-register me-2"></i>Choisir la caisse pour exécution
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted mb-3">
                            Sélectionnez la caissière qui devra exécuter le paiement de cette opération.
                        </p>

                        <div class="alert alert-warning py-2 mb-4">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            <strong>Montant à décaisser :</strong>
                            {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <button type="button"
                                        class="btn btn-outline-primary w-100 p-3 caisse-choice-btn"
                                        data-email="tossaviama@kenamservices.net"
                                        onclick="selectCaisse('tossaviama@kenamservices.net', 'CAISSIÈRE 1 - Tossavi Ama')">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3"><i class="fas fa-wallet fa-2x"></i></div>
                                        <div class="text-start">
                                            <div class="fw-bold fs-5">CAISSIÈRE 1</div>
                                            <div class="text-muted small">Tossavi Ama</div>
                                            <div class="text-muted small"><i class="fas fa-envelope me-1"></i>tossaviama@kenamservices.net</div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div class="col-12">
                                <button type="button"
                                        class="btn btn-outline-success w-100 p-3 caisse-choice-btn"
                                        data-email="agouabenedicten@kenamservices.net"
                                        onclick="selectCaisse('agouabenedicten@kenamservices.net', 'CAISSIÈRE 2 - Agoua Bénédicte')">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3"><i class="fas fa-money-check-alt fa-2x"></i></div>
                                        <div class="text-start">
                                            <div class="fw-bold fs-5">CAISSIÈRE 2</div>
                                            <div class="text-muted small">Agoua Bénédicte</div>
                                            <div class="text-muted small"><i class="fas fa-envelope me-1"></i>agouabenedicten@kenamservices.net</div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div id="caisseConfirmation" class="alert alert-success mt-3 d-none">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong id="caisseSelectedLabel"></strong> sélectionnée.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-success fw-bold" id="confirmCaisseBtn" disabled onclick="submitWithCaisse()">
                            <i class="fas fa-paper-plane me-2"></i>Confirmer et envoyer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.validation-page .validation-progress {
    height: 15px;
    border-radius: 10px;
}

.validation-page .strip-step {
    min-width: 80px;
}

.validation-page .description-content {
    line-height: 1.6;
    white-space: pre-wrap;
}

.validation-page .attachment-preview {
    height: 140px;
    overflow: hidden;
}

.validation-page .preview-image {
    max-height: 120px;
    object-fit: contain;
}

.validation-page .validation-sidebar-card {
    position: sticky;
    top: 1rem;
}

.admin-force-approve {
    transition: all 0.3s ease;
}

.admin-force-approve:hover {
    background-color: #fff5f5 !important;
    border-color: #dc3545 !important;
}

@media (max-width: 991.98px) {
    .validation-page .validation-sidebar-card {
        position: static;
    }
}
</style>

<script>
function selectCaisse(email, label) {
    document.getElementById('caisseEmailField').value = email;
    document.getElementById('caisseSelectedLabel').textContent = label;
    document.getElementById('caisseConfirmation').classList.remove('d-none');
    document.getElementById('confirmCaisseBtn').removeAttribute('disabled');

    document.querySelectorAll('.caisse-choice-btn').forEach(function (button) {
        button.classList.remove('btn-primary', 'btn-success', 'active');
        button.classList.add(button.dataset.email === 'tossaviama@kenamservices.net' ? 'btn-outline-primary' : 'btn-outline-success');
    });

    var selectedButton = document.querySelector('.caisse-choice-btn[data-email="' + email + '"]');
    if (selectedButton) {
        selectedButton.classList.add('active');
    }
}

function submitWithCaisse() {
    if (!document.getElementById('caisseEmailField').value) {
        alert('Veuillez sélectionner une caissière.');
        return;
    }

    document.getElementById('actionField').value = 'approve';
    document.getElementById('validationForm').submit();
}
</script>
@endsection
