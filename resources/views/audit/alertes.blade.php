@extends('layouts.app')

@section('title', 'Alertes Audit - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Alertes d'Audit</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Audit
            </a>
            <a href="{{ route('audit.controles') }}" class="btn btn-outline-primary">
                <i class="fas fa-clipboard-check me-2"></i>Contrôles
            </a>
            <a href="{{ route('audit.rapports') }}" class="btn btn-outline-info">
                <i class="fas fa-file-alt me-2"></i>Rapports
            </a>
            <button class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Alerte
            </button>
        </div>
    </div>

    <!-- KPIs Alertes -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Alertes</h6>
                            <h3 class="mb-0">{{ $alertes->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-exclamation-triangle"></i>
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
                            <h6 class="text-muted mb-1">Alertes Critiques</h6>
                            <h3 class="mb-0">{{ $alertes->where('niveau', 'Critique')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-danger text-white">
                                <i class="fas fa-exclamation"></i>
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
                            <h6 class="text-muted mb-1">Alertes Élevées</h6>
                            <h3 class="mb-0">{{ $alertes->where('niveau', 'Élevé')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-bell"></i>
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
                            <h6 class="text-muted mb-1">Alertes Faibles</h6>
                            <h3 class="mb-0">{{ $alertes->where('niveau', 'Faible')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-info-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes Critiques -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-danger">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alertes Critiques - Action Immédiate Requise
                    </h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-danger text-white me-3">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">ALRT-2026-003 - Logistique</div>
                                    <div class="text-muted">Absence de documentation des processus - Délai: 24 heures</div>
                                </div>
                                <div class="ms-3">
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-bolt"></i> Traiter
                                    </button>
                                </div>
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
                    <label class="form-label">Niveau</label>
                    <select name="niveau" class="form-select">
                        <option value="">Tous les niveaux</option>
                        <option value="Critique">Critique</option>
                        <option value="Élevé">Élevé</option>
                        <option value="Moyen">Moyen</option>
                        <option value="Faible">Faible</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="Non-conformité">Non-conformité</option>
                        <option value="Risque">Risque</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Département</label>
                    <select name="departement" class="form-select">
                        <option value="">Tous les départements</option>
                        <option value="Finance">Finance</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Logistique">Logistique</option>
                        <option value="Ressources Humaines">Ressources Humaines</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="nouveau">Nouveau</option>
                        <option value="en_cours">En cours</option>
                        <option value="en_attente">En attente</option>
                        <option value="résolu">Résolu</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('audit.alertes') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des Alertes -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Niveau</th>
                            <th>Département</th>
                            <th>Description</th>
                            <th>Responsable</th>
                            <th>Délai</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alertes as $alerte)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-{{ $alerte->niveau == 'Critique' ? 'danger' : ($alerte->niveau == 'Élevé' ? 'warning' : ($alerte->niveau == 'Moyen' ? 'info' : 'secondary') }} text-white me-3">
                                        ALRT
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $alerte->reference }}</div>
                                        <div class="text-muted small">ID: {{ $alerte->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $alerte->date->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $alerte->date->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $alerte->type == 'Non-conformité' ? 'danger' : 'warning' }}">
                                    {{ $alerte->type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $alerte->niveau == 'Critique' ? 'danger' : ($alerte->niveau == 'Élevé' ? 'warning' : ($alerte->niveau == 'Moyen' ? 'info' : 'secondary') }}">
                                    {{ $alerte->niveau }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($alerte->departement, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $alerte->departement }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $alerte->description }}">
                                    {{ $alerte->description }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($alerte->responsable, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $alerte->responsable }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-{{ $alerte->delai == '24 heures' ? 'danger' : ($alerte->delai == '3 jours' ? 'warning' : 'info') }}">
                                    {{ $alerte->delai }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $alerte->statut == 'nouveau' ? 'danger' : ($alerte->statut == 'en_cours' ? 'info' : ($alerte->statut == 'en_attente' ? 'warning' : 'success') }}">
                                    {{ ucfirst($alerte->statut) }}
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
                                    @if($alerte->statut == 'nouveau' || $alerte->statut == 'en_attente')
                                        <button class="btn btn-outline-success" title="Traiter">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                    @if($alerte->statut == 'en_cours')
                                        <button class="btn btn-outline-warning" title="Suspendre">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @endif
                                    @if($alerte->statut == 'en_cours')
                                        <button class="btn btn-outline-success" title="Résoudre">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-info" title="Historique">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune alerte trouvée</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Créer la première alerte
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques par Niveau -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques par Niveau</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="text-muted">Alertes par Niveau</h6>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-danger">{{ $alertes->where('niveau', 'Critique')->count() }}</h4>
                                            <p class="mb-0">Critiques</p>
                                            <small>Urgence immédiate</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">{{ $alertes->where('niveau', 'Élevé')->count() }}</h4>
                                            <p class="mb-0">Élevées</p>
                                            <small>Haute priorité</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">{{ $alertes->where('niveau', 'Moyen')->count() }}</h4>
                                            <p class="mb-0">Moyennes</p>
                                            <small>Priorité normale</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-secondary">{{ $alertes->where('niveau', 'Faible')->count() }}</h4>
                                            <p class="mb-0">Faibles</p>
                                            <small>Basse priorité</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Alertes par Type</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-danger">Non-conformité</h4>
                                            <p class="mb-0">{{ $alertes->where('type', 'Non-conformité')->count() }}</p>
                                            <small>Violations</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">Risque</h4>
                                            <p class="mb-0">{{ $alertes->where('type', 'Risque')->count() }}</p>
                                            <small>Menaces</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Alertes par Statut</h6>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-danger">Nouveau</h4>
                                            <p class="mb-0">{{ $alertes->where('statut', 'nouveau')->count() }}</p>
                                            <small>Non traité</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">En attente</h4>
                                            <p class="mb-0">{{ $alertes->where('statut', 'en_attente')->count() }}</p>
                                            <small>En file d'attente</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">En cours</h4>
                                            <p class="mb-0">{{ $alertes->where('statut', 'en_cours')->count() }}</p>
                                            <small>En traitement</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-success">Résolu</h4>
                                            <p class="mb-0">{{ $alertes->where('statut', 'résolu')->count() }}</p>
                                            <small>Traité</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted">Délais de Traitement</h6>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-danger">24 heures</h4>
                                            <p class="mb-0">1 alerte</p>
                                            <small>Critique</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-warning">3 jours</h4>
                                            <p class="mb-0">1 alerte</p>
                                            <small>Élevé</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-info">7 jours</h4>
                                            <p class="mb-0">1 alerte</p>
                                            <small>Élevé</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h4 class="text-secondary">15 jours</h4>
                                            <p class="mb-0">1 alerte</p>
                                            <small>Faible</small>
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
