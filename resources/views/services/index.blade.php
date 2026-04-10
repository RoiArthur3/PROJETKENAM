@extends('layouts.app')

@section('title', 'Services Opérationnels | KENAM SERVICES')

@section('content')
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

<x-list-layout
    title="Gestion des Services Opérationnels"
    icon="fa-building"
    createRoute="admin.services.create"
    createLabel="Ajouter un Service"
>

    <x-slot name="kpis">
        <x-kpi-card
            title="Total Services"
            :value="$services->count()"
            icon="fa-building"
            color="primary"
            subtitle="Tous services confondus"
        />
        <x-kpi-card
            title="Services Actifs"
            :value="$services->where('actif', true)->count()"
            icon="fa-check-circle"
            color="success"
            subtitle="En activité"
        />
        <x-kpi-card
            title="Emails Configurés"
            :value="$services->filter(fn($s) => !empty($s->email))->count()"
            icon="fa-envelope"
            color="info"
            subtitle="Avec email"
        />
        <x-kpi-card
            title="Services Inactifs"
            :value="$services->where('actif', false)->count()"
            icon="fa-exclamation-triangle"
            color="warning"
            subtitle="Désactivés"
        />
    </x-slot>

    <x-slot name="filters">
        <div class="col-md-3">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous les statuts</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Email</label>
            <select name="email" class="form-select">
                <option value="">Tous</option>
                <option value="configured">Configurés</option>
                <option value="not_configured">Non configurés</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Service</th>
            <th>Email</th>
            <th>Responsable</th>
            <th>Téléphone</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($services as $service)
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="{{ $service->icone ?? 'fas fa-building' }} fa-lg" style="color: {{ $service->couleur ?? '#007bff' }};"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $service->nom }}</div>
                        <small class="text-muted">{{ $service->code }}</small>
                    </div>
                </div>
            </td>
            <td>
                <div>
                    <i class="fas fa-envelope me-2 text-muted"></i>
                    {{ $service->email ?? 'Non configuré' }}
                </div>
                @if($service->email)
                    <small class="text-muted">
                        <i class="fas fa-lock me-1"></i>
                        Mot de passe: 12345678
                    </small>
                @endif
            </td>
            <td>{{ $service->responsable ?? 'Non défini' }}</td>
            <td>{{ $service->telephone ?? 'Non défini' }}</td>
            <td>
                <span class="badge bg-{{ $service->actif ? 'success' : 'warning' }}">
                    {{ $service->actif ? 'Actif' : 'Inactif' }}
                </span>
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('services.show', $service->id) }}" class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('services.edit', $service->id) }}" class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('services.destroy', $service->id) }}" method="POST" onsubmit="return deleteService(event, {{ $service->id }}, '{{ $service->nom }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-4">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <div class="text-muted">Aucun service trouvé</div>
                <a href="{{ route('services.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus"></i> Ajouter un service
                </a>
            </td>
        </tr>
        @endforelse
    </tbody>

</x-list-layout>

<script>
function deleteService(event, serviceId, serviceName) {
    event.preventDefault();

    if (!confirm(`Êtes-vous sûr de vouloir supprimer le service "${serviceName}" ?`)) {
        return false;
    }

    const form = event.target;
    const formData = new FormData(form);

    // Désactiver le bouton pendant la suppression
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.redirected) {
            // Si le serveur redirige, suivre la redirection
            window.location.href = response.url;
            return;
        }

        if (!response.ok) {
            throw new Error('Erreur lors de la suppression');
        }

        return response.text();
    })
    .then(html => {
        // Si tout s'est bien passé, recharger la page
        window.location.reload();
    })
    .catch(error => {
        console.error('Erreur:', error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;

        // Afficher un message d'erreur
        alert('Erreur lors de la suppression du service. Veuillez réessayer.');
    });

    return false;
}
</script>
@endsection
