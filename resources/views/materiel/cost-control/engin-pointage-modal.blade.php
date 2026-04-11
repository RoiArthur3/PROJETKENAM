<!-- Modal de Pointage d'Engin -->
<div class="modal fade" id="pointageEnginModal" tabindex="-1" aria-labelledby="pointageEnginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pointageEnginModalLabel">
                    <i class="fas fa-cogs me-2"></i>Nouveau Pointage d'Engin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="{{ route('materiel.cost-control.engin.pointages.store') }}" id="pointageModalForm">
                @csrf
                <div class="modal-body p-0">
                    <!-- Champs cachés pour les données système -->
                    <input type="hidden" name="submodule" value="engin">
                    <input type="hidden" name="unit_type" value="heure">
                    <input type="hidden" name="billing_mode" value="standard">
                    <input type="hidden" name="statut" value="validé">
                    <input type="hidden" name="quantity" id="quantity_input" value="0">
                    <input type="hidden" name="supplier_unit_cost" id="supplier_unit_cost_input" value="0">
                    <input type="hidden" name="client_unit_price" id="client_unit_price_input" value="0">

                    <div class="row g-0">
                        <!-- Colonne principale -->
                        <div class="col-lg-8">
                            <div class="p-4">
                                <!-- ÉTAPE 1: Choix du projet -->
                                <div class="card shadow-sm mb-3 border-start border-primary border-4">
                                    <div class="card-header bg-light py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: bold; font-size: 12px;">1</div>
                                            <h6 class="ms-2 mb-0 fw-bold">Sélectionner le projet</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold small">Projet <span class="text-danger">*</span></label>
                                                <select name="projet_id" id="modal_projet_id" class="form-select" required>
                                                    <option value="">-- Choisir un projet --</option>
                                                    @foreach($projets as $projet)
                                                        <option value="{{ $projet->id }}"
                                                                data-titre="{{ $projet->titre }}"
                                                                data-client="{{ $projet->client ? $projet->client->raison_sociale : 'N/A' }}">
                                                            {{ $projet->titre }} - {{ $projet->client ? $projet->client->raison_sociale : 'N/A' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('projet_id')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ÉTAPE 2: Choix de l'engin -->
                                <div class="card shadow-sm mb-3 border-start border-success border-4">
                                    <div class="card-header bg-light py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: bold; font-size: 12px;">2</div>
                                            <h6 class="ms-2 mb-0 fw-bold">Sélectionner l'engin</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="modal_engins_selection">
                                            <div class="alert alert-info small py-2">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Veuillez d'abord sélectionner un projet pour voir les engins associés.
                                            </div>
                                        </div>
                                        <div id="modal_engins_container" style="display: none;">
                                            <div class="row g-2">
                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold small">Engin <span class="text-danger">*</span></label>
                                                    <select name="vehicle_id" id="modal_vehicle_id" class="form-select" required>
                                                        <option value="">-- Choisir un engin --</option>
                                                    </select>
                                                    @error('vehicle_id')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold small">Fournisseur <span class="text-danger">*</span></label>
                                                    <select name="fournisseur_id" id="modal_fournisseur_id" class="form-select" required>
                                                        <option value="">-- Choisir le fournisseur --</option>
                                                        <option value="kenam" selected>Kenam (interne)</option>
                                                        @foreach($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}">
                                                                {{ $supplier->nom ?? $supplier->name ?? $supplier->raison_sociale }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('fournisseur_id')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ÉTAPE 3: Pointage -->
                                <div class="card shadow-sm mb-3 border-start border-warning border-4">
                                    <div class="card-header bg-light py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: bold; font-size: 12px;">3</div>
                                            <h6 class="ms-2 mb-0 fw-bold">Pointage</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Mode automatique -->
                                        <div id="modal_auto_mode">
                                            <div class="text-center py-3">
                                                <div id="modal_pointage_display" class="mb-3" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="card bg-light">
                                                                <div class="card-body text-center py-2">
                                                                    <i class="fas fa-play-circle text-success fa-lg mb-1"></i>
                                                                    <h6 class="mb-0 small">Démarré à</h6>
                                                                    <h5 id="modal_start_time_display" class="text-success fw-bold small">--:--</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card bg-primary text-white">
                                                                <div class="card-body text-center py-2">
                                                                    <i class="fas fa-clock fa-lg mb-1"></i>
                                                                    <h6 class="mb-0 small">Durée</h6>
                                                                    <h5 id="modal_current_duration" class="fw-bold small">00:00:00</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card bg-light">
                                                                <div class="card-body text-center py-2">
                                                                    <i class="fas fa-stop-circle text-danger fa-lg mb-1"></i>
                                                                    <h6 class="mb-0 small">Temps restant</h6>
                                                                    <h5 id="modal_estimated_end" class="text-muted fw-bold small">--:--</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" id="modal_btn_start_pointage" class="btn btn-success" onclick="startModalPointage()">
                                                        <i class="fas fa-play me-1"></i>Démarrer
                                                    </button>
                                                    <button type="button" id="modal_btn_stop_pointage" class="btn btn-danger" onclick="stopModalPointage()" style="display: none;">
                                                        <i class="fas fa-stop me-1"></i>Arrêter
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="text-center mt-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleModalManualMode()">
                                                    <i class="fas fa-keyboard me-1"></i>Saisie manuelle
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Mode manuel -->
                                        <div id="modal_manual_mode" style="display: none;">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small">Heure de début <span class="text-danger">*</span></label>
                                                    <input type="time" name="heure_debut" id="modal_heure_debut" class="form-control">
                                                    @error('heure_debut')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small">Heure de fin <span class="text-danger">*</span></label>
                                                    <input type="time" name="heure_fin" id="modal_heure_fin" class="form-control">
                                                    @error('heure_fin')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <div class="alert alert-light d-flex align-items-center py-2">
                                                        <i class="fas fa-clock me-2 text-info"></i>
                                                        <span class="fw-semibold small">Durée calculée:</span>
                                                        <span id="modal_duration_calc" class="ms-2 text-primary fw-bold small">0 heures</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleModalManualMode()">
                                                    <i class="fas fa-mouse-pointer me-1"></i>Mode automatique
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Champs cachés pour le mode automatique -->
                                        <input type="hidden" name="heure_debut" id="modal_auto_heure_debut" value="">
                                        <input type="hidden" name="heure_fin" id="modal_auto_heure_fin" value="">
                                    </div>
                                </div>

                                <!-- ÉTAPE 4: Description -->
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: bold; font-size: 12px;">4</div>
                                            <h6 class="ms-2 mb-0 fw-bold">Observations</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label small">Description du travail</label>
                                            <textarea name="description" class="form-control" rows="2" placeholder="Description du travail effectué...">{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne latérale: Résumé -->
                        <div class="col-lg-4">
                            <div class="p-4 bg-light">
                                <h6 class="mb-3 fw-bold"><i class="fas fa-truck me-2"></i>Résumé</h6>
                                
                                <!-- Engin sélectionné -->
                                <div class="mb-3 pb-2 border-bottom">
                                    <h6 class="text-muted small text-uppercase mb-1">Engin</h6>
                                    <p class="mb-0 small" id="modal_summary_vehicle">
                                        <span class="badge bg-secondary">Aucun engin</span>
                                    </p>
                                </div>

                                <!-- Horaires -->
                                <div class="mb-3 pb-2 border-bottom">
                                    <h6 class="text-muted small text-uppercase mb-1">Horaires</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td class="text-muted small">Début:</td>
                                            <td id="modal_display_debut" class="fw-semibold small">--:--</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted small">Fin:</td>
                                            <td id="modal_display_fin" class="fw-semibold small">--:--</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td class="text-muted small">Durée:</td>
                                            <td id="modal_display_duration" class="fw-bold text-info small">0 h</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Tarification -->
                                <div id="modal_tarification_kenam" style="display: none;">
                                    <h6 class="text-muted small text-uppercase mb-1">Tarifs (Kenam)</h6>
                                    <div class="price-card mb-2 p-2 bg-white rounded">
                                        <small class="text-muted">Prix client / heure</small>
                                        <div class="h6 mb-0 text-success small" id="modal_price_client_display">0 FCFA</div>
                                    </div>
                                </div>

                                <div id="modal_tarification_supplier" style="display: none;">
                                    <h6 class="text-muted small text-uppercase mb-1">Tarifs (Fournisseur)</h6>
                                    <div class="price-card mb-2 p-2 bg-white rounded">
                                        <small class="text-muted">Coût fournisseur / heure</small>
                                        <div class="h6 mb-0 text-danger small" id="modal_price_supplier_display">0 FCFA</div>
                                    </div>
                                    <div class="price-card mb-2 p-2 bg-white rounded">
                                        <small class="text-muted">Prix client / heure</small>
                                        <div class="h6 mb-0 text-success small" id="modal_price_client_supplier_display">0 FCFA</div>
                                    </div>
                                </div>

                                <!-- Totaux -->
                                <div id="modal_totaux_section" style="display: none;">
                                    <h6 class="text-muted small text-uppercase mb-2">Totaux</h6>
                                    <div class="total-row d-flex justify-content-between mb-1" id="modal_total_supplier_row" style="display: none;">
                                        <span class="text-muted small">Coût fournisseur:</span>
                                        <strong class="text-danger small" id="modal_total_supplier">0 FCFA</strong>
                                    </div>
                                    <div class="total-row d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Montant client:</span>
                                        <strong class="text-success small" id="modal_total_client">0 FCFA</strong>
                                    </div>
                                    <div class="total-row d-flex justify-content-between pt-1 border-top">
                                        <span class="text-muted fw-bold small">Marge:</span>
                                        <strong class="text-info small" id="modal_total_margin">0 FCFA</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.price-card {
    border-left: 3px solid #007bff;
}

.modal-xl {
    max-width: 95%;
}

.modal-body .card {
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.modal-body .form-control, .modal-body .form-select {
    font-size: 0.875rem;
}

.modal-body .small {
    font-size: 0.875rem;
}
</style>

<script>
// Variables globales pour le pointage modal
let modalPointageTimer = null;
let modalPointageStartTime = null;
let modalIsPointageActive = false;

// Données des engins
const modalVehiclesData = {!! json_encode($vehiclesWithPrices ?? []) !!};

// Gestion de la sélection du projet dans le modal
document.getElementById('modal_projet_id')?.addEventListener('change', function() {
    const projetId = this.value;
    const enginsContainer = document.getElementById('modal_engins_container');
    const enginsSelection = document.getElementById('modal_engins_selection');
    const vehicleSelect = document.getElementById('modal_vehicle_id');

    if (!projetId) {
        enginsContainer.style.display = 'none';
        enginsSelection.style.display = 'block';
        return;
    }

    // Charger les engins associés au projet via AJAX
    fetch(`/api/projets/${projetId}/engins`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.engins.length > 0) {
                // Vider et remplir la liste des engins
                vehicleSelect.innerHTML = '<option value="">-- Choisir un engin --</option>';

                data.engins.forEach(engin => {
                    const option = document.createElement('option');
                    option.value = engin.id;
                    option.setAttribute('data-immatriculation', engin.immatriculation);
                    option.setAttribute('data-marque', engin.marque);
                    option.setAttribute('data-modele', engin.modele);
                    option.textContent = `${engin.immatriculation} - ${engin.marque} ${engin.modele}`;
                    vehicleSelect.appendChild(option);
                });

                enginsContainer.style.display = 'block';
                enginsSelection.style.display = 'none';
            } else {
                enginsSelection.innerHTML = `
                    <div class="alert alert-warning small py-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Aucun engin associé à ce projet.
                    </div>
                `;
                enginsSelection.style.display = 'block';
                enginsContainer.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des engins:', error);
            enginsSelection.innerHTML = `
                <div class="alert alert-danger small py-2">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Erreur lors du chargement des engins.
                </div>
            `;
            enginsSelection.style.display = 'block';
            enginsContainer.style.display = 'none';
        });
});

// Mettre à jour le résumé quand on sélectionne un engin ou un fournisseur dans le modal
document.getElementById('modal_vehicle_id')?.addEventListener('change', updateModalSummary);
document.getElementById('modal_fournisseur_id')?.addEventListener('change', updateModalSummary);
document.getElementById('modal_heure_debut')?.addEventListener('change', updateModalDuration);
document.getElementById('modal_heure_fin')?.addEventListener('change', updateModalDuration);

function updateModalSummary() {
    const vehicleSelect = document.getElementById('modal_vehicle_id');
    const supplierSelect = document.getElementById('modal_fournisseur_id');
    
    if (!vehicleSelect || !supplierSelect) return;
    
    const vehicleId = vehicleSelect.value;
    const supplierId = supplierSelect.value;

    if (!vehicleId || !supplierId) {
        document.getElementById('modal_summary_vehicle').innerHTML = '<span class="badge bg-secondary">Aucun engin</span>';
        document.getElementById('modal_tarification_kenam').style.display = 'none';
        document.getElementById('modal_tarification_supplier').style.display = 'none';
        document.getElementById('modal_totaux_section').style.display = 'none';
        return;
    }

    // Afficher l'engin
    const vehicleOption = vehicleSelect.querySelector(`option[value="${vehicleId}"]`);
    const immatriculation = vehicleOption.dataset.immatriculation;
    const marque = vehicleOption.dataset.marque;
    const modele = vehicleOption.dataset.modele;

    document.getElementById('modal_summary_vehicle').innerHTML = `
        <div class="badge bg-info mb-1">${immatriculation}</div>
        <small class="text-muted">${marque} ${modele}</small>
    `;

    // Déterminer si c'est Kenam ou un autre fournisseur
    const isKenam = supplierId === 'kenam';

    if (isKenam) {
        document.getElementById('modal_tarification_kenam').style.display = 'block';
        document.getElementById('modal_tarification_supplier').style.display = 'none';
        document.getElementById('modal_total_supplier_row').style.display = 'none';

        const vehicleData = modalVehiclesData.find(v => v.id == vehicleId);
        const clientPrice = vehicleData?.client_price_per_hour || vehicleData?.prix_location || 0;
        document.getElementById('modal_price_client_display').textContent = formatPrice(clientPrice);
    } else {
        document.getElementById('modal_tarification_kenam').style.display = 'none';
        document.getElementById('modal_tarification_supplier').style.display = 'block';
        document.getElementById('modal_total_supplier_row').style.display = 'flex';

        const vehicleData = modalVehiclesData.find(v => v.id == vehicleId);
        const supplierPrice = vehicleData?.supplier_price_per_hour || 0;
        const clientPrice = vehicleData?.client_price_per_hour || 0;

        document.getElementById('modal_price_supplier_display').textContent = formatPrice(supplierPrice);
        document.getElementById('modal_price_client_supplier_display').textContent = formatPrice(clientPrice);
    }

    document.getElementById('modal_totaux_section').style.display = 'block';
    updateModalTotals();
}

function updateModalDuration() {
    const debut = document.getElementById('modal_heure_debut').value;
    const fin = document.getElementById('modal_heure_fin').value;

    if (!debut || !fin) {
        document.getElementById('modal_duration_calc').textContent = '-- heures';
        document.getElementById('modal_display_debut').textContent = '--:--';
        document.getElementById('modal_display_fin').textContent = '--:--';
        document.getElementById('modal_display_duration').textContent = '0 h';
        return;
    }

    // Afficher les heures
    document.getElementById('modal_display_debut').textContent = debut;
    document.getElementById('modal_display_fin').textContent = fin;

    // Calculer la durée
    const [debutH, debutM] = debut.split(':').map(Number);
    const [finH, finM] = fin.split(':').map(Number);

    let minutes = (finH * 60 + finM) - (debutH * 60 + debutM);

    if (minutes < 0) {
        minutes += 24 * 60; // Ajouter 24h si fin < début
    }

    const hours = (minutes / 60).toFixed(2);
    document.getElementById('modal_duration_calc').textContent = hours + ' heures';
    document.getElementById('modal_display_duration').textContent = hours + ' h';

    updateModalTotals();
}

function updateModalTotals() {
    const vehicleSelect = document.getElementById('modal_vehicle_id');
    const supplierSelect = document.getElementById('modal_fournisseur_id');
    const debut = document.getElementById('modal_heure_debut').value;
    const fin = document.getElementById('modal_heure_fin').value;

    if (!vehicleSelect || !supplierSelect) return;
    
    const vehicleId = vehicleSelect.value;
    const supplierId = supplierSelect.value;

    if (!vehicleId || !supplierId || !debut || !fin) {
        document.getElementById('modal_total_supplier').textContent = '0 FCFA';
        document.getElementById('modal_total_client').textContent = '0 FCFA';
        document.getElementById('modal_total_margin').textContent = '0 FCFA';
        return;
    }

    const [debutH, debutM] = debut.split(':').map(Number);
    const [finH, finM] = fin.split(':').map(Number);
    let minutes = (finH * 60 + finM) - (debutH * 60 + debutM);
    if (minutes < 0) minutes += 24 * 60;
    const hours = minutes / 60;

    const vehicleData = modalVehiclesData.find(v => v.id == vehicleId);
    const isKenam = supplierId === 'kenam';

    if (isKenam) {
        const clientPrice = vehicleData?.client_price_per_hour || vehicleData?.prix_location || 0;
        const totalClient = clientPrice * hours;

        document.getElementById('modal_total_client').textContent = formatPrice(totalClient);
        document.getElementById('modal_total_margin').textContent = formatPrice(totalClient) + ' (sans coût)';
        document.getElementById('modal_total_supplier_row').style.display = 'none';

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

        document.getElementById('modal_total_supplier').textContent = formatPrice(totalSupplier);
        document.getElementById('modal_total_client').textContent = formatPrice(totalClient);
        document.getElementById('modal_total_margin').textContent = formatPrice(margin) + ' (' + (margin >= 0 ? '+' : '') + ((margin / totalClient * 100).toFixed(1)) + '%)';
        document.getElementById('modal_total_supplier_row').style.display = 'flex';

        // Mettre à jour les champs cachés
        document.getElementById('quantity_input').value = hours.toFixed(2);
        document.getElementById('supplier_unit_cost_input').value = supplierPrice.toFixed(2);
        document.getElementById('client_unit_price_input').value = clientPrice.toFixed(2);
    }
}

// Fonctions de pointage automatique pour le modal
function startModalPointage() {
    // Vérifier que tous les champs requis sont remplis
    const projetId = document.getElementById('modal_projet_id').value;
    const vehicleId = document.getElementById('modal_vehicle_id').value;
    const supplierId = document.getElementById('modal_fournisseur_id').value;

    if (!projetId || !vehicleId || !supplierId) {
        alert('Veuillez d\'abord remplir toutes les étapes précédentes.');
        return;
    }

    // Démarrer le pointage
    modalPointageStartTime = new Date();
    modalIsPointageActive = true;

    // Mettre à jour l'interface
    document.getElementById('modal_btn_start_pointage').style.display = 'none';
    document.getElementById('modal_btn_stop_pointage').style.display = 'inline-block';
    document.getElementById('modal_pointage_display').style.display = 'block';

    // Afficher l'heure de début
    const startTimeStr = modalPointageStartTime.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    document.getElementById('modal_start_time_display').textContent = startTimeStr;
    document.getElementById('modal_auto_heure_debut').value = startTimeStr;
    document.getElementById('modal_display_debut').textContent = startTimeStr;

    // Démarrer le timer
    updateModalDuration();
    modalPointageTimer = setInterval(updateModalDuration, 1000);

    // Estimer l'heure de fin (8 heures standard)
    const estimatedEnd = new Date(modalPointageStartTime.getTime() + 8 * 60 * 60 * 1000);
    document.getElementById('modal_estimated_end').textContent = estimatedEnd.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

function stopModalPointage() {
    if (!modalIsPointageActive) return;

    // Arrêter le timer
    if (modalPointageTimer) {
        clearInterval(modalPointageTimer);
        modalPointageTimer = null;
    }

    // Enregistrer l'heure de fin
    const endTime = new Date();
    const endTimeStr = endTime.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    document.getElementById('modal_auto_heure_fin').value = endTimeStr;
    document.getElementById('modal_display_fin').textContent = endTimeStr;

    // Calculer et afficher la durée finale
    const duration = (endTime - modalPointageStartTime) / (1000 * 60 * 60); // en heures
    const hours = duration.toFixed(2);
    
    document.getElementById('modal_display_duration').textContent = hours + ' h';
    document.getElementById('modal_duration_calc').textContent = hours + ' heures';
    
    // Mettre à jour les totaux
    updateModalTotals();

    modalIsPointageActive = false;
}

function toggleModalManualMode() {
    const autoMode = document.getElementById('modal_auto_mode');
    const manualMode = document.getElementById('modal_manual_mode');
    
    if (autoMode.style.display === 'none') {
        autoMode.style.display = 'block';
        manualMode.style.display = 'none';
    } else {
        autoMode.style.display = 'none';
        manualMode.style.display = 'block';
    }
}

// Fonction utilitaire pour formater les prix
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(price)) + ' FCFA';
}

// Réinitialiser le modal à la fermeture
document.getElementById('pointageEnginModal')?.addEventListener('hidden.bs.modal', function () {
    // Arrêter le pointage si actif
    if (modalIsPointageActive) {
        stopModalPointage();
    }
    
    // Réinitialiser le formulaire
    document.getElementById('pointageModalForm').reset();
    
    // Réinitialiser l'affichage
    document.getElementById('modal_btn_start_pointage').style.display = 'inline-block';
    document.getElementById('modal_btn_stop_pointage').style.display = 'none';
    document.getElementById('modal_pointage_display').style.display = 'none';
    document.getElementById('modal_engins_container').style.display = 'none';
    document.getElementById('modal_engins_selection').style.display = 'block';
    document.getElementById('modal_tarification_kenam').style.display = 'none';
    document.getElementById('modal_tarification_supplier').style.display = 'none';
    document.getElementById('modal_totaux_section').style.display = 'none';
    
    // Réinitialiser les champs cachés
    document.getElementById('quantity_input').value = '0';
    document.getElementById('supplier_unit_cost_input').value = '0';
    document.getElementById('client_unit_price_input').value = '0';
});
</script>
