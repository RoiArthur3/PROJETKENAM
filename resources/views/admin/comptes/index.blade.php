@extends('layouts.app')

@section('title', 'Gestion des Comptes | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Gestion des Comptes"
    icon="fa-users-cog"
    createRoute="admin.comptes.users.create"
    createText="Nouvel utilisateur"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total utilisateurs"
            :value="$stats['total_users']"
            icon="fa-users"
            color="primary"
            subtitle="Inscrits"
        />
        <x-kpi-card
            title="Utilisateurs actifs"
            :value="$stats['active_users']"
            icon="fa-user-check"
            color="success"
            subtitle="{{ round($stats['active_users'] / max($stats['total_users'], 1) * 100, 1) }}% du total"
        />
        <x-kpi-card
            title="Administrateurs"
            :value="$stats['admins']"
            icon="fa-user-shield"
            color="warning"
            subtitle="Privilèges élevés"
        />
        <x-kpi-card
            title="Super admins"
            :value="$stats['superadmins']"
            icon="fa-user-cog"
            color="info"
            subtitle="Accès complet"
        />
        <x-kpi-card
            title="Modérateurs"
            :value="$stats['moderators'] ?? 0"
            icon="fa-user-tie"
            color="secondary"
            subtitle="Liés aux services"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Recherche</label>
                <input type="text" name="search" class="form-control" placeholder="Nom, email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select">
                    <option value="">Tous les rôles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="moderator" {{ request('role') == 'moderator' ? 'selected' : '' }}>Modérateur</option>
                    <option value="agent" {{ request('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Service</label>
                <select name="service" class="form-select">
                    <option value="">Tous les services</option>
                    @foreach(\App\Models\Service::orderBy('nom')->get() as $service)
                        <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>
                            {{ $service->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Utilisateur</th>
            <th>Email</th>
            <th>Rôles</th>
            <th>Services</th>
            <th>Modules</th>
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
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <small class="text-muted">ID: {{ $user->id }}</small>
                        </div>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->role)
                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'superadmin' ? 'warning' : ($user->role == 'moderator' ? 'info' : 'secondary')) }} me-1">
                            {{ ucfirst($user->role) }}
                        </span>
                    @else
                        <span class="text-muted">Aucun rôle</span>
                    @endif
                </td>
                <td>
                    @if($user->service)
                        <span class="badge bg-info me-1">{{ $user->service->nom }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <?php
                    $permissions = \App\Services\UserPermissionService::getUserPermissions($user);
                    $activeModules = is_array($permissions) ? array_keys(array_filter($permissions)) : [];
                    ?>
                    @if(!empty($activeModules))
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($activeModules as $module)
                                @if(isset(\App\Services\UserPermissionService::getAllModules()[$module]))
                                    <span class="badge bg-secondary" title="{{ \App\Services\UserPermissionService::getAllModules()[$module]['label'] }}">
                                        {{ \App\Services\UserPermissionService::getAllModules()[$module]['label'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">Aucun module</span>
                    @endif
                </td>
                <td>
                    @if($user->last_login_at)
                        {{ $user->last_login_at->format('d/m/Y H:i') }}
                    @else
                        <span class="text-muted">Jamais</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.comptes.permissions') }}?user_id={{ $user->id }}"
                           class="btn btn-outline-primary" title="Modifier les permissions">
                            <i class="fas fa-shield-alt"></i>
                        </a>
                        <a href="{{ route('admin.comptes.edit', $user->id) }}"
                           class="btn btn-outline-secondary" title="Modifier le profil" onclick="event.stopPropagation();">
                            <i class="fas fa-user-edit"></i>
                        </a>
                        <a href="{{ route('admin.comptes.edit-credentials', $user->id) }}"
                           class="btn btn-outline-warning" title="Modifier email / téléphone / mot de passe" onclick="event.stopPropagation();">
                            <i class="fas fa-key"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucun utilisateur trouvé</div>
                    <p class="text-muted small">Aucun utilisateur n'a été créé</p>
                    <a href="{{ route('admin.comptes.users.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus"></i> Créer un utilisateur
                    </a>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>
@endsection
