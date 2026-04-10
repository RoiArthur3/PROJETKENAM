@extends('layouts.app')

@section('title', 'Validations en attente - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Validations en attente</h1>
        <a href="{{ route('operations.validation.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operations.pending.validation') }}">
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
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('operations.pending.validation') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des validations -->
    <div class="card">
        <div class="card-body">
            @if ($operations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Demandeur</th>
                                <th>Service</th>
                                <th>Montant</th>
                                <th>Priorité</th>
                                <th>Date</th>
                                <th>Échéance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operations as $operation)
                                <tr>
                                    <td>
                                        <strong>{{ $operation->titre }}</strong>
                                        @if ($operation->description)
                                            <br><small class="text-muted">{{ Str::limit($operation->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $operation->demandeur_name }}</td>
                                    <td>{{ $operation->operationalService ? $operation->operationalService->nom : '-' }}</td>
                                    <td>{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge bg-{{ $operation->priorite === 'urgente' ? 'danger' : ($operation->priorite === 'haute' ? 'warning' : ($operation->priorite === 'moyenne' ? 'info' : 'secondary')) }}">
                                            {{ $operation->priorite }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($operation->date_operation)
                                            {{ is_string($operation->date_operation) ? \Carbon\Carbon::parse($operation->date_operation)->format('d/m/Y') : $operation->date_operation->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($operation->echeance)
                                            @if(is_string($operation->echeance))
                                                {{ \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') }}
                                                @if(\Carbon\Carbon::parse($operation->echeance)->isPast())
                                                    <br><small class="text-danger">En retard</small>
                                                @endif
                                            @else
                                                {{ $operation->echeance->format('d/m/Y') }}
                                                @if($operation->echeance->isPast())
                                                    <br><small class="text-danger">En retard</small>
                                                @endif
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operations.tracking', $operation->id) }}" class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('operations.validate', $operation->id) }}" class="btn btn-sm btn-outline-success" title="Valider">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" title="Rejeter" onclick="rejectOperation({{ $operation->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $operations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune validation en attente</h5>
                    <p class="text-muted">Toutes les opérations ont été validées.</p>
                    <a href="{{ route('operations.validation.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Voir le dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function rejectOperation(operationId) {
    if (confirm('Êtes-vous sûr de vouloir rejeter cette opération ?')) {
        window.location.href = '/operations/' + operationId + '/reject';
    }
}
</script>
@endsection
