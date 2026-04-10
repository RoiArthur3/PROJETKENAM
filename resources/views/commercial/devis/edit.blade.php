@extends('layouts.app')

@section('title', 'Modifier Proforma ' . $devis->reference . ' | KENAM SERVICES')

@section('content')
<x-dashboard-layout title="Modifier la Proforma" icon="fa-edit" subtitle="Modification de la proforma {{ $devis->reference }}">

    <!-- Actions principales -->
    <x-slot name="headerActions">
        <div class="d-flex gap-2">
            <a href="{{ route('commercial.devis.show', $devis->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour aux détails
            </a>
            <a href="{{ route('commercial.devis.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i>Liste des proformas
            </a>
        </div>
    </x-slot>

    <!-- Formulaire de modification -->
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-warning text-white">
            <h6 class="mb-0">
                <i class="fas fa-edit me-2"></i>Modifier les Informations de la Proforma
            </h6>
        </div>
        <div class="card-body">
            <form id="editProformaForm" method="POST" action="{{ route('commercial.devis.update', $devis->id) }}">
                @csrf
                @method('PUT')

                <!-- Informations générales -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Informations Générales
                        </h5>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Référence *</label>
                        <input type="text" class="form-control" value="{{ $devis->reference }}" readonly>
                        <small class="text-muted">La référence ne peut pas être modifiée</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Client *</label>
                        <select name="client_id" class="form-select" required>
                            <option value="">Sélectionner un client</option>
                            <option value="1" {{ $devis->client_id == 1 ? 'selected' : '' }}>TechnoPlus SA</option>
                            <option value="2" {{ $devis->client_id == 2 ? 'selected' : '' }}>Logistics Pro</option>
                            <option value="3" {{ $devis->client_id == 3 ? 'selected' : '' }}>Energy Solutions</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="brouillon" {{ $devis->statut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="en_attente" {{ $devis->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="accepte" {{ $devis->statut == 'accepte' ? 'selected' : '' }}>Accepté</option>
                            <option value="refuse" {{ $devis->statut == 'refuse' ? 'selected' : '' }}>Refusé</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date d'émission *</label>
                        <input type="date" name="issue_date" class="form-control" value="{{ $devis->issue_date ? $devis->issue_date->format('Y-m-d') : date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date d'échéance</label>
                        <input type="date" name="due_date" class="form-control" value="{{ $devis->due_date ? $devis->due_date->format('Y-m-d') : '' }}">
                    </div>
                </div>

                <!-- Objet et description -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-file-alt me-2"></i>Description et Objet
                        </h5>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Objet de la proforma *</label>
                        <input type="text" name="objet" class="form-control" value="{{ $devis->objet }}" placeholder="Ex: Maintenance préventive et corrective..." required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Notes complémentaires</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Informations supplémentaires...">{{ $devis->notes }}</textarea>
                    </div>
                </div>

                <!-- Prestations -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-list me-2"></i>Prestations et Services
                        </h5>
                    </div>

                    <div class="col-12">
                        <div id="prestationsContainer">
                            @if(isset($devis->prestations) && is_array($devis->prestations))
                                @foreach($devis->prestations as $index => $prestation)
                                <div class="prestation-item card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Désignation *</label>
                                                <input type="text" name="prestations[{{ $index }}][description]" class="form-control" value="{{ $prestation['description'] }}" required>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Quantité *</label>
                                                <input type="number" name="prestations[{{ $index }}][quantite]" class="form-control" value="{{ $prestation['quantite'] }}" min="1" required>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Prix Unit. *</label>
                                                <input type="number" name="prestations[{{ $index }}][prix_unitaire]" class="form-control" value="{{ $prestation['prix_unitaire'] }}" min="0" step="0.01" required>
                                            </div>
                                            <div class="col-md-1 mb-2">
                                                <label class="form-label">TVA %</label>
                                                <input type="number" name="prestations[{{ $index }}][tva]" class="form-control" value="{{ $prestation['tva'] }}" min="0" max="100" value="18">
                                            </div>
                                            <div class="col-md-1 mb-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="supprimerPrestation(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label">Détails</label>
                                                <textarea name="prestations[{{ $index }}][details]" class="form-control" rows="2">{{ $prestation['details'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <!-- Prestations par défaut pour DEV-0019 -->
                                <div class="prestation-item card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Désignation *</label>
                                                <input type="text" name="prestations[0][description]" class="form-control" value="Audit système complet" required>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Quantité *</label>
                                                <input type="number" name="prestations[0][quantite]" class="form-control" value="1" min="1" required>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Prix Unit. *</label>
                                                <input type="number" name="prestations[0][prix_unitaire]" class="form-control" value="1250000" min="0" step="0.01" required>
                                            </div>
                                            <div class="col-md-1 mb-2">
                                                <label class="form-label">TVA %</label>
                                                <input type="number" name="prestations[0][tva]" class="form-control" value="18" min="0" max="100">
                                            </div>
                                            <div class="col-md-1 mb-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="supprimerPrestation(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label">Détails</label>
                                                <textarea name="prestations[0][details]" class="form-control" rows="2">Audit complet des systèmes informatiques, sécurité et conformité</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" onclick="ajouterPrestation()">
                                <i class="fas fa-plus me-1"></i>Ajouter une prestation
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-calculator me-2"></i>Récapitulatif Financier
                        </h5>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <div class="h4 text-primary mb-1" id="totalHT">0 FCFA</div>
                                <div class="text-muted small">Total HT</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <div class="h4 text-info mb-1" id="totalTVA">0 FCFA</div>
                                <div class="text-muted small">TVA (18%)</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <div class="h4 mb-1" id="totalTTC">0 FCFA</div>
                                <div class="text-muted small">Total TTC</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="btn-group" role="group">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                            </button>
                            <button type="button" class="btn btn-warning btn-lg" onclick="previsualiser()">
                                <i class="fas fa-eye me-2"></i>Prévisualiser
                            </button>
                            <button type="button" class="btn btn-info btn-lg" onclick="dupliquer()">
                                <i class="fas fa-copy me-2"></i>Dupliquer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>

@push('scripts')
<script>
let prestationIndex = JSON.parse('{{ isset($devis->prestations) ? count($devis->prestations) : 1 }}');

// Calcul automatique des totaux
function calculerTotaux() {
    let totalHT = 0;

    document.querySelectorAll('.prestation-item').forEach(item => {
        const quantite = parseFloat(item.querySelector('input[name*="[quantite]"]').value) || 0;
        const prixUnitaire = parseFloat(item.querySelector('input[name*="[prix_unitaire]"]').value) || 0;
        const tva = parseFloat(item.querySelector('input[name*="[tva]"]').value) || 0;

        const sousTotal = quantite * prixUnitaire;
        totalHT += sousTotal;
    });

    const totalTVA = totalHT * 0.18;
    const totalTTC = totalHT + totalTVA;

    document.getElementById('totalHT').textContent = totalHT.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('totalTVA').textContent = totalTVA.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('totalTTC').textContent = totalTTC.toLocaleString('fr-FR') + ' FCFA';
}

// Ajouter une prestation
function ajouterPrestation() {
    const container = document.getElementById('prestationsContainer');
    const newPrestation = document.createElement('div');
    newPrestation.className = 'prestation-item card mb-3';
    newPrestation.innerHTML = `
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Désignation *</label>
                    <input type="text" name="prestations[${prestationIndex}][description]" class="form-control" required>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Quantité *</label>
                    <input type="number" name="prestations[${prestationIndex}][quantite]" class="form-control" value="1" min="1" required>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Prix Unit. *</label>
                    <input type="number" name="prestations[${prestationIndex}][prix_unitaire]" class="form-control" min="0" step="0.01" required>
                </div>
                <div class="col-md-1 mb-2">
                    <label class="form-label">TVA %</label>
                    <input type="number" name="prestations[${prestationIndex}][tva]" class="form-control" value="18" min="0" max="100">
                </div>
                <div class="col-md-1 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="supprimerPrestation(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label class="form-label">Détails</label>
                    <textarea name="prestations[${prestationIndex}][details]" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    `;
    container.appendChild(newPrestation);
    prestationIndex++;

    // Ajouter les event listeners pour les calculs
    attacherEventListeners();
}

// Supprimer une prestation
function supprimerPrestation(button) {
    button.closest('.prestation-item').remove();
    calculerTotaux();
}

// Attacher les event listeners pour les calculs
function attacherEventListeners() {
    document.querySelectorAll('input[name*="[quantite]"], input[name*="[prix_unitaire]"], input[name*="[tva]"]').forEach(input => {
        input.addEventListener('input', calculerTotaux);
    });
}

// Autres actions
function previsualiser() {
    // Ouvrir l'aperçu PDF dans une nouvelle fenêtre
    const formData = new FormData(document.getElementById('editProformaForm'));
    const previewData = {};

    // Collecter les données du formulaire
    for (let [key, value] of formData.entries()) {
        if (key.includes('prestations')) {
            // Gérer les prestations (structure complexe)
            const matches = key.match(/prestations\[(\d+)\]\[(\w+)\]/);
            if (matches) {
                const index = matches[1];
                const field = matches[2];
                if (!previewData.prestations) previewData.prestations = {};
                if (!previewData.prestations[index]) previewData.prestations[index] = {};
                previewData.prestations[index][field] = value;
            }
        } else {
            previewData[key] = value;
        }
    }

    // Convertir prestations en tableau
    if (previewData.prestations) {
        previewData.prestations = Object.values(previewData.prestations);
    }

    // Ouvrir la prévisualisation dans une nouvelle fenêtre
    const previewWindow = window.open('/commercial/proforma/preview', '_blank', 'width=1200,height=800');

    // Attendre que la fenêtre se charge puis envoyer les données
    previewWindow.onload = function() {
        previewWindow.postMessage({
            type: 'preview-data',
            data: previewData
        }, '*');
    };
}

function dupliquer() {
    if (confirm('Créer une copie de cette proforma ?')) {
        // Collecter les données actuelles du formulaire
        const formData = new FormData(document.getElementById('editProformaForm'));
        const duplicateData = {};

        // Collecter les données du formulaire
        for (let [key, value] of formData.entries()) {
            if (key.includes('prestations')) {
                // Gérer les prestations (structure complexe)
                const matches = key.match(/prestations\[(\d+)\]\[(\w+)\]/);
                if (matches) {
                    const index = matches[1];
                    const field = matches[2];
                    if (!duplicateData.prestations) duplicateData.prestations = {};
                    if (!duplicateData.prestations[index]) duplicateData.prestations[index] = {};
                    duplicateData.prestations[index][field] = value;
                }
            } else {
                duplicateData[key] = value;
            }
        }

        // Convertir prestations en tableau
        if (duplicateData.prestations) {
            duplicateData.prestations = Object.values(duplicateData.prestations);
        }

        // Générer une nouvelle référence
        const currentId = JSON.parse('{{ $devis->id }}');
        const newId = Math.floor(Math.random() * 1000) + 100; // ID temporaire pour la démo
        duplicateData.reference = 'DEV-' + String(newId).padStart(4, '0');
        duplicateData.id = newId;

        // Stocker temporairement en session
        fetch('/commercial/proforma/duplicate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(duplicateData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Proforma dupliquée avec succès ! Nouvelle référence : ' + duplicateData.reference);
                // Rediriger vers la nouvelle proforma
                window.location.href = '/commercial/proforma/' + newId + '/edit';
            } else {
                alert('Erreur lors de la duplication : ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la duplication de la proforma');
        });
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    attacherEventListeners();
    calculerTotaux();
});

// Soumission du formulaire
document.getElementById('editProformaForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Validation basique
    const requiredFields = this.querySelectorAll('input[required], select[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (isValid) {
        // Désactiver le bouton de soumission pour éviter les doubles soumissions
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';

        // Soumettre le formulaire
        this.submit();
    } else {
        alert('Veuillez remplir tous les champs obligatoires.');
    }
});
</script>
@endpush

<style>
.prestation-item {
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.prestation-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.form-label {
    font-weight: 600 !important;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group .btn {
    margin: 0 2px;
}
</style>
@endsection
