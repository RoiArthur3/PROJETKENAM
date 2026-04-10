@extends('layouts.app')

@section('title', 'Mes Requêtes - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list-alt me-2"></i>
                Mes Requêtes
            </h1>
            <p class="text-muted mb-0">Historique de toutes vos requêtes</p>
        </div>
        <div>
            <a href="{{ route('agent.requetes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvelle Requête
            </a>
        </div>
    </div>

    @if(isset($stats))
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En attente</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_attente'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">En cours</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_cours'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-spinner fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Clôturées</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['cloturees'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div class="fw-semibold">Raccourcis</div>
                    <div class="text-muted small">Créer une nouvelle requête ou consulter vos requêtes</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('agent.requetes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvelle requête
                    </a>
                    <a href="{{ route('agent.requetes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-1"></i>Rafraîchir la liste
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('agent.requetes.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="ENREGISTREE" {{ request('statut') == 'ENREGISTREE' ? 'selected' : '' }}>Enregistrée</option>
                            <option value="EN_ATTENTE_ENVOI" {{ request('statut') == 'EN_ATTENTE_ENVOI' ? 'selected' : '' }}>En attente d'envoi</option>
                            <option value="ENVOYEE" {{ request('statut') == 'ENVOYEE' ? 'selected' : '' }}>Envoyée</option>
                            <option value="EN_COURS_DE_TRAITEMENT" {{ request('statut') == 'EN_COURS_DE_TRAITEMENT' ? 'selected' : '' }}>En cours de traitement</option>
                            <option value="TRANSFERE" {{ request('statut') == 'TRANSFERE' ? 'selected' : '' }}>Transférée</option>
                            <option value="CLOTUREE" {{ request('statut') == 'CLOTUREE' ? 'selected' : '' }}>Clôturée</option>
                            <option value="REJETEE" {{ request('statut') == 'REJETEE' ? 'selected' : '' }}>Rejetée</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="priorite" class="form-label">Priorité</label>
                        <select name="priorite" id="priorite" class="form-select">
                            <option value="">Toutes les priorités</option>
                            <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                            <option value="moyenne" {{ request('priorite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                            <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                            <option value="urgente" {{ request('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" name="search" id="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Rechercher par objet...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des requêtes -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Liste des Requêtes
            </h6>
        </div>
        <div class="card-body">
            @if($requetes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Objet</th>
                                <th>Type d'opération</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requetes as $requete)
                                <tr>
                                    <td>#{{ $requete->id }}</td>
                                    <td>
                                        <strong>{{ $requete->objet }}</strong>
                                        @if($requete->description)
                                            <br><small class="text-muted">{{ Str::limit($requete->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $requete->operation->libelle ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $requete->statut_label }}</span>
                                    </td>
                                    <td>{{ $requete->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('agent.requetes.show', $requete) }}" class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
                            Affichage de {{ $requetes->firstItem() }} à {{ $requetes->lastItem() }}
                            sur {{ $requetes->total() }} requête(s)
                        </small>
                    </div>
                    <div>
                        {{ $requetes->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-600">Aucune requête trouvée</h4>
                    <p class="text-gray-500">Vous n'avez pas encore créé de requête ou aucun résultat ne correspond à vos critères.</p>
                    <a href="{{ route('agent.requetes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Créer votre première requête
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
