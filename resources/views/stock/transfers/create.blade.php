@extends('layouts.app')

@section('title', 'Nouveau Transfert de Stock')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">
                <i class="fas fa-exchange-alt text-info me-2"></i>
                Nouveau Transfert de Stock
            </h4>
            <p class="text-muted mb-0">Transférez des produits entre vos entrepôts</p>
        </div>
        <div>
            <a href="{{ route('stock.transfers') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <form action="{{ route('stock.transfers.store') }}" method="POST" id="transferForm">
        @csrf

        <div class="row g-4">
            <!-- Informations générales -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Informations générales
                        </h6>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <label class="form-label">Référence <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="reference"
                                   value="TRANS-{{ now()->format('Ymd-Hi') }}" readonly>
                            <small class="text-muted">Généré automatiquement</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de transfert <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="movement_date"
                                   value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Priorité</label>
                            <select class="form-select" name="priority">
                                <option value="normal">Normal</option>
                                <option value="urgent">Urgent</option>
                                <option value="low">Basse</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type de document</label>
                            <select class="form-select" name="document_type">
                                <option value="Transfert">Bon de Transfert</option>
                                <option value="Demande">Demande de transfert</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Référence document</label>
                            <input type="text" class="form-control" name="document_reference"
                                   placeholder="Numéro du document">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motif <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reason" rows="3" required
                                      placeholder="Description du motif du transfert..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2"
                                      placeholder="Notes supplémentaires..."></textarea>
                        </div>

                        <div class="mt-auto">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="validate_immediately"
                                       id="validateImmediately">
                                <label class="form-check-label" for="validateImmediately">
                                    Valider immédiatement le transfert
                                </label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" onclick="checkTransfer()">
                                    <i class="fas fa-search me-2"></i>Vérifier
                                </button>
                                <button type="submit" class="btn btn-info flex-fill">
                                    <i class="fas fa-exchange-alt me-2"></i>Effectuer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails du transfert -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-exchange-alt text-info me-2"></i>
                            Détails du transfert
                        </h6>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- Sélection produit et entrepôts -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Produit <span class="text-danger">*</span></label>
                                <select class="form-select" name="product_id" id="productSelect" required>
                                    <option value="">Sélectionner un produit</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-unit="{{ $product->unit }}"
                                                data-price="{{ $product->unit_price ?? 0 }}">
                                            {{ $product->name }} ({{ $product->reference }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="quantity"
                                           id="quantity" step="0.01" min="0.01" required>
                                    <span class="input-group-text" id="unitLabel">Unité</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Entrepôt source <span class="text-danger">*</span></label>
                                <select class="form-select" name="from_warehouse_id" id="fromWarehouseSelect" required>
                                    <option value="">Sélectionner l'entrepôt source</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Entrepôt destination <span class="text-danger">*</span></label>
                                <select class="form-select" name="to_warehouse_id" id="toWarehouseSelect" required>
                                    <option value="">Sélectionner l'entrepôt destination</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Stock disponible -->
                        <div class="alert alert-info mb-3" id="stockInfo" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>
                            <div id="stockInfoText"></div>
                        </div>

                        <!-- Alertes -->
                        <div class="alert alert-warning mb-3" id="transferWarning" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div id="transferWarningText"></div>
                        </div>

                        <!-- Résumé -->
                        <div class="mt-auto">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-calculator text-info me-2"></i>
                                        Résumé du transfert
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="text-muted small">Quantité</div>
                                                <div class="h4 text-info mb-0" id="summaryQuantity">0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="text-muted small">Valeur</div>
                                                <div class="h4 text-success mb-0" id="summaryValue">0 FCFA</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="text-muted small">Source</div>
                                                <div class="h6 text-primary mb-0" id="summarySource">-</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="text-muted small">Destination</div>
                                                <div class="h6 text-primary mb-0" id="summaryDestination">-</div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="text-muted small">Source</div>
                                    <div class="h6 text-danger mb-0" id="summarySource">-</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="text-muted small">Destination</div>
                                    <div class="h6 text-success mb-0" id="summaryDestination">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Update unit label when product changes
document.getElementById('productSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const unit = selectedOption.dataset.unit || 'Unité';
    const price = selectedOption.dataset.price || 0;

    document.getElementById('unitLabel').textContent = unit;
    calculateTotal();
    checkTransfer();
});

// Prevent same warehouse selection
document.getElementById('fromWarehouseSelect').addEventListener('change', function() {
    const toSelect = document.getElementById('toWarehouseSelect');
    toSelect.value = '';
    updateSummary();
    checkTransfer();
});

document.getElementById('toWarehouseSelect').addEventListener('change', function() {
    const fromSelect = document.getElementById('fromWarehouseSelect');
    if (this.value === fromSelect.value) {
        this.value = '';
        alert('L\'entrepôt destination doit être différent de l\'entrepôt source');
    }
    updateSummary();
    checkTransfer();
});

// Calculate total value
function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const productSelect = document.getElementById('productSelect');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const unitPrice = parseFloat(selectedOption.dataset.price) || 0;
    const total = quantity * unitPrice;

    // Update summary
    document.getElementById('summaryQuantity').textContent = quantity.toLocaleString('fr-FR');
    document.getElementById('summaryValue').textContent = total.toLocaleString('fr-FR') + ' FCFA';
}

