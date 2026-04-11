

<?php $__env->startSection('title', 'Dashboard RH | KENAM SERVICES'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2"><i class="fas fa-users me-2"></i>Tableau de Bord RH</h2>
                    <p class="text-muted mb-0">Suivi des indicateurs Ressources Humaines uniquement - <?php echo e(now()->format('d/m/Y')); ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(url('/rh/personnel')); ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list me-1"></i>Liste du personnel
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Personnel total</div>
                    <div class="h3 mb-0 text-primary"><?php echo e($rhStats['total'] ?? 0); ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Actifs</div>
                    <div class="h3 mb-0 text-success"><?php echo e($rhStats['actifs'] ?? 0); ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En période d'essai</div>
                    <div class="h3 mb-0 text-warning"><?php echo e($rhStats['en_essai'] ?? 0); ?></div>
                    <small class="text-muted">Fin d'essai proche: <?php echo e($rhStats['fin_essai_expiring'] ?? 0); ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-info h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Salaire moyen</div>
                    <div class="h3 mb-0 text-info"><?php echo e(number_format($rhStats['salaire_moyen'] ?? 0, 0)); ?></div>
                    <small class="text-muted">FCFA / mois</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-danger h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Inactifs</div>
                    <div class="h3 mb-0 text-danger"><?php echo e($rhStats['inactifs'] ?? 0); ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-secondary h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Résiliés</div>
                    <div class="h3 mb-0 text-secondary"><?php echo e($rhStats['realties'] ?? 0); ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-dark h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Contrats CDI</div>
                    <div class="h3 mb-0 text-dark"><?php echo e($rhStats['contrats_cdi'] ?? 0); ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-dark h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Contrats CDD</div>
                    <div class="h3 mb-0 text-dark"><?php echo e($rhStats['contrats_cdd'] ?? 0); ?></div>
                    <small class="text-muted">Expirant sous 30 jours: <?php echo e($rhStats['contrats_expiring'] ?? 0); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/rh/personnel/dashboard-rh.blade.php ENDPATH**/ ?>