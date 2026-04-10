@extends('layouts.app')

@section('title', 'Mes Requêtes - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Mes Requêtes</h1>
        <a href="{{ route('operations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Requête
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operations.my.requests') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente">En attente</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Priorité</label>
                        <select name="priorite" class="form-select">
                            <option value="">Toutes les priorités</option>
                            <option value="urgente">Urgente</option>
                            <option value="haute">Haute</option>
                            <option value="moyenne">Moyenne</option>
                            <option value="basse">Basse</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Période</label>
                        <input type="date" name="date_debut" class="form-control" placeholder="Date début">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('operations.my.requests') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des requêtes -->
    <div class="card">
        <div class="card-body">
            @if ($operations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Montant</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Échéance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operations as $operation)
                                <tr>
                                    <td>
                                        <strong>{{ $operation->titre }}</strong>
                                        @if ($operation->description)
                                            <br><small class="text-muted">{{ Str::limit($operation->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge bg-{{ $operation->priorite === 'urgente' ? 'danger' : ($operation->priorite === 'haute' ? 'warning' : ($operation->priorite === 'moyenne' ? 'info' : 'secondary')) }}">
                                            {{ $operation->priorite }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $operation->statut_courant === 'en_attente' ? 'warning' : ($operation->statut_courant === 'en_cours' ? 'info' : ($operation->statut_courant === 'termine' ? 'success' : 'danger')) }}">
                                            {{ $operation->statut_courant }}
                                        </span>
                                    </td>
                                    <td>{{ $operation->date_operation->format('d/m/Y') }}</td>
                                    <td>{{ $operation->echeance ? $operation->echeance->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operations.tracking', $operation->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($operation->statut_courant === 'brouillon')
                                                <a href="{{ route('operations.edit', $operation->id) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $operations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune requête trouvée</h5>
                    <p class="text-muted">Vous n'avez pas encore créé de requête d'opération.</p>
                    <a href="{{ route('operations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer ma première requête
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
