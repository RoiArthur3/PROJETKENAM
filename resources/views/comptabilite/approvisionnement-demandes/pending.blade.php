@extends('layouts.app')

@section('title', 'Validation des Demandes d\'Approvisionnement')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-clipboard-check text-primary me-2"></i>
                Demandes d'Approvisionnement
            </h4>
            <small class="text-muted">Validez, refusez ou exécutez les demandes de la trésorerie</small>
        </div>
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

    {{-- KPI --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <div class="fw-bold fs-4 text-warning">{{ $stats['pending'] }}</div>
                <small class="text-muted">En attente</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <i class="fas fa-user-tie fa-2x text-secondary mb-2"></i>
                <div class="fw-bold fs-4 text-secondary">{{ $stats['pending_dg'] ?? 0 }}</div>
                <small class="text-muted">En attente DG</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <i class="fas fa-check fa-2x text-info mb-2"></i>
                <div class="fw-bold fs-4 text-info">{{ $stats['approved'] }}</div>
                <small class="text-muted">Approuvées (à exécuter)</small>
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
                <i class="fas fa-list fa-2x text-secondary mb-2"></i>
                <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>

    {{-- Modal Refus --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2"></i>Refuser la demande
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted">Veuillez indiquer le motif du refus qui sera communiqué au trésorier.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Motif du refus <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Expliquez pourquoi cette demande est refusée..."
                                      required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times-circle me-1"></i>Confirmer le refus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Demandeur</th>
                            <th>Montant</th>
                            <th>Caisse dest.</th>
                            <th>Raison</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($currentRole = auth()->user()->role ?? null)
                        @forelse($demandes as $demande)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $demande->numero_demande }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-user text-muted me-1"></i>
                                    {{ $demande->demandeur->name ?? '—' }}
                                </td>
                                <td class="fw-semibold text-success">
                                    {{ number_format($demande->montant, 0, ',', ' ') }}
                                    <small class="text-muted">{{ $demande->devise }}</small>
                                </td>
                                <td>{{ $demande->caisseDestination->nom ?? '—' }}</td>
                                <td>
                                    <span title="{{ $demande->raison }}">
                                        {{ \Illuminate\Support\Str::limit($demande->raison, 35) }}
                                    </span>
                                </td>
                                <td>{{ $demande->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $demande->statutColor() }}">
                                        <i class="{{ $demande->statutIcon() }} me-1"></i>
                                        {{ $demande->statutLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        @if($demande->isPending())
                                            {{-- Comptabilité: transférer à la DG / DG: approuver --}}
                                            <form method="POST"
                                                  action="{{ route('comptabilite.approvisionnement-demandes.approve', $demande) }}"
                                                  onsubmit="return confirm('{{ $currentRole === 'comptabilite' ? 'Transférer cette demande à la DG' : 'Approuver la demande' }} {{ $demande->numero_demande }} ?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        title="{{ $currentRole === 'comptabilite' ? 'Transférer à la DG' : 'Approuver (DG)' }}">
                                                    @if($currentRole === 'comptabilite')
                                                        <i class="fas fa-share-square"></i> Transférer DG
                                                    @else
                                                        <i class="fas fa-check"></i> Approuver DG
                                                    @endif
                                                </button>
                                            </form>
                                            {{-- Refuser --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-reject"
                                                    data-url="{{ route('comptabilite.approvisionnement-demandes.reject', $demande) }}"
                                                    title="Refuser">
                                                <i class="fas fa-times"></i> Refuser
                                            </button>
                                        @elseif($demande->isPendingDg())
                                            @if(in_array($currentRole, ['dg', 'admin', 'superadmin'], true))
                                                <form method="POST"
                                                      action="{{ route('comptabilite.approvisionnement-demandes.approve', $demande) }}"
                                                      onsubmit="return confirm('Approuver définitivement la demande {{ $demande->numero_demande }} ?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approuver (DG)">
                                                        <i class="fas fa-check"></i> Approuver DG
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-user-tie me-1"></i> En attente DG
                                                </span>
                                            @endif
                                        @elseif($demande->isApproved())
                                            {{-- Exécuter --}}
                                            <form method="POST"
                                                  action="{{ route('comptabilite.approvisionnement-demandes.execute', $demande) }}"
                                                  onsubmit="return confirm('Exécuter l\'approvisionnement pour la demande {{ $demande->numero_demande }} ? Cette action créera un approvisionnement de {{ number_format($demande->montant, 0, ',', ' ') }} {{ $demande->devise }}.')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-play-circle me-1"></i>Exécuter
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-check-double fa-3x mb-3 d-block text-success"></i>
                                    Aucune demande en attente de traitement.
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

@push('scripts')
<script>
document.querySelectorAll('.btn-reject').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var url = this.dataset.url;
        document.getElementById('rejectForm').action = url;
        var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    });
});
</script>
@endpush
@endsection
