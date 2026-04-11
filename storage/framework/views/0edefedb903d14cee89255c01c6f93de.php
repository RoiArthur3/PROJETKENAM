<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => 'Dashboard', 'icon' => 'fa-tachometer-alt']));

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

foreach (array_filter((['title' => 'Dashboard', 'icon' => 'fa-tachometer-alt']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<!-- Contenu qui s'intègre dans la container-fluid du layout principal -->
<div class="dashboard-wrapper">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas <?php echo e($icon); ?> me-2 text-primary"></i><?php echo e($title); ?>

            </h1>
            <p class="text-muted mb-0"><?php echo e(now()->locale('fr')->isoFormat('dddd D MMMM YYYY')); ?></p>
        </div>
        <?php if(isset($headerActions)): ?>
            <div>
                <?php echo e($headerActions); ?>

            </div>
        <?php else: ?>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Rafraîchir
            </button>
        <?php endif; ?>
    </div>

    <!-- KPIs Section (si fourni) -->
    <?php if(isset($kpis)): ?>
        <div class="row mb-4">
            <?php echo e($kpis); ?>

        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="dashboard-content">
        <?php echo e($slot); ?>

    </div>
</div>

<style>
.dashboard-wrapper {
    width: 100%;
    /* Permettre au contenu de s'adapter à la container-fluid parente */
}

/* Assurer que le contenu ne dépasse pas la container-fluid parente */
.dashboard-content {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
}

.dashboard-content .row {
    margin-left: -15px;
    margin-right: -15px;
}

.dashboard-content .col,
.dashboard-content .col-1,
.dashboard-content .col-2,
.dashboard-content .col-3,
.dashboard-content .col-4,
.dashboard-content .col-5,
.dashboard-content .col-6,
.dashboard-content .col-7,
.dashboard-content .col-8,
.dashboard-content .col-9,
.dashboard-content .col-10,
.dashboard-content .col-11,
.dashboard-content .col-12,
.dashboard-content .col-lg-8,
.dashboard-content .col-lg-6,
.dashboard-content .col-lg-4,
.dashboard-content .col-lg-12,
.dashboard-content .col-md-3,
.dashboard-content .col-md-6 {
    padding-left: 15px;
    padding-right: 15px;
    max-width: 100%;
}

/* Forcer les tableaux à faire défiler horizontalement si nécessaire */
.dashboard-content .table-responsive {
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Assurer que les cartes ne dépassent pas */
.dashboard-content .card {
    max-width: 100%;
    margin-bottom: 1rem;
    width: 100%;
}

/* Graphiques responsives */
.dashboard-content .chart-container {
    position: relative;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
    margin: 0 auto;
}

/* Contenu qui pourrait déborder */
.dashboard-content canvas,
.dashboard-content .chartjs-render-monitor {
    max-width: 100% !important;
    width: 100% !important;
    height: auto !important;
}

/* Empêcher tout débordement horizontal */
.dashboard-content * {
    max-width: 100%;
    box-sizing: border-box;
}

/* Styles spécifiques pour les cartes */
.card {
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
    width: 100%;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    background: white !important;
    border-bottom: 2px solid #f0f0f0;
}

.badge {
    font-weight: 600;
}

.table-hover tbody tr:hover {
    background-color: rgba(22, 163, 74, 0.05);
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-content .col-lg-8,
    .dashboard-content .col-lg-6,
    .dashboard-content .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .dashboard-content .chart-container {
        height: 250px !important;
    }
}
</style>
<?php /**PATH C:\laragon\www\kenam\resources\views/components/dashboard-layout.blade.php ENDPATH**/ ?>