@extends('layouts.app')

@section('title', 'Nouveau Employé - KENAM SERVICES')

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-user-plus me-2"></i>Nouveau Employé
            </h2>
            <p class="text-muted mb-0">Informations de base pour le recrutement</p>
        </div>
        <div>
            <a href="{{ route('rh.employes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('rh.employes.store') }}">
                @csrf

                <!-- Informations Personnelles -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user me-2"></i>Informations Personnelles
                        </h5>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Matricule *</label>
                        <input type="text" name="matricule" class="form-control @error('matricule') is-invalid @enderror"
                               value="{{ old('matricule') }}" placeholder="Ex: EMP2024-001" required>
                        @error('matricule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade *</label>
                        <select name="grade" class="form-select @error('grade') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="Agent" {{ old('grade') == 'Agent' ? 'selected' : '' }}>Agent</option>
                            <option value="Superviseur" {{ old('grade') == 'Superviseur' ? 'selected' : '' }}>Superviseur</option>
                            <option value="Chef de Service" {{ old('grade') == 'Chef de Service' ? 'selected' : '' }}>Chef de Service</option>
                            <option value="Directeur" {{ old('grade') == 'Directeur' ? 'selected' : '' }}>Directeur</option>
                            <option value="Manager" {{ old('grade') == 'Manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                        @error('grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom') }}" placeholder="Nom de famille" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Prénoms *</label>
                        <input type="text" name="prenoms" class="form-control @error('prenoms') is-invalid @enderror"
                               value="{{ old('prenoms') }}" placeholder="Prénoms" required>
                        @error('prenoms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de Naissance</label>
                        <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror"
                               value="{{ old('date_naissance') }}">
                        @error('date_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lieu de Naissance</label>
                        <input type="text" name="lieu_naissance" class="form-control @error('lieu_naissance') is-invalid @enderror"
                               value="{{ old('lieu_naissance') }}" placeholder="Ville">
                        @error('lieu_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nationalité</label>
                        <input type="text" name="nationalite" class="form-control @error('nationalite') is-invalid @enderror"
                               value="{{ old('nationalite') ?? 'Ivoirienne' }}">
                        @error('nationalite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sexe *</label>
                        <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                        @error('sexe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Situation Familiale -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-home me-2"></i>Situation Familiale
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Situation Matrimoniale</label>
                        <select name="situation_matrimoniale" class="form-select @error('situation_matrimoniale') is-invalid @enderror">
                            <option value="">Sélectionner</option>
                            <option value="Célibataire" {{ old('situation_matrimoniale') == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                            <option value="Marié(e)" {{ old('situation_matrimoniale') == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                            <option value="Divorcé(e)" {{ old('situation_matrimoniale') == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                            <option value="Veuf(ve)" {{ old('situation_matrimoniale') == 'Veuf(ve)' ? 'selected' : '' }}>Veuf(ve)</option>
                            <option value="Concubinage" {{ old('situation_matrimoniale') == 'Concubinage' ? 'selected' : '' }}>Concubinage</option>
                        </select>
                        @error('situation_matrimoniale')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombre d'Enfants à Charge</label>
                        <input type="number" name="nb_enfants_charge" class="form-control @error('nb_enfants_charge') is-invalid @enderror"
                               value="{{ old('nb_enfants_charge') ?? 0 }}" min="0">
                        @error('nb_enfants_charge')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Contact -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-phone me-2"></i>Contact
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Téléphone Principal *</label>
                        <input type="tel" name="telephone_principal" class="form-control @error('telephone_principal') is-invalid @enderror"
                               value="{{ old('telephone_principal') }}" placeholder="Ex: 07XX XX XX XX" required>
                        @error('telephone_principal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Téléphone Secondaire</label>
                        <input type="tel" name="telephone_secondaire" class="form-control @error('telephone_secondaire') is-invalid @enderror"
                               value="{{ old('telephone_secondaire') }}" placeholder="Ex: 05XX XX XX XX">
                        @error('telephone_secondaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email Personnel</label>
                        <input type="email" name="email_personnel" class="form-control @error('email_personnel') is-invalid @enderror"
                               value="{{ old('email_personnel') }}" placeholder="email@exemple.com">
                        @error('email_personnel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Adresse de Résidence</label>
                        <textarea name="adresse_residence" class="form-control @error('adresse_residence') is-invalid @enderror"
                                  rows="2" placeholder="Adresse complète">{{ old('adresse_residence') }}</textarea>
                        @error('adresse_residence')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Pièce d'Identité -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-id-card me-2"></i>Pièce d'Identité
                        </h5>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type de Pièce</label>
                        <select name="type_piece" class="form-select @error('type_piece') is-invalid @enderror">
                            <option value="">Sélectionner</option>
                            <option value="CNI" {{ old('type_piece') == 'CNI' ? 'selected' : '' }}>Carte Nationale d'Identité</option>
                            <option value="PASSEPORT" {{ old('type_piece') == 'PASSEPORT' ? 'selected' : '' }}>Passeport</option>
                            <option value="PERMIS" {{ old('type_piece') == 'PERMIS' ? 'selected' : '' }}>Permis de Conduire</option>
                            <option value="CARTE_SEJOUR" {{ old('type_piece') == 'CARTE_SEJOUR' ? 'selected' : '' }}>Carte de Séjour</option>
                        </select>
                        @error('type_piece')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Numéro de Pièce</label>
                        <input type="text" name="numero_piece" class="form-control @error('numero_piece') is-invalid @enderror"
                               value="{{ old('numero_piece') }}" placeholder="Numéro de la pièce">
                        @error('numero_piece')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de Délivrance</label>
                        <input type="date" name="date_delivrance_piece" class="form-control @error('date_delivrance_piece') is-invalid @enderror"
                               value="{{ old('date_delivrance_piece') }}">
                        @error('date_delivrance_piece')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date d'Expiration</label>
                        <input type="date" name="expiration_piece" class="form-control @error('expiration_piece') is-invalid @enderror"
                               value="{{ old('expiration_piece') }}">
                        @error('expiration_piece')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Lieu de Délivrance</label>
                        <input type="text" name="lieu_delivrance_piece" class="form-control @error('lieu_delivrance_piece') is-invalid @enderror"
                               value="{{ old('lieu_delivrance_piece') }}" placeholder="Lieu de délivrance de la pièce">
                        @error('lieu_delivrance_piece')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Informations de Recrutement -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user-plus me-2"></i>Informations de Recrutement
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Poste *</label>
                        <input type="text" name="poste" class="form-control @error('poste') is-invalid @enderror"
                               value="{{ old('poste') }}" placeholder="Ex: Comptable, Chauffeur, Secrétaire..." required>
                        @error('poste')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date d'Embauche *</label>
                        <input type="date" name="date_embauche" class="form-control @error('date_embauche') is-invalid @enderror"
                               value="{{ old('date_embauche') ?? date('Y-m-d') }}" required>
                        @error('date_embauche')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut *</label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="ACTIF" {{ old('statut') == 'ACTIF' ? 'selected' : '' }}>Actif</option>
                            <option value="ESSAI" {{ old('statut') == 'ESSAI' ? 'selected' : '' }}>Période d'essai</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mt-3">
                        <label class="form-label">Mode de Paiement</label>
                        <select name="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                            <option value="">Sélectionner</option>
                            <option value="VIREMENT" {{ old('mode_paiement') == 'VIREMENT' ? 'selected' : '' }}>Virement Bancaire</option>
                            <option value="ESPECE" {{ old('mode_paiement') == 'ESPECE' ? 'selected' : '' }}>Espèce</option>
                            <option value="CHEQUE" {{ old('mode_paiement') == 'CHEQUE' ? 'selected' : '' }}>Chèque</option>
                            <option value="MOBILE_MONEY" {{ old('mode_paiement') == 'MOBILE_MONEY' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                        @error('mode_paiement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mt-3">
                        <label class="form-label">Fréquence de Paiement</label>
                        <select name="frequence_paiement" class="form-select @error('frequence_paiement') is-invalid @enderror">
                            <option value="MENSUEL" {{ old('frequence_paiement', 'MENSUEL') == 'MENSUEL' ? 'selected' : '' }}>Mensuel</option>
                            <option value="HEBDOMADAIRE" {{ old('frequence_paiement') == 'HEBDOMADAIRE' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="QUINZOMADAIRE" {{ old('frequence_paiement') == 'QUINZOMADAIRE' ? 'selected' : '' }}>Quinzomadaire</option>
                        </select>
                        @error('frequence_paiement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-sticky-note me-2"></i>Notes
                        </h5>
                        <div class="col-md-12">
                            <label class="form-label">Informations complémentaires</label>
                            <textarea name="observations" class="form-control @error('observations') is-invalid @enderror"
                                      rows="3" placeholder="Informations supplémentaires sur le candidat...">{{ old('observations') }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rh.employes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer l'Employé
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
