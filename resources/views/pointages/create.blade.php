@extends('layouts.app')

@section('title', 'Nouveau Pointage Personnel | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock me-2 text-primary"></i>Nouveau Pointage Personnel
            </h1>
            <p class="text-muted mb-0">Enregistrer le pointage journalier du personnel</p>
        </div>
        <a href="{{ route('rh.pointages-engins.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Informations de Pointage
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('rh.pointages-engins.store') }}" method="POST">
                        @csrf

                        <!-- Informations Mission -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-tasks me-2"></i>Mission et Engin
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="operation_id" class="form-label fw-bold">
                                    <i class="fas fa-project-diagram me-1"></i>Mission (BC) *
                                </label>
                                <select name="operation_id" id="operation_id" class="form-select" required>
                                    <option value="">Sélectionner une mission</option>
                                    @foreach($operations as $operation)
                                        <option value="{{ $operation->id }}" 
                                                data-debut="{{ $operation->created_at->format('Y-m-d') }}"
                                                data-fin="{{ $operation->echeance?->format('Y-m-d') }}">
                                            {{ $operation->titre }} - {{ $operation->montant ?? 0 }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label fw-bold">
                                    <i class="fas fa-truck me-1"></i>Engin *
                                </label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                                    <option value="">Sélectionner un engin</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}">
                                            {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="driver_id" class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i>Chauffeur *
                                </label>
                                <select name="driver_id" id="driver_id" class="form-select" required>
                                    <option value="">Sélectionner un chauffeur</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">
                                            {{ $driver->name }} - {{ $driver->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_pointage" class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Date du Pointage *
                                </label>
                                <input type="date" name="date_pointage" id="date_pointage" 
                                       class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- Horaires -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-clock me-2"></i>Horaires de Travail
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="heure_debut" class="form-label fw-bold">
                                    <i class="fas fa-play me-1"></i>Heure de Début *
                                </label>
                                <input type="time" name="heure_debut" id="heure_debut" 
                                       class="form-control" required>
                                <small class="text-muted">Heure de début de travail</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="heure_fin" class="form-label fw-bold">
                                    <i class="fas fa-stop me-1"></i>Heure de Fin
                                </label>
                                <input type="time" name="heure_fin" id="heure_fin" 
                                       class="form-control">
                                <small class="text-muted">Laisser vide si en cours</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="objectif_heures" class="form-label fw-bold">
                                    <i class="fas fa-bullseye me-1"></i>Objectif Heures/Jour
                                </label>
                                <input type="number" name="objectif_heures" id="objectif_heures" 
                                       class="form-control" value="8" min="0" step="0.5">
                                <small class="text-muted">Objectif d'heures par jour</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="objectif_jours" class="form-label fw-bold">
                                    <i class="fas fa-calendar-check me-1"></i>Objectif Jours Total
                                </label>
                                <input type="number" name="objectif_jours" id="objectif_jours" 
                                       class="form-control" value="1" min="0" step="0.5">
                                <small class="text-muted">Objectif total en jours</small>
                            </div>
                        </div>

                        <!-- Kilométrage et Carburant -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-tachometer-alt me-2"></i>Kilométrage et Carburant
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="kilometrage_debut" class="form-label fw-bold">
                                    <i class="fas fa-road me-1"></i>Kilométrage Début
                                </label>
                                <input type="number" name="kilometrage_debut" id="kilometrage_debut" 
                                       class="form-control" min="0" step="0.1">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="kilometrage_fin" class="form-label fw-bold">
                                    <i class="fas fa-flag-checkered me-1"></i>Kilométrage Fin
                                </label>
                                <input type="number" name="kilometrage_fin" id="kilometrage_fin" 
                                       class="form-control" min="0" step="0.1">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="carburant_debut" class="form-label fw-bold">
                                    <i class="fas fa-gas-pump me-1"></i>Carburant Début (L)
                                </label>
                                <input type="number" name="carburant_debut" id="carburant_debut" 
                                       class="form-control" min="0" step="0.1">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="carburant_fin" class="form-label fw-bold">
                                    <i class="fas fa-gas-pump me-1"></i>Carburant Fin (L)
                                </label>
                                <input type="number" name="carburant_fin" id="carburant_fin" 
                                       class="form-control" min="0" step="0.1">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="notes" class="form-label fw-bold">
                                    <i class="fas fa-sticky-note me-1"></i>Notes et Observations
                                </label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="form-control" 
                                          placeholder="Notes supplémentaires sur le pointage..."></textarea>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('rh.pointages-engins.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer le Pointage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations sur le retard -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alerte de Retard
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning small">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Règle de retard:</strong> Un engin est considéré en retard 
                        si le premier pointage est effectué plus de 48h après le début de la mission.
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <div>
                            <small class="text-muted">Délai maximum autorisé:</small>
                            <div class="fw-bold text-warning">48 heures</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calcul d'efficacité -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line me-2"></i>Calcul d'Efficacité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small">
                        <i class="fas fa-calculator me-2"></i>
                        <strong>Formule:</strong> Efficacité = (Heures réelles / Objectif heures) × 100
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Exemple:</small>
                        <div class="bg-light p-2 rounded">
                            <div>Objectif: 8 heures</div>
                            <div>Réel: 7.5 heures</div>
                            <div class="fw-bold text-info">Efficacité: 93.75%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow-sm">
                <div class="card-header bg-success">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques du Jour
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="small text-muted">Pointages aujourd'hui</div>
                            <div class="h4 text-success mb-0">0</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="small text-muted">En retard</div>
                            <div class="h4 text-warning mb-0">0</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Validés</div>
                            <div class="h4 text-info mb-0">0</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Efficacité moyenne</div>
                            <div class="h4 text-primary mb-0">0%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculation when times change
    const heureDebut = document.getElementById('heure_debut');
    const heureFin = document.getElementById('heure_fin');
    const objectifHeures = document.getElementById('objectif_heures');

    function calculateDuration() {
        if (heureDebut.value && heureFin.value) {
            const debut = new Date(`2000-01-01T${heureDebut.value}`);
            const fin = new Date(`2000-01-01T${heureFin.value}`);
            
            if (fin < debut) {
                fin.setDate(fin.getDate() + 1);
            }
            
            const duration = (fin - debut) / (1000 * 60 * 60);
            
            // Update a hidden field or show the duration
            console.log(`Durée calculée: ${duration.toFixed(2)} heures`);
        }
    }

    function calculateEfficacy() {
        if (heureDebut.value && heureFin.value && objectifHeures.value) {
            const debut = new Date(`2000-01-01T${heureDebut.value}`);
            const fin = new Date(`2000-01-01T${heureFin.value}`);
            
            if (fin < debut) {
                fin.setDate(fin.getDate() + 1);
            }
            
            const actualHours = (fin - debut) / (1000 * 60 * 60);
            const targetHours = parseFloat(objectifHeures.value);
            
            if (targetHours > 0) {
                const efficacy = Math.min(100, (actualHours / targetHours) * 100);
                console.log(`Efficacité: ${efficacy.toFixed(2)}%`);
            }
        }
    }

    heureDebut.addEventListener('change', () => {
        calculateDuration();
        calculateEfficacy();
    });
    
    heureFin.addEventListener('change', () => {
        calculateDuration();
        calculateEfficacy();
    });
    
    objectifHeures.addEventListener('change', calculateEfficacy);

    // Check for delay warning
    const operationSelect = document.getElementById('operation_id');
    const datePointage = document.getElementById('date_pointage');

    function checkDelay() {
        if (operationSelect.value && datePointage.value) {
            const selectedOption = operationSelect.options[operationSelect.selectedIndex];
            const operationStart = new Date(selectedOption.dataset.debut);
            const pointageDate = new Date(datePointage.value);
            
            const diffHours = (pointageDate - operationStart) / (1000 * 60 * 60);
            
            if (diffHours > 48) {
                console.log('ATTENTION: Pointage en retard!');
                // Show warning
            }
        }
    }

    operationSelect.addEventListener('change', checkDelay);
    datePointage.addEventListener('change', checkDelay);
});
</script>
@endsection

