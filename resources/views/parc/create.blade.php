@extends('layouts.app')

@section('title', 'Ajouter un Véhicule - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Ajouter un Nouveau Véhicule</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du Véhicule</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="immatriculation" class="form-label">Immatriculation</label>
                                <input type="text" class="form-control" id="immatriculation" required placeholder="Ex: AB-123-CD">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="marque" class="form-label">Marque</label>
                                <input type="text" class="form-control" id="marque" required placeholder="Ex: Toyota">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modele" class="form-label">Modèle</label>
                                <input type="text" class="form-control" id="modele" required placeholder="Ex: Camry">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="annee" class="form-label">Année</label>
                                <input type="number" class="form-control" id="annee" required placeholder="Ex: 2020" min="1900" max="2025">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" required>
                                    <option value="voiture">Voiture</option>
                                    <option value="camion">Camion</option>
                                    <option value="moto">Moto</option>
                                    <option value="utilitaire">Utilitaire</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="etat" class="form-label">État</label>
                                <select class="form-select" id="etat" required>
                                    <option value="neuf">Neuf</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="mauvais">Mauvais</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kilometrage" class="form-label">Kilométrage</label>
                                <input type="number" class="form-control" id="kilometrage" placeholder="Ex: 45000" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_achat" class="form-label">Date d'Achat</label>
                                <input type="date" class="form-control" id="date_achat">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="prix_achat" class="form-label">Prix d'Achat (FCFA)</label>
                            <input type="number" class="form-control" id="prix_achat" placeholder="Ex: 15000000" step="0.01">
                        </div>

                        <div class="mb-3">
                            <label for="service_assigne" class="form-label">Service Assigné</label>
                            <select class="form-select" id="service_assigne">
                                <option value="">Non assigné</option>
                                <option value="Logistique">Logistique</option>
                                <option value="Entretien">Entretien</option>
                                <option value="Comptabilité">Comptabilité</option>
                                <option value="RH">RH</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (optionnel)</label>
                            <textarea class="form-control" id="description" rows="3" placeholder="Détails supplémentaires sur le véhicule..."></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('fleet.index') }}" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Ajouter le Véhicule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
