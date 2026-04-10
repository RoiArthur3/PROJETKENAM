@extends('layouts.app')

@section('title', 'Nouveau Bon de Commande - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-contract me-2 text-primary"></i>Nouveau Bon de Commande
            </h1>
            <p class="text-muted mb-0">Création d'un nouveau bon de commande</p>
        </div>
        <div>
            <a href="{{ route('commercial.bon-commande.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <form action="{{ route('commercial.bon-commande.store') }}" method="POST">
        @csrf
        <!-- Informations générales -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Informations générales
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Numéro du bon *</label>
                        <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $reference ?? 'BC-' . date('Ym') . '-001') }}" readonly>
                        @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date *</label>
                        <input type="date" name="date_commande" class="form-control @error('date_commande') is-invalid @enderror" value="{{ old('date_commande', date('Y-m-d')) }}" required>
                        @error('date_commande')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date de livraison prévue</label>
                        <input type="date" name="date_livraison_prevue" class="form-control @error('date_livraison_prevue') is-invalid @enderror" value="{{ old('date_livraison_prevue') }}">
                        @error('date_livraison_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <label class="form-label">Nombre de jours à travailler *</label>
                        <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" min="1" value="{{ old('duration_days', 1) }}" required>
                        @error('duration_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Client *</label>
                        <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ (string) old('client_id') === (string) $client->id ? 'selected' : '' }}>
                                    {{ $client->raison_sociale ?? $client->nom ?? $client->company_name ?? ('Client #' . $client->id) }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Statut *</label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                            <option value="en_attente" {{ old('statut', 'en_attente') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="valide" {{ old('statut') === 'valide' ? 'selected' : '' }}>Validé</option>
                            <option value="livre" {{ old('statut') === 'livre' ? 'selected' : '' }}>Livré</option>
                            <option value="annule" {{ old('statut') === 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-list me-2"></i>Articles
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addArticle()">
                    <i class="fas fa-plus me-1"></i>Ajouter un article
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="articlesTable">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" placeholder="Désignation">
                                </td>
                                <td>
                                    <input type="number" class="form-control" min="1" value="1" onchange="calculateTotal()">
                                </td>
                                <td>
                                    <input type="number" class="form-control" min="0" step="100" onchange="calculateTotal()">
                                </td>
                                <td>
                                    <input type="text" class="form-control" readonly value="0 FCFA">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeArticle(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totaux -->
                <div class="row mt-3">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total HT:</span>
                                    <strong id="totalHT">0 FCFA</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>TVA (18%):</span>
                                    <strong id="totalTVA">0 FCFA</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5>Total TTC:</h5>
                                    <h5 class="text-success" id="totalTTC">0 FCFA</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-sticky-note me-2"></i>Notes et conditions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Notes supplémentaires...">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label">Conditions de paiement</label>
                        <select class="form-select">
                            <option value="comptant">Comptant</option>
                            <option value="30jours">30 jours</option>
                            <option value="60jours">60 jours</option>
                            <option value="90jours">90 jours</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-save me-2"></i>Enregistrer brouillon
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('commercial.bon-commande.index') }}" class="btn btn-outline-danger me-2">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>Créer le bon de commande
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="montant_total" id="montant_total_input" value="0">
    </form>
</div>

<script>
function addArticle() {
    const tbody = document.querySelector('#articlesTable tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <input type="text" class="form-control" placeholder="Désignation">
        </td>
        <td>
            <input type="number" class="form-control" min="1" value="1" onchange="calculateTotal()">
        </td>
        <td>
            <input type="number" class="form-control" min="0" step="100" onchange="calculateTotal()">
        </td>
        <td>
            <input type="text" class="form-control" readonly value="0 FCFA">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeArticle(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
}

function removeArticle(button) {
    const row = button.closest('tr');
    row.remove();
    calculateTotal();
}

function calculateTotal() {
    let totalHT = 0;
    const rows = document.querySelectorAll('#articlesTable tbody tr');

    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('td:nth-child(2) input').value) || 0;
        const price = parseFloat(row.querySelector('td:nth-child(3) input').value) || 0;
        const rowTotal = quantity * price;

        row.querySelector('td:nth-child(4) input').value = rowTotal.toLocaleString('fr-FR') + ' FCFA';
        totalHT += rowTotal;
    });

    const totalTVA = totalHT * 0.18;
    const totalTTC = totalHT + totalTVA;

    document.getElementById('totalHT').textContent = totalHT.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('totalTVA').textContent = totalTVA.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('totalTTC').textContent = totalTTC.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('montant_total_input').value = totalTTC;
}

calculateTotal();
</script>

@endsection
