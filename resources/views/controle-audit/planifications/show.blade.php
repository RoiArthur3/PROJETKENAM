@extends('layouts.app')

@section('title', 'Détails Planification - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-calendar-alt text-primary me-2"></i>
                Détails de la Planification
            </h1>
            <p class="text-muted mb-0">
                <span class="badge badge-primary">PLAN-{{ str_pad($planification->id, 6, '0', STR_PAD_LEFT) }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('controle-audit.planifications.index') }}" class="btn btn-kenam-outline me-2">
                <i class="fas fa-arrow-left me-2"></i>
                Retour
            </a>
            <a href="{{ route('controle-audit.planifications.edit', $planification) }}" class="btn btn-kenam-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card-kenam">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations Générales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label-kenam">Titre</label>
                                <h5 class="mb-0">{{ $planification->titre }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label-kenam">Type</label>
                                <div class="d-flex align-items-center">
                                    <i class="{{ $planification->type_icon }} me-2" style="color: #007bff; font-size: 20px;"></i>
                                    <span class="badge badge-info">{{ $planification->type_label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-kenam">Description</label>
                        <div class="bg-light p-3 rounded">
                            {{ nl2br($planification->description) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label-kenam">Service Concerné</label>
                                @if($planification->service)
                                    <div class="d-flex align-items-center">
                                        <div class="service-icon me-2" style="background: {{ $planification->service->couleur }}20; color: {{ $planification->service->couleur }};">
                                            <i class="{{ $planification->service->icone }}"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $planification->service->nom }}</strong><br>
                                            <small class="text-muted">{{ $planification->service->code }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label-kenam">Créateur</label>
                                @if($planification->createur)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            {{ substr($planification->createur->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <strong>{{ $planification->createur->name }}</strong><br>
                                            <small class="text-muted">{{ $planification->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($planification->notes)
                    <div class="mb-3">
                        <label class="form-label-kenam">Notes Additionnelles</label>
                        <div class="bg-warning-light p-3 rounded">
                            {{ nl2br($planification->notes) }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Participants -->
            @if($planification->participants && count($planification->participants) > 0)
            <div class="card-kenam mt-4">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Participants ({{ count($planification->participants) }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($planification->participants as $participant)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    {{ substr($participant->name ?? 'NA', 0, 2) }}
                                </div>
                                <div>
                                    <strong>{{ $participant->name ?? 'Non spécifié' }}</strong><br>
                                    <small class="text-muted">{{ $participant->email ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Documents -->
            @if($planification->documents && count($planification->documents) > 0)
            <div class="card-kenam mt-4">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-paperclip me-2"></i>
                        Documents Joints ({{ count($planification->documents) }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($planification->documents as $index => $document)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-file-alt text-primary me-3" style="font-size: 24px;"></i>
                                <div class="flex-grow-1">
                                    <strong>Document {{ $index + 1 }}</strong><br>
                                    <small class="text-muted">{{ basename($document) }}</small>
                                </div>
                                <a href="{{ asset('storage/' . $document) }}" target="_blank" class="btn btn-sm btn-kenam-outline">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Informations logistiques -->
            <div class="card-kenam mb-4">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Informations Logistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label-kenam">Date</label>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar me-2 text-primary"></i>
                            <strong>{{ $planification->date_formatee }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-kenam">Horaire</label>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            <strong>{{ $planification->plage_horaire }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-kenam">Lieu</label>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            <strong>{{ $planification->lieu }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-kenam">Durée</label>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hourglass-half me-2 text-primary"></i>
                            <strong>{{ $planification->duree }} heures</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- État et priorité -->
            <div class="card-kenam mb-4">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-flag me-2"></i>
                        État et Priorité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label-kenam">Statut</label>
                        <div>
                            <span class="badge" style="background-color: {{ $planification->statut_color }}; color: white; font-size: 14px; padding: 8px 16px;">
                                <i class="fas fa-circle me-2"></i>
                                {{ $planification->statut_label }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-kenam">Priorité</label>
                        <div>
                            <span class="badge" style="background-color: {{ $planification->priorite_color }}; color: white; font-size: 14px; padding: 8px 16px;">
                                <i class="fas fa-flag me-2"></i>
                                {{ $planification->priorite_label }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-kenam">Rapport Attendu</label>
                        <div>
                            @if($planification->rapport_attendu)
                                <span class="badge badge-success">
                                    <i class="fas fa-check me-2"></i>
                                    Oui
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Non
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget -->
            <div class="card-kenam mb-4">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Budget
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-primary mb-1">{{ $planification->budget_formate }}</h4>
                        <small class="text-muted">Budget Estimé</small>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card-kenam">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('controle-audit.planifications.edit', $planification) }}" class="btn btn-kenam-outline">
                            <i class="fas fa-edit me-2"></i>
                            Modifier
                        </a>
                        <form action="{{ route('controle-audit.planifications.duplicate', $planification) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-kenam-outline w-100">
                                <i class="fas fa-copy me-2"></i>
                                Dupliquer
                            </button>
                        </form>
                        <button type="button" class="btn btn-kenam-outline w-100" onclick="changeStatut()">
                            <i class="fas fa-exchange-alt me-2"></i>
                            Changer le Statut
                        </button>
                        <form action="{{ route('controle-audit.planifications.destroy', $planification) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette planification ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal changement de statut -->
<div class="modal fade" id="changeStatutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer le Statut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('controle-audit.planifications.change-statut', $planification) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-kenam">Nouveau Statut</label>
                        <select class="form-control-kenam" name="statut" required>
                            <option value="">Sélectionner un statut</option>
                            <option value="planifie" {{ $planification->statut == 'planifie' ? 'selected' : '' }}>Planifiée</option>
                            <option value="en_cours" {{ $planification->statut == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                            <option value="terminee" {{ $planification->statut == 'terminee' ? 'selected' : '' }}>Terminée</option>
                            <option value="annulee" {{ $planification->statut == 'annulee' ? 'selected' : '' }}>Annulée</option>
                            <option value="reportee" {{ $planification->statut == 'reportee' ? 'selected' : '' }}>Reportée</option>
                        </select>
                    </div>
                    <div class="mb-3" id="motifField" style="display: none;">
                        <label class="form-label-kenam">Motif</label>
                        <textarea class="form-control-kenam" name="motif" rows="3" placeholder="Expliquez le motif du changement..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-kenam-primary">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du changement de statut
    const statutSelect = document.querySelector('select[name="statut"]');
    const motifField = document.getElementById('motifField');

    if (statutSelect && motifField) {
        statutSelect.addEventListener('change', function() {
            const statut = this.value;
            if (statut === 'annulee' || statut === 'reportee') {
                motifField.style.display = 'block';
                motifField.querySelector('textarea').required = true;
            } else {
                motifField.style.display = 'none';
                motifField.querySelector('textarea').required = false;
            }
        });
    }
});

window.changeStatut = function() {
    new bootstrap.Modal(document.getElementById('changeStatutModal')).show();
};
</script>

<style>
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
}

.service-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.bg-warning-light {
    background-color: #fff3cd !important;
    border: 1px solid #ffeaa7;
    border-radius: 6px;
}

.d-grid {
    display: grid;
    gap: 0.5rem;
}

.d-grid .btn {
    margin: 0;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .d-flex.justify-content-between > div {
        width: 100%;
    }

    .d-flex.justify-content-between .btn {
        width: 100%;
    }
}
</style>
@endpush
