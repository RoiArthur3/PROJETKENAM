@extends('layouts.app')

@section('title', 'Validations en Attente')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>
                        <i class="fas fa-tasks me-2"></i>Validations en Attente
                    </h2>
                    <small class="text-muted">Opérations en attente de votre validation</small>
                </div>
                <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour aux Opérations
                </a>
            </div>


            <!-- Messages -->
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Erreur!</strong>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Liste des opérations -->
            <!-- Filtres avancés -->
            <form method="GET" action="" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Montant min</label>
                        <input type="number" name="montant_min" class="form-control" value="{{ request('montant_min') }}" placeholder="Min">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Montant max</label>
                        <input type="number" name="montant_max" class="form-control" value="{{ request('montant_max') }}" placeholder="Max">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>

            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="m-0">
                        <i class="fas fa-hourglass-half me-2"></i>{{ $operations ? $operations->total() : 0 }} Opération(s) en attente
                    </h5>
                </div>
                <div class="card-body">
                    @if ($operations && $operations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Référence</th>
                                        <th>Titre</th>
                                        <th>Montant</th>
                                        <th>Priorité</th>
                                        <th>Demandeur</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operations as $operation)
                                    <tr>
                                        <td>
                                            <strong>#{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                        </td>
                                        <td>
                                            <span title="{{ $operation->titre }}">
                                                {{ Str::limit($operation->titre, 40) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $operation->priorite === 'urgente' ? 'danger' : ($operation->priorite === 'haute' ? 'warning' : 'info') }}">
                                                {{ ucfirst($operation->priorite) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $operation->demandeur_name ?? '-' }}
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($operation->created_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('operations.validate', $operation->id) }}" class="btn btn-primary" title="Valider cette opération">
                                                    <i class="fas fa-eye me-1"></i>Valider
                                                </a>
                                                <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-info" title="Voir les détails">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
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
                            <i class="fas fa-inbox text-success fa-3x mb-3"></i>
                            <p class="text-muted">
                                <strong>Aucune opération en attente de validation</strong><br>
                                Tous les dossiers en attente ont été traités. Bravo !
                            </p>
                            <a href="{{ route('validations.approved') }}" class="btn btn-success btn-sm me-2">
                                <i class="fas fa-check-circle me-1"></i>Voir les approuvées
                            </a>
                            <a href="{{ route('validations.rejected') }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-times-circle me-1"></i>Voir les rejetées
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Onglets de navigation -->
            <div class="mt-4">
                <div class="nav nav-tabs" role="tablist">
                    <a href="{{ route('validations.pending') }}" class="nav-link active">
                        <i class="fas fa-hourglass-half me-1"></i>En Attente
                    </a>
                    <a href="{{ route('validations.approved') }}" class="nav-link">
                        <i class="fas fa-check-circle me-1"></i>Approuvées
                    </a>
                    <a href="{{ route('validations.rejected') }}" class="nav-link">
                        <i class="fas fa-times-circle me-1"></i>Rejetées
                    </a>
                    <a href="{{ route('validations.history') }}" class="nav-link">
                        <i class="fas fa-history me-1"></i>Historique
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
