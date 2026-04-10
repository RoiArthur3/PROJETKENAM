@extends('layouts.app')

@section('title', 'Détails Agent - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user me-2 text-primary"></i>Détails de l'Agent
            </h1>
            <p class="text-muted mb-0">{{ $agent->nom_complet }}</p>
        </div>
        <div>
            <a href="{{ route('admin.agents.edit', $agent->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Informations Personnelles
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Matricule :</strong></td>
                            <td>{{ $agent->matricule }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nom complet :</strong></td>
                            <td>{{ $agent->nom_complet }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email :</strong></td>
                            <td>{{ $agent->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Téléphone :</strong></td>
                            <td>{{ $agent->telephone ?? 'Non renseigné' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Statut :</strong></td>
                            <td>
                                @if($agent->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-danger">Inactif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-building me-2"></i>Service Assigné
                    </h6>
                </div>
                <div class="card-body">
                    @if($agent->service)
                        <div class="text-center">
                            <div class="avatar-lg bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                            <h5 class="font-weight-bold">{{ $agent->service->nom }}</h5>
                            <p class="text-muted">{{ $agent->service->description ?? 'Aucune description' }}</p>
                            <span class="badge bg-info">{{ $agent->service->responsable ? $agent->service->responsable->nom_complet : 'Aucun responsable' }}</span>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <div>Aucun service assigné</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informations système -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-clock me-2"></i>Informations Système
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Créé le :</strong></td>
                            <td>{{ $agent->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dernière modification :</strong></td>
                            <td>{{ $agent->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dernière connexion :</strong></td>
                            <td>
                                @if($agent->last_login)
                                    {{ $agent->last_login->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Jamais connecté</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-tools me-2"></i>Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('admin.agents.toggle-status', $agent->id) }}"
                              onsubmit="return confirm('Êtes-vous sûr de changer le statut?')">
                            @csrf
                            <button type="submit" class="btn btn-{{ $agent->is_active ? 'danger' : 'success' }} btn-sm w-100">
                                <i class="fas fa-{{ $agent->is_active ? 'ban' : 'check' }} me-1"></i>
                                {{ $agent->is_active ? 'Désactiver' : 'Activer' }} l'agent
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.agents.reset-password', $agent->id) }}"
                              onsubmit="return confirm('Réinitialiser le mot de passe?')">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm w-100">
                                <i class="fas fa-key me-1"></i>Réinitialiser le mot de passe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requêtes récentes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-list me-2"></i>Requêtes Récentes
            </h6>
        </div>
        <div class="card-body">
            @if($agent->requetes && $agent->requetes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agent->requetes as $requete)
                                <tr>
                                    <td>{{ $requete->reference }}</td>
                                    <td>{{ $requete->type ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $requete->statut_color ?? 'secondary' }}">
                                            {{ $requete->statut_label ?? $requete->statut }}
                                        </span>
                                    </td>
                                    <td>{{ $requete->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                    <div>Aucune requête trouvée pour cet agent</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
