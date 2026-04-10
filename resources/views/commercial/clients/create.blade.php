@extends('layouts.app')

@section('title', 'Commercial - Nouveau client | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Nouveau client
                    </h5>
                    <a href="{{ route('commercial.clients.index') }}" class="btn btn-light btn-sm text-success">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commercial.clients.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom de la société <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" placeholder="Ex: Société ABC" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Contact principal <span class="text-danger">*</span></label>
                            <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror" value="{{ old('contact') }}" placeholder="Nom du contact" required>
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="contact@client.ci" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Téléphone <span class="text-danger">*</span></label>
                                <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}" placeholder="+225 .. .. .. .." required>
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ville <span class="text-danger">*</span></label>
                                <input type="text" name="ville" class="form-control @error('ville') is-invalid @enderror" value="{{ old('ville') }}" placeholder="Abidjan" required>
                                @error('ville')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Catégorie <span class="text-danger">*</span></label>
                                <select name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="Transport" {{ old('categorie') == 'Transport' ? 'selected' : '' }}>Transport</option>
                                    <option value="BTP" {{ old('categorie') == 'BTP' ? 'selected' : '' }}>BTP</option>
                                    <option value="Commerce" {{ old('categorie') == 'Commerce' ? 'selected' : '' }}>Commerce</option>
                                    <option value="Industrie" {{ old('categorie') == 'Industrie' ? 'selected' : '' }}>Industrie</option>
                                    <option value="Services" {{ old('categorie') == 'Services' ? 'selected' : '' }}>Services</option>
                                    <option value="Autre" {{ old('categorie') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Type de client</label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="entreprise" {{ old('type') == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                                    <option value="particulier" {{ old('type') == 'particulier' ? 'selected' : '' }}>Particulier</option>
                                    <option value="administration" {{ old('type') == 'administration' ? 'selected' : '' }}>Administration</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Statut</label>
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                                    <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="prospect" {{ old('statut') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                                    <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Adresse</label>
                            <input type="text" name="adresse" class="form-control @error('adresse') is-invalid @enderror" value="{{ old('adresse') }}" placeholder="Adresse complète">
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3" placeholder="Informations supplémentaires sur le client...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('commercial.clients.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Enregistrer le client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
