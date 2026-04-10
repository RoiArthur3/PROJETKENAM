@extends('layouts.app')

@section('title', 'Paiements Fournisseurs - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Paiements Fournisseurs</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-info">
                <i class="fas fa-money-bill-wave me-2"></i>Tous les Paiements
            </a>
            <a href="{{ route('tresorerie.paiements-salaires') }}" class="btn btn-outline-success">
                <i class="fas fa-users me-2"></i>Salaires
            </a>
            <a href="{{ route('tresorerie.paiements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Paiement
            </a>
        </div>
    </div>

    <!-- KPIs Paiements Fournisseurs -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Paiements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $paiements->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Payés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $paiements->where('statut', 'validé')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $paiements->where('statut', 'en attente')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Paiements Fournisseurs -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Paiements Fournisseurs</h5>

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
                    <select class="form-select" id="fournisseurFilter">
                        <option value="">Tous les fournisseurs</option>
                        <option value="BUREAU PLUS">BUREAU PLUS</option>
                        <option value="INFO-TECH SARL">INFO-TECH SARL</option>
                        <option value="TELECOM CI">TELECOM CI</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="modeFilter">
                        <option value="">Tous les modes</option>
                        <option value="Virement">Virement</option>
                        <option value="Espèces">Espèces</option>
                        <option value="Chèque">Chèque</option>
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
                            <th>Fournisseur</th>
                            <th>Facture</th>
                            <th>Montant</th>
                            <th>Mode Paiement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiements as $paiement)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-info text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
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
                            <td>{{ $paiement->fournisseur }}</td>
                            <td>
                                <code>{{ $paiement->facture }}</code>
                            </td>
                            <td>
                                <span class="fw-bold text-danger">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $paiement->mode_paiement ?? 'Virement' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $paiement->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($paiement->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.paiements-fournisseurs.show', $paiement->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.paiements-fournisseurs.edit', $paiement->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.paiements-fournisseurs.destroy', $paiement->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')">
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
        const fournisseur = document.getElementById('fournisseurFilter').value;
        const mode = document.getElementById('modeFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[8]?.textContent.toLowerCase().includes(statut);
            const matchesFournisseur = !fournisseur || cells[4]?.textContent.toLowerCase().includes(fournisseur);
            const matchesMode = !mode || cells[7]?.textContent.toLowerCase().includes(mode);

            row.style.display = (matchesSearch && matchesStatut && matchesFournisseur && matchesMode) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportPaiements() {
        const paiements = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                fournisseur: cells[4]?.textContent.trim(),
                facture: cells[5]?.textContent.trim(),
                montant: cells[6]?.textContent.trim(),
                mode_paiement: cells[7]?.textContent.trim(),
                statut: cells[8]?.textContent.trim()
            };
        });

        console.log('Paiements fournisseurs à exporter:', paiements);
        alert('Export des paiements fournisseurs simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterPaiements);
    document.getElementById('statutFilter').addEventListener('change', filterPaiements);
    document.getElementById('fournisseurFilter').addEventListener('change', filterPaiements);
    document.getElementById('modeFilter').addEventListener('change', filterPaiements);
});
</script>

@endsection
