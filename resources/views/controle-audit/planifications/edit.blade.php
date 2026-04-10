@extends('layouts.app')

@section('title', 'Modifier Planification - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-edit text-primary me-2"></i>
                Modifier la Planification
            </h1>
            <p class="text-muted mb-0">
                <span class="badge badge-primary">PLAN-{{ str_pad($planification->id, 6, '0', STR_PAD_LEFT) }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('controle-audit.planifications.show', $planification) }}" class="btn btn-kenam-outline me-2">
                <i class="fas fa-eye me-2"></i>
                Voir
            </a>
            <a href="{{ route('controle-audit.planifications.index') }}" class="btn btn-kenam-outline">
                <i class="fas fa-arrow-left me-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card-kenam">
                <div class="card-kenam-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Modifier les Informations
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('controle-audit.planifications.update', $planification) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Informations principales -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-heading me-2"></i>
                                        Titre *
                                    </label>
                                    <input type="text"
                                           class="form-control-kenam"
                                           name="titre"
                                           value="{{ old('titre', $planification->titre) }}"
                                           required
                                           placeholder="Ex: Audit Interne Q1 2025">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-list me-2"></i>
                                        Type de Planification *
                                    </label>
                                    <select class="form-control-kenam" name="type_planification" required>
                                        <option value="">Sélectionner un type</option>
                                        @foreach($types as $key => $value)
                                        <option value="{{ $key }}" {{ old('type_planification', $planification->type_planification) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label class="form-label-kenam">
                                <i class="fas fa-align-left me-2"></i>
                                Description *
                            </label>
                            <textarea class="form-control-kenam"
                                      name="description"
                                      rows="4"
                                      required
                                      placeholder="Décrivez en détail la planification...">{{ old('description', $planification->description) }}</textarea>
                        </div>

                        <!-- Service et Date -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-building me-2"></i>
                                        Service Concerné *
                                    </label>
                                    <select class="form-control-kenam" name="service_concerne_id" required>
                                        <option value="">Sélectionner un service</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_concerne_id', $planification->service_concerne_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }} ({{ $service->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-calendar me-2"></i>
                                        Date de Planification *
                                    </label>
                                    <input type="date"
                                           class="form-control-kenam"
                                           name="date_planification"
                                           value="{{ old('date_planification', $planification->date_planification->format('Y-m-d')) }}"
                                           required
                                           min="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Heures et Lieu -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-clock me-2"></i>
                                        Heure de Début *
                                    </label>
                                    <input type="time"
                                           class="form-control-kenam"
                                           name="heure_debut"
                                           value="{{ old('heure_debut', $planification->heure_debut) }}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-clock me-2"></i>
                                        Heure de Fin *
                                    </label>
                                    <input type="time"
                                           class="form-control-kenam"
                                           name="heure_fin"
                                           value="{{ old('heure_fin', $planification->heure_fin) }}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Lieu *
                                    </label>
                                    <input type="text"
                                           class="form-control-kenam"
                                           name="lieu"
                                           value="{{ old('lieu', $planification->lieu) }}"
                                           required
                                           placeholder="Ex: Salle de Réunion A">
                                </div>
                            </div>
                        </div>

                        <!-- Priorité et Statut -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-flag me-2"></i>
                                        Priorité *
                                    </label>
                                    <select class="form-control-kenam" name="priorite" required>
                                        <option value="">Sélectionner une priorité</option>
                                        @foreach($priorites as $key => $value)
                                        <option value="{{ $key }}" {{ old('priorite', $planification->priorite) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Statut *
                                    </label>
                                    <select class="form-control-kenam" name="statut" required>
                                        <option value="">Sélectionner un statut</option>
                                        @foreach($statuts as $key => $value)
                                        <option value="{{ $key }}" {{ old('statut', $planification->statut) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Budget et Rapport -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Budget Estimé (FCFA)
                                    </label>
                                    <input type="number"
                                           class="form-control-kenam"
                                           name="budget_estime"
                                           value="{{ old('budget_estime', $planification->budget_estime) }}"
                                           min="0"
                                           step="1000"
                                           placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label-kenam">
                                        <i class="fas fa-file-alt me-2"></i>
                                        Rapport Attendu
                                    </label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="rapport_attendu"
                                               value="1"
                                               {{ old('rapport_attendu', $planification->rapport_attendu) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Un rapport est attendu après cette planification
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Participants -->
                        <div class="form-group mb-3">
                            <label class="form-label-kenam">
                                <i class="fas fa-users me-2"></i>
                                Participants
                            </label>
                            <select class="form-control-kenam" name="participants[]" multiple>
                                <option value="">Sélectionner des participants</option>
                                <!-- Les participants peuvent être chargés via AJAX -->
                                @if($planification->participants)
                                    @foreach($planification->participants as $participant)
                                    <option value="{{ $participant }}" selected>{{ $participant }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Documents -->
                        <div class="form-group mb-3">
                            <label class="form-label-kenam">
                                <i class="fas fa-paperclip me-2"></i>
                                Documents Joints
                            </label>
                            <div class="border rounded p-3 bg-light">
                                <!-- Documents existants -->
                                @if($planification->documents && count($planification->documents) > 0)
                                <div class="mb-3">
                                    <small class="text-muted">Documents actuels :</small>
                                    @foreach($planification->documents as $index => $document)
                                    <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded mb-2">
                                        <span>
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            {{ basename($document) }}
                                        </span>
                                        <a href="{{ asset('storage/' . $document) }}" target="_blank" class="btn btn-sm btn-kenam-outline">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Ajout de nouveaux documents -->
                                <small class="text-muted">Ajouter de nouveaux documents :</small>
                                <input type="file"
                                       class="form-control-kenam"
                                       name="documents[]"
                                       multiple
                                       accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted">
                                    Formats acceptés : PDF, DOC, DOCX, XLS, XLSX (Max: 2MB par fichier)
                                </small>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group mb-4">
                            <label class="form-label-kenam">
                                <i class="fas fa-sticky-note me-2"></i>
                                Notes Additionnelles
                            </label>
                            <textarea class="form-control-kenam"
                                      name="notes"
                                      rows="3"
                                      placeholder="Notes ou remarques importantes...">{{ old('notes', $planification->notes) }}</textarea>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('controle-audit.planifications.show', $planification) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Annuler
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-kenam-outline me-2" onclick="previewPlanification()">
                                    <i class="fas fa-eye me-2"></i>
                                    Aperçu
                                </button>
                                <button type="submit" class="btn btn-kenam-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Mettre à Jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar informations -->
        <div class="col-lg-4">
            <!-- Informations actuelles -->
            <div class="card-kenam mb-4">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations Actuelles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label-kenam">Référence</label>
                        <div>
                            <span class="badge badge-primary">PLAN-{{ str_pad($planification->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-kenam">Créateur</label>
                        @if($planification->createur)
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
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
                    <div class="mb-3">
                        <label class="form-label-kenam">Dernière modification</label>
                        <div>
                            <strong>{{ $planification->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guide -->
            <div class="card-kenam mb-4">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Conseils de Modification
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Recommandations
                        </h6>
                        <ul class="mb-0">
                            <li>Modifiez uniquement les informations nécessaires</li>
                            <li>Conservez l'historique des modifications importantes</li>
                            <li>Informez les participants des changements majeurs</li>
                            <li>Vérifiez la cohérence des dates et heures</li>
                        </ul>
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
                        <a href="{{ route('controle-audit.planifications.show', $planification) }}" class="btn btn-kenam-outline">
                            <i class="fas fa-eye me-2"></i>
                            Voir les Détails
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'aperçu -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu de la Planification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Contenu généré dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction d'aperçu
    window.previewPlanification = function() {
        const form = document.querySelector('form');
        const formData = new FormData(form);

        const titre = formData.get('titre') || 'Non spécifié';
        const type = formData.get('type_planification') || 'Non spécifié';
        const description = formData.get('description') || 'Non spécifiée';
        const service = formData.get('service_concerne_id') || 'Non spécifié';
        const date = formData.get('date_planification') || 'Non spécifiée';
        const heureDebut = formData.get('heure_debut') || 'Non spécifiée';
        const heureFin = formData.get('heure_fin') || 'Non spécifiée';
        const lieu = formData.get('lieu') || 'Non spécifié';
        const priorite = formData.get('priorite') || 'Non spécifiée';
        const statut = formData.get('statut') || 'Non spécifié';
        const budget = formData.get('budget_estime') || 'Non spécifié';
        const rapport = formData.get('rapport_attendu') ? 'Oui' : 'Non';
        const notes = formData.get('notes') || 'Aucune';

        // Générer le contenu de l'aperçu
        const previewHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informations Principales</h6>
                    <p><strong>Titre:</strong> ${titre}</p>
                    <p><strong>Type:</strong> ${type}</p>
                    <p><strong>Description:</strong> ${description}</p>
                    <p><strong>Service:</strong> ${service}</p>
                    <p><strong>Date:</strong> ${date}</p>
                </div>
                <div class="col-md-6">
                    <h6>Détails Logistiques</h6>
                    <p><strong>Horaire:</strong> ${heureDebut} - ${heureFin}</p>
                    <p><strong>Lieu:</strong> ${lieu}</p>
                    <p><strong>Priorité:</strong> ${priorite}</p>
                    <p><strong>Statut:</strong> ${statut}</p>
                    <p><strong>Budget:</strong> ${budget} FCFA</p>
                    <p><strong>Rapport attendu:</strong> ${rapport}</p>
                    <p><strong>Notes:</strong> ${notes}</p>
                </div>
            </div>
        `;

        document.getElementById('previewContent').innerHTML = previewHTML;
        new bootstrap.Modal(document.getElementById('previewModal')).show();
    };

    // Validation des heures
    document.querySelector('input[name="heure_debut"]').addEventListener('change', function() {
        const debut = this.value;
        const finInput = document.querySelector('input[name="heure_fin"]');
        finInput.min = debut;

        if (finInput.value && finInput.value <= debut) {
            finInput.value = '';
        }
    });

    // Formatage du budget
    document.querySelector('input[name="budget_estime"]').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('fr-FR');
        }
        this.value = value;
    });
});

window.changeStatut = function() {
    // Implémenter le changement de statut si nécessaire
    alert('Fonctionnalité de changement de statut à implémenter');
};
</script>

<style>
.avatar-sm {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
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
