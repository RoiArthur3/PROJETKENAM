@extends('layouts.app')

@section('title', 'Opportunités Commerciales - Commercial')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-bullseye text-success me-2"></i>Opportunités Commerciales
                    </h1>
                    <p class="text-muted mb-0">Gérez vos opportunités d'affaires et suivez leur évolution</p>
                </div>
                <a href="{{ route('commercial.opportunites.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Nouvelle Opportunité
                </a>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Opportunités
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $opportunites->count() ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Valeur Totale
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($opportunites->sum('valeur_estimee') ?? 0, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Négociation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $opportunites->where('statut', 'negociation')->count() ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Taux de Conversion
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $opportunites->count() > 0 ? round(($opportunites->where('statut', 'gagnee')->count() / $opportunites->count()) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtres
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="prospection">Prospection</option>
                                <option value="negociation">Négociation</option>
                                <option value="gagnee">Gagnée</option>
                                <option value="perdue">Perdue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Client</label>
                            <input type="text" class="form-control" id="clientFilter" placeholder="Nom du client">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Valeur minimale</label>
                            <input type="number" class="form-control" id="valueFilter" placeholder="Montant minimum">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Période</label>
                            <select class="form-select" id="periodFilter">
                                <option value="">Toutes les périodes</option>
                                <option value="this_month">Ce mois</option>
                                <option value="last_month">Mois dernier</option>
                                <option value="this_quarter">Ce trimestre</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes des opportunités -->
    <div class="row" id="opportunitiesContainer">
        @forelse ($opportunites ?? collect() as $opportunite)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4 opportunity-card" data-status="{{ $opportunite->statut ?? 'prospection' }}">
                <div class="card shadow h-100 opportunity-card-content">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">{{ $opportunite->titre ?? 'Opportunité Test' }}</h6>
                            @php
                                $statusColors = [
                                    'prospection' => 'secondary',
                                    'negociation' => 'warning',
                                    'gagnee' => 'success',
                                    'perdue' => 'danger',
                                ];
                                $statusLabels = [
                                    'prospection' => 'Prospection',
                                    'negociation' => 'Négociation',
                                    'gagnee' => 'Gagnée',
                                    'perdue' => 'Perdue',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$opportunite->statut ?? 'prospection'] }}">
                                {{ $statusLabels[$opportunite->statut ?? 'prospection'] }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-building text-primary me-2"></i>
                            <strong>Client:</strong> {{ $opportunite->client_nom ?? 'Client Test' }}
                        </div>

                        <div class="mb-3">
                            <i class="fas fa-euro-sign text-success me-2"></i>
                            <strong>Valeur estimée:</strong><br>
                            <span class="h5 text-success">{{ number_format($opportunite->valeur_estimee ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>

                        <div class="mb-3">
                            <i class="fas fa-calendar text-info me-2"></i>
                            <strong>Date de clôture:</strong><br>
                            {{ $opportunite->date_cloture ? \Carbon\Carbon::parse($opportunite->date_cloture)->format('d/m/Y') : 'Non définie' }}
                        </div>

                        <div class="mb-3">
                            <i class="fas fa-user text-warning me-2"></i>
                            <strong>Commercial:</strong> {{ $opportunite->commercial_nom ?? 'Commercial Test' }}
                        </div>

                        <div class="mb-3">
                            <i class="fas fa-chart-line text-secondary me-2"></i>
                            <strong>Probabilité:</strong> {{ $opportunite->probabilite ?? 50 }}%
                            <div class="progress mt-1" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                     style="width: {{ $opportunite->probabilite ?? 50 }}%"
                                     aria-valuenow="{{ $opportunite->probabilite ?? 50 }}"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        @if($opportunite->description)
                            <div class="mb-3">
                                <i class="fas fa-file-alt text-muted me-2"></i>
                                <strong>Description:</strong><br>
                                <small class="text-muted">{{ Str::limit($opportunite->description, 100) }}</small>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Créée: {{ $opportunite->created_at ? \Carbon\Carbon::parse($opportunite->created_at)->format('d/m/Y') : 'N/A' }}
                            </small>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm" onclick="viewOpportunity({{ $opportunite->id ?? 1 }})" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning btn-sm" onclick="editOpportunity({{ $opportunite->id ?? 1 }})" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-success btn-sm" onclick="updateStatus({{ $opportunite->id ?? 1 }}, '{{ $opportunite->statut ?? 'prospection' }}')" title="Changer statut">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bullseye fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">Aucune opportunité trouvée</h4>
                        <p class="text-muted">Créez votre première opportunité commerciale pour commencer à suivre vos affaires.</p>
                        <a href="{{ route('commercial.opportunites.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer la première opportunité
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.opportunity-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.opportunity-card:hover .opportunity-card-content {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
}

.card-header.bg-gradient-primary {
    border-bottom: none;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}
</style>

<script>
function viewOpportunity(id) {
    window.location.href = `/commercial/opportunites/${id}`;
}

function editOpportunity(id) {
    window.location.href = `/commercial/opportunites/${id}/edit`;
}

function updateStatus(id, currentStatus) {
    // Simple status update - in real app, this would be an AJAX call
    const nextStatus = {
        'prospection': 'negociation',
        'negociation': 'gagnee',
        'gagnee': 'gagnee',
        'perdue': 'prospection'
    };

    if (confirm('Changer le statut de cette opportunité ?')) {
        // Here you would make an AJAX call to update the status
        console.log('Update status for opportunity', id, 'to', nextStatus[currentStatus]);
        // Reload or update the card
        location.reload();
    }
}

// Filtres
document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
document.getElementById('clientFilter')?.addEventListener('input', applyFilters);
document.getElementById('valueFilter')?.addEventListener('input', applyFilters);
document.getElementById('periodFilter')?.addEventListener('change', applyFilters);

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const clientFilter = document.getElementById('clientFilter').value.toLowerCase();
    const valueFilter = parseFloat(document.getElementById('valueFilter').value) || 0;
    const periodFilter = document.getElementById('periodFilter').value;

    const cards = document.querySelectorAll('.opportunity-card');

    cards.forEach(card => {
        const status = card.getAttribute('data-status');
        const clientName = card.querySelector('.card-body').textContent.toLowerCase();
        const valueText = card.querySelector('.text-success').textContent.replace(/[^0-9]/g, '');
        const value = parseFloat(valueText) || 0;

        let show = true;

        if (statusFilter && status !== statusFilter) show = false;
        if (clientFilter && !clientName.includes(clientFilter)) show = false;
        if (valueFilter && value < valueFilter) show = false;

        // Period filter would need date comparison - simplified for now
        if (periodFilter) {
            // Add date filtering logic here if needed
        }

        card.style.display = show ? '' : 'none';
    });
}
</script>
@endsection
