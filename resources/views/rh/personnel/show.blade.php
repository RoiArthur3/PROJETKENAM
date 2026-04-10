@extends('layouts.app')

@section('title', 'Détails Personnel - ' . $personnel->nom . ' ' . $personnel->prenoms)

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-user me-2"></i>{{ $personnel->nom }} {{ $personnel->prenoms }}
            </h2>
            <small class="text-muted">Matricule: {{ $personnel->matricule }} | {{ $personnel->poste }} - {{ $personnel->service }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rh.personnel.contrats.create', ['personnel_id' => $personnel->id]) }}" class="btn btn-success">
                <i class="fas fa-file-contract me-1"></i>Nouveau Contrat
            </a>
            <a href="{{ route('rh.personnel.fiche.pdf', $personnel->id) }}" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf me-1"></i>Fiche PDF
            </a>
            <a href="{{ route('rh.personnel.edit', $personnel->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('rh.personnel.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Carte principale -->
    <div class="row">
        <!-- Colonne gauche : Photo et infos principales -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body text-center">
                    <!-- Photo -->
                    @if($personnel->photo_profil)
                        <img src="{{ asset('storage/' . $personnel->photo_profil) }}" 
                             alt="Photo de {{ $personnel->nom }}" 
                             class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" 
                             style="width: 150px; height: 150px;">
                            <i class="fas fa-user text-white fa-4x"></i>
                        </div>
                    @endif

                    <!-- Statut -->
                    <div class="mb-3">
                        @switch($personnel->statut)
                            @case('ACTIF')
                                <span class="badge bg-success fs-6">Actif</span>
                                @break
                            @case('CONGE')
                                <span class="badge bg-info fs-6">En congé</span>
                                @break
                            @case('MALADIE')
                                <span class="badge bg-warning fs-6">Maladie</span>
                                @break
                            @case('SUSPENDU')
                                <span class="badge bg-secondary fs-6">Suspendu</span>
                                @break
                            @case('DEMISSION')
                                <span class="badge bg-danger fs-6">Démission</span>
                                @break
                            @case('LICENCIE')
                                <span class="badge bg-danger fs-6">Licencié</span>
                                @break
                            @case('RETRAITE')
                                <span class="badge bg-dark fs-6">Retraité</span>
                                @break
                            @default
                                <span class="badge bg-secondary fs-6">{{ $personnel->statut }}</span>
                        @endswitch
                    </div>

                    <!-- Infos principales -->
                    <h5 class="card-title">{{ $personnel->nom }} {{ $personnel->prenoms }}</h5>
                    <p class="card-text">
                        <strong>Matricule:</strong> {{ $personnel->matricule }}<br>
                        <strong>Fonction:</strong> {{ $personnel->poste }}<br>
                        <strong>Service:</strong> {{ $personnel->service }}<br>
                        @if($personnel->departement)
                            <strong>Département:</strong> {{ $personnel->departement }}<br>
                        @endif
                        <strong>Catégorie:</strong> {{ $personnel->categorie }}
                        @if($personnel->echelon)
                            / {{ $personnel->echelon }}
                        @endif
                    </p>

                    <!-- Alertes spéciales -->
                    @if($personnel->enEssai)
                        <div class="alert alert-warning alert-sm">
                            <i class="fas fa-hourglass-half me-1"></i>
                            <strong>Période d'essai</strong><br>
                            <small>Fin le: {{ \Carbon\Carbon::parse($personnel->fin_periode_essai)->format('d/m/Y') }}</small>
                        </div>
                    @endif

                    @if($personnel->contratBientotExpire)
                        <div class="alert alert-danger alert-sm">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Contrat expirant</strong><br>
                            <small>Fin le: {{ \Carbon\Carbon::parse($personnel->date_fin_contrat)->format('d/m/Y') }}</small>
                        </div>
                    @endif

                    <!-- Actions rapides -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('rh.personnel.conges', $personnel->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar-alt me-1"></i>Gérer les congés
                        </a>
                        <a href="{{ route('rh.personnel.paies', $personnel->id) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-money-bill me-1"></i>Historique paie
                        </a>
                        <a href="{{ route('rh.personnel.documents', $personnel->id) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-file me-1"></i>Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Détails complets -->
        <div class="col-md-8">
            <!-- Navigation par onglets -->
            <ul class="nav nav-tabs mb-3" id="detailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="infos-tab" data-bs-toggle="tab" data-bs-target="#infos" type="button" role="tab">
                        <i class="fas fa-user me-1"></i>Informations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contrat-tab" data-bs-toggle="tab" data-bs-target="#contrat" type="button" role="tab">
                        <i class="fas fa-file-contract me-1"></i>Contrat
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cnps-tab" data-bs-toggle="tab" data-bs-target="#cnps" type="button" role="tab">
                        <i class="fas fa-shield-alt me-1"></i>CNPS & Fiscalité
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                        <i class="fas fa-address-book me-1"></i>Contacts
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                        <i class="fas fa-file me-1"></i>Documents
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="detailTabsContent">
                <!-- Onglet Informations -->
                <div class="tab-pane fade show active" id="infos" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h6><i class="fas fa-user me-2"></i>Informations Personnelles</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Date de naissance:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($personnel->date_naissance)->format('d/m/Y') }} ({{ $personnel->age }} ans)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lieu de naissance:</strong></td>
                                            <td>{{ $personnel->lieu_naissance }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nationalité:</strong></td>
                                            <td>{{ $personnel->nationalite }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sexe:</strong></td>
                                            <td>{{ $personnel->sexe == 'M' ? 'Masculin' : 'Féminin' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Situation matrimoniale:</strong></td>
                                            <td>{{ $personnel->situation_matrimoniale }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Enfants à charge:</strong></td>
                                            <td>{{ $personnel->nb_enfants_charge }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Téléphone principal:</strong></td>
                                            <td>{{ $personnel->telephone_principal }}</td>
                                        </tr>
                                        @if($personnel->telephone_secondaire)
                                        <tr>
                                            <td><strong>Téléphone secondaire:</strong></td>
                                            <td>{{ $personnel->telephone_secondaire }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->email_personnel)
                                        <tr>
                                            <td><strong>Email personnel:</strong></td>
                                            <td>{{ $personnel->email_personnel }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Adresse:</strong></td>
                                            <td>{{ $personnel->adresse_residence }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ville:</strong></td>
                                            <td>{{ $personnel->ville }}</td>
                                        </tr>
                                        @if($personnel->quartier)
                                        <tr>
                                            <td><strong>Quartier:</strong></td>
                                            <td>{{ $personnel->quartier }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <hr>
                            <h6><i class="fas fa-id-card me-2"></i>Pièce d'identité</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Type de pièce:</strong></td>
                                            <td>{{ $personnel->type_piece }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Numéro:</strong></td>
                                            <td>{{ $personnel->numero_piece }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de délivrance:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($personnel->date_delivrance_piece)->format('d/m/Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        @if($personnel->expiration_piece)
                                        <tr>
                                            <td><strong>Date d'expiration:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($personnel->expiration_piece)->format('d/m/Y') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Lieu de délivrance:</strong></td>
                                            <td>{{ $personnel->lieu_delivrance_piece }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($personnel->observations)
                            <hr>
                            <h6><i class="fas fa-sticky-note me-2"></i>Observations</h6>
                            <p>{{ $personnel->observations }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Onglet Contrat -->
                <div class="tab-pane fade" id="contrat" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h6><i class="fas fa-file-contract me-2"></i>Informations Contractuelles</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Type de contrat:</strong></td>
                                            <td><span class="badge bg-info">{{ $personnel->type_contrat }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date d'embauche:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($personnel->date_embauche)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ancienneté:</strong></td>
                                            <td>{{ $personnel->anciennete }} ans ({{ $personnel->joursAnciennete() }} jours)</td>
                                        </tr>
                                        @if($personnel->date_fin_contrat)
                                        <tr>
                                            <td><strong>Date fin contrat:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($personnel->date_fin_contrat)->format('d/m/Y') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Durée période d'essai:</strong></td>
                                            <td>{{ $personnel->duree_essai_jours }} jours</td>
                                        </tr>
                                        @if($personnel->fin_periode_essai)
                                        <tr>
                                            <td><strong>Fin période d'essai:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($personnel->fin_periode_essai)->format('d/m/Y') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Salaire de base:</strong></td>
                                            <td class="text-success fw-bold">{{ $personnel->salaireBaseFormatte }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fréquence paiement:</strong></td>
                                            <td>{{ $personnel->frequence_paiement }}</td>
                                        </tr>
                                        @if($personnel->date_depart)
                                        <tr>
                                            <td><strong>Date de départ:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($personnel->date_depart)->format('d/m/Y') }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->motif_depart)
                                        <tr>
                                            <td><strong>Motif de départ:</strong></td>
                                            <td>{{ $personnel->motif_depart }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            @if($personnel->user)
                            <hr>
                            <h6><i class="fas fa-link me-2"></i>Liaison avec compte utilisateur</h6>
                            <div class="alert alert-info">
                                <i class="fas fa-user me-2"></i>
                                <strong>Compte système:</strong> {{ $personnel->user->name }} ({{ $personnel->user->email }})
                                <br><small>Ce personnel est lié à un compte utilisateur du système</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Onglet CNPS & Fiscalité -->
                <div class="tab-pane fade" id="cnps" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h6><i class="fas fa-shield-alt me-2"></i>CNPS (Côte d'Ivoire)</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        @if($personnel->numero_cnps)
                                        <tr>
                                            <td><strong>Numéro CNPS:</strong></td>
                                            <td>{{ $personnel->numero_cnps }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->date_affiliation_cnps)
                                        <tr>
                                            <td><strong>Date affiliation:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($personnel->date_affiliation_cnps)->format('d/m/Y') }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->categorie_cnps)
                                        <tr>
                                            <td><strong>Catégorie CNPS:</strong></td>
                                            <td><span class="badge bg-primary">{{ $personnel->categorie_cnps }}</span></td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            @if($personnel->numero_contribuable || $personnel->situation_fiscale)
                            <hr>
                            <h6><i class="fas fa-receipt me-2"></i>Fiscalité</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        @if($personnel->numero_contribuable)
                                        <tr>
                                            <td><strong>Numéro contribuable:</strong></td>
                                            <td>{{ $personnel->numero_contribuable }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Parts fiscales:</strong></td>
                                            <td>{{ $personnel->nb_parts_fiscales }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Situation fiscale:</strong></td>
                                            <td><span class="badge bg-warning">{{ $personnel->situation_fiscale }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @endif

                            @if($personnel->banque || $personnel->numero_compte_bancaire)
                            <hr>
                            <h6><i class="fas fa-university me-2"></i>Coordonnées bancaires</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        @if($personnel->banque)
                                        <tr>
                                            <td><strong>Banque:</strong></td>
                                            <td>{{ $personnel->banque }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->agence_bancaire)
                                        <tr>
                                            <td><strong>Agence:</strong></td>
                                            <td>{{ $personnel->agence_bancaire }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->numero_compte_bancaire)
                                        <tr>
                                            <td><strong>Numéro compte:</strong></td>
                                            <td>{{ $personnel->numero_compte_bancaire }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->rib)
                                        <tr>
                                            <td><strong>RIB:</strong></td>
                                            <td>{{ $personnel->rib }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Onglet Contacts -->
                <div class="tab-pane fade" id="contact" role="tabpanel">
                    <div class="row">
                        <!-- Contact d'urgence -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-phone-alt me-2"></i>Contact d'urgence</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Nom:</strong></td>
                                            <td>{{ $personnel->nom_urgence }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Téléphone:</strong></td>
                                            <td>{{ $personnel->telephone_urgence }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lien parenté:</strong></td>
                                            <td>{{ $personnel->lien_parente }}</td>
                                        </tr>
                                        @if($personnel->adresse_urgence)
                                        <tr>
                                            <td><strong>Adresse:</strong></td>
                                            <td>{{ $personnel->adresse_urgence }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Informations santé -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-heartbeat me-2"></i>Informations médicales</h6>
                                    <table class="table table-sm">
                                        @if($personnel->groupe_sanguin)
                                        <tr>
                                            <td><strong>Groupe sanguin:</strong></td>
                                            <td><span class="badge bg-danger">{{ $personnel->groupe_sanguin }}</span></td>
                                        </tr>
                                        @endif
                                        @if($personnel->allergies)
                                        <tr>
                                            <td><strong>Allergies:</strong></td>
                                            <td>{{ $personnel->allergies }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->maladies_chroniques)
                                        <tr>
                                            <td><strong>Maladies chroniques:</strong></td>
                                            <td>{{ $personnel->maladies_chroniques }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->medecin_traitant)
                                        <tr>
                                            <td><strong>Médecin traitant:</strong></td>
                                            <td>{{ $personnel->medecin_traitant }}</td>
                                        </tr>
                                        @endif
                                        @if($personnel->telephone_medecin)
                                        <tr>
                                            <td><strong>Téléphone médecin:</strong></td>
                                            <td>{{ $personnel->telephone_medecin }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglet Documents -->
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h6><i class="fas fa-file me-2"></i>Documents disponibles</h6>
                            <div class="row">
                                @if($personnel->cv_path)
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <i class="fas fa-file-pdf text-danger fa-2x mb-2"></i>
                                            <h6>CV</h6>
                                            <a href="{{ asset('storage/' . $personnel->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($personnel->lettre_motivation_path)
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <i class="fas fa-file-alt text-primary fa-2x mb-2"></i>
                                            <h6>Lettre motivation</h6>
                                            <a href="{{ asset('storage/' . $personnel->lettre_motivation_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($personnel->contrat_path)
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <i class="fas fa-file-contract text-success fa-2x mb-2"></i>
                                            <h6>Contrat</h6>
                                            <a href="{{ asset('storage/' . $personnel->contrat_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($personnel->casier_judiciaire_path)
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <i class="fas fa-shield-alt text-warning fa-2x mb-2"></i>
                                            <h6>Casier judiciaire</h6>
                                            <a href="{{ asset('storage/' . $personnel->casier_judiciaire_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($personnel->certificat_medical_path)
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <i class="fas fa-heartbeat text-info fa-2x mb-2"></i>
                                            <h6>Certificat médical</h6>
                                            <a href="{{ asset('storage/' . $personnel->certificat_medical_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($personnel->diplomes_path)
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <i class="fas fa-graduation-cap text-dark fa-2x mb-2"></i>
                                            <h6>Diplômes</h6>
                                            <a href="{{ asset('storage/' . $personnel->diplomes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($personnel->attestations_path)
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <i class="fas fa-certificate text-secondary fa-2x mb-2"></i>
                                            <h6>Attestations</h6>
                                            <a href="{{ asset('storage/' . $personnel->attestations_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if(!$personnel->cv_path && !$personnel->lettre_motivation_path && !$personnel->contrat_path && !$personnel->casier_judiciaire_path && !$personnel->certificat_medical_path && !$personnel->diplomes_path && !$personnel->attestations_path)
                                <div class="text-center py-4">
                                    <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">Aucun document disponible</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Activer les tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endpush
