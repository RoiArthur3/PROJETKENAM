<?php $__env->startSection('title', 'Dossiers de financement'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-hand-holding-usd me-2"></i>Dossiers de financement
        </h1>
        <a href="<?php echo e(route('juridique.financements.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau dossier
        </a>
    </div>

    <!-- Tableau des financements -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Liste des dossiers de financement
            </h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Intitulé</th>
                        <th>Type</th>
                        <th>Organisme</th>
                        <th>Montant</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $dossiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dossier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark"><?php echo e($dossier->reference ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <strong><?php echo e($dossier->titre); ?></strong>
                                <?php if($dossier->contrat): ?>
                                    <br><small class="text-muted">Contrat: <?php echo e($dossier->contrat->reference); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo e($dossier->type_financement_formatted ?? 'Non défini'); ?></span>
                            </td>
                            <td>
                                <?php echo e($dossier->organisme_preteur ?? 'Non spécifié'); ?>

                            </td>
                            <td>
                                <span class="text-success fw-bold"><?php echo e($dossier->montant_emprunte_formatted ?? '0 FCFA'); ?></span>
                                <?php if($dossier->taux_interet): ?>
                                    <br><small class="text-muted"><?php echo e($dossier->taux_interet_formatted ?? '0%'); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo e($dossier->duree_formatted ?? 'Non défini'); ?>

                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($dossier->statut == 'en_cours' ? 'success' : ($dossier->statut == 'termine' ? 'secondary' : 'warning')); ?>">
                                    <?php echo e($dossier->statut_formatted ?? 'Inconnu'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('juridique.financements.show', $dossier)); ?>" class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('juridique.financements.edit', $dossier)); ?>" class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('juridique.financements.destroy', $dossier)); ?>" method="POST" style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier de financement ?')">
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
                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                    <p class="mb-3">Aucun dossier de financement trouvé</p>
                                    <a href="<?php echo e(route('juridique.financements.create')); ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Créer le premier dossier
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if($dossiers->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($dossiers->links()); ?>

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
</style>

<script>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/juridique/financements/index.blade.php ENDPATH**/ ?>