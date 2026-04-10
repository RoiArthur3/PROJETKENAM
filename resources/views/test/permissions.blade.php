@extends('layouts.app')

@section('title', 'Test des Permissions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt me-2"></i>
                        Test des Permissions par Rôle
                    </h3>
                </div>
                <div class="card-body">
                    @if(auth()->check())
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-user me-2"></i>Utilisateur Connecté</h5>
                                    <p><strong>Nom:</strong> {{ auth()->user()->name }}</p>
                                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                    <p><strong>Téléphone:</strong> {{ auth()->user()->phone }}</p>
                                    <p><strong>Rôle:</strong> 
                                        <span class="badge bg-{{ getRoleBadgeClass(auth()->user()->role) }}">
                                            {{ strtoupper(auth()->user()->role) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-key me-2"></i>Permissions</h5>
                                    <p><strong>Accès Paramétrage:</strong> 
                                        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                                            <span class="badge bg-success">AUTORISÉ</span>
                                        @else
                                            <span class="badge bg-danger">REFUSÉ</span>
                                        @endif
                                    </p>
                                    <p><strong>Admin/Superadmin:</strong> 
                                        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                                            <span class="badge bg-success">OUI</span>
                                        @else
                                            <span class="badge bg-warning">NON</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4><i class="fas fa-modules me-2"></i>Modules Accessibles</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card {{ auth()->user()->role === 'superadmin' ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body text-center">
                                            <i class="fas fa-tachometer-alt fa-2x mb-2 {{ auth()->user()->role === 'superadmin' ? 'text-success' : 'text-muted' }}"></i>
                                            <h6>Dashboard</h6>
                                            @if(auth()->user()->role === 'superadmin')
                                                <span class="badge bg-success">Accès</span>
                                            @else
                                                <span class="badge bg-secondary">Limité</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <i class="fas fa-cogs fa-2x mb-2 text-success"></i>
                                            <h6>Opérations</h6>
                                            <span class="badge bg-success">Accès</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                            <h6>Validations</h6>
                                            <span class="badge bg-success">Accès</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card {{ in_array(auth()->user()->role, ['admin', 'superadmin']) ? 'border-success' : 'border-danger' }}">
                                        <div class="card-body text-center">
                                            <i class="fas fa-cog fa-2x mb-2 {{ in_array(auth()->user()->role, ['admin', 'superadmin']) ? 'text-success' : 'text-danger' }}"></i>
                                            <h6>Paramétrage</h6>
                                            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                                                <span class="badge bg-success">Accès</span>
                                            @else
                                                <span class="badge bg-danger">Refusé</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4><i class="fas fa-link me-2"></i>Liens de Test</h4>
                            <div class="list-group">
                                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Dashboard Principal 
                                    @if(auth()->user()->role !== 'superadmin')
                                        <span class="badge bg-warning float-end">Redirection</span>
                                    @endif
                                </a>
                                <a href="{{ route('parametrage.index') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-cog me-2"></i>
                                    Paramétrage 
                                    @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                                        <span class="badge bg-success float-end">Accès</span>
                                    @else
                                        <span class="badge bg-danger float-end">403</span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.comptes.users.create') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Création Utilisateur
                                    @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                                        <span class="badge bg-success float-end">Accès</span>
                                    @else
                                        <span class="badge bg-danger float-end">403</span>
                                    @endif
                                </a>
                                <a href="{{ route('operations.dashboard') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-cogs me-2"></i>
                                    Opérations Dashboard
                                    <span class="badge bg-success float-end">Accès</span>
                                </a>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4><i class="fas fa-users me-2"></i>Créer Utilisateurs de Test</h4>
                            <p class="text-muted">Créez des utilisateurs de test pour chaque rôle et testez leurs permissions.</p>
                            <button onclick="createTestUsers()" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                Créer les Utilisateurs de Test
                            </button>
                            <div id="test-users-result" class="mt-3"></div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Vous devez être connecté pour tester les permissions.
                            <a href="{{ route('login') }}" class="btn btn-primary ms-2">Se connecter</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createTestUsers() {
    fetch('{{ route("test.create-users") }}')
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('test-users-result');
            if (data.users) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle me-2"></i>Utilisateurs créés:</h6>
                        <ul class="mb-0">
                            ${data.users.map(user => `<li>${user}</li>`).join('')}
                        </ul>
                        <hr>
                        <p class="mb-0"><strong>Mot de passe pour tous:</strong> password123</p>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Erreur lors de la création: ${data.message || 'Erreur inconnue'}
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('test-users-result').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erreur: ${error.message}
                </div>
            `;
        });
}
</script>

@php
function getRoleBadgeClass($role) {
    switch($role) {
        case 'superadmin': return 'danger';
        case 'admin': return 'warning';
        case 'moderator': return 'info';
        case 'agent': return 'primary';
        default: return 'secondary';
    }
}
@endphp
@endpush
