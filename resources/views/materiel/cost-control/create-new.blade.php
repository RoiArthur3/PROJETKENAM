@extends('layouts.authenticated')
@section('header-title', 'Nouveau Pointage Engin - Cost Control')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">
                <i class="fas fa-stopwatch me-2 text-info"></i>Nouveau Pointage Engin
            </h1>
            <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour au Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Informations de Pointage
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('materiel.cost-control.store') }}" method="POST">
                        @csrf

                        <!-- Sélection Mission/Véhicule -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-tasks me-2"></i>Mission et Engin
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_mission_id" class="form-label fw-bold">
                                    <i class="fas fa-route me-1"></i>Mission Véhicule *
                                </label>
                                <select name="vehicle_mission_id" id="vehicle_mission_id" class="form-select" 
                                        onchange="loadMissionDetails(this.value)">
                                    <option value="">Sélectionner une mission</option>
                                    @foreach($missions as $mission)
                                        <option value="{{ $mission->id }}" 
                                                data-vehicle="{{ $mission->vehicle_id }}"
                                                data-driver="{{ $mission->driver_id }}"
                                                data-client="{{ $mission->client->nom ?? '' }}"
                                                data-start="{{ $mission->start_at?->format('Y-m-d') }}"
                                                data-end="{{ $mission->end_at?->format('Y-m-d') }}"
                                                data-supplier-price="{{ $mission->daily_supplier_price ?? 0 }}"
                                                data-client-price="{{ $mission->daily_client_price ?? 0 }}"
                                                {{ $selectedMissionId == $mission->id ? 'selected' : '' }}>
                                            {{ $mission->vehicle->immatriculation ?? 'N/A' }} - 
                                            {{ $mission->client->nom ?? 'N/A' }} 
                                            ({{ $mission->start_at?->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="operation_id" class="form-label fw-bold">
                                    <i class="fas fa-project-diagram me-1"></i>Opération (Alternative)
                                </label>
                                <select name="operation_id" id="operation_id" class="form-select">
                                    <option value="">Sélectionner une opération</option>
                                    @foreach($operations as $operation)
                                        <option value="{{ $operation->id }}">
                                            {{ $operation->titre }} - {{ $operation->montant ?? 0 }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Utiliser si aucune mission véhicule</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label fw-bold">
                                    <i class="fas fa-truck me-1"></i>Engin *
                                </label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                                    <option value="">Sélectionner un engin</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="driver_id" class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i>Chauffeur
                                </label>
                                <select name="driver_id" id="driver_id" class="form-select">
                                    <option value="">Sélectionner un chauffeur</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">
                                            {{ $driver->nom }} {{ $driver->prenoms }} - {{ $driver->contact }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_pointage" class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Date du Pointage *
                                </label>
                                <input type="date" name="date_pointage" id="date_pointage" 
                                       class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="unit_type" class="form-label fw-bold">
                                    <i class="fas fa-clock me-1"></i>Type d'Unité *
                                </label>
                                <select name="unit_type" id="unit_type" class="form-select" required>
                                    <option value="heure">Heure</option>
                                    <option value="jour">Jour</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label fw-bold">
                                    <i class="fas fa-calculator me-1"></i>Quantité *
                                </label>
                                <input type="number" name="quantity" id="quantity" 
                                       class="form-control" step="0.01" min="0" required>
                                <small class="text-muted">Nombre d'heures ou de jours</small>
                            </div>
                        </div>

                        <!-- Coûts et Prix -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-money-bill-wave me-2"></i>Coûts et Prix
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="supplier_unit_cost" class="form-label fw-bold">
                                    <i class="fas fa-hand-holding-usd me-1"></i>Coût Unitaire Fournisseur
                                </label>
                                <input type="number" name="supplier_unit_cost" id="supplier_unit_cost" 
                                       class="form-control" step="0.01" min="0">
                                <small class="text-muted">Coût par unité pour le fournisseur</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="client_unit_price" class="form-label fw-bold">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>Prix Unitaire Client
                                </label>
                                <input type="number" name="client_unit_price" id="client_unit_price" 
                                       class="form-control" step="0.01" min="0">
                                <small class="text-muted">Prix par unité pour le client</small>
                            </div>

                            <!-- Calculs automatiques -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calculator me-1"></i>Coût Total Fournisseur
                                </label>
                                <div class="form-control bg-light" id="total_supplier_cost">
                                    0 FCFA
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-file-invoice me-1"></i>Montant Total Client
                                </label>
                                <div class="form-control bg-light" id="total_client_amount">
                                    0 FCFA
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-chart-line me-1"></i>Marge Bénéficiaire
                                </label>
                                <div class="form-control bg-{{ ($pointage->total_client_amount ?? 0) - ($pointage->total_supplier_cost ?? 0) >= 0 ? 'success' : 'danger' }} text-white" id="margin">
                                    0 FCFA
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="notes" class="form-label fw-bold">
                                    <i class="fas fa-sticky-note me-1"></i>Notes et Observations
                                </label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="form-control" 
                                          placeholder="Notes supplémentaires sur le pointage...">{{ $pointage->notes ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer le Pointage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations Mission -->
            <div class="card shadow-sm mb-4" id="mission-info" style="display: none;">
                <div class="card-header bg-info">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Détails Mission
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Client:</small>
                        <div class="fw-bold" id="mission-client">-</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Période:</small>
                        <div class="fw-bold" id="mission-period">-</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Véhicule:</small>
                        <div class="fw-bold" id="mission-vehicle">-</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Chauffeur:</small>
                        <div class="fw-bold" id="mission-driver">-</div>
                    </div>
                </div>
            </div>

            <!-- Calculatrice -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-calculator me-2"></i>Calculatrice
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small">Quantité:</label>
                        <input type="number" id="calc-quantity" class="form-control form-control-sm" 
                               step="0.01" min="0" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Prix Unitaire:</label>
                        <input type="number" id="calc-price" class="form-control form-control-sm" 
                               step="0.01" min="0" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Total:</label>
                        <div class="form-control bg-light form-control-sm" id="calc-total">0 FCFA</div>
                    </div>
                </div>
            </div>

            <!-- Aide -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-question-circle me-2"></i>Aide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Pointage Cost Control:</strong> Enregistrez les heures/jours travaillés 
                        avec les coûts fournisseurs et prix clients pour suivre la rentabilité.
                    </div>
                    <div class="small">
                        <div class="mb-2">
                            <strong>Unité Heure:</strong> Pour les missions facturées à l'heure
                        </div>
                        <div class="mb-2">
                            <strong>Unité Jour:</strong> Pour les missions facturées au jour
                        </div>
                        <div>
                            <strong>Marge:</strong> Différence entre revenu client et coût fournisseur
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const supplierCostInput = document.getElementById('supplier_unit_cost');
    const clientPriceInput = document.getElementById('client_unit_price');
    const calcQuantity = document.getElementById('calc-quantity');
    const calcPrice = document.getElementById('calc-price');
    
    function calculateCosts() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const supplierCost = parseFloat(supplierCostInput.value) || 0;
        const clientPrice = parseFloat(clientPriceInput.value) || 0;
        
        const totalSupplierCost = quantity * supplierCost;
        const totalClientAmount = quantity * clientPrice;
        const margin = totalClientAmount - totalSupplierCost;
        
        document.getElementById('total_supplier_cost').textContent = 
            totalSupplierCost.toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('total_client_amount').textContent = 
            totalClientAmount.toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('margin').textContent = 
            margin.toLocaleString('fr-FR') + ' FCFA';
        
        // Couleur de la marge
        const marginElement = document.getElementById('margin');
        marginElement.className = 'form-control bg-' + (margin >= 0 ? 'success' : 'danger') + ' text-white';
    }
    
    function calculateCalculator() {
        const quantity = parseFloat(calcQuantity.value) || 0;
        const price = parseFloat(calcPrice.value) || 0;
        const total = quantity * price;
        
        document.getElementById('calc-total').textContent = 
            total.toLocaleString('fr-FR') + ' FCFA';
    }
    
    function loadMissionDetails(missionId) {
        const select = document.getElementById('vehicle_mission_id');
        const option = select.options[select.selectedIndex];
        
        if (missionId && option) {
            // Remplir les champs automatiquement
            document.getElementById('vehicle_id').value = option.dataset.vehicle || '';
            document.getElementById('driver_id').value = option.dataset.driver || '';
            document.getElementById('supplier_unit_cost').value = option.dataset.supplierPrice || '';
            document.getElementById('client_unit_price').value = option.dataset.clientPrice || '';
            
            // Afficher les infos de mission
            document.getElementById('mission-client').textContent = option.dataset.client || '-';
            document.getElementById('mission-period').textContent = 
                (option.dataset.start || '-') + ' au ' + (option.dataset.end || '-');
            document.getElementById('mission-vehicle').textContent = 
                option.dataset.vehicle ? document.querySelector('#vehicle_id option[value="' + option.dataset.vehicle + '")?.textContent : '-';
            document.getElementById('mission-driver').textContent = 
                option.dataset.driver ? document.querySelector('#driver_id option[value="' + option.dataset.driver + '")?.textContent : '-';
            
            document.getElementById('mission-info').style.display = 'block';
            
            calculateCosts();
        } else {
            document.getElementById('mission-info').style.display = 'none';
        }
    }
    
    // Écouteurs d'événements
    quantityInput.addEventListener('input', calculateCosts);
    supplierCostInput.addEventListener('input', calculateCosts);
    clientPriceInput.addEventListener('input', calculateCosts);
    calcQuantity.addEventListener('input', calculateCalculator);
    calcPrice.addEventListener('input', calculateCalculator);
    
    // Charger les détails au chargement si une mission est sélectionnée
    const selectedMission = document.getElementById('vehicle_mission_id').value;
    if (selectedMission) {
        loadMissionDetails(selectedMission);
    }
});
</script>
@endsection
