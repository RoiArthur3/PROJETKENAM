@extends('layouts.app')

@section('title', ($entry->exists ? 'Modifier' : 'Nouvelle') . ' charge / CA | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-wallet me-2 text-info"></i>{{ $entry->exists ? 'Modifier' : 'Nouvelle' }} charge / chiffre d'affaires</h1>
                    <p class="text-muted mb-0">Rattacher les charges chantier à la trésorerie et le CA au module Facture</p>
                </div>
                <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-light border">Retour</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ $entry->exists ? route('materiel.cost-control.financial-entries.update', $entry) : route('materiel.cost-control.financial-entries.store') }}">
                        @csrf
                        @if($entry->exists)
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mission</label>
                                <select name="vehicle_mission_id" id="mission_id" class="form-select">
                                    <option value="">Aucune mission liée</option>
                                    @foreach($missions as $mission)
                                        <option value="{{ $mission->id }}" data-vehicle-id="{{ $mission->vehicle_id }}" @selected(old('vehicle_mission_id', $entry->vehicle_mission_id ?: $selectedMissionId) == $mission->id)>
                                            {{ $mission->reference }} - {{ $mission->destination }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Projet / opération</label>
                                <select name="operation_id" class="form-select">
                                    <option value="">Aucun projet lié</option>
                                    @foreach($operations as $operation)
                                        <option value="{{ $operation->id }}" @selected(old('operation_id', $entry->operation_id) == $operation->id)>
                                            {{ $operation->titre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    @foreach(\App\Models\VehicleFinancialEntry::entryTypes() as $value => $label)
                                        <option value="{{ $value }}" @selected(old('type', $entry->type ?: $defaultEntryType) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Catégorie</label>
                                <select name="category" id="category" class="form-select" required>
                                    @foreach(\App\Models\VehicleFinancialEntry::categories() as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category', $entry->category ?: 'fuel') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" name="transaction_date" id="transaction_date" class="form-control" value="{{ old('transaction_date', optional($entry->transaction_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Engin</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select">
                                    <option value="">Choisir un engin...</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $entry->vehicle_id) == $vehicle->id)>
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Source</label>
                                <select name="source_module" id="source_module" class="form-select" required>
                                    @foreach(\App\Models\VehicleFinancialEntry::sourceModules() as $value => $label)
                                        <option value="{{ $value }}" @selected(old('source_module', $entry->source_module ?: 'manual') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-bold">Libellé</label>
                                <input type="text" name="label" id="label" class="form-control" value="{{ old('label', $entry->label) }}" placeholder="Ex: Carburant chantier Bafoussam">
                            </div>

                            <div class="col-md-4 source-block" data-source="tresorerie_decaissement">
                                <label class="form-label fw-bold">Décaissement Trésorerie</label>
                                <select name="depense_caisse_id" id="depense_caisse_id" class="form-select">
                                    <option value="">Choisir un décaissement</option>
                                    @foreach($decaissements as $decaissement)
                                        <option value="{{ $decaissement->id }}" data-amount="{{ $decaissement->montant }}" data-label="{{ $decaissement->libelle }}" data-date="{{ optional($decaissement->date_depense)->format('Y-m-d') }}" data-reference="{{ $decaissement->reference }}" @selected(old('depense_caisse_id', $entry->depense_caisse_id) == $decaissement->id)>
                                            {{ $decaissement->reference }} - {{ $decaissement->libelle }} ({{ number_format($decaissement->montant, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 source-block" data-source="facture">
                                <label class="form-label fw-bold">Facture client</label>
                                <select name="facture_id" id="facture_id" class="form-select">
                                    <option value="">Choisir une facture</option>
                                    @foreach($factures as $facture)
                                        <option value="{{ $facture->id }}" data-amount="{{ $facture->montant_ttc ?? $facture->montant_ht }}" data-label="Facture {{ $facture->numero ?? $facture->numero_facture ?? '#'.$facture->id }}" data-date="{{ optional($facture->date_facture)->format('Y-m-d') }}" data-reference="{{ $facture->numero ?? $facture->numero_facture ?? '#'.$facture->id }}" @selected(old('facture_id', $entry->facture_id) == $facture->id)>
                                            {{ $facture->numero ?? $facture->numero_facture ?? '#'.$facture->id }} - {{ $facture->client->raison_sociale ?? 'Client' }} ({{ number_format($facture->montant_ttc ?? $facture->montant_ht, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 source-block" data-source="tresorerie_avance">
                                <label class="form-label fw-bold">Référence avance</label>
                                <input type="text" name="external_reference" id="external_reference" class="form-control" value="{{ old('external_reference', $entry->external_reference) }}" placeholder="Ex: AV-CHANTIER-001">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Montant</label>
                                <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount', $entry->amount ?: '') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Notes</label>
                                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $entry->notes) }}</textarea>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-light border mb-0">
                                    <div class="fw-bold">Règle de calcul utilisée</div>
                                    <div class="small text-muted">Les charges ajoutent un coût réel à la mission. Le chiffre d'affaires facturé remplace le revenu pointé dès qu'au moins une facture liée existe.</div>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-light border">Annuler</a>
                                <button type="submit" class="btn btn-primary">{{ $entry->exists ? 'Mettre à jour' : 'Enregistrer' }}</button>
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
    const sourceSelect = document.getElementById('source_module');
    const entryTypeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category');
    const amountInput = document.getElementById('amount');
    const labelInput = document.getElementById('label');
    const dateInput = document.getElementById('transaction_date');
    const externalReferenceInput = document.getElementById('external_reference');
    const decaissementSelect = document.getElementById('depense_caisse_id');
    const factureSelect = document.getElementById('facture_id');

    function syncMissionVehicle() {
        const selected = missionSelect.options[missionSelect.selectedIndex];
        if (selected && selected.dataset.vehicleId && !vehicleSelect.value) {
            vehicleSelect.value = selected.dataset.vehicleId;
        }
    }

    function toggleSourceBlocks() {
        const currentSource = sourceSelect.value;
        document.querySelectorAll('.source-block').forEach(function (block) {
            block.style.display = block.dataset.source === currentSource ? '' : 'none';
        });

        if (currentSource === 'facture') {
            entryTypeSelect.value = 'revenue';
            categorySelect.value = 'invoice';
        }

        if (currentSource === 'tresorerie_decaissement' || currentSource === 'tresorerie_avance') {
            entryTypeSelect.value = 'expense';
        }
    }

    function applyOptionDefaults(selectElement) {
        const selected = selectElement.options[selectElement.selectedIndex];
        if (!selected || !selected.value) {
            return;
        }

        if (selected.dataset.amount) {
            amountInput.value = selected.dataset.amount;
        }

        if (selected.dataset.label && !labelInput.value) {
            labelInput.value = selected.dataset.label;
        }

        if (selected.dataset.date) {
            dateInput.value = selected.dataset.date;
        }

        if (selected.dataset.reference && externalReferenceInput && !externalReferenceInput.value) {
            externalReferenceInput.value = selected.dataset.reference;
        }
    }

    missionSelect.addEventListener('change', syncMissionVehicle);
    sourceSelect.addEventListener('change', toggleSourceBlocks);
    decaissementSelect.addEventListener('change', function () { applyOptionDefaults(decaissementSelect); });
    factureSelect.addEventListener('change', function () { applyOptionDefaults(factureSelect); });

    syncMissionVehicle();
    toggleSourceBlocks();
});
</script>
@endsection
