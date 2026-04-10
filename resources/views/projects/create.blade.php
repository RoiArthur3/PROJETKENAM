@extends('layouts.app')

@section('title', 'Créer un Nouveau Projet - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus-circle me-2"></i>Créer un Nouveau Projet
                    </h6>
                    <span class="badge bg-secondary">Brouillon</span>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Erreur!</strong><br>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="projectForm" method="POST" action="{{ route('projets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Section: Informations Générales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Informations Générales
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label required">Nom du Projet <span style="color:red">*</span></label>
                                <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" value="{{ old('nom') }}" placeholder="Ex: Transport Marchandises" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Donnez un nom explicite à votre projet</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label required">Type de Projet <span style="color:red">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" id="type" required>
                                    <option value="">-- Sélectionner un type --</option>
                                    @foreach($types as $typeValue)
                                        <option value="{{ $typeValue }}" {{ old('type') == $typeValue ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $typeValue)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Catégorie du projet (Transport, Location, Chantier, etc.)</div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label required">Description <span style="color:red">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                          id="description" rows="4" placeholder="Décrivez les objectifs, le contexte et les spécificités du projet" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Fournissez les détails importants pour exécuter le projet</div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">Notes Supplémentaires</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" rows="2" placeholder="Ajoutez des remarques, constraints ou informations additionnelles">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section: Affectations et Responsabilités -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-users me-2"></i>Affectations et Responsabilités
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label">Client</label>
                                <select class="form-select @error('client_id') is-invalid @enderror" name="client_id" id="client_id">
                                    <option value="">-- Sélectionner un client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Client pour lequel le projet est réalisé (optionnel)</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="responsable_id" class="form-label">Responsable du Projet</label>
                                <select class="form-select @error('responsable_id') is-invalid @enderror" name="responsable_id" id="responsable_id">
                                    <option value="">-- Assigner un responsable --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('responsable_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->nom ?? $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('responsable_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Personne responsable de la conduite du projet</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section: Budgétisation -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-coins me-2"></i>Budgétisation et Budget
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="budget_estime" class="form-label">Budget Estimé (en millions FCFA)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" name="budget_estime" class="form-control @error('budget_estime') is-invalid @enderror"
                                           id="budget_estime" value="{{ old('budget_estime') }}" min="0" step="0.01" placeholder="Ex: 50">
                                </div>
                                @error('budget_estime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Budget prévisionnel en millions FCFA</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section: Planification -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>Planification
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_debut" class="form-label required">Date de Début <span style="color:red">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                    <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror"
                                           id="date_debut" value="{{ old('date_debut') }}" required>
                                </div>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Date de démarrage effectif du projet</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_fin_prevue" class="form-label required">Date Fin Prévue <span style="color:red">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                    <input type="date" name="date_fin_prevue" class="form-control @error('date_fin_prevue') is-invalid @enderror"
                                           id="date_fin_prevue" value="{{ old('date_fin_prevue') }}" required>
                                </div>
                                @error('date_fin_prevue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Date prévue de fin du projet</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Boutons d'Action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-between">
                                    <a href="{{ route('projets.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check me-2"></i>Créer le Projet
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide et Information -->
            <div class="card border-left-info mt-4">
                <div class="card-body">
                    <h6 class="text-info mb-2">
                        <i class="fas fa-lightbulb me-2"></i>Information
                    </h6>
                    <p class="text-muted mb-0 small">
                        Le projet sera créé en statut <strong>"Brouillon"</strong>. Vous pourrez le modifier, ajouter des ressources, 
                        et des opérations. Une fois prêt, vous pourrez valider le projet pour commencer son exécution.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-label.required:after {
    content: " *";
    color: #dc3545;
}
</style>
@endsection
