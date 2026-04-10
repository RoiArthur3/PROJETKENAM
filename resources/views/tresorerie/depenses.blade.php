@extends('layouts.app')

@section('title', 'Dépenses de Trésorerie - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dépenses de Trésorerie</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="{{ route('tresorerie.caisses.index') }}" class="btn btn-outline-info">
                <i class="fas fa-cash-register me-2"></i>Caisse
            </a>
            <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-outline-success">
                <i class="fas fa-plus-circle me-2"></i>Approvisionnements
            </a>
            <a href="{{ route('tresorerie.encaissements') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-down me-2"></i>Encaissements
            </a>
            <a href="{{ route('tresorerie.depenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Dépense
            </a>
        </div>
    </div>

    <!-- KPIs Dépenses -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Dépenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $depenses->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($depenses->sum('montant'), 0, ',', ' ') }} FCFA</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $depenses->where('statut', 'en attente')->count() }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $depenses->where('statut', 'validé')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Dépenses -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Dépenses</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Rechercher une dépense..." id="searchInput">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statutFilter">
                        <option value="">Tous les statuts</option>
                        <option value="validé">Validé</option>
                        <option value="en attente">En attente</option>
                        <option value="annulé">Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="caisseFilter">
                        <option value="">Toutes les caisses</option>
                        <option value="Caisse Principale">Caisse Principale</option>
                        <option value="Caisse Secondaire">Caisse Secondaire</option>
                        <option value="Caisse Mobile">Caisse Mobile</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="motifFilter">
                        <option value="">Tous les motifs</option>
                        <option value="Fournitures">Fournitures</option>
                        <option value="Carburant">Carburant</option>
                        <option value="Transport">Transport</option>
                        <option value="Communication">Communication</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportDepenses()">
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
                            <th>Libellé</th>
                            <th>Caisse</th>
                            <th>Motif</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($depenses as $depense)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-danger text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($depense->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $depense->reference }}</div>
                                        <div class="text-muted small">ID: {{ $depense->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($depense->date_depense)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>{{ $depense->libelle }}</td>
                            <td>
                                <span class="badge bg-info">{{ $depense->caisse }}</span>
                            </td>
                            <td>{{ $depense->motif }}</td>
                            <td>
                                <span class="fw-bold text-danger">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $depense->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($depense->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.depenses.show', $depense->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.depenses.edit', $depense->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.depenses.destroy', $depense->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')">
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
    function filterDepenses() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const caisse = document.getElementById('caisseFilter').value;
        const motif = document.getElementById('motifFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[8]?.textContent.toLowerCase().includes(statut);
            const matchesCaisse = !caisse || cells[5]?.textContent.toLowerCase().includes(caisse);
            const matchesMotif = !motif || cells[6]?.textContent.toLowerCase().includes(motif);

            row.style.display = (matchesSearch && matchesStatut && matchesCaisse && matchesMotif) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportDepenses() {
        const depenses = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                libelle: cells[3]?.textContent.trim(),
                caisse: cells[5]?.textContent.trim(),
                motif: cells[6]?.textContent.trim(),
                montant: cells[7]?.textContent.trim(),
                statut: cells[8]?.textContent.trim()
            };
        });

        console.log('Dépenses à exporter:', depenses);
        alert('Export des dépenses simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterDepenses);
    document.getElementById('statutFilter').addEventListener('change', filterDepenses);
    document.getElementById('caisseFilter').addEventListener('change', filterDepenses);
    document.getElementById('motifFilter').addEventListener('change', filterDepenses);
});
</script>

@endsection
