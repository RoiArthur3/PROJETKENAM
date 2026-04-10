@extends('layouts.app')

@section('title', 'Opérations - Détails')


@section('content')
<div class="container-fluid">

    @php
        // Sécurité : si $steps ou $currentStep ne sont pas définis, les initialiser
        if (!isset($steps) || !($steps instanceof \Illuminate\Support\Collection)) {
            $steps = collect();
        }
        if (!isset($currentStep)) {
            $currentStep = null;
        }
        $totalSteps = $steps->count();
        $approvedSteps = $steps->where('statut', 'approved')->count();
        $progress = $totalSteps > 0 ? floor(($approvedSteps / $totalSteps) * 100) : ($operation->statut_courant === 'approuvee' ? 100 : 0);
        $isPaid = $operation->is_paid || $operation->statut_courant === 'payee';
        if ($isPaid) {
            $progress = 100;
        }
    @endphp

    <div class="mb-3">
        <ul class="nav nav-pills flex-wrap gap-2">
            <li class="nav-item"><a class="nav-link active" href="#infos">{{ __('operations.nav.informations') }}</a></li>
            <li class="nav-item">
                @if($currentStep && $operation->demandeur_email !== auth()->user()->email)
                    <a class="nav-link" href="{{ route('operations.validate', $operation->id) }}">{{ __('operations.nav.validations') }}</a>
                @else
                    <span class="nav-link text-muted">{{ __('operations.nav.validations') }}</span>
                @endif
            </li>
            <li class="nav-item"><a class="nav-link" href="#historique">{{ __('operations.nav.history') }}</a></li>
        </ul>
    </div>

    <!-- Barre de Progression d'Évolution -->
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('operations.show.evolution_title') }}</h6>
                <span class="badge bg-{{ $isPaid ? 'success' : getOperationStatusColor($operation->statut_courant) }}">
                    @if($isPaid)
                        <i class="fas fa-check-circle me-1"></i>Payée - 100%
                    @else
                        {{ getOperationStatusText($operation->statut_courant) }} - {{ $progress }}%
                    @endif
                </span>
            </div>
            <div class="progress shadow-sm mb-2" style="height: 15px; border-radius: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $operation->statut_courant === 'rejetee' ? 'danger' : ($progress == 100 ? 'success' : 'primary') }}"
                     role="progressbar"
                     style="width: {{ $progress }}%;"
                     aria-valuenow="{{ $progress }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-1 overflow-auto">
                <div class="text-center" style="min-width: 80px;">
                    <i class="fas fa-file-invoice text-success"></i><br>
                    <small class="font-weight-bold text-success">{{ __('operations.show.start') }}</small>
                </div>
                @foreach($steps as $s)
                <div class="text-center" style="min-width: 80px;">
                    <i class="fas @if($s->statut === 'approved') fa-check-circle text-success @elseif($s->statut === 'pending' || $s->statut === 'EN_COURS') fa-clock text-warning @else fa-circle text-muted @endif"></i><br>
                    <small class="font-weight-bold @if($s->statut === 'approved') text-success @elseif($s->statut === 'pending' || $s->statut === 'EN_COURS') text-warning @else text-muted @endif">S{{ $s->ordre_validation }}</small>
                </div>
                @endforeach
                <div class="text-center" style="min-width: 80px;">
                    <i class="fas @if($operation->statut_courant === 'approuvee') fa-flag-checkered text-success @else fa-flag text-muted @endif"></i><br>
                    <small class="font-weight-bold @if($operation->statut_courant === 'approuvee') text-success @else text-muted @endif">{{ __('operations.show.end') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        @if($operation->statut_courant === 'pending_validation' || $operation->statut_courant === 'draft' || $operation->statut_courant === 'en_validation' || empty($operation->statut_courant))
            @php
                $user = auth()->user();
                $realCurrentStep = $currentStep;
                if (!$realCurrentStep && isset($steps)) {
                    $realCurrentStep = $steps->where('statut', 'EN_COURS')->first();
                }

                $canAct = false;
                if ($realCurrentStep && $user) {
                    $isVal = ($user->service_id == $realCurrentStep->service_operationnel_id);
                    if (!$isVal && isset($steps)) {
                        $sd = $steps->where('service_operationnel_id', $realCurrentStep->service_operationnel_id)->first();
                        if ($sd && $sd->service_email === $user->email) $isVal = true;
                    }
                    $canAct = $isVal || in_array($user->role, ['admin', 'superadmin']);
                }
            @endphp

            @if($canAct && $operation->demandeur_email !== $user->email)
                <a href="{{ route('operations.validate', $operation->id) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-check me-1"></i> {{ __('operations.cta.go_validation', ['step' => $realCurrentStep->ordre_validation]) }}
                </a>
            @elseif($operation->demandeur_email === $user->email)
                <span class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-info-circle me-1"></i> {{ __('operations.show.no_self_validation') }}
                </span>
            @else
                <span class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-info-circle me-1"></i> {{ __('operations.show.no_steps') }}
                </span>
            @endif
        @elseif($operation->statut_courant === 'rejected')
            @if($currentStep)
                <a href="{{ route('operations.validate', $operation->id) }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-rotate-left me-1"></i> {{ __('operations.cta.fix_review') }}
                </a>
            @else
                <span class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-info-circle me-1"></i> {{ __('operations.show.no_steps') }}
                </span>
            @endif
        @else
            <a href="{{ route('analyses.dashboard') }}" class="btn btn-success btn-sm">
                <i class="fas fa-chart-line me-1"></i> {{ __('operations.cta.open_reporting') }}
            </a>
        @endif

        @php $authUser = auth()->user(); @endphp
        @if($authUser && (in_array($authUser->role, ['admin', 'superadmin']) || ($authUser->id == $operation->user_id && $operation->statut_courant === 'brouillon') || ($authUser->role === 'manager' && $operation->operational_service_id == $authUser->service_id)))
            <form action="{{ route('operations.destroy', ['operationId' => $operation->id]) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette opération #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }} ? Cette action est irréversible !');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash me-1"></i> Supprimer
                </button>
            </form>
        @endif

        @if($authUser && in_array($authUser->role, ['admin', 'superadmin']))
            @if(!$operation->is_paid)
                <form action="{{ route('validations.operations.markPaid', $operation) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Marquer cette opération comme payée ?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-coins me-1"></i> Marquer payée
                    </button>
                </form>
            @else
                <span class="badge bg-success ms-2">
                    Payée {{ $operation->paid_at ? $operation->paid_at->format('d/m/Y H:i') : '' }}
                    @if($operation->paidBy)
                        par {{ $operation->paidBy->name }}
                    @endif
                </span>
            @endif
        @endif
    </div>

    <!-- Informations Générales -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div id="infos" class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('operations.show.general_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('operations.fields.title') }} :</strong> {{ $operation->titre }}</p>
                            @if(isset($operation->montant))
                                <p><strong>Montant :</strong>
                                    @php
                                        $montant = is_numeric($operation->montant) ? $operation->montant : 0;
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

                                    @if($montant == 0)
                                        {{ $montantFormate }} ({{ $montantEnLettres }} Francs CFA)
                                    @else
                                        {{ $montantFormate }}
                                        <br>
                                        <small class="text-muted">
                                            ({{ ucfirst($montantEnLettres) }} Francs CFA)
                                        </small>
                                    @endif
                                </p>
                            @endif
                            <p><strong>{{ __('operations.fields.services') }} :</strong> {{ $steps->pluck('service_name')->join(', ') }}</p>
                            <p><strong>{{ __('operations.fields.priority') }} :</strong>
                                <span class="badge bg-{{ getPriorityColor($operation->priorite) }}">
                                    {{ __('operations.traductions.' . $operation->priorite) ?? ucfirst($operation->priorite) }}
                                </span>
                            </p>
                            <p><strong>{{ __('operations.fields.created_at') }} :</strong> {{ $operation->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('operations.show.current_status') }} :</strong>
                                <span class="badge bg-{{ getOperationStatusColor($operation->statut_courant) }}"
                                    @if($operation->statut_courant === 'rejetee' && $statusHistory->where('to_status', 'rejetee')->isNotEmpty())
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $statusHistory->where('to_status', 'rejetee')->last()->commentaire }}"
                                    @endif>
                                    {{ getOperationStatusText($operation->statut_courant) }}
                                </span>
                            </p>
                            <p><strong>{{ __('operations.show.due_date') }} :</strong> {{ $operation->echeance?->format('d/m/Y') ?? '—' }}</p>
                            <p><strong>{{ __('operations.show.created_by') }} :</strong> {{ $operation->demandeur_name ?? '—' }}</p>
                            <p><strong>{{ __('operations.show.contact') }} :</strong> {{ $operation->demandeur_email ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>{{ __('operations.show.description') }} :</strong>
                        <div class="alert alert-info mt-2 p-3" style="white-space: pre-wrap; word-wrap: break-word; line-height: 1.6; min-height: 60px; max-width: 100%;">
                            {{ $operation->description ?? '—' }}
                        </div>
                    </div>

                    @if($operation->fichiers->count() > 0)
                        <div class="mt-4">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-paperclip me-2"></i>Pièces jointes ({{ $operation->fichiers->count() }})
                            </h6>
                            <div class="row">
                                @foreach($operation->fichiers as $fichier)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded bg-light p-2 h-100">
                                            @if($fichier->estImage())
                                                <div class="mb-2 text-center bg-white rounded p-1" style="height: 120px; overflow: hidden;">
                                                    <a href="{{ $fichier->url }}" class="image-preview-trigger"
                                                       data-file-id="{{ $fichier->id }}"
                                                       data-file-name="{{ $fichier->nom }}">
                                                        <img src="{{ $fichier->url }}" alt="{{ $fichier->nom }}" class="img-fluid" style="max-height: 110px; object-fit: contain;">
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="{{ $fichier->icone }} fa-2x text-secondary"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="text-truncate fw-bold" title="{{ $fichier->nom }}">
                                                        {{ $fichier->nom }}
                                                    </div>
                                                    <small class="text-muted">{{ $fichier->taille_formatee }}</small>
                                                </div>
                                                <div class="ms-2 d-flex">
                                                    <a href="{{ route('operations.files.download', $fichier->id) }}" class="btn btn-sm btn-outline-success me-1" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($fichier->extension === 'pdf')
                                                        <a href="{{ $fichier->url }}" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Visualiser">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('operations.files.download-as-pdf', $fichier->id) }}?view=1" target="_blank" class="btn btn-sm btn-outline-danger" title="Convertir en PDF et visualiser">
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
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('operations.show.actions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(auth()->check())
                            @php
                                $user = auth()->user();
                                // Utiliser l'objet currentStep passé par le contrôleur ou le recalculer de manière robuste
                                $realCurrentStep = $currentStep;
                                if (!$realCurrentStep && isset($steps)) {
                                    $realCurrentStep = $steps->where('statut', 'EN_COURS')->first();
                                }

                                $canValidate = false;
                                if ($realCurrentStep) {
                                    $isValidator = ($user->service_id == $realCurrentStep->service_operationnel_id);
                                    if (!$isValidator && isset($steps)) {
                                        // Vérifier par email si l'ID ne correspond pas
                                        $stepDetail = $steps->where('service_operationnel_id', $realCurrentStep->service_operationnel_id)->first();
                                        if ($stepDetail && ($stepDetail->service_email === $user->email)) {
                                            $isValidator = true;
                                        }
                                    }
                                    $canValidate = $isValidator || in_array($user->role, ['admin', 'superadmin', 'moderator', 'moderateur']);
                                }
                            @endphp

                            @if($canValidate && $operation->demandeur_email !== $user->email)
                                <a href="{{ route('operations.validate', $operation->id) }}" class="btn btn-success">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('operations.show.validate_step', ['step' => $realCurrentStep->ordre_validation]) }}
                                </a>
                            @elseif($operation->demandeur_email === $user->email && $operation->statut_courant !== 'approuvee' && $operation->statut_courant !== 'rejetee')
                                <span class="btn btn-secondary" disabled>
                                    <i class="fas fa-info-circle me-1"></i> {{ __('operations.show.no_self_validation') }}
                                </span>
                            @endif
                        @endif
                        <a href="#" class="btn btn-info">{{ __('operations.show.add_comment') }}</a>
                        <a href="{{ route('operations.download-pdf', $operation->id) }}" class="btn btn-secondary">
                            <i class="fas fa-file-pdf me-1"></i> {{ __('operations.show.download_report') }}
                        </a>

                        @if($operation->statut_courant === 'approuvee' && (auth()->user()->id === $operation->user_id || auth()->user()->email === $operation->demandeur_email))
                            <a href="{{ route('operations.bon-pour-accord', $operation->id) }}" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Envoyer le Bon Pour Accord
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow et Étapes -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('operations.show.services_sequence') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($steps as $s)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>S{{ $s->ordre_validation }} • {{ $s->service_name }}</strong><br>
                                <small class="text-muted">{{ $s->service_email }}</small>
                            </div>
                            <span class="badge bg-{{ getOperationStatusColor($s->statut) }}">{{ getOperationStatusText($s->statut) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="historique" class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>{{ __('operations.show.history_actions') }}
                    </h6>
                    <span class="badge bg-primary rounded-pill">{{ $statusHistory->count() }} action(s)</span>
                </div>
                <div class="card-body p-0">
                    @forelse($statusHistory as $log)
                    @php
                        $statusIcon = match($log->to_status) {
                            'approuvee', 'APPROUVE', 'approved' => 'fa-check-circle text-success',
                            'rejetee', 'REJETE', 'rejected' => 'fa-times-circle text-danger',
                            'en_validation', 'EN_COURS', 'pending_validation' => 'fa-clock text-warning',
                            'EN_ATTENTE' => 'fa-hourglass-start text-secondary',
                            default => 'fa-info-circle text-primary',
                        };
                        $statusBg = match($log->to_status) {
                            'approuvee', 'APPROUVE', 'approved' => 'border-start border-success border-4',
                            'rejetee', 'REJETE', 'rejected' => 'border-start border-danger border-4',
                            'en_validation', 'EN_COURS', 'pending_validation' => 'border-start border-warning border-4',
                            default => 'border-start border-primary border-4',
                        };
                    @endphp
                    <div class="px-4 py-3 {{ $statusBg }} {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-start">
                            <div class="me-3 mt-1">
                                <i class="fas {{ $statusIcon }}" style="font-size: 1.3rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <span class="fw-bold">{{ getOperationStatusText($log->to_status) }}</span>
                                        @if($log->from_status)
                                            <small class="text-muted ms-1">
                                                (depuis {{ getOperationStatusText($log->from_status) }})
                                            </small>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $log->created_at->format('d/m/Y H:i') }}
                                        <span class="d-none d-md-inline">· {{ $log->created_at->diffForHumans() }}</span>
                                    </small>
                                </div>
                                @if($log->commentaire)
                                    <p class="mb-1 text-dark" style="font-size: 0.9rem;">
                                        <i class="fas fa-comment-dots text-muted me-1"></i>{{ $log->commentaire }}
                                    </p>
                                @endif
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>{{ $log->user_name ?? 'Système' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox mb-2" style="font-size: 2rem;"></i>
                        <p class="mb-0">{{ __('operations.show.history_empty') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons de Navigation -->
    <div class="row">
        <div class="col d-flex justify-content-between">
            <a href="{{ route('operations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('operations.actions.back_to_list') }}
            </a>
            <div>
                <button type="button" class="btn btn-danger" onclick='confirmDelete({{ $operation->id }}, "{{ $operation->titre }}", "{{ route('operations.destroy', ['operationId' => $operation->id]) }}")'>
                    <i class="fas fa-trash me-1"></i>{{ __('operations.actions.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour l'envoi de mail d'exécution -->
<div class="modal fade" id="executionMailModal" tabindex="-1" aria-labelledby="executionMailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="executionMailModalLabel">{{ __('operations.execution_mail.modal_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('operations.send-execution-email', $operation->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="execution_email" class="form-label">{{ __('operations.execution_mail.recipient_label') }}</label>
                        <input type="email" class="form-control" id="execution_email" name="email" required placeholder="{{ __('operations.execution_mail.recipient_placeholder') }}">
                        <div class="form-text">{!! __('operations.execution_mail.mention_help') !!}</div>
                    </div>
                    <div class="mb-3">
                        <label for="execution_message" class="form-label">{{ __('operations.execution_mail.message_label') }}</label>
                        <textarea class="form-control" id="execution_message" name="message" rows="4" placeholder="{{ __('operations.execution_mail.message_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('operations.execution_mail.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> {{ __('operations.execution_mail.send') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    // Fonction de confirmation de suppression
    function confirmDelete(operationId, operationTitle, deleteUrl) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer l'opération "${operationTitle}" (ID: ${operationId}) ?\n\nCette action est irréversible !`)) {
            // Créer un formulaire caché pour la suppression
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            form.style.display = 'none';

            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            // Ajouter le champ pour la méthode DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            // Soumettre le formulaire
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
