@extends('layouts.app')

@section('title', 'Tableau de Bord Agent - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-tie me-2"></i>
                Tableau de Bord Agent
            </h1>
            <p class="text-muted mb-0">Bienvenue, {{ Auth::user()->nom }}</p>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('agent.requetes.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i>Nouvelle Requête
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="agentDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>{{ Auth::user()->nom }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('agent.profile') }}">
                        <i class="fas fa-user-edit me-2"></i>Mon Profil
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Requêtes En Cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['en_cours'] }}
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Requêtes Approuvées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['cloturees'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Requêtes Rejetées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['rejetees'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                                Total Requêtes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_requetes'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières requêtes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Mes Dernières Requêtes
            </h6>
            <a href="{{ route('agent.requetes.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-list me-1"></i>Voir tout
            </a>
        </div>
        <div class="card-body">
            @if($dernieresRequetes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Opération</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dernieresRequetes as $requete)
                                <tr>
                                    <td>{{ $requete->titre }}</td>
                                    <td>{{ $requete->operation->nom ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{
                                            $requete->priorite === 'urgente' ? 'danger' :
                                            $requete->priorite === 'haute' ? 'warning' :
                                            $requete->priorite === 'moyenne' ? 'info' : 'secondary'
                                        }}">
                                            {{ ucfirst($requete->priorite) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{
                                            $requete->statut === 'approuve' ? 'success' :
                                            $requete->statut === 'rejete' ? 'danger' :
                                            $requete->statut === 'en_cours' ? 'warning' : 'info'
                                        }}">
                                            {{ ucfirst(str_replace('_', ' ', $requete->statut)) }}
                                        </span>
                                    </td>
                                    <td>{{ $requete->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('agent.requetes.show', $requete) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Aucune requête</h5>
                    <p class="text-gray-500">Vous n'avez pas encore créé de requête.</p>
                    <a href="{{ route('agent.requetes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Créer une requête
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('agent.requetes.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus me-2"></i>Nouvelle Requête
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('agent.requetes.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-list me-2"></i>Mes Requêtes
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('agent.profile') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-user-cog me-2"></i>Mon Profil
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-success btn-block" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Imprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Votre service</small>
                        <p class="mb-2 fw-bold">{{ Auth::user()->service->nom ?? 'Non assigné' }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Email</small>
                        <p class="mb-2">{{ Auth::user()->email }}</p>
                    </div>
                    @if(Auth::user()->telephone)
                        <div class="mb-3">
                            <small class="text-muted">Téléphone</small>
                            <p class="mb-2">{{ Auth::user()->telephone }}</p>
                        </div>
                    @endif
                    <div class="mb-0">
                        <small class="text-muted">Dernière connexion</small>
                        <p class="mb-0">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y H:i') : 'Première connexion' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire de déconnexion caché -->
<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

@endsection
