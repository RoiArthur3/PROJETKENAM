@extends('layouts.app')

@section('title', 'Créer une Facture - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Créer une Nouvelle Facture</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Détails de la Facture</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('invoicing.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type de Facture</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="client">Client</option>
                                    <option value="supplier">Fournisseur</option>
                                    <option value="internal">Interne</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="invoice_number" class="form-label">N° Facture</label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="FAC-2025-003" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label">Client (si applicable)</label>
                                <select class="form-select" id="client_id" name="client_id">
                                    <option value="">Sélectionner un client</option>
                                    <!-- Liste des utilisateurs/clients -->
                                    <option value="1">Entreprise XYZ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="supplier_name" class="form-label">Fournisseur (si externe)</label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="Nom du fournisseur">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="operation_reference" class="form-label">Référence Opération (optionnel)</label>
                            <input type="text" class="form-control" id="operation_reference" name="operation_reference" placeholder="Ex: OP-2025-001">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description de la Prestation</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label">Quantité</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="1" step="0.01" oninput="calculateTotal()">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unit_price" class="form-label">Prix Unitaire (FCFA)</label>
                                <input type="number" class="form-control" id="unit_price" name="unit_price" step="0.01" oninput="calculateTotal()">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="total_amount" class="form-label">Montant Total</label>
                                <input type="number" class="form-control" id="total_amount" name="total_amount" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tax_rate" class="form-label">TVA (%)</label>
                                <input type="number" class="form-control" id="tax_rate" name="tax_rate" value="18" step="0.01" oninput="calculateTotal()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discount" class="form-label">Remise (FCFA)</label>
                                <input type="number" class="form-control" id="discount" name="discount" value="0" step="0.01" oninput="calculateTotal()">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Mode de Paiement</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="bank_transfer">Virement Bancaire</option>
                                    <option value="cash">Espèces</option>
                                    <option value="check">Chèque</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Date d'Échéance</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optionnel)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('invoicing.index') }}" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Créer la Facture</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;

    const total = quantity * unitPrice;
    const taxAmount = (total - discount) * (taxRate / 100);
    const netAmount = total - discount + taxAmount;

    document.getElementById('total_amount').value = total.toFixed(2);
    document.getElementById('tax_amount').value = taxAmount.toFixed(2);
    document.getElementById('net_amount').value = netAmount.toFixed(2);
}
</script>
@endsection
