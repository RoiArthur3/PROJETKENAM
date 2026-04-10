@extends('layouts.app')

@section('title', 'Historique des Ventes - Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Historique des Ventes
                    </h4>
                    <a href="{{ route('stock.shop') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour au magasin
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Date début</label>
                            <input type="date" class="form-control" id="dateFrom" name="date_from">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date fin</label>
                            <input type="date" class="form-control" id="dateTo" name="date_to">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut paiement</label>
                            <select class="form-select" id="paymentStatus">
                                <option value="">Tous</option>
                                <option value="pending">En attente</option>
                                <option value="paid">Payé</option>
                                <option value="partial">Partiel</option>
                                <option value="cancelled">Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-success w-100" id="filterBtn">
                                <i class="fas fa-filter me-1"></i>
                                Filtrer
                            </button>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $sales->total() }}</h5>
                                    <small>Total ventes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>{{ number_format($sales->sum('net_amount'), 0, ',', ' ') }} FCFA</h5>
                                    <small>Chiffre d'affaires</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $sales->where('payment_status', 'paid')->count() }}</h5>
                                    <small>Ventes payées</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $sales->where('payment_status', 'pending')->count() }}</h5>
                                    <small>En attente</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des ventes -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Montant HT</th>
                                    <th>TVA</th>
                                    <th>Total TTC</th>
                                    <th>Facture</th>
                                    <th>Paiement</th>
                                    <th>Statut</th>
                                    <th>Vendeur</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                <tr>
                                    <td>
                                        <strong>{{ $sale->reference }}</strong>
                                    </td>
                                    <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($sale->customer_name)
                                            {{ $sale->customer_name }}
                                            @if($sale->customer_phone)
                                                <br><small class="text-muted">{{ $sale->customer_phone }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Client anonyme</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($sale->tax_amount, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <strong>{{ number_format($sale->net_amount, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                    <td>
                                        @if($sale->invoice)
                                            <span class="badge bg-info">{{ $sale->invoice->invoice_number }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($sale->payment_method) }}</span>
                                    </td>
                                    <td>
                                        @switch($sale->payment_status)
                                            @case('pending')
                                                <span class="badge bg-warning">En attente</span>
                                                @break
                                            @case('paid')
                                                <span class="badge bg-success">Payé</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-info">Partiel</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Annulé</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="showSaleDetails({{ $sale->id }})" title="Détails rapide">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('stock.shop.sales.edit', $sale) }}" class="btn btn-sm btn-outline-warning" title="Éditer la vente">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($sale->invoice)
                                            <button type="button" class="btn btn-sm btn-outline-success" title="Voir la facture" onclick="alert('Facture: {{ $sale->invoice->invoice_number }}')">
                                                <i class="fas fa-file-invoice"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-success" onclick="printReceipt({{ $sale->id }})" title="Imprimer le reçu">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Aucune vente trouvée</h5>
                                        <p class="text-muted">Les ventes apparaîtront ici une fois que vous aurez commencé à vendre des produits.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de détails de vente -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt me-2"></i>
                    Détails de la vente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="saleDetailsContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Filtrage
    $('#filterBtn').on('click', function() {
        const params = new URLSearchParams();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();
        const paymentStatus = $('#paymentStatus').val();

        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        if (paymentStatus) params.append('payment_status', paymentStatus);

        window.location.href = '{{ route("stock.shop.sales") }}?' + params.toString();
    });

    // Réinitialiser les filtres
    $('#dateFrom, #dateTo, #paymentStatus').on('change', function() {
        // Optionnel: filtrage en temps réel
    });
});

function showSaleDetails(saleId) {
    $.get('{{ route("stock.shop.sales.show", ":id") }}'.replace(':id', saleId))
        .done(function(data) {
            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Référence:</strong> ${data.reference}<br>
                        <strong>Date:</strong> ${new Date(data.sale_date).toLocaleString('fr-FR')}<br>
                        <strong>Vendeur:</strong> ${data.user?.name || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Client:</strong> ${data.customer_name || 'Anonyme'}<br>
                        <strong>Téléphone:</strong> ${data.customer_phone || 'N/A'}<br>
                        <strong>Paiement:</strong> ${data.payment_method} - <span class="badge bg-${getStatusColor(data.payment_status)}">${getStatusText(data.payment_status)}</span>
                    </div>
                </div>

                <h6>Articles vendus:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>`;

            data.items.forEach(item => {
                html += `
                    <tr>
                        <td>${item.product.name}</td>
                        <td>${item.quantity}</td>
                        <td>${Number(item.unit_price).toLocaleString()} FCFA</td>
                        <td>${Number(item.total_amount).toLocaleString()} FCFA</td>
                    </tr>`;
            });

            html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Sous-total HT</th>
                                <th>${Number(data.total_amount).toLocaleString()} FCFA</th>
                            </tr>
                            <tr>
                                <th colspan="3">TVA (18%)</th>
                                <th>${Number(data.tax_amount).toLocaleString()} FCFA</th>
                            </tr>
                            <tr>
                                <th colspan="3">Total TTC</th>
                                <th><strong>${Number(data.net_amount).toLocaleString()} FCFA</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>`;

            $('#saleDetailsContent').html(html);
            $('#saleDetailsModal').modal('show');
        })
        .fail(function() {
            alert('Erreur lors du chargement des détails');
        });
}

function printReceipt(saleId) {
    const baseUrl = '{{ url('/stock/shop/sales') }}';
    const url = baseUrl + '/' + saleId + '/receipt';
    window.open(url, '_blank');
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'paid': 'success',
        'partial': 'info',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusText(status) {
    const texts = {
        'pending': 'En attente',
        'paid': 'Payé',
        'partial': 'Partiel',
        'cancelled': 'Annulé'
    };
    return texts[status] || status;
}
</script>
@endsection
