@extends('layouts.app')

@section('title', 'Contrôles Audit - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Contrôles d'Audit</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Audit
            </a>
            <a href="{{ route('audit.rapports') }}" class="btn btn-outline-info">
                <i class="fas fa-file-alt me-2"></i>Rapports
            </a>
            <a href="{{ route('audit.alertes') }}" class="btn btn-outline-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Alertes
            </a>
            <button class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Contrôle
            </button>
        </div>
    </div>

    <!-- KPIs Contrôles -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Contrôles</h6>
                            <h3 class="mb-0">{{ $controles->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-clipboard-check"></i>
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
                            <h6 class="text-muted mb-1">Terminés</h6>
                            <h3 class="mb-0">{{ $controles->where('statut', 'terminé')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-check-circle"></i>
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
                            <h6 class="text-muted mb-1">En Cours</h6>
                            <h3 class="mb-0">{{ $controles->where('statut', 'en_cours')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-spinner"></i>
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
                            <h6 class="text-muted mb-1">Planifiés</h6>
                            <h3 class="mb-0">{{ $controles->where('statut', 'planifié')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Département</label>
                    <select name="departement" class="form-select">
                        <option value="">Tous les départements</option>
                        <option value="Finance">Finance</option>
                        <option value="Ressources Humaines">Ressources Humaines</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Logistique">Logistique</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="Interne">Interne</option>
                        <option value="Externe">Externe</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="terminé">Terminé</option>
                        <option value="en_cours">En cours</option>
                        <option value="planifié">Planifié</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="recherche" class="form-control" placeholder="Rechercher un contrôle...">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('audit.controles') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des Contrôles -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Département</th>
                            <th>Auditeur</th>
                            <th>Score</th>
                            <th>Conformité</th>
                            <th>Recommandations</th>
                            <th>Non-Conformités</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($controles as $controle)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        CTRL
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $controle->reference }}</div>
                                        <div class="text-muted small">ID: {{ $controle->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $controle->date->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $controle->date->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $controle->type == 'Interne' ? 'info' : 'warning' }}">
                                    {{ $controle->type }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($controle->departement, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $controle->departement }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($controle->auditeur, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $controle->auditeur }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($controle->score)
                                    <div class="fw-bold text-{{ $controle->score >= 90 ? 'success' : ($controle->score >= 80 ? 'warning' : 'danger') }}">
                                        {{ $controle->score }}%
                                    </div>
                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </td>
                            <td>
                                @if($controle->conformite)
                                    <span class="badge bg-{{ $controle->score >= 90 ? 'success' : ($controle->score >= 80 ? 'warning' : 'danger') }}">
                                        {{ $controle->conformite }}
                                    </span>
                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </td>
                            <td>
                                @if($controle->recommandations)
                                    <div class="fw-bold">{{ $controle->recommandations }}</div>
                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </td>
                            <td>
                                @if($controle->non_conformites)
                                    <div class="fw-bold text-danger">{{ $controle->non_conformites }}</div>
                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $controle->statut == 'terminé' ? 'success' : ($controle->statut == 'en_cours' ? 'info' : 'warning') }}">
                                    {{ ucfirst($controle->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($controle->statut == 'en_cours')
                                        <button class="btn btn-outline-success" title="Terminer">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-info" title="Rapport">
                                        <i class="fas fa-file-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun contrôle trouvé</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Planifier le premier contrôle
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques par Département -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques par Département</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="text-muted">Contrôles par Département</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-primary">Finance</h4>
                                            <p class="mb-0">1 contrôle</p>
                                            <small>Score: 85%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">Informatique</h4>
                                            <p class="mb-0">1 contrôle</p>
                                            <small>Score: 92%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Contrôles par Type</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">Interne</h4>
                                            <p class="mb-0">3 contrôles</p>
                                            <small>Score moyen: 88.5%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">Externe</h4>
                                            <p class="mb-0">1 contrôle</p>
                                            <small>En cours</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Recommandations</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-primary">Total</h4>
                                            <p class="mb-0">5</p>
                                            <small>En attente: 2</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-success">Mises en œuvre</h4>
                                            <p class="mb-0">3</p>
                                            <small>60%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Non-Conformités</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-danger">Total</h4>
                                            <p class="mb-0">3</p>
                                            <small>Critiques: 1</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">Résolues</h4>
                                            <p class="mb-0">1</p>
                                            <small>33%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
</style>
@endsection
