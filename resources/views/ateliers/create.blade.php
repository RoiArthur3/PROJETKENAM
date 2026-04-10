@extends('layouts.app')

@section('title', 'Créer une Intervention - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Créer une Nouvelle Intervention</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Détails de l'Intervention</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom de l'Intervention</label>
                                <input type="text" class="form-control" id="nom" required placeholder="Ex: Maintenance Préventive">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" required>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="reparation">Réparation</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="3" required placeholder="Détaillez l'intervention..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label">Véhicule (optionnel)</label>
                                <select class="form-select" id="vehicle_id">
                                    <option value="">Sélectionner un véhicule</option>
                                    <!-- Liste des véhicules -->
                                    <option value="1">Toyota Camry (AB-123-CD)</option>
                                    <option value="2">Renault Truck (XY-456-ZW)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_debut" class="form-label">Date de Début</label>
                                <input type="date" class="form-control" id="date_debut" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cout_estime" class="form-label">Coût Estimé (FCFA)</label>
                                <input type="number" class="form-control" id="cout_estime" placeholder="Ex: 50000" min="0" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_fin" class="form-label">Date de Fin Prévue</label>
                                <input type="date" class="form-control" id="date_fin" placeholder="Ex: 2023-10-15">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" rows="2" placeholder="Informations supplémentaires..."></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('ateliers.index') }}" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Créer l'Intervention</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
