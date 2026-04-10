@extends('layouts.app')

@section('title', 'Gestion des Dépenses - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Gestion des Dépenses</h1>
            <a href="{{ route('depenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Dépense
            </a>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="statutFilter">
                <option value="">Tous les Statuts</option>
                <option value="en_attente">En Attente</option>
                <option value="approuvee">Approuvée</option>
                <option value="payee">Payée</option>
                <option value="rejetee">Rejetée</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="serviceFilter">
                <option value="">Tous les Services</option>
                <option value="Logistique">Logistique</option>
                <option value="RH">RH</option>
                <option value="Maintenance">Maintenance</option>
                <option value="Commercial">Commercial</option>
            </select>
        </div>
        <div class="col-md-3">
            <input class="form-control" type="search" placeholder="Rechercher une dépense..." id="searchFilter">
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-success" onclick="filterExpenses()">
                <i class="fas fa-search"></i> Filtrer
            </button>
        </div>
    </div>

    <!-- Cartes Résumé -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-money-bill-wave fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Total Dépenses</h5>
                        <h2>2.5M FCFA</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-clock fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">En Attente</h5>
                        <h2>150 000 FCFA</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-check-circle fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Approuvées</h5>
                        <h2>2.2M FCFA</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-danger text-white shadow">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title">Dépassement Budget</h5>
                        <h2>50 000 FCFA</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques (Simulation avec Chart.js) -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6>Répartition par Service</h6>
                </div>
                <div class="card-body">
                    <canvas id="serviceChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6>Évolution Mensuelle</h6>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes Dépenses -->
    <div class="row" id="expenseCards">
        <div class="col-md-4 mb-4">
            <div class="card shadow hover-effect">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title">Dépense #EXP-001</h6>
                            <p class="card-text text-muted">Carburant</p>
                            <p class="card-text">Service: Logistique</p>
                            <p class="card-text">Montant: 75 000 FCFA</p>
                        </div>
                        <span class="badge bg-warning">En Attente</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-info me-2">Voir</button>
                        <button class="btn btn-sm btn-warning me-2">Modifier</button>
                        <button class="btn btn-sm btn-success">Approuver</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow hover-effect">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title">Dépense #EXP-002</h6>
                            <p class="card-text text-muted">Maintenance</p>
                            <p class="card-text">Service: Maintenance</p>
                            <p class="card-text">Montant: 150 000 FCFA</p>
                        </div>
                        <span class="badge bg-success">Approuvée</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-info me-2">Voir</button>
                        <button class="btn btn-sm btn-warning me-2">Modifier</button>
                        <button class="btn btn-sm btn-primary">Marquer Payée</button>
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
                                    <th>Référence</th>
                                    <th>Catégorie</th>
                                    <th>Service</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="expenseTable">
                                <tr>
                                    <td>EXP-001</td>
                                    <td>Carburant</td>
                                    <td>Logistique</td>
                                    <td>75 000 FCFA</td>
                                    <td>2023-10-15</td>
                                    <td><span class="badge bg-warning">En Attente</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info">Voir</button>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-success">Approuver</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>EXP-002</td>
                                    <td>Maintenance</td>
                                    <td>Maintenance</td>
                                    <td>150 000 FCFA</td>
                                    <td>2023-10-16</td>
                                    <td><span class="badge bg-success">Approuvée</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info">Voir</button>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-primary">Marquer Payée</button>
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
function filterExpenses() {
    const statut = document.getElementById('statutFilter').value;
    const service = document.getElementById('serviceFilter').value;
    const search = document.getElementById('searchFilter').value;
    console.log('Filtrage:', statut, service, search);
}

// Graphiques avec Chart.js (simulation)
document.addEventListener('DOMContentLoaded', function() {
    const ctx1 = document.getElementById('serviceChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Logistique', 'Maintenance', 'RH', 'Commercial'],
            datasets: [{
                data: [40, 30, 20, 10],
                backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545']
            }]
        }
    });

    const ctx2 = document.getElementById('evolutionChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Dépenses',
                data: [100, 150, 120, 200, 180, 220],
                borderColor: '#007bff',
                fill: false
            }]
        }
    });
});
</script>
@endsection
