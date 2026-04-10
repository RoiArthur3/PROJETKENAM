@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête du dashboard -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>Tableau de Bord
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble de l'activité</p>
        </div>
        <button class="btn btn-primary" onclick="rafraichirDashboard()">
            <i class="fas fa-sync-alt me-2"></i>Rafraîchir
        </button>
    </div>

    <!-- KPIs Financiers - PRIORITÉ ABSOLUE -->
    <div class="row mb-4">
        <!-- Revenus du mois -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Revenus du Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(DB::table('factures')->whereMonth('date_facture', now()->month)->where('statut', 'payee')->sum('montant_ttc') / 1000000, 1) }}M</div>
                            <div class="text-muted small">FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Factures impayées -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Factures Impayées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('factures')->where('statut', 'impayee')->count() }}</div>
                            <div class="text-muted small">En attente de paiement</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Solde trésorerie -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Solde Trésorerie</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(DB::table('caisses')->where('est_active', 1)->sum('solde_actuel') + DB::table('comptes_bancaires')->sum('solde'), 0, ',', ' ') }} FCFA</div>
                            <div class="text-muted small">Disponible</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Opérationnels - Deuxième priorité -->
    <div class="row mb-4">
        <!-- Opérations du jour -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Opérations du Jour</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('operations')->whereDate('created_at', today())->count() }}</div>
                            <div class="text-muted small">En cours / Terminées</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matériel roulant -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Matériel Roulant</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('vehicules')->where('disponible', true)->count() }} / {{ DB::table('vehicules')->count() }}</div>
                            <div class="text-muted small">Disponibles / Total</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personnel présent -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Personnel Présent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('pointages')->whereDate('date_pointage', today())->distinct('user_id')->count() }} / {{ DB::table('users')->where('role', 'agent')->count() }}</div>
                            <div class="text-muted small">Présents / Total agents</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock alertes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stock Alertes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('produits')->where('stock_actuel', '<=', 'stock_min')->count() }}</div>
                            <div class="text-muted small">Produits en alerte</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validations en attente -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Validations en Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('validations')->where('statut', 'en_attente')->count() }}</div>
                            <div class="text-muted small">À traiter</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Comptabilité -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator me-2"></i>Comptabilité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Total Factures -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-success shadow-sm">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Factures</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('factures')->count() }}</div>
                                    <div class="text-muted small">Documents émis</div>
                                </div>
                            </div>
                        </div>

                        <!-- Factures Impayées -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-danger shadow-sm">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Factures Impayées</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('factures')->where('statut', 'impayee')->count() }}</div>
                                    <div class="text-muted small">En attente de paiement</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Recettes -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-info shadow-sm">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Recettes</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('recettes')->count() }}</div>
                                    <div class="text-muted small">Opérations enregistrées</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Dépenses -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-warning shadow-sm">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Dépenses</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ DB::table('depenses')->count() }}</div>
                                    <div class="text-muted small">Dépenses enregistrées</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/comptabilite/dashboard" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>Voir le dashboard comptabilité complet
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Trésorerie -->
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-wallet me-2"></i>Trésorerie
                        </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Solde Caisses -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-success shadow-sm">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Solde Caisses</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(DB::table('caisses')->where('est_active', 1)->sum('solde_actuel'), 0, ',', ' ') }} FCFA</div>
                                    <div class="text-muted small">Disponible</div>
                                </div>
                            </div>
                        </div>

                        <!-- Solde Banques -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-info shadow-sm">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Solde Banques</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(DB::table('comptes_bancaires')->sum('solde'), 0, ',', ' ') }} FCFA</div>
                                    <div class="text-muted small">Disponible</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Trésorerie -->
                        <div class="col-md-12 mb-3">
                            <div class="card border-left-primary shadow-sm">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Trésorerie</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(DB::table('caisses')->where('est_active', 1)->sum('solde_actuel') + DB::table('comptes_bancaires')->sum('solde'), 0, ',', ' ') }} FCFA</div>
                                    <div class="text-muted small">Caisses + Banques</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/tresorerie/dashboard" class="btn btn-warning">
                            <i class="fas fa-arrow-right me-2"></i>Voir le dashboard trésorerie complet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et Activités Récentes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Vue d'ensemble
                    </h3>
                    <button class="btn btn-primary" onclick="rafraichirDashboard()">
                        <i class="fas fa-sync-alt me-2"></i>Rafraîchir
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="operationsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Opérations Récentes -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Opérations Récentes
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $operationsRecentes = DB::table('operations')
                            ->leftJoin('users', 'operations.user_id', '=', 'users.id')
                            ->select('operations.*', 'users.name as responsable_name')
                            ->orderBy('operations.created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @forelse($operationsRecentes as $operation)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Responsable</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($operationsRecentes as $operation)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $operation->id }}
                                                </span>
                                            </td>
                                            <td>{{ $operation->type ?? 'N/A' }}</td>
                                            <td>
                                                @switch($operation->statut_courant ?? 'en_attente')
                                                    @case('en_attente')
                                                        <span class="badge bg-warning">En attente</span>
                                                    @break
                                                    @case('en_cours')
                                                        <span class="badge bg-info">En cours</span>
                                                    @break
                                                    @case('termine')
                                                        <span class="badge bg-success">Terminé</span>
                                                    @break
                                                    @case('annule')
                                                        <span class="badge bg-danger">Annulé</span>
                                                    @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $operation->statut_courant ?? 'N/A' }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($operation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $operation->responsable_name ?? 'Non assigné' }}</td>
                                            <td>
                                                <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0">Aucune opération récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fonction pour rafraîchir le dashboard
    function rafraichirDashboard() {
        location.reload();
    }

// Initialisation des graphiques
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des opérations mensuelles
    const operationsCtx = document.getElementById('operationsChart').getContext('2d');

    fetch('/api/chart-data')
        .then(response => response.json())
        .then(data => {
            new Chart(operationsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Opérations',
                        data: data.map(item => item.count),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Erreur lors du chargement des données:', error));
});

// Fonction pour changer la période
function changerPeriode(periode) {
    console.log('Changement de période:', periode);
    // Ici vous pouvez ajouter la logique pour changer la période du graphique
}
</script>
@endsection
