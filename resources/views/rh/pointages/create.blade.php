@extends('layouts.app')
@section('title', 'Pointage Individuel - KENAM SERVICES')
@section('content')
@php
    $colors = ['#4e73df','#1cc88a','#f6c23e','#36b9cc','#e74a3b','#858796','#5a5c69','#fd7e14'];
@endphp
<x-dashboard-layout title="Pointage Individuel" icon="fa-user-check" subtitle="Enregistrer un pointage pour le personnel RH">
    <!-- Formulaire de pointage -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('rh.pointages.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="personnel_id" class="form-label fw-bold">
                                    Personnel RH <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users-cog"></i></span>
                                    <select name="personnel_id" id="personnel_id" class="form-select" required>
                                        <option value="">-- Sélectionner un personnel RH --</option>
                                        @if(isset($personnels))
                                            @foreach($personnels as $personnel)
                                                <option value="{{ $personnel->id }}" {{ old('personnel_id') == $personnel->id ? 'selected' : '' }}>
                                                    {{ $personnel->nom }} {{ $personnel->prenoms }} - {{ $personnel->poste }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @error('personnel_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_pointage" class="form-label fw-bold">
                                    Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date"
                                           name="date_pointage"
                                           id="date_pointage"
                                           class="form-control"
                                           value="{{ old('date_pointage', date('Y-m-d')) }}"
                                           required>
                                </div>
                                @error('date_pointage')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="heure_arrivee" class="form-label fw-bold">
                                    Heure d'arrivée <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="time"
                                           name="heure_arrivee"
                                           id="heure_arrivee"
                                           class="form-control"
                                           value="{{ old('heure_arrivee', '08:00') }}"
                                           required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="setCurrentTime('heure_arrivee')" title="Heure actuelle">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                                @error('heure_arrivee')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="heure_depart" class="form-label fw-bold">
                                    Heure de départ
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="time"
                                           name="heure_depart"
                                           id="heure_depart"
                                           class="form-control"
                                           value="{{ old('heure_depart', '17:00') }}">
                                    <button type="button" class="btn btn-outline-secondary" onclick="setCurrentTime('heure_depart')" title="Heure actuelle">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                                @error('heure_depart')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="statut" class="form-label fw-bold">
                                Statut <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                <select name="statut" id="statut" class="form-select" required>
                                    <option value="present" {{ old('statut', 'present') == 'present' ? 'selected' : '' }}>
                                        <i class="fas fa-check-circle text-success"></i> Présent
                                    </option>
                                    <option value="absent" {{ old('statut') == 'absent' ? 'selected' : '' }}>
                                        <i class="fas fa-times-circle text-danger"></i> Absent
                                    </option>
                                    <option value="retard" {{ old('statut') == 'retard' ? 'selected' : '' }}>
                                        <i class="fas fa-exclamation-triangle text-warning"></i> Retard
                                    </option>
                                    <option value="conge" {{ old('statut') == 'conge' ? 'selected' : '' }}>
                                        <i class="fas fa-calendar text-info"></i> Congé
                                    </option>
                                    <option value="maladie" {{ old('statut') == 'maladie' ? 'selected' : '' }}>
                                        <i class="fas fa-heartbeat text-danger"></i> Maladie
                                    </option>
                                </select>
                            </div>
                            @error('statut')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-bold">Observations</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                <textarea name="notes"
                                          id="notes"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Ajoutez des observations ou commentaires...">{{ old('notes') }}</textarea>
                            </div>
                            @error('notes')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Résumé du pointage -->
                        <div class="alert alert-light">
                            <h6><i class="fas fa-info-circle me-2"></i>Résumé du pointage</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Personnel RH:</strong> <span id="resume-personnel">Non sélectionné</span><br>
                                    <strong>Date:</strong> <span id="resume-date">{{ date('d/m/Y') }}</span><br>
                                    <strong>Statut:</strong> <span id="resume-statut">Présent</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Arrivée:</strong> <span id="resume-arrivee">08:00</span><br>
                                    <strong>Départ:</strong> <span id="resume-depart">17:00</span><br>
                                    <strong>Total:</strong> <span id="resume-total">9 heures</span><br>
                                    <strong>Retard:</strong> <span id="resume-retard">0 min</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('rh.pointages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer le Pointage
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
@push('scripts')
<script>
// Fonction pour calculer la durée
function calculateDuration() {
    const arrivee = document.getElementById('heure_arrivee').value;
    const depart = document.getElementById('heure_depart').value;

    if (arrivee) {
        const [arrivalHours, arrivalMinutes] = arrivee.split(':').map(Number);
        const delayReference = (7 * 60) + 30;
        const totalArrivalMinutes = (arrivalHours * 60) + arrivalMinutes;
        const delayMinutes = Math.max(0, totalArrivalMinutes - delayReference);
        document.getElementById('resume-retard').textContent = `${delayMinutes} min`;
    }

    if (arrivee && depart) {
        const [h1, m1] = arrivee.split(':').map(Number);
        const [h2, m2] = depart.split(':').map(Number);

        let totalMinutes = (h2 * 60 + m2) - (h1 * 60 + m1);

        if (totalMinutes < 0) {
            totalMinutes += 24 * 60; // Ajouter 24h si le départ est le lendemain
        }

        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;

        document.getElementById('resume-total').textContent =
            `${hours} heure${hours > 1 ? 's' : ''}${minutes > 0 ? ' ' + minutes + ' minute' + (minutes > 1 ? 's' : '') : ''}`;
    } else {
        document.getElementById('resume-total').textContent = '--';
    }
}

// Fonction pour définir l'heure actuelle
function setCurrentTime(fieldId) {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const timeString = hours + ':' + minutes;
    document.getElementById(fieldId).value = timeString;

    // Mettre à jour le résumé si c'est le champ d'arrivée ou de départ
    if (fieldId === 'heure_arrivee') {
        document.getElementById('resume-arrivee').textContent = timeString;
    } else if (fieldId === 'heure_depart') {
        document.getElementById('resume-depart').textContent = timeString;
    }

    calculateDuration();
}

// Mettre à jour le résumé en temps réel
document.getElementById('personnel_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    document.getElementById('resume-personnel').textContent = option.text || 'Non sélectionné';
});

document.getElementById('date_pointage').addEventListener('change', function() {
    if (this.value) {
        const date = new Date(this.value);
        const formatted = date.toLocaleDateString('fr-FR');
        document.getElementById('resume-date').textContent = formatted;
    }
});

document.getElementById('heure_arrivee').addEventListener('change', function() {
    document.getElementById('resume-arrivee').textContent = this.value || '--:--';
    calculateDuration();
});

document.getElementById('heure_depart').addEventListener('change', function() {
    document.getElementById('resume-depart').textContent = this.value || '--:--';
    calculateDuration();
});

document.getElementById('statut').addEventListener('change', function() {
    const statuts = {
        'present': 'Présent',
        'absent': 'Absent',
        'retard': 'Retard',
        'conge': 'Congé',
        'maladie': 'Maladie'
    };
    document.getElementById('resume-statut').textContent = statuts[this.value] || 'Non défini';
});

// Initialisation
calculateDuration();
</script>
@endpush
