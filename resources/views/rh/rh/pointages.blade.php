@extends('layouts.app')

@section('title', 'RH - Pointages | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock me-2 text-primary"></i>Pointage du Personnel
            </h1>
            <p class="text-muted">{{ \Carbon\Carbon::parse($date ?? now())->translatedFormat('l d F Y') }} - Gestion des arrivées et départs</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-success" onclick="exporterPointages()">
                <i class="fas fa-file-excel me-2"></i>Exporter
            </button>
            <a href="{{ route('rh.pointages.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Pointage Individuel
            </a>
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#historiqueModal">
                <i class="fas fa-history me-2"></i>Historique
            </button>
        </div>
    </div>

    <!-- Statistiques du jour -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Présents</div>
                            <div class="h3 mb-0 text-success">{{ $presents ?? 0 }}</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">En Retard</div>
                            <div class="h3 mb-0 text-warning">{{ $retards ?? 0 }}</div>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Absents</div>
                            <div class="h3 mb-0 text-danger">{{ $absents ?? 0 }}</div>
                        </div>
                        <i class="fas fa-times-circle fa-2x text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase">Taux Présence</div>
                            <div class="h3 mb-0 text-info">{{ ($tauxPresence ?? 0) }}%</div>
                        </div>
                        <i class="fas fa-chart-pie fa-2x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-3">
        <div class="col-md-3">
            <input type="text" class="form-control" id="searchAgent" placeholder="🔍 Rechercher un agent...">
        </div>
        <div class="col-md-2">
            <select class="form-select" id="filterService">
                <option value="">Tous les services</option>
                <option value="logistique">Logistique</option>
                <option value="commercial">Commercial</option>
                <option value="entretien">Entretien</option>
                <option value="administration">Administration</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" id="filterStatut">
                <option value="">Tous les statuts</option>
                <option value="present">Présent</option>
                <option value="absent">Absent</option>
                <option value="retard">En retard</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" id="filterDate" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-3 text-end">
            <button class="btn btn-outline-primary" onclick="rafraichirListe()">
                <i class="fas fa-sync-alt me-2"></i>Rafraîchir
            </button>
        </div>
    </div>

    <!-- Liste des agents -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>Liste du Personnel
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="tablePointages">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 25%">Agent</th>
                            <th style="width: 15%">Poste</th>
                            <th style="width: 20%">Type</th>
                            <th style="width: 15%">Heure</th>
                            <th style="width: 10%">Statut</th>
                            <th style="width: 10%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pointages ?? collect() as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;font-size:14px;">
                                            {{ collect(explode(' ', $p->user->name ?? '')) ->filter()->map(fn($n) => mb_substr($n,0,1))->join('') }}
                                        </div>
                                        <strong>{{ $p->user->name ?? '—' }}</strong>
                                    </div>
                                </td>
                                <td>{{ $p->user->role ?? '—' }}</td>
                                <td>{{ ucfirst($p->type ?? '') }}</td>
                                <td>{{ $p->heure_pointage ? $p->heure_pointage->format('H:i') : '—' }}</td>
                                <td>
                                    @php
                                        $statut = $p->statut ?? 'en_attente';
                                        $badgeClass = $statut === 'valide' ? 'success' : ($statut === 'rejete' ? 'danger' : 'secondary');
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($statut) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('rh.agents.show', $p->user_id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-user"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Aucun pointage pour cette date</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historique -->
<div class="modal fade" id="historiqueModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-history me-2"></i>Historique des Pointages
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Date Début</label>
                        <input type="date" class="form-control" id="histoDateDebut" value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Fin</label>
                        <input type="date" class="form-control" id="histoDateFin" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Agent</label>
                        <select class="form-select" id="histoAgent">
                            <option value="">Tous les agents</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="chargerHistorique()">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="tableHistorique">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Agent</th>
                                <th>Service</th>
                                <th>Arrivée</th>
                                <th>Départ</th>
                                <th>Durée</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody id="historiqueBody">
                            <!-- Données historiques -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-success" onclick="exporterHistorique()">
                    <i class="fas fa-file-excel me-2"></i>Exporter
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function rafraichirListe() {
    const date = document.getElementById('filterDate').value;
    const url = new URL(window.location.href);
    if (date) {
        url.searchParams.set('date', date);
    } else {
        url.searchParams.delete('date');
    }
    window.location.href = url.toString();
}
</script>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.toast {
    min-width: 300px;
}
</style>
@endsection
