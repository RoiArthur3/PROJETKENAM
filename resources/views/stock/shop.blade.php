@extends('layouts.app')

@section('title', 'Magasin - Vente de Matériel')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-store me-2"></i>
                        Magasin - Vente de Matériel
                    </h4>
                    <div>
                        <a href="{{ route('stock.shop.sales') }}" class="btn btn-light text-success me-2">
                            <i class="fas fa-history me-1"></i>
                            Historique
                        </a>
                        <a href="{{ route('stock.transfers.create') }}" class="btn btn-light text-success me-2">
                            <i class="fas fa-plus me-1"></i>
                            Approvisionner
                        </a>
                        <button class="btn btn-light text-success" id="cartBtn">
                            <i class="fas fa-shopping-cart me-1"></i>
                            Panier (<span id="cartCount">0</span>)
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if(isset($error))
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ $error }}
                        </div>
                    @endif
                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un produit...">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Toutes les catégories</option>
                                @foreach($productsByCategory as $category => $products)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="availableOnly">
                                <label class="form-check-label" for="availableOnly">
                                    Produits disponibles uniquement
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Produits par catégorie -->
                    @foreach($productsByCategory as $category => $categoryProducts)
                        <div class="category-section mb-5" data-category="{{ $category }}">
                            <h5 class="mb-3 text-success border-bottom pb-2">
                                <i class="fas fa-tag me-2"></i>
                                {{ $category }}
                            </h5>

                            <div class="row">
                                @foreach($categoryProducts as $product)
                                    <div class="col-md-3 mb-3 product-card" data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->unit_price }}">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-box fa-3x text-success mb-3"></i>
                                                <h6>{{ $product->name }}</h6>
                                                <p class="text-muted small">{{ $product->description ?? 'Aucune description' }}</p>

                                                @if($product->stock_levels_sum_current_stock > 0)
                                                    <div class="mb-2">
                                                        <small class="text-muted">Stock: {{ number_format($product->stock_levels_sum_current_stock, 0, ',', ' ') }}</small>
                                                    </div>
                                                @else
                                                    <div class="mb-2">
                                                        <small class="text-danger">Rupture de stock</small>
                                                    </div>
                                                @endif

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="h5 text-success mb-0">{{ number_format($product->unit_price, 0, ',', ' ') }} FCFA</span>
                                                    @if($product->stock_levels_sum_current_stock > 0)
                                                        <button class="btn btn-success btn-sm add-to-cart" data-product-id="{{ $product->id }}">
                                                            <i class="fas fa-cart-plus"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-secondary btn-sm" disabled>
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if($products->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun produit en magasin</h5>
                            <p class="text-muted">Les produits doivent être transférés depuis l'entrepôt vers le magasin.</p>
                            <a href="{{ route('stock.transfers.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i>
                                Transférer des produits
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de transfert -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Transférer des produits vers le magasin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transferForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="transferProduct" class="form-label">Produit</label>
                            <select class="form-select" id="transferProduct" name="product_id" required>
                                <option value="">Sélectionner un produit</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">
                                        {{ $product->name }} ({{ number_format($product->unit_price, 0, ',', ' ') }} FCFA)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="transferWarehouse" class="form-label">Entrepôt source</label>
                            <select class="form-select" id="transferWarehouse" name="warehouse_id" required>
                                <option value="">Sélectionner un entrepôt</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="transferQuantity" class="form-label">Quantité</label>
                            <input type="number" class="form-control" id="transferQuantity" name="quantity" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock disponible</label>
                            <p class="form-control-plaintext" id="availableStock">0</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="transferBtn">
                    <i class="fas fa-exchange-alt me-1"></i>
                    Transférer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal du panier -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Panier de vente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cartItems">
                    <!-- Les articles du panier seront ajoutés ici -->
                </div>
                <div class="text-end mt-3">
                    <h4>Total: <span id="cartTotal">0</span> FCFA</h4>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continuer les achats</button>
                <button type="button" class="btn btn-success" id="finalizeSaleBtn">
                    <i class="fas fa-cash-register me-1"></i>
                    Finaliser la vente
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let cart = [];

    // Filtrage des produits
    $('#searchInput').on('input', filterProducts);
    $('#categoryFilter').on('change', filterProducts);
    $('#availableOnly').on('change', filterProducts);

    function filterProducts() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const selectedCategory = $('#categoryFilter').val();
        const availableOnly = $('#availableOnly').is(':checked');

        $('.product-card').each(function() {
            const $card = $(this);
            const productName = $card.data('name').toLowerCase();
            const category = $card.closest('.category-section').data('category');
            const hasStock = $card.find('.text-danger').length === 0;

            let show = true;

            // Filtre par recherche
            if (searchTerm && !productName.includes(searchTerm)) {
                show = false;
            }

            // Filtre par catégorie
            if (selectedCategory && category !== selectedCategory) {
                show = false;
            }

            // Filtre par disponibilité
            if (availableOnly && !hasStock) {
                show = false;
            }

            $card.toggle(show);
        });

        // Masquer les catégories vides
        $('.category-section').each(function() {
            const $section = $(this);
            const visibleCards = $section.find('.product-card:visible').length;
            $section.toggle(visibleCards > 0);
        });
    }

    // Gestion du transfert
    $('#transferProduct').on('change', function() {
        const productId = $(this).val();
        if (productId) {
            loadWarehousesWithStock(productId);
        }
    });

    $('#transferWarehouse').on('change', function() {
        const productId = $('#transferProduct').val();
        const warehouseId = $(this).val();
        if (productId && warehouseId) {
            checkWarehouseStock(productId, warehouseId);
        }
    });

    $('#transferBtn').on('click', function() {
        const formData = new FormData(document.getElementById('transferForm'));

        $.ajax({
            url: '{{ route("stock.api.shop.transfer") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#transferModal').modal('hide');
                location.reload(); // Recharger la page pour voir les nouveaux stocks
            },
            error: function(xhr) {
                alert('Erreur: ' + xhr.responseJSON.message);
            }
        });
    });

    // Gestion du panier
    $('.add-to-cart').on('click', function() {
        const $btn = $(this);
        const productId = $btn.data('product-id');
        const $card = $btn.closest('.product-card');
        const productName = $card.data('name');
        const productPrice = parseFloat($card.data('price'));

        // Ajouter au panier
        const existingItem = cart.find(item => item.id === productId);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1
            });
        }

        updateCartCount();
        updateCartDisplay();
    });

    $('#cartBtn').on('click', function() {
        $('#cartModal').modal('show');
    });

    function loadWarehousesWithStock(productId) {
        $.get('{{ route("stock.api.shop.warehouses-with-stock") }}', { product_id: productId })
            .done(function(data) {
                const $select = $('#transferWarehouse');
                $select.empty().append('<option value="">Sélectionner un entrepôt</option>');

                data.forEach(function(warehouse) {
                    $select.append(`<option value="${warehouse.id}">${warehouse.name} (${warehouse.available_stock})</option>`);
                });
            });
    }

    function checkWarehouseStock(productId, warehouseId) {
        $.get('{{ route("stock.api.shop.check-warehouse-stock") }}', {
            product_id: productId,
            warehouse_id: warehouseId
        }).done(function(data) {
            $('#availableStock').text(data.available);
        });
    }

    function updateCartCount() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        $('#cartCount').text(totalItems);
    }

    function updateCartDisplay() {
        const $cartItems = $('#cartItems');
        $cartItems.empty();

        if (cart.length === 0) {
            $cartItems.html('<p class="text-center text-muted">Votre panier est vide</p>');
            $('#cartTotal').text('0');
            return;
        }

        let total = 0;
        cart.forEach(function(item) {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            $cartItems.append(`
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong>${item.name}</strong><br>
                        <small class="text-muted">${item.quantity} × ${item.price.toLocaleString()} FCFA</small>
                    </div>
                    <div class="text-end">
                        <strong>${itemTotal.toLocaleString()} FCFA</strong>
                        <button class="btn btn-sm btn-outline-danger ms-2 remove-from-cart" data-product-id="${item.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `);
        });

        $('#cartTotal').text(total.toLocaleString());
    }

    // Gestion de la suppression du panier
    $(document).on('click', '.remove-from-cart', function() {
        const productId = $(this).data('product-id');
        cart = cart.filter(item => item.id !== productId);
        updateCartCount();
        updateCartDisplay();
    });

    // Gestion de la finalisation de la vente
    $('#finalizeSaleBtn').on('click', function() {
        if (cart.length === 0) {
            alert('Le panier est vide');
            return;
        }

        // Demander les informations client
        const customerName = prompt('Nom du client (optionnel):');
        const customerPhone = prompt('Téléphone du client (optionnel):');
        const paymentMethod = prompt('Mode de paiement (cash/card/transfer/check):', 'cash');

        if (!paymentMethod || !['cash', 'card', 'transfer', 'check'].includes(paymentMethod)) {
            alert('Mode de paiement invalide');
            return;
        }

        // Préparer les données de vente
        const saleData = {
            customer_name: customerName || null,
            customer_phone: customerPhone || null,
            payment_method: paymentMethod,
            items: cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity
            }))
        };

        // Envoyer la requête
        $.ajax({
            url: '{{ route("stock.api.shop.finalize-sale") }}',
            method: 'POST',
            data: JSON.stringify(saleData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#cartModal').modal('hide');
                cart = [];
                updateCartCount();
                updateCartDisplay();

                alert('Vente finalisée avec succès! Référence: ' + response.sale.reference +
                      (response.invoice_number ? '\nFacture: ' + response.invoice_number : ''));

                // Recharger la page pour mettre à jour les stocks
                location.reload();
            },
            error: function(xhr) {
                alert('Erreur lors de la finalisation: ' + xhr.responseJSON.message);
            }
        });
    });
});
@endsection
