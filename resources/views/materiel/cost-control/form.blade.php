@extends('layouts.app')

@section('title', ($pointage->exists ? 'Modifier' : 'Nouveau') . ' pointage Cost Control | KENAM SERVICES')

@section('content')
@php
    $currentSubmodule = old('submodule', $pointage->submodule ?? request('submodule', 'engin'));
    $currentBillingMode = old('billing_mode', $pointage->billing_mode ?? 'standard');
    $selectedMissionValue = old('vehicle_mission_id', $selectedMissionId ?? $pointage->vehicle_mission_id);
@endphp

<div class="container-fluid cost-control-create py-3">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-stopwatch me-2 text-primary"></i>
                {{ $pointage->exists ? 'Modifier le pointage Cost Control' : 'Nouveau pointage Cost Control' }}
            </h1>
            <p class="text-muted mb-0">Saisie inspirée du module RH, avec facturation intelligente selon Standard ou Camion Plateau.</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ $currentSubmodule === 'camion_plateau' ? route('materiel.cost-control.plateau.list') : route('materiel.cost-control.engin.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Sous-module</div>
                    <div class="h5 mb-0 text-primary" id="kpiSubmodule">{{ $currentSubmodule === 'camion_plateau' ? 'Camion Plateau' : 'Standard' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Facturation</div>
                    <div class="h5 mb-0 text-warning" id="kpiBilling">{{ $currentBillingMode === 'monthly' ? 'Au mois' : ($currentBillingMode === 'trip' ? 'Au voyage' : 'Standard') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Coût fournisseur</div>
                    <div class="h5 mb-0 text-danger" id="kpiSupplier">0 FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Montant client</div>
                    <div class="h5 mb-0 text-success" id="kpiClient">0 FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ $pointage->exists ? ($currentSubmodule === 'camion_plateau' ? route('materiel.cost-control.plateau.pointages.update', $pointage) : route('materiel.cost-control.engin.pointages.update', $pointage)) : ($currentSubmodule === 'camion_plateau' ? route('materiel.cost-control.plateau.pointages.store') : route('materiel.cost-control.engin.pointages.store')) }}" id="costControlPointageForm">
        @csrf
        @if($pointage->exists)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-layer-group me-2"></i>Cadre du pointage</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sous-module</label>
                                <select name="submodule" id="submodule" class="form-select" required>
                                    <option value="engin" {{ $currentSubmodule === 'engin' ? 'selected' : '' }}>Engin standard</option>
                                    <option value="camion_plateau" {{ $currentSubmodule === 'camion_plateau' ? 'selected' : '' }}>Camion Plateau</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date du pointage</label>
                                <input type="date" name="date_pointage" class="form-control" value="{{ old('date_pointage', optional($pointage->date_pointage)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Statut</label>
                                @php $statut = old('statut', $pointage->statut ?? 'validé'); @endphp
                                <select name="statut" class="form-select">
                                    <option value="validé" {{ $statut === 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="brouillon" {{ $statut === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mission</label>
                                <select name="vehicle_mission_id" id="vehicle_mission_id" class="form-select">
                                    <option value="">Sélectionner une mission</option>
                                    @foreach($missions as $mission)
                                        @php
                                            $missionProfile = method_exists($mission, 'resolvePointageProfile')
                                                ? $mission->resolvePointageProfile()
                                                : ['submodule' => 'engin', 'billing_mode' => 'standard'];
                                        @endphp
                                        <option
                                            value="{{ $mission->id }}"
                                            data-vehicle-id="{{ $mission->vehicle_id }}"
                                            data-driver-id="{{ $mission->driver_id ?? $mission->user_id }}"
                                            data-supplier-price="{{ $mission->daily_supplier_price ?? 0 }}"
                                            data-client-price="{{ $mission->daily_client_price ?? 0 }}"
                                            data-submodule="{{ $missionProfile['submodule'] }}"
                                            data-billing-mode="{{ $missionProfile['billing_mode'] }}"
                                            {{ (string) $selectedMissionValue === (string) $mission->id ? 'selected' : '' }}
                                        >
                                            {{ $mission->reference ?? ('Mission #' . $mission->id) }}
                                            @if($mission->vehicle)
                                                - {{ $mission->vehicle->immatriculation }}
                                            @endif
                                            @if($mission->client)
                                                - {{ $mission->client->nom ?? $mission->client->name ?? 'Client' }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Engin</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select">
                                    <option value="">Sélectionner un engin</option>
                                    @foreach($vehicles as $vehicle)
                                        <option
                                            value="{{ $vehicle->id }}"
                                            data-supplier-price="{{ (float) ($vehicle->prix_location ?? 0) }}"
                                            {{ (string) old('vehicle_id', $pointage->vehicle_id) === (string) $vehicle->id ? 'selected' : '' }}
                                        >
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Conducteur</label>
                                <select name="driver_id" id="driver_id" class="form-select">
                                    <option value="">Sélectionner un conducteur</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" {{ (string) old('driver_id', $pointage->driver_id) === (string) $driver->id ? 'selected' : '' }}>
                                            {{ trim(($driver->nom ?? '') . ' ' . ($driver->prenoms ?? '')) ?: ('Personnel #' . $driver->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 standard-fields">
                                <label class="form-label fw-semibold">Heure début</label>
                                <input type="time" name="heure_debut" class="form-control" value="{{ old('heure_debut', $pointage->heure_arrivee ?? '') }}">
                            </div>
                            <div class="col-md-6 standard-fields">
                                <label class="form-label fw-semibold">Heure fin</label>
                                <input type="time" name="heure_fin" class="form-control" value="{{ old('heure_fin', $pointage->heure_depart ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 standard-fields">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-truck-moving me-2"></i>Pointage engin standard</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Type d'unité</label>
                                @php $unitType = old('unit_type', $pointage->unit_type ?? 'heure'); @endphp
                                <select name="unit_type" class="form-select">
                                    <option value="heure" {{ $unitType === 'heure' ? 'selected' : '' }}>Heure</option>
                                    <option value="jour" {{ $unitType === 'jour' ? 'selected' : '' }}>Jour</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Quantité travaillée</label>
                                <input type="number" step="0.01" min="0.01" name="quantity" id="quantity_standard" class="form-control" value="{{ old('quantity', $pointage->quantity ?? 1) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Montant fournisseur unitaire</label>
                                <input type="number" step="0.01" min="0" name="supplier_unit_cost" id="supplier_unit_cost_standard" class="form-control" value="{{ old('supplier_unit_cost', $pointage->supplier_unit_cost ?? 0) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Prix client unitaire</label>
                                <input type="number" step="0.01" min="0" name="client_unit_price" id="client_unit_price_standard" class="form-control" value="{{ old('client_unit_price', $pointage->client_unit_price ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 plateau-fields">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-road me-2"></i>Pointage Camion Plateau</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tâche / libellé de pointage</label>
                                <input type="text" name="task_label" class="form-control" value="{{ old('task_label', $pointage->task_label) }}" placeholder="Ex: Livraison plateau Abidjan - Yamoussoukro">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Mode de facturation</label>
                                <select name="billing_mode" id="billing_mode" class="form-select">
                                    <option value="monthly" {{ $currentBillingMode === 'monthly' ? 'selected' : '' }}>Au mois</option>
                                    <option value="trip" {{ $currentBillingMode === 'trip' ? 'selected' : '' }}>Au voyage</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Nombre de voyages</label>
                                <input type="number" step="0.01" min="1" name="trip_count" id="trip_count" class="form-control" value="{{ old('trip_count', $pointage->trip_count ?? 1) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Montant fournisseur</label>
                                <input type="number" step="0.01" min="0" name="supplier_unit_cost" id="supplier_unit_cost_plateau" class="form-control" value="{{ old('supplier_unit_cost', $pointage->supplier_unit_cost ?? 0) }}">
                            </div>
                            <div class="col-md-4 monthly-fields">
                                <label class="form-label fw-semibold">Seuil voyages facturés au mois</label>
                                <input type="number" step="0.01" min="0" name="monthly_trip_threshold" id="monthly_trip_threshold" class="form-control" value="{{ old('monthly_trip_threshold', $pointage->monthly_trip_threshold ?? 0) }}">
                            </div>
                            <div class="col-md-4 monthly-fields">
                                <label class="form-label fw-semibold">Montant forfaitaire mensuel</label>
                                <input type="number" step="0.01" min="0" name="monthly_flat_rate" id="monthly_flat_rate" class="form-control" value="{{ old('monthly_flat_rate', $pointage->monthly_flat_rate ?? 0) }}">
                            </div>
                            <div class="col-md-4 monthly-fields">
                                <label class="form-label fw-semibold">Montant supplémentaire par voyage</label>
                                <input type="number" step="0.01" min="0" name="extra_trip_unit_price" id="extra_trip_unit_price" class="form-control" value="{{ old('extra_trip_unit_price', $pointage->extra_trip_unit_price ?? 0) }}">
                            </div>

                            <div class="col-md-4 trip-fields">
                                <label class="form-label fw-semibold">Départ</label>
                                <input type="text" name="departure_location" class="form-control" value="{{ old('departure_location', $pointage->departure_location) }}">
                            </div>
                            <div class="col-md-4 trip-fields">
                                <label class="form-label fw-semibold">Arrivée</label>
                                <input type="text" name="arrival_location" class="form-control" value="{{ old('arrival_location', $pointage->arrival_location) }}">
                            </div>
                            <div class="col-md-4 trip-fields">
                                <label class="form-label fw-semibold">Distance (km)</label>
                                <input type="number" step="0.01" min="0" name="distance_km" class="form-control" value="{{ old('distance_km', $pointage->distance_km ?? 0) }}">
                            </div>
                            <div class="col-md-4 trip-fields">
                                <label class="form-label fw-semibold">Prix client par voyage</label>
                                <input type="number" step="0.01" min="0" name="client_unit_price" id="client_unit_price_plateau" class="form-control" value="{{ old('client_unit_price', $pointage->client_unit_price ?? 0) }}">
                            </div>
                            <div class="col-md-4 trip-fields">
                                <label class="form-label fw-semibold">N° bon de livraison</label>
                                <input type="text" name="delivery_note_number" class="form-control" value="{{ old('delivery_note_number', $pointage->delivery_note_number) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Montant carburant</label>
                                <input type="number" step="0.01" min="0" name="fuel_amount" class="form-control" value="{{ old('fuel_amount', $pointage->fuel_amount ?? 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Frais de route</label>
                                <input type="number" step="0.01" min="0" name="road_fees" class="form-control" value="{{ old('road_fees', $pointage->road_fees ?? 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Frais de péage</label>
                                <input type="number" step="0.01" min="0" name="toll_fees" class="form-control" value="{{ old('toll_fees', $pointage->toll_fees ?? 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Autres frais</label>
                                <input type="number" step="0.01" min="0" name="other_fees" class="form-control" value="{{ old('other_fees', $pointage->other_fees ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-secondary"><i class="fas fa-sticky-note me-2"></i>Commentaires</h6>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control" rows="4" placeholder="Observations, difficultés, précision sur le trajet ou la mission">{{ old('notes', $pointage->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 1rem;">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-calculator me-2"></i>Résumé métier</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light border mb-3">
                            <strong>Facturation intelligente</strong><br>
                            Standard: quantité × coûts unitaires. Camion Plateau: au mois ou au voyage.
                        </div>
                        <div class="small text-muted mb-3">
                            <div class="mb-2">Marge estimée:</div>
                            <div class="h5 mb-0" id="kpiMargin">0 FCFA</div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>{{ $pointage->exists ? 'Mettre à jour' : 'Enregistrer le pointage' }}
                            </button>
                            <a href="{{ $currentSubmodule === 'camion_plateau' ? route('materiel.cost-control.plateau.list') : route('materiel.cost-control.engin.list') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.cost-control-create .card-header h6 { letter-spacing: .2px; }
.cost-control-create .border-4 { border-width: 4px !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const submoduleSelect = document.getElementById('submodule');
    const billingModeSelect = document.getElementById('billing_mode');
    const missionSelect = document.getElementById('vehicle_mission_id');
    const vehicleSelect = document.getElementById('vehicle_id');
    const driverSelect = document.getElementById('driver_id');
    const standardSupplierInput = document.getElementById('supplier_unit_cost_standard');
    const plateauSupplierInput = document.getElementById('supplier_unit_cost_plateau');
    const standardClientInput = document.getElementById('client_unit_price_standard');
    const plateauClientInput = document.getElementById('client_unit_price_plateau');

    const kpiSubmodule = document.getElementById('kpiSubmodule');
    const kpiBilling = document.getElementById('kpiBilling');
    const kpiSupplier = document.getElementById('kpiSupplier');
    const kpiClient = document.getElementById('kpiClient');
    const kpiMargin = document.getElementById('kpiMargin');

    function fmt(value) {
        return new Intl.NumberFormat('fr-FR').format(Number(value || 0)) + ' FCFA';
    }

    function setDisabledInSection(selector, disabled) {
        document.querySelectorAll(selector).forEach((block) => {
            block.querySelectorAll('input, select, textarea').forEach((field) => {
                field.disabled = disabled;
            });
        });
    }

    function toggleSections() {
        const isPlateau = submoduleSelect.value === 'camion_plateau';
        document.querySelectorAll('.standard-fields').forEach((element) => {
            element.style.display = isPlateau ? 'none' : '';
        });
        document.querySelectorAll('.plateau-fields').forEach((element) => {
            element.style.display = isPlateau ? '' : 'none';
        });

        setDisabledInSection('.standard-fields', isPlateau);
        setDisabledInSection('.plateau-fields', !isPlateau);

        const isMonthly = billingModeSelect.value === 'monthly';
        document.querySelectorAll('.monthly-fields').forEach((element) => {
            element.style.display = isPlateau && isMonthly ? '' : 'none';
            element.querySelectorAll('input, select, textarea').forEach((field) => field.disabled = !(isPlateau && isMonthly));
        });
        document.querySelectorAll('.trip-fields').forEach((element) => {
            element.style.display = isPlateau && !isMonthly ? '' : 'none';
            element.querySelectorAll('input, select, textarea').forEach((field) => field.disabled = !(isPlateau && !isMonthly));
        });
    }

    function syncMissionDefaults() {
        const option = missionSelect.options[missionSelect.selectedIndex];
        if (!option) return;

        if (option.dataset.vehicleId && !vehicleSelect.value) {
            vehicleSelect.value = option.dataset.vehicleId;
        }
        if (option.dataset.driverId && !driverSelect.value) {
            driverSelect.value = option.dataset.driverId;
        }
        if (option.dataset.supplierPrice) {
            if (standardSupplierInput && !standardSupplierInput.value) standardSupplierInput.value = option.dataset.supplierPrice;
            if (plateauSupplierInput && !plateauSupplierInput.value) plateauSupplierInput.value = option.dataset.supplierPrice;
        }
        if (option.dataset.clientPrice) {
            if (standardClientInput && !standardClientInput.value) standardClientInput.value = option.dataset.clientPrice;
            if (plateauClientInput && !plateauClientInput.value) plateauClientInput.value = option.dataset.clientPrice;
        }
        if (option.dataset.submodule) submoduleSelect.value = option.dataset.submodule;
        if (option.dataset.billingMode) billingModeSelect.value = option.dataset.billingMode;

        toggleSections();
        updateKpis();
    }

    function syncVehicleSupplierPrice() {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        if (!selectedOption || !selectedOption.dataset.supplierPrice) return;

        const selectedVehiclePrice = selectedOption.dataset.supplierPrice;
        const isStandardSubmodule = submoduleSelect.value === 'engin';
        if (isStandardSubmodule && standardSupplierInput) {
            standardSupplierInput.value = selectedVehiclePrice;
        }
        updateKpis();
    }

    function updateKpis() {
        const isPlateau = submoduleSelect.value === 'camion_plateau';
        const billing = billingModeSelect.value;

        kpiSubmodule.textContent = isPlateau ? 'Camion Plateau' : 'Standard';
        kpiBilling.textContent = !isPlateau ? 'Standard' : (billing === 'monthly' ? 'Au mois' : 'Au voyage');

        let supplier = 0;
        let client = 0;

        if (!isPlateau) {
            const qty = parseFloat(document.getElementById('quantity_standard')?.value || 0);
            const supplierUnit = parseFloat(standardSupplierInput?.value || 0);
            const clientUnit = parseFloat(standardClientInput?.value || 0);
            supplier = qty * supplierUnit;
            client = qty * clientUnit;
        } else {
            const tripCount = parseFloat(document.getElementById('trip_count')?.value || 0);
            const supplierBase = parseFloat(plateauSupplierInput?.value || 0);
            const fuel = parseFloat(document.querySelector('input[name="fuel_amount"]')?.value || 0);
            const road = parseFloat(document.querySelector('input[name="road_fees"]')?.value || 0);
            const toll = parseFloat(document.querySelector('input[name="toll_fees"]')?.value || 0);
            const other = parseFloat(document.querySelector('input[name="other_fees"]')?.value || 0);
            supplier = supplierBase + fuel + road + toll + other;

            if (billing === 'monthly') {
                const threshold = parseFloat(document.getElementById('monthly_trip_threshold')?.value || 0);
                const flat = parseFloat(document.getElementById('monthly_flat_rate')?.value || 0);
                const extra = parseFloat(document.getElementById('extra_trip_unit_price')?.value || 0);
                const extraTrips = Math.max(0, tripCount - threshold);
                client = flat + (extraTrips * extra);
            } else {
                const clientTrip = parseFloat(plateauClientInput?.value || 0);
                client = tripCount * clientTrip;
            }
        }

        const margin = client - supplier;
        kpiSupplier.textContent = fmt(supplier);
        kpiClient.textContent = fmt(client);
        kpiMargin.textContent = fmt(margin);
        kpiMargin.classList.toggle('text-success', margin >= 0);
        kpiMargin.classList.toggle('text-danger', margin < 0);
    }

    submoduleSelect.addEventListener('change', () => { toggleSections(); updateKpis(); });
    billingModeSelect.addEventListener('change', () => { toggleSections(); updateKpis(); });
    missionSelect.addEventListener('change', syncMissionDefaults);
    vehicleSelect.addEventListener('change', syncVehicleSupplierPrice);

    document.querySelectorAll('#costControlPointageForm input, #costControlPointageForm select').forEach((el) => {
        el.addEventListener('input', updateKpis);
        el.addEventListener('change', updateKpis);
    });

    toggleSections();
    syncMissionDefaults();
    syncVehicleSupplierPrice();
    updateKpis();
});
</script>
@endsection
