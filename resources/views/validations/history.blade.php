@extends('layouts.app')

@section('title', 'Historique des Validations - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm border-start border-primary border-4">
                <div>
                    <h4 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-history me-2"></i>Historique des Validations
                    </h4>
                    <p class="text-muted mb-0 small">Archives de toutes les décisions prises sur les opérations</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('validations.pending') }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-clock"></i> En attente
                    </a>
                    <a href="{{ route('validations.approved') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-check"></i> Approuvées
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Rapides -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-white border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                            <i class="fas fa-list-check fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small text-uppercase fw-bold ls-1">Total Actions</h6>
                            <h3 class="mb-0 fw-bold">{{ $operations->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white border-0 shadow-sm h-100 overflow-hidden border-start border-success border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                            <i class="fas fa-check-double fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small text-uppercase fw-bold ls-1">Approbations</h6>
                            <h3 class="mb-0 fw-bold">{{ $operations->whereIn('statut_courant', ['Approuvé_en_attente_paiement', 'payee'])->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white shadow-sm h-100 overflow-hidden border-start border-danger border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger me-3">
                            <i class="fas fa-ban fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 small text-uppercase fw-bold ls-1">Rejets</h6>
                            <h3 class="mb-0 fw-bold">{{ $operations->where('statut_courant', 'rejetee')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Validations -->
    <div class="card border-0 shadow-sm overflow-hidden animate__animated animate__fadeIn">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light bg-opacity-50 border-bottom">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase">Opération</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase">Service</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase">Décision</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase">Date & Heure</th>
                            <th class="py-3 text-muted fw-semibold small text-uppercase">Commentaire</th>
                            <th class="text-end pe-4 py-3 text-muted fw-semibold small text-uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($operations as $operation)
                        <tr class="transition-all">
                            <td class="ps-4 py-3">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark mb-1">REQ-{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-muted small text-truncate d-block" style="max-width: 280px;" title="{{ $operation->titre }}">
                                        {{ $operation->titre }}
                                    </span>
                                    <div class="mt-2">
                                        @php
                                            $statusBadgeMap = [
                                                'Approuvé_en_attente_paiement' => 'success',
                                                'rejetee' => 'danger',
                                                'pending_validation' => 'warning',
                                                'en_validation' => 'info',
                                                'payee' => 'success',
                                                'termine' => 'primary'
                                            ];
                                            $currentStatut = $operation->statut_courant;
                                            $badgeColor = $statusBadgeMap[$currentStatut] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} border border-{{ $badgeColor }} border-opacity-25" style="font-size: 0.65rem; font-weight: 600;">
                                            <i class="fas fa-circle me-1 small"></i> STATUT ACTUEL: {{ strtoupper(str_replace('_', ' ', $currentStatut)) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                    <i class="fas fa-building me-1 opacity-50"></i>
                                    {{ $operation->operationalService->nom ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($operation->statut_courant === 'Approuvé_en_attente_paiement' || $operation->statut_courant === 'payee')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-check-circle me-1"></i> APPROUVÉ
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-times-circle me-1"></i> REJETÉ
                                    </span>
                                @endif
                                <div class="mt-2 x-small text-muted fw-medium">
                                    <i class="fas fa-user-edit me-1 opacity-50"></i>{{ $operation->demandeur_name ?? 'Système' }}
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold" style="font-size: 0.9rem;">
                                        {{ $operation->updated_at ? $operation->updated_at->format('d/m/Y') : '-' }}
                                    </span>
                                    <span class="text-muted x-small">
                                        <i class="far fa-clock me-1"></i>{{ $operation->updated_at ? $operation->updated_at->format('H:i') : '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="text-muted small fst-italic" style="max-width: 300px; line-height: 1.4;">
                                    {{ $operation->description ? Str::limit($operation->description, 120) : 'Aucun commentaire fourni.' }}
                                </div>
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-sm btn-outline-primary rounded-0">
                                        <i class="fas fa-eye me-1"></i> Voir
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                <div class="fw-bold">Aucun historique de validation trouvé</div>
                                <div class="small">Les validations approuvées et rejetées apparaîtront ici</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 0.5px; }
    .x-small { font-size: 0.75rem; }
    .transition-all { transition: all 0.2s ease; }
    tr.transition-all:hover { background-color: rgba(22, 163, 74, 0.02) !important; }
    .btn-white { background: white; }
    .btn-white:hover { background: #f8fafc; }
    .ls-1 { letter-spacing: 0.05rem; }
</style>

@push('scripts')
<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip()
    })
</script>
@endpush
@endsection
