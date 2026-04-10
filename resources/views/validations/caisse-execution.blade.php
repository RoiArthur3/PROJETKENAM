@extends('layouts.app')

@section('title', 'Bon pour exécution')

@section('content')
<div class="content-wrapper">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">
                <i class="fas fa-cash-register text-success me-2"></i>
                Bon pour exécution
            </h1>
            <p class="text-muted mb-0 small">
                Ces opérations ont reçu le BON POUR ACCORD comptable. Cliquez sur
                <strong>PAYER</strong> pour enregistrer le décaissement.
            </p>
        </div>
        <a href="{{ route('validations.paid') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-history me-1"></i> Opérations payées
        </a>
    </div>

    {{-- ALERTES --}}
    @foreach(['success', 'error', 'info'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t === 'error' ? 'danger' : $t }} alert-dismissible fade show shadow-sm mb-4">
                <i class="fas fa-{{ match($t) { 'success' => 'check-circle', 'error' => 'exclamation-triangle', default => 'info-circle' } }} me-2"></i>
                {{ session($t) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- KPI --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #f59e0b !important; background:linear-gradient(135deg,#fffbeb,#fef3c7)">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold" style="font-size:2rem">{{ $operations->total() }}</div>
                        <div class="text-muted small text-uppercase">À décaisser</div>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #10b981 !important; background:linear-gradient(135deg,#ecfdf5,#d1fae5)">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold" style="font-size:1.5rem">{{ number_format($totalEnAttente ?? 0, 0, ',', ' ') }}</div>
                        <div class="text-muted small text-uppercase">Total FCFA</div>
                    </div>
                    <i class="fas fa-wallet fa-2x opacity-25 text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-left:4px solid #6366f1 !important; background:linear-gradient(135deg,#eef2ff,#e0e7ff)">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold" style="font-size:1.5rem">{{ number_format($operations->where('priorite', 'urgente')->count()) }}</div>
                        <div class="text-muted small text-uppercase">Urgentes</div>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x opacity-25 text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-list text-success me-2"></i>
                Liste Bon pour exécution
            </h5>
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                       placeholder="Rechercher…" style="width:220px">
                <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="card-body p-0">
            @if($operations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">N° Réf</th>
                                <th>Opération</th>
                                <th>Demandeur</th>
                                <th>Caisse désignée</th>
                                <th class="text-end">Montant</th>
                                <th class="text-center">Priorité</th>
                                <th class="text-center">BON ACCORD</th>
                                <th class="text-center pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($operations as $op)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light border text-secondary fw-semibold">
                                            {{ $op->numero_operation ?? '#' . $op->id }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $op->titre }}</div>
                                        <div class="text-muted small">{{ Str::limit($op->description, 55) }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $op->demandeur_name ?? '—' }}</div>
                                        <div class="text-muted small">{{ $op->demandeur_email }}</div>
                                    </td>
                                    <td>
                                        @if($op->caisse_email)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                {{ $op->caisseExecutante?->nom ?? $op->caisse_email }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Non précisée</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-dark fs-6">{{ number_format($op->montant ?? 0, 0, ',', ' ') }}</strong>
                                        <div class="text-muted small">FCFA</div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $pc = ['urgente' => 'danger', 'haute' => 'warning', 'moyenne' => 'info', 'basse' => 'secondary'][$op->priorite] ?? 'secondary';
                                        @endphp
                                        <span class="badge rounded-pill bg-{{ $pc }}">{{ ucfirst($op->priorite) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-success small fw-semibold">
                                            <i class="fas fa-check-circle me-1"></i>Accordé
                                        </div>
                                        <div class="text-muted" style="font-size:.7rem">
                                            {{ $op->bon_pour_accord_at?->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('operations.show', $op->id) }}"
                                               class="btn btn-sm btn-outline-secondary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-success fw-bold shadow-sm btn-exec-payer"
                                                    data-op-id="{{ $op->id }}"
                                                    data-op-titre="{{ $op->titre }}"
                                                    data-op-montant="{{ number_format($op->montant ?? 0, 0, ',', ' ') }}"
                                                    data-op-demandeur="{{ $op->demandeur_name }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalExecPay">
                                                <i class="fas fa-check-circle me-1"></i> PAYER
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top">
                    {{ $operations->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-double fa-4x text-success opacity-25 d-block mb-3"></i>
                    <h5 class="text-muted">Tout est à jour !</h5>
                    <p class="text-muted">Aucune opération en attente d'exécution par la caisse.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL EXÉCUTION CAISSE --}}
<div class="modal fade" id="modalExecPay" tabindex="-1" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px">

            <div class="modal-header text-white" style="background:linear-gradient(135deg,#059669,#10b981); border-radius:14px 14px 0 0">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Exécuter le Paiement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formExecPay" method="POST" action="">
                @csrf
                <div class="modal-body">

                    {{-- Récap --}}
                    <div class="p-3 mb-3 rounded" style="background:#f0fdf4; border:1px solid #bbf7d0">
                        <div class="fw-bold text-dark mb-1" id="execOpTitre">—</div>
                        <div class="text-muted small">Demandeur : <span id="execOpDemandeur"></span></div>
                        <div class="mt-2">
                            Montant à décaisser :
                            <strong class="text-success" style="font-size:1.4rem" id="execOpMontant">0</strong>
                            <span class="text-muted">FCFA</span>
                        </div>
                    </div>

                    {{-- Mode --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-credit-card me-1 text-primary"></i>
                            Mode de paiement <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['especes' => ['💵', 'Espèces'], 'cheque' => ['📄', 'Chèque'], 'virement' => ['🏦', 'Virement'], 'mobile_money' => ['📱', 'Mobile Money'], 'autre' => ['📋', 'Autre']] as $val => $lbl)
                                <label class="mode-btn">
                                    <input type="radio" name="mode_paiement" value="{{ $val }}" class="d-none mode-radio">
                                    <span class="btn btn-outline-secondary btn-sm mode-label">
                                        {{ $lbl[0] }} {{ $lbl[1] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Référence --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-hashtag me-1 text-secondary"></i> Référence / Reçu
                        </label>
                        <input type="text" name="payment_reference" id="exec_ref"
                               class="form-control" placeholder="Ex : REC-2026-042" maxlength="100">
                    </div>

                    {{-- Commentaire --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-comment me-1 text-secondary"></i> Note caisse
                        </label>
                        <textarea name="commentaire" class="form-control" rows="2"
                                  placeholder="Observations sur le paiement…" maxlength="500"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" id="btnConfirmPay"
                            class="btn btn-success fw-bold px-4"
                            onclick="return confirm('Confirmer le décaissement ? Cette action est irréversible.')">
                        <i class="fas fa-check-circle me-2"></i> Confirmer le Paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .mode-radio:checked + .mode-label {
        background: #198754;
        color: #fff;
        border-color: #198754;
    }
    .mode-label { cursor: pointer; user-select: none; transition: all .15s; }
    .mode-label:hover { background: #d1fae5; border-color: #059669; }
</style>
@endpush

@push('scripts')
<script>
    const markPaidUrlTemplate = @json(route('validations.operations.markPaid', ['operation' => '__OPERATION__']));

    document.querySelectorAll('.btn-exec-payer').forEach(btn => {
        btn.addEventListener('click', function () {
            const id        = this.dataset.opId;
            const titre     = this.dataset.opTitre;
            const montant   = this.dataset.opMontant;
            const demandeur = this.dataset.opDemandeur;

            document.getElementById('formExecPay').action = markPaidUrlTemplate.replace('__OPERATION__', id);
            document.getElementById('execOpTitre').textContent     = titre;
            document.getElementById('execOpMontant').textContent   = montant;
            document.getElementById('execOpDemandeur').textContent = demandeur;
            // Reset radios
            document.querySelectorAll('.mode-radio').forEach(r => r.checked = false);
            document.getElementById('exec_ref').value = '';
        });
    });
</script>
@endpush
@endsection
