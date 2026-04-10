@extends('layouts.app')

@section('title', 'Pointages - RH')

@section('content')
@php
    $colors = ['#4e73df','#1cc88a','#f6c23e','#36b9cc','#e74a3b','#858796','#5a5c69','#fd7e14'];
@endphp
<x-dashboard-layout title="Gestion des Pointages" icon="fa-clock" subtitle="Suivi des pointages du personnel RH">
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Présents Aujourd'hui"
            :value="$presents ?? 0"
            icon="fa-user-check"
            color="success"
            subtitle="Personnel présent"
        />
        <x-kpi-card
            title="En Retard"
            :value="$retards ?? 0"
            icon="fa-clock"
            color="warning"
            subtitle="Retards ce jour"
        />
        <x-kpi-card
            title="Absents"
            :value="$absents ?? 0"
            icon="fa-user-times"
            color="danger"
            subtitle="Absences ce jour"
        />
        <x-kpi-card
            title="Taux de Présence"
            :value="$tauxPresence ?? 0"
            icon="fa-percent"
            color="info"
            subtitle="% de présence"
        />
    </x-slot>

    <!-- Filtres et Actions -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtres de Recherche
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('rh.pointages.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar text-muted"></i>
                                </span>
                                <input type="date" class="form-control" id="dateFilter" name="date" value="{{ $date }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Recherche</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Nom du personnel..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" onclick="filterPointages()">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <button type="button" class="btn btn-success" onclick="toggleMassForm()">
                                    <i class="fas fa-users"></i> Pointage en Masse
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-plus me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('rh.pointages.create') }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-user"></i> Pointage Individuel
                    </a>
                    <small class="text-muted d-block">
                        Pointage individuel pour le personnel RH
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de pointage en masse (masqué par défaut) -->
    <div id="massForm" class="mb-4" style="display: none;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Pointage en Masse - {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('rh.pointages.mass.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <button type="button" class="btn btn-success btn-sm me-2" onclick="setAllArrivals()">
                                <i class="fas fa-clock me-1"></i>
                                Marquer Arrivées
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="setAllDepartures()">
                                <i class="fas fa-clock me-1"></i>
                                Marquer Départs
                            </button>
                        </div>
                        <small class="text-muted">Utilisez les boutons pour définir l'heure actuelle pour tous les employés</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Personnel RH</th>
                                    <th>Heure d'arrivée</th>
                                    <th>Heure de départ</th>
                                    <th>Statut</th>
                                    <th>Retard (min)</th>
                                    <th>Heures travaillées</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($personnels as $personnel)
                                    <tr>
                                        <td>
                                            <strong>{{ $personnel->nom }} {{ $personnel->prenoms }}</strong>
                                            <br><small class="text-muted">{{ $personnel->poste }}</small>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="time" class="form-control form-control-sm"
                                                       name="pointages[{{ $personnel->id }}][heure_arrivee]">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="setCurrentTime(this.previousElementSibling)"
                                                        title="Définir l'heure actuelle">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="time" class="form-control form-control-sm"
                                                       name="pointages[{{ $personnel->id }}][heure_depart]">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="setCurrentTime(this.previousElementSibling)"
                                                        title="Définir l'heure actuelle">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm"
                                                    name="pointages[{{ $personnel->id }}][statut]">
                                                <option value="present">Présent</option>
                                                <option value="absent">Absent</option>
                                                <option value="retard">Retard</option>
                                            </select>
                                        </td>
                                        <td>
                                            <span class="text-muted small">Calculé auto</span>
                                        </td>
                                        <td>
                                            <span class="text-muted small">Calculé auto</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="button" class="btn btn-secondary" onclick="toggleMassForm()">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Enregistrer les Pointages
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tableau des pointages -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Personnel RH</th>
                            <th>Heure d'arrivée</th>
                            <th>Heure de départ</th>
                            <th>Statut</th>
                            <th>Retard (min)</th>
                            <th>Heures travaillées</th>
                            <th>Validé</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pointages as $pointage)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $pointage->personnel ? $pointage->personnel->nom . ' ' . $pointage->personnel->prenoms : 'N/A' }}</div>
                                            @if($pointage->personnel)
                                                <small class="text-muted">{{ $pointage->personnel->poste }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($pointage->heure_arrivee)
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_arrivee)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($pointage->heure_depart)
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_depart)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $pointage->statut == 'present' ? 'success' : ($pointage->statut == 'retard' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($pointage->statut) }}
                                    </span>
                                </td>
                                <td>
                                    @if($pointage->delay_minutes > 0)
                                        <span class="text-danger fw-semibold">{{ $pointage->delay_minutes }} min</span>
                                    @else
                                        <span class="text-success">À l'heure</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pointage->worked_hours > 0)
                                        {{ number_format($pointage->worked_hours, 2) }}h
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($pointage->validated_at)
                                        <i class="fas fa-check text-success" title="Validé le {{ $pointage->validated_at->format('d/m/Y H:i') }}"></i>
                                    @else
                                        <i class="fas fa-clock text-warning" title="En attente"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary"
                                                onclick="editPointage({{ $pointage->id }})"
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger"
                                                onclick="deletePointage({{ $pointage->id }})"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucun pointage pour cette date</div>
                                    <button type="button" class="btn btn-primary mt-2" onclick="toggleMassForm()">
                                        <i class="fas fa-plus"></i> Commencer les pointages
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<script>
function filterPointages() {
    const date = document.getElementById('dateFilter').value;
    if (date) {
        window.location.href = `?date=${date}`;
    }
}

function toggleMassForm() {
    const form = document.getElementById('massForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function editPointage(id) {
    // Implémenter l'édition
    console.log('Edit pointage:', id);
}

function deletePointage(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce pointage ?')) {
        // Implémenter la suppression
        console.log('Delete pointage:', id);
    }
}

// Recherche en temps réel
document.getElementById('searchInput')?.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
});

// Fonction pour définir l'heure actuelle
function setCurrentTime(input) {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const timeString = hours + ':' + minutes;
    input.value = timeString;
}

// Fonctions pour marquer toutes les arrivées ou départs
function setAllArrivals() {
    const arrivalInputs = document.querySelectorAll('input[name*="[heure_arrivee]"]');
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const timeString = hours + ':' + minutes;

    arrivalInputs.forEach(input => {
        input.value = timeString;
    });
}

function setAllDepartures() {
    const departureInputs = document.querySelectorAll('input[name*="[heure_depart]"]');
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const timeString = hours + ':' + minutes;

    departureInputs.forEach(input => {
        input.value = timeString;
    });
}
</script>
@endsection
