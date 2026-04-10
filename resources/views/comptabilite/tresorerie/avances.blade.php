@extends('layouts.app')

@section('title', 'Avances - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Gestion des Avances</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.caisses') }}" class="btn btn-outline-info">
                <i class="fas fa-cash-register me-2"></i>Caisses
            </a>
            <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-warning">
                <i class="fas fa-money-bill-wave me-2"></i>Paiements
            </a>
            <a href="{{ route('tresorerie.depenses.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-receipt me-2"></i>Dépenses
            </a>
            <a href="{{ route('tresorerie.avances.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Avance
            </a>
        </div>
    </div>

    <!-- KPIs Avances -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Avances</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avances->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Montant Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($avances->sum('montant'), 0, ',', ' ') }} FCFA</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avances->where('statut', 'en attente')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Validées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avances->where('statut', 'validé')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Avances -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Avances</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Rechercher une avance..." id="searchInput">
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
                    <select class="form-select" id="beneficiaireFilter">
                        <option value="">Tous les bénéficiaires</option>
                        <option value="Agent Principal">Agent Principal</option>
                        <option value="Agent Secondaire">Agent Secondaire</option>
                        <option value="Agent Terrain">Agent Terrain</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="motifFilter">
                        <option value="">Tous les motifs</option>
                        <option value="Mission">Mission</option>
                        <option value="Frais terrain">Frais terrain</option>
                        <option value="Urgence">Urgence</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportAvances()">
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
                            <th>Bénéficiaire</th>
                            <th>Motif</th>
                            <th>Montant</th>
                            <th>Caisse</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($avances as $avance)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($avance->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $avance->reference }}</div>
                                        <div class="text-muted small">ID: {{ $avance->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($avance->date_avance)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($avance->date_avance)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>{{ $avance->beneficiaire }}</td>
                            <td>{{ $avance->motif }}</td>
                            <td>
                                <span class="fw-bold text-warning">{{ number_format($avance->montant, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="badge bg-info">Caisse Principale</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $avance->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($avance->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.avances.show', $avance->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.avances.edit', $avance->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.avances.destroy', $avance->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette avance ?')">
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
    function filterAvances() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const beneficiaire = document.getElementById('beneficiaireFilter').value;
        const motif = document.getElementById('motifFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[7]?.textContent.toLowerCase().includes(statut);
            const matchesBeneficiaire = !beneficiaire || cells[3]?.textContent.toLowerCase().includes(beneficiaire);
            const matchesMotif = !motif || cells[4]?.textContent.toLowerCase().includes(motif);

            row.style.display = (matchesSearch && matchesStatut && matchesBeneficiaire && matchesMotif) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportAvances() {
        const avances = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                beneficiaire: cells[3]?.textContent.trim(),
                motif: cells[4]?.textContent.trim(),
                montant: cells[5]?.textContent.trim(),
                caisse: cells[6]?.textContent.trim(),
                statut: cells[7]?.textContent.trim()
            };
        });

        console.log('Avances à exporter:', avances);
        alert('Export des avances simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterAvances);
    document.getElementById('statutFilter').addEventListener('change', filterAvances);
    document.getElementById('beneficiaireFilter').addEventListener('change', filterAvances);
    document.getElementById('motifFilter').addEventListener('change', filterAvances);
});
</script>

@endsection
