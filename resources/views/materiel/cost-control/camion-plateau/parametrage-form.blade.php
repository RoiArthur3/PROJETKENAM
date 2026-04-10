@extends('layouts.app')

@section('title', isset($param->id) ? 'Modifier Paramétrage' : 'Nouveau Paramétrage')

@section('content')
<div class="container-fluid">

    @include('materiel.cost-control.camion-plateau._nav')

    <div class="card shadow-sm" style="max-width:820px;margin:0 auto;">
        <div class="card-header bg-warning bg-gradient py-3">
            <h5 class="mb-0 text-white fw-bold">
                <i class="fas fa-{{ isset($param->id) ? 'edit' : 'plus' }} me-2"></i>
                {{ isset($param->id) ? 'Modifier le paramétrage' : 'Nouveau paramétrage Camion Plateau' }}
            </h5>
        </div>
        <div class="card-body p-4">

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ isset($param->id)
                    ? route('materiel.cost-control.plateau.parametrage.update', $param)
                    : route('materiel.cost-control.plateau.parametrage.store') }}">
                @csrf
                @if(isset($param->id)) @method('PUT') @endif

                {{-- ── Section: Engin + Client --}}
                <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="fas fa-truck me-2"></i>Engin & Client</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Engin (Camion Plateau)</label>
                        <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                            <option value="">— Choisir un engin —</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ old('vehicle_id', $param->vehicle_id) == $v->id ? 'selected' : '' }}>
                                    {{ $v->immatriculation }}{{ $v->marque ? ' – ' . $v->marque : '' }}{{ $v->modele ? ' ' . $v->modele : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Client</label>
                        <select name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                            <option value="">— Choisir un client —</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ old('client_id', $param->client_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- ── Section: Facturation client --}}
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="fas fa-file-invoice-dollar me-2"></i>Facturation Client</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Type de facturation <span class="text-danger">*</span></label>
                        <select name="type_facturation" id="type_facturation" class="form-select @error('type_facturation') is-invalid @enderror" required>
                            <option value="trip" {{ old('type_facturation', $param->type_facturation ?? 'trip') === 'trip' ? 'selected' : '' }}>À la Tâche / Voyage</option>
                            <option value="monthly" {{ old('type_facturation', $param->type_facturation) === 'monthly' ? 'selected' : '' }}>Forfait Mensuel</option>
                        </select>
                        @error('type_facturation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Champs voyage --}}
                    <div class="col-md-4" id="row-trip-price">
                        <label class="form-label fw-semibold">Prix client / voyage (FCFA)</label>
                        <input type="number" step="1" min="0" name="trip_client_price"
                               value="{{ old('trip_client_price', $param->trip_client_price ?? 0) }}"
                               class="form-control @error('trip_client_price') is-invalid @enderror">
                        @error('trip_client_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Champs mensuel --}}
                    <div class="col-md-4" id="row-monthly-flat" style="display:none;">
                        <label class="form-label fw-semibold">Forfait mensuel (FCFA)</label>
                        <input type="number" step="1" min="0" name="monthly_flat_rate"
                               value="{{ old('monthly_flat_rate', $param->monthly_flat_rate ?? 0) }}"
                               class="form-control @error('monthly_flat_rate') is-invalid @enderror">
                        @error('monthly_flat_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4" id="row-monthly-threshold" style="display:none;">
                        <label class="form-label fw-semibold">Seuil voyages inclus / mois</label>
                        <div class="input-group">
                            <input type="number" step="1" min="0" name="monthly_trip_threshold"
                                   value="{{ old('monthly_trip_threshold', $param->monthly_trip_threshold ?? 0) }}"
                                   class="form-control @error('monthly_trip_threshold') is-invalid @enderror">
                            <span class="input-group-text">voyages</span>
                        </div>
                        <small class="form-text text-muted">Au-delà de ce seuil, surcharge appliquée.</small>
                        @error('monthly_trip_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4" id="row-extra-trip" style="display:none;">
                        <label class="form-label fw-semibold">Surcharge voyage supplémentaire (FCFA)</label>
                        <input type="number" step="1" min="0" name="extra_trip_unit_price"
                               value="{{ old('extra_trip_unit_price', $param->extra_trip_unit_price ?? 0) }}"
                               class="form-control @error('extra_trip_unit_price') is-invalid @enderror">
                        @error('extra_trip_unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- Hidden zeros for unused type --}}
                    <input type="hidden" name="_type_helper" value="1">
                </div>

                {{-- ── Section: Paiement fournisseur --}}
                <h6 class="fw-bold text-danger border-bottom pb-2 mb-3"><i class="fas fa-hand-holding-usd me-2"></i>Coût Fournisseur</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Type paiement fournisseur <span class="text-danger">*</span></label>
                        <select name="supplier_type_paiement" id="supplier_type_paiement" class="form-select @error('supplier_type_paiement') is-invalid @enderror" required>
                            <option value="trip" {{ old('supplier_type_paiement', $param->supplier_type_paiement ?? 'trip') === 'trip' ? 'selected' : '' }}>Par Voyage</option>
                            <option value="monthly" {{ old('supplier_type_paiement', $param->supplier_type_paiement) === 'monthly' ? 'selected' : '' }}>Mensuel</option>
                        </select>
                        @error('supplier_type_paiement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4" id="row-supp-trip">
                        <label class="form-label fw-semibold">Coût fournisseur / voyage (FCFA)</label>
                        <input type="number" step="1" min="0" name="supplier_trip_cost"
                               value="{{ old('supplier_trip_cost', $param->supplier_trip_cost ?? 0) }}"
                               class="form-control @error('supplier_trip_cost') is-invalid @enderror">
                        @error('supplier_trip_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4" id="row-supp-monthly" style="display:none;">
                        <label class="form-label fw-semibold">Coût fournisseur mensuel (FCFA)</label>
                        <input type="number" step="1" min="0" name="supplier_monthly_cost"
                               value="{{ old('supplier_monthly_cost', $param->supplier_monthly_cost ?? 0) }}"
                               class="form-control @error('supplier_monthly_cost') is-invalid @enderror">
                        @error('supplier_monthly_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- ── Notes + Statut --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $param->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Statut</label>
                        <div class="form-check form-switch mt-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   id="is_active" {{ old('is_active', $param->is_active ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Paramétrage actif</label>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('materiel.cost-control.plateau.parametrage') }}" class="btn btn-outline-secondary">
                        {{-- <i class="fas fa-times me-1"></i>Annuler --}}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>{{ isset($param->id) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function toggleFacturation() {
        const v = document.getElementById('type_facturation').value;
        const monthly = v === 'monthly';
        document.getElementById('row-trip-price').style.display      = monthly ? 'none' : '';
        document.getElementById('row-monthly-flat').style.display     = monthly ? '' : 'none';
        document.getElementById('row-monthly-threshold').style.display = monthly ? '' : 'none';
        document.getElementById('row-extra-trip').style.display       = monthly ? '' : 'none';
    }

    function toggleSupplier() {
        const v = document.getElementById('supplier_type_paiement').value;
        document.getElementById('row-supp-trip').style.display    = v === 'trip' ? '' : 'none';
        document.getElementById('row-supp-monthly').style.display = v === 'monthly' ? '' : 'none';
    }

    document.getElementById('type_facturation').addEventListener('change', toggleFacturation);
    document.getElementById('supplier_type_paiement').addEventListener('change', toggleSupplier);
    toggleFacturation();
    toggleSupplier();
})();
</script>
@endpush
