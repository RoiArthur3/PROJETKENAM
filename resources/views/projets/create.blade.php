@extends('layouts.app')

@section('title', 'Créer un Projet | KENAM SERVICES')

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
                        <i class="fas fa-plus-circle me-2"></i>Créer un Nouveau Projet
                    </h6>
                    <span class="badge bg-secondary">Brouillon</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projets.store') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- ====== SECTION 1: Informations Générales ====== -->
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-file-alt me-2"></i>Informations Générales
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="nom" class="form-label">
                                    <i class="fas fa-heading text-primary"></i> Nom du Projet <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" name="nom" placeholder="Ex: Transport NESTLE Abidjan-Korhogo" 
                                       value="{{ old('nom') }}" required>
                                <small class="form-text text-muted">Nom unique du projet</small>
                                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">
                                    <i class="fas fa-tag text-primary"></i> Type de Projet <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">-- Sélectionner un type --</option>
                                    <option value="Transport" {{ old('type') === 'Transport' ? 'selected' : '' }}>Transport</option>
                                    <option value="Logistique" {{ old('type') === 'Logistique' ? 'selected' : '' }}>Logistique</option>
                                    <option value="Distribution" {{ old('type') === 'Distribution' ? 'selected' : '' }}>Distribution</option>
                                    <option value="Entrepôt" {{ old('type') === 'Entrepôt' ? 'selected' : '' }}>Entrepôt</option>
                                    <option value="Autre" {{ old('type') === 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                <small class="form-text text-muted">Catégorie du projet</small>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-4">
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
                                <small class="form-text text-muted">Sélectionner le client bénéficiaire</small>
                                @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="responsable_id" class="form-label">
                                    <i class="fas fa-user-tie text-primary"></i> Responsable du Projet <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('responsable_id') is-invalid @enderror" 
                                        id="responsable_id" name="responsable_id" required>
                                    <option value="">-- Sélectionner un responsable --</option>
                                    <option value="{{ Auth::id() }}" selected>{{ Auth::user()->name }}</option>
                                </select>
                                <small class="form-text text-muted">Chef de projet</small>
                                @error('responsable_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left text-primary"></i> Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Détails et contexte du projet...">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Description détaillée du projet</small>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ====== SECTION 2: Affectations ====== -->
                        <h5 class="text-primary mb-3 mt-4">
                            <i class="fas fa-tasks me-2"></i>Affectations
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label">
                                    <i class="fas fa-calendar-alt text-primary"></i> Date de Début <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_debut') is-invalid @enderror" 
                                       id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required>
                                <small class="form-text text-muted">Date de démarrage</small>
                                @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date_fin_prevue" class="form-label">
                                    <i class="fas fa-calendar-check text-primary"></i> Date Fin Prévue <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_fin_prevue') is-invalid @enderror" 
                                       id="date_fin_prevue" name="date_fin_prevue" value="{{ old('date_fin_prevue') }}" required>
                                <small class="form-text text-muted">Date de fin prévue</small>
                                @error('date_fin_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            L'affectation détaillée des ressources (véhicules, chauffeurs, matériel) se fera après la création du projet.
                        </div>

                        <!-- ====== SECTION 3: Budgétisation ====== -->
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-calculator me-2"></i>Budgétisation
                        </h5>
                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="budget_estime" class="form-label">
                                    <i class="fas fa-money-bill-wave text-primary"></i> Budget Estimé (FCFA) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">FCFA</span>
                                    <input type="number" class="form-control @error('budget_estime') is-invalid @enderror" 
                                           id="budget_estime" name="budget_estime" placeholder="0" 
                                           value="{{ old('budget_estime') }}" required step="100" min="0">
                                </div>
                                <small class="form-text text-muted">Budget total estimé du projet</small>
                                @error('budget_estime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="budget_realise" class="form-label">
                                    <i class="fas fa-receipt text-primary"></i> Budget Réalisé (FCFA)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">FCFA</span>
                                    <input type="number" class="form-control @error('budget_realise') is-invalid @enderror" 
                                           id="budget_realise" name="budget_realise" placeholder="0" 
                                           value="{{ old('budget_realise', '0') }}" step="100" min="0" readonly>
                                </div>
                                <small class="form-text text-muted">Sera mis à jour au fur et à mesure</small>
                                @error('budget_realise')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
</script>
@endsection
