@extends('layouts.app')

@section('title', 'Employés - Personnel RH - KENAM SERVICES')

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-users-cog me-2"></i>Employés - Personnel RH
            </h2>
            <p class="text-muted mb-0">Gestion des employés et ressources humaines</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rh.employes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvel Employé
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                            <small>Total Employés</small>
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
                            <h4 class="mb-0">{{ $stats['actifs'] ?? 0 }}</h4>
                            <small>Actifs</small>
                        </div>
                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['essai'] ?? 0 }}</h4>
                            <small>En Essai</small>
                        </div>
                        <i class="fas fa-user-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $employes->count() }}</h4>
                            <small>Affichés</small>
                        </div>
                        <i class="fas fa-list fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('rh.employes.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Nom, Prénoms, Matricule...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="">Tous</option>
                            <option value="ACTIF" {{ request('statut') == 'ACTIF' ? 'selected' : '' }}>Actif</option>
                            <option value="ESSAI" {{ request('statut') == 'ESSAI' ? 'selected' : '' }}>Essai</option>
                            <option value="CONGE" {{ request('statut') == 'CONGE' ? 'selected' : '' }}>Congé</option>
                            <option value="SUSPENDU" {{ request('statut') == 'SUSPENDU' ? 'selected' : '' }}>Suspendu</option>
                            <option value="DEPART" {{ request('statut') == 'DEPART' ? 'selected' : '' }}>Départ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Service</label>
                        <input type="text" name="service" class="form-control"
                               value="{{ request('service') }}" placeholder="Service...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('rh.employes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des employés -->
    <div class="card">
        <div class="card-body">
            @if($employes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom & Prénoms</th>
                                <th>Grade</th>
                                <th>Poste</th>
                                <th>Service</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Date Embauche</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employes as $employe)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $employe->matricule }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($employe->photo_profil)
                                            <img src="{{ asset('storage/' . $employe->photo_profil) }}"
                                                 alt="Photo" class="rounded-circle me-2" width="32" height="32">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $employe->nom }} {{ $employe->prenoms }}</div>
                                            @if($employe->email_personnel)
                                                <small class="text-muted">{{ $employe->email_personnel }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $employe->grade ?? 'Non défini' }}</span>
                                </td>
                                <td>{{ $employe->poste }}</td>
                                <td>{{ $employe->service }}</td>
                                <td>{{ $employe->telephone_principal }}</td>
                                <td>
                                    @switch($employe->statut)
                                        @case('ACTIF')
                                            <span class="badge bg-success">Actif</span>
                                            @break
                                        @case('ESSAI')
                                            <span class="badge bg-warning">Essai</span>
                                            @break
                                        @case('CONGE')
                                            <span class="badge bg-info">Congé</span>
                                            @break
                                        @case('SUSPENDU')
                                            <span class="badge bg-danger">Suspendu</span>
                                            @break
                                        @case('DEPART')
                                            <span class="badge bg-secondary">Départ</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $employe->statut }}</span>
                                    @endswitch
                                </td>
                                <td>{{ \Carbon\Carbon::parse($employe->date_embauche)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('rh.employes.show', $employe->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rh.employes.edit', $employe->id) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('rh.employes.destroy', $employe->id) }}"
                                              class="d-inline" onsubmit="return confirm('Supprimer cet employé ?')">
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
                            Affichage de {{ $employes->firstItem() }} à {{ $employes->lastItem() }}
                            sur {{ $employes->total() }} employés
                        </small>
                    </div>
                    <div>
                        {{ $employes->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun employé trouvé</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'statut', 'service']))
                            Essayez de modifier vos filtres de recherche.
                        @else
                            Commencez par ajouter votre premier employé.
                        @endif
                    </p>
                    <a href="{{ route('rh.employes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Ajouter un Employé
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
