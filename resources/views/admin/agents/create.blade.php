@extends('layouts.app')

@section('title', 'Créer un Agent - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus me-2 text-primary"></i>Créer un Agent
            </h1>
            <p class="text-muted mb-0">Ajouter un nouvel agent lié à un service</p>
        </div>
        <div>
            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-plus me-2"></i>Informations de l'Agent
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('parc.vehicules.admin.agents.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Matricule *</label>
                            <input type="text" name="matricule" class="form-control"
                                   value="{{ old('matricule') }}" required maxlength="20"
                                   placeholder="Ex: AGT001">
                            <div class="form-text">Identifiant unique de l'agent</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email') }}" required>
                            <div class="form-text">Adresse email professionnelle</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control"
                                   value="{{ old('nom') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control"
                                   value="{{ old('prenom') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control"
                                   value="{{ old('telephone') }}" placeholder="Ex: +225 XX XX XX XX">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Service *</label>
                            <select name="service_id" class="form-control" required>
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Mot de passe *</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                            <div class="form-text">Minimum 8 caractères</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Confirmer mot de passe *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input"
                                       id="is_active" {{ old('is_active') ? 'checked' : '' }} value="1">
                                <label class="form-check-label" for="is_active">
                                    Agent actif
                                </label>
                            </div>
                            <div class="form-text">Cocher si l'agent peut se connecter</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Créer l'Agent
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
