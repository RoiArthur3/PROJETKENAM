@extends('layouts.app')

@section('title', 'Reçu de Vente - Magasin')

@section('content')
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card" id="receipt-card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h5 class="mb-0">MAGASIN</h5>
                        <small class="text-muted">Reçu de vente</small>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>Réf. vente :</span>
                            <strong>{{ $sale->reference }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Date :</span>
                            <span>{{ $sale->sale_date ? $sale->sale_date->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Vendu par :</span>
                            <span>{{ $sale->user->name ?? 'N/A' }}</span>
                        </div>
                        @if($sale->customer_name)
                        <div class="d-flex justify-content-between">
                            <span>Client :</span>
                            <span>{{ $sale->customer_name }}</span>
                        </div>
                        @endif
                    </div>

                    <hr class="my-2">

                    <div class="mb-2">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Art.</th>
                                    <th class="text-end">Qté</th>
                                    <th class="text-end">P.U</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr>
                                    <td>{{ \\Illuminate\\Support\\Str::limit($item->product->name ?? 'Article', 12) }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($item->total_amount, 0, ',', ' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-2">

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>Sous-total</span>
                            <span>{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>TVA</span>
                            <span>{{ number_format($sale->tax_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if(($sale->discount_amount ?? 0) > 0)
                        <div class="d-flex justify-content-between">
                            <span>Remise</span>
                            <span>- {{ number_format($sale->discount_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <strong>Total TTC</strong>
                            <strong>{{ number_format($sale->net_amount, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>Mode paiement</span>
                            <span>{{ ucfirst($sale->payment_method) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Statut</span>
                            <span>{{ ucfirst($sale->payment_status) }}</span>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-muted">Merci pour votre achat</small>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3 no-print">
                <a href="{{ route('stock.shop.sales') }}" class="btn btn-outline-secondary btn-sm">Retour</a>
                <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimer</button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #receipt-card, #receipt-card * {
        visibility: visible;
    }
    #receipt-card {
        margin: 0;
        box-shadow: none;
        border: none;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
