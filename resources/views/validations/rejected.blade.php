@extends('layouts.app')

@section('title', 'Validations Rejetées')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>
                        <i class="fas fa-times-circle me-2"></i>Validations Rejetées
                    </h2>
                    <small class="text-muted">Opérations que vous avez rejetées</small>
                </div>
                <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour aux Opérations
                </a>
            </div>

            <!-- Messages -->
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Liste des opérations -->
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="m-0">
                        <i class="fas fa-times me-2"></i>{{ count($operations) }} Opération(s) rejetée(s)
                    </h5>
                </div>
                <div class="card-body">
                    @if ($operations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Référence</th>
                                        <th>Titre</th>
                                        <th>Montant</th>
                                        <th>Demandeur</th>
                                        <th>Date de Rejet</th>
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
                                            {{ $operation->demandeur_name ?? '-' }}
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($operation->updated_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
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
                            <i class="fas fa-times-circle text-danger fa-3x mb-3"></i>
                            <p class="text-muted">
                                <strong>Vous n'avez rejeté aucune opération</strong><br>
                                Les opérations que vous rejetterez apparaîtront ici.
                            </p>
                            <a href="{{ route('validations.pending') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-hourglass-half me-1"></i>Voir les en attente
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Onglets de navigation -->
            <div class="mt-4">
                <div class="nav nav-tabs" role="tablist">
                    <a href="{{ route('validations.pending') }}" class="nav-link">
                        <i class="fas fa-hourglass-half me-1"></i>En Attente
                    </a>
                    <a href="{{ route('validations.approved') }}" class="nav-link">
                        <i class="fas fa-check-circle me-1"></i>Approuvées
                    </a>
                    <a href="{{ route('validations.rejected') }}" class="nav-link active">
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
