@extends('layouts.app')

@section('title', 'Administration - Détails Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">Détails de l'utilisateur: {{ $user->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informations générales -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Nom complet</label>
                            <p class="form-control-plaintext fw-bold">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Téléphone (connexion)</label>
                            <p class="form-control-plaintext fw-bold">{{ $user->phone ?? 'Non défini' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Adresse email</label>
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rôles et permissions -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tag me-2"></i>Rôles et permissions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Rôles assignés</label>
                        <div>
                            @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary me-2 mb-1">{{ $role->name }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary">Aucun rôle assigné</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-muted">Permissions</label>
                        <div class="row">
                            @php
                                // Afficher les permissions d'accès aux modules (nos booléens)
                                $modulePermissions = [];
                                $moduleFields = [
                                    'dashboard' => 'Tableau de bord',
                                    'operations' => 'Opérations',
                                    'hr' => 'Ressources Humaines',
                                    'fleet' => 'Parc Auto',
                                    'suppliers' => 'Fournisseurs',
                                    'warehouse' => 'Stock/Entrepôt',
                                    'accounting' => 'Comptabilité',
                                    'juridique' => 'Juridique',
                                    'invoicing' => 'Facturation',
                                    'reporting' => 'Rapports',
                                    'commercial' => 'Commercial',
                                    'prospection' => 'Prospection',
                                    'ateliers' => 'Ateliers',
                                    'projects' => 'Projets',
                                    'audit' => 'Audit'
                                ];

                                foreach ($moduleFields as $field => $label) {
                                    $fieldName = 'can_access_' . $field;
                                    if (isset($user->$fieldName) && $user->$fieldName) {
                                        $modulePermissions[] = $label;
                                    }
                                }
                            @endphp

                            @if(empty($modulePermissions))
                                <div class="col-12">
                                    <span class="text-muted small">Aucune permission de module</span>
                                </div>
                            @else
                                <div class="col-12">
                                    @foreach($modulePermissions as $permission)
                                        <span class="badge bg-success me-2 mb-1">{{ $permission }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations système -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations système</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">ID utilisateur</small>
                        <p class="mb-0 fw-bold">{{ $user->id }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Créé le</small>
                        <p class="mb-0">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Dernière modification</small>
                        <p class="mb-0">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <small class="text-muted">Dernière connexion</small>
                        <p class="mb-0">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions rapides</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Modifier l'utilisateur
                        </a>
                    </div>
                    @if(!$user->hasRole('Administrateur'))
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cet utilisateur ? Cette action est irréversible.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Supprimer l'utilisateur
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
