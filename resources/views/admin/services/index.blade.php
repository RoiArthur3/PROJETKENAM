@extends('layouts.app')

@section('title', 'Services Opérationnels | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary">
            <i class="fas fa-building me-2"></i>Services Opérationnels
        </h4>
        <a href="/admin/services/create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nouveau Service
        </a>
    </div>

    @if ($services->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun service trouvé</h5>
                <p class="text-muted">Commencez par créer votre premier service opérationnel.</p>
                <a href="/admin/services/create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Créer un service
                </a>
            </div>
        </div>
    @else
        <div class="row">
            @foreach ($services as $service)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100 {{ $service->actif ? '' : 'border-warning' }}">
                        <div class="card-header d-flex justify-content-between align-items-center"
                             style="background-color: {{ $service->couleur ?? '#007bff' }}20; border-bottom: 2px solid {{ $service->couleur ?? '#007bff' }};">
                            <div class="d-flex align-items-center">
                                <i class="{{ $service->icone ?? 'fas fa-building' }} me-2" style="color: {{ $service->couleur ?? '#007bff' }};"></i>
                                <h6 class="mb-0">{{ $service->nom }}</h6>
                            </div>
                            <div>
                                @if($service->actif)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-warning">Inactif</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Email</small><br>
                                <strong>{{ $service->email }}</strong>
                            </div>

                            @if($service->telephone)
                            <div class="mb-2">
                                <small class="text-muted">Téléphone</small><br>
                                <strong>{{ $service->telephone }}</strong>
                            </div>
                            @endif

                            @if($service->responsable)
                            <div class="mb-2">
                                <small class="text-muted">Responsable</small><br>
                                <strong>{{ $service->responsable }}</strong>
                            </div>
                            @endif

                            @if($service->description)
                            <div class="mb-2">
                                <small class="text-muted">Description</small><br>
                                <span>{{ Str::limit($service->description, 80) }}</span>
                            </div>
                            @endif

                            <div class="mt-3">
                                <small class="text-muted">Créé le {{ $service->created_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="btn-group w-100" role="group">
                                <a href="/admin/services/{{ $service->id }}/edit" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i>Modifier
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $service->id }}, '{{ $service->nom }}')">
                                    <i class="fas fa-trash me-1"></i>Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le service <strong id="serviceName"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(serviceId, serviceName) {
    document.getElementById('serviceName').textContent = serviceName;
    document.getElementById('deleteForm').action = '/admin/services/' + serviceId;

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
