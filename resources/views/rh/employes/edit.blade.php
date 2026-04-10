@extends('layouts.app')

@section('title', 'Modifier Employé - Personnel RH - KENAM SERVICES')

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-user-edit me-2"></i>Modifier Employé
            </h2>
            <p class="text-muted mb-0">Modifier les informations de {{ $employe->nom }} {{ $employe->prenoms }}</p>
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
            <form method="POST" action="{{ route('rh.employes.update', $employe->id) }}">
                @csrf
                @method('PUT')

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
                               value="{{ old('matricule', $employe->matricule) }}" placeholder="Ex: EMP2024-001" required>
                        @error('matricule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade *</label>
                        <select name="grade" class="form-select @error('grade') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="Agent" {{ old('grade', $employe->grade) == 'Agent' ? 'selected' : '' }}>Agent</option>
                            <option value="Superviseur" {{ old('grade', $employe->grade) == 'Superviseur' ? 'selected' : '' }}>Superviseur</option>
                            <option value="Chef de Service" {{ old('grade', $employe->grade) == 'Chef de Service' ? 'selected' : '' }}>Chef de Service</option>
                            <option value="Directeur" {{ old('grade', $employe->grade) == 'Directeur' ? 'selected' : '' }}>Directeur</option>
                            <option value="Manager" {{ old('grade', $employe->grade) == 'Manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                        @error('grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom', $employe->nom) }}" placeholder="Nom de famille" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Prénoms *</label>
                        <input type="text" name="prenoms" class="form-control @error('prenoms') is-invalid @enderror"
                               value="{{ old('prenoms', $employe->prenoms) }}" placeholder="Prénoms" required>
                        @error('prenoms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de Naissance</label>
                        <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror"
                               value="{{ old('date_naissance', optional($employe->date_naissance)->format('Y-m-d')) }}">
                        @error('date_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lieu de Naissance</label>
                        <input type="text" name="lieu_naissance" class="form-control @error('lieu_naissance') is-invalid @enderror"
                               value="{{ old('lieu_naissance', $employe->lieu_naissance) }}" placeholder="Ville">
                        @error('lieu_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nationalité</label>
                        <input type="text" name="nationalite" class="form-control @error('nationalite') is-invalid @enderror"
                               value="{{ old('nationalite', $employe->nationalite ?? 'Ivoirienne') }}">
                        @error('nationalite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sexe *</label>
                        <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="M" {{ old('sexe', $employe->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe', $employe->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
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
                            <option value="Célibataire" {{ old('situation_matrimoniale', $employe->situation_matrimoniale) == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                            <option value="Marié(e)" {{ old('situation_matrimoniale', $employe->situation_matrimoniale) == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                            <option value="Divorcé(e)" {{ old('situation_matrimoniale', $employe->situation_matrimoniale) == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                            <option value="Veuf(ve)" {{ old('situation_matrimoniale', $employe->situation_matrimoniale) == 'Veuf(ve)' ? 'selected' : '' }}>Veuf(ve)</option>
                            <option value="Concubinage" {{ old('situation_matrimoniale', $employe->situation_matrimoniale) == 'Concubinage' ? 'selected' : '' }}>Concubinage</option>
                        </select>
                        @error('situation_matrimoniale')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombre d'Enfants à Charge</label>
                        <input type="number" name="nb_enfants_charge" class="form-control @error('nb_enfants_charge') is-invalid @enderror"
                               value="{{ old('nb_enfants_charge', $employe->nb_enfants_charge ?? 0) }}" min="0">
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
                               value="{{ old('telephone_principal', $employe->telephone_principal) }}" placeholder="Ex: 07XX XX XX XX" required>
                        @error('telephone_principal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Téléphone Secondaire</label>
                        <input type="tel" name="telephone_secondaire" class="form-control @error('telephone_secondaire') is-invalid @enderror"
                               value="{{ old('telephone_secondaire', $employe->telephone_secondaire) }}" placeholder="Ex: 05XX XX XX XX">
                        @error('telephone_secondaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email Personnel</label>
                        <input type="email" name="email_personnel" class="form-control @error('email_personnel') is-invalid @enderror"
                               value="{{ old('email_personnel', $employe->email_personnel) }}" placeholder="email@exemple.com">
                        @error('email_personnel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Adresse de Résidence</label>
                        <textarea name="adresse_residence" class="form-control @error('adresse_residence') is-invalid @enderror"
                                  rows="2" placeholder="Adresse complète">{{ old('adresse_residence', $employe->adresse_residence) }}</textarea>
                        @error('adresse_residence')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Informations Professionnelles -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-briefcase me-2"></i>Informations Professionnelles
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Service *</label>
                        <input type="text" name="service" class="form-control @error('service') is-invalid @enderror"
                               value="{{ old('service', $employe->service) }}" placeholder="Ex: Administration, Production, Commercial..." required>
                        @error('service')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Poste *</label>
                        <input type="text" name="poste" class="form-control @error('poste') is-invalid @enderror"
                               value="{{ old('poste', $employe->poste) }}" placeholder="Ex: Comptable, Chauffeur, Secrétaire..." required>
                        @error('poste')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Niveau Hiérarchique</label>
                        <select name="niveau_hierarchique" class="form-select @error('niveau_hierarchique') is-invalid @enderror">
                            <option value="">Sélectionner</option>
                            <option value="Employé" {{ old('niveau_hierarchique', $employe->niveau_hierarchique) == 'Employé' ? 'selected' : '' }}>Employé</option>
                            <option value="Maîtrise" {{ old('niveau_hierarchique', $employe->niveau_hierarchique) == 'Maîtrise' ? 'selected' : '' }}>Maîtrise</option>
                            <option value="Cadre" {{ old('niveau_hierarchique', $employe->niveau_hierarchique) == 'Cadre' ? 'selected' : '' }}>Cadre</option>
                            <option value="Cadre Supérieur" {{ old('niveau_hierarchique', $employe->niveau_hierarchique) == 'Cadre Supérieur' ? 'selected' : '' }}>Cadre Supérieur</option>
                        </select>
                        @error('niveau_hierarchique')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date d'Embauche *</label>
                        <input type="date" name="date_embauche" class="form-control @error('date_embauche') is-invalid @enderror"
                               value="{{ old('date_embauche', $employe->date_embauche ? $employe->date_embauche->format('Y-m-d') : '') }}" required>
                        @error('date_embauche')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type de Contrat *</label>
                        <select name="type_contrat" class="form-select @error('type_contrat') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="CDI" {{ old('type_contrat', $employe->type_contrat) == 'CDI' ? 'selected' : '' }}>CDI - Contrat à Durée Indéterminée</option>
                            <option value="CDD" {{ old('type_contrat', $employe->type_contrat) == 'CDD' ? 'selected' : '' }}>CDD - Contrat à Durée Déterminée</option>
                            <option value="JOURNALIER" {{ old('type_contrat', $employe->type_contrat) == 'JOURNALIER' ? 'selected' : '' }}>Journalier</option>
                            <option value="STAGIAIRE" {{ old('type_contrat', $employe->type_contrat) == 'STAGIAIRE' ? 'selected' : '' }}>Stagiaire</option>
                            <option value="APPRENTI" {{ old('type_contrat', $employe->type_contrat) == 'APPRENTI' ? 'selected' : '' }}>Apprenti</option>
                            <option value="TEMPORAIRE" {{ old('type_contrat', $employe->type_contrat) == 'TEMPORAIRE' ? 'selected' : '' }}>Temporaire</option>
                            <option value="INTERIM" {{ old('type_contrat', $employe->type_contrat) == 'INTERIM' ? 'selected' : '' }}>Intérimaire</option>
                        </select>
                        @error('type_contrat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut *</label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="ACTIF" {{ old('statut', $employe->statut) == 'ACTIF' ? 'selected' : '' }}>Actif</option>
                            <option value="ESSAI" {{ old('statut', $employe->statut) == 'ESSAI' ? 'selected' : '' }}>Période d'essai</option>
                            <option value="CONGE" {{ old('statut', $employe->statut) == 'CONGE' ? 'selected' : '' }}>Congé</option>
                            <option value="SUSPENDU" {{ old('statut', $employe->statut) == 'SUSPENDU' ? 'selected' : '' }}>Suspendu</option>
                            <option value="DEPART" {{ old('statut', $employe->statut) == 'DEPART' ? 'selected' : '' }}>Départ</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Rémunération -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-money-bill-wave me-2"></i>Rémunération
                        </h5>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Salaire de Base (FCFA)</label>
                        <input type="number" name="salaire_base" class="form-control @error('salaire_base') is-invalid @enderror"
                               value="{{ old('salaire_base', $employe->salaire_base) }}" min="0" step="1000" placeholder="Ex: 150000">
                        @error('salaire_base')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Salaire Journalier (FCFA)</label>
                        <input type="number" name="salaire_journalier" class="form-control @error('salaire_journalier') is-invalid @enderror"
                               value="{{ old('salaire_journalier', $employe->salaire_journalier) }}" min="0" step="500" placeholder="Pour journaliers uniquement">
                        @error('salaire_journalier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mode de Paiement</label>
                        <select name="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                            <option value="">Sélectionner</option>
                            <option value="VIREMENT" {{ old('mode_paiement', $employe->mode_paiement) == 'VIREMENT' ? 'selected' : '' }}>Virement Bancaire</option>
                            <option value="ESPECE" {{ old('mode_paiement', $employe->mode_paiement) == 'ESPECE' ? 'selected' : '' }}>Espèce</option>
                            <option value="CHEQUE" {{ old('mode_paiement', $employe->mode_paiement) == 'CHEQUE' ? 'selected' : '' }}>Chèque</option>
                            <option value="MOBILE_MONEY" {{ old('mode_paiement', $employe->mode_paiement) == 'MOBILE_MONEY' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                        @error('mode_paiement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Observations -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-comment me-2"></i>Observations
                        </h5>
                        <div class="col-md-12">
                            <label class="form-label">Notes ou Observations</label>
                            <textarea name="observations" class="form-control @error('observations') is-invalid @enderror"
                                      rows="3" placeholder="Informations supplémentaires sur l'employé...">{{ old('observations', $employe->observations) }}</textarea>
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
                                <i class="fas fa-save me-1"></i>Mettre à jour l'Employé
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
