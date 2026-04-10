@extends('layouts.app')

@section('title', 'Détails de l\'Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="m-0">
                        <i class="fas fa-user me-2"></i>
                        Détails de l'Utilisateur
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Informations principales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary">Informations générales</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID :</strong></td>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nom complet :</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email :</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rôle :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ match($user->role) {
                                            'superadmin' => 'danger',
                                            'admin' => 'warning',
                                            'moderator' => 'info',
                                            'agent' => 'primary',
                                            'user' => 'secondary',
                                            default => 'secondary'
                                        } }}">
                                            {{ strtoupper($user->role) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Téléphone :</strong></td>
                                    <td>{{ $user->telephone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Service :</strong></td>
                                    <td>{{ $user->service->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary">Informations système</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Date de création :</strong></td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dernière mise à jour :</strong></td>
                                    <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email vérifié :</strong></td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">{{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="badge bg-warning">Non vérifié</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Date d'embauche :</strong></td>
                                    <td>{{ $user->date_embauche ? $user->date_embauche->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Contrat :</strong></td>
                                    <td>{{ $user->contrat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Salaire :</strong></td>
                                    <td>{{ $user->salaire ? number_format($user->salaire, 0, ',', ' ') . ' FCFA' : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">Permissions d'accès</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-secondary">Modules principaux</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_dashboard ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Tableau de bord</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_operations ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Opérations</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_hr ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Ressources Humaines</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_fleet ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Parc auto</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-secondary">Modules gestion</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_suppliers ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Fournisseurs</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_warehouse ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Entrepôt</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_accounting ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Comptabilité</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_invoicing ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Facturation</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-secondary">Modules avancés</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_reporting ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Rapports</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_commercial ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Commercial</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_services ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Services</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $user->can_access_system ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">Système</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.comptes.users.edit', $user->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>
                                    Modifier
                                </a>
                                <a href="{{ route('admin.comptes.users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Retour à la liste
                                </a>
                                @if(!$user->hasRole('admin') && !$user->hasRole('superadmin'))
                                <form action="{{ route('admin.comptes.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                                        <i class="fas fa-trash me-2"></i>
                                        Supprimer
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
