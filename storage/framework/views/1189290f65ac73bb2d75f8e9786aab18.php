<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'KPI',
    'value' => '0',
    'icon' => 'fa-chart-line',
    'color' => 'primary',
    'subtitle' => '',
    'trend' => null,
    'trendValue' => null
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title' => 'KPI',
    'value' => '0',
    'icon' => 'fa-chart-line',
    'color' => 'primary',
    'subtitle' => '',
    'trend' => null,
    'trendValue' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-start border-<?php echo e($color); ?> border-4 shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted small text-uppercase fw-bold mb-1"><?php echo e($title); ?></div>
                    <div class="h3 mb-0 text-<?php echo e($color); ?>"><?php echo e($value); ?></div>
                    <?php if($subtitle): ?>
                        <small class="text-muted"><?php echo e($subtitle); ?></small>
                    <?php endif; ?>
                    <?php if($trend && $trendValue): ?>
                        <small class="text-<?php echo e($trend === 'up' ? 'success' : ($trend === 'down' ? 'danger' : 'muted')); ?>">
                            <i class="fas fa-arrow-<?php echo e($trend === 'up' ? 'up' : 'down'); ?>"></i> <?php echo e($trendValue); ?>

                        </small>
                    <?php endif; ?>
                </div>
                <i class="fas <?php echo e($icon); ?> fa-3x text-<?php echo e($color); ?> opacity-25"></i>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\kenam\resources\views/components/kpi-card.blade.php ENDPATH**/ ?>