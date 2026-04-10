@props([
    'name' => 'services',
    'multiple' => true,
    'required' => false,
    'placeholder' => 'Sélectionner des services...',
    'maxSelection' => null
])

<div class="dynamic-service-selector" data-name="{{ $name }}" data-multiple="{{ $multiple ? 'true' : 'false' }}" data-required="{{ $required ? 'true' : 'false' }}">
    <label class="form-label fw-bold">
        <i class="fas fa-building me-2"></i>Services concernés
        @if($required) <span class="text-danger">*</span> @endif
    </label>

    <!-- Champ de recherche -->
    <div class="mb-3">
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control" id="serviceSearch_{{ $name }}"
                   placeholder="Rechercher un service par nom, email ou responsable...">
            <button type="button" class="btn btn-outline-primary" onclick="loadServices('{{ $name }}')">
                <i class="fas fa-sync"></i>
            </button>
        </div>
    </div>

    <!-- Liste des services disponibles -->
    <div class="services-list mb-3 border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
        <div class="text-center text-muted py-3">
            <i class="fas fa-spinner fa-spin"></i>
            <p class="mb-0 mt-2">Chargement des services...</p>
        </div>
    </div>

    <!-- Services sélectionnés -->
    <div class="selected-services mb-3">
        <h6 class="text-primary mb-2">
            <i class="fas fa-check-circle me-2"></i>Services sélectionnés ({{ $multiple ? 'multiple' : 'un' }})
        </h6>
        <div class="selected-list border rounded p-3 bg-white" style="min-height: 60px;">
            <div class="text-center text-muted py-3">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p class="mb-0">Aucun service sélectionné</p>
            </div>
        </div>
    </div>

    <!-- Champ caché pour le formulaire -->
    <input type="hidden" name="{{ $name }}" id="{{ $name }}_hidden" value="">

    <!-- Champ pour ordre de validation (si multiple) -->
    @if($multiple)
    <input type="hidden" name="{{ $name }}_order" id="{{ $name }}_order" value="">
    @endif

    <div class="form-text">
        <i class="fas fa-info-circle me-1"></i>
        Les services seront chargés dynamiquement depuis la base de données.
    </div>
</div>

<script>
// Variables globales pour le sélecteur
let availableServices = {};
let selectedServices = {};
let serviceSelectors = {};

// Initialiser le sélecteur
document.addEventListener('DOMContentLoaded', function() {
    const selectorName = '{{ $name }}';
    serviceSelectors[selectorName] = {
        multiple: {{ $multiple ? 'true' : 'false' }},
        required: {{ $required ? 'true' : 'false' }},
        maxSelection: {{ $maxSelection ?? 'null' }}
    };

    // Charger les services au démarrage
    loadServices(selectorName);

    // Ajouter l'événement de recherche
    const searchInput = document.getElementById('serviceSearch_' + selectorName);
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadServices(selectorName, this.value);
            }, 300);
        });
    }
});

