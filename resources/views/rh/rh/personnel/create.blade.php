@extends('layouts.app')

@section('title', 'Recrutement Nouveau Personnel | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('personnel.index') }}">Personnel RH</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nouveau Recrutement</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-xl-11">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="display-6 fw-bold text-primary mb-1">
                        <i class="fas fa-user-plus me-3"></i>Fiche de Recrutement Personnel
                    </h2>
                    <p class="text-muted fs-5">Enregistrement complet du dossier administratif (Convention Collective CI)</p>
                </div>
                <div>
                    <a href="{{ route('personnel.index') }}" class="btn btn-outline-secondary btn-lg shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('personnel.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                <!-- Main Form Layout -->
                <div class="row">
                    <!-- Sidebar: Summary & Navigation -->
                    <div class="col-lg-3 mb-4">
                        <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                            <div class="card-body p-4 text-center">
                                <div class="mb-4">
                                    <div class="position-relative d-inline-block">
                                        <img id="photoPreview" src="{{ asset('img/avatar-placeholder.png') }}" alt="Avatar" 
                                             class="rounded-circle border border-4 border-light shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                        <label for="photo_profil" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 mb-3" style="width: 32px; height: 32px; padding: 0; line-height: 32px;">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        <input type="file" name="photo_profil" id="photo_profil" class="d-none" accept="image/*" onchange="previewFile()">
                                    </div>
                                    <h6 class="fw-bold mb-0">Photo de Profil</h6>
                                    <small class="text-muted">Optionnel</small>
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
                                        <span class="badge bg-primary-soft text-primary me-3 p-3"><i class="fas fa-user fa-2x"></i></span>
                                        <div>
                                            <h4 class="fw-bold mb-0 text-dark">Identité & État Civil</h4>
                                            <p class="text-muted mb-0 small">Informations personnelles et documents d'identité officiels</p>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <!-- Matricule & Noms -->
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="matricule" class="form-control" id="matricule" value="{{ old('matricule', $suggestedMatricule ?? '') }}" required>
                                                <label>Matricule RH *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating text-uppercase">
                                                <input type="text" name="nom" class="form-control" id="nom" value="{{ old('nom') }}" required>
                                                <label>Nom de famille *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="prenoms" class="form-control" id="prenoms" value="{{ old('prenoms') }}" required>
                                                <label>Prénoms *</label>
                                            </div>
                                        </div>

                                        <!-- Naissance & Sexe -->
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="date" name="date_naissance" class="form-control" value="{{ old('date_naissance') }}" required>
                                                <label>Date de Naissance *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="lieu_naissance" class="form-control" value="{{ old('lieu_naissance') }}" required>
                                                <label>Lieu de Naissance *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="sexe" class="form-select" required>
                                                    <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                                    <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                                                </select>
                                                <label>Genre *</label>
                                            </div>
                                        </div>

                                        <!-- Situation & Enfants -->
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="situation_matrimoniale" class="form-select" required>
                                                    <option value="Célibataire" {{ old('situation_matrimoniale') == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                                                    <option value="Marié(e)" {{ old('situation_matrimoniale') == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                                                    <option value="Divorcé(e)" {{ old('situation_matrimoniale') == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                                                </select>
                                                <label>Situation Matrimoniale *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" name="nb_enfants_charge" class="form-control" value="{{ old('nb_enfants_charge', 0) }}" min="0" required>
                                                <label>Enfants à charge *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="nationalite" class="form-control" value="{{ old('nationalite', 'Ivoirienne') }}" required>
                                                <label>Nationalité *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pièce d'Identité -->
                                    <h6 class="fw-bold mb-3 mt-2 text-primary"><i class="fas fa-passport me-2"></i>Identification Officielle</h6>
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select name="type_piece" class="form-select" required>
                                                    <option value="CNI" {{ old('type_piece') == 'CNI' ? 'selected' : '' }}>CNI</option>
                                                    <option value="PASSEPORT" {{ old('type_piece') == 'PASSEPORT' ? 'selected' : '' }}>Passeport</option>
                                                    <option value="CARTE_SEJOUR" {{ old('type_piece') == 'CARTE_SEJOUR' ? 'selected' : '' }}>Carte de Séjour</option>
                                                </select>
                                                <label>Type de pièce *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" name="numero_piece" class="form-control" value="{{ old('numero_piece') }}" placeholder="Numéro" required>
                                                <label>Numéro de pièce *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="date" name="date_delivrance_piece" class="form-control" value="{{ old('date_delivrance_piece') }}" required>
                                                <label>Date Délivrance *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" name="lieu_delivrance_piece" class="form-control" value="{{ old('lieu_delivrance_piece') }}" required>
                                                <label>Lieu Délivrance *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Coordonnées -->
                                    <h6 class="fw-bold mb-3 mt-2 text-primary"><i class="fas fa-map-marked-alt me-2"></i>Coordonnées & Contact</h6>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" name="telephone_principal" class="form-control" value="{{ old('telephone_principal') }}" required>
                                                <label>Téléphone Principal *</label>
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
                                                <input type="text" name="ville" class="form-control" value="{{ old('ville', 'Abidjan') }}" required>
                                                <label>Ville de Résidence *</label>
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
                                                <textarea name="adresse_residence" class="form-control" style="height: 80px;" required>{{ old('adresse_residence') }}</textarea>
                                                <label>Adresse Détaillée (Domicile) *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary btn-lg px-5 shadow" onclick="goToTab('v-pills-pro-tab')">
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
                                                <select name="poste" class="form-select" id="posteSelect" required>
                                                    <option value="">Choisir la fonction...</option>
                                                    <option value="Chauffeur" {{ old('poste') == 'Chauffeur' ? 'selected' : '' }}>Chauffeur</option>
                                                    <option value="Chauffeur Poids-Lourd" {{ old('poste') == 'Chauffeur Poids-Lourd' ? 'selected' : '' }}>Chauffeur Poids-Lourd</option>
                                                    <option value="Magasinier" {{ old('poste') == 'Magasinier' ? 'selected' : '' }}>Magasinier</option>
                                                    <option value="Manutentionnaire" {{ old('poste') == 'Manutentionnaire' ? 'selected' : '' }}>Manutentionnaire</option>
                                                    <option value="Cariste" {{ old('poste') == 'Cariste' ? 'selected' : '' }}>Cariste</option>
                                                    <option value="Agent de Logistique" {{ old('poste') == 'Agent de Logistique' ? 'selected' : '' }}>Agent de Logistique</option>
                                                    <option value="Chef Magasin" {{ old('poste') == 'Chef Magasin' ? 'selected' : '' }}>Chef Magasin</option>
                                                    <option value="Inspecteur Véhicules" {{ old('poste') == 'Inspecteur Véhicules' ? 'selected' : '' }}>Inspecteur Véhicules</option>
                                                </select>
                                                <label>Fonction / Poste *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select name="service" class="form-select" required>
                                                    @foreach($services as $service)
                                                        <option value="{{ $service }}" {{ old('service') == $service ? 'selected' : '' }}>{{ $service }}</option>
                                                    @endforeach
                                                </select>
                                                <label>Service d'Affectation *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="categorie" class="form-select" required>
                                                    <option value="Ouvrier" {{ old('categorie') == 'Ouvrier' ? 'selected' : '' }}>Ouvrier / Employé</option>
                                                    <option value="Agent de Maitrise" {{ old('categorie') == 'Agent de Maitrise' ? 'selected' : '' }}>Agent de Maîtrise</option>
                                                    <option value="Cadre" {{ old('categorie') == 'Cadre' ? 'selected' : '' }}>Cadre</option>
                                                </select>
                                                <label>Catégorie Professionnelle *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="type_contrat" class="form-select" required>
                                                    <option value="CDI" {{ old('type_contrat') == 'CDI' ? 'selected' : '' }}>CDI (Indéterminé)</option>
                                                    <option value="CDD" {{ old('type_contrat') == 'CDD' ? 'selected' : '' }}>CDD (Déterminé)</option>
                                                    <option value="STAGE" {{ old('type_contrat') == 'STAGE' ? 'selected' : '' }}>Stage</option>
                                                </select>
                                                <label>Nature du Contrat *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="date" name="date_embauche" class="form-control" value="{{ old('date_embauche', date('Y-m-d')) }}" required>
                                                <label>Date d'Embauche *</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" name="salaire_base" class="form-control" value="{{ old('salaire_base') }}" required>
                                                <label>Salaire de Base Brut *</label>
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
                                                <input type="number" name="duree_essai_jours" class="form-control" value="{{ old('duree_essai_jours', 30) }}" required>
                                                <label>Durée Essai (Jours) *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="goToTab('v-pills-civilite-tab')">
                                            <i class="fas fa-arrow-left me-2"></i> Précédent
                                        </button>
                                        <button type="button" class="btn btn-primary btn-lg px-5 shadow" onclick="goToTab('v-pills-finance-tab')">
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
                                                <select name="situation_fiscale" class="form-select" required>
                                                    <option value="IMPOSABLE" {{ old('situation_fiscale') == 'IMPOSABLE' ? 'selected' : '' }}>Imposable</option>
                                                    <option value="NON_IMPOSABLE" {{ old('situation_fiscale') == 'NON_IMPOSABLE' ? 'selected' : '' }}>Non Imposable</option>
                                                    <option value="EXONERE" {{ old('situation_fiscale') == 'EXONERE' ? 'selected' : '' }}>Exonéré</option>
                                                </select>
                                                <label>Régime Fiscal *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="number" name="nb_parts_fiscales" class="form-control" value="{{ old('nb_parts_fiscales', 1) }}" min="1" step="0.5" required>
                                                <label>Nombre de Parts Fiscales *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 mt-4 text-primary"><i class="fas fa-bank me-2"></i>Informations Bancaires</h6>
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="banque" class="form-control" value="{{ old('banque') }}" placeholder="Nom de la banque">
                                                <label>Nom de la Banque</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="numero_compte_bancaire" class="form-control" value="{{ old('numero_compte_bancaire') }}" placeholder="RIB / IBAN">
                                                <label>Numéro de Compte / RIB</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="goToTab('v-pills-pro-tab')">
                                            <i class="fas fa-arrow-left me-2"></i> Précédent
                                        </button>
                                        <button type="button" class="btn btn-primary btn-lg px-5 shadow" onclick="goToTab('v-pills-urgence-tab')">
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
                                                <input type="text" name="nom_urgence" class="form-control" value="{{ old('nom_urgence') }}" required>
                                                <label>Nom du Contact de secours *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" name="telephone_urgence" class="form-control" value="{{ old('telephone_urgence') }}" required>
                                                <label>Téléphone Urgence *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="lien_parente" class="form-control" value="{{ old('lien_parente') }}" required>
                                                <label>Lien de Parenté *</label>
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
                                        <input type="hidden" name="frequence_paiement" value="MENSUEL">
                                        <input type="hidden" name="statut" value="ACTIF">
                                    </div>

                                    <div class="mt-5 d-flex justify-content-between pt-4 border-top">
                                        <button type="button" class="btn btn-outline-primary btn-lg px-4" onclick="goToTab('v-pills-finance-tab')">
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
            var tab = bootstrap.Tab.getOrCreateInstance(tabTriggerEl);
            tab.show();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function previewFile() {
        const preview = document.getElementById('photoPreview');
        const file = document.getElementById('photo_profil').files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ asset('img/avatar-placeholder.png') }}";
        }
    }
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
        color: #0d6efd;
    }
    .custom-pills .nav-link.active {
        background-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
    }
    
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: #0d6efd;
    }
</style>
@endsection
