@props([
    'operation' => null,
    'services' => [],
    'seuil' => 1000000
])

<div class="operation-validation-container">
    <!-- Header de validation -->
    <div class="validation-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">
                    <i class="fas fa-shield-alt text-primary me-2"></i>
                    Validation d'Opération
                </h5>
                <p class="text-muted mb-0">
                    Seuil de validation spéciale: {{ number_format($seuil, 0, ',', ' ') }} FCFA
                </p>
            </div>
            <div class="validation-status">
                @if($operation && $operation->montant >= $seuil)
                    <span class="badge badge-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Validation DG/Manager Requise
                    </span>
                @else
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle me-1"></i>
                        Validation Standard
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Formulaire de validation -->
    <form id="operationValidationForm" class="validation-form">
        @csrf
        <input type="hidden" name="operation_id" value="{{ $operation?->id }}">

        <div class="row">
            <!-- Montant -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label-kenam">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Montant de l'Opération (FCFA)
                    </label>
                    <div class="input-group">
                        <input type="number"
                               class="form-control-kenam"
                               name="montant"
                               id="montantOperation"
                               value="{{ $operation?->montant ?? 0 }}"
                               min="0"
                               step="1000"
                               required>
                        <span class="input-group-text">FCFA</span>
                    </div>
                    <div class="form-text">
                        <small id="montantValidationMessage"></small>
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label-kenam">
                        <i class="fas fa-info-circle me-2"></i>
                        Statut de Validation
                    </label>
                    <select class="form-control-kenam" name="statut" id="statutOperation">
                        <option value="en_attente">En Attente</option>
                        <option value="en_validation">En Validation</option>
                        <option value="approuve">Approuvé</option>
                        <option value="rejete">Rejeté</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Services Additionnels (si montant >= seuil) -->
        <div id="servicesAdditionnelsSection" class="services-section mb-4" style="display: none;">
            <div class="card-kenam">
                <div class="card-kenam-header">
                    <h6 class="mb-0">
                        <i class="fas fa-users-cog me-2"></i>
                        Services Additionnels à Notifier
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Sélectionnez les services additionnels qui doivent être notifiés pour cette opération.
                    </p>

                    <div class="row" id="servicesGrid">
                        @foreach($services as $service)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="service-selector">
                                <label class="service-card">
                                    <input type="radio"
                                           name="services_additionnels"
                                           value="{{ $service->id }}"
                                           class="service-radio">
                                    <div class="service-card-content">
                                        <div class="service-icon" style="background: {{ $service->couleur }}20; color: {{ $service->couleur }};">
                                            <i class="{{ $service->icone }}"></i>
                                        </div>
                                        <div class="service-info">
                                            <div class="service-name">{{ $service->nom }}</div>
                                            <div class="service-code">{{ $service->code }}</div>
                                            <div class="service-email">{{ $service->email }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Option aucun service -->
                    <div class="col-12">
                        <label class="no-service-option">
                            <input type="radio" name="services_additionnels" value="" checked>
                            <span>Aucun service additionnel</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commentaire de validation -->
        <div class="form-group mb-4">
            <label class="form-label-kenam">
                <i class="fas fa-comment-dots me-2"></i>
                Commentaire de Validation
            </label>
            <textarea class="form-control-kenam"
                      name="commentaire"
                      rows="4"
                      placeholder="Ajoutez un commentaire concernant cette validation...">{{ $operation?->commentaire_validation ?? '' }}</textarea>
        </div>

        <!-- Actions -->
        <div class="validation-actions">
            <div class="d-flex justify-content-between align-items-center">
                <div class="validation-summary">
                    <div id="validationSummary" class="alert alert-info" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="summaryText"></span>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn btn-secondary" onclick="annulerValidation()">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-kenam-primary" id="submitValidation">
                        <i class="fas fa-check me-2"></i>
                        Valider l'Opération
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.operation-validation-container {
    background: #ffffff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.validation-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 20px;
    margin-bottom: 25px;
}

.service-selector {
    width: 100%;
}

.service-card {
    display: block;
    cursor: pointer;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
    background: #ffffff;
}

.service-card:hover {
    border-color: #28a745;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
}

.service-card input[type="radio"] {
    display: none;
}

.service-card input[type="radio"]:checked + .service-card-content {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.service-card-content {
    display: flex;
    align-items: center;
    gap: 12px;
    border-radius: 6px;
    padding: 8px;
    transition: all 0.3s ease;
}

.service-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.service-info {
    flex: 1;
    min-width: 0;
}

.service-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
    margin-bottom: 2px;
}

.service-code {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 2px;
}

.service-email {
    font-size: 11px;
    color: #adb5bd;
    word-break: break-all;
}

.no-service-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.no-service-option:hover {
    background: #f8f9fa;
}

.no-service-option input[type="radio"] {
    margin: 0;
}

.validation-actions {
    border-top: 2px solid #e9ecef;
    padding-top: 20px;
    margin-top: 25px;
}

.amount-alert {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border: 1px solid #dc3545;
    color: #721c24;
    padding: 12px 16px;
    border-radius: 8px;
    margin: 10px 0;
}

.amount-normal {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: 1px solid #28a745;
    color: #155724;
    padding: 12px 16px;
    border-radius: 8px;
    margin: 10px 0;
}

@media (max-width: 768px) {
    .operation-validation-container {
        padding: 15px;
    }

    .service-card-content {
        flex-direction: column;
        text-align: center;
    }

    .service-icon {
        margin-bottom: 8px;
    }

    .validation-actions .d-flex {
        flex-direction: column;
        gap: 15px;
    }

    .action-buttons {
        width: 100%;
    }

    .action-buttons button {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const montantInput = document.getElementById('montantOperation');
    const servicesSection = document.getElementById('servicesAdditionnelsSection');
    const validationMessage = document.getElementById('montantValidationMessage');
    const validationSummary = document.getElementById('validationSummary');
    const summaryText = document.getElementById('summaryText');
    const seuil = {{ $seuil }};

    // Vérifier le montant à chaque changement
    montantInput.addEventListener('input', function() {
        const montant = parseFloat(this.value) || 0;

        if (montant >= seuil) {
            // Afficher la section des services additionnels
            servicesSection.style.display = 'block';

            // Message d'alerte
            validationMessage.innerHTML = `
                <div class="amount-alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention:</strong> Ce montant nécessite une validation du DG et Manager.
                    Les services additionnels seront automatiquement notifiés.
                </div>
            `;

            // Résumé de validation
            validationSummary.style.display = 'block';
            summaryText.textContent = `Cette opération de ${number_format(montant, 0, ',', ' ')} FCFA nécessite une validation spéciale et notifiera le DG et Manager.`;

            // Changer le statut automatiquement
            document.getElementById('statutOperation').value = 'en_validation';
        } else {
            // Masquer la section des services additionnels
            servicesSection.style.display = 'none';

            // Message normal
            validationMessage.innerHTML = `
                <div class="amount-normal">
                    <i class="fas fa-check-circle me-2"></i>
                    Ce montant ne nécessite pas de validation spéciale.
                </div>
            `;

            // Masquer le résumé
            validationSummary.style.display = 'none';

            // Réinitialiser le statut
            document.getElementById('statutOperation').value = 'en_attente';
        }
    });

    // Soumission du formulaire
    document.getElementById('operationValidationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const montant = parseFloat(formData.get('montant')) || 0;

        // Confirmation si montant élevé
        if (montant >= seuil) {
            if (!confirm(`Cette opération de ${number_format(montant, 0, ',', ' ')} FCFA sera automatiquement envoyée au DG et Manager pour validation. Confirmer ?`)) {
                return;
            }
        }

        // Désactiver le bouton
        const submitBtn = document.getElementById('submitValidation');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Validation en cours...';

        // Envoyer la requête AJAX
        fetch('{{ route("operations.validate", $operation->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                operation_id: formData.get('operation_id'),
                montant: montant,
                statut: formData.get('statut'),
                services_additionnels: formData.get('services_additionnels') ? [formData.get('services_additionnels')] : [],
                commentaire: formData.get('commentaire')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher le message de succès
                alert(data.message);

                // Recharger la page ou rediriger
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la validation.');
        })
        .finally(() => {
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Valider l\'Opération';
        });
    });
});

function annulerValidation() {
    if (confirm('Êtes-vous sûr de vouloir annuler cette validation ?')) {
        // Fermer le modal ou revenir en arrière
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/operations';
        }
    }
}

// Fonction de formatage des nombres
function numberFormat(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (sep[0]) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep[0]);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
</script>
