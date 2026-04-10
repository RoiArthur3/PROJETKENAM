@extends('layouts.app')

@section('title', 'Terminaux Reconnaissance Faciale - RH')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-camera me-2 text-primary"></i>Terminaux Reconnaissance Faciale
            </h1>
            <p class="text-muted mb-0">Gestion des terminaux Hikvision pour pointage automatique</p>
        </div>
        <div>
            <a href="{{ route('rh.facial-devices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajouter un terminal
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($devices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Série</th>
                                <th>IP:Port</th>
                                <th>Token API</th>
                                <th>Statut</th>
                                <th>Dernière activité</th>
                                <th>Erreur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devices as $device)
                                <tr>
                                    <td><strong>{{ $device->name }}</strong></td>
                                    <td><code>{{ $device->serial_number }}</code></td>
                                    <td>{{ $device->ip_address }}:{{ $device->port }}</td>
                                    <td><code class="text-muted">{{ Str::limit($device->api_token, 12) }}...</code></td>
                                    <td>
                                        @if($device->is_active)
                                            @if($device->last_seen_at && $device->last_seen_at->gt(now()->subMinutes(5)))
                                                <span class="badge bg-success">En ligne</span>
                                            @else
                                                <span class="badge bg-warning">Actif - Inactif</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Désactivé</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($device->last_seen_at)
                                            {{ $device->last_seen_at->diffForHumans() }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($device->last_error)
                                            <span class="text-danger" title="{{ $device->last_error }}">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" title="Régénérer token" onclick="regenerateToken({{ $device->id }})">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Activer/Désactiver" onclick="toggleStatus({{ $device->id }})">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="confirmDelete({{ $device->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun terminal configuré</h5>
                    <p class="text-muted">Ajoutez votre premier terminal Hikvision pour commencer le pointage automatique.</p>
                    <a href="{{ route('rh.facial-devices.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Ajouter un terminal
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function regenerateToken(id) {
    if (confirm('Êtes-vous sûr de vouloir régénérer le token API ?')) {
        fetch(`/rh/facial-devices/${id}/regenerate-token`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }
}

function toggleStatus(id) {
    fetch(`/rh/facial-devices/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    });
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce terminal ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/rh/facial-devices/${id}`;
        form.innerHTML = `<input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="{{ csrf_token() }}">`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
