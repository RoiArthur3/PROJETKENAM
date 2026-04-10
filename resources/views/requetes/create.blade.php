@extends('layouts.app')

@section('title', 'Nouvelle Requête - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2 text-primary"></i>Nouvelle Requête
            </h1>
            <p class="text-muted mb-0">Créer une nouvelle demande</p>
        </div>
        <div>
            <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('requetes.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="titre" class="form-label">Titre de la requête *</label>
                        <input type="text" name="titre" id="titre" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="service" class="form-label">Service concerné *</label>
                        <select name="service" id="service" class="form-select" required>
                            <option value="">Sélectionner un service</option>
                            <option value="rh">Ressources Humaines</option>
                            <option value="comptabilite">Comptabilité</option>
                            <option value="informatique">Informatique</option>
                            <option value="logistique">Logistique</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="priorite" class="form-label">Priorité *</label>
                        <select name="priorite" id="priorite" class="form-select" required>
                            <option value="">Sélectionner une priorité</option>
                            <option value="basse">Basse</option>
                            <option value="normale">Normale</option>
                            <option value="haute">Haute</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description détaillée *</label>
                        <textarea name="description" id="description" class="form-control" rows="4" required placeholder="Décrivez votre demande en détail..."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer la requête
                        </button>
                        <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
