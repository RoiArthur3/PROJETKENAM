<?php $__env->startSection('title', 'Documents juridiques'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-file-contract me-2"></i>Documents juridiques
        </h1>
        <a href="<?php echo e(route('juridique.documents.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau document
        </a>
    </div>

    <!-- Tableau des documents -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Liste des documents
            </h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Contrat</th>
                        <th>Fichier</th>
                        <th>Expiration</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark"><?php echo e($document->reference ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <strong><?php echo e($document->titre); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo e($document->type_document ?? 'Non défini'); ?></span>
                            </td>
                            <td>
                                <?php if($document->contrat): ?>
                                    <a href="<?php echo e(route('juridique.contrats.show', $document->contrat)); ?>" class="text-decoration-none">
                                        <?php echo e($document->contrat->titre); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($document->chemin_fichier): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Disponible
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times me-1"></i>Aucun
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($document->date_expiration): ?>
                                    <span class="text-<?php echo e(optional($document->date_expiration)->isPast() ? 'danger' : 'success'); ?>">
                                        <?php echo e(optional($document->date_expiration)->format('d/m/Y')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($document->statut == 'actif' ? 'success' : 'secondary'); ?>">
                                    <?php echo e($document->statut ?? 'Inconnu'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('juridique.documents.show', $document)); ?>" class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('juridique.documents.edit', $document)); ?>" class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('juridique.documents.destroy', $document)); ?>" method="POST" style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-3">Aucun document juridique trouvé</p>
                                    <a href="<?php echo e(route('juridique.documents.create')); ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Créer le premier document
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if($documents->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($documents->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.badge {
    font-size: 0.75rem;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    border-top: none;
    background-color: #f8f9fa;
}

.table td {
    font-size: 0.875rem;
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.text-decoration-none:hover {
    text-decoration: underline !important;
}
</style>

<script>
// Confirmation de suppression
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce document ? Cette action est irréversible.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 50);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/juridique/documents/index.blade.php ENDPATH**/ ?>