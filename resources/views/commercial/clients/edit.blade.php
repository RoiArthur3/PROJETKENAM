@extends('layouts.app')

@section('title', 'Commercial - Modifier client | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Modifier le client #{{ $client->id ?? '-' }}
                    </h5>
                    <a href="{{ route('commercial.clients.index') }}" class="btn btn-light btn-sm text-warning">
                        <i class="fas fa-arrow-left me-1"></i>Retour a la liste
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commercial.clients.update', $client->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom de la societe <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $client->nom ?? $client->raison_sociale ?? $client->company_name ?? '') }}"
                                   placeholder="Ex: Societe ABC" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Contact principal <span class="text-danger">*</span></label>
                            <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                                   value="{{ old('contact', $client->contact ?? $client->contact_person ?? $client->contact_nom ?? '') }}"
                                   placeholder="Nom du contact" required>
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $client->email ?? '') }}" placeholder="contact@client.ci" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telephone</label>
                                <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                       value="{{ old('telephone', $client->telephone ?? '') }}" placeholder="+225 .. .. .. ..">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ville</label>
                                <input type="text" name="ville" class="form-control @error('ville') is-invalid @enderror"
                                       value="{{ old('ville', $client->ville ?? $client->city ?? '') }}" placeholder="Abidjan">
                                @error('ville')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Categorie</label>
                                <select name="categorie" class="form-select @error('categorie') is-invalid @enderror">
                                    @php($selectedCategorie = old('categorie', $client->categorie ?? ''))
                                    <option value="">Selectionner une categorie</option>
                                    <option value="Transport" {{ $selectedCategorie == 'Transport' ? 'selected' : '' }}>Transport</option>
                                    <option value="BTP" {{ $selectedCategorie == 'BTP' ? 'selected' : '' }}>BTP</option>
                                    <option value="Commerce" {{ $selectedCategorie == 'Commerce' ? 'selected' : '' }}>Commerce</option>
                                    <option value="Industrie" {{ $selectedCategorie == 'Industrie' ? 'selected' : '' }}>Industrie</option>
                                    <option value="Services" {{ $selectedCategorie == 'Services' ? 'selected' : '' }}>Services</option>
                                    <option value="Autre" {{ $selectedCategorie == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Type de client</label>
                                @php($selectedType = old('type', $client->type ?? 'entreprise'))
                                <select name="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="entreprise" {{ $selectedType == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                                    <option value="particulier" {{ $selectedType == 'particulier' ? 'selected' : '' }}>Particulier</option>
                                    <option value="administration" {{ $selectedType == 'administration' ? 'selected' : '' }}>Administration</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Statut</label>
                                @php($selectedStatut = old('statut', $client->statut ?? 'actif'))
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                                    <option value="actif" {{ $selectedStatut == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="prospect" {{ $selectedStatut == 'prospect' ? 'selected' : '' }}>Prospect</option>
                                    <option value="inactif" {{ $selectedStatut == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Adresse</label>
                            <input type="text" name="adresse" class="form-control @error('adresse') is-invalid @enderror"
                                   value="{{ old('adresse', $client->adresse ?? '') }}" placeholder="Adresse complete">
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3" placeholder="Informations supplementaires sur le client...">{{ old('notes', $client->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('commercial.clients.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Mettre a jour le client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
