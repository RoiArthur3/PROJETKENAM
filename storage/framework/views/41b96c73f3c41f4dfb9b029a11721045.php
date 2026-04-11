<?php $__env->startSection('title', 'Dashboard Comptabilité - KENAM SERVICES'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calculator me-2 text-primary"></i>Dashboard Comptabilité
            </h1>
            <p class="text-muted mb-0">Gestion financière et rapports</p>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Écritures</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_ecritures'] ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Factures</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_factures'] ?? 0); ?></div>
                            <div class="text-muted small">Documents émis</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Paiements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_paiements'] ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Solde Trésorerie</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($stats['solde_tresorerie'] ?? 0, 0, ',', ' ')); ?> FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Secondaires -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Nombre de Factures Impayées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['factures_impayees'] ?? 0); ?></div>
                            <div class="text-muted small"><?php echo e(number_format($stats['montant_impaye'] ?? 0, 0, ',', ' ')); ?> FCFA</div>
                            <div class="text-muted small">Retard: <?php echo e($stats['factures_en_retard'] ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Nombre de Recettes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_recettes'] ?? 0); ?></div>
                            <div class="text-muted small"><?php echo e(number_format($stats['montant_recettes'] ?? 0, 0, ',', ' ')); ?> FCFA</div>
                            <div class="text-muted small">Opérations enregistrées</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Nombre de Dépenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_depenses'] ?? 0); ?></div>
                            <div class="text-muted small"><?php echo e(number_format($stats['montant_depenses'] ?? 0, 0, ',', ' ')); ?> FCFA</div>
                            <div class="text-muted small">Dépenses enregistrées</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Solde Caisses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($stats['solde_caisses'] ?? 0, 0, ',', ' ')); ?></div>
                            <div class="text-muted small">FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Écritures récentes et Factures en attente -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Écritures Récentes
                    </h6>
                </div>
                <div class="card-body">
                    <?php if(isset($recentEcritures) && $recentEcritures->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Libellé</th>
                                        <th>Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentEcritures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ecriture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(\Carbon\Carbon::parse($ecriture->date_ecriture ?? now())->format('d/m/Y')); ?></td>
                                        <td><?php echo e($ecriture->libelle ?? 'N/A'); ?></td>
                                        <td><?php echo e(number_format($ecriture->montant ?? 0, 0, ',', ' ')); ?> FCFA</td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Aucune écriture récente</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Factures en Attente
                    </h6>
                </div>
                <div class="card-body">
                    <?php if(isset($pendingInvoices) && $pendingInvoices->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>N° Facture</th>
                                        <th>Client</th>
                                        <th>Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $pendingInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($invoice->reference ?? 'N/A'); ?></td>
                                        <td><?php echo e($invoice->client_nom ?? 'N/A'); ?></td>
                                        <td><?php echo e(number_format($invoice->montant ?? 0, 0, ',', ' ')); ?> FCFA</td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Aucune facture en attente</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/comptabilite/dashboard.blade.php ENDPATH**/ ?>