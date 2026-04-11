<?php $__env->startSection('title', 'Dashboard Tresorerie - KENAM SERVICES'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginal0de143e5b61900e6d7b990ac144ae3fb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0de143e5b61900e6d7b990ac144ae3fb = $attributes; } ?>
<?php $component = App\View\Components\DashboardLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\DashboardLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard Tresorerie','icon' => 'fa-sack-dollar']); ?>

     <?php $__env->slot('headerActions', null, []); ?> 
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo e(route('tresorerie.caisses')); ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-cash-register me-1"></i>Caisses
            </a>
            <a href="<?php echo e(route('tresorerie.approvisionnements')); ?>" class="btn btn-outline-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i>Approvisionnements
            </a>
            <a href="<?php echo e(route('tresorerie.decaissements')); ?>" class="btn btn-outline-warning btn-sm">
                <i class="fas fa-money-bill-wave me-1"></i>Decaissements
            </a>
            <a href="<?php echo e(route('tresorerie.virements')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-exchange-alt me-1"></i>Virements
            </a>
            <a href="<?php echo e(route('tresorerie.flux')); ?>" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-stream me-1"></i>Flux
            </a>
            <a href="<?php echo e(route('tresorerie.bon-pour-accord')); ?>" class="btn btn-outline-success btn-sm">
                <i class="fas fa-check-double me-1"></i>BON POUR ACCORD
            </a>
        </div>
     <?php $__env->endSlot(); ?>

     <?php $__env->slot('kpis', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginal6d6e6b9172afb4a6de0e07680b309c58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58 = $attributes; } ?>
<?php $component = App\View\Components\KpiCard::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\KpiCard::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Caisses','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['total_caisses']),'icon' => 'fa-cash-register','color' => 'primary','subtitle' => 'Caisses actives']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58)): ?>
<?php $attributes = $__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58; ?>
<?php unset($__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d6e6b9172afb4a6de0e07680b309c58)): ?>
<?php $component = $__componentOriginal6d6e6b9172afb4a6de0e07680b309c58; ?>
<?php unset($__componentOriginal6d6e6b9172afb4a6de0e07680b309c58); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal6d6e6b9172afb4a6de0e07680b309c58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58 = $attributes; } ?>
<?php $component = App\View\Components\KpiCard::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\KpiCard::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Solde Total','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($stats['solde_total'], 0, ',', ' ') . ' FCFA'),'icon' => 'fa-wallet','color' => 'success','subtitle' => 'Disponibilite globale']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58)): ?>
<?php $attributes = $__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58; ?>
<?php unset($__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d6e6b9172afb4a6de0e07680b309c58)): ?>
<?php $component = $__componentOriginal6d6e6b9172afb4a6de0e07680b309c58; ?>
<?php unset($__componentOriginal6d6e6b9172afb4a6de0e07680b309c58); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal6d6e6b9172afb4a6de0e07680b309c58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58 = $attributes; } ?>
<?php $component = App\View\Components\KpiCard::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\KpiCard::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Decaissements','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format(abs($stats['total_decaissements']), 0, ',', ' ') . ' FCFA'),'icon' => 'fa-arrow-up-right-from-square','color' => 'warning','subtitle' => 'Sorties enregistrees']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58)): ?>
<?php $attributes = $__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58; ?>
<?php unset($__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d6e6b9172afb4a6de0e07680b309c58)): ?>
<?php $component = $__componentOriginal6d6e6b9172afb4a6de0e07680b309c58; ?>
<?php unset($__componentOriginal6d6e6b9172afb4a6de0e07680b309c58); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal6d6e6b9172afb4a6de0e07680b309c58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58 = $attributes; } ?>
<?php $component = App\View\Components\KpiCard::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\KpiCard::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'En Attente','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['en_attente']),'icon' => 'fa-clock','color' => 'info','subtitle' => 'Approvisionnements a traiter']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58)): ?>
<?php $attributes = $__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58; ?>
<?php unset($__attributesOriginal6d6e6b9172afb4a6de0e07680b309c58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d6e6b9172afb4a6de0e07680b309c58)): ?>
<?php $component = $__componentOriginal6d6e6b9172afb4a6de0e07680b309c58; ?>
<?php unset($__componentOriginal6d6e6b9172afb4a6de0e07680b309c58); ?>
<?php endif; ?>
     <?php $__env->endSlot(); ?>

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-tachometer-alt me-2"></i>Indicateurs de Performance Tresorerie
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-primary"><?php echo e($stats['total_approvisionnements']); ?></div>
                                <div class="metric-label">Approvisionnements</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-success"><?php echo e($stats['valides']); ?></div>
                                <div class="metric-label">Approvisionnements valides</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-info"><?php echo e($stats['total_virements']); ?></div>
                                <div class="metric-label">Virements</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-warning"><?php echo e(number_format($depensesMensuelles, 0, ',', ' ')); ?></div>
                                <div class="metric-label">Depenses du mois (FCFA)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Mouvements Recents (5 derniers)
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="mouvementsTresorerieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-chart-pie me-2"></i>Repartition des Caisses
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="caissesRepartitionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-warning">
                        <i class="fas fa-money-bill-wave me-2"></i>Decaissements Recents
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Libelle</th>
                                    <th class="text-end">Montant</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $decaissements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $decaissement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><span class="badge bg-warning text-dark"><?php echo e($decaissement->reference); ?></span></td>
                                        <td><?php echo e($decaissement->libelle); ?></td>
                                        <td class="text-end fw-bold text-danger">-<?php echo e(number_format((float) $decaissement->montant, 0, ',', ' ')); ?></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($decaissement->date_depense)->format('d/m/Y')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun decaissement recent</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="fas fa-plus-circle me-2"></i>Approvisionnements Recents
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Caisse</th>
                                    <th class="text-end">Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $approvisionnements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approvisionnement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?php echo e($approvisionnement->reference); ?></span></td>
                                        <td><?php echo e($approvisionnement->destination->nom ?? ($approvisionnement->destination->libelle ?? 'N/A')); ?></td>
                                        <td class="text-end fw-bold text-success">+<?php echo e(number_format((float) $approvisionnement->montant, 0, ',', ' ')); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e(strtolower($approvisionnement->statut) === 'valide' || strtolower($approvisionnement->statut) === 'validé' ? 'success' : 'warning'); ?>">
                                                <?php echo e($approvisionnement->statut); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun approvisionnement recent</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0de143e5b61900e6d7b990ac144ae3fb)): ?>
