<?php $__env->startSection('title', 'Pointage Engin Standard | KENAM SERVICES'); ?>

<?php $__env->startSection('content'); ?>
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
            <a href="<?php echo e(route('materiel.cost-control.engin.list')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('materiel.cost-control.engin.pointages.store')); ?>" id="pointageForm">
        <?php echo csrf_field(); ?>

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
                <!-- ÉTAPE 1: Choix du projet -->
                <div class="card shadow-sm mb-4 border-start border-primary border-4">
                    <div class="card-header bg-light py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">1</div>
                            <h6 class="ms-3 mb-0 fw-bold">Sélectionner le projet</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
                                <select name="projet_id" id="projet_id" class="form-select form-select-lg" required>
                                    <option value="">-- Choisir un projet --</option>
                                    <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($projet->id); ?>"
                                                data-titre="<?php echo e($projet->titre); ?>"
                                                data-client="<?php echo e($projet->client ? $projet->client->raison_sociale : 'N/A'); ?>">
                                            <?php echo e($projet->titre); ?> - <?php echo e($projet->client ? $projet->client->raison_sociale : 'N/A'); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['projet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ÉTAPE 2: Choix de l'engin -->
                <div class="card shadow-sm mb-4 border-start border-success border-4">
                    <div class="card-header bg-light py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">2</div>
                            <h6 class="ms-3 mb-0 fw-bold">Sélectionner l'engin</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="engins_selection">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Veuillez d'abord sélectionner un projet pour voir les engins associés.
                            </div>
                        </div>
                        <div id="engins_container" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Engin <span class="text-danger">*</span></label>
                                    <select name="vehicle_id" id="vehicle_id" class="form-select form-select-lg" required>
                                        <option value="">-- Choisir un engin --</option>
                                    </select>
                                    <?php $__errorArgs = ['vehicle_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fournisseur / Propriétaire <span class="text-danger">*</span></label>
                                <select name="fournisseur_id" id="fournisseur_id" class="form-select form-select-lg" required>
                                    <option value="">-- Choisir le fournisseur --</option>
                                    <option value="kenam" selected>
                                        <i class="fas fa-building me-1"></i>Kenam (interne)
                                    </option>
                                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($supplier->id); ?>">
                                            <?php echo e($supplier->nom ?? $supplier->name ?? $supplier->raison_sociale); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['fournisseur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ÉTAPE 3: Récapitulatif fournisseur -->
                <div class="card shadow-sm mb-4 border-start border-warning border-4">
                    <div class="card-header bg-light py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">3</div>
                            <h6 class="ms-3 mb-0 fw-bold">Récapitulatif fournisseur</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="supplier_info" class="alert alert-light border">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            <strong>Fournisseur sélectionné:</strong>
                            <span id="supplier_name_display" class="badge bg-info ms-2">Aucun fournisseur</span>
                        </div>
                            </div>
                            <div id="current_status" class="badge bg-secondary">
                                <i class="fas fa-pause-circle me-1"></i>En attente
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Mode automatique (par défaut) -->
                        <div id="auto_mode">
                            <div class="text-center py-4">
                                <div id="pointage_display" class="mb-4" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-play-circle text-success fa-2x mb-2"></i>
                                                    <h6 class="mb-1">Démarré à</h6>
                                                    <h4 id="start_time_display" class="text-success fw-bold">--:--</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                                    <h6 class="mb-1">Durée actuelle</h6>
                                                    <h4 id="current_duration" class="fw-bold">00:00:00</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-stop-circle text-danger fa-2x mb-2"></i>
                                                    <h6 class="mb-1">Temps restant</h6>
                                                    <h4 id="estimated_end" class="text-muted fw-bold">--:--</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center gap-3">
                                    <button type="button" id="btn_start_pointage" class="btn btn-success btn-lg px-4" onclick="startPointage()">
                                        <i class="fas fa-play me-2"></i>Démarrer le pointage
                                    </button>
                                    <button type="button" id="btn_stop_pointage" class="btn btn-danger btn-lg px-4" onclick="stopPointage()" style="display: none;">
                                        <i class="fas fa-stop me-2"></i>Arrêter le pointage
                                    </button>
                                </div>
                            </div>

                            <!-- Mode manuel (optionnel) -->
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleManualMode()">
                                    <i class="fas fa-keyboard me-1"></i>Saisie manuelle
                                </button>
                            </div>
                        </div>

                        <!-- Mode manuel (caché par défaut) -->
                        <div id="manual_mode" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Heure de début <span class="text-danger">*</span></label>
                                    <input type="time" name="heure_debut" id="heure_debut" class="form-control form-control-lg">
                                    <?php $__errorArgs = ['heure_debut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Heure de fin <span class="text-danger">*</span></label>
                                    <input type="time" name="heure_fin" id="heure_fin" class="form-control form-control-lg">
                                    <?php $__errorArgs = ['heure_fin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-light d-flex align-items-center">
                                        <i class="fas fa-clock me-2 text-info"></i>
                                        <span class="fw-semibold">Durée calculée:</span>
                                        <span id="duration_calc" class="ms-2 text-primary fw-bold">0 heures</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleManualMode()">
                                    <i class="fas fa-mouse-pointer me-1"></i>Retour au mode automatique
                                </button>
                            </div>
                        </div>

                        <!-- Champs cachés pour le mode automatique -->
                        <input type="hidden" name="heure_debut" id="auto_heure_debut" value="">
                        <input type="hidden" name="heure_fin" id="auto_heure_fin" value="">
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
                            <textarea name="description" class="form-control" rows="3" placeholder="Ex: Transport de matériaux, travaux d'excavation, etc."><?php echo e(old('description')); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action alignés à droite -->
                <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Enregistrer le pointage
                    </button>
                    <a href="<?php echo e(route('materiel.cost-control.engin.list')); ?>" class="btn btn-outline-secondary btn-lg">
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
    const vehiclesData = <?php echo json_encode($vehiclesWithPrices ?? []); ?>;

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

    // Gestion de la sélection du projet
    document.getElementById('projet_id').addEventListener('change', function() {
        const projetId = this.value;
        const enginsContainer = document.getElementById('engins_container');
        const enginsSelection = document.getElementById('engins_selection');
        const vehicleSelect = document.getElementById('vehicle_id');

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
                    // Afficher un message si aucun engin n'est associé
                    enginsSelection.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Aucun engin n'est associé à ce projet. Veuillez contacter l'administrateur.
                        </div>
                    `;
                    enginsSelection.style.display = 'block';
                    enginsContainer.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des engins:', error);
                enginsSelection.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Erreur lors du chargement des engins. Veuillez réessayer.
                    </div>
                `;
                enginsSelection.style.display = 'block';
                enginsContainer.style.display = 'none';
            });
    });

    // Variables globales pour le pointage automatique
    let pointageTimer = null;
    let pointageStartTime = null;
    let isPointageActive = false;

    // Fonctions de pointage automatique
    function startPointage() {
        // Vérifier que tous les champs requis sont remplis
        const projetId = document.getElementById('projet_id').value;
        const vehicleId = document.getElementById('vehicle_id').value;
        const supplierId = document.getElementById('fournisseur_id').value;

        if (!projetId || !vehicleId || !supplierId) {
            alert('Veuillez d\'abord remplir toutes les étapes précédentes (projet, engin, fournisseur).');
            return;
        }

        // Démarrer le pointage
        pointageStartTime = new Date();
        isPointageActive = true;

        // Mettre à jour l'interface
        document.getElementById('btn_start_pointage').style.display = 'none';
        document.getElementById('btn_stop_pointage').style.display = 'inline-block';
        document.getElementById('pointage_display').style.display = 'block';
        document.getElementById('current_status').className = 'badge bg-success';
        document.getElementById('current_status').innerHTML = '<i class="fas fa-play-circle me-1"></i>Pointage en cours';

        // Afficher l'heure de début
        const startTimeStr = pointageStartTime.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('start_time_display').textContent = startTimeStr;
        document.getElementById('auto_heure_debut').value = startTimeStr;

        // Démarrer le timer
        updateDuration();
        pointageTimer = setInterval(updateDuration, 1000);

        // Estimer l'heure de fin (8 heures standard)
        const estimatedEnd = new Date(pointageStartTime.getTime() + 8 * 60 * 60 * 1000);
        document.getElementById('estimated_end').textContent = estimatedEnd.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    function stopPointage() {
        if (!isPointageActive) return;

        // Arrêter le timer
        if (pointageTimer) {
            clearInterval(pointageTimer);
            pointageTimer = null;
        }

        // Enregistrer l'heure de fin
        const endTime = new Date();
        const endTimeStr = endTime.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('auto_heure_fin').value = endTimeStr;

        // Calculer la durée
        const duration = (endTime - pointageStartTime) / 1000 / 60 / 60; // en heures
        const durationStr = duration.toFixed(2) + ' heures';

        // Mettre à jour l'interface
        document.getElementById('btn_start_pointage').style.display = 'inline-block';
        document.getElementById('btn_stop_pointage').style.display = 'none';
        document.getElementById('current_status').className = 'badge bg-danger';
        document.getElementById('current_status').innerHTML = '<i class="fas fa-stop-circle me-1"></i>Pointage terminé';

        // Mettre à jour les champs cachés et calculer les totaux
        document.getElementById('quantity_input').value = duration.toFixed(2);

        // Mettre à jour l'affichage de la durée
        document.getElementById('duration_calc').textContent = durationStr;
        document.getElementById('display_duration').textContent = duration.toFixed(2) + ' h';

        // Calculer les totaux
        updateTotals();

        // Réinitialiser les variables
        isPointageActive = false;
        pointageStartTime = null;

        // Afficher un message de confirmation
        showPointageSummary(duration);
    }

    function updateDuration() {
        if (!isPointageActive || !pointageStartTime) return;

        const now = new Date();
        const elapsed = now - pointageStartTime;

        // Formater en HH:MM:SS
        const hours = Math.floor(elapsed / 1000 / 60 / 60);
        const minutes = Math.floor((elapsed / 1000 / 60) % 60);
        const seconds = Math.floor((elapsed / 1000) % 60);

        const durationStr = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('current_duration').textContent = durationStr;
    }

    function toggleManualMode() {
        const autoMode = document.getElementById('auto_mode');
        const manualMode = document.getElementById('manual_mode');

        if (autoMode.style.display === 'none') {
            autoMode.style.display = 'block';
            manualMode.style.display = 'none';
        } else {
            autoMode.style.display = 'none';
            manualMode.style.display = 'block';
        }
    }

    function showPointageSummary(duration) {
        const supplierId = document.getElementById('fournisseur_id').value;
        const vehicleId = document.getElementById('vehicle_id').value;
        const vehicleData = vehiclesData.find(v => v.id == vehicleId);

        let summary = `Pointage terminé !\n\n`;
        summary += `Durée: ${duration.toFixed(2)} heures\n`;

        if (supplierId === 'kenam') {
            const clientPrice = vehicleData?.client_price_per_hour || vehicleData?.prix_location || 0;
            summary += `Coût total: ${formatPrice(clientPrice * duration)}\n`;
            summary += `(Fournisseur interne - pas de coût fournisseur)`;
        } else {
            const supplierPrice = vehicleData?.supplier_price_per_hour || 0;
            const clientPrice = vehicleData?.client_price_per_hour || 0;
            const margin = (clientPrice - supplierPrice) * duration;
            summary += `Coût fournisseur: ${formatPrice(supplierPrice * duration)}\n`;
            summary += `Montant client: ${formatPrice(clientPrice * duration)}\n`;
            summary += `Marge: ${formatPrice(margin)}`;
        }

        // Afficher dans une alerte stylisée
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Pointage enregistré!</h6>
            <pre class="mb-0">${summary}</pre>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        // Auto-suppression après 5 secondes
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/materiel/cost-control/engin-pointage-form.blade.php ENDPATH**/ ?>