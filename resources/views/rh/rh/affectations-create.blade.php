@extends('layouts.app')

@section('title', 'Nouvelle Affectation - RH | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle mr-2 text-primary"></i>Nouvelle affectation de service
            </h1>
            <p class="text-muted">Affecter un agent à un service / département</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('rh.affectations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-list mr-2"></i>Informations de l'affectation
                    </h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('rh.affectations.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="agent_id" class="form-label">Agent <span class="text-danger">*</span></label>
                                <select class="form-control @error('agent_id') is-invalid @enderror" 
                                        id="agent_id" 
                                        name="agent_id" 
                                        required>
                                    <option value="">-- Sélectionner un agent --</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="service_id" class="form-label">Service / Département <span class="text-danger">*</span></label>
                                <select class="form-control @error('service_id') is-invalid @enderror" 
                                        id="service_id" 
                                        name="service_id" 
                                        required>
                                    <option value="">-- Sélectionner un service --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="mission" class="form-label">Rôle / Mission dans le service <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('mission') is-invalid @enderror" 
                                      id="mission" 
                                      name="mission" 
                                      rows="3" 
                                      required
                                      placeholder="Décrivez la mission ou le rôle de l'agent dans ce service">{{ old('mission') }}</textarea>
                            @error('mission')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('date_debut') is-invalid @enderror" 
                                       id="date_debut" 
                                       name="date_debut" 
                                       value="{{ old('date_debut', date('Y-m-d')) }}"
                                       required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_fin" class="form-label">Date de fin (optionnel)</label>
                                <input type="date" 
                                       class="form-control @error('date_fin') is-invalid @enderror" 
                                       id="date_fin" 
                                       name="date_fin" 
                                       value="{{ old('date_fin') }}">
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Laisser vide pour une affectation permanente</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('rh.affectations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Enregistrer l'affectation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle mr-2"></i>Aide
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">Instructions</h6>
                    <ul class="mb-3">
                        <li>Sélectionnez l'agent concerné</li>
                        <li>Choisissez le service ou département d'affectation</li>
                        <li>Décrivez la mission ou le rôle de l'agent</li>
                        <li>Définissez les dates de début et fin de l'affectation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
