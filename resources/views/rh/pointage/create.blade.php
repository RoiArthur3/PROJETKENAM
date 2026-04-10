@extends('layouts.app')

@section('title', 'Nouveau Pointage - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clock text-primary"></i>
            Enregistrer un Pointage
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('rh.pointages.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Liste des Pointages
            </a>
            <a href="{{ route('rh.pointages.mass') }}" class="btn btn-outline-success">
                <i class="fas fa-users me-2"></i>Pointage en Masse
            </a>
        </div>
    </div>

    <!-- Formulaire de pointage -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user-clock me-2"></i>Informations du Pointage
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rh.pointages.store') }}" id="pointageForm">
                        @csrf

                        <!-- Sélection du personnel -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="personnel_id" class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i>Personnel RH *
                                </label>
                                <select class="form-select" id="personnel_id" name="personnel_id" required>
                                    <option value="">Sélectionner un personnel...</option>
                                    @if(isset($personnels))
                                        @foreach($personnels as $personnel)
                                            <option value="{{ $personnel->id }}">
                                                {{ $personnel->matricule }} - {{ $personnel->nom }} {{ $personnel->prenoms }} ({{ $personnel->poste }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Aucun personnel disponible</option>
                                    @endif
                                </select>
                                @if(!isset($personnels) || $personnels->isEmpty())
                                    <div class="alert alert-warning mt-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Aucun personnel RH disponible.
                                        <a href="{{ route('rh.personnel.create') }}" class="alert-link">Créez d'abord un personnel RH</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Informations temporelles -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="date_pointage" class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Date *
                                </label>
                                <input type="date" class="form-control" id="date_pointage" name="date_pointage" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="heure_arrivee" class="form-label fw-bold">
                                    <i class="fas fa-sign-in-alt me-1"></i>Heure d'Arrivée
                                </label>
                                <input type="time" class="form-control" id="heure_arrivee" name="heure_arrivee" value="08:00">
                            </div>
                            <div class="col-md-4">
                                <label for="heure_depart" class="form-label fw-bold">
                                    <i class="fas fa-sign-out-alt me-1"></i>Heure de Départ
                                </label>
                                <input type="time" class="form-control" id="heure_depart" name="heure_depart" value="17:00">
                            </div>
                        </div>

                        <!-- Statut et motif -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="statut" class="form-label fw-bold">
                                    <i class="fas fa-info-circle me-1"></i>Statut *
                                </label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="present">✅ Présent</option>
                                    <option value="retard">⏰ En Retard</option>
                                    <option value="absent">❌ Absent</option>
                                    <option value="half_day">🕐 Demi-journée</option>
                                    <option value="mission">✈️ En Mission</option>
                                    <option value="conge">🏖️ En Congé</option>
                                    <option value="maladie">🤒 Maladie</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="type_pointage" class="form-label fw-bold">
                                    <i class="fas fa-tag me-1"></i>Type de Pointage
                                </label>
                                <select class="form-select" id="type_pointage" name="type_pointage">
                                    <option value="normal">Normal</option>
                                    <option value="weekend">Weekend</option>
                                    <option value="holiday">Jour Férié</option>
                                    <option value="overtime">Heures Supplémentaires</option>
                                </select>
                            </div>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="motif" class="form-label fw-bold">
                                    <i class="fas fa-comment me-1"></i>Motif / Notes
                                </label>
                                <textarea class="form-control" id="motif" name="motif" rows="3"
                                    placeholder="Précisez le motif si absent/retard, ou ajoutez des notes supplémentaires..."></textarea>
                            </div>
                        </div>

                        <!-- Calcul automatique -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-calculator me-2"></i>Résumé du Pointage
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h5 text-primary mb-0" id="totalHours">--:--</div>
                                                    <small class="text-muted">Total Heures</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h5 text-success mb-0" id="workedHours">--:--</div>
                                                    <small class="text-muted">Heures Travaillées</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h5 text-warning mb-0" id="overtimeHours">--:--</div>
                                                    <small class="text-muted">Heures Sup.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="h5 text-info mb-0" id="lateMinutes">--</div>
                                                    <small class="text-muted">Retard (min)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-outline-info" onclick="calculateSummary()">
                                    <i class="fas fa-calculator me-1"></i>Calculer
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i>Réinitialiser
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('rh.pointages.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Enregistrer le Pointage
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcul automatique quand les heures changent
    const heureArrivee = document.getElementById('heure_arrivee');
    const heureDepart = document.getElementById('heure_depart');

    if (heureArrivee && heureDepart) {
        heureArrivee.addEventListener('change', calculateSummary);
        heureDepart.addEventListener('change', calculateSummary);
    }

    // Calcul initial au chargement
    calculateSummary();
});

function calculateSummary() {
    const arrivee = document.getElementById('heure_arrivee').value;
    const depart = document.getElementById('heure_depart').value;

    if (!arrivee || !depart) {
        resetSummary();
        return;
    }

    // Convertir en minutes
    const arriveeMinutes = timeToMinutes(arrivee);
    const departMinutes = timeToMinutes(depart);

    if (departMinutes <= arriveeMinutes) {
        resetSummary();
        return;
    }

    // Calcul du total
    const totalMinutes = departMinutes - arriveeMinutes;
    const totalHours = Math.floor(totalMinutes / 60);
    const totalMinutesRemainder = totalMinutes % 60;

    // Standard: 8h = 480 minutes
    const standardWorkMinutes = 480;
    let workedMinutes = totalMinutes;
    let overtimeMinutes = 0;

    if (totalMinutes > standardWorkMinutes) {
        workedMinutes = standardWorkMinutes;
        overtimeMinutes = totalMinutes - standardWorkMinutes;
    }

    // Calcul du retard (si arrivée après 08:00)
    const standardArrivee = timeToMinutes('08:00');
    const lateMinutes = Math.max(0, arriveeMinutes - standardArrivee);

    // Affichage
    document.getElementById('totalHours').textContent =
        `${String(totalHours).padStart(2, '0')}:${String(totalMinutesRemainder).padStart(2, '0')}`;

    const workedHours = Math.floor(workedMinutes / 60);
    const workedMinutesRemainder = workedMinutes % 60;
    document.getElementById('workedHours').textContent =
        `${String(workedHours).padStart(2, '0')}:${String(workedMinutesRemainder).padStart(2, '0')}`;

    const overtimeHours = Math.floor(overtimeMinutes / 60);
    const overtimeMinutesRemainder = overtimeMinutes % 60;
    document.getElementById('overtimeHours').textContent =
        overtimeMinutes > 0 ? `${String(overtimeHours).padStart(2, '0')}:${String(overtimeMinutesRemainder).padStart(2, '0')}` : '00:00';

    document.getElementById('lateMinutes').textContent =
        lateMinutes > 0 ? `${lateMinutes}` : '0';
}

function timeToMinutes(time) {
    const [hours, minutes] = time.split(':').map(Number);
    return hours * 60 + minutes;
}

function resetSummary() {
    document.getElementById('totalHours').textContent = '--:--';
    document.getElementById('workedHours').textContent = '--:--';
    document.getElementById('overtimeHours').textContent = '--:--';
    document.getElementById('lateMinutes').textContent = '--';
}

function resetForm() {
    if (confirm('Voulez-vous vraiment réinitialiser le formulaire ?')) {
        document.getElementById('pointageForm').reset();
        document.getElementById('date_pointage').value = new Date().toISOString().split('T')[0];
        calculateSummary();
    }
}
</script>
@endpush
