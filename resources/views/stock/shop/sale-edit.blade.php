@extends('layouts.app')

@section('title', 'Éditer une vente magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8 offset-lg-2">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Éditer la vente {{ $sale->reference }}
                    </h5>
                    <a href="{{ route('stock.shop.sales') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à l'historique
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('stock.shop.sales.update', $sale) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom du client</label>
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $sale->customer_name) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $sale->customer_phone) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $sale->customer_email) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Mode de paiement</label>
                                <select name="payment_method" class="form-select" required>
                                    @foreach(['cash' => 'Espèces', 'card' => 'Carte', 'transfer' => 'Virement', 'check' => 'Chèque'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('payment_method', $sale->payment_method) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut paiement</label>
                                <select name="payment_status" class="form-select" required>
                                    @foreach(['pending' => 'En attente', 'paid' => 'Payé', 'partial' => 'Partiel', 'cancelled' => 'Annulé'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('payment_status', $sale->payment_status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Remise (FCFA)</label>
                                <input type="number" step="0.01" name="discount_amount" class="form-control" value="{{ old('discount_amount', $sale->discount_amount) }}" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Résumé de la vente</label>
                            <ul class="list-group">
                                @foreach($sale->items as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->product->name ?? 'Produit' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</small>
                                        </div>
                                        <span class="fw-bold">{{ number_format($item->total_amount, 0, ',', ' ') }} FCFA</span>
                                    </li>
                                @endforeach
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Sous-total HT</span>
                                    <span class="fw-bold">{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>TVA</span>
                                    <span class="fw-bold">{{ number_format($sale->tax_amount, 0, ',', ' ') }} FCFA</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total TTC</span>
                                    <span class="fw-bold text-success">{{ number_format($sale->net_amount, 0, ',', ' ') }} FCFA</span>
                                </li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer les modifications
                            </button>

                            <button type="submit" form="sendToAccountingForm" class="btn btn-success">
                                <i class="fas fa-paper-plane me-1"></i> Envoyer à la comptabilité
                            </button>
                        </div>
                    </form>

                    <form id="sendToAccountingForm" method="POST" action="{{ route('stock.shop.sales.send-to-accounting', $sale) }}">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
