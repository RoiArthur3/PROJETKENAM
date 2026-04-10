@extends('layouts.app')

@section('title', 'Modifier Compte Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier le Compte Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.banques.show', $banque) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0"><i class="fas fa-university me-2"></i>Banque : {{ $banque->nom ?? $banque->libelle ?? '-' }} — Compte : {{ $compte->numero_compte }}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('tresorerie.banques.comptes.update', [$banque, $compte]) }}">
                @csrf
                @method('PUT')

                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informations du compte</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="numero_compte" class="form-label">Numéro de compte</label>
                        <input type="text" class="form-control" id="numero_compte" value="{{ $compte->numero_compte }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="intitule_compte" class="form-label">Intitulé du compte <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('intitule_compte') is-invalid @enderror" id="intitule_compte" name="intitule_compte" value="{{ old('intitule_compte', $compte->intitule_compte) }}" required>
                        @error('intitule_compte')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="type_compte" class="form-label">Type de compte <span class="text-danger">*</span></label>
                        <select class="form-select @error('type_compte') is-invalid @enderror" id="type_compte" name="type_compte" required>
                            @foreach($typesCompte as $key => $label)
                                <option value="{{ $key }}" {{ old('type_compte', $compte->type_compte) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type_compte')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="devise" class="form-label">Devise <span class="text-danger">*</span></label>
                        <select class="form-select @error('devise') is-invalid @enderror" id="devise" name="devise" required>
                            @foreach($devises as $key => $label)
                                <option value="{{ $key }}" {{ old('devise', $compte->devise) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('devise')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="solde_ouverture" class="form-label">Solde d'ouverture (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('solde_ouverture') is-invalid @enderror" id="solde_ouverture" name="solde_ouverture" value="{{ old('solde_ouverture', $compte->solde_ouverture) }}" required min="0" step="1">
                        @error('solde_ouverture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date_ouverture" class="form-label">Date d'ouverture <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_ouverture') is-invalid @enderror" id="date_ouverture" name="date_ouverture" value="{{ old('date_ouverture', $compte->date_ouverture ? \Carbon\Carbon::parse($compte->date_ouverture)->format('Y-m-d') : '') }}" required>
                        @error('date_ouverture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date_fermeture" class="form-label">Date de fermeture</label>
                        <input type="date" class="form-control @error('date_fermeture') is-invalid @enderror" id="date_fermeture" name="date_fermeture" value="{{ old('date_fermeture', $compte->date_fermeture ? \Carbon\Carbon::parse($compte->date_fermeture)->format('Y-m-d') : '') }}">
                        @error('date_fermeture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="est_actif" class="form-label">Statut</label>
                        <select class="form-select @error('est_actif') is-invalid @enderror" id="est_actif" name="est_actif">
                            <option value="1" {{ old('est_actif', $compte->est_actif) == '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ old('est_actif', $compte->est_actif) == '0' ? 'selected' : '' }}>Inactif</option>
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
                        <input type="text" class="form-control @error('nom_titulaire') is-invalid @enderror" id="nom_titulaire" name="nom_titulaire" value="{{ old('nom_titulaire', $compte->nom_titulaire) }}" required>
                        @error('nom_titulaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="adresse_titulaire" class="form-label">Adresse</label>
                        <input type="text" class="form-control @error('adresse_titulaire') is-invalid @enderror" id="adresse_titulaire" name="adresse_titulaire" value="{{ old('adresse_titulaire', $compte->adresse_titulaire) }}">
                        @error('adresse_titulaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telephone_titulaire" class="form-label">Téléphone</label>
                        <input type="text" class="form-control @error('telephone_titulaire') is-invalid @enderror" id="telephone_titulaire" name="telephone_titulaire" value="{{ old('telephone_titulaire', $compte->telephone_titulaire) }}">
                        @error('telephone_titulaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email_titulaire" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email_titulaire') is-invalid @enderror" id="email_titulaire" name="email_titulaire" value="{{ old('email_titulaire', $compte->email_titulaire) }}">
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
                        <input type="text" class="form-control @error('nom_contact') is-invalid @enderror" id="nom_contact" name="nom_contact" value="{{ old('nom_contact', $compte->nom_contact) }}">
                        @error('nom_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="telephone_contact" class="form-label">Téléphone contact</label>
                        <input type="text" class="form-control @error('telephone_contact') is-invalid @enderror" id="telephone_contact" name="telephone_contact" value="{{ old('telephone_contact', $compte->telephone_contact) }}">
                        @error('telephone_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="email_contact" class="form-label">Email contact</label>
                        <input type="email" class="form-control @error('email_contact') is-invalid @enderror" id="email_contact" name="email_contact" value="{{ old('email_contact', $compte->email_contact) }}">
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
                        <input type="number" class="form-control @error('decouvert_autorise') is-invalid @enderror" id="decouvert_autorise" name="decouvert_autorise" value="{{ old('decouvert_autorise', $compte->decouvert_autorise) }}" min="0" step="1">
                        @error('decouvert_autorise')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="taux_interet" class="form-label">Taux d'intérêt (%)</label>
                        <input type="number" class="form-control @error('taux_interet') is-invalid @enderror" id="taux_interet" name="taux_interet" value="{{ old('taux_interet', $compte->taux_interet) }}" min="0" max="100" step="0.01">
                        @error('taux_interet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="informations_supplementaires" class="form-label">Informations supplémentaires</label>
                        <textarea class="form-control @error('informations_supplementaires') is-invalid @enderror" id="informations_supplementaires" name="informations_supplementaires" rows="3">{{ old('informations_supplementaires', $compte->informations_supplementaires) }}</textarea>
                        @error('informations_supplementaires')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('tresorerie.banques.show', $banque) }}" class="btn btn-outline-secondary">
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
@endsection
