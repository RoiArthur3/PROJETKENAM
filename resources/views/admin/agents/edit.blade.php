@extends('layouts.app')

@section('title', 'Modifier un Agent - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-edit me-2 text-primary"></i>Modifier un Agent
            </h1>
            <p class="text-muted mb-0">Modifier les informations de l'agent : {{ $agent->nom_complet }}</p>
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
                <i class="fas fa-user-edit me-2"></i>Modifier les Informations
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.agents.update', $agent->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Matricule *</label>
                            <input type="text" name="matricule" class="form-control"
                                   value="{{ old('matricule', $agent->matricule) }}" required maxlength="20"
                                   placeholder="Ex: AGT001">
                            <div class="form-text">Identifiant unique de l'agent</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $agent->email) }}" required>
                            <div class="form-text">Adresse email professionnelle</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control"
                                   value="{{ old('nom', $agent->nom) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control"
                                   value="{{ old('prenom', $agent->prenom) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control"
                                   value="{{ old('telephone', $agent->telephone) }}" placeholder="Ex: +225 XX XX XX XX">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Service *</label>
                            <select name="service_id" class="form-control" required>
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}"
                                            {{ old('service_id', $agent->service_id) == $service->id ? 'selected' : '' }}>
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
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control" minlength="8" placeholder="Laisser vide pour ne pas changer">
                            <div class="form-text">Minimum 8 caractères (laisser vide pour conserver l'actuel)</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Confirmer mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmer le nouveau mot de passe">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input"
                                       id="is_active" {{ old('is_active', $agent->is_active) ? 'checked' : '' }} value="1">
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
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Informations actuelles -->
    <div class="card shadow mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-info-circle me-2"></i>Informations Actuelles
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Créé le :</strong></td>
                            <td>{{ $agent->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dernière connexion :</strong></td>
                            <td>
                                @if($agent->last_login)
                                    {{ $agent->last_login->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Jamais</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Service actuel :</strong></td>
                            <td>
                                @if($agent->service)
                                    <span class="badge bg-info">{{ $agent->service->nom }}</span>
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Statut :</strong></td>
                            <td>
                                @if($agent->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-danger">Inactif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
