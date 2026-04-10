@extends('layouts.app')

@section('title', 'Nouvelle Vente - Magasin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-cash-register text-success me-2"></i>
                Nouvelle Vente
            </h4>
            <small class="text-muted">Créer une nouvelle vente et générer une facture.</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('shop') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Retour au magasin
            </a>
        </div>
    </div>

    <form action="{{ route('shop.store') }}" method="POST" id="saleForm">
        @csrf

        <div class="row">
            <!-- Informations client -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-user text-primary me-2"></i>
                            Informations Client
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" name="customer_phone">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="customer_email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control" name="customer_address" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produits à vendre -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-boxes text-success me-2"></i>
                            Produits à Vendre
                        </h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="addProduct()">
                            <i class="fas fa-plus me-1"></i>
                            Ajouter Produit
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="productsContainer">
                            <!-- Lignes de produits seront ajoutées ici -->
                        </div>

                        <!-- Total -->
                        <div class="row mt-4">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <strong>Total HT:</strong>
                                            <span id="totalHT">0 FCFA</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <strong>TVA (18%):</strong>
                                            <span id="totalTVA">0 FCFA</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total TTC:</strong>
                                            <span id="totalTTC">0 FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mode de Paiement</label>
                                    <select class="form-select" name="payment_method">
                                        <option value="cash">Espèces</option>
                                        <option value="card">Carte bancaire</option>
                                        <option value="transfer">Virement</option>
                                        <option value="check">Chèque</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date de Vente</label>
                                    <input type="datetime-local" class="form-control" name="sale_date"
                                           value="{{ now()->format('Y-m-d\TH:i') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                    <i class="fas fa-redo me-2"></i>
                                    Réinitialiser
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-info" onclick="saveAsDraft()">
                                    <i class="fas fa-save me-2"></i>
                                    Sauvegarder Brouillon
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>
                                    Valider la Vente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Template pour une ligne de produit
const productRowTemplate = `
    <div class="product-row border rounded p-3 mb-3 bg-light">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Produit</label>
                <select class="form-select product-select" name="products[]" required>
                    <option value="">Sélectionner un produit</option>
                    <!-- Options de produits seront chargées -->
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantité</label>
                <input type="number" class="form-control quantity-input" name="quantities[]" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Prix Unit. HT</label>
                <input type="number" class="form-control price-input" name="prices[]" step="0.01" min="0" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label">Total HT</label>
                <input type="number" class="form-control total-input" step="0.01" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-product">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
`;

// Ajouter une ligne de produit
function addProduct() {
    const container = document.getElementById('productsContainer');
    const row = document.createElement('div');
    row.innerHTML = productRowTemplate;
    container.appendChild(row);

    // Charger les options de produits (simulation)
    loadProductOptions(row.querySelector('.product-select'));

    // Événements
    setupRowEvents(row);
}

// Charger les options de produits (données statiques pour l'exemple)
function loadProductOptions(select) {
    const products = [
        {id: 1, name: 'Produit A', price: 5000},
        {id: 2, name: 'Produit B', price: 7500},
        {id: 3, name: 'Produit C', price: 3200},
    ];

    select.innerHTML = '<option value="">Sélectionner un produit</option>';
    products.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = `${product.name} - ${product.price} FCFA`;
        option.dataset.price = product.price;
        select.appendChild(option);
    });
}

// Configurer les événements pour une ligne
function setupRowEvents(row) {
    const select = row.querySelector('.product-select');
    const quantity = row.querySelector('.quantity-input');
    const price = row.querySelector('.price-input');
    const total = row.querySelector('.total-input');
    const removeBtn = row.querySelector('.remove-product');

    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        price.value = selectedOption.dataset.price || 0;
        calculateRowTotal(row);
    });

    quantity.addEventListener('input', () => calculateRowTotal(row));

    removeBtn.addEventListener('click', () => {
        row.remove();
        calculateTotals();
    });
}

// Calculer le total d'une ligne
function calculateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const total = quantity * price;

    row.querySelector('.total-input').value = total.toFixed(2);
    calculateTotals();
}

// Calculer les totaux généraux
function calculateTotals() {
    const rows = document.querySelectorAll('.product-row');
    let totalHT = 0;

    rows.forEach(row => {
        const rowTotal = parseFloat(row.querySelector('.total-input').value) || 0;
        totalHT += rowTotal;
    });

    const tva = totalHT * 0.18;
    const totalTTC = totalHT + tva;

    document.getElementById('totalHT').textContent = totalHT.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('totalTVA').textContent = tva.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('totalTTC').textContent = totalTTC.toLocaleString('fr-FR') + ' FCFA';
}

// Réinitialiser le formulaire
function clearForm() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')) {
        document.getElementById('saleForm').reset();
        document.getElementById('productsContainer').innerHTML = '';
        calculateTotals();
    }
}

// Sauvegarder comme brouillon
function saveAsDraft() {
    alert('Fonctionnalité de sauvegarde en brouillon à implémenter');
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter une première ligne de produit
    addProduct();
});
</script>
@endsection
