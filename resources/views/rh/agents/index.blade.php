@extends('layouts.app')

@section('title', 'Gestion des Agents - RH')

@section('content')
<x-list-layout
    title="Gestion des Agents"
    icon="fa-users"
    createRoute="rh.employes.create"
    createText="Nouvel Agent"
    exportRoute="rh.agents.export"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total Agents"
            value="{{ $agents->total() }}"
            icon="fa-users"
            color="primary"
        />
        <x-kpi-card
            title="Agents Actifs"
            value="{{ $agents->where('actif', true)->count() }}"
            icon="fa-user-check"
            color="success"
        />
        <x-kpi-card
            title="Services"
            value="{{ \App\Models\Service::count() }}"
            icon="fa-building"
            color="info"
        />
        <x-kpi-card
            title="Présents Aujourd'hui"
            value="0"
            icon="fa-clock"
            color="warning"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Recherche</label>
                <input type="text" class="form-control" placeholder="Nom, email, téléphone..." id="searchInput">
            </div>
            <div class="col-md-3">
                <label class="form-label">Service</label>
                <select class="form-select" id="serviceFilter">
                    <option value="">Tous les services</option>
                    @foreach(\App\Models\Service::orderBy('nom')->get() as $service)
                        <option value="{{ $service->id }}">{{ $service->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>
                <input type="checkbox" class="form-check-input" id="selectAll">
            </th>
            <th>Agent</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Service</th>
            <th>Poste</th>
            <th>Statut</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($agents as $agent)
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $agent->id }}">
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $agent->name }}</div>
                            <small class="text-muted">{{ $agent->id }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="mailto:{{ $agent->email }}" class="text-decoration-none">
                        {{ $agent->email }}
                    </a>
                </td>
                <td>
                    @if($agent->phone)
                        <a href="tel:{{ $agent->phone }}" class="text-decoration-none">
                            {{ $agent->phone }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($agent->service)
                        <span class="badge bg-info">{{ $agent->service->nom }}</span>
                    @else
                        <span class="badge bg-secondary">Non assigné</span>
                    @endif
                </td>
                <td>{{ $agent->role ?? '-' }}</td>
                <td>
                    <span class="badge bg-{{ $agent->actif ? 'success' : 'secondary' }}">
                        {{ $agent->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary"
                                onclick="window.location.href='{{ route('rh.agents.show', $agent) }}'"
                                title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning"
                                onclick="window.location.href='{{ route('rh.agents.edit', $agent) }}'"
                                title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger"
                                onclick="confirmDelete({{ $agent->id }})"
                                title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucun agent trouvé</div>
                    <button type="button" class="btn btn-primary mt-2"
                            onclick="window.location.href='{{ route('rh.employes.create') }}'">
                        <i class="fas fa-plus"></i> Créer le premier agent
                    </button>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

<script>
function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const service = document.getElementById('serviceFilter').value;
    const status = document.getElementById('statusFilter').value;

    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const serviceMatch = !service || row.querySelector('td:nth-child(5)')?.textContent.toLowerCase().includes(service);
        const statusMatch = !status || row.querySelector('td:nth-child(7)')?.textContent.toLowerCase().includes(status === '1' ? 'actif' : 'inactif');

        row.style.display = (text.includes(search) && serviceMatch && statusMatch) ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('serviceFilter').value = '';
    document.getElementById('statusFilter').value = '';
    applyFilters();
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet agent ?')) {
        // Implement delete functionality
        console.log('Delete agent:', id);
    }
}

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
