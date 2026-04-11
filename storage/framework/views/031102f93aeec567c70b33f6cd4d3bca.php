<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Liste',
    'icon' => 'fa-list',
    'createRoute' => null,
    'createLabel' => 'Ajouter',
    'exportable' => false,
    'searchable' => true,
    'searchPlaceholder' => 'Rechercher...',
    'smartFilter' => true
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
    'title' => 'Liste',
    'icon' => 'fa-list',
    'createRoute' => null,
    'createLabel' => 'Ajouter',
    'exportable' => false,
    'searchable' => true,
    'searchPlaceholder' => 'Rechercher...',
    'smartFilter' => true
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="container-fluid p-0 smart-list-layout" data-smart-filter="<?php echo e($smartFilter ? '1' : '0'); ?>">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas <?php echo e($icon); ?> me-2 text-primary"></i><?php echo e($title); ?>

            </h1>
            <?php if(isset($subtitle)): ?>
                <p class="text-muted mb-0"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </div>
        <div>
            <div class="btn-group">
                <?php if($createRoute): ?>
                    <a href="<?php echo e(route($createRoute)); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i><?php echo e($createLabel); ?>

                    </a>
                <?php endif; ?>
                <?php if($exportable): ?>
                    <button class="btn btn-outline-secondary btn-sm" onclick="exportData()">
                        <i class="fas fa-download me-1"></i>Exporter
                    </button>
                <?php endif; ?>
                <?php if(isset($headerActions)): ?>
                    <?php echo e($headerActions); ?>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cartes KPI (optionnel) -->
    <?php if(isset($kpis)): ?>
        <div class="row mb-3 px-3">
            <?php echo e($kpis); ?>

        </div>
    <?php endif; ?>

    <!-- Filtres et Recherche -->
    <?php if($searchable || isset($filters)): ?>
        <div class="card shadow-sm mb-3 mx-3">
            <div class="card-body">
                <form class="row g-3" method="GET">
                    <?php if($searchable): ?>
                        <div class="col-md-4">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control js-smart-filter-input" placeholder="<?php echo e($searchPlaceholder); ?>" value="<?php echo e(request('search')); ?>">
                                <button class="btn btn-outline-success" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <?php if($smartFilter): ?>
                                <small class="text-muted d-block mt-1">
                                    Filtre intelligent: mots-cles, "phrase exacte", -mot exclu.
                                </small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($filters)): ?>
                        <?php echo e($filters); ?>

                    <?php endif; ?>

                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="<?php echo e(url()->current()); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tableau principal -->
    <div class="card shadow-sm mx-3">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-table me-2"></i><?php echo e($title); ?>

                <?php if(isset($count)): ?>
                    <span class="badge bg-primary ms-2"><?php echo e($count); ?></span>
                <?php endif; ?>
            </h6>
            <?php if(isset($tableActions)): ?>
                <?php echo e($tableActions); ?>

            <?php endif; ?>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm js-smart-filter-table">
                    <?php echo e($slot); ?>

                </table>
            </div>

            <!-- Pagination -->
            <?php if(isset($pagination)): ?>
                <nav class="mt-2">
                    <?php echo e($pagination); ?>

                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card-header {
    background: white !important;
    border-bottom: 2px solid #f0f0f0;
}

.table-hover tbody tr:hover {
    background-color: rgba(22, 163, 74, 0.05);
    cursor: pointer;
}

.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #6c757d;
    border-bottom: 2px solid #dee2e6;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.badge {
    font-weight: 600;
    padding: 0.35em 0.65em;
}

.input-group .btn {
    border-color: #ced4da;
}

.input-group .btn:hover {
    background-color: #16a34a;
    border-color: #16a34a;
    color: white;
}

.smart-filter-hidden-row {
    display: none !important;
}
</style>

<script>
(function () {
    function normalizeText(value) {
        return (value || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/\s+/g, ' ')
            .trim();
    }

    function parseTokens(value) {
        const tokens = [];
        const input = value || '';
        const pattern = /"([^"]+)"|(\S+)/g;
        let match;

        while ((match = pattern.exec(input)) !== null) {
            const raw = match[1] || match[2] || '';
            const token = normalizeText(raw);
            if (!token) continue;

            if (token.startsWith('-') && token.length > 1) {
                tokens.push({ type: 'exclude', value: token.substring(1) });
            } else {
                tokens.push({ type: 'include', value: token });
            }
        }

        return tokens;
    }

    function applySmartFilter(root) {
        const isEnabled = root.getAttribute('data-smart-filter') === '1';
        if (!isEnabled) return;

        const input = root.querySelector('.js-smart-filter-input');
        const table = root.querySelector('.js-smart-filter-table');
        if (!input || !table) return;

        const bodyRows = Array.from(table.querySelectorAll('tbody tr'));
        if (!bodyRows.length) return;

        const run = function () {
            const tokens = parseTokens(input.value);
            let visibleCount = 0;

            bodyRows.forEach(function (row) {
                const haystack = normalizeText(row.innerText);
                const includeOk = tokens
                    .filter(function (t) { return t.type === 'include'; })
                    .every(function (t) { return haystack.includes(t.value); });
                const excludeOk = tokens
                    .filter(function (t) { return t.type === 'exclude'; })
                    .every(function (t) { return !haystack.includes(t.value); });

                const isVisible = includeOk && excludeOk;
                row.classList.toggle('smart-filter-hidden-row', !isVisible);
                if (isVisible) visibleCount++;
            });

            root.setAttribute('data-smart-visible-count', String(visibleCount));
        };

        let timer = null;
        input.addEventListener('input', function () {
            if (timer) {
                clearTimeout(timer);
            }
            timer = setTimeout(run, 120);
        });

        run();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.smart-list-layout').forEach(applySmartFilter);
    });
})();
</script>
<?php /**PATH C:\laragon\www\kenam\resources\views/components/list-layout.blade.php ENDPATH**/ ?>