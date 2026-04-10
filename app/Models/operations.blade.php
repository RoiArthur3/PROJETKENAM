@extends('layouts.app')

@section('title', 'Reporting Opérations - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Reporting Opérations
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Navigation rapide -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('reporting.dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                                <a href="{{ route('reporting.financier') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-chart-line me-2"></i>Financier
                                </a>
                                <a href="{{ route('reporting.operations') }}" class="btn btn-warning active">
                                    <i class="fas fa-cogs me-2"></i>Opérations
                                </a>
                                <a href="{{ route('reporting.services') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-building me-2"></i>Services
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Type d'opération</label>
                            <select class="form-select">
                                <option>Tous les types</option>
                                <option>Transport</option>
                                <option>Maintenance</option>
                                <option>Carburant</option>
                                <option>Piéces détachées</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select">
                                <option>Tous les statuts</option>
                                <option>En cours</option>
                                <option>Terminé</option>
                                <option>Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Période</label>
                            <select class="form-select">
                                <option>Cette semaine</option>
                                <option>Ce mois</option>
                                <option>Ce trimestre</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filtrer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs Opérations -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Total Opérations</h6>
                                            <h3 class="mb-0">{{ number_format($stats['total_operations'], 0, ',', ' ') }}</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-tasks fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Opérations Terminées</h6>
                                            <h3 class="mb-0">{{ number_format($stats['operations_completed'], 0, ',', ' ') }}</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-check-double fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">En Cours / Attente</h6>
                                            <h3 class="mb-0">{{ number_format($stats['operations_pending'], 0, ',', ' ') }}</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-clock fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-white-50 mb-1">Taux de Succès</h6>
                                            <h3 class="mb-0">{{ $stats['success_rate'] }}%</h3>
                                        </div>
                                        <div class="ms-3">
                                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des opérations -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0 text-primary">
                                        <i class="fas fa-list me-2"></i>
                                        Suivi des Opérations Réelles
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Titre / Référence</th>
                                                    <th>Demandeur</th>
                                                    <th>Date Création</th>
                                                    <th>Statut</th>
                                                    <th class="text-end">Montant</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($operations as $operation)
                                                <tr>
                                                    <td><span class="fw-bold">#{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span>{{ $operation->titre }}</span>
                                                            <small class="text-muted">{{ $operation->type_operation ?? 'Opération' }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($operation->user)
                                                            <span class="badge bg-secondary">{{ $operation->user->name }}</span>
                                                        @else
                                                            <span class="text-muted">{{ $operation->demandeur_name ?? '-' }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $operation->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @php
                                                            $badgeClass = match($operation->statut_courant) {
                                                                'payee', 'termine', 'cloture' => 'bg-success',
                                                                'rejetee' => 'bg-danger',
                                                                'approuvee' => 'bg-info',
                                                                default => 'bg-warning'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} text-uppercase">{{ $operation->statut_courant }}</span>
                                                    </td>
                                                    <td class="text-end fw-bold">{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('operations.download-pdf', $operation->id) }}" class="btn btn-sm btn-outline-secondary" title="Télécharger PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-5">
                                                        <div class="text-muted">
                                                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                                                            <p>Aucune opération trouvée dans la base de données.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="p-3">
                                        {{ $operations->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
