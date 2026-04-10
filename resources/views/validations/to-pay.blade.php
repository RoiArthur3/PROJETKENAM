@extends('layouts.app')

@section('title', 'Opérations à Régler — Comptabilité')

@section('content')
<div class="content-wrapper">

    {{-- ── HEADER ─────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">
                <i class="fas fa-hand-holding-usd text-success me-2"></i>
                Opérations — BON POUR ACCORD
            </h1>
            <p class="text-muted mb-0 small">
                Ces opérations ont été approuvées par tous les validateurs.
                Choisissez la caisse qui effectuera le paiement.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('validations.paid') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-history me-1"></i> Historique payées
            </a>
        </div>
    </div>

    {{-- ── ALERTES ──────────────────────────────────────────────────────── --}}
    @foreach(['success', 'error', 'info'] as $type)
        @if(session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show shadow-sm mb-4">
                <i class="fas fa-{{ $type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-triangle' : 'info-circle') }} me-2"></i>
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- ── KPI CARDS ────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 kpi-card kpi-warning">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="kpi-value">{{ $operations->total() }}</div>
                            <div class="kpi-label">En attente de paiement</div>
                        </div>
                        <i class="fas fa-hourglass-half kpi-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 kpi-card kpi-success">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="kpi-value">{{ number_format($totalEnAttente ?? 0, 0, ',', ' ') }}</div>
                            <div class="kpi-label">Montant total à décaisser (FCFA)</div>
                        </div>
                        <i class="fas fa-wallet kpi-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 kpi-card kpi-info">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="kpi-value">{{ $caisses->count() }}</div>
                            <div class="kpi-label">Caisses disponibles</div>
                        </div>
                        <i class="fas fa-cash-register kpi-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TABLEAU ──────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-list-check text-primary me-2"></i>
                        Liste des opérations à décaisser
                    </h5>
                </div>
                <div class="col-auto">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="q" value="{{ $search ?? '' }}"
                               class="form-control form-control-sm" placeholder="Rechercher…" style="width:220px">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($operations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-nowrap">N° Opération</th>
                                <th>Opération / Demandeur</th>
                                <th>Service</th>
                                <th class="text-end">Montant</th>
                                <th class="text-center">Priorité</th>
                                <th class="text-center">BON POUR ACCORD</th>
                                <th class="text-center pe-4">Action Comptabilité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($operations as $op)
                                <tr id="row-{{ $op->id }}">
                                    {{-- N° Opération --}}
                                    <td class="ps-4">
                                        <span class="badge bg-secondary-soft text-secondary fw-semibold">
                                            {{ $op->numero_operation ?? '#' . $op->id }}
                                        </span>
                                        @if(($op->montant ?? 0) > 250000)
                                            <span class="badge bg-warning text-dark ms-1" title="Montant > 250 000 — validé DG">
                                                <i class="fas fa-building-columns"></i> DG
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Titre / Demandeur --}}
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $op->titre }}</div>
                                        <div class="text-muted small">
                                            <i class="fas fa-user me-1"></i>{{ $op->demandeur_name ?? '—' }}
                                            <span class="mx-1">·</span>
                                            <i class="fas fa-clock me-1"></i>{{ $op->created_at?->diffForHumans() }}
                                        </div>
                                        @if($op->description)
                                            <div class="text-muted small mt-1">{{ Str::limit($op->description, 60) }}</div>
                                        @endif
                                    </td>

                                    {{-- Service --}}
                                    <td class="text-nowrap">
                                        {{ $op->operationalService?->nom ?? '—' }}
                                    </td>

                                    {{-- Montant --}}
                                    <td class="text-end">
                                        <span class="fw-bold fs-6 text-dark">
                                            {{ number_format($op->montant ?? 0, 0, ',', ' ') }}
                                        </span>
                                        <small class="text-muted d-block">FCFA</small>
                                    </td>

                                    {{-- Priorité --}}
                                    <td class="text-center">
                                        @php
                                            $colors = ['urgente' => 'danger', 'haute' => 'warning', 'moyenne' => 'info', 'basse' => 'secondary'];
                                            $color  = $colors[$op->priorite] ?? 'secondary';
                                        @endphp
                                        <span class="badge rounded-pill bg-{{ $color }}">
                                            {{ ucfirst($op->priorite) }}
                                        </span>
                                    </td>

                                    {{-- Caisse déjà désignée ? --}}
                                    <td class="text-center">
                                        @if($op->caisse_email)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2">
                                                <i class="fas fa-check-circle me-1"></i>
                                                {{ $op->caisseExecutante?->nom ?? $op->caisse_email }}
                                            </span>
                                            <div class="text-muted" style="font-size:0.7rem">
                                                {{ $op->bon_pour_accord_at?->format('d/m/Y H:i') }}
                                            </div>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">
                                                <i class="fas fa-clock me-1"></i> À désigner
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('operations.show', $op->id) }}"
                                               class="btn btn-sm btn-outline-secondary" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Bouton A PAYER → ouvre le popup --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-success fw-bold shadow-sm btn-payer"
                                                    data-op-id="{{ $op->id }}"
                                                    data-op-titre="{{ $op->titre }}"
                                                    data-op-montant="{{ number_format($op->montant ?? 0, 0, ',', ' ') }}"
                                                    data-op-demandeur="{{ $op->demandeur_name }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalPaiement">
                                                <i class="fas fa-money-bill-wave me-1"></i> À PAYER
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
                    <i class="fas fa-check-double fa-4x text-success opacity-25 mb-3 d-block"></i>
                    <h5 class="text-muted">Tout est réglé !</h5>
                    <p class="text-muted">Aucune opération approuvée n'attend de règlement.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL — Sélection de la caisse et exécution du paiement
