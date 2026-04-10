@extends('layouts.app')

@section('title', 'Opérations - Liste')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2 text-primary"></i>{{ __('operations.index.title') }}
            </h1>
            <p class="text-muted mb-0">{{ __('operations.index.subtitle') }}</p>
        </div>
        <div>
            @if(auth()->check())
                @if($canCreateOperation ?? false)
                    <a href="{{ route('operations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>{{ __('operations.index.create_cta') }}
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operations.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="q" class="form-label">{{ __('operations.index.filters.search') }}</label>
                        <input type="text" name="q" id="q" class="form-control" placeholder="{{ __('operations.index.filters.search_placeholder') }}" value="{{ request('q') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="statut" class="form-label">{{ __('operations.index.filters.status') }}</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="">{{ __('operations.index.filters.all') }}</option>
                            <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="pending_validation" {{ request('statut') == 'pending_validation' ? 'selected' : '' }}>En attente de validation</option>
                            <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="approuvee" {{ request('statut') == 'approuvee' ? 'selected' : '' }}>Approuvée</option>
                            <option value="rejetee" {{ request('statut') == 'rejetee' ? 'selected' : '' }}>Rejetée</option>
                            <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="priorite" class="form-label">{{ __('operations.index.filters.priority') }}</label>
                        <select name="priorite" id="priorite" class="form-select">
                            <option value="">{{ __('operations.index.filters.all_priorities') }}</option>
                            <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                            <option value="moyenne" {{ request('priorite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                            <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                            <option value="urgente" {{ request('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>{{ __('operations.index.filters.filter_btn') }}
                            </button>
                            <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>{{ __('operations.index.filters.reset_btn') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des opérations -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>{{ __('operations.index.table.list_title') }}
            </h6>
        </div>
        <div class="card-body">
            @if(isset($operations) && $operations->count() > 0)
            @php
                $latestOperation = $operations->first();
            @endphp

            @if($latestOperation)
            <div class="latest-operation-banner mb-4">
                <div class="latest-operation-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <div class="small text-uppercase fw-bold text-primary mb-1">Derniere operation</div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-primary">#{{ str_pad($latestOperation->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <strong class="fs-5 mb-0">{{ $latestOperation->titre }}</strong>
                        </div>
                        <div class="text-muted small">
                            Creee par {{ $latestOperation->demandeur_name ?? 'N/A' }}
                            @if($latestOperation->created_at)
                                le {{ $latestOperation->created_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-{{ getPriorityColor($latestOperation->priorite) }}">{{ __('operations.traductions.' . $latestOperation->priorite) ?? ucfirst($latestOperation->priorite) }}</span>
                        <span class="badge bg-{{ getOperationStatusColor($latestOperation->statut_courant) }}">{{ getOperationStatusText($latestOperation->statut_courant) }}</span>
                        <a href="{{ route('operations.show', ['operationId' => $latestOperation->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Ouvrir
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('operations.index.table.id') }}</th>
                            <th>{{ __('operations.index.table.title') }}</th>
                            <th>{{ __('operations.index.table.requester') }}</th>
                            <th>{{ __('operations.index.table.priority') }}</th>
                            <th>{{ __('operations.index.table.status') }}</th>
                            @if(!in_array($user->role ?? '', ['agent']))
                            <th>{{ __('operations.index.table.validations') }}</th>
                            @endif
                            <th>{{ __('operations.index.table.due_date') }}</th>
                            <th>{{ __('operations.index.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operations as $operation)
                        <tr class="{{ $loop->first ? 'latest-operation-row' : '' }}">
                            <td>
                                <span class="badge {{ $loop->first ? 'bg-dark latest-operation-id' : 'bg-primary' }}">#{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</span>
                                @if($loop->first)
                                    <div class="small text-primary fw-bold mt-1">Plus recente</div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $operation->titre }}</strong>
                                    @if($operation->description)
                                    <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($operation->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($operation->demandeur_name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $operation->demandeur_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $operation->demandeur_email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ getPriorityColor($operation->priorite) }}">
                                    {{ __('operations.traductions.' . $operation->priorite) ?? ucfirst($operation->priorite) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ getOperationStatusColor($operation->statut_courant) }}">
                                    {{ getOperationStatusText($operation->statut_courant) }}
                                </span>
                            </td>
                            <td>
                                @if(!in_array($user->role ?? '', ['agent']))
                                    @if($operation->validations && $operation->validations->count() > 0)
                                        @php
                                            $total = $operation->validations->count();
                                            $done = $operation->validations->whereIn('statut', ['approved', 'APPROUVE', 'Approuvé'])->count();
                                            $percent = ($done / $total) * 100;
                                        @endphp
                                        <div class="progress mb-1" style="height: 4px;" title="{{ floor($percent) }}%">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <div class="small">
                                            @php
                                                // Organiser les validations par ordre
                                                $validationsByOrder = $operation->validations->sortBy('ordre_validation');
                                            @endphp
                                            <div class="validation-steps">
                                                @foreach($validationsByOrder as $validation)
                                                    @php
                                                        $stepNumber = $validation->ordre_validation;
                                                        $stepStatus = $validation->statut;

                                                        // Déterminer l'icône et la couleur selon le statut
                                                        $statusUpper = strtoupper($stepStatus);
                                                        if (in_array($statusUpper, ['APPROVED', 'APPROUVE', 'APPROUVÉ'])) {
                                                            $stepIcon = 'fa-check-circle';
                                                            $stepColor = 'success';
                                                            $stepText = 'Validé';
                                                        } elseif (in_array($statusUpper, ['EN_COURS', 'IN_PROGRESS', 'PENDING'])) {
                                                            $stepIcon = 'fa-hourglass-half';
                                                            $stepColor = 'primary';
                                                            $stepText = 'En cours';
                                                        } elseif (in_array($statusUpper, ['REJECTED', 'REJETE', 'REJETÉ'])) {
                                                            $stepIcon = 'fa-times-circle';
                                                            $stepColor = 'danger';
                                                            $stepText = 'Rejeté';
                                                        } elseif ($statusUpper === 'ANNULEE') {
                                                            $stepIcon = 'fa-ban';
                                                            $stepColor = 'warning';
                                                            $stepText = 'Annulé';
                                                        } elseif (in_array($statusUpper, ['EN_ATTENTE', 'WAITING'])) {
                                                            $stepIcon = 'fa-clock';
                                                            $stepColor = 'secondary';
                                                            $stepText = 'En attente';
                                                        } else {
                                                            $stepIcon = 'fa-circle';
                                                            $stepColor = 'secondary';
                                                            $stepText = 'En attente';
                                                        }

                                                        // Déterminer le libellé du validateur
                                                        if ($stepNumber == 1) {
                                                            $validatorLabel = 'R1';
                                                            $validatorTitle = 'Responsable';
                                                        } elseif ($stepNumber == 2) {
                                                            $validatorLabel = 'R2';
                                                            $validatorTitle = 'Responsable N+2';
                                                        } elseif ($stepNumber == 3) {
                                                            $validatorLabel = 'DG';
                                                            $validatorTitle = 'Direction Générale';
                                                        } else {
                                                            $validatorLabel = 'V' . $stepNumber;
                                                            $validatorTitle = 'Validateur ' . $stepNumber;
                                                        }
                                                    @endphp
                                                    <div class="validation-step d-flex align-items-center mb-2 p-2 border rounded bg-light">
                                                        <div class="step-indicator me-2">
                                                            <span class="badge bg-{{ $stepColor }} text-white" title="{{ $validatorTitle }}">
                                                                {{ $validatorLabel }}
                                                            </span>
                                                        </div>
                                                        <div class="step-content flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="fw-bold">{{ $stepText }}</small>
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $validation->serviceOperationnel->nom ?? 'N/A' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <small class="text-muted">{{ __('operations.index.table.no_validations') }}</small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($operation->echeance)
                                    {{ $operation->echeance->format('d/m/Y') }}
                                    @if($operation->echeance < now() && $operation->statut_courant !== 'termine')
                                        <br><small class="text-danger">{{ __('operations.index.table.overdue') }}</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('operations.show', ['operationId' => $operation->id]) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->check())
                                        @if(in_array($user->role, ['admin', 'superadmin']))
                                            <!-- Admin peut supprimer n'importe quelle opération -->
                                            <form action="{{ route('operations.destroy', ['operationId' => $operation->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette opération #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }} ? Cette action est irréversible !');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @elseif($user->role === 'manager' && $operation->operational_service_id == $user->service_id)
                                            <!-- Manager peut supprimer les opérations de son service -->
                                            <form action="{{ route('operations.destroy', ['operationId' => $operation->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette opération #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }} ? Cette action est irréversible !');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @elseif($user->role === 'agent' && $operation->user_id == $user->id)
                                            <!-- Agent peut supprimer ses propres opérations (tous statuts) -->
                                            <form action="{{ route('operations.destroy', ['operationId' => $operation->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette opération #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }} ? Cette action est irréversible !');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @elseif(in_array($user->role, ['manager_general', 'directeur', 'dg', 'comptable', 'tresorier', 'moderator', 'moderateur']))
                                            <!-- Autres rôles peuvent supprimer -->
                                            <form action="{{ route('operations.destroy', ['operationId' => $operation->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette opération #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }} ? Cette action est irréversible !');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $operations->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('operations.index.empty.title') }}</h5>
                <p class="text-muted">{{ __('operations.index.empty.text') }}</p>
                @if($canCreateOperation ?? false)
                    <a href="{{ route('operations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>{{ __('operations.index.empty.create_first') }}
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    font-size: 12px;
    font-weight: bold;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.latest-operation-banner {
    position: relative;
    padding: 1rem 1.25rem;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.12) 0%, rgba(13, 110, 253, 0.04) 100%);
    border: 1px solid rgba(0, 123, 255, 0.18);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
}

.latest-operation-banner::before {
    content: '';
    position: absolute;
    top: 12px;
    left: 12px;
    bottom: 12px;
    width: 4px;
    border-radius: 999px;
    background: linear-gradient(180deg, #0d6efd 0%, #198754 100%);
}

.latest-operation-header {
    padding-left: 0.75rem;
}

.latest-operation-row {
    background: linear-gradient(90deg, rgba(13, 110, 253, 0.12) 0%, rgba(13, 110, 253, 0.03) 100%) !important;
}

.latest-operation-row td {
    border-top: 2px solid rgba(13, 110, 253, 0.2);
    border-bottom: 2px solid rgba(13, 110, 253, 0.12);
}

.latest-operation-id {
    box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
}

/* Styles pour les étapes de validation */
.validation-steps {
    max-height: 200px;
    overflow-y: auto;
}

.validation-step {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.validation-step:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.step-indicator {
    min-width: 40px;
}

.step-indicator .badge {
    font-size: 0.7rem;
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

.step-content {
    font-size: 0.8rem;
}

.step-content .d-flex {
    align-items: center;
}

.step-content i {
    font-size: 0.9rem;
}

/* Scrollbar personnalisée pour les étapes */
.validation-steps::-webkit-scrollbar {
    width: 4px;
}

.validation-steps::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.validation-steps::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.validation-steps::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive */
@media (max-width: 768px) {
    .latest-operation-banner {
        padding: 0.9rem 1rem;
    }

    .latest-operation-header {
        padding-left: 0.5rem;
    }

    .validation-step {
        padding: 0.5rem !important;
    }

    .step-indicator {
        min-width: 30px;
    }

    .step-content {
        font-size: 0.7rem;
    }

    .validation-steps {
        max-height: 150px;
    }
}

/* Style pour le statut ANNULEE */
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.text-warning {
    color: #856404 !important;
}

/* Styles améliorés pour les tableaux Bootstrap */
.table {
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    color: #495057;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.08);
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #f1f3f4;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.02);
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: #fff;
}

/* Amélioration des badges dans les tableaux */
.table .badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* Amélioration des boutons d'action */
.table .btn-group .btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.table .btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.table .btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

.table .btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

.table .btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.table .btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
}

/* Responsive pour les tableaux */
@media (max-width: 768px) {
    .table {
        font-size: 0.875rem;
    }

    .table thead th {
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
    }

    .table td {
        padding: 0.75rem 0.5rem;
    }

    .table .badge {
        font-size: 0.7rem;
        padding: 0.4rem 0.6rem;
    }

    .table .btn-group .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
}
</style>
@endsection
