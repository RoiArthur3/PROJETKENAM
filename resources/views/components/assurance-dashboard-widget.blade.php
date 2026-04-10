@php
    use App\Http\Controllers\Materiel\AssuranceController;
    
    $expiring = AssuranceController::getExpiringAlerts(30);
    $expired = AssuranceController::getExpiredAlerts();
    $total = $expiring->count() + $expired->count();
@endphp

<div class="card h-100 shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-shield-alt text-primary me-2"></i>Suivi Assurances</h5>
        <a href="{{ route('materiel.assurances.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
    </div>
    <div class="card-body">
        @if($total == 0)
            <div class="text-center py-4">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <p class="text-muted">Toutes les assurances sont à jour</p>
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($expired as $assurance)
                    <div class="list-group-item px-0 py-3 border-start border-4 border-danger">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $assurance->numero_police }}</h6>
                                <small class="text-muted">
                                    @if($assurance->vehicle)
                                        {{ $assurance->vehicle->immatriculation }}
                                    @endif
                                </small>
                                <br>
                                <span class="badge bg-danger mt-1">Expirée depuis {{ now()->diffInDays($assurance->date_fin) }} jour{{ now()->diffInDays($assurance->date_fin) > 1 ? 's' : '' }}</span>
                            </div>
                            <a href="{{ route('materiel.assurances.show', $assurance) }}" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach

                @foreach($expiring as $assurance)
                    <div class="list-group-item px-0 py-3 border-start border-4 border-warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $assurance->numero_police }}</h6>
                                <small class="text-muted">
                                    @if($assurance->vehicle)
                                        {{ $assurance->vehicle->immatriculation }}
                                    @endif
                                </small>
                                <br>
                                <span class="badge bg-warning text-dark mt-1">{{ now()->diffInDays($assurance->date_fin) }} jour{{ now()->diffInDays($assurance->date_fin) > 1 ? 's' : '' }} restants</span>
                            </div>
                            <a href="{{ route('materiel.assurances.edit', $assurance) }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
