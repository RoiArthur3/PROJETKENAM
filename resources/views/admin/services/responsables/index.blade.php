@extends('layouts.app')

@section('title', 'Responsables de Services | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users-cog mr-2 text-primary"></i>Responsables de Services
            </h1>
            <p class="text-muted">Gestion des comptes responsables des services</p>
        </div>
        <div class="col-auto">
            <form action="{{ route('admin.services.responsables.generate-all') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning" onclick="return confirm('Créer des comptes pour tous les services sans responsable?')">
                    <i class="fas fa-magic mr-1"></i>Générer tous les comptes
                </button>
            </form>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i>Retour aux services
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $services->total() }}</h4>
                            <small>Total services</small>
                        </div>
                        <i class="fas fa-building fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $services->where('responsable', '!==', null)->count() }}</h4>
                            <small>Avec responsable</small>
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
                            <h4 class="mb-0">{{ $services->whereNull('responsable')->count() }}</h4>
                            <small>Sans responsable</small>
                        </div>
                        <i class="fas fa-user-times fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $services->filter(fn($s) => $s->responsable && $s->responsable->is_active)->count() }}</h4>
                            <small>Comptes actifs</small>
                        </div>
                        <i class="fas fa-user-shield fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des services -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des services et leurs responsables</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Service</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Responsable</th>
                            <th>Statut compte</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                        <tr>
                            <td>
                                <strong>{{ $service->nom }}</strong>
                                @if($service->code)
                                    <br><small class="text-muted">Code: {{ $service->code }}</small>
                                @endif
                            </td>
                            <td>
                                @if($service->responsable_email)
                                    {{ $service->responsable_email }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($service->responsable_telephone)
                                    {{ $service->responsable_telephone }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($service->responsable)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm mr-2">
                                            <div class="avatar-title bg-primary rounded-circle">
                                                {{ strtoupper(substr($service->responsable->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $service->responsable->name }}</strong><br>
                                            <small class="text-muted">{{ $service->responsable->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                @if($service->responsable)
                                    @if($service->responsable->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Aucun compte</span>
                                @endif
                            </td>
                            <td>
                                @if($service->responsable_compte_cree_le)
                                    {{ $service->responsable_compte_cree_le->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($service->responsable)
                                        <!-- Modifier mot de passe -->
                                        <a href="{{ route('admin.services.responsables.edit-password', $service) }}"
                                           class="btn btn-sm btn-outline-primary" title="Modifier mot de passe">
                                            <i class="fas fa-key"></i>
                                        </a>

                                        <!-- Activer/Désactiver -->
                                        <form action="{{ route('admin.services.responsables.toggle', $service) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $service->responsable->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $service->responsable->is_active ? 'Désactiver' : 'Activer' }} le compte">
                                                <i class="fas {{ $service->responsable->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button>
                                        </form>

                                        <!-- Supprimer -->
                                        <form action="{{ route('admin.services.responsables.destroy', $service) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Supprimer le compte responsable de {{ $service->nom }}?')"
                                                    title="Supprimer le compte">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Créer un compte -->
                                        <a href="{{ route('admin.services.responsables.create', $service) }}"
                                           class="btn btn-sm btn-primary" title="Créer un compte responsable">
                                            <i class="fas fa-plus"></i> Créer
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Affichage de {{ $services->firstItem() }} à {{ $services->lastItem() }} sur {{ $services->total() }} services
                </div>
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