══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalPaiement" tabindex="-1" aria-labelledby="modalPaiementLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalPaiementLabel">
                    <i class="fas fa-money-check-alt me-2"></i>
                    Désigner la caisse et émettre le bon pour exécution
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formPaiement" method="POST" action="">
                @csrf
                <div class="modal-body">

                    {{-- Récap opération --}}
                    <div class="alert alert-info py-2 mb-3">
                        <div class="fw-bold" id="modalOpTitre">—</div>
                        <small class="text-muted">Demandeur : <span id="modalOpDemandeur">—</span></small>
                        <div class="mt-1">
                            Montant : <strong class="text-success fs-5" id="modalOpMontant">0</strong>
                            <span class="text-muted">FCFA</span>
                        </div>
                    </div>

                    {{-- Choix de la caisse --}}
                    <div class="mb-3">
                        <label for="caisse_id" class="form-label fw-semibold">
                            <i class="fas fa-cash-register me-1 text-success"></i>
                            Caisse qui effectue le paiement <span class="text-danger">*</span>
                        </label>
                        @if($caisses->count() > 0)
                            <select name="caisse_id" id="caisse_id" class="form-select" required
                                    onchange="syncCaisseEmail(this)">
                                <option value="">— Sélectionner une caisse —</option>
                                @foreach($caisses as $caisse)
                                    @php
                                        $caisseContactEmail = $caisse->email ?? optional($caisse->responsable)->email;
                                    @endphp
                                    <option value="{{ $caisse->id }}"
                                            data-email="{{ $caisseContactEmail }}"
                                            data-nom="{{ $caisse->nom }}">
                                        {{ $caisse->nom }}
                                        @if($caisseContactEmail) — {{ $caisseContactEmail }} @endif
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-warning py-2 mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Aucune caisse configurée. Entrez l'email manuellement.
                            </div>
                        @endif
                        <input type="hidden" name="caisse_email" id="caisse_email_hidden">
                        {{-- Fallback texte si pas de caisse --}}
                        <input type="email" name="caisse_email_manual" id="caisse_email_manual"
                               class="form-control mt-2 {{ $caisses->count() > 0 ? 'd-none' : '' }}"
                               placeholder="Email de la caisse">
                        <small id="caisse_email_help" class="text-muted d-none mt-1">
                            Aucun email n'est configuré sur cette caisse. Veuillez saisir un email manuellement.
                        </small>
                    </div>

                    {{-- Mode de paiement --}}
                    <div class="mb-3">
                        <label for="mode_paiement" class="form-label fw-semibold">
                            <i class="fas fa-credit-card me-1 text-primary"></i>
                            Mode de paiement <span class="text-danger">*</span>
                        </label>
                        <select name="mode_paiement" id="mode_paiement" class="form-select" required>
                            <option value="">— Choisir —</option>
                            <option value="especes">💵 Espèces</option>
                            <option value="cheque">📄 Chèque</option>
                            <option value="virement">🏦 Virement bancaire</option>
                            <option value="mobile_money">📱 Mobile Money</option>
                            <option value="autre">📋 Autre</option>
                        </select>
                    </div>

                    {{-- Référence --}}
                    <div class="mb-3">
                        <label for="payment_reference" class="form-label fw-semibold">
                            <i class="fas fa-hashtag me-1 text-secondary"></i>
                            Référence / N° de pièce
                        </label>
                        <input type="text" name="payment_reference" id="payment_reference"
                               class="form-control" placeholder="Ex : CHQ-2026-001 / REC-0042…" maxlength="100">
                    </div>

                    {{-- Commentaire --}}
                    <div class="mb-1">
                        <label for="commentaire_paiement" class="form-label fw-semibold">
                            <i class="fas fa-comment me-1 text-secondary"></i>
                            Commentaire (optionnel)
                        </label>
                        <textarea name="commentaire" id="commentaire_paiement"
                                  class="form-control" rows="2" maxlength="500"
                                  placeholder="Observations, instructions pour la caisse…"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fas fa-paper-plane me-2"></i>
                        Envoyer le bon pour exécution
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('styles')
<style>
    /* KPI Cards */
    .kpi-card { border-radius: 12px; overflow: hidden; position: relative; }
    .kpi-card::before {
        content: '';
        position: absolute; inset: 0;
        opacity: .12;
        border-radius: inherit;
    }
    .kpi-warning { background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%); border-left: 4px solid #ffc107 !important; }
    .kpi-success { background: linear-gradient(135deg, #e8f8f1 0%, #d1f0e3 100%); border-left: 4px solid #198754 !important; }
    .kpi-info    { background: linear-gradient(135deg, #e7f3ff 0%, #cfe2ff 100%); border-left: 4px solid #0d6efd !important; }
    .kpi-value   { font-size: 1.8rem; font-weight: 800; line-height: 1.1; }
    .kpi-label   { font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; margin-top: 4px; }
    .kpi-icon    { font-size: 2.4rem; opacity: .18; }

    /* Table */
    .table > tbody > tr:hover { background-color: #f8fffe; }
    .badge.bg-secondary-soft { background-color: #f0f0f3; color: #495057; border: 1px solid #dee2e6; }
    .badge.bg-success-subtle { background-color: #d1e7dd; }
    .badge.bg-warning-subtle { background-color: #fff3cd; }

    /* Modal */
    #modalPaiement .modal-content { border-radius: 14px; }
    #modalPaiement .modal-header  { border-radius: 14px 14px 0 0; }
</style>
@endpush

@push('scripts')
<script>
    const emitExecutionUrlTemplate = @json(route('validations.emit-execution', ['operation' => '__OPERATION__']));

    // Pré-remplir le modal avec les infos de la ligne cliquée
    document.querySelectorAll('.btn-payer').forEach(btn => {
        btn.addEventListener('click', function () {
            const opId       = this.dataset.opId;
            const opTitre    = this.dataset.opTitre;
            const opMontant  = this.dataset.opMontant;
            const opDemandeur= this.dataset.opDemandeur;

            // Action du formulaire
            document.getElementById('formPaiement').action = emitExecutionUrlTemplate.replace('__OPERATION__', opId);

            // Récap
            document.getElementById('modalOpTitre').textContent    = opTitre;
            document.getElementById('modalOpMontant').textContent  = opMontant;
            document.getElementById('modalOpDemandeur').textContent= opDemandeur;

            // Reset champs
            if (document.getElementById('caisse_id')) {
                document.getElementById('caisse_id').value = '';
            }
            document.getElementById('caisse_email_hidden').value = '';
            const manualEmailInput = document.getElementById('caisse_email_manual');
            if (manualEmailInput) {
                manualEmailInput.value = '';
                manualEmailInput.required = false;
                if (document.getElementById('caisse_id')) {
                    manualEmailInput.classList.add('d-none');
                }
            }
            document.getElementById('mode_paiement').value = '';
            document.getElementById('payment_reference').value = '';
            document.getElementById('commentaire_paiement').value = '';

            // Synchronise immédiatement l'email caché après reset/choix
            syncCaisseEmail(document.getElementById('caisse_id'));
        });
    });

    // Synchronise l'email caché quand on choisit une caisse dans le select
    function syncCaisseEmail(sel) {
        if (!sel) {
            return;
        }

        const manualEmailInput = document.getElementById('caisse_email_manual');
        const helpText = document.getElementById('caisse_email_help');
        const opt = sel.options[sel.selectedIndex];
        const email = opt ? (opt.dataset.email ?? '').trim() : '';
        document.getElementById('caisse_email_hidden').value = email;

        if (!manualEmailInput) {
            return;
        }

        // Si aucune caisse sélectionnée, on masque le champ manuel (le select reste obligatoire)
        if (!sel.value) {
            manualEmailInput.required = false;
            if (helpText) {
                helpText.classList.add('d-none');
            }
            if (sel) {
                manualEmailInput.classList.add('d-none');
            }
            return;
        }

        // Si la caisse sélectionnée n'a pas d'email, on demande la saisie manuelle
        if (!email) {
            manualEmailInput.classList.remove('d-none');
            manualEmailInput.required = true;
            if (helpText) {
                helpText.classList.remove('d-none');
            }
            return;
        }

        // Sinon on utilise l'email de la caisse et on masque la saisie manuelle
        manualEmailInput.required = false;
        manualEmailInput.value = '';
        manualEmailInput.classList.add('d-none');
        if (helpText) {
            helpText.classList.add('d-none');
        }
    }

    // Validation avant envoi
    document.getElementById('formPaiement')?.addEventListener('submit', function(e) {
        const caisseId = document.getElementById('caisse_id')?.value;
        const caisseEmail = document.getElementById('caisse_email_hidden')?.value;
        const caisseEmailManual = document.getElementById('caisse_email_manual')?.value?.trim();
        const mode   = document.getElementById('mode_paiement').value;

        if (!caisseId) {
            e.preventDefault();
            alert('Veuillez sélectionner une caisse.');
            return;
        }
        if (!caisseEmail && !caisseEmailManual) {
            e.preventDefault();
            alert('Veuillez renseigner un email valide pour la caisse sélectionnée.');
            return;
        }
        if (!mode) {
            e.preventDefault();
            alert('Veuillez choisir le mode de paiement.');
            return;
        }

        const btn = this.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours…';
    });
</script>
@endpush
@endsection
