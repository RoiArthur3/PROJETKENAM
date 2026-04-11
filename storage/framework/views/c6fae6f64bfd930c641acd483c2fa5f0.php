<?php $__env->startSection('title', 'Créer une facture'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Créer une nouvelle facture</h5>
                    <a href="<?php echo e(route('comptabilite.factures.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <form id="factureForm">
                        <?php echo csrf_field(); ?>
                        <!-- Informations générales -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id">Client *</label>
                                    <select class="form-control" id="client_id" name="client_id" required>
                                        <option value="">Sélectionner un client</option>
                                        <?php if(isset($clients)): ?>
                                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($client->id); ?>">
                                                    <?php echo e($client->raison_sociale ?? $client->nom); ?> (<?php echo e($client->email); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero">Numéro de facture *</label>
                                    <input type="text" class="form-control" id="numero" name="numero"
                                           value="<?php echo e($nextNumero ?? 'FAC-' . date('Y-m-d') . '-001'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_fne">Numéro FNE</label>
                                    <input type="text" class="form-control" id="numero_fne" name="numero_fne" 
                                           placeholder="Numéro FNE (manuel)">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_facture">Date de facture *</label>
                                    <input type="date" class="form-control" id="date_facture" name="date_facture" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bon_commande_id">Bon de commande</label>
                                    <select class="form-control" id="bon_commande_id" name="bon_commande_id">
                                        <option value="">Sélectionner un bon de commande</option>
                                        <?php if(isset($bonsCommande)): ?>
                                            <?php $__currentLoopData = $bonsCommande; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($bc->id); ?>">
                                                    <?php echo e($bc->numero_bc ?? 'BC-' . $bc->id); ?> - <?php echo e($bc->reference ?? ''); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mission">Mission</label>
                                    <input type="text" class="form-control" id="mission" name="mission" 
                                           placeholder="Description de la mission (optionnel)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="acompte_paye">Acompte payé</label>
                                    <input type="number" class="form-control" id="acompte_paye" name="acompte_paye" 
                                           min="0" step="0.01" placeholder="Montant de l'acompte">
                                </div>
                            </div>
                        </div>

                        <!-- Articles -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Articles</h6>
                                <button type="button" class="btn btn-sm btn-primary" id="addArticle">
                                    <i class="fas fa-plus"></i> Ajouter un article
                                </button>
                            </div>
                            <div id="articlesContainer">
                                <!-- Premier article par défaut -->
                                <div class="article-row border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label>Description *</label>
                                            <input type="text" class="form-control" name="articles[0][description]" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Quantité *</label>
                                            <input type="number" class="form-control" name="articles[0][quantite]" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Prix unitaire *</label>
                                            <input type="number" class="form-control" name="articles[0][prix_unitaire]" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>TVA (%)</label>
                                            <input type="number" class="form-control" name="articles[0][tva]" min="0" max="100" step="0.01" value="18">
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-sm btn-danger remove-article">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Réduction et conditions -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="remise_globale">Remise globale (%)</label>
                                    <input type="number" class="form-control" id="remise_globale" name="remise_globale" min="0" max="100" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="conditions_paiement">Conditions de paiement</label>
                                    <textarea class="form-control" id="conditions_paiement" name="conditions_paiement" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group mb-4">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                        <input type="hidden" id="montant_ht" name="montant_ht" value="0">
                        <input type="hidden" id="tva_global" name="tva" value="0">
                        <input type="hidden" id="montant_ttc" name="montant_ttc" value="0">
                        <input type="hidden" id="statut" name="statut" value="en_attente">

                        <!-- Récapitulatif -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Récapitulatif</h6>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <div class="d-flex justify-content-between">
                                            <span>Total HT:</span>
                                            <span id="totalHT">0 FCFA</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>TVA:</span>
                                            <span id="totalTVA">0 FCFA</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Remise:</span>
                                            <span id="totalRemise">0 FCFA</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between font-weight-bold">
                                            <span>Total TTC:</span>
                                            <span id="totalTTC">0 FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="text-right mt-4">
                            <a href="<?php echo e(route('comptabilite.factures.index')); ?>" class="btn btn-secondary">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer la facture
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let articleIndex = 1;

    // Ajouter un article
    $('#addArticle').click(function() {
        const articleHtml = `
            <div class="article-row border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <label>Description *</label>
                        <input type="text" class="form-control" name="articles[${articleIndex}][description]" required>
                    </div>
                    <div class="col-md-2">
                        <label>Quantité *</label>
                        <input type="number" class="form-control" name="articles[${articleIndex}][quantite]" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label>Prix unitaire *</label>
                        <input type="number" class="form-control" name="articles[${articleIndex}][prix_unitaire]" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label>TVA (%)</label>
                        <input type="number" class="form-control" name="articles[${articleIndex}][tva]" min="0" max="100" step="0.01" value="18">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-sm btn-danger remove-article">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#articlesContainer').append(articleHtml);
        articleIndex++;
    });

    // Supprimer un article
    $(document).on('click', '.remove-article', function() {
        if($('.article-row').length > 1) {
            $(this).closest('.article-row').remove();
            calculateTotals();
        }
    });

    // Calculer les totaux
    function calculateTotals() {
        let totalHT = 0;
        let totalTVA = 0;

        $('.article-row').each(function() {
            const quantite = parseFloat($(this).find('input[name*="[quantite]"]').val()) || 0;
            const prixUnitaire = parseFloat($(this).find('input[name*="[prix_unitaire]"]').val()) || 0;
            const tva = parseFloat($(this).find('input[name*="[tva]"]').val()) || 0;

            const montantHT = quantite * prixUnitaire;
            const montantTVA = montantHT * (tva / 100);

            totalHT += montantHT;
            totalTVA += montantTVA;
        });

        const remiseGlobale = parseFloat($('#remise_globale').val()) || 0;
        const montantRemise = totalHT * (remiseGlobale / 100);
        const totalTTC = totalHT + totalTVA - montantRemise;
        const tauxTvaGlobal = totalHT > 0 ? (totalTVA / totalHT) * 100 : 0;

        $('#totalHT').text(totalHT.toLocaleString('fr-FR') + ' FCFA');
        $('#totalTVA').text(totalTVA.toLocaleString('fr-FR') + ' FCFA');
        $('#totalRemise').text(montantRemise.toLocaleString('fr-FR') + ' FCFA');
        $('#totalTTC').text(totalTTC.toLocaleString('fr-FR') + ' FCFA');

        $('#montant_ht').val(totalHT.toFixed(2));
        $('#tva_global').val(tauxTvaGlobal.toFixed(2));
        $('#montant_ttc').val(Math.max(0, totalTTC).toFixed(2));
    }

    // Recalculer lors des changements
    $(document).on('input change', '.article-row input, #remise_globale', calculateTotals);

    // Soumission du formulaire
    $('#factureForm').submit(function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: '<?php echo e(route("comptabilite.factures.store")); ?>',
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    alert('✅ ' + response.message);
                    window.location.href = response.redirect || '<?php echo e(route("comptabilite.factures.index")); ?>';
                } else {
                    alert('❌ ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMsg = '❌ Erreur lors de la création de la facture';
                try {
                    const response = xhr.responseJSON;
                    if (response && response.message) {
                        errorMsg = response.message;
                        if (response.errors) {
                            errorMsg += '\n\nErreurs de validation:';
                            for (let field in response.errors) {
                                errorMsg += '\n- ' + response.errors[field].join(', ');
                            }
                        }
                    }
                } catch(e) {
                    errorMsg += '\n' + xhr.responseText;
                }
                alert(errorMsg);
                console.error('Erreur AJAX:', xhr);
            }
        });
    });

    // Date du jour par défaut
    $('#date_facture').val(new Date().toISOString().split('T')[0]);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/comptabilite/facturation-create.blade.php ENDPATH**/ ?>