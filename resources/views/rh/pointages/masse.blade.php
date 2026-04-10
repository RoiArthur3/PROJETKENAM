@extends('layouts.app')

@section('content')
<x-dashboard-layout title="Pointages en Masse" icon="fas fa-users-clock">
    <x-slot name="kpis">
        <x-kpi-card title="Agents Actifs" value="{{ $personnels->count() }}" icon="fas fa-user-check" color="success" />
        <x-kpi-card title="Date Sélectionnée" value="{{ date('d/m/Y') }}" icon="fas fa-calendar-day" color="primary" />
        <x-kpi-card title="Pointages à Traiter" value="{{ $personnels->count() }}" icon="fas fa-clock" color="warning" />
        <x-kpi-card title="Traitement Rapide" value="Batch" icon="fas fa-tachometer-alt" color="info" />
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users-clock me-2"></i>
                        Saisie des Pointages en Masse
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rh.pointages.mass.store') }}" id="massPointageForm">
                        @csrf

                        <!-- Sélection de la date -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="date" class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>
                                    Date des pointages <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control @error('date') is-invalid @enderror"
                                       id="date"
                                       name="date"
                                       value="{{ old('date', date('Y-m-d')) }}"
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-end h-100">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <small>
                                            Sélectionnez la date puis remplissez les pointages pour tous les employés actifs.
                                            Les champs vides seront ignorés lors de la sauvegarde.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions rapides -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-success" onclick="setAllPresent()">
                                        <i class="fas fa-check-circle me-1"></i>Tous Présents
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="setAllAbsent()">
                                        <i class="fas fa-times-circle me-1"></i>Tous Absents
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" onclick="setAllRetard()">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Tous en Retard
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearAll()">
                                        <i class="fas fa-eraser me-1"></i>Effacer Tout
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des pointages -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" onchange="toggleAll()">
                                        </th>
                                        <th>Agent</th>
                                        <th>Service</th>
                                        <th>Statut</th>
                                        <th>Heure d'Arrivée</th>
                                        <th>Heure de Départ</th>
                                        <th>Observations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($personnels as $index => $personnel)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   class="form-check-input agent-checkbox"
                                                   value="{{ $personnel->id }}"
                                                   onchange="toggleAgentRow({{ $personnel->id }})">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($personnel->nom ?? '', 0, 1) . substr($personnel->prenoms ?? '', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $personnel->nom }} {{ $personnel->prenoms }}</strong><br>
                                                    <small class="text-muted">{{ $personnel->poste ?? 'Poste non défini' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $personnel->departement ?? 'Non défini' }}
                                            </span>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm"
                                                    name="pointages[{{ $personnel->id }}][statut]"
                                                    id="statut_{{ $personnel->id }}"
                                                    onchange="updateRowStyle({{ $personnel->id }})">
                                                <option value="present">Présent</option>
                                                <option value="absent">Absent</option>
                                                <option value="retard">Retard</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="time"
                                                   class="form-control form-control-sm"
                                                   name="pointages[{{ $personnel->id }}][heure_arrivee]"
                                                   id="arrivee_{{ $personnel->id }}"
                                                   placeholder="08:00">
                                        </td>
                                        <td>
                                            <input type="time"
                                                   class="form-control form-control-sm"
                                                   name="pointages[{{ $personnel->id }}][heure_depart]"
                                                   id="depart_{{ $personnel->id }}"
                                                   placeholder="17:00">
                                        </td>
                                        <td>
                                            <input type="text"
                                                   class="form-control form-control-sm"
                                                   name="pointages[{{ $personnel->id }}][observations]"
                                                   placeholder="Observations">
                                        </td>
                                        <input type="hidden" name="pointages[{{ $personnel->id }}][personnel_id]" value="{{ $personnel->id }}">
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Résumé et actions -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="alert alert-light">
                                    <h6><i class="fas fa-chart-bar me-2"></i>Résumé</h6>
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div class="text-success">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                                <div class="fw-bold" id="countPresent">0</div>
                                                <small>Présents</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="text-danger">
                                                <i class="fas fa-times-circle fa-2x"></i>
                                                <div class="fw-bold" id="countAbsent">0</div>
                                                <small>Absents</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="text-warning">
                                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                                <div class="fw-bold" id="countRetard">0</div>
                                                <small>Retards</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="text-secondary">
                                                <i class="fas fa-question-circle fa-2x"></i>
                                                <div class="fw-bold" id="countTotal">{{ $personnels->count() }}</div>
                                                <small>Total</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end align-items-center h-100">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                            <i class="fas fa-redo me-1"></i>Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-success" style="background-color: #28a745 !important; border-color: #28a745 !important;">
                                            <i class="fas fa-save me-1"></i>
                                            Enregistrer les Pointages
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
@endsection

@push('scripts')
<script>
// Fonctions pour les actions rapides
function setAllPresent() {
    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        select.value = 'present';
        updateRowStyle(select.id.replace('statut_', ''));
    });
    updateSummary();
}

function setAllAbsent() {
    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        select.value = 'absent';
        updateRowStyle(select.id.replace('statut_', ''));
    });
    updateSummary();
}

function setAllRetard() {
    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        select.value = 'retard';
        updateRowStyle(select.id.replace('statut_', ''));
    });
    updateSummary();
}

function clearAll() {
    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        select.value = 'present';
    });
    document.querySelectorAll('input[type="time"]').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('input[name*="[observations]"]').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('tr').forEach(row => {
        row.classList.remove('table-success', 'table-danger', 'table-warning');
    });
    updateSummary();
}

function toggleAll() {
    const selectAll = document.getElementById('selectAll');
    document.querySelectorAll('.agent-checkbox').forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        toggleAgentRow(checkbox.value);
    });
}

function toggleAgentRow(agentId) {
    const checkbox = document.querySelector(`input[value="${agentId}"]`);
    const row = checkbox.closest('tr');

    if (checkbox.checked) {
        row.style.opacity = '1';
        row.querySelectorAll('input, select').forEach(input => {
            input.disabled = false;
        });
    } else {
        row.style.opacity = '0.5';
        row.querySelectorAll('input, select').forEach(input => {
            if (input.type !== 'checkbox') {
                input.disabled = true;
            }
        });
    }
}

function updateRowStyle(agentId) {
    const statut = document.getElementById(`statut_${agentId}`).value;
    const row = document.getElementById(`statut_${agentId}`).closest('tr');

    row.classList.remove('table-success', 'table-danger', 'table-warning');

    switch(statut) {
        case 'present':
            row.classList.add('table-success');
            break;
        case 'absent':
            row.classList.add('table-danger');
            break;
        case 'retard':
            row.classList.add('table-warning');
            break;
    }

    updateSummary();
}

function updateSummary() {
    let present = 0, absent = 0, retard = 0;

    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        if (!select.disabled) {
            switch(select.value) {
                case 'present': present++; break;
                case 'absent': absent++; break;
                case 'retard': retard++; break;
            }
        }
    });

    document.getElementById('countPresent').textContent = present;
    document.getElementById('countAbsent').textContent = absent;
    document.getElementById('countRetard').textContent = retard;
}

function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les pointages ?')) {
        document.getElementById('massPointageForm').reset();
        document.querySelectorAll('tr').forEach(row => {
            row.classList.remove('table-success', 'table-danger', 'table-warning');
        });
        updateSummary();
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();

    // Écouter les changements de statut
    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        select.addEventListener('change', function() {
            updateRowStyle(this.id.replace('statut_', ''));
        });
    });
});
</script>
@endpush
