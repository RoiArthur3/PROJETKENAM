@extends('layouts.app')

@section('title', 'Ouvrir un Projet | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-exclamation-circle me-2"></i>Erreurs de Validation
                    </h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-folder-plus me-2"></i>Ouvrir un Nouveau Projet
                    </h6>
                    <span class="badge bg-info">Période Limitée</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projets.store') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- ====== SECTION 1: Intitulé du Projet ====== -->
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-file-alt me-2"></i>Intitulé du Projet
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="nom" class="form-label">
                                    <i class="fas fa-heading text-primary"></i> Intitulé du Projet <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" name="nom" placeholder="Ex: Transport NESTLE Abidjan-Korhogo"
                                       value="{{ old('nom') }}" required>
                                <small class="form-text text-muted">Nom unique et descriptif du projet</small>
                                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="type" class="form-label">
                                    <i class="fas fa-tag text-primary"></i> Type de Projet <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">-- Sélectionner un type --</option>
                                    <option value="transport" {{ old('type') === 'transport' ? 'selected' : '' }}>Transport</option>
                                    <option value="logistique" {{ old('type') === 'logistique' ? 'selected' : '' }}>Logistique</option>
                                    <option value="distribution" {{ old('type') === 'distribution' ? 'selected' : '' }}>Distribution</option>
                                    <option value="entrepot" {{ old('type') === 'entrepot' ? 'selected' : '' }}>Entrepôt</option>
                                    <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                <small class="form-text text-muted">Catégorie du projet</small>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="client_id" class="form-label">
                                    <i class="fas fa-building text-primary"></i> Client <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('client_id') is-invalid @enderror"
                                        id="client_id" name="client_id" required>
                                    <option value="">-- Sélectionner un client --</option>
                                    @forelse(($clients ?? []) as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->nom ?? $client->raison_sociale ?? $client->id }}
                                        </option>
                                    @empty
                                        <option value="">Aucun client disponible</option>
                                    @endforelse
                                </select>
                                <small class="form-text text-muted">Client bénéficiaire du projet</small>
                                @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left text-primary"></i> Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Description détaillée du projet...">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Détails et contexte du projet</small>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ====== SECTION 2: Période du Projet ====== -->
                        <h5 class="text-primary mb-3 mt-4">
                            <i class="fas fa-calendar-alt me-2"></i>Période du Projet
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label">
                                    <i class="fas fa-calendar text-primary"></i> Date de Début <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_debut') is-invalid @enderror"
                                       id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required>
                                <small class="form-text text-muted">Date de démarrage du projet</small>
                                @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date_fin" class="form-label">
                                    <i class="fas fa-calendar-check text-primary"></i> Date de Fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_fin') is-invalid @enderror"
                                       id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required>
                                <small class="form-text text-muted">Date de fin prévue (peut être prolongée)</small>
                                @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Important :</strong> Le projet est limité dans le temps. La période peut être prolongée en modifiant les dates du projet.
                        </div>

                        <!-- ====== SECTION 3: Association Véhicules/Engins ====== -->
                        <h5 class="text-primary mb-3 mt-4">
                            <i class="fas fa-truck me-2"></i>Association Véhicules/Engins
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">
                                    <i class="fas fa-cogs text-primary"></i> Sélectionner les Véhicules/Engins
                                </label>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="vehiculesTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">
                                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                                </th>
                                                <th>Immatriculation</th>
                                                <th>Type</th>
                                                <th>Marque/Modèle</th>
                                                <th>Statut</th>
                                                <th>Disponibilité</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($vehicules) && $vehicules->count() > 0)
                                                @foreach($vehicules as $vehicule)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="vehicules[]" value="{{ $vehicule->id }}"
                                                               class="form-check-input vehicule-checkbox">
                                                    </td>
                                                    <td>{{ $vehicule->immatriculation ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $vehicule->type_materiel ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>{{ $vehicule->marque }} {{ $vehicule->modele }}</td>
                                                    <td>
                                                        @if($vehicule->disponible)
                                                            <span class="badge bg-success">Disponible</span>
                                                        @else
                                                            <span class="badge bg-warning">En mission</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($vehicule->disponible)
                                                            <span class="text-success">Libre</span>
                                                        @else
                                                            <span class="text-warning">Occupé</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">Aucun véhicule/engin disponible</p>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <small class="form-text text-muted">Sélectionnez les véhicules/engins à associer à ce projet</small>
                            </div>
                        </div>

                        <!-- ====== SECTION 4: Responsable ====== -->
                        <h5 class="text-primary mb-3 mt-4">
                            <i class="fas fa-user-tie me-2"></i>Responsable du Projet
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="responsable_id" class="form-label">
                                    <i class="fas fa-user text-primary"></i> Responsable du Projet <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('responsable_id') is-invalid @enderror"
                                        id="responsable_id" name="responsable_id" required>
                                    <option value="">-- Sélectionner un responsable --</option>
                                    @if(isset($users))
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('responsable_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->role ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Aucun utilisateur disponible</option>
                                    @endif
                                </select>
                                <small class="form-text text-muted">Responsable du projet (choisi parmi le personnel RH)</small>
                                @error('responsable_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- ====== SECTION 5: Budgétisation ====== -->
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-calculator me-2"></i>Budgétisation
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="cout_estimatif" class="form-label">
                                    <i class="fas fa-money-bill-wave text-primary"></i> Coût Estimatif (FCFA)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">FCFA</span>
                                    <input type="number" class="form-control @error('cout_estimatif') is-invalid @enderror"
                                           id="cout_estimatif" name="cout_estimatif" placeholder="0"
                                           value="{{ old('cout_estimatif') }}" step="100" min="0">
                                </div>
                                <small class="form-text text-muted">Coût estimatif (non obligatoire)</small>
                                @error('cout_estimatif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="montant_facturer" class="form-label">
                                    <i class="fas fa-receipt text-primary"></i> Montant à Facturer (FCFA) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">FCFA</span>
                                    <input type="number" class="form-control @error('montant_facturer') is-invalid @enderror"
                                           id="montant_facturer" name="montant_facturer" placeholder="0"
                                           value="{{ old('montant_facturer') }}" required step="100" min="0">
                                </div>
                                <small class="form-text text-muted">Montant qui sera facturé au client</small>
                                @error('montant_facturer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- ====== SECTION 4: Planification ====== -->
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-chart-gantt me-2"></i>Planification
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="notes" class="form-label">
                                    <i class="fas fa-sticky-note text-primary"></i> Notes & Remarques
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="3"
                                          placeholder="Remarques, conditions spéciales...">{{ old('notes') }}</textarea>
                                <small class="form-text text-muted">Notes additionnelles sur le projet</small>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary mb-3">
                                            <i class="fas fa-info-circle me-2"></i>Status Initial
                                        </h6>
                                        <p class="mb-2">
                                            <span class="badge bg-secondary">Brouillon</span>
                                            <span class="ms-2 text-muted small">Initial</span>
                                        </p>
                                        <p class="small text-muted mb-0">
                                            Le projet démarrera en mode brouillon. Vous pourrez le valider et le lancer après création.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ====== ACTIONS ====== -->
                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-success flex-grow-1">
                                <i class="fas fa-save me-2"></i>Créer le Projet
                            </button>
                            <a href="{{ route('projets.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info mt-4 mb-0" role="alert">
                <h6 class="alert-heading">
                    <i class="fas fa-lightbulb me-2"></i>Conseils
                </h6>
                <ul class="mb-0">
                    <li>Remplissez tous les champs obligatoires (*)</li>
                    <li>La date de fin doit être après la date de début</li>
                    <li>Vous pourrez modifier le projet après sa création</li>
                    <li>Les ressources seront affectées après la validation du projet</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .form-text {
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    h5 {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .input-group-text {
        background-color: #e9ecef;
        border: 1px solid #dee2e6;
    }
</style>

<script>
    // Form validation
    (function() {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Gestion de la sélection des véhicules
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const vehiculeCheckboxes = document.querySelectorAll('.vehicule-checkbox');

        // Sélectionner/désélectionner tous les véhicules
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                vehiculeCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }

        // Mettre à jour le compteur de sélection
        vehiculeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
                updateSelectAllState();
            });
        });

        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.vehicule-checkbox:checked').length;
            const totalCount = vehiculeCheckboxes.length;

            if (selectedCount > 0) {
                const countBadge = document.createElement('span');
                countBadge.className = 'badge bg-primary ms-2';
                countBadge.textContent = `${selectedCount} sélectionné(s)`;
                countBadge.id = 'selectedCount';

                // Supprimer l'ancien badge s'il existe
                const oldBadge = document.getElementById('selectedCount');
                if (oldBadge) oldBadge.remove();

                // Ajouter le nouveau badge
                const label = document.querySelector('label[for="vehiculesTable"]');
                if (label) label.appendChild(countBadge);
            } else {
                const badge = document.getElementById('selectedCount');
                if (badge) badge.remove();
            }
        }

        function updateSelectAllState() {
            if (selectAllCheckbox) {
                const checkedCount = document.querySelectorAll('.vehicule-checkbox:checked').length;
                const totalCount = vehiculeCheckboxes.length;

                if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === totalCount) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }
        }

        // Validation des dates
        const dateDebut = document.getElementById('date_debut');
        const dateFin = document.getElementById('date_fin');

        function validateDates() {
            if (dateDebut.value && dateFin.value) {
                const debut = new Date(dateDebut.value);
                const fin = new Date(dateFin.value);

                if (fin < debut) {
                    dateFin.setCustomValidity('La date de fin doit être postérieure à la date de début');
                } else {
                    dateFin.setCustomValidity('');
                }
            }
        }

        if (dateDebut && dateFin) {
            dateDebut.addEventListener('change', validateDates);
            dateFin.addEventListener('change', validateDates);
        }

        // Animation des sections
        const sections = document.querySelectorAll('h5.text-primary');
        sections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                section.style.transition = 'all 0.5s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endsection
