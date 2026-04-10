@props([
    'services' => null,
    'selectedServices' => []
])

@php
    if (!$services) {
        $services = App\Models\ServiceOperationnel::where('actif', true)->whereNotNull('email')->orderBy('nom')->get();
    }
@endphp

<!-- Sélecteur de services pour les opérations -->
<div class="operation-service-selector">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <label class="form-label fw-bold">
            <i class="fas fa-users me-2"></i>Services concernés (par ordre de validation) <span class="text-danger">*</span>
        </label>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addServiceToOperation()">
            <i class="fas fa-plus me-1"></i>Ajouter un service
        </button>
    </div>

    <!-- Liste des services sélectionnés -->
    <div id="selectedOperationServices" class="sortable-services mb-3 border rounded p-3 bg-light">
        @forelse($selectedServices as $index => $service)
            <div class="service-item d-flex align-items-center p-2 mb-2 border rounded bg-white" data-service-id="{{ $service->id }}">
                <div class="drag-handle me-2">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="badge bg-primary me-2">Validation {{ $index + 1 }}</div>
                <div class="flex-grow-1">
                    <strong>{{ $service->nom }}</strong><br>
                    <small class="text-muted">{{ $service->email }}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOperationService({{ $service->id }})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @empty
            <div class="text-center text-muted py-3">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p>Aucun service sélectionné</p>
            </div>
        @endforelse
    </div>

    <!-- Select caché pour les données du formulaire -->
    <select name="services_validation[]" id="servicesValidation" multiple class="d-none">
        @foreach($selectedServices as $service)
            <option value="{{ $service->id }}" selected>{{ $service->nom }}</option>
        @endforeach
    </select>

    <div class="form-text">
        <i class="fas fa-info-circle me-1"></i>
        Les services seront notifiés par ordre. Le premier service recevra "Validation 1", etc.
    </div>
</div>

<!-- Modal pour sélectionner un service -->
<div class="modal fade" id="serviceSelectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-building me-2"></i>Sélectionner un service
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select class="form-select" id="serviceSelect">
                    <option value="">Choisir un service...</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" data-email="{{ $service->email }}" data-name="{{ $service->nom }}">
                            {{ $service->nom }} ({{ $service->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="confirmAddOperationService()">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales pour le sélecteur de services
let availableOperationServices = @json($services);
let selectedOperationServices = @json($selectedServices);

function addServiceToOperation() {
    const modal = new bootstrap.Modal(document.getElementById('serviceSelectModal'));
    modal.show();
}

function confirmAddOperationService() {
    const select = document.getElementById('serviceSelect');
    const selectedOption = select.options[select.selectedIndex];
    
    if (!selectedOption.value) {
        alert('Veuillez sélectionner un service');
        return;
    }
    
    const serviceId = parseInt(selectedOption.value);
    
    // Vérifier si le service n'est pas déjà sélectionné
    if (selectedOperationServices.find(s => s.id === serviceId)) {
        alert('Ce service est déjà dans la liste');
        return;
    }
    
    const service = {
        id: serviceId,
        nom: selectedOption.dataset.name,
        email: selectedOption.dataset.email
    };
    
    selectedOperationServices.push(service);
    updateOperationServicesDisplay();
    
    // Fermer le modal et réinitialiser
    bootstrap.Modal.getInstance(document.getElementById('serviceSelectModal')).hide();
    select.value = '';
}

function removeOperationService(serviceId) {
    selectedOperationServices = selectedOperationServices.filter(s => s.id !== serviceId);
    updateOperationServicesDisplay();
}

function updateOperationServicesDisplay() {
    const container = document.getElementById('selectedOperationServices');
    const select = document.getElementById('servicesValidation');
    
    if (selectedOperationServices.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p>Aucun service sélectionné</p>
            </div>
        `;
        select.innerHTML = '';
        return;
    }
    
    let html = '';
    let selectHtml = '';
    
    selectedOperationServices.forEach((service, index) => {
        html += `
            <div class="service-item d-flex align-items-center p-2 mb-2 border rounded bg-white" data-service-id="${service.id}">
                <div class="drag-handle me-2">
                    <i class="fas fa-grip-vertical text-muted"></i>
                </div>
                <div class="badge bg-primary me-2">Validation ${index + 1}</div>
                <div class="flex-grow-1">
                    <strong>${service.nom}</strong><br>
                    <small class="text-muted">${service.email}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOperationService(${service.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        selectHtml += `<option value="${service.id}" selected>${service.nom}</option>`;
    });
    
    container.innerHTML = html;
    select.innerHTML = selectHtml;
}

// Initialiser le drag and drop si disponible
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('selectedOperationServices');
    if (container && typeof Sortable !== 'undefined') {
        new Sortable(container, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: function() {
                // Mettre à jour l'ordre des badges
                const items = container.querySelectorAll('.service-item');
                items.forEach((item, index) => {
                    const badge = item.querySelector('.badge');
                    badge.textContent = `Validation ${index + 1}`;
                });
                
                // Mettre à jour le tableau selectedOperationServices
                const newOrder = [];
                items.forEach(item => {
                    const serviceId = parseInt(item.dataset.serviceId);
                    const service = selectedOperationServices.find(s => s.id === serviceId);
                    if (service) newOrder.push(service);
                });
                selectedOperationServices = newOrder;
                updateOperationServicesDisplay();
            }
        });
    }
});
</script>
