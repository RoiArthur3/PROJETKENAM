@extends('layouts.app')

@section('title', 'Virements - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Virements</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="{{ route('comptabilite.rapports.tresorerie') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-2"></i>Rapport Trésorerie
            </a>
            <a href="{{ route('tresorerie.virements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Virement
            </a>
        </div>
    </div>

    <!-- KPIs Virements -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Virements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $virements->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Sortant</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($virements->sum('montant'), 0, ',', ' ') }} FCFA</div>
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
                    <div class="row no-gultats align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $virements->where('statut', 'en attente')->count() }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $virements->where('statut', 'validé')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Virements -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Virements</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Rechercher un virement..." id="searchInput">
                </div>
                <div class="col-md-3">
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
                        <option value="Employés KENAM">Employés KENAM</option>
                        <option value="INFO-TECH SARL">INFO-TECH SARL</option>
                        <option value="IMMOBILIER CI">IMMOBILIER CI</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportVirements()">
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
                            <th>Compte Source</th>
                            <th>Compte Destination</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($virements as $virement)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($virement->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $virement->reference }}</div>
                                        <div class="text-muted small">ID: {{ $virement->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($virement->date_virement)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>{{ $virement->libelle }}</td>
                            <td>{{ $virement->beneficiaire }}</td>
                            <td>{{ $virement->compte_source }}</td>
                            <td>{{ $virement->compte_destination }}</td>
                            <td>
                                <span class="fw-bold text-primary">{{ number_format($virement->montant, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $virement->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($virement->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tresorerie.virements.show', $virement->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tresorerie.virements.edit', $virement->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tresorerie.virements.destroy', $virement->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce virement ?')">
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
    function filterVirements() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const beneficiaire = document.getElementById('beneficiaireFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[7]?.textContent.toLowerCase().includes(statut);
            const matchesBeneficiaire = !beneficiaire || cells[4]?.textContent.toLowerCase().includes(beneficiaire);

            row.style.display = (matchesSearch && matchesStatut && matchesBeneficiaire) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportVirements() {
        // Simuler l'exportation des virements
        const virements = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                libelle: cells[2]?.textContent.trim(),
                beneficiaire: cells[4]?.textContent.trim(),
                compte_source: cells[5]?.textContent.trim(),
                compte_destination: cells[6]?.textContent.trim(),
                montant: cells[7]?.textContent.trim(),
                statut: cells[7]?.textContent.trim()
            };
        });

        console.log('Virements à exporter:', virements);
        alert('Export des virements simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterVirements);
    document.getElementById('statutFilter').addEventListener('change', filterVirements);
    document.getElementById('beneficiaireFilter').addEventListener('change', filterVirements);
});
</script>

@endsection
