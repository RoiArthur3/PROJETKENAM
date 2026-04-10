@extends('layouts.app')

@section('title', 'Analyses Comparatives - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-balance-scale me-2 text-primary"></i>Analyses Comparatives
            </h1>
            <p class="text-muted mb-0">Comparez les performances entre deux périodes pour différentes métriques</p>
        </div>
    </div>

    <!-- Formulaire de sélection des paramètres -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-filter me-2"></i>Paramètres de Comparaison
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('analyses.comparatifs') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="period1" class="form-label fw-bold">Première Période</label>
                    <select class="form-select" id="period1" name="period1">
                        <option value="month" {{ $period1 == 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="last_month" {{ $period1 == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                        <option value="quarter" {{ $period1 == 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                        <option value="last_quarter" {{ $period1 == 'last_quarter' ? 'selected' : '' }}>Trimestre dernier</option>
                        <option value="year" {{ $period1 == 'year' ? 'selected' : '' }}>Cette année</option>
                        <option value="last_year" {{ $period1 == 'last_year' ? 'selected' : '' }}>Année dernière</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="period2" class="form-label fw-bold">Deuxième Période</label>
                    <select class="form-select" id="period2" name="period2">
                        <option value="last_month" {{ $period2 == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                        <option value="month" {{ $period2 == 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="last_quarter" {{ $period2 == 'last_quarter' ? 'selected' : '' }}>Trimestre dernier</option>
                        <option value="quarter" {{ $period2 == 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                        <option value="last_year" {{ $period2 == 'last_year' ? 'selected' : '' }}>Année dernière</option>
                        <option value="year" {{ $period2 == 'year' ? 'selected' : '' }}>Cette année</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="metric" class="form-label fw-bold">Métrique</label>
                    <select class="form-select" id="metric" name="metric">
                        <option value="operations" {{ $metric == 'operations' ? 'selected' : '' }}>Nombre d'opérations</option>
                        <option value="revenue" {{ $metric == 'revenue' ? 'selected' : '' }}>Chiffre d'affaires</option>
                        <option value="expenses" {{ $metric == 'expenses' ? 'selected' : '' }}>Dépenses</option>
                        <option value="profit" {{ $metric == 'profit' ? 'selected' : '' }}>Bénéfice</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Comparer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de la comparaison -->
    <div class="row mb-4">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-calendar-alt me-2"></i>{{ ucfirst(str_replace('_', ' ', $period1)) }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="display-4 text-primary mb-2">{{ number_format($data1 ?? 0, $metric == 'operations' ? 0 : 2, ',', ' ') }}</div>
                    <div class="text-muted">{{ $metric == 'operations' ? 'opérations' : ($metric == 'revenue' ? 'FCFA de CA' : ($metric == 'expenses' ? 'FCFA de dépenses' : 'FCFA de bénéfice')) }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card border-left-secondary shadow h-100">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-calendar-alt me-2"></i>{{ ucfirst(str_replace('_', ' ', $period2)) }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="display-4 text-secondary mb-2">{{ number_format($data2 ?? 0, $metric == 'operations' ? 0 : 2, ',', ' ') }}</div>
                    <div class="text-muted">{{ $metric == 'operations' ? 'opérations' : ($metric == 'revenue' ? 'FCFA de CA' : ($metric == 'expenses' ? 'FCFA de dépenses' : 'FCFA de bénéfice')) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analyse de l'écart -->
    @php
        $v1 = (float)($data1 ?? 0);
        $v2 = (float)($data2 ?? 0);
        $delta = $v2 != 0 ? round((($v1 - $v2) / $v2) * 100, 1) : null;
        $evolution = $delta !== null ? ($delta > 0 ? 'hausse' : ($delta < 0 ? 'baisse' : 'stable')) : 'non calculable';
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Analyse de l'Évolution
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="text-{{ $delta > 0 ? 'success' : ($delta < 0 ? 'danger' : 'warning') }} mb-2">
                                <i class="fas fa-arrow-{{ $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'right') }} me-2"></i>
                                {{ $delta !== null ? abs($delta) . '% de ' . $evolution : 'Évolution non calculable' }}
                            </h4>
                            <p class="text-muted mb-0">
                                Comparaison entre {{ ucfirst(str_replace('_', ' ', $period1)) }} et {{ ucfirst(str_replace('_', ' ', $period2)) }} pour la métrique "{{ $metric == 'operations' ? 'Nombre d\'opérations' : ($metric == 'revenue' ? 'Chiffre d\'affaires' : ($metric == 'expenses' ? 'Dépenses' : 'Bénéfice')) }}"
                            </p>
                        </div>
                        <div class="col-md-4">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $delta > 0 ? 'success' : ($delta < 0 ? 'danger' : 'warning') }}"
                                     style="width: {{ $delta !== null ? min(max($delta + 50, 0), 100) : 50 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique Comparatif -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Visualisation Comparée
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="comparaisonChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('comparaisonChart').getContext('2d');
const data1 = {{ json_encode((float)($data1 ?? 0)) }};
const data2 = {{ json_encode((float)($data2 ?? 0)) }};
const period1Label = '{{ ucfirst(str_replace('_', ' ', $period1)) }}';
const period2Label = '{{ ucfirst(str_replace('_', ' ', $period2)) }}';
const metricLabel = '{{ $metric == 'operations' ? 'Opérations' : ($metric == 'revenue' ? 'CA (FCFA)' : ($metric == 'expenses' ? 'Dépenses (FCFA)' : 'Bénéfice (FCFA)')) }}';

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [period1Label, period2Label],
        datasets: [{
            label: metricLabel,
            data: [data1, data2],
            backgroundColor: ['#4e73df', '#858796'],
            borderColor: ['#4e73df', '#858796'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '{{ $metric == 'operations' ? '' : 'FCFA ' }}' + value.toLocaleString('fr-FR');
                    }
                }
            }
        },
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + '{{ $metric == 'operations' ? '' : ' FCFA' }}';
                    }
                }
            }
        }
    }
});
</script>
@endsection
