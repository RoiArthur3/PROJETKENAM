@extends('layouts.app')

@section('title', 'Détails de l\'opération #'.($validation->id ?? 'N/A'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-check-circle me-2 text-primary"></i>
                Opération #{{ str_pad($validation->id, 4, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="text-muted mb-0">{{ $validation->titre }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            @php
                $statut = $validation->statut ?? $validation->statut_courant;
            @endphp
            @if(in_array($statut, ['en_attente', 'en_cours', 'pending_validation']) &&
               (!$validation->validateur_id || $validation->validateur_id == auth()->id()))
                <button type="button" class="btn btn-success" onclick="openValidationModal()">
                    <i class="fas fa-check me-1"></i> Traiter
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations de la demande
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Référence:</strong>
                                <span class="badge bg-secondary ms-2">#{{ str_pad($validation->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Statut:</strong>
                                @php
                                    $statut = $validation->statut ?? $validation->statut_courant;
                                    $color = 'secondary';
                                    if(in_array($statut, ['en_attente', 'pending_validation'])) $color = 'warning';
                                    elseif(in_array($statut, ['en_cours', 'en_validation'])) $color = 'info';
                                    elseif(in_array($statut, ['valide', 'approuvee'])) $color = 'success';
                                    elseif(in_array($statut, ['rejete', 'rejetee'])) $color = 'danger';
                                @endphp
                                <span class="badge bg-{{ $color }} ms-2">
                                    {{ ucfirst(str_replace('_', ' ', $statut)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Module source:</strong>
                                @php
                                    $module = $validation->module_source ?? 'operations';
                                @endphp
                                <span class="badge bg-primary ms-2">
                                    {{ ucfirst(str_replace('_', ' ', $module)) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <strong>Date de création:</strong>
                                <span class="ms-2">{{ $validation->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Titre:</strong>
                        <div class="mt-1">{{ $validation->titre }}</div>
                    </div>

                    @if($validation->description)
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <div class="mt-1 p-3 bg-light rounded">{{ nl2br($validation->description) }}</div>
                    </div>
                    @endif

                    @if($validation->module_source === 'operations' && $validation->operation)
                    <div class="mb-3">
                        <strong>Opération liée:</strong>
                        <div class="mt-1">
                            <a href="{{ route('operations.show', $validation->operation) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Voir l'opération #{{ $validation->operation->id }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($validation->data)
                    <div class="mb-3">
                        <strong>Données supplémentaires:</strong>
                        <div class="mt-1">
                            <pre class="bg-light p-3 rounded small">{{ json_encode($validation->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Acteurs -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Acteurs
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Initiateur -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-user-plus me-1"></i>
                            Initiateur
                        </h6>
                        @if($validation->initiateur)
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 18px;">
                                {{ strtoupper(substr($validation->initiateur->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $validation->initiateur->name }}</div>
                                <small class="text-muted">{{ $validation->initiateur->email }}</small>
                                @if($validation->initiateur->service)
                                    <div class="small text-primary">{{ $validation->initiateur->service }}</div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="text-muted">Initiateur inconnu</div>
                        @endif
                    </div>

                    <!-- Validateur -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-user-check me-1"></i>
                            Validateur
                        </h6>
                        @if($validation->validateur)
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 18px;">
                                {{ strtoupper(substr($validation->validateur->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $validation->validateur->name }}</div>
                                <small class="text-muted">{{ $validation->validateur->email }}</small>
                                @if($validation->validateur->service)
                                    <div class="small text-primary">{{ $validation->validateur->service }}</div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="text-muted">
                            <i class="fas fa-users me-1"></i>
                            Non assigné
                        </div>
                        @endif
                    </div>

                    <!-- Dates importantes -->
                    @if($validation->date_validation)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-calendar-check me-1"></i>
                            Date de validation
                        </h6>
                        <div>{{ $validation->date_validation->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif

                    @if($validation->updated_at != $validation->created_at)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Dernière mise à jour
                        </h6>
                        <div>{{ $validation->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow" id="historique">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Historique des actions
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $logs = collect();
                        if (isset($validation->logs)) {
                            $logs = $validation->logs;
                        } elseif (isset($validation->statusLogs)) {
                            $logs = $validation->statusLogs->map(function($l) {
                                return (object)[
                                    'action' => in_array($l->to_status, ['approuvee', 'valide']) ? 'approved' : ($l->to_status === 'rejetee' ? 'rejected' : 'created'),
                                    'commentaire' => $l->commentaire,
                                    'created_at' => $l->created_at,
                                    'user' => (object)['name' => $l->user_name]
                                ];
                            });
                        }
                    @endphp
                    @if($logs->count() > 0)
                    <div class="timeline">
                        @foreach($logs->sortByDesc('created_at') as $log)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $log->action === 'created' ? 'primary' : ($log->action === 'approved' ? 'success' : ($log->action === 'rejected' ? 'danger' : 'warning')) }}"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0">
                                            @switch($log->action)
                                                @case('created')
                                                    <i class="fas fa-plus-circle text-primary me-1"></i>
                                                    Demande / Action créée
                                                    @break
                                                @case('approved')
                                                    <i class="fas fa-check-circle text-success me-1"></i>
                                                    Action approuvée
                                                    @break
                                                @case('rejected')
                                                    <i class="fas fa-times-circle text-danger me-1"></i>
                                                    Action rejetée
                                                    @break
                                                @case('corrected')
                                                    <i class="fas fa-edit text-warning me-1"></i>
                                                    Retournée pour correction
                                                    @break
                                                @default
                                                    <i class="fas fa-info-circle text-info me-1"></i>
                                                    Action: {{ $log->action }}
                                            @endswitch
                                        </h6>
                                        <small class="text-muted">
                                            Par {{ $log->user->name ?? $log->user_name ?? 'Système' }}
                                            le {{ $log->created_at->format('d/m/Y à H:i') }}
                                        </small>
                                    </div>
                                </div>
                                @if($log->commentaire)
                                <div class="bg-light p-3 rounded mt-2">
                                    <strong>Commentaire:</strong>
                                    <br>
                                    {!! nl2br(e($log->commentaire)) !!}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <div class="text-muted">Aucun historique disponible</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de validation -->
<div class="modal fade" id="validationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Traiter la demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('operations.valider.validate', $validation) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Décision</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="decision" id="approve" value="approve" required>
                            <label class="btn btn-outline-success" for="approve">
                                <i class="fas fa-check me-1"></i> Approuver
                            </label>

                            <input type="radio" class="btn-check" name="decision" id="reject" value="reject">
                            <label class="btn btn-outline-danger" for="reject">
                                <i class="fas fa-times me-1"></i> Rejeter
                            </label>

                            <input type="radio" class="btn-check" name="decision" id="correct" value="correct">
                            <label class="btn btn-outline-warning" for="correct">
                                <i class="fas fa-edit me-1"></i> À corriger
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Commentaire</label>
                        <textarea class="form-control" id="commentaire" name="commentaire" rows="3"
                                  placeholder="Ajoutez un commentaire (optionnel)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Soumettre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
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
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 8px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.avatar {
    font-weight: 600;
    font-size: 16px;
}
</style>
@endpush

@push('scripts')
<script>
function openValidationModal() {
    const modal = new bootstrap.Modal(document.getElementById('validationModal'));
    modal.show();
}

// Réinitialiser le modal à la fermeture
document.getElementById('validationModal')?.addEventListener('hidden.bs.modal', function () {
    document.getElementById('validationForm')?.reset();
});
</script>
@endpush

@endsection
