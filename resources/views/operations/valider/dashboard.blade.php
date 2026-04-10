@extends('layouts.app')

@section('title', 'Validation des Opérations - Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-check-circle me-2 text-primary"></i>Validation des Opérations
            </h1>
            <p class="text-muted mb-0">Validation des opérations en attente de traitement</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_attente'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Validées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['validees'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejetées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejetees'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['aujourdhui'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opérations en attente -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Opérations en Attente
            </h6>
            <span class="badge bg-warning">{{ $operationsEnAttente->count() ?? 0 }}</span>
        </div>
        <div class="card-body">
            @if($operationsEnAttente->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Véhicule</th>
                                <th>Chauffeur</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($operationsEnAttente as $operation)
                                <tr>
                                    <td>
                                        <strong>{{ $operation->reference ?? 'OP-' . str_pad($operation->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                    </td>
                                    <td>
                                        @if($operation->client)
                                            {{ $operation->client->nom ?? $operation->client->name ?? '—' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($operation->vehicule)
                                            {{ $operation->vehicule->immatriculation ?? '—' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($operation->chauffeur)
                                            {{ $operation->chauffeur->nom ?? $operation->chauffeur->name ?? '—' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($operation->date_operation)
                                            {{ \Carbon\Carbon::parse($operation->date_operation)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">En attente</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('operations.edit', $operation->id) }}" class="btn btn-outline-success" title="Valider">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <button class="btn btn-outline-danger" title="Rejeter" data-operation-id="{{ $operation->id }}" onclick="confirmRejet(this.dataset.operationId)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                                            <p class="mb-0">Aucune opération en attente</p>
                                            <small>Toutes les opérations ont été traitées</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">Aucune opération en attente</h5>
                    <p class="text-muted">Toutes les opérations ont été validées ou rejetées</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmRejet(operationId) {
    if(confirm('Êtes-vous sûr de vouloir rejeter cette opération ?')) {
        // Rediriger vers l'action de rejet
        window.location.href = '/operations/' + operationId + '/rejeter';
    }
}
</script>
@endsection
