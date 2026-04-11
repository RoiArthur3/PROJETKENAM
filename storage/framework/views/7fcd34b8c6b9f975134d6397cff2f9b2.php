<?php $__env->startSection('title', 'Gestion de la Paie - RH'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalccad3158532bfedaf3c3145fd14bfba0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalccad3158532bfedaf3c3145fd14bfba0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.list-layout','data' => ['title' => 'Gestion de la Paie','icon' => 'fa-money-bill-wave','exportRoute' => 'rh.paie.export']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('list-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Gestion de la Paie','icon' => 'fa-money-bill-wave','exportRoute' => 'rh.paie.export']); ?>

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
<?php $component->withAttributes(['title' => 'Total Brut','value' => ''.e(number_format($totalBrut, 0, ',', ' ')).' FCFA','icon' => 'fa-euro-sign','color' => 'primary']); ?>
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
<?php $component->withAttributes(['title' => 'Total Charges','value' => ''.e(number_format($totalCharges, 0, ',', ' ')).' FCFA','icon' => 'fa-minus-circle','color' => 'warning']); ?>
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
<?php $component->withAttributes(['title' => 'Total Net','value' => ''.e(number_format($totalNet, 0, ',', ' ')).' FCFA','icon' => 'fa-money-check','color' => 'success']); ?>
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
<?php $component->withAttributes(['title' => 'Bulletins Payés','value' => ''.e($bulletinsPayes).'/'.e($bulletins).'','icon' => 'fa-check-circle','color' => 'info']); ?>
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

     <?php $__env->slot('filters', null, []); ?> 
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Mois</label>
                <input type="month" class="form-control" id="monthFilter" value="<?php echo e($period->format('Y-m')); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Statut</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="genere">Généré</option>
                    <option value="valide">Validé</option>
                    <option value="paye">Payé</option>
                    <option value="refuse">Refusé</option>
                </select>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <!-- Tableau des paies -->
    <thead class="table-light">
        <tr>
            <th>Agent</th>
            <th>Période</th>
            <th>Salaire de base</th>
            <th>Total Brut</th>
            <th>CNPS</th>
            <th>Autres retenues</th>
            <th>Net à payer</th>
            <th>Statut</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $salaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="fw-semibold"><?php echo e($paie->user->name ?? 'N/A'); ?></div>
                            <small class="text-muted"><?php echo e($paie->user->email ?? ''); ?></small>
                        </div>
                    </div>
                </td>
                <td>
                    <?php echo e($period->format('m')); ?>/<?php echo e($period->format('Y')); ?>

                </td>
                <td><?php echo e(number_format($paie->salaire_brut ?? 0, 0, ',', ' ')); ?> FCFA</td>
                <td class="fw-semibold"><?php echo e(number_format($paie->salaire_brut ?? 0, 0, ',', ' ')); ?> FCFA</td>
                <td><?php echo e(number_format($paie->cnps_salariale ?? 0, 0, ',', ' ')); ?> FCFA</td>
                <td><?php echo e(number_format($paie->autres_retenues ?? 0, 0, ',', ' ')); ?> FCFA</td>
                <td class="fw-bold text-success"><?php echo e(number_format($paie->net_a_payer ?? 0, 0, ',', ' ')); ?> FCFA</td>
                <td>
                    <?php
                        $statusColors = [
                            'genere' => 'secondary',
                            'valide' => 'info',
                            'paye' => 'success',
                            'refuse' => 'danger',
                        ];
                        $statusLabels = [
                            'genere' => 'Généré',
                            'valide' => 'Validé',
                            'paye' => 'Payé',
                            'refuse' => 'Refusé',
                        ];
                    ?>
                    <span class="badge bg-<?php echo e($statusColors[$paie->statut] ?? 'secondary'); ?>">
                        <?php echo e($statusLabels[$paie->statut] ?? $paie->statut); ?>

                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-info"
                                onclick="viewPaie(<?php echo e($paie->user->id ?? 'null'); ?>)"
                                title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning"
                                onclick="window.location.href='<?php echo e(route('rh.paie.edit', $paie->user->id ?? 'null')); ?>'"
                                title="Modifier manuellement">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php if($paie->statut === 'genere'): ?>
                            <button type="button" class="btn btn-outline-success"
                                    onclick="validatePaie(<?php echo e($paie->user->id ?? 'null'); ?>)"
                                    title="Valider">
                                <i class="fas fa-check"></i>
                            </button>
                        <?php elseif($paie->statut === 'valide'): ?>
                            <button type="button" class="btn btn-outline-primary"
                                    onclick="markAsPaid(<?php echo e($paie->user->id ?? 'null'); ?>)"
                                    title="Marquer comme payé">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('rh.paie.pdf', $paie->user->id ?? 'null')); ?>" class="btn btn-outline-secondary"
                           title="Télécharger PDF" target="_blank">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                    <div class="text-muted">Aucune fiche de paie trouvée pour ce mois</div>
                    <button type="button" class="btn btn-primary mt-2"
                            onclick="generateAllPaie()">
                        <i class="fas fa-plus"></i> Générer les paies
                    </button>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalccad3158532bfedaf3c3145fd14bfba0)): ?>
<?php $attributes = $__attributesOriginalccad3158532bfedaf3c3145fd14bfba0; ?>
<?php unset($__attributesOriginalccad3158532bfedaf3c3145fd14bfba0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalccad3158532bfedaf3c3145fd14bfba0)): ?>
<?php $component = $__componentOriginalccad3158532bfedaf3c3145fd14bfba0; ?>
<?php unset($__componentOriginalccad3158532bfedaf3c3145fd14bfba0); ?>
<?php endif; ?>

<script>
function filterByMonth() {
    const month = document.getElementById('monthFilter').value;
    if (month) {
        window.location.href = `?mois=${month}`;
    }
}

function filterByStatus() {
    const status = document.getElementById('statusFilter').value;
    // Implement status filtering
    console.log('Filter by status:', status);
}

function viewPaie(id) {
    // Redirect to paie show page
    window.location.href = `/rh/paie/${id}`;
}

function validatePaie(id) {
    if (confirm('Êtes-vous sûr de vouloir valider cette fiche de paie ?')) {
        // Implement validation
        console.log('Validate paie:', id);
    }
}

function markAsPaid(id) {
    if (confirm('Êtes-vous sûr de vouloir marquer cette paie comme payée ?')) {
        // Implement payment marking
        console.log('Mark as paid:', id);
    }
}

function generateAllPaie() {
    const month = document.getElementById('monthFilter').value;
    if (confirm('Générer les fiches de paie pour tous les agents du mois sélectionné ?')) {
        window.location.href = `/rh/paie/generate?mois=${month}`;
    }
}

// Event listeners
document.getElementById('monthFilter')?.addEventListener('change', filterByMonth);
document.getElementById('statusFilter')?.addEventListener('change', filterByStatus);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/rh/paie/index.blade.php ENDPATH**/ ?>