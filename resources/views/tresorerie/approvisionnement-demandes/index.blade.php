@extends('layouts.app')

@section('title', 'Demandes d\'Approvisionnement')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                Mes Demandes d'Approvisionnement
            </h4>
            <small class="text-muted">Suivez l'état de vos demandes envoyées à la comptabilité</small>
        </div>
        <a href="{{ route('tresorerie.approvisionnement-demandes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouvelle Demande
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistiques --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <i class="fas fa-list-alt fa-2x text-secondary mb-2"></i>
                <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                <small class="text-muted">Total</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <div class="fw-bold fs-4 text-warning">{{ $stats['pending'] }}</div>
                <small class="text-muted">En attente</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <i class="fas fa-check-double fa-2x text-success mb-2"></i>
                <div class="fw-bold fs-4 text-success">{{ $stats['executed'] }}</div>
                <small class="text-muted">Exécutées</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                <div class="fw-bold fs-4 text-danger">{{ $stats['rejected'] }}</div>
                <small class="text-muted">Refusées</small>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Montant</th>
                            <th>Raison</th>
                            <th>Caisse dest.</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $demande)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $demande->numero_demande }}</span>
                                </td>
                                <td class="text-success fw-semibold">
                                    {{ number_format($demande->montant, 0, ',', ' ') }}
                                    <small class="text-muted">{{ $demande->devise }}</small>
                                </td>
                                <td>
                                    <span title="{{ $demande->raison }}">
                                        {{ \Illuminate\Support\Str::limit($demande->raison, 40) }}
                                    </span>
                                </td>
                                <td>{{ $demande->caisseDestination->nom ?? '—' }}</td>
                                <td>{{ $demande->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $demande->statutColor() }}">
                                        <i class="{{ $demande->statutIcon() }} me-1"></i>
                                        {{ $demande->statutLabel() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tresorerie.approvisionnement-demandes.show', $demande) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune demande d'approvisionnement. 
                                    <a href="{{ route('tresorerie.approvisionnement-demandes.create') }}">Créer la première</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($demandes->hasPages())
            <div class="card-footer">
                {{ $demandes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
