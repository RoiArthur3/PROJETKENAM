@extends('layouts.app')

@section('title', 'Opérations Bancaires - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Opérations Bancaires</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="{{ route('tresorerie.virements') }}" class="btn btn-outline-info">
                <i class="fas fa-exchange-alt me-2"></i>Virements
            </a>
            <a href="{{ route('comptabilite.rapports.tresorerie') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-2"></i>Rapport Trésorerie
            </a>
            <a href="{{ route('tresorerie.banque.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Opération
            </a>
        </div>
    </div>

    <!-- KPIs Trésorerie -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Opérations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $operationsBancaires->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-university fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Débits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($operationsBancaires->where('type', 'débit')->sum('montant'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Crédits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($operationsBancaires->where('type', 'crédit')->sum('montant'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Solde</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($operationsBancaires->sum('montant'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Opérations Bancaires -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Opérations Bancaires</h5>
            
            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Rechercher une opération..." id="searchInput">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="typeFilter">
                        <option value="">Tous les types</option>
                        <option value="débit">Débit</option>
                        <option value="crédit">Crédit</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="banqueFilter">
                        <option value="">Toutes les banques</option>
                        <option value="ECOBANK">ECOBANK</option>
                        <option value="SGBCI">SGBCI</option>
                        <option value="BIAO">BIAO</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statutFilter">
                        <option value="">Tous les statuts</option>
                        <option value="validé">Validé</option>
                        <option value="en attente">En attente</option>
                    </select>
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
                            <th>Type</th>
                            <th>Banque</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operationsBancaires as $operation)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($operation->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $operation->reference }}</div>
                                        <div class="text-muted small">ID: {{ $operation->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($operation->date_operation)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($operation->date_operation)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>{{ $operation->libelle }}</td>
                            <td>
                                <span class="badge bg-{{ $operation->type == 'crédit' ? 'success' : 'danger' }}">
                                    {{ ucfirst($operation->type) }}
                                </span>
                            </td>
                            <td>{{ $operation->banque }}</td>
                            <td>
                                <span class="fw-bold {{ $operation->type == 'crédit ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($operation->montant, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $operation->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($operation->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.banque.show', $operation->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.banque.edit', $operation->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.banque.destroy', $operation->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette opération ?')">
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
    function filterOperations() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const type = document.getElementById('typeFilter').value;
        const banque = document.getElementById('banqueFilter').value;
        const statut = document.getElementById('statutFilter').value;
        
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();
            
            const matchesSearch = !search || rowText.includes(search);
            const matchesType = !type || cells[4]?.textContent.toLowerCase().includes(type);
            const matchesBanque = !banque || cells[5]?.textContent.toLowerCase().includes(banque);
            const matchesStatut = !statut || cells[7]?.textContent.toLowerCase().includes(statut);
            
            row.style.display = (matchesSearch && matchesType && matchesBanque && matchesStatut) ? '' : 'none';
        });
    }
    
    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterOperations);
    document.getElementById('typeFilter').addEventListener('change', filterOperations);
    document.getElementById('banqueFilter').addEventListener('change', filterOperations);
    document.getElementById('statutFilter').addEventListener('change', filterOperations);
});
</script>

@endsection
