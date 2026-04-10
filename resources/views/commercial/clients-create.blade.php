@extends('layouts.app')

@section('title', 'Nouveau Client')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Nouveau Client</h5>
            <a href="{{ route('commercial.clients.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('commercial.clients.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label small">Raison sociale <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required placeholder="Ex: KENAM SERVICES">
                    @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Secteur d'activité</label>
                    <input type="text" name="secteur" class="form-control @error('secteur') is-invalid @enderror" value="{{ old('secteur') }}" placeholder="Ex: Transport, Logistique">
                    @error('secteur') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="contact@entreprise.com">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Téléphone <span class="text-danger">*</span></label>
                    <input type="tel" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}" required placeholder="+225 XX XX XX XX">
                    @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Personne à contacter</label>
                    <input type="text" name="contact_principal" class="form-control @error('contact_principal') is-invalid @enderror" value="{{ old('contact_principal') }}" placeholder="Nom du contact principal">
                    @error('contact_principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Fonction</label>
                    <input type="text" name="fonction" class="form-control @error('fonction') is-invalid @enderror" value="{{ old('fonction') }}" placeholder="Ex: Directeur Commercial">
                    @error('fonction') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Type de client</label>
                    <select name="type_client" class="form-select @error('type_client') is-invalid @enderror">
                        <option value="">Sélectionner...</option>
                        <option value="particulier">Particulier</option>
                        <option value="entreprise">Entreprise</option>
                        <option value="administration">Administration</option>
                    </select>
                    @error('type_client') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Potentiel estimé (FCFA)</label>
                    <input type="number" name="potentiel" class="form-control @error('potentiel') is-invalid @enderror" value="{{ old('potentiel') }}" min="0" placeholder="Ex: 5000000">
                    @error('potentiel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label small">Adresse complète</label>
                    <textarea name="adresse" class="form-control @error('adresse') is-invalid @enderror" rows="3" placeholder="Adresse complète du client">{{ old('adresse') }}</textarea>
                    @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Ville</label>
                    <input type="text" name="ville" class="form-control @error('ville') is-invalid @enderror" value="{{ old('ville') }}" placeholder="Ex: Abidjan">
                    @error('ville') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Pays</label>
                    <input type="text" name="pays" class="form-control @error('pays') is-invalid @enderror" value="{{ old('pays') }}" placeholder="Ex: Côte d'Ivoire">
                    @error('pays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Source</label>
                    <select name="source" class="form-select @error('source') is-invalid @enderror">
                        <option value="">Sélectionner...</option>
                        <option value="prospection">Prospection</option>
                        <option value="recommandation">Recommandation</option>
                        <option value="web">Site web</option>
                        <option value="evenement">Événement</option>
                        <option value="partenaire">Partenaire</option>
                    </select>
                    @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Statut</label>
                    <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                        <option value="actif">Actif</option>
                        <option value="prospect">Prospect</option>
                        <option value="inactif">Inactif</option>
                    </select>
                    @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label small">Notes commerciales</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Informations complémentaires sur le client...">{{ old('notes') }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('commercial.clients.index') }}" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Créer le client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
