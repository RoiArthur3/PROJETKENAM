@extends('layouts.app')

@section('title', 'Décaissements - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Décaissements de Caisse</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="{{ route('tresorerie.caisse') }}" class="btn btn-outline-info">
                <i class="fas fa-cash-register me-2"></i>Caisse
            </a>
            <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-primary">
                <i class="fas fa-university me-2"></i>Banque
            </a>
            <a href="{{ route('tresorerie.virements') }}" class="btn btn-outline-warning">
                <i class="fas fa-exchange-alt me-2"></i>Virements
            </a>
            <a href="{{ route('tresorerie.decaissements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Décaissement
            </a>
        </div>
    </div>

    <!-- KPIs Décaissements -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Décaissements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $decaissements->count() }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($decaissements->sum('montant'), 0, ',', ' ') }} FCFA</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $decaissements->where('statut', 'en attente')->count() }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Validés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $decaissements->where('statut', 'validé')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Décaissements -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Décaissements</h5>
            
            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Rechercher un décaissement..." id="searchInput">
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
                    <select class="form-select" id="beneficiaireFilter">
                        <option value="">Tous les bénéficiaires</option>
                        <option value="BUREAU PLUS">BUREAU PLUS</option>
                        <option value="TOTAL CI">TOTAL CI</option>
                        <option value="Transporteur">Transporteur</option>
                        <option value="Restaurant">Restaurant</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportDecaissements()">
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
                            <th>Bénéficiaire</th>
                            <th>Caisse</th>
                            <th>Montant</th>
                            <th>Motif</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($decaissements as $decaissement)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($decaissement->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $decaissement->reference }}</div>
                                        <div class="text-muted small">ID: {{ $decaissement->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($decaissement->date_decaissement)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($decaissement->date_decaissement)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>{{ $decaissement->libelle }}</td>
                            <td>{{ $decaissement->beneficiaire }}</td>
                            <td>
                                <span class="badge bg-info">{{ $decaissement->caisse_nom }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-danger">{{ number_format($decaissement->montant, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>{{ $decaissement->motif }}</td>
                            <td>{{ $decaissement->responsable }}</td>
                            <td>
                                <span class="badge bg-{{ $decaissement->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($decaissement->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.decaissements.show', $decaissement->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.decaissements.edit', $decaissement->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.decaissements.destroy', $decaissement->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce décaissement ?')">
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
    function filterDecaissements() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const caisse = document.getElementById('caisseFilter').value;
        const beneficiaire = document.getElementById('beneficiaireFilter').value;
        
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();
            
            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[9]?.textContent.toLowerCase().includes(statut);
            const matchesCaisse = !caisse || cells[5]?.textContent.toLowerCase().includes(caisse);
            const matchesBeneficiaire = !beneficiaire || cells[4]?.textContent.toLowerCase().includes(beneficiaire);
            
            row.style.display = (matchesSearch && matchesStatut && matchesCaisse && matchesBeneficiaire) ? '' : 'none';
        });
    }
    
    // Fonction d'export
    function exportDecaissements() {
        const decaissements = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                libelle: cells[2]?.textContent.trim(),
                beneficiaire: cells[4]?.textContent.trim(),
                caisse: cells[5]?.textContent.trim(),
                montant: cells[6]?.textContent.trim(),
                motif: cells[7]?.textContent.trim(),
                responsable: cells[8]?.textContent.trim(),
                statut: cells[9]?.textContent.trim()
            };
        });
        
        console.log('Décaissements à exporter:', decaissements);
        alert('Export des décaissements simulé (voir console)');
    }
    
    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterDecaissements);
    document.getElementById('statutFilter').addEventListener('change', filterDecaissements);
    document.getElementById('caisseFilter').addEventListener('change', filterDecaissements);
    document.getElementById('beneficiaireFilter').addEventListener('change', filterDecaissements);
});
</script>

@endsection
