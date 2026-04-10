@extends('layouts.app')

@section('title', 'Gestion des Ateliers - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Gestion des Ateliers</h1>
            <a href="{{ route('ateliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Intervention
            </a>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="row mb-4">
        <div class="col-md-4">
            <select class="form-select" id="statutFilter">
                <option value="">Tous les Statuts</option>
                <option value="planifie">Planifié</option>
                <option value="en_cours">En Cours</option>
                <option value="termine">Terminé</option>
                <option value="annule">Annulé</option>
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="typeFilter">
                <option value="">Tous les Types</option>
                <option value="maintenance">Maintenance</option>
                <option value="reparation">Réparation</option>
                <option value="inspection">Inspection</option>
                <option value="autre">Autre</option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-outline-success" onclick="filterAteliers()">
                <i class="fas fa-search"></i> Filtrer
            </button>
        </div>
    </div>

    <!-- Cartes Résumé -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-tools fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Total Interventions</h5>
                        <h2>15</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-clock fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">En Cours</h5>
                        <h2>5</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-check-circle fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Terminées</h5>
                        <h2>8</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-money-bill-wave fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Coût Total</h5>
                        <h2>750 000 FCFA</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes Ateliers -->
    <div class="row" id="atelierCards">
        <div class="col-md-4 mb-4">
            <div class="card shadow hover-effect">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title">Maintenance Préventive</h6>
                            <p class="card-text text-muted">Toyota Camry (AB-123-CD)</p>
                            <p class="card-text">Type: Maintenance</p>
                            <p class="card-text">Coût Estimé: 50 000 FCFA</p>
                        </div>
                        <span class="badge bg-success">Terminé</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-info me-2">Voir</button>
                        <button class="btn btn-sm btn-warning me-2">Modifier</button>
                        <button class="btn btn-sm btn-danger">Annuler</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow hover-effect">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title">Réparation Moteur</h6>
                            <p class="card-text text-muted">Renault Truck (XY-456-ZW)</p>
                            <p class="card-text">Type: Réparation</p>
                            <p class="card-text">Coût Estimé: 150 000 FCFA</p>
                        </div>
                        <span class="badge bg-warning">En Cours</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-info me-2">Voir</button>
                        <button class="btn btn-sm btn-warning me-2">Modifier</button>
                        <button class="btn btn-sm btn-success">Terminer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau Traditionnel -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Liste Détaillée</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Véhicule</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Date Début</th>
                                    <th>Coût Estimé</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="atelierTable">
                                <tr>
                                    <td>Maintenance Préventive</td>
                                    <td>Toyota Camry (AB-123-CD)</td>
                                    <td>Maintenance</td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                    <td>2023-10-01</td>
                                    <td>50 000 FCFA</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">Voir</button>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-danger">Annuler</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Réparation Moteur</td>
                                    <td>Renault Truck (XY-456-ZW)</td>
                                    <td>Réparation</td>
                                    <td><span class="badge bg-warning">En Cours</span></td>
                                    <td>2023-10-10</td>
                                    <td>150 000 FCFA</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">Voir</button>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-success">Terminer</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-effect {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-effect:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>

<script>
function filterAteliers() {
    const statut = document.getElementById('statutFilter').value;
    const type = document.getElementById('typeFilter').value;
    console.log('Filtrage:', statut, type);
}
</script>
@endsection
