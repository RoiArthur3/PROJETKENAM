<?php $__env->startSection('title', 'Dashboard Juridique - KENAM SERVICES'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Module Juridique</h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo e(route('juridique.documents.index')); ?>" class="btn btn-outline-secondary">Documents</a>
            <a href="<?php echo e(route('juridique.financements.index')); ?>" class="btn btn-outline-success">Financements</a>
            <a href="<?php echo e(route('juridique.offres.index')); ?>" class="btn btn-outline-info">Offres bancaires</a>
            <a href="<?php echo e(route('juridique.echeances.index')); ?>" class="btn btn-primary">Échéanciers</a>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Contrats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['contrats']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Documents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['documents']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dossiers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['dossiers']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Offres</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['offres']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-university fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes financières -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Montant demandé</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($stats['montant_demande'], 0, ',', ' ')); ?> FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Montant obtenu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($stats['montant_obtenu'], 0, ',', ' ')); ?> FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taux d'acceptation</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($tauxAcceptation); ?>%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux récents -->
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>Contrats récents</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Titre</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $contratsRecents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($contrat->reference ?? '-'); ?></td>
                                    <td><?php echo e($contrat->titre ?? '-'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo e($contrat->statut == 'actif' ? 'success' : 'secondary'); ?>">
                                            <?php echo e($contrat->statut ?? '-'); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun contrat récent</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <strong>Échéances proches</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Banque</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $echeancesProches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $echeance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e(optional($echeance->date_echeance)->format('d/m/Y') ?? '-'); ?></td>
                                    <td><?php echo e(optional($echeance->offre)->banque ?? '-'); ?></td>
                                    <td><?php echo e(number_format($echeance->mensualite ?? 0, 0, ',', ' ')); ?> FCFA</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucune échéance proche</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dossiers récents -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <strong>Dossiers de financement récents</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Intitulé</th>
                                <th>Type</th>
                                <th>Montant demandé</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $dossiersRecents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dossier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($dossier->reference ?? '-'); ?></td>
                                    <td><?php echo e($dossier->intitule ?? '-'); ?></td>
                                    <td><?php echo e($dossier->type_financement ?? '-'); ?></td>
                                    <td><?php echo e(number_format($dossier->montant_demande ?? 0, 0, ',', ' ')); ?> FCFA</td>
                                    <td>
                                        <span class="badge badge-<?php echo e($dossier->statut == 'approuve' ? 'success' : ($dossier->statut == 'rejete' ? 'danger' : 'warning')); ?>">
                                            <?php echo e($dossier->statut ?? '-'); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aucun dossier récent</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/juridique/dashboard.blade.php ENDPATH**/ ?>