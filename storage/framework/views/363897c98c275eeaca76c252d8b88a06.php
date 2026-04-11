<?php $__env->startSection('title', 'Gestion des approvisionnements de caisse'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Historique des approvisionnements</h4>
                    <a href="<?php echo e(route('tresorerie.approvisionnements.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvel approvisionnement
                    </a>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="text-primary">
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Caisse</th>
                                    <th>Montant</th>
                                    <th>Demandeur</th>
                                    <th>Statut</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $approvisionnements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approvisionnement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($approvisionnement->created_at->format('d/m/Y H:i')); ?></td>
                                        <td><?php echo e($approvisionnement->numero_operation ?? $approvisionnement->reference ?? 'N/A'); ?></td>
                                        <td><?php echo e($approvisionnement->destination->nom ?? $approvisionnement->destination->libelle ?? 'N/A'); ?></td>
                                        <td class="text-success">
                                            <?php echo e(number_format($approvisionnement->montant, 2, ',', ' ')); ?> <?php echo e($approvisionnement->devise ?? 'EUR'); ?>

                                        </td>
                                        <td><?php echo e($approvisionnement->demandeur->name ?? 'N/A'); ?></td>
                                        <td>
                                            <?php
                                                $badgeClass = [
                                                    'en_attente' => 'warning',
                                                    'valide' => 'success',
                                                    'rejete' => 'danger'
                                                ][$approvisionnement->statut] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?php echo e($badgeClass); ?>">
                                                <?php echo e(ucfirst($approvisionnement->statut)); ?>

                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?php echo e(route('tresorerie.approvisionnements.show', $approvisionnement)); ?>"
                                               class="btn btn-info btn-sm"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($approvisionnement->statut === 'en_attente'): ?>
                                                <?php if(\Illuminate\Support\Facades\Route::has('tresorerie.approvisionnements.edit')): ?>
                                                    <a href="<?php echo e(route('tresorerie.approvisionnements.edit', $approvisionnement)); ?>"
                                                       class="btn btn-warning btn-sm"
                                                       title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if(\Illuminate\Support\Facades\Route::has('tresorerie.approvisionnements.destroy')): ?>
                                                    <form action="<?php echo e(route('tresorerie.approvisionnements.destroy', $approvisionnement)); ?>"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet approvisionnement ?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Aucun approvisionnement trouvé.
                                            <a href="<?php echo e(route('tresorerie.approvisionnements.create')); ?>" class="btn btn-link">
                                                Créer un nouvel approvisionnement
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($approvisionnements->hasPages()): ?>
                        <div class="mt-4">
                            <?php echo e($approvisionnements->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/tresorerie/approvisionnements/index.blade.php ENDPATH**/ ?>