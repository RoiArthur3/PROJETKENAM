@props([
    'title' => 'KPI',
    'value' => '0',
    'icon' => 'fa-chart-line',
    'color' => 'primary',
    'subtitle' => '',
    'trend' => null,
    'trendValue' => null
])

<div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-start border-{{ $color }} border-4 shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted small text-uppercase fw-bold mb-1">{{ $title }}</div>
                    <div class="h3 mb-0 text-{{ $color }}">{{ $value }}</div>
                    @if($subtitle)
                        <small class="text-muted">{{ $subtitle }}</small>
                    @endif
                    @if($trend && $trendValue)
                        <small class="text-{{ $trend === 'up' ? 'success' : ($trend === 'down' ? 'danger' : 'muted') }}">
                            <i class="fas fa-arrow-{{ $trend === 'up' ? 'up' : 'down' }}"></i> {{ $trendValue }}
                        </small>
                    @endif
                </div>
                <i class="fas {{ $icon }} fa-3x text-{{ $color }} opacity-25"></i>
            </div>
        </div>
    </div>
</div>
