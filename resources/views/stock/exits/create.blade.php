@extends('layouts.app')

@section('title', 'Nouvelle Sortie de Stock')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">
                <i class="fas fa-minus-circle text-danger me-2"></i>
                Nouvelle Sortie de Stock
            </h4>
            <p class="text-muted mb-0">Enregistrez les produits sortant de vos entrepôts</p>
        </div>
        <div>
            <a href="{{ route('stock.exits') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Résumé de stock en haut -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0">
                <i class="fas fa-chart-line text-info me-2"></i>
                État du Stock Actuel
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="text-muted small">Produits actifs</div>
                        <div class="h4 text-primary mb-0">{{ $products->count() }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="text-muted small">Entrepôts disponibles</div>
                        <div class="h4 text-success mb-0">{{ $warehouses->count() }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="text-muted small">Opérations en cours</div>
                        <div class="h4 text-warning mb-0">{{ $operations->count() }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="text-muted small">Sorties aujourd'hui</div>
                        <div class="h4 text-danger mb-0">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('stock.exits.store') }}" method="POST" id="exitForm">
        @csrf
        
        <div class="row g-4">
            <!-- Informations générales -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Informations générales
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Référence <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="reference" 
                                           value="SORT-{{ now()->format('Ymd-Hi') }}" readonly>
                                    <small class="text-muted">Généré automatiquement</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Date de mouvement <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" name="movement_date" 
                                           value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Type de sortie <span class="text-danger">*</span></label>
                                    <select class="form-select" name="exit_type" id="exitType" required>
                                        <option value="operation">Pour opération</option>
                                        <option value="internal">Usage interne</option>
                                        <option value="loss">Perte</option>
                                        <option value="damage">Dégât</option>
                                        <option value="return">Retour fournisseur</option>
                                        <option value="adjustment">Ajustement</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="mb-3" id="operationField" style="display: none;">
                                    <label class="form-label">Opération concernée</label>
                                    <select class="form-select" name="operation_id">
                                        <option value="">Sélectionner une opération</option>
                                        @foreach($operations as $operation)
                                            <option value="{{ $operation->id }}">{{ $operation->titre ?? $operation->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Type de document</label>
                                    <select class="form-select" name="document_type">
                                        <option value="BL">Bon de Livraison</option>
                                        <option value="Demande">Demande de sortie</option>
                                        <option value="Ajustement">Ajustement</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Référence document</label>
                                    <input type="text" class="form-control" name="document_reference" 
                                           placeholder="Numéro du document">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Motif <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="reason" rows="3" required
                                              placeholder="Description du motif de la sortie..."></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" rows="3"
                                              placeholder="Notes supplémentaires..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary" onclick="checkStock()">
                                        <i class="fas fa-search me-2"></i>Vérifier stock
                                    </button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-minus-circle me-2"></i>Effectuer sortie
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails de la sortie -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-boxes text-danger me-2"></i>
                            Produits à sortir
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Sélection produit et entrepôt -->
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
                                <label class="form-label">Entrepôt <span class="text-danger">*</span></label>
                                <select class="form-select" name="warehouse_id" id="warehouseSelect" required>
                                    <option value="">Sélectionner un entrepôt</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="quantity" 
                                           id="quantity" step="0.01" min="0.01" required>
                                    <span class="input-group-text" id="unitLabel">Unité</span>
                                </div>
                            </div>
                        </div>

                        <!-- Alertes et informations -->
                        <!-- Stock disponible -->
                        <div class="alert alert-info mb-3" id="stockInfo" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>
                            <div id="stockInfoText"></div>
                        </div>

                        <!-- Alertes de stock -->
                        <div class="alert alert-warning mb-3" id="stockWarning" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div id="stockWarningText"></div>
                        </div>

                        <!-- Informations de la sortie en cours -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-calculator text-danger me-2"></i>
                                    Détails de la sortie
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="text-muted small">Quantité</div>
                                            <div class="h4 text-danger mb-0" id="summaryQuantity">0</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="text-muted small">Valeur</div>
                                            <div class="h4 text-warning mb-0" id="summaryValue">0 FCFA</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="text-muted small">Entrepôt</div>
                                            <div class="h6 text-primary mb-0" id="summaryWarehouse">-</div>
                                        </div>
                                    </div>
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
    document.getElementById('unitPrice').value = price;
    calculateTotal();
    checkStock();
});

// Show/hide operation field based on exit type
document.getElementById('exitType').addEventListener('change', function() {
    const operationField = document.getElementById('operationField');
    if (this.value === 'operation') {
        operationField.style.display = 'block';
    } else {
        operationField.style.display = 'none';
        document.querySelector('[name="operation_id"]').value = '';
    }
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

// Check stock availability
async function checkStock() {
    const productId = document.getElementById('productSelect').value;
    const warehouseId = document.getElementById('warehouseSelect').value;
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    
    if (!productId || !warehouseId) {
        return;
    }
    
    try {
        const response = await fetch(`/stock/api/check-stock?product_id=${productId}&warehouse_id=${warehouseId}`);
        const data = await response.json();
        
        const stockInfo = document.getElementById('stockInfo');
        const stockInfoText = document.getElementById('stockInfoText');
        const stockWarning = document.getElementById('stockWarning');
        const stockWarningText = document.getElementById('stockWarningText');
        
        // Show stock info
        stockInfoText.innerHTML = `
            <strong>Stock disponible:</strong> ${data.available} ${document.getElementById('unitLabel').textContent}<br>
            <strong>Stock total:</strong> ${data.current}<br>
            <strong>Réservé:</strong> ${data.reserved}
        `;
        stockInfo.style.display = 'block';
        
        // Check if quantity exceeds available stock
        if (quantity > data.available) {
            stockWarningText.innerHTML = `
                <strong>Attention:</strong> La quantité demandée (${quantity}) 
                dépasse le stock disponible (${data.available})
            `;
            stockWarning.style.display = 'block';
        } else {
            stockWarning.style.display = 'none';
        }
        
    } catch (error) {
        console.error('Error checking stock:', error);
    }
}

// Update status summary
document.getElementById('validateImmediately').addEventListener('change', function() {
    const status = this.checked ? 'Validée' : 'Brouillon';
    document.getElementById('summaryStatus').textContent = status;
});

// Form validation
document.getElementById('exitForm').addEventListener('submit', function(e) {
    const quantity = parseFloat(document.getElementById('quantity').value);
    
    if (quantity <= 0) {
        e.preventDefault();
        alert('La quantité doit être supérieure à 0');
        return;
    }
});

// Event listeners
document.getElementById('quantity').addEventListener('input', function() {
    calculateTotal();
    checkStock();
});

document.getElementById('warehouseSelect').addEventListener('change', checkStock);

// Initialize
document.getElementById('exitType').dispatchEvent(new Event('change'));
</script>
@endsection
