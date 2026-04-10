@extends('layouts.app')

@section('title', 'Gestion des Clients - Commercial')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users me-2"></i>
            Gestion des Clients
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('commercial.clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Client
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $clients->count() ?? 0 }}</h4>
                            <small>Total Clients</small>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $clients->where('statut', 'actif')->count() ?? 0 }}</h4>
                            <small>Clients Actifs</small>
                        </div>
                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">0</h4>
                            <small>Nouveaux ce mois</small>
                        </div>
                        <i class="fas fa-user-plus fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">0</h4>
                            <small>En retard</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('commercial.clients.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Rechercher un client..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="type">
                            <option value="">Tous les types</option>
                            <option value="particulier" {{ request('type') == 'particulier' ? 'selected' : '' }}>Particulier</option>
                            <option value="entreprise" {{ request('type') == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="statut">
                            <option value="">Tous les statuts</option>
                            <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('commercial.clients.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des clients -->
    <div class="card">
        <div class="card-body">
            @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>
                                        <strong>{{ $client->nom ?? $client->raison_sociale ?? $client->company_name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $client->email ?? '-' }}</td>
                                    <td>{{ $client->telephone ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $client->type == 'entreprise' ? 'primary' : 'secondary' }}">
                                            {{ $client->type ?? 'Non défini' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $client->statut == 'actif' ? 'success' : 'danger' }}">
                                            {{ $client->statut ?? 'Non défini' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($client->created_at)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('commercial.clients.show', $client->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('commercial.clients.edit', $client->id) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('commercial.clients.destroy', $client->id) }}" 
                                                  style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Affichage de {{ $clients->firstItem() }} à {{ $clients->lastItem() }} 
                            sur {{ $clients->total() }} clients
                        </small>
                    </div>
                    <div>
                        {{ $clients->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun client trouvé</h5>
                    <p class="text-muted">
                        @if(request('search') || request('type') || request('statut'))
                            Aucun client ne correspond à vos critères de recherche.
                            <a href="{{ route('commercial.clients.index') }}" class="btn btn-link p-0">Réinitialiser les filtres</a>
                        @else
                            Commencez par ajouter votre premier client.
                            <a href="{{ route('commercial.clients.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Nouveau Client
                            </a>
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
