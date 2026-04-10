@extends('layouts.app')

@section('title', 'Gestion des Congés - ' . $personnel->nom . ' ' . $personnel->prenoms)

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-calendar-alt me-2"></i>Congés - {{ $personnel->nom }} {{ $personnel->prenoms }}
            </h2>
            <small class="text-muted">Matricule: {{ $personnel->matricule }} | Solde: {{ $personnel->soldeConges() }} jours</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('personnel.show', $personnel->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour fiche
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nouveauCongeModal">
                <i class="fas fa-plus me-1"></i>Nouveau Congé
            </button>
        </div>
    </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques congés -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $personnel->soldeConges() }}</h4>
                            <small>Solde disponible</small>
                        </div>
                        <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $personnel->conges()->where('statut', 'VALIDE')->sum('nb_jours') }}</h4>
                            <small>Jours pris cette année</small>
                        </div>
                        <i class="fas fa-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $personnel->conges()->where('statut', 'EN_ATTENTE')->count() }}</h4>
                            <small>Demandes en attente</small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $personnel->conges()->whereYear('date_debut', now()->year)->count() }}</h4>
                            <small>Total congés 2024</small>
                        </div>
                        <i class="fas fa-calendar fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des congés -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>Historique des Congés
            </h5>
        </div>
        <div class="card-body">
            @if($personnel->conges->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date demande</th>
                                <th>Type</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Nb. jours</th>
                                <th>Motif</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($personnel->cones->orderBy('created_at', 'desc')->get() as $conge)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($conge->created_at)->format('d/m/Y') }}</td>
                                <td>
                                    @switch($conge->type)
                                        @case('ANNUEL')
                                            <span class="badge bg-primary">Annuel</span>
                                            @break
                                        @case('MALADIE')
                                            <span class="badge bg-danger">Maladie</span>
                                            @break
                                        @case('MATERNITE')
                                            <span class="badge bg-pink">Maternité</span>
                                            @break
                                        @case('PATERNITE')
                                            <span class="badge bg-info">Paternité</span>
                                            @break
                                        @case('EXCEPTIONNEL')
                                            <span class="badge bg-warning">Exceptionnel</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $conge->type }}</span>
                                    @endswitch
                                </td>
                                <td>{{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}</td>
                                <td>{{ $conge->nb_jours }}</td>
                                <td>{{ $conge->motif ?? '-' }}</td>
                                <td>
                                    @switch($conge->statut)
                                        @case('EN_ATTENTE')
                                            <span class="badge bg-warning">En attente</span>
                                            @break
                                        @case('VALIDE')
                                            <span class="badge bg-success">Validé</span>
                                            @break
                                        @case('REFUSE')
                                            <span class="badge bg-danger">Refusé</span>
                                            @break
                                        @case('ANNULE')
                                            <span class="badge bg-secondary">Annulé</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $conge->statut }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($conge->statut === 'EN_ATTENTE')
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Annuler">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt text-muted fa-3x mb-3"></i>
                    <h5 class="text-muted">Aucun congé enregistré</h5>
                    <p class="text-muted">Ce personnel n'a pas encore de demande de congé</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Nouveau Congé -->
<div class="modal fade" id="nouveauCongeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nouvelle Demande de Congé
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('personnel.conges.store', $personnel->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de congé *</label>
                            <select name="type" class="form-select" required>
                                <option value="">Sélectionner...</option>
                                <option value="ANNUEL">Congé annuel</option>
                                <option value="MALADIE">Congé maladie</option>
                                <option value="MATERNITE">Congé maternité</option>
                                <option value="PATERNITE">Congé paternité</option>
                                <option value="EXCEPTIONNEL">Congé exceptionnel</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de début *</label>
                            <input type="date" name="date_debut" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de fin *</label>
                            <input type="date" name="date_fin" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de jours</label>
                            <input type="number" name="nb_jours" class="form-control" readonly id="nbJoursCalcule">
                            <small class="text-muted">Calculé automatiquement</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Motif</label>
                            <textarea name="motif" class="form-control" rows="3" placeholder="Précisez le motif si nécessaire..."></textarea>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Solde disponible:</strong> {{ $personnel->soldeConges() }} jours
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Enregistrer la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Calcul du nombre de jours entre deux dates
function calculerJours() {
    const dateDebut = document.querySelector('input[name="date_debut"]').value;
    const dateFin = document.querySelector('input[name="date_fin"]').value;
    
    if (dateDebut && dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);
        
        if (fin >= debut) {
            const diffTime = Math.abs(fin - debut);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 pour inclure le jour de début
            document.getElementById('nbJoursCalcule').value = diffDays;
        } else {
            document.getElementById('nbJoursCalcule').value = '';
            alert('La date de fin doit être postérieure à la date de début');
        }
    }
}

// Écouteurs d'événements
document.querySelector('input[name="date_debut"]').addEventListener('change', calculerJours);
document.querySelector('input[name="date_fin"]').addEventListener('change', calculerJours);

// Validation du formulaire
document.querySelector('#nouveauCongeModal form').addEventListener('submit', function(e) {
    const nbJours = parseInt(document.getElementById('nbJoursCalcule').value);
    const soldeDisponible = {{ $personnel->soldeConges() }};
    const typeConge = document.querySelector('select[name="type"]').value;
    
    if (typeConge === 'ANNUEL' && nbJours > soldeDisponible) {
        e.preventDefault();
        alert(`Solde insuffisant ! Il vous reste ${soldeDisponible} jours de congé annuel.`);
    }
});
</script>
@endpush
