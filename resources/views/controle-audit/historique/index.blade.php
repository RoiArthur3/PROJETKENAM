@extends('layouts.app')

@section('title', 'Historique - Contrôle & Audit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-history text-primary me-2"></i>Historique d'Audit
            </h1>
            <p class="text-muted mb-0">Historique des actions et modifications dans le module d'audit</p>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total actions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $historiques->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $historiques->where('created_at', '>=', today())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Cette semaine
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $historiques->where('created_at', '>=', now()->startOfWeek())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Modifications
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $historiques->where('action', 'modification')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table de l'historique -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Historique des actions
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Heure</th>
                            <th>Utilisateur</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historiques as $historique)
                            <tr>
                                <td>
                                    <small>{{ $historique->created_at->format('d/m/Y H:i:s') }}</small>
                                </td>
                                <td>
                                    @if($historique->user)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($historique->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $historique->user->name }}</div>
                                                <small class="text-muted">{{ $historique->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Système</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $historique->action === 'creation' ? 'success' : ($historique->action === 'modification' ? 'warning' : ($historique->action === 'suppression' ? 'danger' : 'info')) }}">
                                        {{ ucfirst($historique->action) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $historique->module }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $historique->description }}</div>
                                    @if($historique->entite_id)
                                        <small class="text-muted">ID: {{ $historique->entite_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($historique->anciennes_valeurs || $historique->nouvelles_valeurs)
                                        <button class="btn btn-sm btn-outline-info" onclick="showDetails({{ $historique->id }})">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucun historique trouvé</div>
                                    <p class="text-muted small">Les actions futures seront enregistrées ici.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $historiques->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les détails -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de l'action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailsContent">
                    <!-- Les détails seront chargés ici -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showDetails(id) {
    // Implémenter la logique pour afficher les détails
    console.log('Show details for ID:', id);
}
</script>
@endsection
