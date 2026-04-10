@php
    use App\Http\Controllers\Materiel\AssuranceController;
    
    $expiring = AssuranceController::getExpiringAlerts(30);
    $expired = AssuranceController::getExpiredAlerts();
    $total_alerts = $expiring->count() + $expired->count();
@endphp

@if($total_alerts > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
        <div class="flex-grow-1">
            <strong>⚠️ Alertes Assurances</strong>
            @if($expired->count() > 0)
                <div class="mt-2">
                    <span class="badge bg-danger me-2">
                        {{ $expired->count() }} expirée{{ $expired->count() > 1 ? 's' : '' }}
                    </span>
                </div>
            @endif
            @if($expiring->count() > 0)
                <div class="mt-2">
                    <span class="badge bg-warning me-2">
                        {{ $expiring->count() }} à renouveler
                    </span>
                </div>
            @endif
            <div class="mt-2">
                <a href="{{ route('materiel.assurances.index') }}" class="btn btn-sm btn-outline-dark">
                    <i class="fas fa-shield-alt me-1"></i>Voir les assurances
                </a>
            </div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
