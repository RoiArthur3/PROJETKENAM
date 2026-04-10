@extends('layouts.app')

@section('title', 'Bon Pour Accord - Opération #' . str_pad($operation->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- En-tête -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-check-circle text-success" style="font-size: 40px;"></i>
                </div>
                <h2 class="fw-bold text-success mb-1">Opération Approuvée</h2>
                <p class="text-muted">Envoyez le Bon Pour Accord au service comptable ou trésorerie</p>
            </div>

            <!-- Carte résumé de l'opération -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $operation->titre }}</h5>
                            <span class="badge bg-secondary">Réf: #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-end">
                            <div class="fs-4 fw-bold text-success">{{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</div>
                            <small class="text-muted">Montant de l'opération</small>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Type</small>
                                    <span class="fw-semibold">{{ $operation->typeOperation->libelle ?? 'Opération' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-flag text-warning me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Priorité</small>
                                    <span class="fw-semibold">{{ ucfirst($operation->priorite ?? 'Normale') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-info me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Demandeur</small>
                                    <span class="fw-semibold">{{ $operation->demandeur_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-check text-success me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Date d'approbation</small>
                                    <span class="fw-semibold">{{ now()->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($operation->description)
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted d-block mb-1"><i class="fas fa-align-left me-1"></i>Description</small>
                        <p class="mb-0">{{ $operation->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Carte formulaire Bon Pour Accord -->
            <div class="card shadow border-0" id="bonPourAccordCard">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="m-0">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer le Bon Pour Accord
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Saisissez l'adresse email du <strong>comptable</strong> ou de la <strong>trésorerie</strong> pour leur transmettre le Bon Pour Accord d'exécution de cette opération.
                    </div>

                    <form id="bonPourAccordForm" action="{{ route('operations.send-execution-email', $operation->id) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-primary"></i>Email du destinataire
                            </label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" required
                                   placeholder="ex: comptabilite@entreprise.com"
                                   style="border-radius: 10px;">
                            <div class="form-text">
                                Adresse email du service comptable ou trésorerie qui doit recevoir le Bon Pour Accord.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">
                                <i class="fas fa-comment me-1 text-primary"></i>Message complémentaire <span class="text-muted fw-normal">(optionnel)</span>
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="3"
                                      placeholder="Ajoutez un message ou des instructions complémentaires..."
                                      style="border-radius: 10px;"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-secondary px-4" style="border-radius: 10px;">
                                <i class="fas fa-arrow-left me-2"></i>Retour à l'opération
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="btnEnvoyer" style="border-radius: 10px;">
                                <i class="fas fa-paper-plane me-2"></i>Envoyer le Bon Pour Accord
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<style>
    .bpa-overlay {
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
    .bpa-overlay.show { opacity: 1; }
    .bpa-box {
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
    .bpa-overlay.show .bpa-box { transform: scale(1) translateY(0); }
    .bpa-icon {
        width: 80px; height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
    }
    .bpa-icon.success { background: linear-gradient(135deg, #d4edda, #a8e6cf); color: #155724; }
    .bpa-icon.error { background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; }
    .bpa-icon i { animation: bpaPopIn 0.5s ease 0.3s both; }
    @keyframes bpaPopIn {
        0%   { transform: scale(0); }
        60%  { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    .bpa-spinner { display: none; }
    .bpa-spinner.active { display: block; }
</style>

<script>
document.getElementById('bonPourAccordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = document.getElementById('btnEnvoyer');
    const email = document.getElementById('email').value;

    if (!email) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: formData
    })
    .then(response => {
        if (response.redirected || response.ok) {
            return response.text().then(text => {
                try { return JSON.parse(text); } catch(e) { return { success: true }; }
            });
        }
        throw new Error('Erreur HTTP ' + response.status);
    })
    .then(data => {
        showBpaModal('success',
            'Bon Pour Accord envoyé !',
            'Le Bon Pour Accord a été envoyé avec succès à <strong>' + email + '</strong>.<br>Le destinataire recevra les détails de l\'opération pour exécution.',
            '{{ route("operations.show", $operation->id) }}'
        );
    })
    .catch(error => {
        showBpaModal('error',
            'Erreur d\'envoi',
            'Une erreur est survenue lors de l\'envoi : ' + error.message,
            null
        );
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer le Bon Pour Accord';
    });
});

function showBpaModal(type, title, message, redirectUrl) {
    const icons = { success: 'fa-check', error: 'fa-times' };
    const overlay = document.createElement('div');
    overlay.className = 'bpa-overlay';
    overlay.innerHTML = `
        <div class="bpa-box">
            <div class="bpa-icon ${type}"><i class="fas ${icons[type]}"></i></div>
            <h4 class="fw-bold mb-2">${title}</h4>
            <p class="text-muted mb-4">${message}</p>
            ${redirectUrl ? '<a href="' + redirectUrl + '" class="btn btn-primary px-4" style="border-radius:10px;"><i class="fas fa-arrow-right me-2"></i>Voir l\'opération</a>' : '<button class="btn btn-secondary px-4" onclick="this.closest(\'.bpa-overlay\').remove()" style="border-radius:10px;">Fermer</button>'}
            ${redirectUrl ? '<div class="mt-3 text-muted small">Redirection automatique dans <strong id="bpaCountdown">5</strong>s</div>' : ''}
        </div>
    `;
    document.body.appendChild(overlay);
    requestAnimationFrame(() => overlay.classList.add('show'));

    if (redirectUrl) {
        let sec = 5;
        const timer = setInterval(() => {
            sec--;
            const el = document.getElementById('bpaCountdown');
            if (el) el.textContent = sec;
            if (sec <= 0) { clearInterval(timer); window.location.href = redirectUrl; }
        }, 1000);
    }
}
</script>
@endsection
