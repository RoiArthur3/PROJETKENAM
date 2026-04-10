@extends('layouts.app')

@section('title', 'Envoyer Email aux Services | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-envelope me-2 text-primary"></i>Envoyer Email aux Services
        </h1>
        <a href="{{ route('services.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour aux Services
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>Composition de l'Email
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('services.email.send') }}">
                        @csrf

                        <!-- Sélection des services -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-users me-2"></i>Services Destinataires *
                            </label>
                            <div class="row">
                                @foreach($services as $service)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input service-checkbox" type="checkbox" 
                                                   name="services[]" value="{{ $service->id }}" id="service_{{ $service->id }}">
                                            <label class="form-check-label" for="service_{{ $service->id }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas fa-building me-1 text-primary"></i>
                                                        {{ $service->nom }}
                                                    </span>
                                                    <small class="text-muted">{{ $service->email }}</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllServices()">
                                    <i class="fas fa-check-square me-1"></i>Tout sélectionner
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllServices()">
                                    <i class="fas fa-square me-1"></i>Tout désélectionner
                                </button>
                            </div>
                        </div>

                        <!-- Sujet -->
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">
                                <i class="fas fa-heading me-2"></i>Sujet *
                            </label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   placeholder="Entrez le sujet de l'email" required>
                        </div>

                        <!-- Message -->
                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">
                                <i class="fas fa-comment me-2"></i>Message *
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="8" 
                                      placeholder="Rédigez votre message ici..." required></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('services.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Envoyer l'Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Aperçu des services sélectionnés -->
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Aperçu Destinataires
                    </h6>
                </div>
                <div class="card-body">
                    <div id="selectedServicesPreview">
                        <p class="text-muted text-center">Aucun service sélectionné</p>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow mt-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $services->count() }}</h4>
                                <small class="text-muted">Services actifs</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-1" id="selectedCount">0</h4>
                            <small class="text-muted">Sélectionnés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateSelectedPreview() {
    const checkboxes = document.querySelectorAll('.service-checkbox:checked');
    const preview = document.getElementById('selectedServicesPreview');
    const count = document.getElementById('selectedCount');
    
    count.textContent = checkboxes.length;
    
    if (checkboxes.length === 0) {
        preview.innerHTML = '<p class="text-muted text-center">Aucun service sélectionné</p>';
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    checkboxes.forEach(checkbox => {
        const label = checkbox.nextElementSibling;
        const serviceName = label.querySelector('span').textContent.trim();
        const serviceEmail = label.querySelector('small').textContent.trim();
        
        html += `
            <div class="list-group-item px-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-building me-1 text-primary"></i>
                        <strong>${serviceName}</strong>
                    </div>
                    <small class="text-muted">${serviceEmail}</small>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    preview.innerHTML = html;
}

function selectAllServices() {
    document.querySelectorAll('.service-checkbox').forEach(cb => cb.checked = true);
    updateSelectedPreview();
}

function deselectAllServices() {
    document.querySelectorAll('.service-checkbox').forEach(cb => cb.checked = false);
    updateSelectedPreview();
}

// Initialiser
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.service-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedPreview);
    });
    updateSelectedPreview();
});
</script>
@endsection
