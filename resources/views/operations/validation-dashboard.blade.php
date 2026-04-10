@extends('layouts.app')

@section('title', 'Dashboard Validation - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard Validation</h1>
        <div>
            <a href="{{ route('validations.pending') }}" class="btn btn-warning me-2">
                <i class="fas fa-clock me-2"></i>En attente ({{ $stats['en_attente'] ?? 0 }})
            </a>
            <a href="{{ route('validations.approved') }}" class="btn btn-success me-2">
                <i class="fas fa-check me-2"></i>Approuvées ({{ $stats['approuvees'] ?? 0 }})
            </a>
            <a href="{{ route('validations.rejected') }}" class="btn btn-danger me-2">
                <i class="fas fa-times me-2"></i>Rejetées ({{ $stats['rejetees'] ?? 0 }})
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                En attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['en_attente'] ?? 0 }}
                            </div>
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
                                Validées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['validees'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Rejetées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['rejetees'] ?? 0 }}
                            </div>
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
                                Aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['aujourdhui'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opérations en attente -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Opérations en attente</h5>
        </div>
        <div class="card-body">
            @if ($operationsEnAttente->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Demandeur</th>
                                <th>Montant</th>
                                <th>Priorité</th>
                                <th>Date</th>
                                <th>Échéance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operationsEnAttente as $operation)
                                <tr>
                                    <td>
                                        <strong>{{ $operation->titre }}</strong>
                                        @if ($operation->description)
                                            <br><small class="text-muted">{{ Str::limit($operation->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $operation->demandeur_name }}</td>
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
                                            @else
                                                {{ $operation->echeance->format('d/m/Y') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operations.tracking', $operation->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('operations.validate', $operation->id) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune opération en attente</h5>
                    <p class="text-muted">Toutes les opérations ont été traitées.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
