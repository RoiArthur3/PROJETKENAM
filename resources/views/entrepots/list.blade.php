@extends('layouts.app')

@section('title', 'Liste des Entrepôts - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Liste des Entrepôts</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('entrepots.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Entrepôts
            </a>
            <a href="{{ route('entrepots.stock') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes me-2"></i>Stock
            </a>
            <a href="{{ route('entrepots.transferts') }}" class="btn btn-outline-info">
                <i class="fas fa-exchange-alt me-2"></i>Transferts
            </a>
            <a href="{{ route('entrepots.rapports') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-2"></i>Rapports
            </a>
            <a href="{{ route('entrepots.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvel Entrepôt
            </a>
        </div>
    </div>

    <!-- KPIs Entrepôts -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Entrepôts</h6>
                            <h3 class="mb-0">{{ $entrepots->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Capacité Totale</h6>
                            <h3 class="mb-0">{{ number_format($entrepots->sum('capacite') ?? 0, 0, ',', ' ') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-info text-white">
                                <i class="fas fa-database"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Capacité Utilisée</h6>
                            <h3 class="mb-0">{{ number_format($entrepots->sum('capacite_utilisee') ?? 0, 0, ',', ' ') }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Taux Occupation Moyen</h6>
                            <h3 class="mb-0">
                                @if($entrepots->sum('capacite') > 0)
                                    {{ number_format(($entrepots->sum('capacite_utilisee') ?? 0) / ($entrepots->sum('capacite') ?? 1) * 100, 1, ',', ' ') }}%
                                @else
                                    0%
                                @endif
                            </h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Entrepôts -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Adresse</th>
            <th>Capacité</th>
                            <th>Utilisée</th>
                            <th>Taux</th>
                            <th>Responsable</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entrepots as $entrepot)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $entrepot->code }}</div>
                                <div class="text-muted small">ID: {{ $entrepot->id }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $entrepot->nom }}</div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $entrepot->adresse }}">
                                    {{ $entrepot->adresse }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ number_format($entrepot->capacite ?? 0, 0, ',', ' ') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ number_format($entrepot->capacite_utilisee ?? 0, 0, ',', ' ') }}</div>
                            </td>
                            <td>
                                @if($entrepot->capacite > 0)
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $entrepot->taux_occupation > 80 ? 'danger' : ($entrepot->taux_occupation > 60 ? 'warning' : 'success') }}"
                                             style="width: {{ $entrepot->taux_occupation }}%">
                                            {{ number_format($entrepot->taux_occupation, 1, ',', ' ') }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($entrepot->capacite_disponible, 0, ',', ' ') }} disponible</small>
                                @else
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-secondary" style="width: 0%">
                                            0%
                                        </div>
                                    </div>
                                    <small class="text-muted">Non défini</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $entrepot->responsable }}</div>
                            </td>
                            <td>
                                <div class="text-primary">{{ $entrepot->telephone }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $entrepot->statut == 'actif' ? 'success' : 'warning' }}">
                                    {{ ucfirst($entrepot->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="#" class="btn btn-outline-primary" title="Voir" data-bs-toggle="modal" data-bs-target="#viewModal{{ $entrepot->id }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary" title="Modifier" data-bs-toggle="modal" data-bs-target="#editModal{{ $entrepot->id }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('entrepots.stock') }}" class="btn btn-outline-info" title="Stock">
                                        <i class="fas fa-boxes"></i>
                                    </a>
                                    <a href="{{ route('entrepots.transferts') }}" class="btn btn-outline-success" title="Transférer">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun entrepôt trouvé</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Ajouter le premier entrepôt
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Carte des Entrepôts -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Carte des Entrepôts</h5>
                    <div class="row text-center">
                        @if(isset($entrepots) && $entrepots->count() > 0)
                        @foreach($entrepots as $entrepot)
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-primary">{{ $entrepot->nom ?? 'Entrepôt' }}</h6>
                                        <p class="mb-0">{{ $entrepot->adresse ?? 'Adresse non spécifiée' }}</p>
                                        <small>Capacité: {{ $entrepot->capacite ?? 0 }} | Utilisé: {{ $entrepot->espace_utilise ?? 0 }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Aucun entrepôt configuré. Créez des entrepôts pour les voir ici.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<!-- Modal Voir Entrepôt -->
@foreach($entrepots as $entrepot)
<div class="modal fade" id="viewModal{{ $entrepot->id }}" tabindex="-1" aria-labelledby="viewModal{{ $entrepot->id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModal{{ $entrepot->id }}Label">Détails de l'Entrepôt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations Générales</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Code:</strong></td>
                                <td>{{ $entrepot->code }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nom:</strong></td>
                                <td>{{ $entrepot->nom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Adresse:</strong></td>
                                <td>{{ $entrepot->adresse }}</td>
                            </tr>
                            <tr>
                                <td><strong>Statut:</strong></td>
                                <td><span class="badge bg-{{ $entrepot->statut == 'actif' ? 'success' : 'warning' }}">{{ ucfirst($entrepot->statut) }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Capacité et Responsable</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Capacité Totale:</strong></td>
                                <td>{{ number_format($entrepot->capacite, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Capacité Utilisée:</strong></td>
                                <td>{{ number_format($entrepot->capacite_utilisee, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Taux Occupation:</strong></td>
                                <td>{{ number_format(($entrepot->capacite_utilisee / $entrepot->capacite) * 100, 1, ',', ' ') }}%</td>
                            </tr>
                            <tr>
                                <td><strong>Responsable:</strong></td>
                                <td>{{ $entrepot->responsable }}</td>
                            </tr>
                            <tr>
                                <td><strong>Téléphone:</strong></td>
                                <td>{{ $entrepot->telephone }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Progression de l'Occupation</h6>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-{{ $entrepot->capacite_utilisee / $entrepot->capacite > 0.8 ? 'danger' : ($entrepot->capacite_utilisee / $entrepot->capacite > 0.6 ? 'warning' : 'success') }}"
                                 style="width: {{ ($entrepot->capacite_utilisee / $entrepot->capacite) * 100 }}%">
                                {{ number_format(($entrepot->capacite_utilisee / $entrepot->capacite) * 100, 1) }}%
                            </div>
                        </div>
                        <small class="text-muted">{{ $entrepot->capacite_utilisee }} / {{ $entrepot->capacite }} unités</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('entrepots.stock') }}'">Voir le Stock</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modifier Entrepôt -->
<div class="modal fade" id="editModal{{ $entrepot->id }}" tabindex="-1" aria-labelledby="editModal{{ $entrepot->id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModal{{ $entrepot->id }}Label">Modifier l'Entrepôt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom{{ $entrepot->id }}" class="form-label">Nom de l'Entrepôt</label>
                                <input type="text" class="form-control" id="nom{{ $entrepot->id }}" value="{{ $entrepot->nom }}">
                            </div>
                            <div class="mb-3">
                                <label for="code{{ $entrepot->id }}" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code{{ $entrepot->id }}" value="{{ $entrepot->code }}">
                            </div>
                            <div class="mb-3">
                                <label for="adresse{{ $entrepot->id }}" class="form-label">Adresse</label>
                                <textarea class="form-control" id="adresse{{ $entrepot->id }}" rows="2">{{ $entrepot->adresse }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacite{{ $entrepot->id }}" class="form-label">Capacité Totale</label>
                                <input type="number" class="form-control" id="capacite{{ $entrepot->id }}" value="{{ $entrepot->capacite }}">
                            </div>
                            <div class="mb-3">
                                <label for="responsable{{ $entrepot->id }}" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable{{ $entrepot->id }}" value="{{ $entrepot->responsable }}">
                            </div>
                            <div class="mb-3">
                                <label for="telephone{{ $entrepot->id }}" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone{{ $entrepot->id }}" value="{{ $entrepot->telephone }}">
                            </div>
                            <div class="mb-3">
                                <label for="statut{{ $entrepot->id }}" class="form-label">Statut</label>
                                <select class="form-select" id="statut{{ $entrepot->id }}">
                                    <option value="actif" {{ $entrepot->statut == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="en_maintenance" {{ $entrepot->statut == 'en_maintenance' ? 'selected' : '' }}>En Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveEntrepot({{ $entrepot->id }})">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
function saveEntrepot(id) {
    // Récupérer les données du formulaire
    const nom = document.getElementById('nom' + id).value;
    const code = document.getElementById('code' + id).value;
    const adresse = document.getElementById('adresse' + id).value;
    const capacite = document.getElementById('capacite' + id).value;
    const responsable = document.getElementById('responsable' + id).value;
    const telephone = document.getElementById('telephone' + id).value;
    const statut = document.getElementById('statut' + id).value;

    // Simulation de sauvegarde
    alert('Entrepôt ' + nom + ' modifié avec succès!');

    // Fermer le modal
    const modal = document.getElementById('editModal' + id);
    const bsModal = bootstrap.Modal.getInstance(modal);
    bsModal.hide();

    // Recharger la page pour voir les changements
    window.location.reload();
}
</script>
@endsection
