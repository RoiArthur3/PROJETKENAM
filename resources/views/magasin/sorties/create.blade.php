@extends('layouts.app')

@section('title', 'Demande de Sortie - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouvelle Demande de Sortie</h1>
        <div>
            <a href="{{ route('magasin.sorties.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour aux sorties
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-arrow-up me-2"></i>Formulaire de Demande
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('magasin.sorties.store') }}">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Référence -->
                            <div class="col-md-6">
                                <label for="reference" class="form-label">Référence *</label>
                                <input type="text" class="form-control" id="reference" name="reference" 
                                       placeholder="Ex: SOR-2026-001" required>
                                <div class="form-text">Référence unique de la sortie</div>
                            </div>

                            <!-- Date de sortie -->
                            <div class="col-md-6">
                                <label for="date_sortie" class="form-label">Date de Sortie *</label>
                                <input type="date" class="form-control" id="date_sortie" name="date_sortie" 
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>

                            <!-- Produit -->
                            <div class="col-md-12">
                                <label for="produit" class="form-label">Produit *</label>
                                <select class="form-select" id="produit" name="produit" required>
                                    <option value="">Sélectionner un produit</option>
                                    <option value="1">Ordinateur Portable - LAP-001</option>
                                    <option value="2">Papier A4 - PAP-001</option>
                                    <option value="3">Cartouche d'encre - CAR-001</option>
                                </select>
                                <div class="form-text">Produit à sortir du stock</div>
                            </div>

                            <!-- Quantité -->
                            <div class="col-md-4">
                                <label for="quantite" class="form-label">Quantité *</label>
                                <input type="number" class="form-control" id="quantite" name="quantite" 
                                       min="1" step="1" required>
                                <div class="form-text">Quantité à sortir</div>
                            </div>

                            <!-- Service -->
                            <div class="col-md-4">
                                <label for="service" class="form-label">Service *</label>
                                <select class="form-select" id="service" name="service" required>
                                    <option value="">Sélectionner un service</option>
                                    <option value="Administration">Administration</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Opérations">Opérations</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Logistique">Logistique</option>
                                    <option value="RH">RH</option>
                                    <option value="Informatique">Informatique</option>
                                </select>
                            </div>

                            <!-- Demandeur -->
                            <div class="col-md-4">
                                <label for="demandeur" class="form-label">Demandeur *</label>
                                <input type="text" class="form-control" id="demandeur" name="demandeur" 
                                       placeholder="Nom du demandeur" required>
                            </div>

                            <!-- Motif -->
                            <div class="col-12">
                                <label for="motif" class="form-label">Motif de la Sortie *</label>
                                <textarea class="form-control" id="motif" name="motif" rows="3"
                                          placeholder="Explique la raison de cette sortie..." required></textarea>
                            </div>

                            <!-- Urgence -->
                            <div class="col-md-6">
                                <label for="urgence" class="form-label">Urgence</label>
                                <select class="form-select" id="urgence" name="urgence">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="critique">Critique</option>
                                </select>
                            </div>

                            <!-- Destination -->
                            <div class="col-md-6">
                                <label for="destination" class="form-label">Destination</label>
                                <input type="text" class="form-control" id="destination" name="destination" 
                                       placeholder="Lieu de destination">
                                <div class="form-text">Où le produit sera utilisé</div>
                            </div>

                            <!-- Boutons -->
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('magasin.sorties.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-paper-plane me-2"></i>Envoyer la Demande
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informations supplémentaires -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Conseils
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Vérifiez la disponibilité du produit avant de demander
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Indiquez un motif clair et précis
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Choisissez le bon niveau d'urgence
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Spécifiez la destination du produit
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Stock Actuel
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-info">{{ \App\Models\Stock::count() ?? 0 }}</h4>
                        <p class="text-muted mb-0">Produits en stock</p>
                    </div>
                </div>
            </div>

            <!-- Alertes de stock -->
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alertes de Stock
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-danger">1</h4>
                        <p class="text-muted mb-0">Produits en rupture</p>
                        <small class="text-muted">Cartouche d'encre</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
