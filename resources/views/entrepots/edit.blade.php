@extends('layouts.app')

@section('title', 'Modifier Entrepôt - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-warehouse me-2"></i>Modifier l'Entrepôt
        </h1>
        <a href="{{ route('stock.entrepots') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0">
                        <i class="fas fa-edit me-2"></i>Formulaire de modification
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock.entrepots.update', $entrepot) }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $entrepot->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" name="nom" value="{{ old('nom', $entrepot->nom) }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="3">{{ old('adresse', $entrepot->adresse) }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control @error('responsable') is-invalid @enderror" 
                                       id="responsable" name="responsable" value="{{ old('responsable', $entrepot->responsable) }}">
                                @error('responsable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                       id="telephone" name="telephone" value="{{ old('telephone', $entrepot->telephone) }}">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="capacite" class="form-label">Capacité</label>
                            <input type="number" class="form-control @error('capacite') is-invalid @enderror" 
                                   id="capacite" name="capacite" value="{{ old('capacite', $entrepot->capacite) }}" min="0">
                            @error('capacite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('actif') is-invalid @enderror" 
                                       type="checkbox" id="actif" name="actif" value="1" {{ old('actif', $entrepot->actif) ? 'checked' : '' }}>
                                <label class="form-check-label" for="actif">
                                    Entrepôt actif
                                </label>
                            </div>
                            @error('actif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stock.entrepots') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
