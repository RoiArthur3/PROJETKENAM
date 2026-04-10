@extends('layouts.app')

@section('title', 'Modifier la Proforma')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Modifier la Proforma {{ $proforma->reference }}
                    </h4>
                </div>

                <form id="proforma-form" action="{{ route('proformas.update', $proforma->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <!-- Informations générales -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-warning mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Informations générales
                                </h5>

                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                    <select name="client_id" id="client_id" class="form-select" required>
                                        <option value="">Sélectionner un client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ $proforma->client_id == $client->id ? 'selected' : '' }}>
                                                {{ $client->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="projet_id" class="form-label">Projet (optionnel)</label>
                                    <select name="projet_id" id="projet_id" class="form-select">
                                        <option value="">Sélectionner un projet</option>
                                        @foreach($projets as $projet)
                                            <option value="{{ $projet->id }}" {{ $proforma->projet_id == $projet->id ? 'selected' : '' }}>
                                                {{ $projet->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_proposition" class="form-label">Date de proposition <span class="text-danger">*</span></label>
                                            <input type="date" name="date_proposition" id="date_proposition" class="form-control" value="{{ $proforma->date_proposition->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_validite" class="form-label">Date de validité <span class="text-danger">*</span></label>
                                            <input type="date" name="date_validite" id="date_validite" class="form-control" value="{{ $proforma->date_validite->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="conditions_generales" class="form-label">Conditions générales</label>
                                    <textarea name="conditions_generales" id="conditions_generales" class="form-control" rows="3">{{ $proforma->conditions_generales }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="note" class="form-label">Notes</label>
                                    <textarea name="note" id="note" class="form-control" rows="2">{{ $proforma->note }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-warning mb-3">
                                    <i class="fas fa-list me-2"></i>
                                    Articles de la proforma
                                </h5>

                                <div id="items-container">
                                    <!-- Les lignes d'articles existants seront chargées ici -->
                                    @foreach($proforma->items as $index => $item)
                                    <div class="item-row border rounded p-3 mb-3 bg-white">
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label class="form-label">Désignation <span class="text-danger">*</span></label>
                                                <input type="text" name="items[{{ $index }}][designation]" class="form-control designation" value="{{ $item->designation }}" placeholder="Description de l'article" required>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <label class="form-label">Prix unit. HT <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="items[{{ $index }}][prix_unitaire_ht]" class="form-control prix-unitaire" step="0.01" min="0" value="{{ $item->prix_unitaire_ht }}" placeholder="0.00" required>
                                                    <span class="input-group-text">FCFA</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="items[{{ $index }}][quantite]" class="form-control quantite" step="0.01" min="0.01" value="{{ $item->quantite }}" required>
                                                    <input type="text" name="items[{{ $index }}][unite]" class="form-control unite" value="{{ $item->unite }}" placeholder="unité" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Total HT</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control total-ht-item" value="{{ number_format($item->total_ht, 2, ',', ' ') }} FCFA" readonly>
                                                    <button type="button" class="btn btn-danger remove-item">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">TVA <span class="text-danger">*</span></label>
                                                <div class="tva-radio-group">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input tva-radio" type="radio" name="items[{{ $index }}][tva]" id="tva-0-{{ $index }}" value="0" {{ $item->tva == 0 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tva-0-{{ $index }}">Exonéré (0%)</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input tva-radio" type="radio" name="items[{{ $index }}][tva]" id="tva-18-{{ $index }}" value="18" {{ $item->tva == 18 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tva-18-{{ $index }}">TVA Côte d'Ivoire (18%)</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input tva-radio" type="radio" name="items[{{ $index }}][tva]" id="tva-19-{{ $index }}" value="19" {{ $item->tva == 19 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tva-19-{{ $index }}">TVA Standard (19%)</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input tva-radio" type="radio" name="items[{{ $index }}][tva]" id="tva-20-{{ $index }}" value="20" {{ $item->tva == 20 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="tva-20-{{ $index }}">TVA Réduite (20%)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="button" id="add-item-btn" class="btn btn-outline-warning btn-sm mb-3">
                                    <i class="fas fa-plus me-1"></i>
                                    Ajouter une ligne
                                </button>

                                <!-- Totaux -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Total HT :</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="total-ht">{{ number_format($proforma->total_ht, 2, ',', ' ') }} FCFA</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Total TVA :</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="total-tva">{{ number_format($proforma->total_tva, 2, ',', ' ') }} FCFA</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Total TTC :</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <strong id="total-ttc">{{ number_format($proforma->total_ttc, 2, ',', ' ') }} FCFA</strong>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <small class="text-muted">Montant en lettres : <span id="montant-lettres">{{ $proforma->montant_lettres }}</span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('proformas.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Annuler
                                    </a>
                                    <div>
                                        <button type="submit" name="save_as_draft" value="1" class="btn btn-warning me-2">
                                            <i class="fas fa-save me-1"></i>
                                            Enregistrer en brouillon
                                        </button>
                                        <a href="{{ route('proformas.pdf', $proforma->id) }}" target="_blank" class="btn btn-info me-2">
                                            <i class="fas fa-eye me-1"></i>
                                            Aperçu PDF
                                        </a>
                                        <button type="submit" name="generate_pdf" value="1" class="btn btn-success me-2">
                                            <i class="fas fa-file-pdf me-1"></i>
                                            Valider et Générer PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Template pour une ligne d'article
const itemTemplate = `
    <div class="item-row border rounded p-3 mb-3 bg-white">
        <div class="row mb-2">
            <div class="col-md-12">
                <label class="form-label">Désignation <span class="text-danger">*</span></label>
                <input type="text" name="items[{index}][designation]" class="form-control designation" placeholder="Description de l'article" required>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4">
                <label class="form-label">Prix unit. HT <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="items[{index}][prix_unitaire_ht]" class="form-control prix-unitaire" step="0.01" min="0" placeholder="0.00" required>
                    <span class="input-group-text">FCFA</span>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="items[{index}][quantite]" class="form-control quantite" step="0.01" min="0.01" value="1" required>
                    <input type="text" name="items[{index}][unite]" class="form-control unite" value="pièce" placeholder="unité" required>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Total HT</label>
                <div class="input-group">
                    <input type="text" class="form-control total-ht-item" readonly>
                    <button type="button" class="btn btn-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="form-label">TVA <span class="text-danger">*</span></label>
                <div class="tva-radio-group">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input tva-radio" type="radio" name="items[{index}][tva]" id="tva-0-{index}" value="0">
                        <label class="form-check-label" for="tva-0-{index}">Exonéré (0%)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input tva-radio" type="radio" name="items[{index}][tva]" id="tva-18-{index}" value="18" checked>
                        <label class="form-check-label" for="tva-18-{index}">TVA Côte d'Ivoire (18%)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input tva-radio" type="radio" name="items[{index}][tva]" id="tva-19-{index}" value="19">
                        <label class="form-check-label" for="tva-19-{index}">TVA Standard (19%)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input tva-radio" type="radio" name="items[{index}][tva]" id="tva-20-{index}" value="20">
                        <label class="form-check-label" for="tva-20-{index}">TVA Réduite (20%)</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
`;

let itemIndex = {{ $proforma->items->count() }};

// Ajouter une ligne d'article
function addItem() {
    const container = document.getElementById('items-container');
    const newItem = itemTemplate.replace(/{index}/g, itemIndex);
    container.insertAdjacentHTML('beforeend', newItem);
    itemIndex++;
    attachItemEvents();
    calculateTotals();
}

// Supprimer une ligne d'article
function removeItem(button) {
    button.closest('.item-row').remove();
    calculateTotals();
}

// Attacher les événements aux éléments
function attachItemEvents() {
    document.querySelectorAll('.prix-unitaire, .quantite, .tva-radio').forEach(element => {
        element.addEventListener('input', calculateTotals);
        element.addEventListener('change', calculateTotals);
    });

    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            removeItem(this);
        });
    });
}

// Calculer les totaux
function calculateTotals() {
    let totalHT = 0;
    let totalTVA = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const prix = parseFloat(row.querySelector('.prix-unitaire').value) || 0;
        const quantite = parseFloat(row.querySelector('.quantite').value) || 0;
        const tvaRadio = row.querySelector('input[name*="[tva]"]:checked');
        const tva = tvaRadio ? parseFloat(tvaRadio.value) : 18; // Défaut 18% si rien de sélectionné

        const itemTotalHT = prix * quantite;
        const itemTotalTVA = itemTotalHT * (tva / 100);

        row.querySelector('.total-ht-item').value = itemTotalHT.toFixed(2) + ' FCFA';

        totalHT += itemTotalHT;
        totalTVA += itemTotalTVA;
    });

    const totalTTC = totalHT + totalTVA;

    document.getElementById('total-ht').textContent = totalHT.toFixed(2) + ' FCFA';
    document.getElementById('total-tva').textContent = totalTVA.toFixed(2) + ' FCFA';
    document.getElementById('total-ttc').textContent = totalTTC.toFixed(2) + ' FCFA';

    // Conversion en lettres (simplifiée)
    document.getElementById('montant-lettres').textContent = numberToWords(totalTTC);
}

// Conversion nombre en lettres (version simplifiée)
function numberToWords(number) {
    if (number === 0) return 'zéro FCFA';
    return number.toFixed(2).replace('.', ',') + ' FCFA';
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-item-btn').addEventListener('click', addItem);
    attachItemEvents();
    calculateTotals(); // Calculer les totaux au chargement
});
</script>

<style>
.item-row {
    transition: all 0.3s ease;
}

.item-row:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.remove-item {
    min-width: 40px;
}

.tva-radio-group {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}

.tva-radio-group .form-check {
    margin-bottom: 5px;
}

.tva-radio-group .form-check-input:checked {
    background-color: #1B5E20;
    border-color: #1B5E20;
}

.tva-radio-group .form-check-label {
    font-weight: 500;
    color: #495057;
}

.input-group-text {
    background-color: #e9ecef;
    border-color: #ced4da;
}
</style>
@endsection
