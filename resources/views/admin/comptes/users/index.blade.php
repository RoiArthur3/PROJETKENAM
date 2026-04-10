@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="m-0">
                        <i class="fas fa-users me-2"></i>
                        Gestion des Utilisateurs
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Bouton d'ajout -->
                    <div class="mb-3">
                        <a href="{{ route('admin.comptes.users.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Ajouter un utilisateur
                        </a>
                    </div>

                    <!-- Tableau des utilisateurs -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Téléphone</th>
                                    <th>Service</th>
                                    <th>Statut</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
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
                                        <td>{{ $user->telephone ?? '-' }}</td>
                                        <td>{{ $user->service->name ?? '-' }}</td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.comptes.users.show', $user->id) }}" class="btn btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.comptes.users.edit', $user->id) }}" class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->role !== 'admin' && $user->role !== 'superadmin')
                                                <form action="{{ route('admin.comptes.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement cet utilisateur ?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucun utilisateur trouvé.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
