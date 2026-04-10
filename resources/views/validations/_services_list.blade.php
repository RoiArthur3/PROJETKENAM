<!-- Affichage des services pour une opération -->
@if(isset($operation) && $operation->services && $operation->services->count() > 0)
    <div class="mb-3">
        <p class="mb-1"><strong>Services concernés:</strong></p>
        <div class="d-flex flex-wrap gap-2">
            @foreach($operation->services as $service)
                <span class="badge bg-{{ getServiceStatusColor($service->statut) }} d-flex align-items-center">
                    <i class="fas fa-building me-1"></i>
                    {{ $service->service_name }}
                    @if($service->destinataire_nom)
                        <small class="ms-1">({{ $service->destinataire_nom }})</small>
                    @endif
                </span>
            @endforeach
        </div>
    </div>
@else
    <div class="mb-3">
        <p class="mb-1"><strong>Services concernés:</strong></p>
        <span class="text-muted">Aucun service spécifié</span>
    </div>
@endif

@php
function getServiceStatusColor($statut) {
    switch($statut) {
        case 'en_attente': return 'warning';
        case 'validé': return 'success';
        case 'rejeté': return 'danger';
        case 'en_cours': return 'info';
        default: return 'secondary';
    }
}
@endphp
