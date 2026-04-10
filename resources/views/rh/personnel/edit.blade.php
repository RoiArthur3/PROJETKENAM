@extends('layouts.app')

@section('title', 'Modifier Personnel - ' . $personnel->nom . ' ' . $personnel->prenoms)

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-user-edit me-2"></i>Modifier Personnel
            </h2>
            <small class="text-muted">{{ $personnel->nom }} {{ $personnel->prenoms }} | Matricule: {{ $personnel->matricule }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rh.personnel.show', $personnel->id) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-1"></i>Voir les détails
            </a>
            <a href="{{ route('rh.personnel.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('rh.personnel.update', $personnel->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Navigation par onglets -->
        <ul class="nav nav-tabs mb-4" id="personnelTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="infos-tab" data-bs-toggle="tab" data-bs-target="#infos" type="button" role="tab">
                    <i class="fas fa-user me-1"></i>Informations Personnelles
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="professionnel-tab" data-bs-toggle="tab" data-bs-target="#professionnel" type="button" role="tab">
                    <i class="fas fa-briefcase me-1"></i>Informations Professionnelles
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contrat-tab" data-bs-toggle="tab" data-bs-target="#contrat" type="button" role="tab">
                    <i class="fas fa-file-contract me-1"></i>Contrat & Rémunération
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cnps-tab" data-bs-toggle="tab" data-bs-target="#cnps" type="button" role="tab">
                    <i class="fas fa-shield-alt me-1"></i>CNPS & Fiscalité
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="banque-tab" data-bs-toggle="tab" data-bs-target="#banque" type="button" role="tab">
                    <i class="fas fa-university me-1"></i>Banque
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="urgence-tab" data-bs-toggle="tab" data-bs-target="#urgence" type="button" role="tab">
                    <i class="fas fa-phone-alt me-1"></i>Contact d'Urgence
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sante-tab" data-bs-toggle="tab" data-bs-target="#sante" type="button" role="tab">
                    <i class="fas fa-heartbeat me-1"></i>Santé
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                    <i class="fas fa-file me-1"></i>Documents
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="statut-tab" data-bs-toggle="tab" data-bs-target="#statut" type="button" role="tab">
                    <i class="fas fa-cog me-1"></i>Statut & Administration
                </button>
            </li>
        </ul>

        <div class="tab-content" id="personnelTabsContent">
            <!-- Onglet 1: Informations Personnelles -->
            <div class="tab-pane fade show active" id="infos" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                <label class="form-label">Photo de profil</label>
                                <div class="d-flex flex-column align-items-center">
                                    <div id="photoPreview" class="mb-2">
                                        @if($personnel->photo_profil)
                                            <img src="{{ asset('storage/' . $personnel->photo_profil) }}"
                                                 alt="Photo actuelle" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                                 style="width: 120px; height: 120px;">
                                                <i class="fas fa-user text-white fa-3x"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="photo_profil" id="photo_profil" class="form-control" accept="image/*" onchange="previewPhoto(event)">
                                    <small class="text-muted">Laissez vide pour garder l'actuelle</small>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Matricule *</label>
                                        <input type="text" name="matricule" class="form-control @error('matricule') is-invalid @enderror"
                                               value="{{ old('matricule', $personnel->matricule) }}" required>
                                        @error('matricule')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nom *</label>
                                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                               value="{{ old('nom', $personnel->nom) }}" required>
                                        @error('nom')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">Prénoms *</label>
                                        <input type="text" name="prenoms" class="form-control @error('prenoms') is-invalid @enderror"
                                               value="{{ old('prenoms', $personnel->prenoms) }}" required>
                                        @error('prenoms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Date de naissance *</label>
                                        <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror"
                                               value="{{ old('date_naissance', optional($personnel->date_naissance)->format('Y-m-d')) }}" required>
                                        @error('date_naissance')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Lieu de naissance *</label>
                                        <input type="text" name="lieu_naissance" class="form-control @error('lieu_naissance') is-invalid @enderror"
                                               value="{{ old('lieu_naissance', $personnel->lieu_naissance) }}" required>
                                        @error('lieu_naissance')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nationalité *</label>
                                        <select name="nationalite" class="form-select @error('nationalite') is-invalid @enderror" required>
                                            <option value="">Sélectionner...</option>
                                            <option value="Ivoirienne" {{ old('nationalite', $personnel->nationalite) == 'Ivoirienne' ? 'selected' : '' }}>Ivoirienne</option>
                                            <option value="Française" {{ old('nationalite', $personnel->nationalite) == 'Française' ? 'selected' : '' }}>Française</option>
                                            <option value="Malienne" {{ old('nationalite', $personnel->nationalite) == 'Malienne' ? 'selected' : '' }}>Malienne</option>
                                            <option value="Burkinabé" {{ old('nationalite', $personnel->nationalite) == 'Burkinabé' ? 'selected' : '' }}>Burkinabé</option>
                                            <option value="Autre" {{ old('nationalite', $personnel->nationalite) == 'Autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('nationalite')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Sexe *</label>
                                        <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                                            <option value="">Sélectionner...</option>
                                            <option value="M" {{ old('sexe', $personnel->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                                            <option value="F" {{ old('sexe', $personnel->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                                        </select>
                                        @error('sexe')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Situation matrimoniale *</label>
                                        <select name="situation_matrimoniale" class="form-select @error('situation_matrimoniale') is-invalid @enderror" required>
                                            <option value="">Sélectionner...</option>
                                            <option value="Célibataire" {{ old('situation_matrimoniale', $personnel->situation_matrimoniale) == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                                            <option value="Marié(e)" {{ old('situation_matrimoniale', $personnel->situation_matrimoniale) == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                                            <option value="Divorcé(e)" {{ old('situation_matrimoniale', $personnel->situation_matrimoniale) == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                                            <option value="Veuf(ve)" {{ old('situation_matrimoniale', $personnel->situation_matrimoniale) == 'Veuf(ve)' ? 'selected' : '' }}>Veuf(ve)</option>
                                        </select>
                                        @error('situation_matrimoniale')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Nb. enfants</label>
                                        <input type="number" name="nb_enfants_charge" class="form-control"
                                               value="{{ old('nb_enfants_charge', $personnel->nb_enfants_charge) }}" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Identification Caméra -->
                        <hr>
                        <h6><i class="fas fa-camera me-2"></i>Identification Caméra</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ID Personnel Caméra</label>
                                <input type="text" name="camera_person_id" class="form-control @error('camera_person_id') is-invalid @enderror"
                                       value="{{ old('camera_person_id', $personnel->camera_person_id) }}" placeholder="ID de la caméra">
                                <small class="text-muted">
                                    ID pour la reconnaissance faciale sur la caméra
                                    @if(!$personnel->camera_person_id && $personnel->photo_profil)
                                        <br><span class="text-info">📷 Sera généré automatiquement si vous laissez vide</span>
                                    @endif
                                </small>
                                @error('camera_person_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Statut synchronisation</label>
                                <div class="form-control bg-light">
                                    @if($personnel->camera_person_id)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Configuré ({{ $personnel->camera_person_id }})
                                        </span>
                                    @elseif($personnel->photo_profil)
                                        <span class="badge bg-info">
                                            <i class="fas fa-clock me-1"></i>Photo OK, ID manquant
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Photo requise
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Coordonnées -->
                        <hr>
                        <h6><i class="fas fa-map-marker-alt me-2"></i>Coordonnées</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Téléphone principal *</label>
                                <input type="tel" name="telephone_principal" class="form-control @error('telephone_principal') is-invalid @enderror"
                                       value="{{ old('telephone_principal', $personnel->telephone_principal) }}" required>
                                @error('telephone_principal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Téléphone secondaire</label>
                                <input type="tel" name="telephone_secondaire" class="form-control" value="{{ old('telephone_secondaire', $personnel->telephone_secondaire) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Email personnel</label>
                                <input type="email" name="email_personnel" class="form-control @error('email_personnel') is-invalid @enderror"
                                       value="{{ old('email_personnel', $personnel->email_personnel) }}">
                                @error('email_personnel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Ville *</label>
                                <input type="text" name="ville" class="form-control @error('ville') is-invalid @enderror"
                                       value="{{ old('ville', $personnel->ville) }}" required>
                                @error('ville')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adresse de résidence *</label>
                                <textarea name="adresse_residence" class="form-control @error('adresse_residence') is-invalid @enderror"
                                          rows="2" required>{{ old('adresse_residence', $personnel->adresse_residence) }}</textarea>
                                @error('adresse_residence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Quartier</label>
                                <input type="text" name="quartier" class="form-control" value="{{ old('quartier', $personnel->quartier) }}">
                            </div>
                        </div>

                        <!-- Pièce d'identité -->
                        <hr>
                        <h6><i class="fas fa-id-card me-2"></i>Pièce d'identité</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Type de pièce *</label>
                                <select name="type_piece" class="form-select @error('type_piece') is-invalid @enderror" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="CNI" {{ old('type_piece', $personnel->type_piece) == 'CNI' ? 'selected' : '' }}>Carte Nationale d'Identité</option>
                                    <option value="PASSEPORT" {{ old('type_piece', $personnel->type_piece) == 'PASSEPORT' ? 'selected' : '' }}>Passeport</option>
                                    <option value="CARTE_SEJOUR" {{ old('type_piece', $personnel->type_piece) == 'CARTE_SEJOUR' ? 'selected' : '' }}>Carte de Séjour</option>
                                    <option value="AUTRE" {{ old('type_piece', $personnel->type_piece) == 'AUTRE' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('type_piece')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Numéro de pièce *</label>
                                <input type="text" name="numero_piece" class="form-control @error('numero_piece') is-invalid @enderror"
                                       value="{{ old('numero_piece', $personnel->numero_piece) }}" required>
                                @error('numero_piece')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Date délivrance *</label>
                                <input type="date" name="date_delivrance_piece" class="form-control @error('date_delivrance_piece') is-invalid @enderror"
                                       value="{{ old('date_delivrance_piece', optional($personnel->date_delivrance_piece)->format('Y-m-d')) }}" required>
                                @error('date_delivrance_piece')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Date expiration</label>
                                <input type="date" name="expiration_piece" class="form-control" value="{{ old('expiration_piece', optional($personnel->expiration_piece)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Lieu délivrance *</label>
                                <input type="text" name="lieu_delivrance_piece" class="form-control @error('lieu_delivrance_piece') is-invalid @enderror"
                                       value="{{ old('lieu_delivrance_piece', $personnel->lieu_delivrance_piece) }}" required>
                                @error('lieu_delivrance_piece')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 2: Informations Professionnelles -->
            <div class="tab-pane fade" id="professionnel" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fonction *</label>
                                <input type="text" name="poste" class="form-control @error('poste') is-invalid @enderror"
                                       value="{{ old('poste', $personnel->poste) }}"
                                       placeholder="Ex: Chauffeur, Magasinier, Comptable..."
                                       list="postesSuggestionsEdit" required>
                                <datalist id="postesSuggestionsEdit">
                                    <option value="Chauffeur">
                                    <option value="Chauffeur Poids-Lourd">
                                    <option value="Magasinier">
                                    <option value="Manutentionnaire">
                                    <option value="Cariste">
                                    <option value="Agent de Logistique">
                                    <option value="Chef Magasin">
                                    <option value="Inspecteur Véhicules">
                                    <option value="Comptable">
                                    <option value="Secrétaire">
                                    <option value="Agent Administratif">
                                    <option value="Technicien">
                                    <option value="Chef d'équipe">
                                    <option value="Gestionnaire de Stock">
                                    <option value="Responsable Qualité">
                                    <option value="Agent de Sécurité">
                                    <option value="Conducteur d'Engins">
                                    <option value="Mécanicien">
                                    <option value="Électricien">
                                    <option value="Agent d'Entretien">
                                    <option value="Responsable Logistique">
                                    <option value="Directeur Général">
                                    <option value="Directeur Administratif">
                                    <option value="Responsable RH">
                                    <option value="Commercial">
                                    <option value="Chargé de Clientèle">
                                </datalist>
                                <small class="text-muted">Saisissez votre fonction ou sélectionnez dans les suggestions</small>
                                @error('poste')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Service *</label>
                                <input type="text" name="service" class="form-control @error('service') is-invalid @enderror"
                                       value="{{ old('service', $personnel->service) }}" required>
                                @error('service')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Département</label>
                                <input type="text" name="departement" class="form-control" value="{{ old('departement', $personnel->departement) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Catégorie *</label>
                                <select name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                                    <option value="">Sélectionner...</option>
                                    @if(old('categorie', $personnel->categorie) && !in_array(old('categorie', $personnel->categorie), ['Ouvrier', 'Agent de Maitrise', 'Cadre', 'A', 'B', 'C', 'D', 'E']))
                                        <option value="{{ old('categorie', $personnel->categorie) }}" selected>{{ old('categorie', $personnel->categorie) }}</option>
                                    @endif
                                    <option value="Ouvrier" {{ old('categorie', $personnel->categorie) == 'Ouvrier' ? 'selected' : '' }}>Ouvrier / Employé</option>
                                    <option value="Agent de Maitrise" {{ old('categorie', $personnel->categorie) == 'Agent de Maitrise' ? 'selected' : '' }}>Agent de Maîtrise</option>
                                    <option value="Cadre" {{ old('categorie', $personnel->categorie) == 'Cadre' ? 'selected' : '' }}>Cadre</option>
                                    <option value="A" {{ old('categorie', $personnel->categorie) == 'A' ? 'selected' : '' }}>Catégorie A</option>
                                    <option value="B" {{ old('categorie', $personnel->categorie) == 'B' ? 'selected' : '' }}>Catégorie B</option>
                                    <option value="C" {{ old('categorie', $personnel->categorie) == 'C' ? 'selected' : '' }}>Catégorie C</option>
                                    <option value="D" {{ old('categorie', $personnel->categorie) == 'D' ? 'selected' : '' }}>Catégorie D</option>
                                    <option value="E" {{ old('categorie', $personnel->categorie) == 'E' ? 'selected' : '' }}>Catégorie E</option>
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Échelon</label>
                                <input type="text" name="echelon" class="form-control" value="{{ old('echelon', $personnel->echelon) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Indice</label>
                                <input type="text" name="indice" class="form-control" value="{{ old('indice', $personnel->indice) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Liaison compte utilisateur</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Aucun (Personnel uniquement)</option>
                                    @if(isset($users))
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $personnel->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="text-muted">Optionnel - Lie à un compte utilisateur système</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 3: Contrat & Rémunération -->
            <div class="tab-pane fade" id="contrat" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Type de contrat *</label>
                                <select name="type_contrat" class="form-select @error('type_contrat') is-invalid @enderror" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="CDI" {{ old('type_contrat', $personnel->type_contrat) == 'CDI' ? 'selected' : '' }}>CDI (Contrat à Durée Indéterminée)</option>
                                    <option value="CDD" {{ old('type_contrat', $personnel->type_contrat) == 'CDD' ? 'selected' : '' }}>CDD (Contrat à Durée Déterminée)</option>
                                    <option value="STAGE" {{ old('type_contrat', $personnel->type_contrat) == 'STAGE' ? 'selected' : '' }}>Stage</option>
                                    <option value="ESSAI" {{ old('type_contrat', $personnel->type_contrat) == 'ESSAI' ? 'selected' : '' }}>Période d'Essai</option>
                                    <option value="INTERIM" {{ old('type_contrat', $personnel->type_contrat) == 'INTERIM' ? 'selected' : '' }}>Intérim</option>
                                    <option value="CONSULTANT" {{ old('type_contrat', $personnel->type_contrat) == 'CONSULTANT' ? 'selected' : '' }}>Consultant</option>
                                </select>
                                @error('type_contrat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date d'embauche *</label>
                                <input type="date" name="date_embauche" class="form-control @error('date_embauche') is-invalid @enderror"
                                       value="{{ old('date_embauche', optional($personnel->date_embauche)->format('Y-m-d')) }}" required>
                                @error('date_embauche')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3" id="dateFinContratField">
                                <label class="form-label">Date fin contrat</label>
                                <input type="date" name="date_fin_contrat" class="form-control @error('date_fin_contrat') is-invalid @enderror"
                                       value="{{ old('date_fin_contrat', optional($personnel->date_fin_contrat)->format('Y-m-d')) }}">
                                @error('date_fin_contrat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Obligatoire pour CDD</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Durée essai (jours) *</label>
                                <input type="number" name="duree_essai_jours" class="form-control @error('duree_essai_jours') is-invalid @enderror"
                                       value="{{ old('duree_essai_jours', $personnel->duree_essai_jours) }}" min="1" required>
                                @error('duree_essai_jours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Salaire de base *</label>
                                <div class="input-group">
                                    <input type="number" name="salaire_base" class="form-control @error('salaire_base') is-invalid @enderror"
                                           value="{{ old('salaire_base', $personnel->salaire_base) }}" min="0" step="0.01" required>
                                    <select name="devise" class="form-select" style="max-width: 80px;">
                                        <option value="XOF" {{ old('devise', $personnel->devise) == 'XOF' ? 'selected' : '' }}>XOF</option>
                                        <option value="EUR" {{ old('devise', $personnel->devise) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="USD" {{ old('devise', $personnel->devise) == 'USD' ? 'selected' : '' }}>USD</option>
                                    </select>
                                </div>
                                @error('salaire_base')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Fréquence paiement *</label>
                                <select name="frequence_paiement" class="form-select @error('frequence_paiement') is-invalid @enderror" required>
                                    <option value="MENSUEL" {{ old('frequence_paiement', $personnel->frequence_paiement) == 'MENSUEL' ? 'selected' : '' }}>Mensuel</option>
                                    <option value="HEBDOMADAIRE" {{ old('frequence_paiement', $personnel->frequence_paiement) == 'HEBDOMADAIRE' ? 'selected' : '' }}>Hebdomadaire</option>
                                    <option value="QUINZOMADAIRE" {{ old('frequence_paiement', $personnel->frequence_paiement) == 'QUINZOMADAIRE' ? 'selected' : '' }}>Quinzomadaire</option>
                                </select>
                                @error('frequence_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Mode de paiement</label>
                                <select name="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                                    <option value="">Sélectionner...</option>
                                    <option value="VIREMENT" {{ old('mode_paiement', $personnel->mode_paiement) == 'VIREMENT' ? 'selected' : '' }}>Virement Bancaire</option>
                                    <option value="ESPECE" {{ old('mode_paiement', $personnel->mode_paiement) == 'ESPECE' ? 'selected' : '' }}>Espèce</option>
                                    <option value="CHEQUE" {{ old('mode_paiement', $personnel->mode_paiement) == 'CHEQUE' ? 'selected' : '' }}>Chèque</option>
                                    <option value="MOBILE_MONEY" {{ old('mode_paiement', $personnel->mode_paiement) == 'MOBILE_MONEY' ? 'selected' : '' }}>Mobile Money</option>
                                </select>
                                @error('mode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 4: CNPS & Fiscalité -->
            <div class="tab-pane fade" id="cnps" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-shield-alt me-2"></i>CNPS (Côte d'Ivoire)</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Numéro CNPS</label>
                                <input type="text" name="numero_cnps" class="form-control @error('numero_cnps') is-invalid @enderror"
                                       value="{{ old('numero_cnps', $personnel->numero_cnps) }}">
                                @error('numero_cnps')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date affiliation CNPS</label>
                                <input type="date" name="date_affiliation_cnps" class="form-control @error('date_affiliation_cnps') is-invalid @enderror"
                                       value="{{ old('date_affiliation_cnps', optional($personnel->date_affiliation_cnps)->format('Y-m-d')) }}">
                                @error('date_affiliation_cnps')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Catégorie CNPS</label>
                                <select name="categorie_cnps" class="form-select @error('categorie_cnps') is-invalid @enderror">
                                    <option value="">Sélectionner...</option>
                                    <option value="A" {{ old('categorie_cnps', $personnel->categorie_cnps) == 'A' ? 'selected' : '' }}>Catégorie A</option>
                                    <option value="B" {{ old('categorie_cnps', $personnel->categorie_cnps) == 'B' ? 'selected' : '' }}>Catégorie B</option>
                                    <option value="C" {{ old('categorie_cnps', $personnel->categorie_cnps) == 'C' ? 'selected' : '' }}>Catégorie C</option>
                                    <option value="D" {{ old('categorie_cnps', $personnel->categorie_cnps) == 'D' ? 'selected' : '' }}>Catégorie D</option>
                                    <option value="E" {{ old('categorie_cnps', $personnel->categorie_cnps) == 'E' ? 'selected' : '' }}>Catégorie E</option>
                                    <option value="F" {{ old('categorie_cnps', $personnel->categorie_cnps) == 'F' ? 'selected' : '' }}>Catégorie F</option>
                                    <option value="G" {{ old('categorie_cnps', $personnel->categorie_cnps) == 'G' ? 'selected' : '' }}>Catégorie G</option>
                                </select>
                                @error('categorie_cnps')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6><i class="fas fa-receipt me-2"></i>Fiscalité</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Numéro contribuable</label>
                                <input type="text" name="numero_contribuable" class="form-control @error('numero_contribuable') is-invalid @enderror"
                                       value="{{ old('numero_contribuable', $personnel->numero_contribuable) }}">
                                @error('numero_contribuable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Parts fiscales *</label>
                                <input type="number" name="nb_parts_fiscales" class="form-control @error('nb_parts_fiscales') is-invalid @enderror"
                                       value="{{ old('nb_parts_fiscales', $personnel->nb_parts_fiscales) }}" min="1" required>
                                @error('nb_parts_fiscales')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Situation fiscale *</label>
                                <select name="situation_fiscale" class="form-select @error('situation_fiscale') is-invalid @enderror" required>
                                    <option value="IMPOSABLE" {{ old('situation_fiscale', $personnel->situation_fiscale) == 'IMPOSABLE' ? 'selected' : '' }}>Imposable</option>
                                    <option value="NON_IMPOSABLE" {{ old('situation_fiscale', $personnel->situation_fiscale) == 'NON_IMPOSABLE' ? 'selected' : '' }}>Non imposable</option>
                                    <option value="EXONERE" {{ old('situation_fiscale', $personnel->situation_fiscale) == 'EXONERE' ? 'selected' : '' }}>Exonéré</option>
                                </select>
                                @error('situation_fiscale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 5: Banque -->
            <div class="tab-pane fade" id="banque" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Banque</label>
                                <input type="text" name="banque" class="form-control" value="{{ old('banque', $personnel->banque) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Agence bancaire</label>
                                <input type="text" name="agence_bancaire" class="form-control" value="{{ old('agence_bancaire', $personnel->agence_bancaire) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Numéro compte</label>
                                <input type="text" name="numero_compte_bancaire" class="form-control" value="{{ old('numero_compte_bancaire', $personnel->numero_compte_bancaire) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">RIB</label>
                                <input type="text" name="rib" class="form-control" value="{{ old('rib', $personnel->rib) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 6: Contact d'Urgence -->
            <div class="tab-pane fade" id="urgence" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nom contact *</label>
                                <input type="text" name="nom_urgence" class="form-control @error('nom_urgence') is-invalid @enderror"
                                       value="{{ old('nom_urgence', $personnel->nom_urgence) }}" required>
                                @error('nom_urgence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Téléphone d'urgence *</label>
                                <input type="tel" name="telephone_urgence" class="form-control @error('telephone_urgence') is-invalid @enderror"
                                       value="{{ old('telephone_urgence', $personnel->telephone_urgence) }}" required>
                                @error('telephone_urgence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Lien parenté *</label>
                                <input type="text" name="lien_parente" class="form-control @error('lien_parente') is-invalid @enderror"
                                       value="{{ old('lien_parente', $personnel->lien_parente) }}" required>
                                @error('lien_parente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Adresse d'urgence</label>
                                <textarea name="adresse_urgence" class="form-control" rows="2">{{ old('adresse_urgence', $personnel->adresse_urgence) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 7: Santé -->
            <div class="tab-pane fade" id="sante" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Groupe sanguin</label>
                                <select name="groupe_sanguin" class="form-select @error('groupe_sanguin') is-invalid @enderror">
                                    <option value="">Sélectionner...</option>
                                    <option value="A+" {{ old('groupe_sanguin', $personnel->groupe_sanguin) == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('groupe_sanguin', $personnel->groupe_sanguin) == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('groupe_sanguin', $personnel->groupe_sanguin) == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('groupe_sanguin', $personnel->groupe_sanguin) == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="O+" {{ old('groupe_sanguin', $personnel->groupe_sanguin) == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('groupe_sanguin', $personnel->groupe_sanguin) == 'O-' ? 'selected' : '' }}>O-</option>
                                    <option value="AB+" {{ old('groupe_sanguin', $personnel->groupe_sanguin) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('groupe_sanguin', $personnel->groupe_sanguin) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                </select>
                                @error('groupe_sanguin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Allergies</label>
                                <textarea name="allergies" class="form-control" rows="2">{{ old('allergies', $personnel->allergies) }}</textarea>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Maladies chroniques</label>
                                <textarea name="maladies_chroniques" class="form-control" rows="2">{{ old('maladies_chroniques', $personnel->maladies_chroniques) }}</textarea>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Médecin traitant</label>
                                <input type="text" name="medecin_traitant" class="form-control" value="{{ old('medecin_traitant', $personnel->medecin_traitant) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Téléphone médecin</label>
                                <input type="tel" name="telephone_medecin" class="form-control" value="{{ old('telephone_medecin', $personnel->telephone_medecin) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 8: Documents -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-file-upload me-2"></i>Mettre à jour les documents</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">CV</label>
                                <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                                <small class="text-muted">Laissez vide pour garder l'actuel</small>
                                @if($personnel->cv_path)
                                    <br><small class="text-success">✓ Document existant</small>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Lettre motivation</label>
                                <input type="file" name="lettre_motivation" class="form-control" accept=".pdf,.doc,.docx">
                                <small class="text-muted">Laissez vide pour garder l'actuel</small>
                                @if($personnel->lettre_motivation_path)
                                    <br><small class="text-success">✓ Document existant</small>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contrat travail</label>
                                <input type="file" name="contrat" class="form-control" accept=".pdf,.doc,.docx">
                                <small class="text-muted">Laissez vide pour garder l'actuel</small>
                                @if($personnel->contrat_path)
                                    <br><small class="text-success">✓ Document existant</small>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Casier judiciaire</label>
                                <input type="file" name="casier_judiciaire" class="form-control" accept=".pdf,.jpg,.jpeg">
                                <small class="text-muted">Laissez vide pour garder l'actuel</small>
                                @if($personnel->casier_judiciaire_path)
                                    <br><small class="text-success">✓ Document existant</small>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Certificat médical</label>
                                <input type="file" name="certificat_medical" class="form-control" accept=".pdf,.jpg,.jpeg">
                                <small class="text-muted">Laissez vide pour garder l'actuel</small>
                                @if($personnel->certificat_medical_path)
                                    <br><small class="text-success">✓ Document existant</small>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Diplômes</label>
                                <input type="file" name="diplomes" class="form-control" accept=".pdf,.zip">
                                <small class="text-muted">Laissez vide pour garder l'actuel</small>
                                @if($personnel->diplomes_path)
                                    <br><small class="text-success">✓ Document existant</small>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Attestations</label>
                                <input type="file" name="attestations" class="form-control" accept=".pdf,.zip">
                                <small class="text-muted">Laissez vide pour garder l'actuel</small>
                                @if($personnel->attestations_path)
                                    <br><small class="text-success">✓ Document existant</small>
                                @endif
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observations</label>
                                <textarea name="observations" class="form-control" rows="3" placeholder="Notes supplémentaires sur le personnel...">{{ old('observations', $personnel->observations) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet 9: Statut & Administration -->
            <div class="tab-pane fade" id="statut" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-cog me-2"></i>Statut administratif</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Statut actuel *</label>
                                <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="ACTIF" {{ old('statut', $personnel->statut) == 'ACTIF' ? 'selected' : '' }}>Actif</option>
                                    <option value="CONGE" {{ old('statut', $personnel->statut) == 'CONGE' ? 'selected' : '' }}>En congé</option>
                                    <option value="MALADIE" {{ old('statut', $personnel->statut) == 'MALADIE' ? 'selected' : '' }}>Maladie</option>
                                    <option value="SUSPENDU" {{ old('statut', $personnel->statut) == 'SUSPENDU' ? 'selected' : '' }}>Suspendu</option>
                                    <option value="DEMISSION" {{ old('statut', $personnel->statut) == 'DEMISSION' ? 'selected' : '' }}>Démission</option>
                                    <option value="LICENCIE" {{ old('statut', $personnel->statut) == 'LICENCIE' ? 'selected' : '' }}>Licencié</option>
                                    <option value="RETRAITE" {{ old('statut', $personnel->statut) == 'RETRAITE' ? 'selected' : '' }}>Retraité</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3" id="dateDepartField">
                                <label class="form-label">Date de départ</label>
                                <input type="date" name="date_depart" class="form-control @error('date_depart') is-invalid @enderror"
                                       value="{{ old('date_depart', optional($personnel->date_depart)->format('Y-m-d')) }}">
                                @error('date_depart')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Requis pour Démission, Licenciement, Retraite</small>
                            </div>
                            <div class="col-md-4 mb-3" id="motifDepartField">
                                <label class="form-label">Motif de départ</label>
                                <textarea name="motif_depart" class="form-control @error('motif_depart') is-invalid @enderror"
                                          rows="2">{{ old('motif_depart', $personnel->motif_depart) }}</textarea>
                                @error('motif_depart')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Requis pour Démission, Licenciement, Retraite</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informations système:</strong><br>
                            <small>Créé le: {{ $personnel->created_at->format('d/m/Y H:i') }} par {{ $personnel->createdBy->name ?? 'Système' }}</small><br>
                            @if($personnel->updated_by)
                                <small>Modifié le: {{ $personnel->updated_at->format('d/m/Y H:i') }} par {{ $personnel->updatedBy->name ?? 'Système' }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="card mt-4">
            <div class="card-body text-end">
                <a href="{{ route('rh.personnel.show', $personnel->id) }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-times me-1"></i>Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Preview photo
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML = `
                <img src="${e.target.result}" alt="Photo" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
            `;
        }
        reader.readAsDataURL(file);
    }
}

// Afficher/masquer date fin contrat selon type
document.querySelector('select[name="type_contrat"]').addEventListener('change', function() {
    const dateFinField = document.getElementById('dateFinContratField');
    if (this.value === 'CDD') {
        dateFinField.style.display = 'block';
        dateFinField.querySelector('input').setAttribute('required', 'required');
    } else {
        dateFinField.style.display = 'none';
        dateFinField.querySelector('input').removeAttribute('required');
    }
});

// Afficher/masquer champs départ selon statut
document.querySelector('select[name="statut"]').addEventListener('change', function() {
    const dateDepartField = document.getElementById('dateDepartField');
    const motifDepartField = document.getElementById('motifDepartField');

    if (['DEMISSION', 'LICENCIE', 'RETRAITE'].includes(this.value)) {
        dateDepartField.style.display = 'block';
        motifDepartField.style.display = 'block';
        dateDepartField.querySelector('input').setAttribute('required', 'required');
        motifDepartField.querySelector('textarea').setAttribute('required', 'required');
    } else {
        dateDepartField.style.display = 'none';
        motifDepartField.style.display = 'none';
        dateDepartField.querySelector('input').removeAttribute('required');
        motifDepartField.querySelector('textarea').removeAttribute('required');
    }
});

// Initialiser l'affichage
document.addEventListener('DOMContentLoaded', function() {
    // Déclencher les événements pour initialiser l'affichage
    document.querySelector('select[name="type_contrat"]').dispatchEvent(new Event('change'));
    document.querySelector('select[name="statut"]').dispatchEvent(new Event('change'));
});

// Validation du formulaire avant soumission
document.querySelector('form').addEventListener('submit', function(e) {
    const typeContrat = document.querySelector('select[name="type_contrat"]').value;
    const dateFinContrat = document.querySelector('input[name="date_fin_contrat"]').value;
    const statut = document.querySelector('select[name="statut"]').value;
    const dateDepart = document.querySelector('input[name="date_depart"]').value;
    const motifDepart = document.querySelector('textarea[name="motif_depart"]').value;

    if (typeContrat === 'CDD' && !dateFinContrat) {
        e.preventDefault();
        alert('La date de fin de contrat est obligatoire pour un CDD.');
        document.querySelector('input[name="date_fin_contrat"]').focus();
        return;
    }

    if (['DEMISSION', 'LICENCIE', 'RETRAITE'].includes(statut) && (!dateDepart || !motifDepart)) {
        e.preventDefault();
        alert('La date de départ et le motif sont obligatoires pour ce statut.');
        if (!dateDepart) document.querySelector('input[name="date_depart"]').focus();
        else document.querySelector('textarea[name="motif_depart"]').focus();
        return;
    }
});
</script>
@endpush
