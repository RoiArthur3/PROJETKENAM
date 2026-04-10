@extends('layouts.app')

@section('title', 'Nouvelle Requête - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2 text-primary"></i>Nouvelle Requête
            </h1>
            <p class="text-muted mb-0">Créer et envoyer une nouvelle demande à un service</p>
        </div>
        <div>
            <a href="/suivi-validation" class="btn btn-success">
                <i class="fas fa-eye me-1"></i>Mes Requêtes
            </a>
        </div>
    </div>

    <!-- Formulaire de création -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Formulaire de requête
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/requetes">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_source" class="form-label">Service source</label>
                                    <select class="form-select" id="service_source" name="service_source" required>
                                        <option value="">Sélectionner un service</option>
                                        <option value="RH">Ressources Humaines</option>
                                        <option value="Parc Auto">Parc Auto</option>
                                        <option value="Logistique">Logistique</option>
                                        <option value="Commercial">Commercial</option>
                                        <option value="Comptabilité">Comptabilité</option>
                                        <option value="Stock">Stock & Entrepôt</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_destination" class="form-label">Service destination</label>
                                    <select class="form-select" id="service_destination" name="service_destination" required>
                                        <option value="">Sélectionner un service</option>
                                        <option value="RH">Ressources Humaines</option>
                                        <option value="Parc Auto">Parc Auto</option>
                                        <option value="Logistique">Logistique</option>
                                        <option value="Commercial">Commercial</option>
                                        <option value="Comptabilité">Comptabilité</option>
                                        <option value="Stock">Stock & Entrepôt</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre de la requête</label>
                            <input type="text" class="form-control" id="titre" name="titre" required placeholder="Entrez un titre clair et concis">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Décrivez votre demande en détail..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priorite" class="form-label">Priorité</label>
                                    <select class="form-select" id="priorite" name="priorite" required>
                                        <option value="basse">Basse</option>
                                        <option value="normale" selected>Normale</option>
                                        <option value="haute">Haute</option>
                                        <option value="urgente">Urgente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type de requête</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="information">Demande d'information</option>
                                        <option value="validation">Validation requise</option>
                                        <option value="intervention">Intervention</option>
                                        <option value="approvisionnement">Approvisionnement</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fichier" class="form-label">Fichier joint (optionnel)</label>
                            <input type="file" class="form-control" id="fichier" name="fichier">
                            <div class="form-text">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX (Max 5MB)</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='/suivi-validation'">
                                <i class="fas fa-times me-2"></i>Annuler
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Envoyer la requête
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const titre = document.getElementById('titre').value.trim();
            const description = document.getElementById('description').value.trim();

            if (!titre || !description) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }

            // Confirmer l'envoi
            if (!confirm('Êtes-vous sûr de vouloir envoyer cette requête ?')) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endpush
