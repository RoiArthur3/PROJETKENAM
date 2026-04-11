<?php $__env->startSection('title', 'Nouveau document juridique'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-file-plus me-2"></i>Nouveau document juridique
        </h1>
        <a href="<?php echo e(route('juridique.documents.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('juridique.documents.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    <!-- Référence -->
                    <div class="col-md-4">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" id="reference" name="reference" class="form-control"
                               value="<?php echo e(old('reference')); ?>" placeholder="Ex: DOC-2026-001">
                        <?php $__errorArgs = ['reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Titre -->
                    <div class="col-md-8">
                        <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" id="titre" name="titre" class="form-control"
                               value="<?php echo e(old('titre')); ?>" required placeholder="Titre du document">
                        <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Type de document -->
                    <div class="col-md-4">
                        <label for="type_document" class="form-label">Type de document</label>
                        <select id="type_document" name="type_document" class="form-select">
                            <option value="">Sélectionner un type</option>
                            <option value="contrat" <?php echo e(old('type_document') == 'contrat' ? 'selected' : ''); ?>>Contrat</option>
                            <option value="convention" <?php echo e(old('type_document') == 'convention' ? 'selected' : ''); ?>>Convention</option>
                            <option value="facture" <?php echo e(old('type_document') == 'facture' ? 'selected' : ''); ?>>Facture</option>
                            <option value="devis" <?php echo e(old('type_document') == 'devis' ? 'selected' : ''); ?>>Devis</option>
                            <option value="acte" <?php echo e(old('type_document') == 'acte' ? 'selected' : ''); ?>>Acte</option>
                            <option value="courrier" <?php echo e(old('type_document') == 'courrier' ? 'selected' : ''); ?>>Courrier</option>
                            <option value="autre" <?php echo e(old('type_document') == 'autre' ? 'selected' : ''); ?>>Autre</option>
                        </select>
                        <?php $__errorArgs = ['type_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Contrat lié -->
                    <div class="col-md-8">
                        <label for="contrat_id" class="form-label">Contrat lié</label>
                        <select id="contrat_id" name="contrat_id" class="form-select">
                            <option value="">Aucun contrat</option>
                            <?php $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($contrat->id); ?>" <?php echo e(old('contrat_id') == $contrat->id ? 'selected' : ''); ?>>
                                    <?php echo e($contrat->titre); ?> (<?php echo e($contrat->reference); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['contrat_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Date du document -->
                    <div class="col-md-4">
                        <label for="date_document" class="form-label">Date du document</label>
                        <input type="date" id="date_document" name="date_document" class="form-control"
                               value="<?php echo e(old('date_document')); ?>">
                        <?php $__errorArgs = ['date_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Date d'expiration -->
                    <div class="col-md-4">
                        <label for="date_expiration" class="form-label">Date d'expiration</label>
                        <input type="date" id="date_expiration" name="date_expiration" class="form-control"
                               value="<?php echo e(old('date_expiration')); ?>">
                        <?php $__errorArgs = ['date_expiration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Statut -->
                    <div class="col-md-4">
                        <label for="statut" class="form-label">Statut</label>
                        <select id="statut" name="statut" class="form-select">
                            <option value="actif" <?php echo e(old('statut', 'actif') == 'actif' ? 'selected' : ''); ?>>Actif</option>
                            <option value="inactif" <?php echo e(old('statut') == 'inactif' ? 'selected' : ''); ?>>Inactif</option>
                            <option value="archive" <?php echo e(old('statut') == 'archive' ? 'selected' : ''); ?>>Archivé</option>
                            <option value="expire" <?php echo e(old('statut') == 'expire' ? 'selected' : ''); ?>>Expiré</option>
                        </select>
                        <?php $__errorArgs = ['statut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Fichier -->
                    <div class="col-12">
                        <label for="fichier" class="form-label">Fichier du document</label>
                        <input type="file" id="fichier" name="fichier" class="form-control"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.txt,.jpg,.jpeg,.png,.webp,.zip,.rar">
                        <small class="text-muted">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, CSV, TXT, JPG, PNG, ZIP, RAR (Max: 10MB)</small>
                        <?php $__errorArgs = ['fichier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="4"
                                  placeholder="Notes ou observations sur ce document..."><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="<?php echo e(route('juridique.documents.index')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer le document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-body {
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    padding: 0.5rem 1.5rem;
}

.text-danger {
    font-size: 0.875rem;
}

.text-muted {
    font-size: 0.75rem;
}
</style>

<script>
// Auto-génération de référence
document.addEventListener('DOMContentLoaded', function() {
    const referenceInput = document.getElementById('reference');
    const titreInput = document.getElementById('titre');

    // Si la référence est vide, en générer une automatiquement
    if (!referenceInput.value) {
        const today = new Date();
        const year = today.getFullYear();
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        referenceInput.value = `DOC-${year}-${random}`;
    }

    // Optionnel: générer une référence basée sur le titre
    titreInput.addEventListener('blur', function() {
        if (!referenceInput.value || referenceInput.value.startsWith('DOC-')) {
            const titre = this.value.trim();
            if (titre) {
                const today = new Date();
                const year = today.getFullYear();
                const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                referenceInput.value = `DOC-${year}-${random}`;
            }
        }
    });

    // Validation de la date d'expiration
    const dateDocument = document.getElementById('date_document');
    const dateExpiration = document.getElementById('date_expiration');

    dateExpiration.addEventListener('change', function() {
        if (dateDocument.value && this.value) {
            if (new Date(this.value) < new Date(dateDocument.value)) {
                alert('La date d\'expiration ne peut pas être antérieure à la date du document.');
                this.value = '';
            }
        }
    });

    // Animation des champs
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach((input, index) => {
        input.style.opacity = '0';
        input.style.transform = 'translateY(10px)';
        setTimeout(() => {
            input.style.transition = 'all 0.3s ease';
            input.style.opacity = '1';
            input.style.transform = 'translateY(0)';
        }, index * 50);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/juridique/documents/create.blade.php ENDPATH**/ ?>