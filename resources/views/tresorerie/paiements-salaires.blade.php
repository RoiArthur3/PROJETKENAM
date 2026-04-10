@extends('layouts.app')

@section('title', 'Paiements Salaires - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Paiements Salaires</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-info">
                <i class="fas fa-money-bill-wave me-2"></i>Tous les Paiements
            </a>
            <a href="{{ route('tresorerie.paiements-fournisseurs') }}" class="btn btn-outline-primary">
                <i class="fas fa-truck me-2"></i>Fournisseurs
            </a>
            <a href="{{ route('tresorerie.paiements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Paiement
            </a>
        </div>
    </div>

    <!-- KPIs Paiements Salaires -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Salariés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $paiements->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Masse Salariale</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($paiements->sum('montant'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ce Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $paiements->where('periode', 'Janvier 2024')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Moyenne</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($paiements->count() > 0 ? $paiements->sum('montant') / $paiements->count() : 0, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Paiements Salaires -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Paiements Salaires</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Rechercher un paiement..." id="searchInput">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statutFilter">
                        <option value="">Tous les statuts</option>
                        <option value="validé">Validé</option>
                        <option value="en attente">En attente</option>
                        <option value="annulé">Annulé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="employeFilter">
                        <option value="">Tous les employés</option>
                        <option value="Agent Principal">Agent Principal</option>
                        <option value="Agent Secondaire">Agent Secondaire</option>
                        <option value="Agent Terrain">Agent Terrain</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="periodeFilter">
                        <option value="">Toutes les périodes</option>
                        <option value="Janvier 2024">Janvier 2024</option>
                        <option value="Décembre 2023">Décembre 2023</option>
                        <option value="Novembre 2023">Novembre 2023</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportPaiements()">
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
                            <th>Date</th>
                            <th>Employé</th>
                            <th>Période</th>
                            <th>Salaire Brut</th>
                            <th>Salaire Net</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiements as $paiement)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($paiement->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $paiement->reference }}</div>
                                        <div class="text-muted small">ID: {{ $paiement->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>{{ $paiement->employe }}</td>
                            <td>
                                <span class="badge bg-info">{{ $paiement->periode }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-warning">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($paiement->montant * 0.85, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $paiement->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($paiement->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.paiements-salaries.show', $paiement->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.paiements-salaries.edit', $paiement->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.paiements-salaries.destroy', $paiement->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')">
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
    function filterPaiements() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const employe = document.getElementById('employeFilter').value;
        const periode = document.getElementById('periodeFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[8]?.textContent.toLowerCase().includes(statut);
            const matchesEmploye = !employe || cells[4]?.textContent.toLowerCase().includes(employe);
            const matchesPeriode = !periode || cells[5]?.textContent.toLowerCase().includes(periode);

            row.style.display = (matchesSearch && matchesStatut && matchesEmploye && matchesPeriode) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportPaiements() {
        const paiements = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                employe: cells[4]?.textContent.trim(),
                periode: cells[5]?.textContent.trim(),
                salaire_brut: cells[6]?.textContent.trim(),
                salaire_net: cells[7]?.textContent.trim(),
                statut: cells[8]?.textContent.trim()
            };
        });

        console.log('Paiements salaires à exporter:', paiements);
        alert('Export des paiements salaires simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterPaiements);
    document.getElementById('statutFilter').addEventListener('change', filterPaiements);
    document.getElementById('employeFilter').addEventListener('change', filterPaiements);
    document.getElementById('periodeFilter').addEventListener('change', filterPaiements);
});
</script>

@endsection
