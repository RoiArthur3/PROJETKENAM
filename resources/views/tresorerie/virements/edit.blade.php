@extends('layouts.app')

@section('title', 'Modifier Virement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier le Virement {{ $virement->reference }}</h1>
        <a href="{{ route('tresorerie.virements.show', $virement) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('tresorerie.virements.update', $virement) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="compte_source_id" class="form-label">Compte source <span class="text-danger">*</span></label>
                        <select class="form-select @error('compte_source_id') is-invalid @enderror" id="compte_source_id" name="compte_source_id" required>
                            <option value="">Sélectionner un compte</option>
                            @foreach($comptes as $banque => $comptesGroup)
                                <optgroup label="{{ $banque }}">
                                    @foreach($comptesGroup as $compte)
                                        <option value="{{ $compte->id }}" {{ old('compte_source_id', $virement->compte_source_id) == $compte->id ? 'selected' : '' }}>
                                            {{ $compte->intitule_compte ?? $compte->numero_compte }} — {{ number_format($compte->solde ?? 0, 0, ',', ' ') }} FCFA
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('compte_source_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="compte_destination_id" class="form-label">Compte destination <span class="text-danger">*</span></label>
                        <select class="form-select @error('compte_destination_id') is-invalid @enderror" id="compte_destination_id" name="compte_destination_id" required>
                            <option value="">Sélectionner un compte</option>
                            @foreach($comptes as $banque => $comptesGroup)
                                <optgroup label="{{ $banque }}">
                                    @foreach($comptesGroup as $compte)
                                        <option value="{{ $compte->id }}" {{ old('compte_destination_id', $virement->compte_destination_id) == $compte->id ? 'selected' : '' }}>
                                            {{ $compte->intitule_compte ?? $compte->numero_compte }} — {{ number_format($compte->solde ?? 0, 0, ',', ' ') }} FCFA
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('compte_destination_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="montant" class="form-label">Montant (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('montant') is-invalid @enderror" id="montant" name="montant" value="{{ old('montant', $virement->montant) }}" required min="1" step="1">
                        @error('montant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date_virement" class="form-label">Date de virement <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_virement" name="date_virement" value="{{ old('date_virement', $virement->date_virement ? \Carbon\Carbon::parse($virement->date_virement)->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="frais" class="form-label">Frais (FCFA)</label>
                        <input type="number" class="form-control" id="frais" name="frais" value="{{ old('frais', $virement->frais ?? 0) }}" min="0" step="1">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="motif" class="form-label">Motif <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="motif" name="motif" value="{{ old('motif', $virement->motif) }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $virement->notes) }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('tresorerie.virements.show', $virement) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
