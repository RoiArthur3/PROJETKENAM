@extends('layouts.app')

@section('title', 'Nouvelle Opération Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouvelle Opération Bancaire</h1>
        <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulaire d'Enregistrement</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('tresorerie.banque.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="caisse_id" class="form-label font-weight-bold">Banque / Compte</label>
                                <select name="caisse_id" id="caisse_id" class="form-select @error('caisse_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner une banque</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}" {{ old('caisse_id') == $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom }} ({{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                                @error('caisse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_mouvement" class="form-label font-weight-bold">Date de l'opération</label>
                                <input type="datetime-local" name="date_mouvement" id="date_mouvement" 
                                       class="form-control @error('date_mouvement') is-invalid @enderror" 
                                       value="{{ old('date_mouvement', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('date_mouvement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="libelle" class="form-label font-weight-bold">Libellé / Objet</label>
                            <input type="text" name="libelle" id="libelle" 
                                   class="form-control @error('libelle') is-invalid @enderror" 
                                   value="{{ old('libelle') }}" placeholder="Ex: Virement reçu client X" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type_mouvement" class="form-label font-weight-bold">Type d'opération</label>
                                <select name="type_mouvement" id="type_mouvement" class="form-select @error('type_mouvement') is-invalid @enderror" required>
                                    <option value="entree" {{ old('type_mouvement') == 'entree' ? 'selected' : '' }}>ENTRÉE (CRÉDIT)</option>
                                    <option value="sortie" {{ old('type_mouvement') == 'sortie' ? 'selected' : '' }}>SORTIE (DÉBIT)</option>
                                </select>
                                @error('type_mouvement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="montant" class="form-label font-weight-bold">Montant (FCFA)</label>
                                <div class="input-group">
                                    <input type="number" name="montant" id="montant" 
                                           class="form-control @error('montant') is-invalid @enderror" 
                                           value="{{ old('montant') }}" min="0" required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label font-weight-bold">Description / Notes</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Informations complémentaires...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer l'opération
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
