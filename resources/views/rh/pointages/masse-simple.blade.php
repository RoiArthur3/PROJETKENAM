@extends('layouts.app')

@section('title', 'RH - Pointage en Masse | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users-clock me-2"></i>
                        Pointage en Masse
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('rh.pointages.mass.store') }}">
                        @csrf

                        <!-- Sélection de la date -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="date" class="form-label fw-bold">
                                    Date des pointages <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control"
                                       id="date"
                                       name="date"
                                       value="{{ date('Y-m-d') }}"
                                       required>
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-end h-100">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success" onclick="setAllPresent()">
                                            <i class="fas fa-check me-1"></i>Tous Présents
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="setAllAbsent()">
                                            <i class="fas fa-times me-1"></i>Tous Absents
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="setAllRetard()">
                                            <i class="fas fa-exclamation me-1"></i>Tous en Retard
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des agents -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAll" onchange="toggleAll()">
                                        </th>
                                        <th>Agent</th>
                                        <th>Service</th>
                                        <th>Statut</th>
                                        <th>Heure Arrivée</th>
                                        <th>Heure Départ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($agents) && $agents->count() > 0)
                                        @foreach($agents as $agent)
                                        <tr>
                                            <td>
                                                <input type="checkbox"
                                                       class="form-check-input agent-checkbox"
                                                       value="{{ $agent->id }}"
                                                       name="selected_agents[]"
                                                       onchange="toggleAgentRow({{ $agent->id }})">
                                            </td>
                                            <td>
                                                <strong>{{ $agent->name }}</strong><br>
                                                <small class="text-muted">{{ $agent->email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $agent->service->nom ?? 'Non défini' }}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm"
                                                        name="pointages[{{ $agent->id }}][statut]"
                                                        id="statut_{{ $agent->id }}">
                                                    <option value="present">Présent</option>
                                                    <option value="absent">Absent</option>
                                                    <option value="retard">Retard</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="time"
                                                       class="form-control form-control-sm"
                                                       name="pointages[{{ $agent->id }}][heure_arrivee]"
                                                       value="08:00">
                                            </td>
                                            <td>
                                                <input type="time"
                                                       class="form-control form-control-sm"
                                                       name="pointages[{{ $agent->id }}][heure_depart]"
                                                       value="17:00">
                                            </td>
                                            <input type="hidden" name="pointages[{{ $agent->id }}][user_id]" value="{{ $agent->id }}">
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <p class="text-muted">Aucun agent trouvé</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Instructions :</strong> Sélectionnez les agents, définissez leur statut et heures, puis cliquez sur "Enregistrer".
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                        <i class="fas fa-redo me-1"></i>Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-success" style="background-color: #28a745 !important; border-color: #28a745 !important;">
                                        <i class="fas fa-save me-1"></i>Enregistrer les Pointages
                                    </button>
                                </div>
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
function toggleAll() {
    const selectAll = document.getElementById('selectAll');
    document.querySelectorAll('.agent-checkbox').forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function setAllPresent() {
    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        select.value = 'present';
    });
}

function setAllAbsent() {
    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        select.value = 'absent';
    });
}

function setAllRetard() {
    document.querySelectorAll('select[name*="[statut]"]').forEach(select => {
        select.value = 'retard';
    });
}

function resetForm() {
    if (confirm('Réinitialiser tous les pointages ?')) {
        document.querySelector('form').reset();
        document.getElementById('selectAll').checked = false;
        document.querySelectorAll('.agent-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
}
</script>
@endpush
