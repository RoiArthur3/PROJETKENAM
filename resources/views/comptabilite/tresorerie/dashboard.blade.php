@extends('layouts.app')

@section('title', 'Tableau de Bord Trésorerie - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tableau de Bord Trésorerie</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-primary active">
                <i class="fas fa-cash-register me-2"></i>Trésorerie
            </a>
        </div>
    </div>

    <!-- KPIs Trésorerie -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-white-50 mb-1">Total Caisses</h6>
                            <h3 class="mb-0">{{ $stats['total_caisses'] }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-cash-register fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-white-50 mb-1">Solde Total</h6>
                            <h3 class="mb-0">{{ number_format($stats['solde_total'], 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-wallet fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-white-50 mb-1">Variation</h6>
                            <h3 class="mb-0">{{ number_format($stats['variation'], 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-white-50 mb-1">En Attente</h6>
                            <h3 class="mb-0">{{ $stats['en_attente'] }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Caisses -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cash-register me-2"></i>
                        État des Caisses
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nom</th>
                                    <th>Solde Actuel</th>
                                    <th>Solde Initial</th>
                                    <th>Variation</th>
                                    <th>Statut</th>
                                    <th>Responsable</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($caisses) && $caisses->count() > 0)
                                    @foreach($caisses as $caisse)
                                        <tr>
                                            <td>{{ $caisse->nom }}</td>
                                            <td class="fw-bold">{{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($caisse->solde_initial, 0, ',', ' ') }} FCFA</td>
                                            <td class="{{ $caisse->variation > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $caisse->variation > 0 ? '+' : '' }}{{ number_format($caisse->variation, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $caisse->statut == 'actif' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($caisse->statut) }}
                                                </span>
                                            </td>
                                            <td>{{ $caisse->responsable }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucune caisse trouvée</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('tresorerie.caisses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouvelle Caisse
                </a>
                <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-success">
                    <i class="fas fa-plus-circle me-2"></i>Approvisionnement
                </a>
                <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-warning">
                    <i class="fas fa-minus-circle me-2"></i>Décaissement
                </a>
                <a href="{{ route('tresorerie.virements') }}" class="btn btn-info">
                    <i class="fas fa-exchange-alt me-2"></i>Virements
                </a>
            </div>
        </div>
    </div>

    <!-- Mouvements Récents -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Approvisionnements Récents
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($approvisionnements) && $approvisionnements->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($approvisionnements->take(5) as $approvisionnement)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $approvisionnement->reference }}</h6>
                                            <small>{{ isset($approvisionnement->date) ? $approvisionnement->date : 'N/A' }}</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-success">{{ number_format($approvisionnement->montant, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun approvisionnement récent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-minus-circle me-2"></i>
                        Décaissements Récents
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($decaissements) && $decaissements->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($decaissements->take(5) as $decaissement)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $decaissement->reference }}</h6>
                                            <small>{{ isset($decaissement->date) ? $decaissement->date : 'N/A' }}</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-danger">{{ number_format($decaissement->montant, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-minus-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun décaissement récent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
