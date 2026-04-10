@extends('layouts.app')

@section('title', 'Pointage des Engins - Cost Control | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-truck me-2 text-primary"></i>Pointage des Engins
            </h1>
            <p class="text-muted mb-0">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
        <div>
            <a href="{{ route('materiel.cost-control.home') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour au Dashboard
            </a>
            <button class="btn btn-light" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Actualiser
            </button>
            <a href="{{ route('materiel.vehicules') }}" class="btn btn-outline-primary">
                <i class="fas fa-truck me-1"></i>Gérer les Engins
            </a>
        </div>
    </div>

    <!-- Bande d'explication du workflow -->
    <div class="alert alert-primary border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start gap-3">
            <div class="pt-1">
                <i class="fas fa-sitemap fa-lg"></i>
            </div>
            <div class="w-100">
                <h6 class="mb-2 fw-bold">Workflow de création du pointage</h6>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-primary">1. Sélectionner l'engin</span>
                    <i class="fas fa-arrow-right text-muted"></i>
                    <span class="badge bg-info text-dark">2. Choisir la mission</span>
                    <i class="fas fa-arrow-right text-muted"></i>
                    <span class="badge bg-success">3. Saisir les horaires et données terrain</span>
                    <i class="fas fa-arrow-right text-muted"></i>
                    <span class="badge bg-warning text-dark">4. Contrôler et enregistrer</span>
                </div>
                <p class="mb-0 mt-2 small text-muted">
                    Astuce: la mission et l'engin se synchronisent automatiquement pour accélérer la saisie.
                </p>
            </div>
        </div>
    </div>

    <!-- Formulaire de pointage -->
    <form action="{{ route('materiel.cost-control.engin.pointages.store') }}" method="POST" id="pointageEnginForm">
        @csrf

        <input type="hidden" name="vehicle_mission_id" id="vehicle_mission_id" value="{{ $selectedMissionId }}">
        <input type="hidden" name="operation_id" id="operation_id" value="{{ old('operation_id') }}">
        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id') }}">
        <input type="hidden" name="unit_type" value="heure">
        <input type="hidden" name="quantity" id="quantity" value="{{ old('quantity') }}">
        <input type="hidden" name="client_id" id="client_id" value="">

        <!-- Sélection de l'engin -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-truck me-2"></i>Sélection de l'Engin
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="vehicle_select" class="form-label fw-bold">
                            <i class="fas fa-truck me-1"></i>Engin / Véhicule *
                        </label>
                        <select id="vehicle_select" class="form-select" required>
                            <option value="">Sélectionner un engin</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                        data-immat="{{ $vehicle->immatriculation ?? '' }}"
                                        data-type="{{ $vehicle->type_materiel ?? 'Non spécifié' }}"
                                        data-status="{{ $vehicle->status ?? 'Disponible' }}">
                                    {{ $vehicle->immatriculation ?? $vehicle->nom }}
                                    ({{ $vehicle->type_materiel ?? 'Engin' }})
                                    - {{ $vehicle->status ?? 'Disponible' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Choisissez l'engin à pointer</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="mission_select" class="form-label fw-bold">
                            <i class="fas fa-route me-1"></i>Mission Assignée *
                        </label>
                        <select id="mission_select" class="form-select" required>
                            <option value="">Sélectionner une mission</option>
                            @foreach($missions as $mission)
                                <option value="{{ $mission->id }}"
                                        {{ (int) $selectedMissionId === (int) $mission->id ? 'selected' : '' }}
                                        data-vehicle="{{ $mission->vehicle_id ?? '' }}"
                                    data-operation="{{ $mission->operation_id ?? '' }}"
                                        data-client="{{ $mission->client_id ?? '' }}"
                                        data-vehicle-immat="{{ $mission->vehicle->immatriculation ?? '' }}"
                                        data-client-name="{{ $mission->client->nom ?? '' }}">
                                    {{ $mission->reference ?? ('Mission #' . $mission->id) }}
                                    @if($mission->vehicle)
                                        - {{ $mission->vehicle->immatriculation ?? '' }}
                                    @endif
                                    @if($mission->client)
                                        - {{ $mission->client->nom }}
                                    @endif
                                    @if($mission->start_at)
                                        ({{ $mission->start_at->format('d/m/Y') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Mission à laquelle l'engin est assigné</div>
                    </div>
                </div>

                <!-- Informations sur l'engin sélectionné -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Engin sélectionné:</strong> <span id="selected_engin">Non sélectionné</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Type:</strong> <span id="engin_type">-</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Statut:</strong> <span id="engin_status">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations de pointage -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-clock me-2"></i>Informations de Pointage
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date_pointage" class="form-label fw-bold">
                            <i class="fas fa-calendar me-1"></i>Date de Pointage *
                        </label>
                        <input type="date" id="date_pointage" name="date_pointage"
                               class="form-control"
                               value="{{ old('date_pointage') ?? now()->format('Y-m-d') }}"
                               required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="heure_debut" class="form-label fw-bold">
                            <i class="fas fa-play me-1"></i>Heure de Début *
                        </label>
                        <input type="time" id="heure_debut" name="heure_debut"
                               class="form-control"
                               value="{{ old('heure_debut') ?? now()->format('H:i') }}"
                               required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="heure_fin" class="form-label fw-bold">
                            <i class="fas fa-stop me-1"></i>Heure de Fin *
                        </label>
                        <input type="time" id="heure_fin" name="heure_fin"
                               class="form-control"
                               value="{{ old('heure_fin') ?? now()->addHours(8)->format('H:i') }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="kilometrage_debut" class="form-label fw-bold">
                            <i class="fas fa-tachometer-alt me-1"></i>Kilométrage Début
                        </label>
                        <input type="number" id="kilometrage_debut" name="kilometrage_debut"
                               class="form-control"
                               value="{{ old('kilometrage_debut') }}"
                               step="1" min="0">
                        <div class="form-text">Kilométrage au début du service</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="kilometrage_fin" class="form-label fw-bold">
                            <i class="fas fa-tachometer-alt me-1"></i>Kilométrage Fin
                        </label>
                        <input type="number" id="kilometrage_fin" name="kilometrage_fin"
                               class="form-control"
                               value="{{ old('kilometrage_fin') }}"
                               step="1" min="0">
                        <div class="form-text">Kilométrage à la fin du service</div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="chauffeur_id" class="form-label fw-bold">
                            <i class="fas fa-user me-1"></i>Chauffeur / Opérateur
                        </label>
                        <select id="chauffeur_id" name="driver_id" class="form-select">
                            <option value="">Sélectionner un chauffeur</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name ?? ('Utilisateur #' . $driver->id) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Chauffeur ayant utilisé l'engin</div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="alert alert-light border">
                            <i class="fas fa-info-circle me-1 text-primary"></i>
                            Le volume d'activité (heures) est calculé automatiquement à partir de l'heure de début et de l'heure d'arrêt.
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="lieu_mission" class="form-label fw-bold">
                            <i class="fas fa-exclamation-triangle me-1"></i>Difficultés rencontrées
                        </label>
                        <input type="text" id="lieu_mission" name="notes"
                               class="form-control"
                               value="{{ old('notes') }}"
                               placeholder="Ex: panne légère, accès difficile, retard carburant...">
                        <div class="form-text">Décrivez brièvement les difficultés ou incidents de la journée</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <a href="{{ route('materiel.cost-control.engin.list') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Annuler
                </a>
                <button type="button" class="btn btn-outline-info" onclick="calculerDuree()">
                    <i class="fas fa-calculator me-1"></i>Calculer Durée
                </button>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check me-1"></i>Enregistrer le Pointage
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Scripts pour le pointage d'engins -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du formulaire
    const vehicleSelect = document.getElementById('vehicle_select');
    const missionSelect = document.getElementById('mission_select');
    const heureDebut = document.getElementById('heure_debut');
    const heureFin = document.getElementById('heure_fin');

    // Éléments d'affichage
    const selectedEngin = document.getElementById('selected_engin');
    const enginType = document.getElementById('engin_type');
    const enginStatus = document.getElementById('engin_status');

    // Mise à jour des informations de l'engin
    vehicleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        selectedEngin.textContent = selectedOption.dataset.immat || 'Non spécifié';
        enginType.textContent = selectedOption.dataset.type || 'Non spécifié';
        enginStatus.textContent = selectedOption.dataset.status || 'Non spécifié';
        document.getElementById('vehicle_id').value = selectedOption.value || '';

        // Synchroniser avec la mission si possible
        if (selectedOption.value) {
            for (let option of missionSelect.options) {
                if (option.dataset.vehicle === selectedOption.value) {
                    missionSelect.value = option.value;
                    break;
                }
            }
        }
    });

    // Mise à jour de la mission
    missionSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('vehicle_mission_id').value = this.value;
        document.getElementById('client_id').value = selectedOption.dataset.client || '';
        document.getElementById('operation_id').value = selectedOption.dataset.operation || '';

        if (selectedOption.dataset.vehicle && selectedOption.dataset.vehicle !== vehicleSelect.value) {
            vehicleSelect.value = selectedOption.dataset.vehicle;
            vehicleSelect.dispatchEvent(new Event('change'));
        }
    });

    window.calculerDuree = function() {
        const duree = calculerDureeHeures();
        if (duree > 0) {
            alert('Durée du travail: ' + duree.toFixed(2) + ' heures');
        } else {
            alert('Veuillez spécifier les heures de début et de fin');
        }
    };

    function calculerDureeHeures() {
        if (!heureDebut.value || !heureFin.value) return 0;

        const debut = new Date('2000-01-01 ' + heureDebut.value);
        const fin = new Date('2000-01-01 ' + heureFin.value);

        let dureeMs = fin - debut;
        if (dureeMs < 0) {
            dureeMs += 24 * 60 * 60 * 1000;
        }

        return dureeMs / (1000 * 60 * 60);
    }

    document.getElementById('pointageEnginForm').addEventListener('submit', function(e) {
        if (!vehicleSelect.value) {
            e.preventDefault();
            alert('Veuillez sélectionner un engin.');
            vehicleSelect.focus();
            return false;
        }

        if (!missionSelect.value) {
            e.preventDefault();
            alert('Veuillez sélectionner une mission.');
            missionSelect.focus();
            return false;
        }

        if (!heureDebut.value || !heureFin.value) {
            e.preventDefault();
            alert('Veuillez renseigner l\'heure de début et l\'heure d\'arrêt.');
            return false;
        }

        document.getElementById('vehicle_id').value = vehicleSelect.value || '';
        const duration = calculerDureeHeures();
        document.getElementById('quantity').value = duration > 0 ? duration.toFixed(2) : '';

        const duree = calculerDureeHeures();
        if (duree <= 0) {
            e.preventDefault();
            alert('L\'heure d\'arrêt doit être postérieure à l\'heure de début.');
            return false;
        }

        return true;
    });

    if (missionSelect.value) {
        missionSelect.dispatchEvent(new Event('change'));
    } else if (vehicleSelect.value) {
        vehicleSelect.dispatchEvent(new Event('change'));
    }

});
</script>
@endsection
