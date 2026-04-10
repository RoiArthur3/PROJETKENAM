@extends('layouts.app')

@section('title', 'Pointage du Personnel - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Pointage du Personnel</h1>
            <a href="{{ route('rh.pointages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Pointage
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-md-4">
            <input type="date" class="form-control" id="dateFilter" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-4">
            <select class="form-select" id="statusFilter">
                <option value="">Tous les statuts</option>
                <option value="present">Présent</option>
                <option value="absent">Absent</option>
                <option value="late">En Retard</option>
                <option value="half_day">Demi-journée</option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-outline-success" onclick="filterAttendances()">
                <i class="fas fa-search"></i> Filtrer
            </button>
        </div>
    </div>

    <!-- Tableau des Pointages -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Liste des Pointages du Jour</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Employé</th>
                                    <th>Date</th>
                                    <th>Heure d'Arrivée</th>
                                    <th>Heure de Départ</th>
                                    <th>Statut</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTable">
                                <!-- Exemples de données -->
                                <tr>
                                    <td>Jean Dupont</td>
                                    <td>2023-10-17</td>
                                    <td>08:00</td>
                                    <td>17:00</td>
                                    <td><span class="badge bg-success">Présent</span></td>
                                    <td>Aucune</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-info">Détails</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Marie Curie</td>
                                    <td>2023-10-17</td>
                                    <td>08:15</td>
                                    <td>16:30</td>
                                    <td><span class="badge bg-warning">En Retard</span></td>
                                    <td>Retard justifié</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning">Modifier</button>
                                        <button class="btn btn-sm btn-info">Détails</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé du Jour -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Présents</h5>
                    <h2>12</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Absents</h5>
                    <h2>2</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">En Retard</h5>
                    <h2>3</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Heures Totales</h5>
                    <h2>96h</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterAttendances() {
    const date = document.getElementById('dateFilter').value;
    const status = document.getElementById('statusFilter').value;
    // Logique pour filtrer les pointages via AJAX ou rechargement
    console.log('Filtrage pour date:', date, 'statut:', status);
}
</script>
@endsection
