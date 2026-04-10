@extends('layouts.app')

@section('title', 'Gestion de la Prospection - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Gestion de la Prospection</h1>
            <a href="{{ route('prospection.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Prospect
            </a>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="row mb-4">
        <div class="col-md-4">
            <select class="form-select" id="statutFilter">
                <option value="">Tous les Statuts</option>
                <option value="nouveau">Nouveau</option>
                <option value="en_cours">En Cours</option>
                <option value="qualifie">Qualifié</option>
                <option value="converti">Converti</option>
                <option value="perdu">Perdu</option>
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="sourceFilter">
                <option value="">Toutes les Sources</option>
                <option value="reseaux_sociaux">Réseaux Sociaux</option>
                <option value="referencement">Référencement</option>
                <option value="bouche_oreille">Bouche à Oreille</option>
                <option value="publicite">Publicité</option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-outline-success" onclick="filterProspects()">
                <i class="fas fa-search"></i> Filtrer
            </button>
        </div>
    </div>

    <!-- Cartes Résumé -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-users fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Total Prospects</h5>
                        <h2>25</h2>
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
                        <h2>10</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-check-circle fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Convertis</h5>
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
                        <h5 class="card-title">Valeur Totale</h5>
                        <h2>2.5M FCFA</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes Prospects -->
    <div class="row" id="prospectCards">
        <div class="col-md-4 mb-4">
            <div class="card shadow hover-effect">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title">Entreprise XYZ</h6>
                            <p class="card-text text-muted">XYZ Logistics</p>
                            <p class="card-text"><i class="fas fa-envelope me-2"></i>contact@xyz.ci</p>
                            <p class="card-text"><i class="fas fa-phone me-2"></i>+225 01 02 03 04 09</p>
                        </div>
                        <span class="badge bg-warning">En Cours</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-info me-2">Voir</button>
                        <button class="btn btn-sm btn-warning me-2">Modifier</button>
                        <button class="btn btn-sm btn-success">Convertir</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow hover-effect">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title">Jean Dupont</h6>
                            <p class="card-text text-muted">Individuel</p>
                            <p class="card-text"><i class="fas fa-envelope me-2"></i>jean.dupont@email.com</p>
                            <p class="card-text"><i class="fas fa-phone me-2"></i>+225 01 02 03 04 10</p>
                        </div>
                        <span class="badge bg-success">Converti</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-info me-2">Voir</button>
                        <button class="btn btn-sm btn-warning me-2">Modifier</button>
                        <button class="btn btn-sm btn-danger">Archiver</button>
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
                                    <th>Entreprise</th>
                                    <th>Source</th>
                                    <th>Valeur Potentielle</th>
                                    <th>Statut</th>
                                    <th>Date Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="prospectTable">
                                <tr>
                                    <td>Entreprise XYZ</td>
                                    <td>XYZ Logistics</td>
                                    <td>Réseaux Sociaux</td>
                                    <td>500 000 FCFA</td>
                                    <td><span class="badge bg-warning">En Cours</span></td>
                                    <td>2023-10-10</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">Voir</button>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-success">Convertir</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Jean Dupont</td>
                                    <td>Individuel</td>
                                    <td>Bouche à Oreille</td>
                                    <td>100 000 FCFA</td>
                                    <td><span class="badge bg-success">Converti</span></td>
                                    <td>2023-10-05</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">Voir</button>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-danger">Archiver</button>
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
function filterProspects() {
    const statut = document.getElementById('statutFilter').value;
    const source = document.getElementById('sourceFilter').value;
    console.log('Filtrage:', statut, source);
}
</script>
@endsection
