@extends('layouts.app')

@section('title', ($pointage->exists ? 'Modifier' : 'Nouveau') . ' pointage engin | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-stopwatch me-2 text-info"></i>{{ $pointage->exists ? 'Modifier' : 'Nouveau' }} pointage d'engin</h1>
                    <p class="text-muted mb-0">Le premier pointage démarre la mission sur chantier et la fin est recalculée selon la durée prévue.</p>
                </div>
                <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-light border">Retour</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ $pointage->exists ? route('materiel.cost-control.update', $pointage) : route('materiel.cost-control.store') }}">
                        @csrf
                        @if($pointage->exists)
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mission</label>
                                <select name="vehicle_mission_id" id="mission_id" class="form-select">
                                    <option value="">Aucune mission liée</option>
                                    @foreach($missions as $mission)
                                        <option
                                            value="{{ $mission->id }}"
                                            data-vehicle-id="{{ $mission->vehicle_id }}"
                                            data-driver-id="{{ $mission->driver_id ?? $mission->user_id }}"
                                            data-supplier-cost="{{ $mission->daily_supplier_price ?? 0 }}"
                                            data-client-price="{{ $mission->daily_client_price ?? 0 }}"
                                            @selected(old('vehicle_mission_id', $pointage->vehicle_mission_id ?: $selectedMissionId) == $mission->id)
                                        >
                                            {{ $mission->reference }} - {{ $mission->destination }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Optionnel si le pointage est lié uniquement à un projet.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Projet / opération</label>
                                <select name="operation_id" class="form-select">
                                    <option value="">Aucun projet lié</option>
                                    @foreach($operations as $operation)
                                        <option value="{{ $operation->id }}" @selected(old('operation_id', $pointage->operation_id) == $operation->id)>
                                            {{ $operation->titre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" name="date_pointage" class="form-control" value="{{ old('date_pointage', optional($pointage->date_pointage)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Engin utilisé</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                                    <option value="">Choisir un engin...</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $pointage->vehicle_id) == $vehicle->id)>
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Chauffeur</label>
                                <select name="driver_id" id="driver_id" class="form-select">
                                    <option value="">Aucun chauffeur</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" @selected(old('driver_id', $pointage->driver_id) == $driver->id)>
                                            {{ $driver->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Unité de travail</label>
                                <select name="unit_type" class="form-select" id="unit_type" required>
                                    <option value="heure" @selected(old('unit_type', $pointage->unit_type ?: 'heure') === 'heure')>Heure</option>
                                    <option value="jour" @selected(old('unit_type', $pointage->unit_type) === 'jour')>Jour</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Quantité travaillée</label>
                                <input type="number" step="0.01" min="0.01" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $pointage->quantity ?: 1) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coût unitaire réel</label>
                                <input type="number" step="0.01" min="0" name="supplier_unit_cost" id="supplier_unit_cost" class="form-control" value="{{ old('supplier_unit_cost', $pointage->supplier_unit_cost ?: 0) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tarif unitaire facturé</label>
                                <input type="number" step="0.01" min="0" name="client_unit_price" id="client_unit_price" class="form-control" value="{{ old('client_unit_price', $pointage->client_unit_price ?: 0) }}">
                            </div>

                            <div class="col-12">
                                <div class="alert alert-light border d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Prévision en temps réel</div>
                                        <div class="small text-muted">Le calcul est mis à jour à partir du pointage saisi</div>
                                    </div>
                                    <div class="text-end">
                                        <div id="computed_cost" class="fw-bold text-danger">0 FCFA</div>
                                        <div id="computed_revenue" class="fw-bold text-success">0 FCFA</div>
                                        <div id="computed_margin" class="fw-bold text-primary">0 FCFA</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Observations</label>
                                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $pointage->notes) }}</textarea>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-light border">Annuler</a>
                                <button type="submit" class="btn btn-primary">{{ $pointage->exists ? 'Mettre à jour' : 'Enregistrer le pointage' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const missionSelect = document.getElementById('mission_id');
    const vehicleSelect = document.getElementById('vehicle_id');
    const driverSelect = document.getElementById('driver_id');
    const quantityInput = document.getElementById('quantity');
    const supplierInput = document.getElementById('supplier_unit_cost');
    const clientInput = document.getElementById('client_unit_price');

    function updateMissionDefaults() {
        const selected = missionSelect.options[missionSelect.selectedIndex];
        if (!selected || !selected.value) {
            computeTotals();
            return;
        }

        if (selected.dataset.vehicleId) {
            vehicleSelect.value = selected.dataset.vehicleId;
        }

        if (selected.dataset.driverId) {
            driverSelect.value = selected.dataset.driverId;
        }

        if (!supplierInput.value || Number(supplierInput.value) === 0) {
            supplierInput.value = selected.dataset.supplierCost || 0;
        }

        if (!clientInput.value || Number(clientInput.value) === 0) {
            clientInput.value = selected.dataset.clientPrice || 0;
        }

        computeTotals();
    }

    function computeTotals() {
        const quantity = Number(quantityInput.value || 0);
        const supplier = Number(supplierInput.value || 0);
        const client = Number(clientInput.value || 0);

        const totalCost = quantity * supplier;
        const totalRevenue = quantity * client;
        const totalMargin = totalRevenue - totalCost;

        document.getElementById('computed_cost').textContent = new Intl.NumberFormat('fr-FR').format(totalCost) + ' FCFA';
        document.getElementById('computed_revenue').textContent = new Intl.NumberFormat('fr-FR').format(totalRevenue) + ' FCFA';
        document.getElementById('computed_margin').textContent = new Intl.NumberFormat('fr-FR').format(totalMargin) + ' FCFA';
    }

    missionSelect.addEventListener('change', updateMissionDefaults);
    quantityInput.addEventListener('input', computeTotals);
    supplierInput.addEventListener('input', computeTotals);
    clientInput.addEventListener('input', computeTotals);

    updateMissionDefaults();
    computeTotals();
});
</script>
@endsection