// Update warehouse summary
function updateSummary() {
    const fromSelect = document.getElementById('fromWarehouseSelect');
    const toSelect = document.getElementById('toWarehouseSelect');

    const fromName = fromSelect.options[fromSelect.selectedIndex]?.text || '-';
    const toName = toSelect.options[toSelect.selectedIndex]?.text || '-';

    document.getElementById('summarySource').textContent = fromName;
    document.getElementById('summaryDestination').textContent = toName;
}

// Check transfer feasibility
async function checkTransfer() {
    const productId = document.getElementById('productSelect').value;
    const fromWarehouseId = document.getElementById('fromWarehouseSelect').value;
    const toWarehouseId = document.getElementById('toWarehouseSelect').value;
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;

    if (!productId || !fromWarehouseId || !toWarehouseId) {
        return;
    }

    const stockInfo = document.getElementById('stockInfo');
    const stockInfoText = document.getElementById('stockInfoText');
    const transferWarning = document.getElementById('transferWarning');
    const transferWarningText = document.getElementById('transferWarningText');

    // Hide warnings initially
    stockInfo.style.display = 'none';
    transferWarning.style.display = 'none';

    // Check if warehouses are different
    if (fromWarehouseId === toWarehouseId) {
        transferWarningText.innerHTML = '<strong>Erreur:</strong> Les entrepôts source et destination doivent être différents';
        transferWarning.style.display = 'block';
        return;
    }

    try {
        // Check stock in source warehouse
        const response = await fetch(`/stock/api/check-stock?product_id=${productId}&warehouse_id=${fromWarehouseId}`);
        const data = await response.json();

        // Show stock info
        stockInfoText.innerHTML = `
            <strong>Stock disponible (source):</strong> ${data.available} ${document.getElementById('unitLabel').textContent}<br>
            <strong>Stock total (source):</strong> ${data.current}<br>
            <strong>Réservé (source):</strong> ${data.reserved}
        `;
        stockInfo.style.display = 'block';

        // Check if quantity exceeds available stock
        if (quantity > data.available) {
            transferWarningText.innerHTML = `
                <strong>Attention:</strong> La quantité demandée (${quantity})
                dépasse le stock disponible dans l'entrepôt source (${data.available})
            `;
            transferWarning.style.display = 'block';
        }

    } catch (error) {
        console.error('Error checking transfer:', error);
    }
}

// Update status summary
document.getElementById('validateImmediately').addEventListener('change', function() {
    const status = this.checked ? 'Validé' : 'Brouillon';
    document.getElementById('summaryStatus').textContent = status;
});

// Form validation
document.getElementById('transferForm').addEventListener('submit', function(e) {
    const quantity = parseFloat(document.getElementById('quantity').value);
    const fromWarehouse = document.getElementById('fromWarehouseSelect').value;
    const toWarehouse = document.getElementById('toWarehouseSelect').value;

    if (quantity <= 0) {
        e.preventDefault();
        alert('La quantité doit être supérieure à 0');
        return;
    }

    if (fromWarehouse === toWarehouse) {
        e.preventDefault();
        alert('Les entrepôts source et destination doivent être différents');
        return;
    }
});

// Event listeners
document.getElementById('quantity').addEventListener('input', function() {
    calculateTotal();
    checkTransfer();
});

// Initialize
calculateTotal();
</script>
@endsection
