@props([
    'title' => 'Liste',
    'icon' => 'fa-list',
    'createRoute' => null,
    'createLabel' => 'Ajouter',
    'exportable' => false,
    'searchable' => true,
    'searchPlaceholder' => 'Rechercher...',
    'smartFilter' => true
])

<div class="container-fluid p-0 smart-list-layout" data-smart-filter="{{ $smartFilter ? '1' : '0' }}">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas {{ $icon }} me-2 text-primary"></i>{{ $title }}
            </h1>
            @if(isset($subtitle))
                <p class="text-muted mb-0">{{ $subtitle }}</p>
            @endif
        </div>
        <div>
            <div class="btn-group">
                @if($createRoute)
                    <a href="{{ route($createRoute) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>{{ $createLabel }}
                    </a>
                @endif
                @if($exportable)
                    <button class="btn btn-outline-secondary btn-sm" onclick="exportData()">
                        <i class="fas fa-download me-1"></i>Exporter
                    </button>
                @endif
                @if(isset($headerActions))
                    {{ $headerActions }}
                @endif
            </div>
        </div>
    </div>

    <!-- Cartes KPI (optionnel) -->
    @if(isset($kpis))
        <div class="row mb-3 px-3">
            {{ $kpis }}
        </div>
    @endif

    <!-- Filtres et Recherche -->
    @if($searchable || isset($filters))
        <div class="card shadow-sm mb-3 mx-3">
            <div class="card-body">
                <form class="row g-3" method="GET">
                    @if($searchable)
                        <div class="col-md-4">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control js-smart-filter-input" placeholder="{{ $searchPlaceholder }}" value="{{ request('search') }}">
                                <button class="btn btn-outline-success" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            @if($smartFilter)
                                <small class="text-muted d-block mt-1">
                                    Filtre intelligent: mots-cles, "phrase exacte", -mot exclu.
                                </small>
                            @endif
                        </div>
                    @endif

                    @if(isset($filters))
                        {{ $filters }}
                    @endif

                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Tableau principal -->
    <div class="card shadow-sm mx-3">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-table me-2"></i>{{ $title }}
                @if(isset($count))
                    <span class="badge bg-primary ms-2">{{ $count }}</span>
                @endif
            </h6>
            @if(isset($tableActions))
                {{ $tableActions }}
            @endif
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm js-smart-filter-table">
                    {{ $slot }}
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($pagination))
                <nav class="mt-2">
                    {{ $pagination }}
                </nav>
            @endif
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