<?php $attributes = $__attributesOriginal0de143e5b61900e6d7b990ac144ae3fb; ?>
<?php unset($__attributesOriginal0de143e5b61900e6d7b990ac144ae3fb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0de143e5b61900e6d7b990ac144ae3fb)): ?>
<?php $component = $__componentOriginal0de143e5b61900e6d7b990ac144ae3fb; ?>
<?php unset($__componentOriginal0de143e5b61900e6d7b990ac144ae3fb); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.metric-card {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.metric-value {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 0.4rem;
}

.metric-label {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mouvementsCtx = document.getElementById('mouvementsTresorerieChart');
    if (mouvementsCtx) {
        const decaissementsLabels = <?php echo json_encode($decaissements->map(function ($item) {
            return \Carbon\Carbon::parse($item->date_depense)->format('d/m');
        })->values(), 15, 512) ?>;

        const decaissementsData = <?php echo json_encode($decaissements->pluck('montant')->values(), 15, 512) ?>;
        const approData = <?php echo json_encode($approvisionnements->pluck('montant')->values(), 15, 512) ?>;

        new Chart(mouvementsCtx, {
            type: 'bar',
            data: {
                labels: decaissementsLabels,
                datasets: [
                    {
                        label: 'Decaissements',
                        data: decaissementsData,
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Approvisionnements',
                        data: approData,
                        backgroundColor: 'rgba(13, 202, 240, 0.7)',
                        borderColor: 'rgba(13, 202, 240, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    const caissesCtx = document.getElementById('caissesRepartitionChart');
    if (caissesCtx) {
        new Chart(caissesCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($caisses->pluck('nom')->values(), 15, 512) ?>,
                datasets: [{
                    data: <?php echo json_encode($caisses->pluck('solde_actuel')->values(), 15, 512) ?>,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2', '#20c997']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/tresorerie/dashboard.blade.php ENDPATH**/ ?>