@extends('layouts.app')

@section('title', 'Approvisionnements - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Approvisionnements de Caisse</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.caisses.index') }}" class="btn btn-outline-info">
                <i class="fas fa-cash-register me-2"></i>Caisses
            </a>
            <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-outline-warning">
                <i class="fas fa-money-bill-wave me-2"></i>Décaissements
            </a>
            <a href="{{ route('tresorerie.virements') }}" class="btn btn-outline-success">
                <i class="fas fa-exchange-alt me-2"></i>Virements
            </a>
            <a href="{{ route('tresorerie.approvisionnements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvel Approvisionnement
            </a>
        </div>
    </div>

    <!-- KPIs Approvisionnements -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Approvisionnements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvisionnements->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Montant Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($approvisionnements->sum('montant'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvisionnements->where('statut', 'en attente')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Validés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvisionnements->where('statut', 'validé')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Approvisionnements -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Approvisionnements</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Rechercher un approvisionnement..." id="searchInput">
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
                    <select class="form-select" id="sourceFilter">
                        <option value="">Toutes les sources</option>
                        <option value="Banque ECOBANK">Banque ECOBANK</option>
                        <option value="Banque SGBCI">Banque SGBCI</option>
                        <option value="Banque BIAO">Banque BIAO</option>
                        <option value="Espèces">Espèces</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportApprovisionnements()">
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
                            <th>Source</th>
                            <th>Référence Source</th>
                            <th>Montant</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvisionnements as $approvisionnement)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($approvisionnement->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $approvisionnement->reference }}</div>
                                        <div class="text-muted small">ID: {{ $approvisionnement->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($approvisionnement->date_approvisionnement)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($approvisionnement->date_approvisionnement)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>{{ $approvisionnement->libelle }}</td>
                            <td>
                                <span class="badge bg-info">{{ $approvisionnement->caisse_nom }}</span>
                            </td>
                            <td>{{ $approvisionnement->source }}</td>
                            <td>{{ $approvisionnement->reference_source }}</td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($approvisionnement->montant, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>{{ $approvisionnement->responsable }}</td>
                            <td>
                                <span class="badge bg-{{ $approvisionnement->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($approvisionnement->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.approvisionnements.show', $approvisionnement->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.approvisionnements.edit', $approvisionnement->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.approvisionnements.destroy', $approvisionnement->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet approvisionnement ?')">
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
    function filterApprovisionnements() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const caisse = document.getElementById('caisseFilter').value;
        const source = document.getElementById('sourceFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[10]?.textContent.toLowerCase().includes(statut);
            const matchesCaisse = !caisse || cells[4]?.textContent.toLowerCase().includes(caisse);
            const matchesSource = !source || cells[5]?.textContent.toLowerCase().includes(source);

            row.style.display = (matchesSearch && matchesStatut && matchesCaisse && matchesSource) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportApprovisionnements() {
        const approvisionnements = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                libelle: cells[2]?.textContent.trim(),
                caisse: cells[4]?.textContent.trim(),
                source: cells[5]?.textContent.trim(),
                reference_source: cells[6]?.textContent.trim(),
                montant: cells[7]?.textContent.trim(),
                responsable: cells[8]?.textContent.trim(),
                statut: cells[9]?.textContent.trim()
            };
        });

        console.log('Approvisionnements à exporter:', approvisionnements);
        alert('Export des approvisionnements simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterApprovisionnements);
    document.getElementById('statutFilter').addEventListener('change', filterApprovisionnements);
    document.getElementById('caisseFilter').addEventListener('change', filterApprovisionnements);
    document.getElementById('sourceFilter').addEventListener('change', filterApprovisionnements);
});
</script>

@endsection
