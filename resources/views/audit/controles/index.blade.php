@extends('layouts.app')

@section('title', 'Contrôles d\'Audit - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Contrôles d'Audit</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
            </a>
            <a href="{{ route('audit.controles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Contrôle
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('audit.controles') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tous les types</option>
                            <option value="Interne" {{ request('type') == 'Interne' ? 'selected' : '' }}>Interne</option>
                            <option value="Externe" {{ request('type') == 'Externe' ? 'selected' : '' }}>Externe</option>
                            <option value="Conformité" {{ request('type') == 'Conformité' ? 'selected' : '' }}>Conformité</option>
                            <option value="Processus" {{ request('type') == 'Processus' ? 'selected' : '' }}>Processus</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="departement" class="form-label">Département</label>
                        <select class="form-select" id="departement" name="departement">
                            <option value="">Tous les départements</option>
                            <option value="Finance" {{ request('departement') == 'Finance' ? 'selected' : '' }}>Finance</option>
                            <option value="Ressources Humaines" {{ request('departement') == 'Ressources Humaines' ? 'selected' : '' }}>Ressources Humaines</option>
                            <option value="Informatique" {{ request('departement') == 'Informatique' ? 'selected' : '' }}>Informatique</option>
                            <option value="Logistique" {{ request('departement') == 'Logistique' ? 'selected' : '' }}>Logistique</option>
                            <option value="Commercial" {{ request('departement') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="Production" {{ request('departement') == 'Production' ? 'selected' : '' }}>Production</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select" id="statut" name="statut">
                            <option value="">Tous les statuts</option>
                            <option value="planifié" {{ request('statut') == 'planifié' ? 'selected' : '' }}>Planifié</option>
                            <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="terminé" {{ request('statut') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                            <option value="annulé" {{ request('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des contrôles -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Département</th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Auditeur</th>
                            <th>Statut</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($controles as $controle)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        {{ substr($controle->reference, 0, 2) }}
                                    </div>
                                    <div>
                                        <strong>{{ $controle->reference }}</strong>
                                        <div class="text-muted small">{{ $controle->objectif }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($controle->type == 'Interne')
                                    <span class="badge bg-info">Interne</span>
                                @elseif($controle->type == 'Externe')
                                    <span class="badge bg-warning">Externe</span>
                                @else
                                    <span class="badge bg-secondary">{{ $controle->type }}</span>
                                @endif
                            </td>
                            <td>{{ $controle->departement }}</td>
                            <td>{{ $controle->date_debut->format('d/m/Y') }}</td>
                            <td>{{ $controle->date_fin->format('d/m/Y') }}</td>
                            <td>{{ $controle->auditeur }}</td>
                            <td>
                                @if($controle->statut == 'planifié')
                                    <span class="badge bg-primary">Planifié</span>
                                @elseif($controle->statut == 'en_cours')
                                    <span class="badge bg-warning">En cours</span>
                                @elseif($controle->statut == 'terminé')
                                    <span class="badge bg-success">Terminé</span>
                                @elseif($controle->statut == 'annulé')
                                    <span class="badge bg-danger">Annulé</span>
                                @endif
                            </td>
                            <td>
                                @if($controle->score)
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $controle->score }}%" aria-valuenow="{{ $controle->score }}" aria-valuemin="0" aria-valuemax="100">{{ $controle->score }}%</div>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="#" class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contrôle ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="mb-0">Aucun contrôle d'audit trouvé</p>
                                <a href="{{ route('audit.controles.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus me-2"></i>Créer un contrôle
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($controles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $controles->firstItem() }} à {{ $controles->lastItem() }} sur {{ $controles->total() }} contrôles
                </div>
                <nav aria-label="Pagination">
                    {{ $controles->withQueryString()->links() }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script pour la gestion des interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

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
