@extends('layouts.app')

@section('title', 'Test des Permissions - KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Test des Permissions des Utilisateurs
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('test.permissions.create-users') }}">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Créer les Utilisateurs de Test
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="text-end">
                                <small class="text-muted">
                                    Mot de passe pour tous les comptes de test: <code>password123</code>
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Modules Accessibles</th>
                                    <th>Module Principal</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'manager' ? 'warning' : 'info') }}">
                                            {{ $user->getRoleDisplayName() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($user->getAccessibleModules() as $module => $name)
                                                <span class="badge bg-secondary">{{ $name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->getPrimaryModule())
                                            <span class="badge bg-primary">{{ $user->getPrimaryModule() }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('test.permissions.test-user', $user) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i> Tester
                                            </a>
                                            <a href="{{ route('login') }}?test_user={{ $user->id }}" class="btn btn-outline-success">
                                                <i class="fas fa-sign-in-alt"></i> Connexion
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>
@endsection
