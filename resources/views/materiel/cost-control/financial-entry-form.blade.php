@extends('layouts.app')

@section('title', ($entry->exists ? 'Modifier' : 'Nouvelle') . ' charge / CA | KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-wallet me-2 text-info"></i>{{ $entry->exists ? 'Modifier' : 'Nouvelle' }} charge / chiffre d'affaires</h1>
            <p class="text-muted mb-0">Rattacher les charges chantier à la trésorerie et le CA au module Facture</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('materiel.cost-control.home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong><i class="fas fa-edit me-2"></i>{{ $entry->exists ? 'Modification' : 'Création' }} d'entrée financière</strong>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ $entry->exists ? route('materiel.cost-control.plateau.charges.update', $entry) : route('materiel.cost-control.plateau.charges.store') }}">
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
                            <option value="">Sélectionner...</option>
                            @foreach(\App\Models\VehicleFinancialEntry::categories() as $value => $label)
                                <option value="{{ $value }}" @selected(old('category', $entry->category) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="transaction_date" id="transaction_date" class="form-control" value="{{ old('transaction_date', $entry->transaction_date ?? now()->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Montant</label>
                        <div class="input-group">
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" value="{{ old('amount', $entry->amount) }}" required>
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Libellé</label>
                        <input type="text" name="label" id="label" class="form-control" value="{{ old('label', $entry->label) }}" placeholder="Description de l'opération" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Module source</label>
                        <select name="source_module" id="source_module" class="form-select">
                            <option value="">Aucun</option>
                            @foreach(\App\Models\VehicleFinancialEntry::sourceModules() as $value => $label)
                                <option value="{{ $value }}" @selected(old('source_module', $entry->source_module) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Référence externe</label>
                        <input type="text" name="external_reference" id="external_reference" class="form-control" value="{{ old('external_reference', $entry->external_reference) }}" placeholder="N° facture, N° paiement, etc.">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Informations complémentaires...">{{ old('notes', $entry->notes) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('materiel.cost-control.home') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>{{ $entry->exists ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
