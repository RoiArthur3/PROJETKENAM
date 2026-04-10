@extends('layouts.app')

@section('title', 'Validations Rejetées - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Mes Validations Rejetées</h1>
        <div>
            <a href="{{ route('validations.pending') }}" class="btn btn-outline-warning me-2">
                <i class="fas fa-clock me-2"></i>En attente
            </a>
            <a href="{{ route('validations.approved') }}" class="btn btn-outline-success me-2">
                <i class="fas fa-check me-2"></i>Approuvées
            </a>
            <a href="{{ route('validations.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $operations->total() }}</h4>
                            <p class="mb-0">Total Rejetées</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($operations->sum('montant'), 0, ',', ' ') }}</h4>
                            <p class="mb-0">Montant Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $operations->where('priorite', 'urgente')->count() }}</h4>
                            <p class="mb-0">Urgentes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $operations->where('statut_courant', 'rejetee')->count() }}</h4>
                            <p class="mb-0">Ce mois</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('validations.rejected') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Priorité</label>
                        <select name="priorite" class="form-select">
                            <option value="">Toutes les priorités</option>
                            <option value="urgente">Urgente</option>
                            <option value="haute">Haute</option>
                            <option value="moyenne">Moyenne</option>
                            <option value="basse">Basse</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Service</label>
                        <select name="service" class="form-select">
                            <option value="">Tous les services</option>
                            @foreach(App\Models\ServiceOperationnel::all() as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de rejet</label>
                        <input type="date" name="date_rejet" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('validations.rejected') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des opérations rejetées -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-times-circle text-danger me-2"></i>
                Liste des opérations rejetées
            </h5>
        </div>
        <div class="card-body">
            @if($operations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Titre</th>
                                <th>Demandeur</th>
                                <th>Service</th>
                                <th>Montant</th>
                                <th>Priorité</th>
                                <th>Date de rejet</th>
                                <th>Motif</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($operations as $operation)
                                <tr>
                                    <td>
                                        <span class="badge bg-danger">OP-{{ str_pad($operation->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $operation->titre }}</strong>
                                        @if ($operation->description)
                                            <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($operation->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $operation->demandeur_name }}</td>
                                    <td>{{ $operation->operationalService ? $operation->operationalService->nom : '-' }}</td>
                                    <td>
                                        <span class="text-danger fw-bold">
                                            {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $operation->priorite === 'urgente' ? 'danger' : ($operation->priorite === 'haute' ? 'warning' : ($operation->priorite === 'moyenne' ? 'info' : 'secondary')) }}">
                                            {{ $operation->priorite }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($operation->date_rejet)
                                            {{ is_string($operation->date_rejet) ? \Carbon\Carbon::parse($operation->date_rejet)->format('d/m/Y H:i') : $operation->date_rejet->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusLog = \App\Models\OperationStatusLog::where('operation_id', $operation->id)
                                                ->where('to_status', 'rejetee')
                                                ->latest()
                                                ->first();
                                        @endphp
                                        @if($statusLog && $statusLog->commentaire)
                                            <span class="text-muted" title="{{ $statusLog->commentaire }}">
                                                {{ \Illuminate\Support\Str::limit($statusLog->commentaire, 30) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operations.tracking', $operation->id) }}" class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-warning" title="Soumettre à nouveau" onclick="resubmitOperation({{ $operation->id }})">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" title="Contacter le demandeur" onclick="contactDemandeur('{{ $operation->demandeur_email }}')">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Affichage de {{ $operations->firstItem() }} à {{ $operations->lastItem() }}
                        sur {{ $operations->total() }} opérations
                    </div>
                    {{ $operations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                    <h5>Aucune opération rejetée</h5>
                    <p class="text-muted">Les opérations rejetées apparaîtront ici.</p>
                    <a href="{{ route('validations.pending') }}" class="btn btn-primary">
                        <i class="fas fa-clock me-2"></i>Voir les validations en attente
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function resubmitOperation(operationId) {
    if (confirm('Voulez-vous vraiment soumettre cette opération à nouveau ?')) {
        window.location.href = '/operations/' + operationId + '/resubmit';
    }
}

function contactDemandeur(email) {
    window.location.href = 'mailto:' + email + '?subject=Concernant votre opération KENAM';
}
</script>
@endsection
