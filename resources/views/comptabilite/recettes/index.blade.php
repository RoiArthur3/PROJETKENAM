@extends('layouts.app')

@section('title', 'Recettes - Comptabilité | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-coins me-2 text-success"></i>Gestion des Recettes
        </h1>
        <div class="d-none d-sm-inline-block">
            <a href="{{ route('recettes.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Nouvelle Recette
            </a>
        </div>
    </div>

    <!-- Cartes KPI -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Recettes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalRecettes ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Ce Mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($recettesMois ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $recettesEnAttente ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Moyenne/Mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($moyenneMensuelle ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('recettes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date de début</label>
                    <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date de fin</label>
                    <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="validee" {{ request('statut') == 'validee' ? 'selected' : '' }}>Validée</option>
                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrer
                        </button>
                        <a href="{{ route('recettes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des recettes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Recettes
            </h6>
            <div>
                <button class="btn btn-success btn-sm" onclick="exportRecettes()">
                    <i class="fas fa-download me-1"></i>Exporter
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(isset($recettes) && $recettes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="text-primary">
                            <tr>
                                <th>Référence</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Description</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recettes as $recette)
                                <tr>
                                    <td>{{ $recette->reference ?? 'REC-' . str_pad($recette->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $recette->date_recette ? $recette->date_recette->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $recette->client ?? 'N/A' }}</td>
                                    <td>{{ $recette->description ?? 'N/A' }}</td>
                                    <td class="text-success fw-bold">{{ number_format($recette->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @switch($recette->statut ?? 'en_attente')
                                            @case('validee')
                                                <span class="badge bg-success">Validée</span>
                                                @break
                                            @case('en_attente')
                                                <span class="badge bg-warning">En attente</span>
                                                @break
                                            @case('annulee')
                                                <span class="badge bg-danger">Annulée</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Inconnu</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="showDetails({{ $recette->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" onclick="validerRecette({{ $recette->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="annulerRecette({{ $recette->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(isset($recettes) && method_exists($recettes, 'links'))
                    {{ $recettes->links() }}
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-coins fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune recette trouvée</h5>
                    <p class="text-muted">Commencez par ajouter votre première recette.</p>
                    <a href="{{ route('recettes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Ajouter une recette
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function showDetails(id) {
    // Fonction pour afficher les détails d'une recette
    alert('Détails de la recette #' + id);
}

function validerRecette(id) {
    if (confirm('Valider cette recette ?')) {
        // Rediriger vers la route de validation
        window.location.href = '/recettes/' + id + '/valider';
    }
}

function annulerRecette(id) {
    if (confirm('Annuler cette recette ?')) {
        // Rediriger vers la route d'annulation
        window.location.href = '/recettes/' + id + '/annuler';
    }
}

function exportRecettes() {
    // Exporter les recettes en CSV
    window.location.href = '/recettes/export';
}
</script>
@endsection
