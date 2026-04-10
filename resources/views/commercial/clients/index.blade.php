@extends('layouts.app')

@section('title', 'Gestion des Clients - Commercial')

@section('content')
<x-list-layout
    title="Gestion des Clients"
    icon="fa-users"
    createRoute="commercial.clients.create"
    createText="Nouveau Client"
    exportRoute="commercial.clients.export"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total Clients"
            value="{{ $clients->count() ?? 0 }}"
            icon="fa-users"
            color="primary"
        />
        <x-kpi-card
            title="Clients Actifs"
            value="{{ $clients->where('statut', 'actif')->count() ?? 0 }}"
            icon="fa-user-check"
            color="success"
        />
        <x-kpi-card
            title="Nouveaux ce mois"
            value="0"
            icon="fa-user-plus"
            color="info"
        />
        <x-kpi-card
            title="Chiffre d'affaires"
            value="0 FCFA"
            icon="fa-euro-sign"
            color="warning"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nom du client</label>
                <input type="text" class="form-control" id="clientNameFilter" placeholder="Rechercher par nom...">
            </div>
            <div class="col-md-4">
                <label class="form-label">Secteur</label>
                <select class="form-select" id="sectorFilter">
                    <option value="">Tous les secteurs</option>
                    <option value="privé">Secteur privé</option>
                    <option value="public">Secteur public</option>
                    <option value="ngo">ONG</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Statut</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                    <option value="prospect">Prospect</option>
                </select>
            </div>
        </div>
    </x-slot>

    <!-- Tableau des clients -->
    <thead class="table-light">
        <tr>
            <th>Client</th>
            <th>Contact</th>
            <th>Secteur</th>
            <th>Dernière activité</th>
            <th>Statut</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($clients ?? collect() as $client)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $client->nom ?? 'Client Test' }}</div>
                            <small class="text-muted">{{ $client->type ?? 'Entreprise' }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div>
                        <div>{{ $client->contact_nom ?? 'John Doe' }}</div>
                        <small class="text-muted">{{ $client->email ?? 'john@example.com' }}</small>
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary">{{ $client->secteur ?? 'Privé' }}</span>
                </td>
                <td>
                    <small>{{ $client->derniere_activite ?? 'Jamais' }}</small>
                </td>
                <td>
                    @php
                        $statusColors = [
                            'actif' => 'success',
                            'inactif' => 'secondary',
                            'prospect' => 'warning',
                        ];
                        $statusLabels = [
                            'actif' => 'Actif',
                            'inactif' => 'Inactif',
                            'prospect' => 'Prospect',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$client->statut ?? 'actif'] ?? 'secondary' }}">
                        {{ $statusLabels[$client->statut ?? 'actif'] ?? 'Inconnu' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-info"
                                onclick="viewClient({{ $client->id ?? 1 }})"
                                title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning"
                                onclick="editClient({{ $client->id ?? 1 }})"
                                title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary"
                                onclick="viewProjects({{ $client->id ?? 1 }})"
                                title="Projets">
                            <i class="fas fa-project-diagram"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucun client trouvé</div>
                    <button type="button" class="btn btn-primary mt-2"
                            onclick="window.location.href='{{ route('commercial.clients.create') }}'">
                        <i class="fas fa-plus"></i> Créer le premier client
                    </button>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

<script>
function viewClient(id) {
    // Redirect to client show page
    window.location.href = `/commercial/clients/${id}`;
}

function editClient(id) {
    // Redirect to client edit page
    window.location.href = `/commercial/clients/${id}/edit`;
}

function viewProjects(id) {
    // Redirect to client's projects
    window.location.href = `/projets?client_id=${id}`;
}

// Filtres
document.getElementById('clientNameFilter')?.addEventListener('input', applyFilters);
document.getElementById('sectorFilter')?.addEventListener('change', applyFilters);
document.getElementById('statusFilter')?.addEventListener('change', applyFilters);

function applyFilters() {
    const name = document.getElementById('clientNameFilter').value.toLowerCase();
    const sector = document.getElementById('sectorFilter').value;
    const status = document.getElementById('statusFilter').value;

    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; // Skip empty state row

        const rowName = row.cells[0].textContent.toLowerCase();
        const rowSector = row.cells[2].textContent.toLowerCase();
        const rowStatus = row.cells[4].textContent.toLowerCase();

        let show = true;

        if (name && !rowName.includes(name)) show = false;
        if (sector && !rowSector.includes(sector)) show = false;
        if (status && !rowStatus.includes(status)) show = false;

        row.style.display = show ? '' : 'none';
    });
}
</script>
@endsection
