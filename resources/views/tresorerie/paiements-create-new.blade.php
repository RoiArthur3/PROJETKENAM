@extends('layouts.app')

@section('title', 'Nouveau Paiement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouveau Paiement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.paiements.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Type de paiement -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Type de paiement *</label>
                                <div class="btn-group" role="group" data-toggle="buttons">
                                    <input type="radio" class="btn-check" name="payment_type" id="type_invoice" value="invoice" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary" for="type_invoice">
                                        <i class="fas fa-file-invoice me-2"></i>Facture
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="payment_type" id="type_operation" value="operation" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="type_operation">
                                        <i class="fas fa-cogs me-2"></i>Opération
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="payment_type" id="type_both" value="both" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="type_both">
                                        <i class="fas fa-link me-2"></i>Les deux
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Section Facture -->
                        <div id="invoice_section" class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-file-invoice me-2"></i>Informations de la facture
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="invoice_id" class="form-label">Facture</label>
                                    <select class="form-select" id="invoice_id" name="invoice_id">
                                        <option value="">Sélectionner une facture</option>
                                        <!-- Les factures seront chargées dynamiquement -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="invoice_reference" class="form-label">Référence facture</label>
                                    <input type="text" class="form-control" id="invoice_reference" name="invoice_reference" placeholder="Numéro de facture">
                                </div>
                            </div>
                        </div>

                        <!-- Section Opération -->
                        <div id="operation_section" class="mb-4" style="display: none;">
                            <h5 class="text-success mb-3">
                                <i class="fas fa-cogs me-2"></i>Opération associée
                            </h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="operation_id" class="form-label">Opération *</label>
                                    <select class="form-select" id="operation_id" name="operation_id">
                                        <option value="">Sélectionner une opération</option>
                                        @foreach($operations as $operation)
                                        <option value="{{ $operation->id }}" 
                                                data-montant="{{ $operation->montant }}"
                                                data-demandeur="{{ $operation->demandeur_name }}"
                                                data-description="{{ $operation->description }}">
                                            OP-{{ $operation->id }} - {{ $operation->demandeur_name }} - {{ number_format($operation->montant, 0, ',', ' ') }} FCFA
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Détails de l'opération</label>
                                    <div id="operation_details" class="alert alert-info" style="display: none;">
                                        <small class="d-block">
                                            <strong>Demandeur:</strong> <span id="op_demandeur"></span><br>
                                            <strong>Montant:</strong> <span id="op_montant"></span> FCFA<br>
                                            <strong>Description:</strong> <span id="op_description"></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations générales du paiement -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence du paiement *</label>
                                <input type="text" class="form-control" id="reference" name="reference" required 
                                       placeholder="PAY-2024-XXX" value="PAY-{{ date('Y') }}-{{ str_pad(date('z') + 1, 3, '0', STR_PAD_LEFT) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_date" class="form-label">Date de paiement *</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" required 
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="amount_paid" class="form-label">Montant payé (FCFA) *</label>
                                <input type="number" class="form-control" id="amount_paid" name="amount_paid" required 
                                       placeholder="0" min="0" step="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Méthode de paiement *</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Sélectionner une méthode</option>
                                    <option value="bank_transfer">Virement bancaire</option>
                                    <option value="cash">Espèces</option>
                                    <option value="check">Chèque</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Statut du paiement *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending">En attente</option>
                                    <option value="processing">En cours</option>
                                    <option value="completed" selected>Complété</option>
                                    <option value="failed">Échoué</option>
                                    <option value="cancelled">Annulé</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="attachment" class="form-label">Pièce jointe</label>
                                <input type="file" class="form-control" id="attachment" name="attachment" 
                                       accept="application/pdf,image/jpeg,image/jpg,image/png">
                                <small class="text-muted">PDF, JPG, PNG (max 2MB)</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Notes ou commentaires sur le paiement..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Créer le paiement
                                </button>
                                <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-secondary btn-lg ms-2">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer l'affichage des sections selon le type de paiement
    const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
    const invoiceSection = document.getElementById('invoice_section');
    const operationSection = document.getElementById('operation_section');
    
    paymentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'invoice') {
                invoiceSection.style.display = 'block';
                operationSection.style.display = 'none';
                document.getElementById('operation_id').required = false;
                document.getElementById('invoice_id').required = true;
            } else if (this.value === 'operation') {
                invoiceSection.style.display = 'none';
                operationSection.style.display = 'block';
                document.getElementById('operation_id').required = true;
                document.getElementById('invoice_id').required = false;
            } else if (this.value === 'both') {
                invoiceSection.style.display = 'block';
                operationSection.style.display = 'block';
                document.getElementById('operation_id').required = true;
                document.getElementById('invoice_id').required = true;
            }
        });
    });
    
    // Afficher les détails de l'opération sélectionnée
    const operationSelect = document.getElementById('operation_id');
    const operationDetails = document.getElementById('operation_details');
    const amountPaidInput = document.getElementById('amount_paid');
    
    operationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            document.getElementById('op_demandeur').textContent = selectedOption.dataset.demandeur || '';
            document.getElementById('op_montant').textContent = selectedOption.dataset.montant || '';
            document.getElementById('op_description').textContent = selectedOption.dataset.description || '';
            operationDetails.style.display = 'block';
            
            // Pré-remplir le montant avec celui de l'opération
            const operationAmount = parseFloat(selectedOption.dataset.montant) || 0;
            if (operationAmount > 0 && !amountPaidInput.value) {
                amountPaidInput.value = operationAmount;
            }
        } else {
            operationDetails.style.display = 'none';
        }
    });
});
</script>
@endsection
