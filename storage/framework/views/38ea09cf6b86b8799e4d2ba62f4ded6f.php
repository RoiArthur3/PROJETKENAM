<?php $__env->startSection('title', 'Nouvel Achat'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nouvel achat</h1>
        <a href="<?php echo e(route('achat.index')); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="<?php echo e(route('achat.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fournisseur *</label>
                        <select name="fournisseur_id" class="form-select" required>
                            <option value="">Sélectionner</option>
                            <?php $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($fournisseur->id); ?>" <?php if(old('fournisseur_id') == $fournisseur->id): echo 'selected'; endif; ?>><?php echo e($fournisseur->raison_sociale); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type d'achat *</label>
                        <input type="text" name="type_achat" class="form-control" value="<?php echo e(old('type_achat')); ?>" placeholder="Maintenance, prestation, fourniture..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Service concerné</label>
                        <input type="text" name="service_concerne" class="form-control" value="<?php echo e(old('service_concerne')); ?>" placeholder="Atelier, RH, Chantier...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Référence</label>
                        <input type="text" name="reference" class="form-control" value="<?php echo e(old('reference')); ?>" placeholder="Laisser vide pour auto-génération">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date d'achat *</label>
                        <input type="date" name="date_commande" class="form-control" value="<?php echo e(old('date_commande', now()->format('Y-m-d'))); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date livraison prévue</label>
                        <input type="date" name="date_livraison_prevue" class="form-control" value="<?php echo e(old('date_livraison_prevue')); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Compte comptable de charge *</label>
                        <select name="compte_comptable_id" class="form-select" required>
                            <option value="">Sélectionner</option>
                            <?php $__currentLoopData = $comptes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($compte->id); ?>" <?php if(old('compte_comptable_id') == $compte->id): echo 'selected'; endif; ?>>
                                    <?php echo e($compte->numero ?? $compte->numero_compte ?? $compte->code ?? $compte->id); ?> - <?php echo e($compte->intitule ?? $compte->libelle ?? 'Compte comptable'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mode de paiement</label>
                        <select name="mode_paiement" class="form-select">
                            <option value="">Sélectionner</option>
                            <option value="virement" <?php if(old('mode_paiement') === 'virement'): echo 'selected'; endif; ?>>Virement</option>
                            <option value="cheque" <?php if(old('mode_paiement') === 'cheque'): echo 'selected'; endif; ?>>Chèque</option>
                            <option value="espece" <?php if(old('mode_paiement') === 'espece'): echo 'selected'; endif; ?>>Espèces</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Conditions paiement</label>
                        <input type="text" name="conditions_paiement" class="form-control" value="<?php echo e(old('conditions_paiement')); ?>" placeholder="Ex: 30 jours">
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Lignes d'achat</h5>
                    <button type="button" id="add-ligne" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-1"></i>Ajouter</button>
                </div>

                <div id="lignes-container"></div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Frais de livraison</label>
                        <input type="number" step="0.01" min="0" name="frais_livraison" class="form-control" value="<?php echo e(old('frais_livraison', 0)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Remise</label>
                        <input type="number" step="0.01" min="0" name="remise" class="form-control" value="<?php echo e(old('remise', 0)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type de remise</label>
                        <select name="type_remise" class="form-select">
                            <option value="pourcentage" <?php if(old('type_remise', 'pourcentage') === 'pourcentage'): echo 'selected'; endif; ?>>Pourcentage</option>
                            <option value="montant" <?php if(old('type_remise') === 'montant'): echo 'selected'; endif; ?>>Montant fixe</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes')); ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?php echo e(route('achat.index')); ?>" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer l'achat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="ligne-template">
    <div class="border rounded p-3 mb-3 ligne-achat">
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">Article</label>
                <select name="lignes[__INDEX__][article_id]" class="form-select article-select">
                    <option value="">Sélectionner</option>
                    <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($article->id); ?>" data-designation="<?php echo e($article->designation); ?>" data-prix="<?php echo e($article->prix_unitaire ?? 0); ?>" data-unite="<?php echo e($article->unite ?? 'Unité'); ?>"><?php echo e($article->designation); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Désignation *</label>
                <input type="text" name="lignes[__INDEX__][designation]" class="form-control designation" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantité *</label>
                <input type="number" step="0.001" min="0.001" name="lignes[__INDEX__][quantite]" class="form-control" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unité *</label>
                <input type="text" name="lignes[__INDEX__][unite]" class="form-control unite" value="Unité" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger remove-ligne"><i class="fas fa-trash"></i></button>
            </div>
            <div class="col-md-3">
                <label class="form-label">Prix unitaire HT *</label>
                <input type="number" step="0.01" min="0" name="lignes[__INDEX__][prix_unitaire_ht]" class="form-control prix-unitaire" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">TVA (%) *</label>
                <input type="number" step="0.01" min="0" max="100" name="lignes[__INDEX__][tva_taux]" class="form-control" value="18" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Remise (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="lignes[__INDEX__][remise]" class="form-control" value="0">
            </div>
            <div class="col-md-5">
                <label class="form-label">Description</label>
                <input type="text" name="lignes[__INDEX__][description]" class="form-control">
            </div>
        </div>
    </div>
</template>

<?php $__env->startPush('scripts'); ?>
<script>
let achatLineIndex = 0;

function addAchatLine() {
    const template = document.getElementById('ligne-template').innerHTML.replaceAll('__INDEX__', achatLineIndex++);
    document.getElementById('lignes-container').insertAdjacentHTML('beforeend', template);
}

document.addEventListener('DOMContentLoaded', function () {
    addAchatLine();

    document.getElementById('add-ligne').addEventListener('click', addAchatLine);

    document.addEventListener('click', function (event) {
        if (event.target.closest('.remove-ligne')) {
            event.target.closest('.ligne-achat').remove();
        }
    });

    document.addEventListener('change', function (event) {
        if (!event.target.classList.contains('article-select')) {
            return;
        }

        const option = event.target.options[event.target.selectedIndex];
        const line = event.target.closest('.ligne-achat');
        line.querySelector('.designation').value = option.dataset.designation || '';
        line.querySelector('.prix-unitaire').value = option.dataset.prix || '';
        line.querySelector('.unite').value = option.dataset.unite || 'Unité';
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/achat/create.blade.php ENDPATH**/ ?>