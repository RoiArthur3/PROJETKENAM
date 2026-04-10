@extends('layouts.app')

@section('title', 'Indicateurs Financiers - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Synthèse des Principaux Indicateurs Financiers</h1>
            <p class="text-muted mb-0">
                Période du {{ $donnees['debut']->format('d/m/Y') }} au {{ $donnees['fin']->format('d/m/Y') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Dashboard
            </a>
            <a href="{{ route('comptabilite.rapports.compte-resultat') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-2"></i>Compte de Résultat
            </a>
            <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('comptabilite.rapports.indicateurs-financiers') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="periode" class="form-label">Période</label>
                    <select name="periode" id="periode" class="form-select">
                        <option value="mois" {{ $donnees['periode'] === 'mois' ? 'selected' : '' }}>Mois en cours</option>
                        <option value="trimestre" {{ $donnees['periode'] === 'trimestre' ? 'selected' : '' }}>Trimestre en cours</option>
                        <option value="annee" {{ $donnees['periode'] === 'annee' ? 'selected' : '' }}>Année en cours</option>
                        <option value="personnalise" {{ $donnees['periode'] === 'personnalise' ? 'selected' : '' }}>Période personnalisée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut', $donnees['debut']->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin', $donnees['fin']->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-sync-alt me-2"></i>Actualiser
                    </button>
                    <a href="{{ route('comptabilite.rapports.indicateurs-financiers') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-success-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Chiffre d'affaires</div>
                    <div class="fs-4 fw-bold">{{ number_format($donnees['chiffre_affaires'], 0, ',', ' ') }} FCFA</div>
                    <div class="small {{ $donnees['variation_ca'] >= 0 ? 'text-success' : 'text-danger' }} mt-2">
                        Variation: {{ number_format($donnees['variation_ca'], 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-primary-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Trésorerie disponible</div>
                    <div class="fs-4 fw-bold">{{ number_format($donnees['tresorerie_disponible'], 0, ',', ' ') }} FCFA</div>
                    <div class="small text-muted mt-2">Encaissements inclus sur la période</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-warning-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Créances clients</div>
                    <div class="fs-4 fw-bold">{{ number_format($donnees['creances_clients'], 0, ',', ' ') }} FCFA</div>
                    <div class="small text-muted mt-2">Factures impayées et en retard</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 {{ $donnees['resultat_net'] >= 0 ? 'bg-info-subtle' : 'bg-danger-subtle' }}">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Résultat net</div>
                    <div class="fs-4 fw-bold {{ $donnees['resultat_net'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($donnees['resultat_net'], 0, ',', ' ') }} FCFA</div>
                    <div class="small {{ $donnees['variation_resultat'] >= 0 ? 'text-success' : 'text-danger' }} mt-2">
                        Variation: {{ number_format($donnees['variation_resultat'], 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="small text-muted text-uppercase">Marge nette</div>
                    <div class="display-6 fw-bold">{{ number_format($donnees['marge_nette'], 1, ',', ' ') }} %</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="small text-muted text-uppercase">Poids des charges</div>
                    <div class="display-6 fw-bold">{{ number_format($donnees['poids_charges'], 1, ',', ' ') }} %</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="small text-muted text-uppercase">Liquidité immédiate</div>
                    <div class="display-6 fw-bold">{{ number_format($donnees['liquidite_immediate'], 1, ',', ' ') }} %</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="small text-muted text-uppercase">Taux de recouvrement</div>
                    <div class="display-6 fw-bold">{{ number_format($donnees['taux_recouvrement'], 1, ',', ' ') }} %</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Évolution mensuelle</h5>
                </div>
                <div class="card-body">
                    <div style="height: 320px;">
                        <canvas id="indicateursFinanciersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Lecture rapide</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Encaissements constatés</span>
                            <strong>{{ number_format($donnees['encaissements'], 0, ',', ' ') }} FCFA</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <span>Dettes fournisseurs</span>
                                Dashboard
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Besoin en fonds de roulement</span>
                            <strong class="{{ $donnees['besoin_fonds_roulement'] <= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($donnees['besoin_fonds_roulement'], 0, ',', ' ') }} FCFA
                            </strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Total charges</span>
                            <strong>{{ number_format($donnees['total_charges'], 0, ',', ' ') }} FCFA</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Produits suivis</h5>
                    <span class="badge bg-success">{{ count($donnees['produits']) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th class="text-end">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donnees['produits'] as $label => $montant)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($montant, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">Aucun produit sur la période.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Charges suivies</h5>
                    <span class="badge bg-danger">{{ count($donnees['charges']) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th class="text-end">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donnees['charges'] as $label => $montant)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($montant, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">Aucune charge sur la période.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const periodSelect = document.getElementById('periode');
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');

    function toggleDates() {
        const enabled = periodSelect.value === 'personnalise';
        dateDebut.toggleAttribute('disabled', !enabled);
        dateFin.toggleAttribute('disabled', !enabled);
    }

    periodSelect.addEventListener('change', toggleDates);
    toggleDates();

    const chartElement = document.getElementById('indicateursFinanciersChart');
    if (!chartElement) {
        return;
    }

    const labels = @json(collect($donnees['graphique_mensuel'])->pluck('mois')->values());
    const produits = @json(collect($donnees['graphique_mensuel'])->pluck('produits')->map(fn($value) => round($value, 2))->values());
    const charges = @json(collect($donnees['graphique_mensuel'])->pluck('charges')->map(fn($value) => round($value, 2))->values());
    const resultat = @json(collect($donnees['graphique_mensuel'])->pluck('resultat')->map(fn($value) => round($value, 2))->values());

    new Chart(chartElement, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Produits',
                    data: produits,
                    borderColor: 'rgba(25, 135, 84, 1)',
                    backgroundColor: 'rgba(25, 135, 84, 0.15)',
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Charges',
                    data: charges,
                    borderColor: 'rgba(220, 53, 69, 1)',
                    backgroundColor: 'rgba(220, 53, 69, 0.12)',
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Résultat net',
                    data: resultat,
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.12)',
                    tension: 0.35,
                    fill: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return Number(value).toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': ' + Number(context.raw).toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection