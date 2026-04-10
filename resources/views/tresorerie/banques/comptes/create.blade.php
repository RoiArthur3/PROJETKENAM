@extends('layouts.app')

@section('title', 'Nouveau Compte Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouveau Compte Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.banques.show', $banque) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="fas fa-university me-2"></i>Banque : {{ $banque->nom ?? $banque->libelle ?? '-' }}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('tresorerie.banques.comptes.store', $banque) }}">
                @csrf

                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informations du compte</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="numero_compte" class="form-label">Numéro de compte <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('numero_compte') is-invalid @enderror" id="numero_compte" name="numero_compte" value="{{ old('numero_compte') }}" required placeholder="Ex: CI001 0000 0000 0000 0000 00">
                        @error('numero_compte')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="intitule_compte" class="form-label">Intitulé du compte <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('intitule_compte') is-invalid @enderror" id="intitule_compte" name="intitule_compte" value="{{ old('intitule_compte') }}" required placeholder="Ex: Compte courant KENAM">
                        @error('intitule_compte')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="type_compte" class="form-label">Type de compte <span class="text-danger">*</span></label>
                        <select class="form-select @error('type_compte') is-invalid @enderror" id="type_compte" name="type_compte" required>
                            <option value="">Sélectionner un type</option>
                            @foreach($typesCompte as $key => $label)
                                <option value="{{ $key }}" {{ old('type_compte') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type_compte')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="devise" class="form-label">Devise <span class="text-danger">*</span></label>
                        <select class="form-select @error('devise') is-invalid @enderror" id="devise" name="devise" required>
                            <option value="">Sélectionner une devise</option>
                            @foreach($devises as $key => $label)
                                <option value="{{ $key }}" {{ old('devise', 'XOF') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('devise')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="solde_ouverture" class="form-label">Solde d'ouverture (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('solde_ouverture') is-invalid @enderror" id="solde_ouverture" name="solde_ouverture" value="{{ old('solde_ouverture', 0) }}" required min="0" step="1">
                        @error('solde_ouverture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date_ouverture" class="form-label">Date d'ouverture <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_ouverture') is-invalid @enderror" id="date_ouverture" name="date_ouverture" value="{{ old('date_ouverture', now()->format('Y-m-d')) }}" required>
                        @error('date_ouverture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date_fermeture" class="form-label">Date de fermeture</label>
                        <input type="date" class="form-control @error('date_fermeture') is-invalid @enderror" id="date_fermeture" name="date_fermeture" value="{{ old('date_fermeture') }}">
                        @error('date_fermeture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="est_actif" class="form-label">Statut</label>
                        <select class="form-select @error('est_actif') is-invalid @enderror" id="est_actif" name="est_actif">
                            <option value="1" {{ old('est_actif', '1') == '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ old('est_actif') == '0' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('est_actif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Titulaire du compte</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom_titulaire" class="form-label">Nom du titulaire <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom_titulaire') is-invalid @enderror" id="nom_titulaire" name="nom_titulaire" value="{{ old('nom_titulaire') }}" required placeholder="KENAM SERVICES">
                        @error('nom_titulaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="adresse_titulaire" class="form-label">Adresse</label>
                        <input type="text" class="form-control @error('adresse_titulaire') is-invalid @enderror" id="adresse_titulaire" name="adresse_titulaire" value="{{ old('adresse_titulaire') }}" placeholder="Adresse du titulaire">
                        @error('adresse_titulaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telephone_titulaire" class="form-label">Téléphone</label>
                        <input type="text" class="form-control @error('telephone_titulaire') is-invalid @enderror" id="telephone_titulaire" name="telephone_titulaire" value="{{ old('telephone_titulaire') }}" placeholder="+225 .. .. .. ..">
                        @error('telephone_titulaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email_titulaire" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email_titulaire') is-invalid @enderror" id="email_titulaire" name="email_titulaire" value="{{ old('email_titulaire') }}" placeholder="contact@kenamservices.net">
                        @error('email_titulaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-address-book me-2"></i>Contact bancaire</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nom_contact" class="form-label">Nom du contact</label>
                        <input type="text" class="form-control @error('nom_contact') is-invalid @enderror" id="nom_contact" name="nom_contact" value="{{ old('nom_contact') }}" placeholder="Nom du conseiller">
                        @error('nom_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="telephone_contact" class="form-label">Téléphone contact</label>
                        <input type="text" class="form-control @error('telephone_contact') is-invalid @enderror" id="telephone_contact" name="telephone_contact" value="{{ old('telephone_contact') }}" placeholder="+225 .. .. .. ..">
                        @error('telephone_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="email_contact" class="form-label">Email contact</label>
                        <input type="email" class="form-control @error('email_contact') is-invalid @enderror" id="email_contact" name="email_contact" value="{{ old('email_contact') }}" placeholder="conseiller@banque.ci">
                        @error('email_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-cog me-2"></i>Paramètres</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="decouvert_autorise" class="form-label">Découvert autorisé (FCFA)</label>
                        <input type="number" class="form-control @error('decouvert_autorise') is-invalid @enderror" id="decouvert_autorise" name="decouvert_autorise" value="{{ old('decouvert_autorise', 0) }}" min="0" step="1">
                        @error('decouvert_autorise')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="taux_interet" class="form-label">Taux d'intérêt (%)</label>
                        <input type="number" class="form-control @error('taux_interet') is-invalid @enderror" id="taux_interet" name="taux_interet" value="{{ old('taux_interet', 0) }}" min="0" max="100" step="0.01">
                        @error('taux_interet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="informations_supplementaires" class="form-label">Informations supplémentaires</label>
                        <textarea class="form-control @error('informations_supplementaires') is-invalid @enderror" id="informations_supplementaires" name="informations_supplementaires" rows="3" placeholder="Notes ou informations complémentaires">{{ old('informations_supplementaires') }}</textarea>
                        @error('informations_supplementaires')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('tresorerie.banques.show', $banque) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer le compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
