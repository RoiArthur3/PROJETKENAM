@extends('layouts.app')

@section('title', 'Flux Financiers - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Flux Financiers</h1>
        <div class="d-flex gap-2 no-print">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
            <a href="{{ route('tresorerie.virements') }}" class="btn btn-outline-info">
                <i class="fas fa-exchange-alt me-1"></i> Virements
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Flux
            </a>
            <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimer le rapport
            </button>
        </div>
    </div>

    <!-- KPIs Flux -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Entrées</h6>
                            <h3 class="mb-0 text-success">{{ number_format($flux->where('type', 'entrant')->sum('montant'), 0, ',', ' ') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Sorties</h6>
                            <h3 class="mb-0 text-danger">{{ number_format($flux->where('type', 'sortant')->sum('montant'), 0, ',', ' ') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-danger text-white">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Solde Net</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($flux->where('type', 'entrant')->sum('montant') - $flux->where('type', 'sortant')->sum('montant'), 0, ',', ' ') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Flux</h6>
                            <h3 class="mb-0">{{ $flux->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rapport synthétique par compte (imprimable) -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Rapport synthétique par compte (période filtrée)</h5>
        </div>
        <div class="card-body">
            @php
                $fluxParCompte = $flux->groupBy('compte');
            @endphp
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Compte</th>
                            <th class="text-end">Entrées</th>
                            <th class="text-end">Sorties</th>
                            <th class="text-end">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fluxParCompte as $compte => $items)
                            @php
                                $totalEntrees = $items->where('type', 'entrant')->sum('montant');
                                $totalSorties = $items->where('type', 'sortant')->sum('montant');
                                $net = $totalEntrees - $totalSorties;
                            @endphp
                            <tr>
                                <td><strong>{{ $compte ?: 'Sans libellé' }}</strong></td>
                                <td class="text-end text-success">{{ number_format($totalEntrees, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end text-danger">{{ number_format($totalSorties, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $net >= 0 ? '+' : '' }}{{ number_format($net, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aucun flux pour la période sélectionnée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Type de Flux</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="entrant" {{ request('type') == 'entrant' ? 'selected' : '' }}>Entrants</option>
                        <option value="sortant" {{ request('type') == 'sortant' ? 'selected' : '' }}>Sortants</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Catégorie</label>
                    <select name="categorie" class="form-select">
                        <option value="">Toutes les catégories</option>
                        <option value="Ventes" {{ request('categorie') == 'Ventes' ? 'selected' : '' }}>Ventes</option>
                        <option value="Achats" {{ request('categorie') == 'Achats' ? 'selected' : '' }}>Achats</option>
                        <option value="Services" {{ request('categorie') == 'Services' ? 'selected' : '' }}>Services</option>
                        <option value="Personnel" {{ request('categorie') == 'Personnel' ? 'selected' : '' }}>Personnel</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select name="periode" class="form-select">
                        <option value="">Toutes périodes</option>
                        <option value="7" {{ request('periode') == '7' ? 'selected' : '' }}>7 derniers jours</option>
                        <option value="30" {{ request('periode') == '30' ? 'selected' : '' }}>30 derniers jours</option>
                        <option value="90" {{ request('periode') == '90' ? 'selected' : '' }}>90 derniers jours</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Compte</label>
                    <select name="compte" class="form-select">
                        <option value="">Tous les comptes</option>
                        <option value="BGCI - Compte Principal" {{ request('compte') == 'BGCI - Compte Principal' ? 'selected' : '' }}>BGCI - Principal</option>
                        <option value="ECOBANK - Compte Opérations" {{ request('compte') == 'ECOBANK - Compte Opérations' ? 'selected' : '' }}>ECOBANK - Opérations</option>
                        <option value="NSIA - Compte Auxiliaire" {{ request('compte') == 'NSIA - Compte Auxiliaire' ? 'selected' : '' }}>NSIA - Auxiliaire</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('tresorerie.flux') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des Flux -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Montant</th>
                            <th>Catégorie</th>
                            <th>Compte</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flux as $item)
                        <tr>
                            <td>
                                <div>
                                    @if($item->date_mouvement)
                                        <div>{{ $item->date_mouvement->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $item->date_mouvement->format('H:i') }}</div>
                                    @else
                                        <div class="text-muted">-</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $item->type == 'entrant' ? 'success' : 'danger' }} me-2">
                                    <i class="fas fa-arrow-{{ $item->type == 'entrant' ? 'down' : 'up' }}"></i>
                                    {{ ucfirst($item->type) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($item->source, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $item->source }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-{{ $item->type == 'entrant' ? 'success' : 'danger' }}">
                                    {{ $item->type == 'entrant' ? '+' : '-' }}{{ number_format($item->montant, 0, ',', ' ') }} FCFA
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item->categorie }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->compte }}</small>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $item->description }}">
                                    {{ $item->description }}
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Détails">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun flux financier trouvé</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Créer le premier flux
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Graphique des Flux -->
    <div class="card mt-4 no-print">
        <div class="card-body">
            <h5 class="card-title mb-4">Évolution des Flux (7 derniers jours)</h5>
            <canvas id="fluxChart" height="100"></canvas>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

@media print {
    .no-print {
        display: none !important;
    }

    .content-wrapper {
        padding: 0;
        background: #fff;
    }

    table {
        font-size: 11px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('fluxChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['{{ now()->subDays(6)->format('d/m') }}', '{{ now()->subDays(5)->format('d/m') }}', '{{ now()->subDays(4)->format('d/m') }}', '{{ now()->subDays(3)->format('d/m') }}', '{{ now()->subDays(2)->format('d/m') }}', '{{ now()->subDays(1)->format('d/m') }}', '{{ now()->format('d/m') }}'],
                datasets: [{
                    label: 'Entrées',
                    data: [450000, 520000, 480000, 610000, 750000, 580000, 450000],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Sorties',
                    data: [320000, 380000, 350000, 420000, 300000, 450000, 280000],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value / 1000 + 'k FCFA';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
