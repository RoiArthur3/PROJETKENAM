@extends('layouts.app')

@section('title', 'Recettes - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Recettes</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="{{ route('comptabilite.depenses.index') }}" class="btn btn-outline-danger">
                <i class="fas fa-arrow-down me-2"></i>Dépenses
            </a>
            <a href="{{ route('comptabilite.rapports.bilan') }}" class="btn btn-outline-primary">
                <i class="fas fa-balance-scale me-2"></i>Bilan
            </a>
            <a href="{{ route('comptabilite.recettes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Recette
            </a>
        </div>
    </div>

    <!-- KPIs Recettes -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Recettes</h6>
                            <h3 class="mb-0">{{ number_format($recettes->sum('montant'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-arrow-up"></i>
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
                            <h6 class="text-muted mb-1">Encaissées</h6>
                            <h3 class="mb-0">{{ $recettes->where('statut', 'encaissée')->count() }}</h3>
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
                            <h6 class="text-muted mb-1">En Attente</h6>
                            <h3 class="mb-0">{{ $recettes->where('statut', 'en_attente')->count() }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-clock"></i>
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
                            <h6 class="text-muted mb-1">Moyenne</h6>
                            <h3 class="mb-0">{{ number_format($recettes->avg('montant'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-calculator"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Recettes -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Catégorie</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recettes as $recette)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-3">
                                        REC
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $recette->reference }}</div>
                                        <div class="text-muted small">ID: {{ $recette->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($recette->date_recette)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($recette->date_recette)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $recette->categorie }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($recette->client, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $recette->client }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">
                                    {{ number_format($recette->montant, 0, ',', ' ') }} FCFA
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $recette->statut == 'encaissée' ? 'success' : 'warning' }}">
                                    {{ ucfirst($recette->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $recette->description }}">
                                    {{ $recette->description }}
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($recette->statut == 'en_attente')
                                        <button class="btn btn-outline-success" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-info" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune recette trouvée</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Ajouter la première recette
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
