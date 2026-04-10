@extends('layouts.app')

@section('title', 'Compte de Résultat - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Compte de Résultat</h1>
            <p class="text-muted mb-0">
                Période du {{ $donnees['debut']->format('d/m/Y') }} au {{ $donnees['fin']->format('d/m/Y') }}
                <span class="badge bg-light text-dark border ms-2">Base {{ $donnees['base_comptable'] }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                Dashboard
            </a>
            <a href="{{ route('comptabilite.rapports.bilan') }}" class="btn btn-outline-info">
                <i class="fas fa-balance-scale me-2"></i>Bilan
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
        </div>
    </div>

    @if(session('error') || !empty($error))
        <div class="alert alert-warning">
            {{ session('error') ?? $error }}
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('comptabilite.rapports.compte-resultat') }}" class="row g-3 align-items-end">
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
                    <a href="{{ route('comptabilite.rapports.compte-resultat') }}" class="btn btn-outline-secondary">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm bg-success-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Produits</div>
                    <div class="fs-4 fw-bold">{{ number_format($donnees['total_produits'], 0, ',', ' ') }} FCFA</div>
                    <div class="small text-muted mt-2">Encaissements et revenus validés</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm bg-danger-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Charges</div>
                    <div class="fs-4 fw-bold">{{ number_format($donnees['total_charges'], 0, ',', ' ') }} FCFA</div>
                    <div class="small text-muted mt-2">Dépenses, véhicules, paies</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm bg-info-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Marge brute</div>
                    <div class="fs-4 fw-bold {{ $donnees['marge_brute'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($donnees['marge_brute'], 0, ',', ' ') }} FCFA
                    </div>
                    <div class="small text-muted mt-2">{{ number_format($donnees['marge_brute_pourcentage'], 1, ',', ' ') }} % du produit</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm {{ $donnees['resultat_net'] >= 0 ? 'bg-primary-subtle' : 'bg-warning-subtle' }}">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Résultat net</div>
                    <div class="fs-4 fw-bold {{ $donnees['resultat_net'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($donnees['resultat_net'], 0, ',', ' ') }} FCFA
                    </div>
                    <div class="small text-muted mt-2">Rentabilité: {{ number_format($donnees['taux_rentabilite'], 1, ',', ' ') }} %</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Produits</h5>
                    <span class="badge bg-success">{{ count($donnees['produits']) }} postes</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th class="text-end">Montant</th>
                                    <th class="text-end">Poids</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donnees['produits'] as $label => $montant)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($montant, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-end text-muted">
                                            {{ $donnees['total_produits'] > 0 ? number_format(($montant / $donnees['total_produits']) * 100, 1, ',', ' ') : '0,0' }} %
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Aucun produit sur la période.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Total produits</th>
                                    <th class="text-end">{{ number_format($donnees['total_produits'], 0, ',', ' ') }} FCFA</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Charges</h5>
                    <span class="badge bg-danger">{{ count($donnees['charges']) }} postes</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th class="text-end">Montant</th>
                                    <th class="text-end">Poids</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donnees['charges'] as $label => $montant)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($montant, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-end text-muted">
                                            {{ $donnees['total_charges'] > 0 ? number_format(($montant / $donnees['total_charges']) * 100, 1, ',', ' ') : '0,0' }} %
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Aucune charge sur la période.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Total charges</th>
                                    <th class="text-end">{{ number_format($donnees['total_charges'], 0, ',', ' ') }} FCFA</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
                        <canvas id="compteResultatChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Analyse synthétique</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small text-uppercase">Résultat d'exploitation</div>
                        <div class="fs-5 fw-bold {{ $donnees['resultat_exploitation'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($donnees['resultat_exploitation'], 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small text-uppercase">Période précédente</div>
                        <div class="fs-5 fw-bold">{{ number_format($donnees['resultat_precedent'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small text-uppercase">Variation</div>
                        <div class="fs-5 fw-bold {{ $donnees['variation'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($donnees['variation'], 0, ',', ' ') }} FCFA
                        </div>
                        <div class="small text-muted">{{ number_format($donnees['variation_pourcentage'], 1, ',', ' ') }} %</div>
                    </div>
                    <hr>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Chiffre d'affaires retenu</span>
                            <strong>{{ number_format($donnees['chiffre_affaires'], 0, ',', ' ') }} FCFA</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Poids des charges</span>
                            <strong>{{ $donnees['total_produits'] > 0 ? number_format(($donnees['total_charges'] / $donnees['total_produits']) * 100, 1, ',', ' ') : '0,0' }} %</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Marge nette</span>
                            <strong>{{ number_format($donnees['resultat_net_pourcentage'], 1, ',', ' ') }} %</strong>
                        </li>
                    </ul>
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

    const chartElement = document.getElementById('compteResultatChart');
    if (!chartElement) {
        return;
    }

    const labels = @json(collect($donnees['graphique_mensuel'])->pluck('mois')->values());
    const produits = @json(collect($donnees['graphique_mensuel'])->pluck('produits')->map(fn($value) => round($value, 2))->values());
    const charges = @json(collect($donnees['graphique_mensuel'])->pluck('charges')->map(fn($value) => round($value, 2))->values());
    const resultat = @json(collect($donnees['graphique_mensuel'])->pluck('resultat')->map(fn($value) => round($value, 2))->values());

    new Chart(chartElement, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Produits',
                    data: produits,
                    backgroundColor: 'rgba(25, 135, 84, 0.65)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1,
                },
                {
                    label: 'Charges',
                    data: charges,
                    backgroundColor: 'rgba(220, 53, 69, 0.6)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1,
                },
                {
                    label: 'Résultat net',
                    data: resultat,
                    type: 'line',
                    tension: 0.35,
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.15)',
                    yAxisID: 'y',
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
                        callback: function(value) {
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
                        label: function(context) {
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