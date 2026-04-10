@extends('layouts.app')

@section('title', 'A Payer - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">A Payer</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Dépenses en attente</h5>
                    <h2>{{ $depensesAPayer->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Opérations validées</h5>
                    <h2>{{ $operationsAPayer->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Total à payer</h5>
                    <h2>{{ number_format($depensesAPayer->sum('montant') + $operationsAPayer->sum('montant'), 0, ',', ' ') }} FCFA</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Urgent</h5>
                    <h2>{{ $depensesAPayer->where('priorite', 'urgent')->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Dépenses en attente -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Dépenses en attente de paiement</h5>
        </div>
        <div class="card-body">
            @if($depensesAPayer->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucune dépense en attente de paiement.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Bénéficiaire</th>
                                <th>Description</th>
                                <th>Montant</th>
                                <th>Caisse</th>
                                <th>Priorité</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($depensesAPayer as $depense)
                            <tr>
                                <td>{{ $depense->date_depense ? $depense->date_depense->format('d/m/Y') : '-' }}</td>
                                <td>{{ $depense->beneficiaire->nom ?? '-' }}</td>
                                <td>{{ $depense->description ?? '-' }}</td>
                                <td class="text-end">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $depense->caisse->nom ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $depense->priorite == 'urgent' ? 'danger' : 'warning' }}">
                                        {{ ucfirst($depense->priorite ?? 'normal') }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="payerDepense({{ $depense->id }})">
                                        <i class="fas fa-check"></i> Payer
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Opérations validées -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Opérations validées en attente de paiement</h5>
        </div>
        <div class="card-body">
            @if($operationsAPayer->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucune opération validée en attente de paiement.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Description</th>
                                <th>Montant</th>
                                <th>Service</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($operationsAPayer as $operation)
                            <tr>
                                <td>{{ $operation->date_operation ? $operation->date_operation->format('d/m/Y') : '-' }}</td>
                                <td>{{ $operation->user->name ?? '-' }}</td>
                                <td>{{ $operation->description ?? '-' }}</td>
                                <td class="text-end">{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $operation->service->nom ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="payerOperation({{ $operation->id }})">
                                        <i class="fas fa-check"></i> Payer
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function payerDepense(id) {
    if(confirm('Confirmer le paiement de cette dépense ?')) {
        // Logique de paiement à implémenter
        alert('Fonctionnalité de paiement à implémenter');
    }
}

function payerOperation(id) {
    if(confirm('Confirmer le paiement de cette opération ?')) {
        // Logique de paiement à implémenter
        alert('Fonctionnalité de paiement à implémenter');
    }
}
</script>
@endsection
