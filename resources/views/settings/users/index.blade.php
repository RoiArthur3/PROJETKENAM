@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2 text-primary"></i>Gestion des Utilisateurs
            </h1>
            <p class="text-muted mb-0">Administration des comptes utilisateur</p>
        </div>
        <a href="{{ route('settings.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nouvel Utilisateur
        </a>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Rechercher par nom ou email..." id="search-input">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="role-filter">
                        <option value="">Tous les rôles</option>
                        <option value="admin">Administrateur</option>
                        <option value="rh">RH</option>
                        <option value="agent">Agent</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="status-filter">
                        <option value="">Tous les statuts</option>
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                        <i class="fas fa-undo me-1"></i>Réinitialiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôles</th>
                            <th>Dernière connexion</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role)
                                        @php
                                            $roleNames = [
                                                'admin' => 'Administrateur',
                                                'superadmin' => 'Super Admin',
                                                'user' => 'Utilisateur'
                                            ];
                                            $roleClass = [
                                                'admin' => 'danger',
                                                'superadmin' => 'danger',
                                                'user' => 'primary'
                                            ];
                                            $displayName = $roleNames[$user->role] ?? ucfirst($user->role);
                                            $class = $roleClass[$user->role] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $class }}">{{ $displayName }}</span>
                                    @else
                                        <span class="text-muted">Aucun rôle</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Jamais connecté</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $user->is_active ?? true ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $user->is_active ?? true ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('settings.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                    onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>Aucun utilisateur trouvé</p>
                                        <a href="{{ route('settings.users.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Créer le premier utilisateur
                                        </a>
                                    </div>
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="user-name"></strong> ?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId, userName) {
    document.getElementById('user-name').textContent = userName;
    document.getElementById('delete-form').action = '{{ route("settings.users.index") }}/' + userId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function resetFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('role-filter').value = '';
    document.getElementById('status-filter').value = '';
    // TODO: Implementer le filtrage
}
</script>
@endsection
