@extends('layouts.app')

@section('title', 'Commercial - Clients | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec encodage UTF-8 explicite -->
    <meta charset="UTF-8">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Liste des clients
                    </h4>
                    <a href="{{ route('commercial.clients.create') }}" class="btn btn-light">
                        <i class="fas fa-plus me-1"></i>
                        Nouveau client
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filtres de recherche -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Rechercher un client..." id="searchInput">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="typeFilter">
                                <option value="">Tous les types</option>
                                <option value="particulier">Particulier</option>
                                <option value="professionnel">Professionnel</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="statutFilter">
                                <option value="">Tous les statuts</option>
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" onclick="filterClients()">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary" onclick="resetFilters()">
                                <i class="fas fa-redo"></i> Réinitialiser
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="table-success">
                                <tr>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Ville</th>
                                    <th>Catégorie</th>
                                    <th>Chiffre d'affaires</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($clients ?? []) as $client)
                                    <tr>
                                        <td>{{ $client->id ?? '-' }}</td>
                                        <td>{{ $client->nom ?? '-' }}</td>
                                        <td>{{ $client->contact ?? $client->nom ?? '-' }}</td>
                                        <td>{{ $client->email ?? '-' }}</td>
                                        <td>{{ $client->telephone ?? '-' }}</td>
                                        <td>{{ $client->ville ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $client->type == 'professionnel' ? 'primary' : 'info' }}">
                                                {{ $client->type ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($client->chiffre_affaires ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('commercial.clients.show', $client->id) }}" class="btn btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('commercial.clients.edit', $client->id) }}" class="btn btn-outline-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-outline-danger" onclick="deleteClient({{ $client->id }})" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun client trouvé</h5>
                                            <p class="text-muted">Commencez par ajouter votre premier client</p>
                                            <a href="{{ route('commercial.clients.create') }}" class="btn btn-success">
                                                <i class="fas fa-plus me-2"></i>Ajouter un client
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($clients) && method_exists($clients, 'links'))
                        <div class="d-flex justify-content-center mt-4">
                            {{ $clients->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction de recherche avec gestion de l'encodage
function filterClients() {
    const search = document.getElementById('searchInput').value;
    const type = document.getElementById('typeFilter').value;
    const statut = document.getElementById('statutFilter').value;

    // Encoder les paramètres pour l'URL
    const params = new URLSearchParams();
    if (search) params.append('search', encodeURIComponent(search));
    if (type) params.append('type', type);
    if (statut) params.append('statut', statut);

    window.location.href = '{{ route("commercial.clients.index") }}?' + params.toString();
}

function resetFilters() {
    window.location.href = '{{ route("commercial.clients.index") }}';
}

function deleteClient(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
        // Implémenter la suppression
        console.log('Suppression du client:', id);
    }
}

// Recherche en temps réel
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchValue = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>
@endsection
