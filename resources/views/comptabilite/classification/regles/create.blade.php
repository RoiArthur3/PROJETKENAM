@extends('layouts.app')

@section('title', 'Nouvelle Règle de Classification')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus me-2 text-primary"></i>
                        Nouvelle Règle de Classification
                    </h1>
                    <p class="text-muted">Créer une règle automatique pour classifier les transactions</p>
                </div>
                <div>
                    <a href="{{ route('comptabilite.classification.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Définition de la Règle
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comptabilite.classification.regles.store') }}">
                        @csrf

                        <div class="row g-3">
                            <!-- Nom de la règle -->
                            <div class="col-md-6">
                                <label for="nom" class="form-label small">Nom de la règle</label>
                                <input type="text" class="form-control" id="nom" name="nom" required
                                       placeholder="Ex: Carburant véhicules">
                            </div>

                            <!-- Type de transaction -->
                            <div class="col-md-6">
                                <label for="type_transaction" class="form-label small">Type de transaction</label>
                                <select class="form-select" id="type_transaction" name="type_transaction" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="charge">Charge</option>
                                    <option value="depense">Dépense</option>
                                    <option value="produit">Produit</option>
                                    <option value="recette">Recette</option>
                                </select>
                            </div>

                            <!-- Condition -->
                            <div class="col-md-6">
                                <label for="condition" class="form-label small">Condition</label>
                                <select class="form-select" id="condition" name="condition" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="contient">Contient</option>
                                    <option value="commence_par">Commence par</option>
                                    <option value="finit_par">Finit par</option>
                                    <option value="egal">Égal à</option>
                                    <option value="montant_superieur">Montant supérieur à</option>
                                    <option value="montant_inferieur">Montant inférieur à</option>
                                </select>
                            </div>

                            <!-- Valeur de la condition -->
                            <div class="col-md-6">
                                <label for="valeur_condition" class="form-label small">Valeur de la condition</label>
                                <input type="text" class="form-control" id="valeur_condition" name="valeur_condition" required
                                       placeholder="Ex: CARBURANT, TOTAL, 50000">
                            </div>

                            <!-- Catégorie comptable -->
                            <div class="col-md-6">
                                <label for="categorie" class="form-label small">Catégorie comptable</label>
                                <select class="form-select" id="categorie" name="categorie" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="exploitation">Charges d'exploitation</option>
                                    <option value="financieres">Charges financières</option>
                                    <option value="exceptionnelles">Charges exceptionnelles</option>
                                    <option value="personnel">Charges de personnel</option>
                                    <option value="externes">Services externes</option>
                                </select>
                            </div>

                            <!-- Sous-catégorie -->
                            <div class="col-md-6">
                                <label for="sous_categorie" class="form-label small">Sous-catégorie (compte)</label>
                                <select class="form-select" id="sous_categorie" name="sous_categorie" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="601">601 - Achats matières premières</option>
                                    <option value="606">601 - Achats non stockés</option>
                                    <option value="613">613 - Locations</option>
                                    <option value="616">616 - Primes d'assurances</option>
                                    <option value="624">624 - Transports</option>
                                    <option value="625">625 - Déplacements</option>
                                    <option value="626">626 - Frais postaux</option>
                                    <option value="627">627 - Services bancaires</option>
                                </select>
                            </div>

                            <!-- Priorité -->
                            <div class="col-md-6">
                                <label for="priorite" class="form-label small">Priorité</label>
                                <select class="form-select" id="priorite" name="priorite" required>
                                    <option value="1">1 - Très haute</option>
                                    <option value="2">2 - Haute</option>
                                    <option value="3" selected>3 - Normale</option>
                                    <option value="4">4 - Basse</option>
                                    <option value="5">5 - Très basse</option>
                                </select>
                            </div>

                            <!-- Statut -->
                            <div class="col-md-6">
                                <label for="statut" class="form-label small">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="actif" selected>Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label small">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Description détaillée de la règle..."></textarea>
                            </div>

                            <!-- Boutons -->
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('comptabilite.classification.index') }}" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer la règle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Exemples -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="fas fa-lightbulb me-2"></i>Exemples de Règles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Exemple 1: Carburant</h6>
                            <ul class="small">
                                <li><strong>Nom:</strong> Carburant véhicules</li>
                                <li><strong>Type:</strong> Dépense</li>
                                <li><strong>Condition:</strong> Contient "CARBURANT" ou "TOTAL" ou "SHELL"</li>
                                <li><strong>Catégorie:</strong> Transports</li>
                                <li><strong>Sous-catégorie:</strong> 624</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Exemple 2: Loyer</h6>
                            <ul class="small">
                                <li><strong>Nom:</strong> Loyer mensuel</li>
                                <li><strong>Type:</strong> Charge</li>
                                <li><strong>Condition:</strong> Contient "LOYER"</li>
                                <li><strong>Catégorie:</strong> Locations</li>
                                <li><strong>Sous-catégorie:</strong> 613</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Logique JavaScript si nécessaire
});
</script>
@endsection
