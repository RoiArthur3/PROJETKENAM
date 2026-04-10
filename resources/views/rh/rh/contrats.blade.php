@extends('layouts.app')

@section('title', 'RH - Contrats | KENAM SERVICES')

@section('content')
@php
    $colors = ['#4e73df','#1cc88a','#f6c23e','#36b9cc','#e74a3b','#858796','#5a5c69','#fd7e14'];
@endphp
<x-dashboard-layout title="Gestion des Contrats" icon="fa-file-contract" subtitle="Contrats du personnel RH">
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Contrats Actifs"
            :value="$total ?? 0"
            icon="fa-file-signature"
            color="primary"
            subtitle="Personnel RH"
        />
        <x-kpi-card
            title="CDI"
            :value="$cdi ?? 0"
            icon="fa-infinity"
            color="success"
            subtitle="Contrats à durée indéterminée"
        />
        <x-kpi-card
            title="CDD"
            :value="$cdd ?? 0"
            icon="fa-clock"
            color="warning"
            subtitle="Contrats à durée déterminée"
        />
        <x-kpi-card
            title="Autres Contrats"
            :value="($total ?? 0) - ($cdi ?? 0) - ($cdd ?? 0)"
            icon="fa-file-alt"
            color="info"
            subtitle="Stage, Intérim, etc."
        />
    </x-slot>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtres de Recherche
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('rh.contrats.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Personnel RH</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="agent" class="form-control" placeholder="Nom, prénoms..." value="{{ request('agent') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Type de contrat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-file-contract text-muted"></i>
                                </span>
                                <select name="type" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="CDI" {{ request('type') === 'CDI' ? 'selected' : '' }}>CDI</option>
                                    <option value="CDD" {{ request('type') === 'CDD' ? 'selected' : '' }}>CDD</option>
                                    <option value="STAGE" {{ request('type') === 'STAGE' ? 'selected' : '' }}>Stage</option>
                                    <option value="INTERIM" {{ request('type') === 'INTERIM' ? 'selected' : '' }}>Intérim</option>
                                    <option value="CONSULTANT" {{ request('type') === 'CONSULTANT' ? 'selected' : '' }}>Consultant</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
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
                    <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#createContratModal">
                        <i class="fas fa-file-contract"></i> Associer un Contrat
                    </button>
                    <a href="{{ route('personnel.create') }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-user-plus"></i> Nouveau Personnel
                    </a>
                    <small class="text-muted d-block">
                        Associez un contrat à un personnel existant ou créez un nouveau personnel
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des contrats -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Personnel RH</th>
                            <th>Type de Contrat</th>
                            <th>Poste</th>
                            <th>Date Début</th>
                            <th>Date Fin</th>
                            <th>Salaire Base</th>
                            <th>Salaire Horaire</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contrats as $contrat)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($contrat->nom ?? 'NA', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $contrat->nom }} {{ $contrat->prenoms }}</div>
                                            <small class="text-muted">{{ $contrat->poste ?? '—' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($contrat->type_contrat)
                                        <span class="badge bg-{{ $contrat->type_contrat === 'CDI' ? 'success' : ($contrat->type_contrat === 'CDD' ? 'warning' : 'info') }}">
                                            {{ $contrat->type_contrat }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $contrat->poste ?? '—' }}</td>
                                <td>
                                    @if($contrat->date_embauche)
                                        {{ \Carbon\Carbon::parse($contrat->date_embauche)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($contrat->date_fin_contrat)
                                        {{ \Carbon\Carbon::parse($contrat->date_fin_contrat)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($contrat->salaire_base)
                                        {{ number_format($contrat->salaire_base, 0, ',', ' ') }} {{ $contrat->devise ?? 'FCFA' }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($contrat->salaire_horaire)
                                        <span class="badge bg-info">
                                            {{ number_format($contrat->salaire_horaire, 2, ',', ' ') }} {{ $contrat->devise ?? 'FCFA' }}/h
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">Actif</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('personnel.show', $contrat->id) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('personnel.edit', $contrat->id) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-info" title="Télécharger contrat" onclick="downloadContract({{ $contrat->id }})">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucun contrat trouvé</div>
                                    <p class="text-muted small">Aucun personnel RH n'a de contrat enregistré</p>
                                    <a href="{{ route('personnel.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-user-plus"></i> Créer un Personnel RH
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<!-- Modal d'association de contrat -->
<div class="modal fade" id="createContratModal" tabindex="-1" aria-labelledby="createContratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createContratModalLabel">
                    <i class="fas fa-file-contract me-2"></i>Associer un Contrat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="POST" id="contratForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important :</strong> Sélectionnez un personnel RH existant puis définissez les termes de son contrat.
                    </div>

                    <!-- Sélection du personnel -->
                    <div class="row g-3 mb-4">
                        <h6 class="text-primary fw-bold col-12">
                            <i class="fas fa-user me-1"></i>Sélection du Personnel RH
                        </h6>
                        <div class="col-md-12">
                            <label class="form-label">Personnel RH *</label>
                            <select name="personnel_id" class="form-select" required id="personnelSelect">
                                <option value="">Sélectionner un personnel...</option>
                                @if(isset($personnelsList))
                                    @foreach($personnelsList as $personnel)
                                        <option value="{{ $personnel->id }}">
                                            {{ $personnel->matricule }} - {{ $personnel->nom }} {{ $personnel->prenoms }} ({{ $personnel->poste }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Aucun personnel disponible</option>
                                @endif
                            </select>
                            @if(!isset($personnelsList) || $personnelsList->isEmpty())
                                <div class="alert alert-warning mt-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Aucun personnel RH disponible.
                                    <a href="{{ route('personnel.create') }}" class="alert-link">Créez d'abord un personnel RH</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informations du contrat -->
                    <div class="row g-3">
                        <h6 class="text-success fw-bold col-12">
                            <i class="fas fa-file-contract me-1"></i>Détails du Contrat
                        </h6>
                        <div class="col-md-3">
                            <label class="form-label">Type de contrat *</label>
                            <select name="type_contrat" class="form-select" required id="typeContratSelect">
                                <option value="">Sélectionner...</option>
                                <option value="CDI">CDI</option>
                                <option value="CDD">CDD</option>
                                <option value="STAGE">Stage</option>
                                <option value="INTERIM">Intérim</option>
                                <option value="CONSULTANT">Consultant</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date d'embauche *</label>
                            <input type="date" name="date_embauche" class="form-control" required>
                        </div>
                        <div class="col-md-3" id="dateFinContratField" style="display: none;">
                            <label class="form-label">Date de fin *</label>
                            <input type="date" name="date_fin_contrat" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Période d'essai (jours) *</label>
                            <input type="number" name="duree_essai_jours" class="form-control" value="30" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salaire de base *</label>
                            <input type="number" name="salaire_base" class="form-control" required id="salaireBase">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salaire horaire *</label>
                            <input type="number" name="salaire_horaire" class="form-control" required id="salaireHoraire" step="0.01">
                            <small class="text-muted">Calculé automatiquement ou saisissez manuellement</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Devise *</label>
                            <select name="devise" class="form-select" required>
                                <option value="XOF">XOF (FCFA)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="USD">USD ($)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Heures par mois</label>
                            <input type="number" name="heures_par_mois" class="form-control" value="173.33" readonly>
                            <small class="text-muted">Base de calcul (40h/semaine)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fréquence de paiement *</label>
                            <select name="frequence_paiement" class="form-select" required>
                                <option value="MENSUEL">Mensuel</option>
                                <option value="HEBDOMADAIRE">Hebdomadaire</option>
                                <option value="QUINZOMADAIRE">Quinzomadaire</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Service *</label>
                            <input type="text" name="service" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Catégorie *</label>
                            <select name="categorie" class="form-select" required>
                                <option value="">Sélectionner...</option>
                                <option value="A">Catégorie A</option>
                                <option value="B">Catégorie B</option>
                                <option value="C">Catégorie C</option>
                                <option value="D">Catégorie D</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Associer le Contrat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Gestion du champ date de fin selon le type de contrat
document.addEventListener('DOMContentLoaded', function() {
    const typeContratSelect = document.querySelector('select[name="type_contrat"]');
    const dateFinField = document.getElementById('dateFinContratField');

    if (typeContratSelect && dateFinField) {
        // Cacher le champ par défaut
        dateFinField.style.display = 'none';

        typeContratSelect.addEventListener('change', function() {
            if (this.value === 'CDD') {
                dateFinField.style.display = 'block';
                dateFinField.querySelector('input').setAttribute('required', 'required');
            } else {
                dateFinField.style.display = 'none';
                dateFinField.querySelector('input').removeAttribute('required');
                dateFinField.querySelector('input').value = '';
            }
        });
    }

    // Calcul automatique du salaire horaire
    const salaireBaseInput = document.getElementById('salaireBase');
    const salaireHoraireInput = document.getElementById('salaireHoraire');
    const frequencePaiementSelect = document.querySelector('select[name="frequence_paiement"]');

    function calculerSalaireHoraire() {
        const salaireBase = parseFloat(salaireBaseInput.value) || 0;
        const frequence = frequencePaiementSelect.value;
        let heuresParMois = 173.33; // Base: 40h/semaine

        // Ajuster selon la fréquence de paiement
        if (frequence === 'HEBDOMADAIRE') {
            heuresParMois = 40; // 40 heures par semaine
        } else if (frequence === 'QUINZOMADAIRE') {
            heuresParMois = 80; // 2 semaines
        }

        const salaireHoraire = salaireBase / heuresParMois;
        salaireHoraireInput.value = salaireHoraire.toFixed(2);

        // Mettre à jour le champ heures_par_mois
        const heuresParMoisInput = document.querySelector('input[name="heures_par_mois"]');
        if (heuresParMoisInput) {
            heuresParMoisInput.value = heuresParMois;
        }
    }

    if (salaireBaseInput && salaireHoraireInput) {
        salaireBaseInput.addEventListener('input', calculerSalaireHoraire);
        frequencePaiementSelect.addEventListener('change', calculerSalaireHoraire);

        // Permettre aussi la saisie manuelle du salaire horaire
        salaireHoraireInput.addEventListener('input', function() {
            // Si l'utilisateur saisit manuellement, ne plus recalculer automatiquement
            salaireBaseInput.removeEventListener('input', calculerSalaireHoraire);
        });
    }

    // Gérer la soumission du formulaire pour mettre à jour l'URL dynamiquement
    const contratForm = document.getElementById('contratForm');
    const personnelSelect = document.getElementById('personnelSelect');

    if (contratForm && personnelSelect) {
        contratForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const personnelId = personnelSelect.value;
            if (!personnelId) {
                alert('Veuillez sélectionner un personnel RH');
                return;
            }

            // Construire l'URL manuellement pour éviter les erreurs de route
            const baseUrl = window.location.origin + '/rh/personnel/' + personnelId;
            contratForm.setAttribute('action', baseUrl);

            // Soumettre le formulaire
            this.submit();
        });
    }
});

function downloadContract(personnelId) {
    try {
        // Construire l'URL manuellement pour éviter les erreurs de route
        const baseUrl = window.location.origin + '/rh/personnel';
        const url = `${baseUrl}/${personnelId}`;

        // Ouvrir dans un nouvel onglet
        window.open(url, '_blank');
    } catch (error) {
        console.error('Erreur lors de l\'ouverture du contrat:', error);
        // En cas d'erreur, rediriger vers la page du personnel
        window.location.href = `/rh/personnel/${personnelId}`;
    }
}
</script>
@endpush
@endsection
