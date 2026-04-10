@extends('layouts.app')

@section('title', 'Caisses - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Gestion des Caisses</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-outline-success">
                <i class="fas fa-plus-circle me-2"></i>Approvisionnements
            </a>
            <a href="{{ route('tresorerie.avances') }}" class="btn btn-outline-primary">
                <i class="fas fa-hand-holding-usd me-2"></i>Acomptes
            </a>
            <a href="{{ route('tresorerie.caisses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Caisse
            </a>
        </div>
    </div>

    <!-- KPIs Caisses -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Caisses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $caisses->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Solde Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($caisses->sum('solde_actuel'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Solde Initial</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($caisses->sum('solde_initial'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Variation</div>
                            <div class="h5 mb-0 font-weight-bold text-{{ $caisses->sum('solde_actuel') > $caisses->sum('solde_initial') ? 'success' : 'danger' }}">
                                {{ number_format($caisses->sum('solde_actuel') - $caisses->sum('solde_initial'), 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Caisses -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Caisses</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Rechercher une caisse..." id="searchInput">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statutFilter">
                        <option value="">Tous les statuts</option>
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="responsableFilter">
                        <option value="">Tous les responsables</option>
                        <option value="Agent Principal">Agent Principal</option>
                        <option value="Agent Secondaire">Agent Secondaire</option>
                        <option value="Agent Terrain">Agent Terrain</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportCaisses()">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Solde Initial</th>
                            <th>Solde Actuel</th>
                            <th>Devise</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($caisses as $caisse)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($caisse->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $caisse->reference }}</div>
                                        <div class="text-muted small">ID: {{ $caisse->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $caisse->nom }}</div>
                                <small class="text-muted">Créée le {{ \Carbon\Carbon::parse($caisse->date_creation)->format('d/m/Y') }}</small>
                            </td>
                            <td>{{ $caisse->description }}</td>
                            <td>{{ number_format($caisse->solde_initial, 0, ',', ' ') }} {{ $caisse->devise }}</td>
                            <td>
                                <span class="fw-bold {{ $caisse->solde_actuel > $caisse->solde_initial ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} {{ $caisse->devise }}
                                </span>
                            </td>
                            <td>{{ $caisse->devise }}</td>
                            <td>{{ $caisse->responsable }}</td>
                            <td>
                                <span class="badge bg-{{ $caisse->statut == 'actif' ? 'success' : 'danger' }}">
                                    {{ ucfirst($caisse->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.caisses.show', $caisse->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.caisses.edit', $caisse->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.caisses.destroy', $caisse->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction de recherche
    function filterCaisses() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const responsable = document.getElementById('responsableFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[8]?.textContent.toLowerCase().includes(statut);
            const matchesResponsable = !responsable || cells[7]?.textContent.toLowerCase().includes(responsable);

            row.style.display = (matchesSearch && matchesStatut && matchesResponsable) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportCaisses() {
        const caisses = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                nom: cells[1]?.textContent.trim(),
                description: cells[2]?.textContent.trim(),
                solde_initial: cells[3]?.textContent.trim(),
                solde_actuel: cells[4]?.textContent.trim(),
                devise: cells[5]?.textContent.trim(),
                responsable: cells[6]?.textContent.trim(),
                statut: cells[7]?.textContent.trim()
            };
        });

        console.log('Caisses à exporter:', caisses);
        alert('Export des caisses simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterCaisses);
    document.getElementById('statutFilter').addEventListener('change', filterCaisses);
    document.getElementById('responsableFilter').addEventListener('change', filterCaisses);
});
</script>

@endsection
