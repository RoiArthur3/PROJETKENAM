@extends('layouts.app')

@section('title', 'Détails de la Facture - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Détails de la Facture #{{ $invoice->invoice_number }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informations Facture -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de la Facture</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>N° Facture :</strong> {{ $invoice->invoice_number }}</p>
                            <p><strong>Type :</strong> {{ ucfirst($invoice->type) }}</p>
                            <p><strong>Date d'Émission :</strong> {{ $invoice->issue_date->format('d/m/Y') }}</p>
                            <p><strong>Date d'Échéance :</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Client/Fournisseur :</strong> {{ $invoice->client->name ?? $invoice->supplier_name }}</p>
                            <p><strong>Montant Net :</strong> {{ number_format($invoice->net_amount, 0, ',', ' ') }} FCFA</p>
                            <p><strong>Montant Payé :</strong> {{ number_format($invoice->total_paid, 0, ',', ' ') }} FCFA</p>
                            <p><strong>Restant :</strong> {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paiements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des Paiements</h6>
                    @if($invoice->remaining_amount > 0)
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-plus"></i> Ajouter un Paiement
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($invoice->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant Payé</th>
                                        <th>Méthode</th>
                                        <th>Référence</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                            <td>{{ number_format($payment->amount_paid, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td>{{ $payment->reference ?? '-' }}</td>
                                            <td>{{ $payment->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Aucun paiement enregistré pour cette facture.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Barre de Progression -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">État du Paiement</h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $invoice->payment_percentage }}%"
                             aria-valuenow="{{ $invoice->payment_percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($invoice->payment_percentage, 1) }}%
                        </div>
                    </div>
                    <p><strong>Pourcentage Payé :</strong> {{ number_format($invoice->payment_percentage, 1) }}%</p>
                    @if($invoice->remaining_amount > 0)
                        <p class="text-warning"><strong>Restant à Payer :</strong> {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</p>
                    @else
                        <p class="text-success"><strong>Facture Complètement Payée</strong></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour Ajouter un Paiement -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Ajouter un Paiement Partiel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Montant du Paiement *</label>
                        <input type="number" class="form-control" id="amount_paid" required min="0.01" max="{{ $invoice->remaining_amount }}" step="0.01" placeholder="Ex: 25000">
                        <div class="form-text">Montant maximum : {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Méthode de Paiement *</label>
                        <select class="form-select" id="payment_method" required>
                            <option value="cash">Espèces</option>
                            <option value="bank_transfer">Virement Bancaire</option>
                            <option value="check">Chèque</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Date du Paiement *</label>
                        <input type="date" class="form-control" id="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="reference" class="form-label">Référence Bancaire</label>
                        <input type="text" class="form-control" id="reference" placeholder="Ex: REF-2023-001">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" rows="3" placeholder="Informations supplémentaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer le Paiement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Soumission du formulaire de paiement
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const amount = document.getElementById('amount_paid').value;
    const method = document.getElementById('payment_method').value;
    const date = document.getElementById('payment_date').value;

    if (!amount || !method || !date) {
        alert('Veuillez remplir tous les champs obligatoires.');
        return;
    }

    // Simulation d'enregistrement
    alert('Paiement enregistré avec succès !');
    location.reload(); // Recharger la page pour voir les changements
});
</script>
@endsection
