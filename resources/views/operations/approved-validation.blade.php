@extends('layouts.app')

@section('title', 'Validations Approuvées - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Mes Validations Approuvées</h1>
        <div>
            <a href="{{ route('validations.pending') }}" class="btn btn-outline-warning me-2">
                <i class="fas fa-clock me-2"></i>En attente
            </a>
            <a href="{{ route('validations.paid') }}" class="btn btn-outline-success me-2">
                <i class="fas fa-coins me-2"></i>Payées
            </a>
            <a href="{{ route('validations.rejected') }}" class="btn btn-outline-danger me-2">
                <i class="fas fa-times me-2"></i>Rejetées
            </a>
            <a href="{{ route('validations.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $operations->total() }}</h4>
                            <p class="mb-0">Total Approuvées</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
            <div class="card bg-primary text-white">
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
                            <h4 class="mb-0">{{ $operations->where('statut_courant', 'approuvee')->count() }}</h4>
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
            <form method="GET" action="{{ route('validations.approved') }}">
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
                        <label class="form-label">Date d'approbation</label>
                        <input type="date" name="date_approbation" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('validations.approved') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des opérations approuvées -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-check-circle text-success me-2"></i>
                Liste des opérations approuvées
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
                                <th>Validateur</th>
                                <th>Date approbation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($operations as $operation)
                                <tr>
                                    <td>
                                        <span class="badge bg-success">OP-{{ str_pad($operation->id, 4, '0', STR_PAD_LEFT) }}</span>
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
                                        <span class="text-success fw-bold">
                                            {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                        @if($operation->montant_approuve && $operation->montant_approuve != $operation->montant)
                                            <br><small class="text-muted">(Approuvé: {{ number_format($operation->montant_approuve, 0, ',', ' ') }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $operation->priorite === 'urgente' ? 'danger' : ($operation->priorite === 'haute' ? 'warning' : ($operation->priorite === 'moyenne' ? 'info' : 'secondary')) }}">
                                            {{ $operation->priorite }}
                                        </span>
                                    </td>
                                    <td>{{ $operation->validator_name }}</td>
                                    <td>
                                        @if($operation->date_approbation)
                                            {{ is_string($operation->date_approbation) ? \Carbon\Carbon::parse($operation->date_approbation)->format('d/m/Y H:i') : $operation->date_approbation->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operations.tracking', $operation->id) }}" class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('operations.edit', $operation->id) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @php
                                                $user = auth()->user();
                                                $comptaEmail = 'konanakanie@kenamservices.net';
                                            @endphp

                                            @if($user && $user->email === $comptaEmail && !$operation->is_paid)
                                                <form action="{{ route('validations.operations.markPaid', $operation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer le paiement de cette opération ?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success shadow-sm" title="Marquer comme payée">
                                                        <i class="fas fa-money-bill-wave"></i> PAYER
                                                    </button>
                                                </form>
                                            @endif

                                            <button class="btn btn-sm btn-outline-success" title="Imprimer" onclick="window.print()">
                                                <i class="fas fa-print"></i>
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
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5>Aucune opération approuvée</h5>
                    <p class="text-muted">Les opérations approuvées apparaîtront ici.</p>
                    <a href="{{ route('validations.pending') }}" class="btn btn-primary">
                        <i class="fas fa-clock me-2"></i>Voir les validations en attente
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
