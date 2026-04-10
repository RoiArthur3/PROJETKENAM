@extends('layouts.app')

@section('title', 'Rapprochements - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Rapprochements Bancaires</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.comptes-bancaires') }}" class="btn btn-outline-info">
                <i class="fas fa-university me-2"></i>Comptes Bancaires
            </a>
            <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-primary">
                <i class="fas fa-exchange-alt me-2"></i>Opérations Bancaires
            </a>
            <a href="{{ route('tresorerie.rapprochements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Rapprochement
            </a>
        </div>
    </div>

    <!-- KPIs Rapprochements -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Rapprochements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rapprochements->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Solde Total Bancaire</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rapprochements->sum('solde_bancaire'), 0, ',', ' ') }} FCFA</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Solde Total Comptable</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rapprochements->sum('solde_comptable'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Écart Total</div>
                            <div class="h5 mb-0 font-weight-bold text-{{ $rapprochements->sum('ecart') > 0 ? 'danger' : 'success' }}">
                                {{ number_format($rapprochements->sum('ecart'), 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Rapprochements -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Rapprochements</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Rechercher un rapprochement..." id="searchInput">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="compteFilter">
                        <option value="">Tous les comptes</option>
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
                        <option value="annulé">Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="ecartFilter">
                        <option value="">Tous les écarts</option>
                        <option value="0">Aucun écart</option>
                        <option value="positif">Écart positif</option>
                        <option value="negatif">Écart négatif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportRapprochements()">
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
                            <th>Compte Bancaire</th>
                            <th>Solde Bancaire</th>
                            <th>Solde Comptable</th>
                            <th>Écart</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rapprochements as $rapprochement)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($rapprochement->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $rapprochement->reference }}</div>
                                        <div class="text-muted small">ID: {{ $rapprochement->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($rapprochement->date_rapprochement)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($rapprochement->date_rapprochement)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $rapprochement->compte_bancaire }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">{{ number_format($rapprochement->solde_bancaire, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="fw-bold text-info">{{ number_format($rapprochement->solde_comptable, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="fw-bold {{ $rapprochement->ecart > 0 ? 'text-danger' : $rapprochement->ecart < 0 ? 'text-warning' : 'text-success' }}">
                                    {{ $rapprochement->ecart > 0 ? '+' : '' }}{{ number_format($rapprochement->ecart, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $rapprochement->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($rapprochement->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.rapprochements.show', $rapprochement->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.rapprochements.edit', $rapprochement->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.rapprochements.destroy', $rapprochement->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rapprochement ?')">
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
    function filterRapprochements() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const compte = document.getElementById('compteFilter').value;
        const statut = document.getElementById('statutFilter').value;
        const ecart = document.getElementById('ecartFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesCompte = !compte || cells[3]?.textContent.toLowerCase().includes(compte);
            const matchesStatut = !statut || cells[8]?.textContent.toLowerCase().includes(statut);

            let matchesEcart = true;
            if (ecart === '0') {
                matchesEcart = cells[6]?.textContent.includes('0 FCFA');
            } else if (ecart === 'positif') {
                matchesEcart = cells[6]?.textContent.includes('+');
            } else if (ecart === 'negatif') {
                matchesEcart = cells[6]?.textContent.includes('-') && !cells[6]?.textContent.includes('0 FCFA');
            }

            row.style.display = (matchesSearch && matchesCompte && matchesStatut && matchesEcart) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportRapprochements() {
        const rapprochements = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                compte_bancaire: cells[3]?.textContent.trim(),
                solde_bancaire: cells[4]?.textContent.trim(),
                solde_comptable: cells[5]?.textContent.trim(),
                ecart: cells[6]?.textContent.trim(),
                statut: cells[7]?.textContent.trim()
            };
        });

        console.log('Rapprochements à exporter:', rapprochements);
        alert('Export des rapprochements simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterRapprochements);
    document.getElementById('compteFilter').addEventListener('change', filterRapprochements);
    document.getElementById('statutFilter').addEventListener('change', filterRapprochements);
    document.getElementById('ecartFilter').addEventListener('change', filterRapprochements);
});
</script>

@endsection
