@extends('layouts.app')

@section('title', 'Affectations - RH')

@section('content')
<x-list-layout
    title="Gestion des Affectations"
    icon="fa-users-cog"
    exportRoute="rh.affectations.export"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Agents Affectés"
            value="{{ $total }}"
            icon="fa-user-check"
            color="success"
        />
        <x-kpi-card
            title="Services Actifs"
            value="{{ \App\Models\Service::count() }}"
            icon="fa-building"
            color="info"
        />
        <x-kpi-card
            title="Sans Affectation"
            value="{{ $agents->where('service', null)->count() }}"
            icon="fa-exclamation-triangle"
            color="warning"
        />
        <x-kpi-card
            title="Taux d'Affectation"
            value="{{ $total > 0 ? round((($total - $agents->where('service', null)->count()) / $total) * 100) : 0 }}%"
            icon="fa-percent"
            color="primary"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Recherche</label>
                <input type="text" class="form-control" placeholder="Nom d'agent..." id="searchInput">
            </div>
            <div class="col-md-4">
                <label class="form-label">Service</label>
                <select class="form-select" id="serviceFilter">
                    <option value="">Tous les services</option>
                    @foreach(\App\Models\Service::orderBy('nom')->get() as $service)
                        <option value="{{ $service->id }}">{{ $service->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-search"></i> Filtrer
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
            <th>Rôle</th>
            <th>Service Actuel</th>
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
                        </div>
                    </div>
                </td>
                <td>
                    <a href="mailto:{{ $agent->email }}" class="text-decoration-none">
                        {{ $agent->email }}
                    </a>
                </td>
                <td>
                    <span class="badge bg-secondary">{{ ucfirst($agent->role ?? 'N/A') }}</span>
                </td>
                <td>
                    @if($agent->service)
                        <span class="badge bg-info">{{ $agent->service->nom }}</span>
                    @else
                        <span class="badge bg-warning">Non affecté</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-{{ $agent->actif ? 'success' : 'secondary' }}">
                        {{ $agent->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary"
                                onclick="editAffectation({{ $agent->id }})"
                                title="Modifier l'affectation">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info"
                                onclick="viewAffectation({{ $agent->id }})"
                                title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucune affectation trouvée</div>
                    <button type="button" class="btn btn-primary mt-2" onclick="createAffectation()">
                        <i class="fas fa-plus"></i> Créer une affectation
                    </button>
                </td>
            </tr>
        @endforelse
    </tbody>

</x-list-layout>

<!-- Modal d'édition d'affectation -->
<div class="modal fade" id="affectationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'Affectation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="affectationForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="agentId" name="agent_id">

                    <div class="mb-3">
                        <label class="form-label">Agent</label>
                        <input type="text" class="form-control" id="agentName" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <select class="form-select" id="serviceSelect" name="service_id">
                            <option value="">Aucun service</option>
                            @foreach(\App\Models\Service::orderBy('nom')->get() as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date d'affectation</label>
                        <input type="date" class="form-control" id="affectationDate" name="date_affectation">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Commentaires</label>
                        <textarea class="form-control" id="affectationNotes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const service = document.getElementById('serviceFilter').value;

    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const serviceMatch = !service || row.querySelector('td:nth-child(5)')?.textContent.toLowerCase().includes(service);

        row.style.display = (text.includes(search) && serviceMatch) ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('serviceFilter').value = '';
    applyFilters();
}

function editAffectation(agentId) {
    // Trouver l'agent dans les données (simplifié)
    const agents = @json($agents);
    const agent = agents.find(a => a.id == agentId);

    if (agent) {
        document.getElementById('agentId').value = agent.id;
        document.getElementById('agentName').value = agent.name;
        document.getElementById('serviceSelect').value = agent.service ? agent.service.id : '';
        document.getElementById('affectationDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('affectationNotes').value = '';

        // Set the form action
        document.getElementById('affectationForm').action = `/rh/affectations/${agentId}`;

        new bootstrap.Modal(document.getElementById('affectationModal')).show();
    }
}

function viewAffectation(agentId) {
    // Rediriger vers la page de détails de l'agent
    window.location.href = `/rh/agents/${agentId}`;
}

function createAffectation() {
    window.location.href = '{{ route("rh.affectations.create") }}';
}

// Soumission du formulaire d'affectation
document.getElementById('affectationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/rh/affectations/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
});

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
