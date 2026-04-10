<!-- Modal de Validation -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="validationModalLabel">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="validationTitle">Valider l'Opération</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="validationForm" method="POST" action="">
                @csrf
                <input type="hidden" name="operation_id" id="operationId">
                <input type="hidden" name="step" id="validationStep">
                <input type="hidden" name="action" id="validationAction">
                
                <div class="modal-body">
                    <!-- Informations de l'opération -->
                    <div class="alert alert-info mb-3">
                        <h6 class="mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails de l'Opération
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Référence:</strong> <span id="operationRef">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Montant:</strong> <span id="operationAmount">-</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>Titre:</strong> <span id="operationTitle">-</span>
                        </div>
                    </div>

                    <!-- Informations du validateur -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-user-check me-2"></i>
                                <span id="validatorRole">Validateur</span>
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Votre Nom</label>
                                    <input type="text" class="form-control" id="validatorName" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Votre Email</label>
                                    <input type="email" class="form-control" id="validatorEmail" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Champ de commentaire (requis pour rejet) -->
                    <div id="commentSection" class="mb-3" style="display: none;">
                        <label for="commentaire" class="form-label">
                            <i class="fas fa-comment me-1"></i>
                            Commentaire <span class="text-danger">*</span>
                        </label>
                        <textarea name="commentaire" id="commentaire" class="form-control" rows="3" 
                                  placeholder="Veuillez indiquer le motif de votre décision..."></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Le commentaire est obligatoire en cas de rejet
                        </div>
                    </div>

                    <!-- Message de confirmation -->
                    <div id="confirmationMessage" class="alert alert-success" style="display: none;">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="confirmationText"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>
                        <span id="submitText">Valider</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour le pop-up de validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const validationModal = new bootstrap.Modal(document.getElementById('validationModal'));
    const validationForm = document.getElementById('validationForm');
    const commentSection = document.getElementById('commentSection');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const validatorRole = document.getElementById('validatorRole');
    const validatorName = document.getElementById('validatorName');
    const validatorEmail = document.getElementById('validatorEmail');
    const confirmationMessage = document.getElementById('confirmationMessage');
    const confirmationText = document.getElementById('confirmationText');

    // Fonction pour ouvrir le pop-up de validation
    window.openValidationModal = function(operationId, step, action, operationData) {
        // Remplir les informations de l'opération
        document.getElementById('operationId').value = operationId;
        document.getElementById('validationStep').value = step;
        document.getElementById('validationAction').value = action;
        
        document.getElementById('operationRef').textContent = operationData.ref || 'N/A';
        document.getElementById('operationAmount').textContent = operationData.amount || 'N/A';
        document.getElementById('operationTitle').textContent = operationData.title || 'N/A';

        // Définir le rôle du validateur
        let roleText = '';
        if (step == 1) {
            roleText = '1er Validateur';
        } else if (step == 2) {
            roleText = '2e Validateur';
        } else if (step == 3) {
            roleText = 'Validateur Final (Destinataire Principal)';
        }
        validatorRole.textContent = roleText;

        // Remplir les informations du validateur
        validatorName.value = operationData.validatorName || '';
        validatorEmail.value = operationData.validatorEmail || '';

        // Configurer l'interface selon l'action
        if (action === 'reject') {
            submitBtn.className = 'btn btn-danger';
            submitText.textContent = 'Rejeter';
            document.getElementById('validationTitle').textContent = 'Rejeter l\'Opération';
            commentSection.style.display = 'block';
            document.getElementById('commentaire').required = true;
        } else {
            submitBtn.className = 'btn btn-success';
            submitText.textContent = 'Approuver';
            document.getElementById('validationTitle').textContent = 'Approuver l\'Opération';
            commentSection.style.display = 'none';
            document.getElementById('commentaire').required = false;
        }

        // Configurer l'action du formulaire vers la bonne route Laravel
        // Route définie dans routes/web.php : prefix('operations')
        // Nom complet : operations.validate
        validationForm.action = `{{ route('operations.validate', ['operation' => '__OP_ID__']) }}`.replace('__OP_ID__', operationId);

        // Afficher le modal
        validationModal.show();
    };

    // Gérer la soumission du formulaire
    validationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(validationForm);
        const action = formData.get('action');
        const commentaire = formData.get('commentaire');

        // Validation du commentaire pour le rejet
        if (action === 'reject' && !commentaire.trim()) {
            alert('Le commentaire est obligatoire en cas de rejet.');
            return;
        }

        // Désactiver le bouton de soumission
        submitBtn.disabled = true;
        submitText.textContent = action === 'reject' ? 'Rejet en cours...' : 'Validation en cours...';

        // Envoyer la requête AJAX
        fetch(validationForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher le message de confirmation
                confirmationMessage.style.display = 'block';
                confirmationText.textContent = data.message || 'Opération validée avec succès!';
                
                // Cacher le formulaire
                validationForm.style.display = 'none';
                
                // Rediriger après un délai
                setTimeout(() => {
                    window.location.href = data.redirect || '/operations';
                }, 2000);
            } else {
                alert('Erreur: ' + (data.message || 'Une erreur est survenue.'));
                submitBtn.disabled = false;
                submitText.textContent = action === 'reject' ? 'Rejeter' : 'Approuver';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
            submitBtn.disabled = false;
            submitText.textContent = action === 'reject' ? 'Rejeter' : 'Approuver';
        });
    });

    // Réinitialiser le modal à la fermeture
    document.getElementById('validationModal').addEventListener('hidden.bs.modal', function() {
        validationForm.reset();
        validationForm.style.display = 'block';
        confirmationMessage.style.display = 'none';
        submitBtn.disabled = false;
        commentSection.style.display = 'none';
        document.getElementById('commentaire').required = false;
    });
});
</script>