function loadServices(selectorName, searchTerm = '') {
    const container = document.querySelector(`.dynamic-service-selector[data-name="${selectorName}"] .services-list`);

    if (!container) return;

    container.innerHTML = `
        <div class="text-center text-muted py-3">
            <i class="fas fa-spinner fa-spin"></i>
            <p class="mb-0 mt-2">Chargement des services...</p>
        </div>
    `;

    // Requête AJAX pour charger les services
    fetch(`/api/services?with_email=true&search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                availableServices[selectorName] = data.data;
                displayServices(selectorName, data.data);
            } else {
                throw new Error(data.message || 'Erreur lors du chargement');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            container.innerHTML = `
                <div class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="mb-0">Erreur lors du chargement des services</p>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="loadServices('${selectorName}')">
                        <i class="fas fa-redo me-1"></i>Réessayer
                    </button>
                </div>
            `;
        });
}

function displayServices(selectorName, services) {
    const container = document.querySelector(`.dynamic-service-selector[data-name="${selectorName}"] .services-list`);
    const selector = serviceSelectors[selectorName];

    if (!services || services.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-search fa-2x mb-2"></i>
                <p class="mb-0">Aucun service trouvé</p>
                <a href="{{ route('services.create') }}" class="btn btn-sm btn-primary mt-2" target="_blank">
                    <i class="fas fa-plus me-1"></i>Créer un service
                </a>
            </div>
        `;
        return;
    }

    let html = '<div class="list-group list-group-flush">';

    services.forEach(service => {
        const isSelected = selectedServices[selectorName] && selectedServices[selectorName][service.id];
        const isDisabled = selector.maxSelection && Object.keys(selectedServices[selectorName] || {}).length >= selector.maxSelection && !isSelected;

        html += `
            <div class="list-group-item list-group-item-action ${isSelected ? 'active' : ''} ${isDisabled ? 'disabled' : ''}"
                 onclick="toggleService('${selectorName}', ${service.id})"
                 style="cursor: ${isDisabled ? 'not-allowed' : 'pointer'};">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center">
                            <i class="${service.icone || 'fas fa-building'} me-2" style="color: ${service.couleur || '#007bff'};"></i>
                            <div>
                                <strong>${service.nom}</strong>
                                ${service.responsable ? `<br><small class="text-muted">Responsable: ${service.responsable}</small>` : ''}
                            </div>
                        </div>
                        <small class="text-muted">${service.email}</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="${selector.multiple ? 'checkbox' : 'radio'}"
                               ${isSelected ? 'checked' : ''}
                               ${isDisabled ? 'disabled' : ''}>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}

function toggleService(selectorName, serviceId) {
    const selector = serviceSelectors[selectorName];

    if (!selectedServices[selectorName]) {
        selectedServices[selectorName] = {};
    }

    const service = availableServices[selectorName].find(s => s.id === serviceId);
    if (!service) return;

    // Vérifier la limite de sélection
    if (selector.maxSelection && !selectedServices[selectorName][serviceId]) {
        const currentCount = Object.keys(selectedServices[selectorName]).length;
        if (currentCount >= selector.maxSelection) {
            alert(`Maximum ${selector.maxSelection} service(s) sélectionnable(s)`);
            return;
        }
    }

    if (selector.multiple) {
        // Sélection multiple
        if (selectedServices[selectorName][serviceId]) {
            delete selectedServices[selectorName][serviceId];
        } else {
            selectedServices[selectorName][serviceId] = service;
        }
    } else {
        // Sélection unique
        selectedServices[selectorName] = {};
        selectedServices[selectorName][serviceId] = service;
    }

    updateSelectedDisplay(selectorName);
    updateHiddenInput(selectorName);
    displayServices(selectorName, availableServices[selectorName]);
}

function updateSelectedDisplay(selectorName) {
    const container = document.querySelector(`.dynamic-service-selector[data-name="${selectorName}"] .selected-list`);
    const services = selectedServices[selectorName] || {};
    const serviceIds = Object.keys(services);

    if (serviceIds.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p class="mb-0">Aucun service sélectionné</p>
            </div>
        `;
        return;
    }

    let html = '<div class="d-flex flex-wrap gap-2">';

    serviceIds.forEach(serviceId => {
        const service = services[serviceId];
        html += `
            <span class="badge bg-primary d-flex align-items-center">
                <i class="${service.icone || 'fas fa-building'} me-1"></i>
                ${service.nom}
                <button type="button" class="btn-close btn-close-white ms-2" onclick="removeService('${selectorName}', ${serviceId})"></button>
            </span>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}

function removeService(selectorName, serviceId) {
    if (selectedServices[selectorName]) {
        delete selectedServices[selectorName][serviceId];
        updateSelectedDisplay(selectorName);
        updateHiddenInput(selectorName);
        displayServices(selectorName, availableServices[selectorName]);
    }
}

function updateHiddenInput(selectorName) {
    const hiddenInput = document.getElementById(selectorName + '_hidden');
    const orderInput = document.getElementById(selectorName + '_order');
    const services = selectedServices[selectorName] || {};
    const serviceIds = Object.keys(services);

    if (hiddenInput) {
        hiddenInput.value = serviceIds.join(',');
    }

    if (orderInput) {
        orderInput.value = serviceIds.join(',');
    }
}
</script>
