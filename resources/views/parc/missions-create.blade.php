@extends('layouts.app')

@section('title', 'Nouveau Projet de location Engin / Véhicule | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-truck-monster me-2"></i>Nouveau Projet de Location d’Engin / Véhicule</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('materiel.missions.store') }}" method="POST" id="missionForm">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fas fa-info-circle me-1"></i>Informations Générales</h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Document source</label>
                                <select name="source_type" class="form-select">
                                    <option value="">Aucun rattachement</option>
                                    <option value="prospection" {{ old('source_type', $defaults['source_type'] ?? '') === 'prospection' ? 'selected' : '' }}>Prospection</option>
                                    <option value="demande_recherche" {{ old('source_type', $defaults['source_type'] ?? '') === 'demande_recherche' ? 'selected' : '' }}>Demande de recherche</option>
                                    <option value="bon_commande" {{ old('source_type', $defaults['source_type'] ?? '') === 'bon_commande' ? 'selected' : '' }}>Bon de commande client</option>
                                    <option value="project" {{ old('source_type', $defaults['source_type'] ?? '') === 'project' ? 'selected' : '' }}>Projet de location</option>
                                    <option value="other" {{ old('source_type', $defaults['source_type'] ?? '') === 'other' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Référence source</label>
                                <input type="text" name="source_reference" class="form-control" value="{{ old('source_reference', $defaults['source_reference'] ?? '') }}" placeholder="Ex: BC-202603-001">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">ID source</label>
                                <input type="number" name="source_id" class="form-control" value="{{ old('source_id', $defaults['source_id'] ?? '') }}" min="1" placeholder="Optionnel si document déjà saisi">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Véhicule / Engin <span class="text-danger">*</span></label>
                                <select name="vehicle_id" class="form-select select2" required id="vehicle_select">
                                    <option value="">Choisir un véhicule...</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}" data-price="{{ $v->prix_location }}" data-type-materiel="{{ strtolower($v->type_materiel ?? '') }}">
                                            {{ $v->immatriculation }} - {{ $v->marque }} {{ $v->modele }} ({{ $v->type_materiel }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Conducteur / Chauffeur <span class="text-danger">*</span></label>
                                <select name="driver_id" class="form-select select2" required>
                                    <option value="">Choisir un chauffeur...</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Statut Initial <span class="text-danger">*</span></label>
                                <select name="status" class="form-select fw-bold">
                                    <option value="planned">En attente (engin pas encore sur chantier)</option>
                                    <option value="ongoing">En cours immédiatement</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Lieu de Destination <span class="text-danger">*</span></label>
                                <input type="text" name="destination" class="form-control" placeholder="Ex: Chantier SOGEA Abidjan" required value="{{ old('destination', $defaults['destination'] ?? '') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Date de début <span class="text-danger">*</span></label>
                                <input type="date" name="start_at" class="form-control" id="start_date" required value="{{ old('start_at', $defaults['start_at'] ?? date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" name="end_at" class="form-control" id="end_date" required value="{{ old('end_at', $defaults['end_at'] ?? date('Y-m-d', strtotime('+5 days'))) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Nombre de jours prévus <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control" id="duration" min="1" required value="{{ old('duration_days', $defaults['duration_days'] ?? 1) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Type de pointage</label>
                                <select name="pointage_submodule" id="pointage_submodule" class="form-select">
                                    <option value="engin" {{ old('pointage_submodule', $defaults['pointage_submodule'] ?? 'engin') === 'engin' ? 'selected' : '' }}>Standard</option>
                                    <option value="camion_plateau" {{ old('pointage_submodule', $defaults['pointage_submodule'] ?? '') === 'camion_plateau' ? 'selected' : '' }}>Camion Plateau</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Type de facturation</label>
                                <select name="billing_mode" id="billing_mode" class="form-select">
                                    <option value="standard" {{ old('billing_mode', $defaults['billing_mode'] ?? 'standard') === 'standard' ? 'selected' : '' }}>Standard</option>
                                    <option value="trip" {{ old('billing_mode', $defaults['billing_mode'] ?? '') === 'trip' ? 'selected' : '' }}>Au voyage</option>
                                    <option value="monthly" {{ old('billing_mode', $defaults['billing_mode'] ?? '') === 'monthly' ? 'selected' : '' }}>Au mois</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary"><i class="fas fa-tachometer-alt me-1"></i>Kilométrage au départ (Optionnel)</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">KM au départ</label>
                                <input type="number" name="start_km" class="form-control" placeholder="Ex: 125000" min="0">
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mt-4">Note : Le kilométrage à l'arrivée pourra être renseigné lors de la clôture de la mission.</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-success"><i class="fas fa-handshake me-1"></i>Parties Prenantes & Tarification</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded h-100 shadow-sm border-start border-danger border-4">
                                    <label class="form-label fw-bold text-danger"><i class="fas fa-truck-loading me-1"></i>Fournisseur (Prêteur)</label>
                                    <select name="supplier_id" class="form-select select2 mb-3">
                                        <option value="">Aucun (Véhicule interne)</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->raison_sociale }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label fw-bold">Prix Journalier Fournisseur (FCFA)</label>
                                    <input type="number" name="daily_supplier_price" class="form-control text-danger fw-bold" id="daily_supplier" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded h-100 shadow-sm border-start border-success border-4">
                                    <label class="form-label fw-bold text-success"><i class="fas fa-user-tie me-1"></i>Client (Locataire)</label>
                                    <select name="client_id" class="form-select select2 mb-3">
                                        <option value="">Sélectionner un client...</option>
                                        @foreach($clients as $c)
                                            <option value="{{ $c->id }}" {{ (string) old('client_id', $defaults['client_id'] ?? '') === (string) $c->id ? 'selected' : '' }}>{{ $c->raison_sociale }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label fw-bold">Prix Journalier Facturé Client (FCFA)</label>
                                    <input type="number" name="daily_client_price" class="form-control text-success fw-bold" id="daily_client" value="0" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info d-flex justify-content-between align-items-center p-4">
                                    <div>
                                        <h4 class="mb-0 fw-bold"><i class="fas fa-calculator me-2"></i>Estimation de la Marge</h4>
                                        <p class="mb-0 small opacity-75">Calcul automatique basé sur <span id="duration_text">{{ old('duration_days', $defaults['duration_days'] ?? 1) }}</span> jours</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="h3 mb-0 fw-bold" id="margin_display">0 FCFA</div>
                                        <div class="small fw-bold text-uppercase opacity-75 shadow-sm px-2 py-1 bg-white rounded mt-1" id="margin_label" style="color: #666;">Neutre</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Notes / Observations</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Détails supplémentaires sur la mission..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('materiel.missions.index') }}" class="btn btn-light border px-4 py-2"><i class="fas fa-times me-1"></i>Annuler</a>
                            <button type="submit" class="btn btn-primary px-5 py-2"><i class="fas fa-check-circle me-1"></i>Enregistrer le Projet de location</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5' });

        const startInput = $('#start_date');
        const endInput = $('#end_date');
        const durationInput = $('#duration');
        const durationText = $('#duration_text');
        const supplierInput = $('#daily_supplier');
        const clientInput = $('#daily_client');
        const marginDisplay = $('#margin_display');
        const marginLabel = $('#margin_label');
        const vehicleSelect = $('#vehicle_select');
        const pointageSubmoduleSelect = $('#pointage_submodule');
        const billingModeSelect = $('#billing_mode');
        const sourceTypeSelect = $('select[name="source_type"]');

        function applyBillingRules() {
            const submodule = pointageSubmoduleSelect.val();
            if (submodule === 'engin') {
                billingModeSelect.val('standard');
                billingModeSelect.find('option[value="standard"]').prop('disabled', false);
                billingModeSelect.find('option[value="trip"]').prop('disabled', true);
                billingModeSelect.find('option[value="monthly"]').prop('disabled', true);
                return;
            }

            if (sourceTypeSelect.val() === 'project') {
                billingModeSelect.val('monthly');
            } else if (billingModeSelect.val() === 'standard') {
                billingModeSelect.val('trip');
            }

            billingModeSelect.find('option[value="standard"]').prop('disabled', true);
            billingModeSelect.find('option[value="trip"]').prop('disabled', false);
            billingModeSelect.find('option[value="monthly"]').prop('disabled', false);
        }

        function syncEndDateFromDuration() {
            const startValue = startInput.val();
            const duration = parseInt(durationInput.val(), 10) || 1;

            if (!startValue) {
                return;
            }

            const start = new Date(startValue + 'T00:00:00');
            start.setDate(start.getDate() + duration - 1);
            endInput.val(start.toISOString().slice(0, 10));
        }

        function syncDurationFromDates() {
            const start = new Date(startInput.val());
            const end = new Date(endInput.val());
            
            if (start && end && end >= start) {
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; 
                durationInput.val(diffDays);
                durationText.text(diffDays);
            }
        }

        function calculateMetrics() {
            const diffDays = parseInt(durationInput.val(), 10) || 1;
            durationText.text(diffDays);

            const cost = parseFloat(supplierInput.val()) || 0;
            const revenue = parseFloat(clientInput.val()) || 0;
            const margin = (revenue - cost) * diffDays;

            marginDisplay.text(new Intl.NumberFormat('fr-FR').format(margin) + ' FCFA');
            
            if (margin > 0) {
                marginDisplay.removeClass('text-danger text-secondary').addClass('text-success');
                marginLabel.text('Bénéfice').removeClass('text-danger').addClass('text-success');
            } else if (margin < 0) {
                marginDisplay.removeClass('text-success text-secondary').addClass('text-danger');
                marginLabel.text('Perte').removeClass('text-success').addClass('text-danger');
            } else {
                marginDisplay.removeClass('text-success text-danger').addClass('text-secondary');
                marginLabel.text('Équilibre').removeClass('text-success text-danger');
            }
        }

        startInput.on('change', function() {
            syncEndDateFromDuration();
            calculateMetrics();
        });
        endInput.on('change', function() {
            syncDurationFromDates();
            calculateMetrics();
        });
        durationInput.on('input', function() {
            syncEndDateFromDuration();
            calculateMetrics();
        });
        supplierInput.on('input', calculateMetrics);
        clientInput.on('input', calculateMetrics);
        
        vehicleSelect.on('change', function() {
            const price = $(this).find(':selected').data('price');
            const typeMateriel = ($(this).find(':selected').data('type-materiel') || '').toString().toLowerCase();
            if (price) {
                clientInput.val(price);
                calculateMetrics();
            }

            if (typeMateriel.includes('camion')) {
                pointageSubmoduleSelect.val('camion_plateau');
            } else {
                pointageSubmoduleSelect.val('engin');
            }

            applyBillingRules();
        });

        pointageSubmoduleSelect.on('change', applyBillingRules);
        sourceTypeSelect.on('change', applyBillingRules);

        syncEndDateFromDuration();
        applyBillingRules();
        calculateMetrics();
    });
</script>
@endpush
@endsection
