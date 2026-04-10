@extends('layouts.app')

@section('title', 'Détails Rapprochement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Rapprochement Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.rapprochements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.rapprochements.edit', $rapprochement->id) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted">Informations générales</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Référence</th>
                                    <td>{{ $rapprochement->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date de rapprochement</th>
                                    <td>{{ \Carbon\Carbon::parse($rapprochement->date_rapprochement)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Compte bancaire</th>
                                    <td>
                                        <span class="badge bg-info">{{ $rapprochement->compte_bancaire }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Période</th>
                                    <td>{{ $rapprochement->periode }}</td>
                                </tr>
                                <tr>
                                    <th>Responsable</th>
                                    <td>{{ $rapprochement->responsable }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge bg-{{ $rapprochement->statut == 'validé' ? 'success' : ($rapprochement->statut == 'en cours' ? 'info' : 'warning') }}">
                                            {{ ucfirst($rapprochement->statut) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Soldes et montants</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Solde initial</th>
                                    <td class="fw-bold">{{ number_format($rapprochement->solde_initial, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Solde final</th>
                                    <td class="fw-bold">{{ number_format($rapprochement->solde_final, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Écart</th>
                                    <td class="fw-bold {{ $rapprochement->ecart != 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($rapprochement->ecart, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total débits</th>
                                    <td class="text-danger">{{ number_format($rapprochement->total_debits, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Total crédits</th>
                                    <td class="text-success">{{ number_format($rapprochement->total_credits, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Date de validation</th>
                                    <td>{{ $rapprochement->date_validation ? \Carbon\Carbon::parse($rapprochement->date_validation)->format('d/m/Y H:i') : 'Non validée' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- KPIs du rapprochement -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Compte</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rapprochement->compte_bancaire }}</div>
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
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Solde final</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rapprochement->solde_final, 0, ',', ' ') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
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
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Écart</div>
                                            <div class="h5 mb-0 font-weight-bold {{ $rapprochement->ecart != 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($rapprochement->ecart, 0, ',', ' ') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
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
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Statut</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ucfirst($rapprochement->statut) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique des soldes -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Évolution des soldes</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="soldeChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Actions rapides</h5>
                            <div class="d-flex gap-2">
                                @if($rapprochement->statut == 'en attente')
                                    <button class="btn btn-success" onclick="validerRapprochement()">
                                        <i class="fas fa-check me-2"></i>Valider
                                    </button>
                                @endif
                                @if($rapprochement->statut == 'en cours')
                                    <button class="btn btn-info" onclick="finaliserRapprochement()">
                                        <i class="fas fa-flag-checkered me-2"></i>Finaliser
                                    </button>
                                @endif
                                <a href="{{ route('tresorerie.rapprochements.edit', $rapprochement->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <button class="btn btn-info" onclick="imprimerRapprochement()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                                <button class="btn btn-outline-primary" onclick="telechargerRapport()">
                                    <i class="fas fa-download me-2"></i>Télécharger rapport
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline du rapprochement -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Historique du rapprochement</h5>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Début du rapprochement</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($rapprochement->date_rapprochement)->format('d/m/Y H:i') }}</p>
                                        <small>Rapprochement du compte {{ $rapprochement->compte_bancaire }} pour la période {{ $rapprochement->periode }}</small>
                                    </div>
                                </div>
                                @if($rapprochement->date_validation)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Validation</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($rapprochement->date_validation)->format('d/m/Y H:i') }}</p>
                                        <small>Rapprochement validé par {{ $rapprochement->validateur ?? $rapprochement->responsable }}</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(isset($rapprochement->observations))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Observations</h5>
                            <p>{{ $rapprochement->observations }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}
.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('soldeChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Solde Initial', 'Total Débits', 'Total Crédits', 'Solde Final'],
                datasets: [{
                    label: 'Montants (FCFA)',
                    data: [{{ $rapprochement->solde_initial }}, {{ $rapprochement->total_debits }}, {{ $rapprochement->total_credits }}, {{ $rapprochement->solde_final }}],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
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
                                return value.toLocaleString() + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
    }
});

function validerRapprochement() {
    if (confirm('Valider ce rapprochement?')) {
        alert('Rapprochement validé avec succès!');
    }
}

function finaliserRapprochement() {
    if (confirm('Finaliser ce rapprochement?')) {
        alert('Rapprochement finalisé avec succès!');
    }
}

function imprimerRapprochement() {
    window.print();
}

function telechargerRapport() {
    alert('Téléchargement du rapport simulé');
}
</script>

@endsection
