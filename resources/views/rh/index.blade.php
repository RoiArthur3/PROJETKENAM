@extends('layouts.app')

@section('title', 'Gestion RH - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Gestion des Ressources Humaines</h1>
        </div>
    </div>

    <!-- Cartes de Résumé RH -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Employés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">25</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Présents Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">22</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Congé</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plane fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Absents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides RH -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('hr.pointage.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-clock fa-fw"></i> Gérer Pointage
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-secondary btn-block">
                                <i class="fas fa-user-plus fa-fw"></i> Ajouter Employé
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-info btn-block">
                                <i class="fas fa-calendar fa-fw"></i> Gérer Congés
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-warning btn-block">
                                <i class="fas fa-chart-bar fa-fw"></i> Rapports RH
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Employés -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Liste des Employés</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Service</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Date d'Embauche</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jean Dupont</td>
                                    <td>Logistique</td>
                                    <td>Agent</td>
                                    <td><span class="badge bg-success">Actif</span></td>
                                    <td>2022-01-15</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">Voir</button>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Marie Curie</td>
                                    <td>Entretien</td>
                                    <td>Chef de Service</td>
                                    <td><span class="badge bg-success">Actif</span></td>
                                    <td>2021-05-10</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">Voir</button>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
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
@endsection
