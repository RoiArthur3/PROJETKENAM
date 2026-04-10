@extends('layouts.app')

@section('title', 'Comptes Bancaires - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Comptes Bancaires</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.caisses') }}" class="btn btn-outline-info">
                <i class="fas fa-cash-register me-2"></i>Caisses
            </a>
            <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-outline-success">
                <i class="fas fa-plus-circle me-2"></i>Approvisionnements
            </a>
            <a href="{{ route('tresorerie.rapprochements') }}" class="btn btn-outline-warning">
                <i class="fas fa-balance-scale me-2"></i>Rapprochements
            </a>
            <a href="{{ route('tresorerie.comptes-bancaires.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Compte
            </a>
        </div>
    </div>

    <!-- KPIs Comptes Bancaires -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Comptes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $comptes->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-university fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Solde Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($comptes->sum('solde'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Comptes Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $comptes->where('statut', 'actif')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Banques</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $comptes->pluck('banque')->unique()->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Comptes Bancaires -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Comptes Bancaires</h5>
            
            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Rechercher un compte..." id="searchInput">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="banqueFilter">
                        <option value="">Toutes les banques</option>
                        <option value="ECOBANK">ECOBANK</option>
                        <option value="SGBCI">SGBCI</option>
                        <option value="BIAO">BIAO</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statutFilter">
                        <option value="">Tous les statuts</option>
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportComptes()">
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
                            <th>Nom du Compte</th>
                            <th>Banque</th>
                            <th>Numéro de Compte</th>
                            <th>Solde</th>
                            <th>Devise</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comptes as $compte)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($compte->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $compte->reference }}</div>
                                        <div class="text-muted small">ID: {{ $compte->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $compte->nom }}</div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $compte->banque }}</span>
                            </td>
                            <td>
                                <code>{{ $compte->numero_compte }}</code>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($compte->solde, 0, ',', ' ') }} {{ $compte->devise }}</span>
                            </td>
                            <td>{{ $compte->devise }}</td>
                            <td>{{ $compte->responsable }}</td>
                            <td>
                                <span class="badge bg-{{ $compte->statut == 'actif' ? 'success' : 'danger' }}">
                                    {{ ucfirst($compte->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.comptes-bancaires.show', $compte->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.comptes-bancaires.edit', $compte->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.comptes-bancaires.destroy', $compte->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce compte bancaire ?')">
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
    function filterComptes() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const banque = document.getElementById('banqueFilter').value;
        const statut = document.getElementById('statutFilter').value;
        
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();
            
            const matchesSearch = !search || rowText.includes(search);
            const matchesBanque = !banque || cells[3]?.textContent.toLowerCase().includes(banque);
            const matchesStatut = !statut || cells[8]?.textContent.toLowerCase().includes(statut);
            
            row.style.display = (matchesSearch && matchesBanque && matchesStatut) ? '' : 'none';
        });
    }
    
    // Fonction d'export
    function exportComptes() {
        const comptes = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                nom: cells[1]?.textContent.trim(),
                banque: cells[3]?.textContent.trim(),
                numero_compte: cells[4]?.textContent.trim(),
                solde: cells[5]?.textContent.trim(),
                devise: cells[6]?.textContent.trim(),
                responsable: cells[7]?.textContent.trim(),
                statut: cells[8]?.textContent.trim()
            };
        });
        
        console.log('Comptes à exporter:', comptes);
        alert('Export des comptes bancaires simulé (voir console)');
    }
    
    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterComptes);
    document.getElementById('banqueFilter').addEventListener('change', filterComptes);
    document.getElementById('statutFilter').addEventListener('change', filterComptes);
});
</script>

@endsection
