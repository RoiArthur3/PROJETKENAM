@extends('layouts.app')

@section('title', 'Dashboard Pointage Personnel | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock me-2 text-primary"></i>Dashboard Pointage Personnel
            </h1>
            <p class="text-muted mb-0">Suivi des pointages et efficacité des missions</p>
        </div>
        <a href="{{ route('rh.pointages-engins.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Pointage Personnel
        </a>
    </div>

    <!-- KPI Principaux -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Total Pointages</div>
                            <div class="h3 mb-0 text-primary">{{ $stats['total_pointages'] ?? 0 }}</div>
                            <div class="d-flex align-items-center mt-1">
                                <small class="text-muted">Tous les pointages</small>
                            </div>
                        </div>
                        <div class="text-primary opacity-25">
                            <i class="fas fa-clipboard-list fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Aujourd'hui</div>
                            <div class="h3 mb-0 text-success">{{ $stats['pointages_aujourdhui'] ?? 0 }}</div>
                            <div class="d-flex align-items-center mt-1">
                                <small class="text-muted">Pointages du jour</small>
                            </div>
                        </div>
                        <div class="text-success opacity-25">
                            <i class="fas fa-calendar-day fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">En Retard</div>
                            <div class="h3 mb-0 text-warning">{{ $stats['pointages_en_retard'] ?? 0 }}</div>
                            <div class="d-flex align-items-center mt-1">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Alerte
                                </small>
                            </div>
                        </div>
                        <div class="text-warning opacity-25">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Efficacité Moyenne</div>
                            <div class="h3 mb-0 text-info">{{ number_format($stats['efficacite_moyenne'] ?? 0, 1) }}%</div>
                            <div class="d-flex align-items-center mt-1">
                                <small class="text-muted">Performance globale</small>
                            </div>
                        </div>
                        <div class="text-info opacity-25">
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Secondaires -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">Validés</div>
                    <div class="h4 mb-0 text-success">{{ $stats['pointages_valides'] ?? 0 }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 80%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">Total Heures</div>
                    <div class="h4 mb-0 text-primary">{{ number_format($stats['total_heures'] ?? 0, 1) }}h</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 65%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">Total Jours</div>
                    <div class="h4 mb-0 text-info">{{ number_format($stats['total_jours'] ?? 0, 1) }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">Total KM</div>
                    <div class="h4 mb-0 text-warning">{{ number_format($stats['total_km'] ?? 0, 0) }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 70%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">En Cours</div>
                    <div class="h4 mb-0 text-secondary">{{ ($stats['total_pointages'] ?? 0) - ($stats['pointages_valides'] ?? 0) }}</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-secondary" style="width: 20%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-bold">Taux Validation</div>
                    <div class="h4 mb-0 text-success">{{ $stats['total_pointages'] > 0 ? round(($stats['pointages_valides'] / $stats['total_pointages']) * 100, 0) : 0 }}%</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ $stats['total_pointages'] > 0 ? round(($stats['pointages_valides'] / $stats['total_pointages']) * 100, 0) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et Actions -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Pointages en Retard
                    </h6>
                </div>
                <div class="card-body">
                    @if($pointagesRetard->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mission</th>
                                        <th>Chauffeur</th>
                                        <th>Engin</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pointagesRetard as $pointage)
                                        <tr>
                                            <td>{{ $pointage->operation->titre ?? '-' }}</td>
                                            <td>{{ $pointage->driver->name ?? '-' }}</td>
                                            <td>{{ $pointage->vehicle->immatriculation ?? '-' }}</td>
                                            <td>{{ $pointage->date_pointage->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('rh.pointages-engins.show', $pointage->id) }}" 
                                                   class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted mb-0">Aucun pointage en retard</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-trophy me-2"></i>Top Efficacité Conducteurs
                    </h6>
                </div>
                <div class="card-body">
                    @if($efficaciteParConducteur->count() > 0)
                        @foreach($efficaciteParConducteur as $index => $conducteur)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info') }} rounded-circle">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $conducteur->driver->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $conducteur->nb_pointages }} pointages</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0 text-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info') }}">
                                        {{ number_format($conducteur->efficacite_moyenne ?? 0, 1) }}%
                                    </div>
                                    <div class="progress" style="width: 80px; height: 6px;">
                                        <div class="progress-bar bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info') }}" 
                                             style="width: {{ $conducteur->efficacite_moyenne ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucune donnée d'efficacité disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers Pointages -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Derniers Pointages Enregistrés
                    </h6>
                    <a href="{{ route('rh.pointages-engins.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Voir tout
                    </a>
                </div>
                <div class="card-body">
                    @if($derniersPointages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th><i class="fas fa-project-diagram me-1"></i>Mission</th>
                                        <th><i class="fas fa-truck me-1"></i>Engin</th>
                                        <th><i class="fas fa-user me-1"></i>Chauffeur</th>
                                        <th><i class="fas fa-clock me-1"></i>Horaire</th>
                                        <th><i class="fas fa-hourglass-half me-1"></i>Durée</th>
                                        <th><i class="fas fa-chart-line me-1"></i>Efficacité</th>
                                        <th><i class="fas fa-flag me-1"></i>Statut</th>
                                        <th><i class="fas fa-cog me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($derniersPointages as $pointage)
                                        <tr>
                                            <td>{{ $pointage->date_pointage->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $pointage->operation->titre ?? '-' }}</div>
                                                <small class="text-muted">{{ $pointage->operation->montant ?? 0 }} FCFA</small>
                                            </td>
                                            <td>{{ $pointage->vehicle->immatriculation ?? '-' }}</td>
                                            <td>{{ $pointage->driver->name ?? '-' }}</td>
                                            <td>
                                                @if($pointage->heure_debut && $pointage->heure_fin)
                                                    {{ $pointage->heure_debut }} - {{ $pointage->heure_fin }}
                                                @elseif($pointage->heure_debut)
                                                    {{ $pointage->heure_debut }} (en cours)
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($pointage->duree_heures > 0)
                                                    <span class="badge bg-info">{{ number_format($pointage->duree_heures, 1) }}h</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($pointage->efficacite_score > 0)
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-{{ $pointage->efficacite_score >= 90 ? 'success' : ($pointage->efficacite_score >= 70 ? 'warning' : 'danger') }}">
                                                            {{ number_format($pointage->efficacite_score, 1) }}%
                                                        </span>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $pointage->statut === 'valide' ? 'success' : ($pointage->statut === 'rejete' ? 'danger' : ($pointage->is_retard ? 'warning' : 'primary')) }}">
                                                    {{ ucfirst($pointage->statut) }}
                                                    @if($pointage->is_retard)
                                                        <i class="fas fa-exclamation-triangle ms-1"></i>
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('rh.pointages-engins.show', $pointage->id) }}" 
                                                       class="btn btn-outline-primary" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($pointage->statut === 'en_cours')
                                                        <a href="{{ route('rh.pointages-engins.validate', $pointage->id) }}" 
                                                           class="btn btn-outline-success" title="Valider">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun pointage enregistré</h5>
                            <p class="text-muted mb-4">Commencez par enregistrer votre premier pointage</p>
                            <a href="{{ route('rh.pointages-engins.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Nouveau Pointage Personnel
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

