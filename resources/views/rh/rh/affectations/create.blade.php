@extends('layouts.app')

@section('title', 'Créer une Affectation - RH')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-user-plus text-success me-2"></i>
                Créer une Nouvelle Affectation
            </h4>
            <small class="text-muted">Affecter un agent à un service ou département.</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('rh.affectations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Retour aux affectations
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-tag me-2"></i>
                        Détails de l'Affectation
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('rh.affectations.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Agent <span class="text-danger">*</span></label>
                                <select class="form-select" name="agent_id" required>
                                    <option value="">Sélectionner un agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Service/Département <span class="text-danger">*</span></label>
                                <select class="form-select" name="service_id" required>
                                    <option value="">Sélectionner un service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date d'affectation</label>
                                <input type="date" class="form-control" name="date_affectation" value="{{ date('Y-m-d') }}">
                                @error('date_affectation')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Statut</label>
                                <select class="form-select" name="statut">
                                    <option value="actif" selected>Actif</option>
                                    <option value="temporaire">Temporaire</option>
                                    <option value="en_attente">En attente</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motif/Commentaires</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Raison de l'affectation, observations..."></textarea>
                            @error('notes')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rh.affectations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>
                                Créer l'Affectation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Services disponibles :</h6>
                    <ul class="list-unstyled small mb-3">
                        @foreach($services as $service)
                            <li><strong>{{ $service->name }}</strong></li>
                        @endforeach
                    </ul>

                    <hr>

                    <p class="small text-muted">
                        Une affectation lie un agent à un service spécifique.
                        Cela permet de suivre les responsabilités et d'organiser le travail.
                    </p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Rappels
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning small">
                        <i class="fas fa-lightbulb me-2"></i>
                        Assurez-vous que l'agent n'est pas déjà affecté à ce service pour éviter les doublons.
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-calendar me-2"></i>
                        La date d'affectation par défaut est aujourd'hui, mais peut être modifiée.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
