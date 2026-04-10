@extends('layouts.app')

@section('title', 'RH - Gestion des Agents | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Gestion des Agents"
    icon="fa-users"
    :createRoute="auth()->user()->hasRole('rh') ? route('rh.employes.create') : null"
    createText="Ajouter Agent"
    exportRoute="auth()->user()->hasRole('rh') ? route('rh.agents.export') : null"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total Agents"
            :value="$agents->total()"
            icon="fa-users"
            color="primary"
            subtitle="Tous services confondus"
        />
        <x-kpi-card
            title="Agents Actifs"
            :value="$agents->where('is_active', true)->count()"
            icon="fa-check-circle"
            color="success"
            subtitle="En activité"
        />
        <x-kpi-card
            title="En Essai"
            value="2"
            icon="fa-clock"
            color="warning"
            subtitle="Période probatoire"
        />
        <x-kpi-card
            title="Départ Prévu"
            value="1"
            icon="fa-sign-out-alt"
            color="danger"
            subtitle="Dans les 3 mois"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="col-md-3">
            <label class="form-label">Service</label>
            <select name="service" class="form-select">
                <option value="">Tous les services</option>
                <option>Logistique</option>
                <option>Entretien</option>
                <option>Administration</option>
                <option>Commercial</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous les statuts</option>
                <option>Actif</option>
                <option>En essai</option>
                <option>En congé</option>
                <option>Démission</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Contrat</label>
            <select name="contrat" class="form-select">
                <option value="">Tous les contrats</option>
                <option>CDI</option>
                <option>CDD</option>
                <option>Intérim</option>
                <option>Stage</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Agent</th>
            <th>Service</th>
            <th>Poste</th>
            <th>Contrat</th>
            <th>Date Embauche</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($agents as $agent)
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                        {{ strtoupper(substr($agent->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $agent->name }}</div>
                        <small class="text-muted">{{ $agent->email }}</small>
                    </div>
                </div>
            </td>
            <td>
                <span class="badge bg-primary">{{ $agent->department ?? 'Non défini' }}</span>
            </td>
            <td>{{ $agent->role_display_name ?? $agent->role }}</td>
            <td>
                <span class="badge bg-success">CDI</span>
            </td>
            <td>{{ $agent->created_at->format('d/m/Y') }}</td>
            <td>
                <span class="badge bg-success">Actif</span>
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    @if(auth()->user()->canAccessModule('rh'))
                        <a href="{{ route('rh.agents.show', $agent->id) }}" class="btn btn-outline-primary" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('rh.agents.edit', $agent->id) }}" class="btn btn-outline-warning" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('rh.agents.destroy', $agent->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cet agent ?');">
                            @csrf
                            @method('DELETE')
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
            <td colspan="7" class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <div class="text-muted">Aucun agent trouvé</div>
                @if(auth()->user()->canAccessModule('rh'))
                    <a href="{{ route('rh.employes.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus"></i> Ajouter un agent
                    </a>
                @endif
            </td>
        </tr>
        @endforelse
    </tbody>

</x-list-layout>
@endsection
