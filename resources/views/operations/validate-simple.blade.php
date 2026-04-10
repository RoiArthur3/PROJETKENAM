@extends('layouts.app')

@section('title', 'Validation de l\'Opération')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <!-- En-tête -->
            <div class="mb-4">
                <a href="{{ route('operations.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>

            <!-- Détails de l'opération -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0">
                        <i class="fas fa-file-alt me-2"></i>Détails de l'Opération
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Référence :</label>
                            <p>#{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Statut :</label>
                            <p>
                                <span class="badge bg-{{ $operation->statut_courant === 'approuvee' ? 'success' : ($operation->statut_courant === 'rejetee' ? 'danger' : 'warning') }}">
                                    {{ ucfirst(str_replace('_', ' ', $operation->statut_courant)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Titre :</label>
                            <p>{{ $operation->titre }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Montant :</label>
                            <p>{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                    @if($operation->description)
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="fw-bold">Description :</label>
                            <p>{{ $operation->description }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <label class="fw-bold">Demandeur :</label>
                            <p>{{ $operation->demandeur_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Date Création :</label>
                            <p>{{ $operation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Étapes de validation -->
            @if($allSteps && $allSteps->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="m-0">
                        <i class="fas fa-tasks me-2"></i>Chaîne de Validation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($allSteps as $step)
                        <div class="timeline-item mb-3">
                            <div class="d-flex align-items-start">
                                <div class="timeline-marker me-3">
                                    @if($step->statut === 'VALIDEE')
                                        <i class="fas fa-check-circle text-success fa-lg"></i>
                                    @elseif($step->statut === 'REJETEE')
                                        <i class="fas fa-times-circle text-danger fa-lg"></i>
                                    @elseif($step->statut === 'EN_COURS')
                                        <i class="fas fa-spinner text-warning fa-lg"></i>
                                    @else
                                        <i class="fas fa-circle text-secondary fa-lg"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Étape {{ $step->ordre_validation }}</strong> - {{ $step->service_name }}
                                            <br>
                                            <small class="text-muted">{{ $step->service_email }}</small>
                                        </div>
                                        <span class="badge bg-{{ $step->statut === 'VALIDEE' ? 'success' : ($step->statut === 'REJETEE' ? 'danger' : ($step->statut === 'EN_COURS' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $step->statut)) }}
                                        </span>
                                    </div>
                                    @if(isset($step->date_validation) && $step->date_validation)
                                    <small class="text-muted d-block mt-2">
                                        Validé par {{ isset($step->validateur_id) && $step->validateur_id ? \App\Models\User::find($step->validateur_id)?->name : 'Système' }} le {{ \Carbon\Carbon::parse($step->date_validation)->format('d/m/Y H:i') }}
                                    </small>
                                    @endif
                                    @if(isset($step->commentaire) && $step->commentaire)
                                    <div class="alert alert-light mt-2 mb-0">
                                        <i class="fas fa-comment me-2"></i>{{ $step->commentaire }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Formulaire de validation (seulement si opération pas terminée) -->
            @if(!in_array($operation->statut_courant, ['approuvee', 'rejetee', 'termine']))
            <div class="card shadow border-primary mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="m-0">
                        <i class="fas fa-gavel me-2"></i>Validation de l'Étape {{ $currentStep->ordre_validation ?? 1 }}
                    </h5>
                </div>
                <div class="card-body">
                    <form id="validationForm" method="POST" action="{{ route('operations.validate', $operation->id) }}">
                        @csrf

                        <input type="hidden" name="action" id="validationAction" value="">
                        <input type="hidden" name="step" value="{{ $currentStep->ordre_validation ?? 1 }}">

                        <!-- Commentaire -->
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">
                                <i class="fas fa-comment me-2"></i>Commentaire
                            </label>
                            <textarea name="commentaire" id="commentaire" class="form-control @error('commentaire') is-invalid @enderror" rows="4" placeholder="Ajoutez votre commentaire (optionnel pour approbation, obligatoire pour rejet)..."></textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <button type="submit" class="btn btn-success w-100" onclick="document.getElementById('validationAction').value = 'approve'">
                                    <i class="fas fa-check me-2"></i>Approuver l'Étape
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="submit" class="btn btn-danger w-100" onclick="document.getElementById('validationAction').value = 'reject'">
                                    <i class="fas fa-times me-2"></i>Rejeter l'Étape
                                </button>
                            </div>
                        </div>

                        <!-- Information -->
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            @if($allSteps && $currentStep->ordre_validation < $allSteps->count())
                                Après votre approbation, le validateur suivant sera notifié par email.
                            @else
                                Après votre approbation, l'opération sera complètement validée et le demandeur notifié.
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-{{ $operation->statut_courant === 'approuvee' ? 'success' : 'danger' }} alert-dismissible fade show">
                <i class="fas fa-{{ $operation->statut_courant === 'approuvee' ? 'check-circle' : 'times-circle' }} me-2"></i>
                <strong>
                    @if($operation->statut_courant === 'approuvee')
                        Cette opération a été approuvée et ne peut plus être modifiée.
                    @else
                        Cette opération a été rejetée et ne peut plus être modifiée.
                    @endif
                </strong>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding: 10px 0;
    }

    .timeline-item {
        border-left: 3px solid #e9ecef;
        padding-left: 20px;
        position: relative;
    }

    .timeline-marker {
        position: absolute;
        left: -18px;
        top: 0;
        background: white;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    /* Modal de résultat */
    .result-modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .result-modal-overlay.show { opacity: 1; }

    .result-modal-box {
        background: white;
        border-radius: 16px;
        padding: 40px 32px 32px;
        max-width: 440px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        transform: scale(0.8) translateY(20px);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .result-modal-overlay.show .result-modal-box {
        transform: scale(1) translateY(0);
    }

    .result-icon {
        width: 80px; height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
    }
    .result-icon.success { background: linear-gradient(135deg, #d4edda, #a8e6cf); color: #155724; }
    .result-icon.danger  { background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; }
    .result-icon.error   { background: linear-gradient(135deg, #fff3cd, #ffeeba); color: #856404; }

    .result-icon i { animation: popIn 0.5s ease 0.3s both; }
    @keyframes popIn {
        0%   { transform: scale(0); }
        60%  { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .result-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .result-message {
        color: #6c757d;
        font-size: 0.95rem;
        margin-bottom: 8px;
        line-height: 1.5;
    }
    .result-detail {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 12px 16px;
        margin: 16px 0;
        font-size: 0.88rem;
        text-align: left;
    }
    .result-detail .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
    }
    .result-detail .detail-label { color: #6c757d; }
    .result-detail .detail-value { font-weight: 600; color: #212529; }

    .result-btn {
        display: inline-block;
        padding: 10px 32px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        color: white;
        border: none;
        cursor: pointer;
        margin-top: 8px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .result-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        color: white;
    }
    .result-btn.success { background: linear-gradient(135deg, #28a745, #20c997); }
    .result-btn.danger  { background: linear-gradient(135deg, #dc3545, #e74c3c); }
    .result-btn.error   { background: linear-gradient(135deg, #ffc107, #fd7e14); color: #212529; }

    .result-countdown {
        font-size: 0.8rem;
        color: #adb5bd;
        margin-top: 10px;
    }

    /* Spinner pendant l'envoi */
    .validation-spinner {
        display: none;
        text-align: center;
        padding: 20px;
    }
    .validation-spinner.active { display: block; }
    .validation-spinner .spinner-border { width: 3rem; height: 3rem; }
</style>

<!-- Spinner pendant l'envoi -->
<div class="validation-spinner" id="validationSpinner">
    <div class="spinner-border text-primary mb-3" role="status">
        <span class="visually-hidden">Envoi en cours...</span>
    </div>
    <p class="text-muted fw-bold">Traitement de la validation en cours...</p>
    <p class="text-muted small">Veuillez patienter quelques instants</p>
</div>

<script>
function showResultModal(type, title, message, redirectUrl, details) {
    // type: 'success', 'danger', 'error'
    const icons = { success: 'fa-check', danger: 'fa-times', error: 'fa-exclamation-triangle' };

    let detailsHtml = '';
    if (details) {
        detailsHtml = '<div class="result-detail">';
        if (details.operation) detailsHtml += '<div class="detail-row"><span class="detail-label">Opération</span><span class="detail-value">' + details.operation + '</span></div>';
        if (details.etape)    detailsHtml += '<div class="detail-row"><span class="detail-label">Étape</span><span class="detail-value">' + details.etape + '</span></div>';
        if (details.action)   detailsHtml += '<div class="detail-row"><span class="detail-label">Action</span><span class="detail-value">' + details.action + '</span></div>';
        if (details.par)      detailsHtml += '<div class="detail-row"><span class="detail-label">Validé par</span><span class="detail-value">' + details.par + '</span></div>';
        if (details.date)     detailsHtml += '<div class="detail-row"><span class="detail-label">Date</span><span class="detail-value">' + details.date + '</span></div>';
        detailsHtml += '</div>';
    }

    const overlay = document.createElement('div');
    overlay.className = 'result-modal-overlay';
    overlay.innerHTML = `
        <div class="result-modal-box">
            <div class="result-icon ${type}">
                <i class="fas ${icons[type]}"></i>
            </div>
            <div class="result-title">${title}</div>
            <div class="result-message">${message}</div>
            ${detailsHtml}
            <a href="${redirectUrl || '{{ route("operations.index") }}'}" class="result-btn ${type}">
                <i class="fas fa-arrow-right me-2"></i>Continuer
            </a>
            <div class="result-countdown" id="resultCountdown">Redirection automatique dans <strong>5</strong>s</div>
        </div>
    `;
    document.body.appendChild(overlay);

    // Animer l'apparition
    requestAnimationFrame(() => overlay.classList.add('show'));

    // Compte à rebours + redirection auto
    let seconds = 5;
    const countdownEl = overlay.querySelector('#resultCountdown strong');
    const timer = setInterval(() => {
        seconds--;
        if (countdownEl) countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = redirectUrl || '{{ route("operations.index") }}';
        }
    }, 1000);

    // Clic sur le bouton = redirection immédiate
    overlay.querySelector('.result-btn').addEventListener('click', () => clearInterval(timer));
}

document.getElementById('validationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const action = document.getElementById('validationAction').value;

    if (!action) {
        showResultModal('error', 'Action manquante', 'Veuillez cliquer sur Approuver ou Rejeter avant de soumettre.', null, null);
        return;
    }

    const buttons = form.querySelectorAll('button[type="submit"]');
    buttons.forEach(btn => btn.disabled = true);

    // Afficher le spinner
    document.getElementById('validationSpinner').classList.add('active');
    form.closest('.card').querySelector('.card-body').style.opacity = '0.4';

    const formData = new FormData(form);
    const formAction = form.getAttribute('action');
    const stepNum = formData.get('step');
    const actionLabel = action === 'approve' ? 'Approuvée' : 'Rejetée';
    const now = new Date().toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });

    fetch(formAction, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Erreur HTTP ' + response.status + ': ' + text.substring(0, 200));
            });
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('validationSpinner').classList.remove('active');

        if (data.success) {
            const isApprove = action === 'approve';
            showResultModal(
                isApprove ? 'success' : 'danger',
                isApprove ? 'Étape validée avec succès !' : 'Étape rejetée',
                data.message,
                data.redirect,
                {
                    operation: '#{{ str_pad($operation->id, 5, "0", STR_PAD_LEFT) }} — {{ $operation->titre }}',
                    etape: 'Étape ' + stepNum + ' / {{ $allSteps ? $allSteps->count() : "?" }}',
                    action: actionLabel,
                    par: '{{ Auth::user()->name }}',
                    date: now
                }
            );
        } else {
            showResultModal('error', 'Erreur de validation', data.message || 'Une erreur est survenue lors du traitement.', null, null);
            buttons.forEach(btn => btn.disabled = false);
            form.closest('.card').querySelector('.card-body').style.opacity = '1';
        }
    })
    .catch(error => {
        document.getElementById('validationSpinner').classList.remove('active');
        form.closest('.card').querySelector('.card-body').style.opacity = '1';
        showResultModal('error', 'Erreur de communication', error.message, null, null);
        buttons.forEach(btn => btn.disabled = false);
    });
});
</script>
@endsection
