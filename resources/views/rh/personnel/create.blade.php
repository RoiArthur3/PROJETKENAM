    @extends('layouts.app')

@section('title', 'Recrutement Nouveau Personnel | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('rh.personnel.index') }}">Personnel RH</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nouveau Recrutement</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-xl-11">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="display-6 fw-bold text-success mb-1">
                        <i class="fas fa-user-plus me-3"></i>Fiche de Recrutement Personnel
                    </h2>
                    <p class="text-muted fs-5">Enregistrement complet du dossier administratif (Convention Collective CI)</p>
                </div>
                <div>
                    <a href="{{ route('rh.personnel.index') }}" class="btn btn-outline-secondary btn-lg shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-triangle-exclamation me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <div class="fw-bold mb-2">
                        <i class="fas fa-circle-info me-2"></i>Le recrutement n'a pas ete valide. Corrigez les champs suivants:
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('rh.personnel.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                <!-- Main Form Layout -->
                <div class="row">
                    <!-- Sidebar: Summary & Navigation -->
                    <div class="col-lg-3 mb-4">
                        <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                            <div class="card-body p-4 text-center">
                                <div class="mb-4 rounded-4 bg-light border p-4">
                                    <div class="text-success mb-3">
                                        <i class="fas fa-database fa-2x"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Formulaire aligné à la base</h6>
                                    <small class="text-muted">Seuls les champs présents dans la table personnel sont affichés.</small>
                                </div>

                                <div class="nav flex-column nav-pills custom-pills text-start" id="v-pills-tab" role="tablist">
                                    <button class="nav-link active mb-2" id="v-pills-civilite-tab" data-bs-toggle="pill" data-bs-target="#v-pills-civilite" type="button" role="tab">
                                        <i class="fas fa-id-card me-2"></i> État Civil & Identité
                                    </button>
                                    <button class="nav-link mb-2" id="v-pills-pro-tab" data-bs-toggle="pill" data-bs-target="#v-pills-pro" type="button" role="tab">
                                        <i class="fas fa-briefcase me-2"></i> Poste & Contrat
                                    </button>
                                    <button class="nav-link mb-2" id="v-pills-finance-tab" data-bs-toggle="pill" data-bs-target="#v-pills-finance" type="button" role="tab">
                                        <i class="fas fa-money-check-alt me-2"></i> Fiscalité & Banque
                                    </button>
                                    <button class="nav-link mb-2" id="v-pills-urgence-tab" data-bs-toggle="pill" data-bs-target="#v-pills-urgence" type="button" role="tab">
                                        <i class="fas fa-ambulance me-2"></i> Urgence & Santé
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content: Form Sections -->
                    <div class="col-lg-9">
                        <div class="tab-content" id="v-pills-tabContent">

                            <!-- Tab 1: Etat Civil -->
                            <div class="tab-pane fade show active" id="v-pills-civilite" role="tabpanel">
                                <div class="card border-0 shadow-lg rounded-4 p-4 p-xl-5">
                                    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                                        <span class="badge bg-success-soft text-success me-3 p-3"><i class="fas fa-user fa-2x"></i></span>
                                        <div>
                                            <h4 class="fw-bold mb-0 text-dark">Identité & État Civil</h4>
                                            <p class="text-muted mb-0 small">Informations personnelles et documents d'identité officiels</p>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <!-- Matricule & Noms -->
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="matricule" class="form-control bg-light" id="matricule" value="{{ old('matricule', $suggestedMatricule ?? '') }}" readonly>
                                                <label>Matricule RH</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Le matricule est généré automatiquement.</small>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating text-uppercase">
                                                <input type="text" name="nom" class="form-control" id="nom" value="{{ old('nom') }}">
                                                <label>Nom de famille</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="prenoms" class="form-control" id="prenoms" value="{{ old('prenoms') }}">
                                                <label>Prénoms</label>
                                            </div>
                                        </div>

                                        <!-- Naissance & Sexe -->
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="date" name="date_naissance" class="form-control" value="{{ old('date_naissance') }}">
                                                <label>Date de Naissance</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="lieu_naissance" class="form-control" value="{{ old('lieu_naissance') }}">
                                                <label>Lieu de Naissance</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="sexe" class="form-select">
                                                    <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                                    <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                                                </select>
                                                <label>Genre</label>
                                            </div>
                                        </div>

                                        <!-- Situation & Enfants -->
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="situation_matrimoniale" class="form-select">
                                                    <option value="Célibataire" {{ old('situation_matrimoniale') == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                                                    <option value="Marié(e)" {{ old('situation_matrimoniale') == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                                                    <option value="Divorcé(e)" {{ old('situation_matrimoniale') == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                                                </select>
                                                <label>Situation Matrimoniale</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" name="nb_enfants_charge" class="form-control" value="{{ old('nb_enfants_charge', 0) }}" min="0">
                                                <label>Enfants à charge</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="nationalite" class="form-control" value="{{ old('nationalite', 'Ivoirienne') }}">
                                                <label>Nationalité</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pièce d'Identité -->
                                    <h6 class="fw-bold mb-3 mt-2 text-success"><i class="fas fa-passport me-2"></i>Identification Officielle</h6>
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select name="type_piece" class="form-select">
                                                    <option value="CNI" {{ old('type_piece') == 'CNI' ? 'selected' : '' }}>CNI</option>
                                                    <option value="PASSEPORT" {{ old('type_piece') == 'PASSEPORT' ? 'selected' : '' }}>Passeport</option>
                                                    <option value="CARTE_SEJOUR" {{ old('type_piece') == 'CARTE_SEJOUR' ? 'selected' : '' }}>Carte de Séjour</option>
                                                </select>
                                                <label>Type de Pièce</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" name="numero_piece" class="form-control" value="{{ old('numero_piece') }}" placeholder="Numéro">
                                                <label>Numéro de Pièce</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="date" name="date_delivrance_piece" class="form-control" value="{{ old('date_delivrance_piece') }}">
                                                <label>Date Délivrance</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" name="lieu_delivrance_piece" class="form-control" value="{{ old('lieu_delivrance_piece') }}">
                                                <label>Lieu Délivrance</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Identification Caméra -->
                                    <h6 class="fw-bold mb-3 mt-2 text-info"><i class="fas fa-camera me-2"></i>Identification Caméra</h6>
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="camera_person_id" class="form-control" value="{{ old('camera_person_id') }}" placeholder="ID de la caméra">
                                                <label>ID Personnel Caméra (optionnel)</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Laissez vide pour remplir plus tard lors de la modification</small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="file" name="photo_profil" class="form-control" accept="image/*" onchange="previewPhoto(event)">
                                                <label>Photo de profil (optionnelle)</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Format: JPG, PNG (max 2MB)</small>
                                        </div>
                                    </div>

                                    <!-- Coordonnées -->
                                    <h6 class="fw-bold mb-3 mt-2 text-success"><i class="fas fa-map-marked-alt me-2"></i>Coordonnées & Contact</h6>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" name="telephone_principal" class="form-control" value="{{ old('telephone_principal') }}">
                                                <label>Téléphone Principal</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" name="email_personnel" class="form-control" value="{{ old('email_personnel') }}">
                                                <label>Email Personnel</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="ville" class="form-control" value="{{ old('ville', 'Abidjan') }}">
                                                <label>Ville de Résidence</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="quartier" class="form-control" value="{{ old('quartier') }}">
                                                <label>Commune / Quartier</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea name="adresse_residence" class="form-control" style="height: 80px;">{{ old('adresse_residence') }}</textarea>
                                                <label>Adresse Détaillée (Domicile)</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 d-flex justify-content-end">
                                        <button type="button" class="btn btn-success btn-lg px-5 shadow" onclick="goToTab('v-pills-pro-tab')">
                                            Suivant <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Professionnel & Contrat -->
                            <div class="tab-pane fade" id="v-pills-pro" role="tabpanel">
                                <div class="card border-0 shadow-lg rounded-4 p-4 p-xl-5">
                                    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                                        <span class="badge bg-success-soft text-success me-3 p-3"><i class="fas fa-file-contract fa-2x"></i></span>
                                        <div>
                                            <h4 class="fw-bold mb-0 text-dark">Détails Professionnels</h4>
                                            <p class="text-muted mb-0 small">Poste, Service et Conditions d'embauche</p>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-5">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="poste" class="form-control" id="posteInput"
                                                       value="{{ old('poste') }}"
                                                       placeholder="Ex: Chauffeur, Magasinier, Comptable..."
                                                       list="postesSuggestions">
                                                <label>Fonction / Poste</label>
                                                <datalist id="postesSuggestions">
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
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select name="service" class="form-select">
                                                    @foreach($services as $service)
                                                        <option value="{{ $service }}" {{ old('service') == $service ? 'selected' : '' }}>{{ $service }}</option>
                                                    @endforeach
                                                </select>
                                                <label>Service d'Affectation</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="categorie" class="form-select">
                                                    <option value="Ouvrier" {{ old('categorie') == 'Ouvrier' ? 'selected' : '' }}>Ouvrier / Employé</option>
                                                    <option value="Agent Maitrise" {{ old('categorie') == 'Agent Maitrise' ? 'selected' : '' }}>Agent de Maîtrise</option>
                                                    <option value="Cadre" {{ old('categorie') == 'Cadre' ? 'selected' : '' }}>Cadre</option>
                                                </select>
                                                <label>Catégorie Professionnelle</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="type_contrat" class="form-select" onchange="toggleDureeEssai()">
                                                    <option value="">-- Sélectionner --</option>
                                                    <option value="CDI" {{ old('type_contrat') == 'CDI' ? 'selected' : '' }}>CDI (Contrat à Durée Indéterminée)</option>
                                                    <option value="CDD" {{ old('type_contrat') == 'CDD' ? 'selected' : '' }}>CDD (Contrat à Durée Déterminée)</option>
                                                    <option value="STAGE" {{ old('type_contrat') == 'STAGE' ? 'selected' : '' }}>Stage</option>
                                                    <option value="ESSAI" {{ old('type_contrat') == 'ESSAI' ? 'selected' : '' }}>Période d'Essai</option>
                                                </select>
                                                <label>Nature du Contrat</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="date" name="date_embauche" class="form-control" value="{{ old('date_embauche', date('Y-m-d')) }}">
                                                <label>Date d'Embauche</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" name="salaire_base" class="form-control" value="{{ old('salaire_base') }}">
                                                <label>Salaire Net</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="devise" class="form-control bg-light" value="XOF" readonly>
                                                <label>Devise</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="mode_paiement" class="form-select">
                                                    <option value="">Sélectionner...</option>
                                                    <option value="VIREMENT" {{ old('mode_paiement') == 'VIREMENT' ? 'selected' : '' }}>Virement Bancaire</option>
                                                    <option value="ESPECE" {{ old('mode_paiement') == 'ESPECE' ? 'selected' : '' }}>Espèce</option>
                                                    <option value="CHEQUE" {{ old('mode_paiement') == 'CHEQUE' ? 'selected' : '' }}>Chèque</option>
                                                    <option value="MOBILE_MONEY" {{ old('mode_paiement') == 'MOBILE_MONEY' ? 'selected' : '' }}>Mobile Money</option>
                                                </select>
                                                <label>Mode de Paiement</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="frequence_paiement" class="form-select">
                                                    <option value="MENSUEL" {{ old('frequence_paiement', 'MENSUEL') == 'MENSUEL' ? 'selected' : '' }}>Mensuel</option>
                                                    <option value="HEBDOMADAIRE" {{ old('frequence_paiement') == 'HEBDOMADAIRE' ? 'selected' : '' }}>Hebdomadaire</option>
                                                    <option value="QUINZOMADAIRE" {{ old('frequence_paiement') == 'QUINZOMADAIRE' ? 'selected' : '' }}>Quinzomadaire</option>
                                                </select>
                                                <label>Fréquence de Paiement</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="dureeEssaiContainer">
                                            <div class="form-floating">
                                                <input type="number" name="duree_essai_jours" class="form-control" value="{{ old('duree_essai_jours', 30) }}">
                                                <label>Durée Essai (Jours)</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-success btn-lg px-4" onclick="goToTab('v-pills-civilite-tab')">
                                            <i class="fas fa-arrow-left me-2"></i> Précédent
                                        </button>
                                        <button type="button" class="btn btn-success btn-lg px-5 shadow" onclick="goToTab('v-pills-finance-tab')">
                                            Suivant <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Finance & Fiscalité -->
                            <div class="tab-pane fade" id="v-pills-finance" role="tabpanel">
                                <div class="card border-0 shadow-lg rounded-4 p-4 p-xl-5">
                                    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                                        <span class="badge bg-warning-soft text-warning me-3 p-3"><i class="fas fa-building-columns fa-2x"></i></span>
                                        <div>
                                            <h4 class="fw-bold mb-0 text-dark">Fiscalité & Coordonnées Bancaires</h4>
                                            <p class="text-muted mb-0 small">CNPS, Impôts et Virement des salaires</p>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="numero_cnps" class="form-control" value="{{ old('numero_cnps') }}">
                                                <label>Numéro CNPS</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select name="situation_fiscale" class="form-select">
                                                    <option value="IMPOSABLE" {{ old('situation_fiscale') == 'IMPOSABLE' ? 'selected' : '' }}>Imposable</option>
                                                    <option value="NON_IMPOSABLE" {{ old('situation_fiscale') == 'NON_IMPOSABLE' ? 'selected' : '' }}>Non Imposable</option>
                                                    <option value="EXONERE" {{ old('situation_fiscale') == 'EXONERE' ? 'selected' : '' }}>Exonéré</option>
                                                </select>
                                                <label>Régime Fiscal</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="number" name="nb_parts_fiscales" class="form-control" value="{{ old('nb_parts_fiscales', 1) }}" min="1" step="0.5">
                                                <label>Nombre de Parts Fiscales</label>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 mt-4 text-success"><i class="fas fa-bank me-2"></i>Informations Bancaires</h6>
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="banque" class="form-control" value="{{ old('banque') }}" placeholder="Nom de la banque">
                                                <label>Nom de la Banque</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="numero_compte" class="form-control" value="{{ old('numero_compte') }}" placeholder="RIB / IBAN">
                                                <label>Numéro de Compte / RIB</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-success btn-lg px-4" onclick="goToTab('v-pills-pro-tab')">
                                            <i class="fas fa-arrow-left me-2"></i> Précédent
                                        </button>
                                        <button type="button" class="btn btn-success btn-lg px-5 shadow" onclick="goToTab('v-pills-urgence-tab')">
                                            Suivant <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: Suivi & Urgence -->
                            <div class="tab-pane fade" id="v-pills-urgence" role="tabpanel">
                                <div class="card border-0 shadow-lg rounded-4 p-4 p-xl-5">
                                    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                                        <span class="badge bg-danger-soft text-danger me-3 p-3"><i class="fas fa-life-ring fa-2x"></i></span>
                                        <div>
                                            <h4 class="fw-bold mb-0 text-dark">Urgence & Sécurité</h4>
                                            <p class="text-muted mb-0 small">Personnes à contacter et informations médicales</p>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="nom_urgence" class="form-control" value="{{ old('nom_urgence') }}">
                                                <label>Nom du Contact de secours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" name="telephone_urgence" class="form-control" value="{{ old('telephone_urgence') }}">
                                                <label>Téléphone Urgence</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="lien_parente" class="form-control" value="{{ old('lien_parente') }}">
                                                <label>Lien de Parenté</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select name="groupe_sanguin" class="form-select">
                                                    <option value="">Inconnu</option>
                                                    <option value="A+">A+</option><option value="A-">A-</option>
                                                    <option value="B+">B+</option><option value="B-">B-</option>
                                                    <option value="O+">O+</option><option value="O-">O-</option>
                                                    <option value="AB+">AB+</option><option value="AB-">AB-</option>
                                                </select>
                                                <label>Groupe Sanguin</label>
                                            </div>
                                        </div>
                                        <input type="hidden" name="statut" value="ACTIF">
                                    </div>

                                    <div class="mt-5 d-flex justify-content-between pt-4 border-top">
                                        <button type="button" class="btn btn-outline-success btn-lg px-4" onclick="goToTab('v-pills-finance-tab')">
                                            <i class="fas fa-arrow-left me-2"></i> Précédent
                                        </button>
                                        <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg">
                                            <i class="fas fa-check-circle me-3 fa-lg"></i>Valider le Recrutement
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end tab content -->
                    </div> <!-- end col-9 -->
                </div> <!-- end row -->
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function goToTab(tabId) {
        var tabTriggerEl = document.getElementById(tabId);
        if (tabTriggerEl) {
            var tab = new bootstrap.Tab(tabTriggerEl);
            tab.show();
        }
    }

    function previewPhoto(event) {
        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Optionnel: afficher un aperçu si nécessaire
            };
            reader.readAsDataURL(file);
        }
    }

    function toggleDureeEssai() {
        var typeContrat = document.querySelector('select[name="type_contrat"]').value;
        var dureeEssaiContainer = document.getElementById('dureeEssaiContainer');

        if (typeContrat === 'STAGE' || typeContrat === 'ESSAI') {
            dureeEssaiContainer.style.display = 'block';
        } else {
            dureeEssaiContainer.style.display = 'none';
            // Vider le champ si on masque
            document.querySelector('input[name="duree_essai_jours"]').value = '';
        }
    }

    // Initialiser au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        toggleDureeEssai();
    });
</script>
@endpush

<style>
    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }

    .custom-pills .nav-link {
        color: #495057;
        font-weight: 500;
        padding: 12px 16px;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .custom-pills .nav-link:hover {
        background-color: #f8f9fa;
        color: #198754;
    }
    .custom-pills .nav-link.active {
        background-color: #198754;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: #198754;
    }
</style>
@endsection
