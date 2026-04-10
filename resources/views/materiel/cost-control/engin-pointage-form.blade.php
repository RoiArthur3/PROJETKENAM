@extends('layouts.app')

@section('title', 'Pointage Engin Standard | KENAM SERVICES')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2 text-info"></i>Pointage Engin Standard
            </h1>
            <p class="text-muted mb-0">Saisir les heures travaillées et calculer automatiquement les coûts et marges</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('materiel.cost-control.engin.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('materiel.cost-control.engin.pointages.store') }}" id="pointageForm">
        @csrf
        
        <!-- Champs cachés pour les données système -->
        <input type="hidden" name="submodule" value="engin">
        <input type="hidden" name="unit_type" value="heure">
        <input type="hidden" name="billing_mode" value="standard">
        <input type="hidden" name="statut" value="validé">
        
        <!-- Champs calculés et cachés -->
        <input type="hidden" name="quantity" id="quantity_input" value="0">
        <input type="hidden" name="supplier_unit_cost" id="supplier_unit_cost_input" value="0">
        <input type="hidden" name="client_unit_price" id="client_unit_price_input" value="0">

        <div class="row g-4">
            <!-- Colonne principale -->
            <div class="col-lg-8">
                <!-- ÉTAPE 1: Choix de l'engin -->
                <div class="card shadow-sm mb-4 border-start border-primary border-4">
                    <div class="card-header bg-light py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">1</div>
                            <h6 class="ms-3 mb-0 fw-bold">Sélectionner l'engin</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Engin <span class="text-danger">*</span></label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select form-select-lg" required>
                                    <option value="">-- Choisir un engin --</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                                data-immatriculation="{{ $vehicle->immatriculation }}"
                                                data-marque="{{ $vehicle->marque }}"
                                                data-modele="{{ $vehicle->modele }}">
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fournisseur / Propriétaire <span class="text-danger">*</span></label>
                                <select name="fournisseur_id" id="fournisseur_id" class="form-select form-select-lg" required>
                                    <option value="">-- Choisir le fournisseur --</option>
                                    <option value="kenam" selected>
                                        <i class="fas fa-building me-1"></i>Kenam (interne)
                                    </option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">
                                            {{ $supplier->nom ?? $supplier->name ?? $supplier->raison_sociale }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fournisseur_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ÉTAPE 2: Récapitulatif fournisseur -->
                <div class="card shadow-sm mb-4 border-start border-warning border-4">
                    <div class="card-header bg-light py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">2</div>
                            <h6 class="ms-3 mb-0 fw-bold">Récapitulatif fournisseur</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="supplier_info" class="alert alert-light border">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            <strong>Fournisseur sélectionné:</strong>
                            <span id="supplier_name_display" class="badge bg-info ms-2">Aucun fournisseur</span>
                        </div>
                        <small class="text-muted">
                            Les tarifs client et fournisseur sont basés sur la sélection de l'engin et du fournisseur propriétaire.
                        </small>
                    </div>
                </div>

                <!-- ÉTAPE 3: Pointage horaire -->
                <div class="card shadow-sm mb-4 border-start border-success border-4">
                    <div class="card-header bg-light py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">3</div>
                            <h6 class="ms-3 mb-0 fw-bold">Pointage horaire</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_pointage" class="form-control form-control-lg" 
                                       value="{{ old('date_pointage', now()->format('Y-m-d')) }}" required>
                                @error('date_pointage')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Heure début <span class="text-danger">*</span></label>
                                <input type="time" name="heure_debut" id="heure_debut" class="form-control form-control-lg" required>
                                @error('heure_debut')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Heure fin <span class="text-danger">*</span></label>
                                <input type="time" name="heure_fin" id="heure_fin" class="form-control form-control-lg" required>
                                @error('heure_fin')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Durée calculée:</strong> <span id="duration_calc">-- heures</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ÉTAPE 4: Description et observations -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">4</div>
                            <h6 class="ms-3 mb-0 fw-bold">Observations</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Description du travail effectué</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Ex: Transport de matériaux, travaux d'excavation, etc.">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Enregistrer le pointage
                    </button>
                    <a href="{{ route('materiel.cost-control.engin.list') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </div>

            <!-- Colonne latérale: Résumé des tarifs -->
            <div class="col-lg-4">
                <!-- Résumé engin -->
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i>Résumé du pointage</h6>
                    </div>
                    <div class="card-body">
                        <!-- Engin sélectionné -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-muted small text-uppercase mb-2">Engin sélectionné</h6>
                            <p class="mb-0" id="summary_vehicle">
                                <span class="badge bg-secondary">Aucun engin</span>
                            </p>
                        </div>

                        <!-- Times -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-muted small text-uppercase mb-2">Horaires</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-muted">Début:</td>
                                    <td id="display_debut" class="fw-semibold">--:--</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Fin:</td>
                                    <td id="display_fin" class="fw-semibold">--:--</td>
                                </tr>
                                <tr class="table-info">
                                    <td class="text-muted">Durée:</td>
                                    <td id="display_duration" class="fw-bold text-info">0 h</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Tarification -->
                        <div id="tarification_kenam" style="display: none;">
                            <h6 class="text-muted small text-uppercase mb-2">Tarifs (Kenam)</h6>
                            <div class="price-card mb-3 p-3 bg-light rounded">
                                <small class="text-muted">Prix location client / heure</small>
                                <div class="h5 mb-0 text-success" id="price_client_display">0 FCFA</div>
                            </div>
                            <div class="alert alert-info small">
                                <i class="fas fa-info-circle me-1"></i>
                                Pas de coût fournisseur pour les engins Kenam
                            </div>
                        </div>

                        <div id="tarification_supplier" style="display: none;">
                            <h6 class="text-muted small text-uppercase mb-2">Tarifs (Fournisseur)</h6>
                            <div class="price-card mb-2 p-3 bg-light rounded">
                                <small class="text-muted">Coût fournisseur / heure</small>
                                <div class="h5 mb-0 text-danger" id="price_supplier_display">0 FCFA</div>
                            </div>
                            <div class="price-card mb-3 p-3 bg-light rounded">
                                <small class="text-muted">Prix client / heure</small>
                                <div class="h5 mb-0 text-success" id="price_client_supplier_display">0 FCFA</div>
                            </div>
                        </div>

                        <!-- Totaux -->
                        <div class="mb-3 pb-3 border-bottom" id="totaux_section" style="display: none;">
                            <h6 class="text-muted small text-uppercase mb-2">Totaux estimés</h6>
                            <div class="total-row d-flex justify-content-between mb-2" id="total_supplier_row" style="display: none;">
                                <span class="text-muted">Coût total fournisseur:</span>
                                <strong class="text-danger" id="total_supplier">0 FCFA</strong>
                            </div>
                            <div class="total-row d-flex justify-content-between mb-2">
                                <span class="text-muted">Montant client total:</span>
                                <strong class="text-success" id="total_client">0 FCFA</strong>
                            </div>
                            <div class="total-row d-flex justify-content-between pt-2 border-top">
                                <span class="text-muted fw-bold">Marge estimée:</span>
                                <strong class="text-info" id="total_margin">0 FCFA</strong>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="alert alert-light border">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1 text-warning"></i>
                                Les tarifs s'affichent une fois l'engin et la provenance sélectionnés.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .price-card {
        border-left: 4px solid #007bff;
    }
    
    .sticky-top {
        z-index: 100;
    }

    .form-check-lg .form-check-input {
        width: 1.5rem;
        height: 1.5rem;
        margin-top: 0.375rem;
    }

    .form-check-lg .form-check-label {
        padding-left: 0.5rem;
        font-size: 1rem;
    }
</style>

<script>
    const vehicleSelect = document.getElementById('vehicle_id');
    const supplierSelect = document.getElementById('fournisseur_id');
    const heureDebut = document.getElementById('heure_debut');
    const heureFin = document.getElementById('heure_fin');

    // Données des engins (avec tarifs)
    const vehiclesData = {!! json_encode($vehiclesWithPrices ?? []) !!};

    // Mettre à jour le résumé quand on sélectionne un engin ou un fournisseur
    vehicleSelect.addEventListener('change', updateSummary);
    supplierSelect.addEventListener('change', updateSummary);
    heureDebut.addEventListener('change', updateDuration);
    heureFin.addEventListener('change', updateDuration);

    function updateSummary() {
        const vehicleId = vehicleSelect.value;
        const supplierId = supplierSelect.value;

        if (!vehicleId || !supplierId) {
            document.getElementById('summary_vehicle').innerHTML = '<span class="badge bg-secondary">Aucun engin</span>';
            document.getElementById('supplier_name_display').textContent = 'Aucun fournisseur';
            document.getElementById('tarification_kenam').style.display = 'none';
            document.getElementById('tarification_supplier').style.display = 'none';
            document.getElementById('totaux_section').style.display = 'none';
            return;
        }

        // Afficher l'engin
        const vehicleOption = vehicleSelect.querySelector(`option[value="${vehicleId}"]`);
        const immatriculation = vehicleOption.dataset.immatriculation;
        const marque = vehicleOption.dataset.marque;
        const modele = vehicleOption.dataset.modele;
        
        document.getElementById('summary_vehicle').innerHTML = `
            <div class="badge bg-info mb-2">${immatriculation}</div>
            <small class="text-muted">${marque} ${modele}</small>
        `;

        // Afficher le fournisseur
        const supplierOption = supplierSelect.querySelector(`option[value="${supplierId}"]`);
        document.getElementById('supplier_name_display').textContent = supplierOption.text;

        // Trouver les données du véhicule
        const vehicleData = vehiclesData.find(v => v.id == vehicleId);
        
        // Déterminer si c'est Kenam ou un autre fournisseur
        const isKenam = supplierId === 'kenam';
        
        if (isKenam) {
            document.getElementById('tarification_kenam').style.display = 'block';
            document.getElementById('tarification_supplier').style.display = 'none';
            document.getElementById('total_supplier_row').style.display = 'none';
            
            const clientPrice = vehicleData?.client_price_per_hour || vehicleData?.prix_location || 0;
            document.getElementById('price_client_display').textContent = formatPrice(clientPrice);
        } else {
            document.getElementById('tarification_kenam').style.display = 'none';
            document.getElementById('tarification_supplier').style.display = 'block';
            document.getElementById('total_supplier_row').style.display = 'flex';
            
            const supplierPrice = vehicleData?.supplier_price_per_hour || 0;
            const clientPrice = vehicleData?.client_price_per_hour || 0;
            
            document.getElementById('price_supplier_display').textContent = formatPrice(supplierPrice);
            document.getElementById('price_client_supplier_display').textContent = formatPrice(clientPrice);
        }

        document.getElementById('totaux_section').style.display = 'block';
        updateTotals();
    }

    function updateDuration() {
        const debut = heureDebut.value;
        const fin = heureFin.value;

        if (!debut || !fin) {
            document.getElementById('duration_calc').textContent = '-- heures';
            document.getElementById('display_debut').textContent = '--:--';
            document.getElementById('display_fin').textContent = '--:--';
            document.getElementById('display_duration').textContent = '0 h';
            return;
        }

        // Afficher les heures
        document.getElementById('display_debut').textContent = debut;
        document.getElementById('display_fin').textContent = fin;

        // Calculer la durée
        const [debutH, debutM] = debut.split(':').map(Number);
        const [finH, finM] = fin.split(':').map(Number);
        
        let minutes = (finH * 60 + finM) - (debutH * 60 + debutM);
        
        if (minutes < 0) {
            minutes += 24 * 60; // Ajouter 24h si fin < début (travail de nuit)
        }
        
        const hours = (minutes / 60).toFixed(2);
        document.getElementById('duration_calc').textContent = hours + ' heures';
        document.getElementById('display_duration').textContent = hours + ' h';

        updateTotals();
    }

    function updateTotals() {
        const vehicleId = vehicleSelect.value;
        const supplierId = supplierSelect.value;
        const debut = heureDebut.value;
        const fin = heureFin.value;

        if (!vehicleId || !supplierId || !debut || !fin) {
            document.getElementById('total_supplier').textContent = '0 FCFA';
            document.getElementById('total_client').textContent = '0 FCFA';
            document.getElementById('total_margin').textContent = '0 FCFA';
            return;
        }

        const [debutH, debutM] = debut.split(':').map(Number);
        const [finH, finM] = fin.split(':').map(Number);
        let minutes = (finH * 60 + finM) - (debutH * 60 + debutM);
        if (minutes < 0) minutes += 24 * 60;
        const hours = minutes / 60;

        const vehicleData = vehiclesData.find(v => v.id == vehicleId);
        const isKenam = supplierId === 'kenam';
        
        if (isKenam) {
            const clientPrice = vehicleData?.client_price_per_hour || vehicleData?.prix_location || 0;
            const totalClient = clientPrice * hours;
            
            document.getElementById('total_client').textContent = formatPrice(totalClient);
            document.getElementById('total_margin').textContent = formatPrice(totalClient) + ' (sans coût)';
            document.getElementById('total_supplier_row').style.display = 'none';
            
            // Mettre à jour les champs cachés
            document.getElementById('quantity_input').value = hours.toFixed(2);
            document.getElementById('supplier_unit_cost_input').value = 0;
            document.getElementById('client_unit_price_input').value = clientPrice.toFixed(2);
        } else {
            const supplierPrice = vehicleData?.supplier_price_per_hour || 0;
            const clientPrice = vehicleData?.client_price_per_hour || 0;
            const totalSupplier = supplierPrice * hours;
            const totalClient = clientPrice * hours;
            const margin = totalClient - totalSupplier;

            document.getElementById('total_supplier').textContent = formatPrice(totalSupplier);
            document.getElementById('total_client').textContent = formatPrice(totalClient);
            document.getElementById('total_margin').textContent = formatPrice(margin) + ' (' + (margin >= 0 ? '+' : '') + ((margin / totalClient * 100).toFixed(1)) + '%)';
            document.getElementById('total_supplier_row').style.display = 'flex';
            
            // Mettre à jour les champs cachés
            document.getElementById('quantity_input').value = hours.toFixed(2);
            document.getElementById('supplier_unit_cost_input').value = supplierPrice.toFixed(2);
            document.getElementById('client_unit_price_input').value = clientPrice.toFixed(2);
        }
    }

    function formatPrice(value) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'XOF',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }
</script>
@endsection
