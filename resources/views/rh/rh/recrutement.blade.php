@extends('layouts.app')

@section('title', 'RH - Formulaire de Recrutement | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus mr-2 text-primary"></i>Recrutement d'un Nouvel Agent
            </h1>
            <p class="text-muted">Formulaire d'embauche conforme au Code du Travail Ivoirien</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('rh.contrats.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('rh.contrats.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Informations Personnelles -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">I. ÉTAT CIVIL & CONTACT</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nom *</label>
                                <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Prénom(s) *</label>
                                <input type="text" name="prenom" class="form-control" value="{{ old('prenom') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Sexe *</label>
                                <select name="sexe" class="form-select" required>
                                    <option value="">Sélectionner</option>
                                    <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date de Naissance *</label>
                                <input type="date" name="date_naissance" class="form-control" value="{{ old('date_naissance') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Lieu de Naissance *</label>
                                <input type="text" name="lieu_naissance" class="form-control" value="{{ old('lieu_naissance') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nationalité *</label>
                                <input type="text" name="nationalite" class="form-control" value="{{ old('nationalite', 'Ivoirienne') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Situation Matrimoniale *</label>
                                <select name="situation_matrimoniale" class="form-select" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Célibataire">Célibataire</option>
                                    <option value="Marié(e)">Marié(e)</option>
                                    <option value="Divorcé(e)">Divorcé(e)</option>
                                    <option value="Veuf/Veuve">Veuf/Veuve</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nombre d'enfants</label>
                                <input type="number" name="nombre_enfants" class="form-control" value="{{ old('nombre_enfants', 0) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Email (Connexion) *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Téléphone *</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">WhatsApp</label>
                                <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Adresse Géographique</label>
                                <input type="text" name="adresse_postale" class="form-control" placeholder="Ex: Cocody Angré, Rue L12" value="{{ old('adresse_postale') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identifiants Sociaux -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">II. IDENTIFIANTS SOCIAUX (CI)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">N° CNPS</label>
                                <input type="text" name="n_cnps" class="form-control" placeholder="Code CNPS" value="{{ old('n_cnps') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">N° CMU</label>
                                <input type="text" name="n_cmu" class="form-control" placeholder="Code CMU" value="{{ old('n_cmu') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Affectation -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 bg-secondary text-white">
                        <h6 class="m-0 font-weight-bold">III. AFFECTATION</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Service *</label>
                                <select name="service_id" class="form-select" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($services as $s)
                                        <option value="{{ $s->id }}">{{ $s->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Poste / Fonction *</label>
                                <input type="text" name="role" class="form-control" placeholder="Ex: Chauffeur, Comptable" value="{{ old('role') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contrat et Salaire -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-success text-white">
                        <h6 class="m-0 font-weight-bold">IV. CONTRAT & RÉMUNÉRATION (CODE DU TRAVAIL CI)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Type de Contrat *</label>
                                <select name="contrat" class="form-select" required>
                                    <option value="CDI">CDI (Indéterminé)</option>
                                    <option value="CDD">CDD (Déterminé)</option>
                                    <option value="Stage">Stage</option>
                                    <option value="Apprentissage">Apprentissage</option>
                                    <option value="Journalier">Journalier</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date d'Embauche *</label>
                                <input type="date" name="date_embauche" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date Fin (si CDD)</label>
                                <input type="date" name="date_fin_contrat" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Période d'essai (jours)</label>
                                <input type="number" name="periode_essai" class="form-control" placeholder="Ex: 30, 90">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Catégorie Professionnelle</label>
                                <select name="categorie_professionnelle" class="form-select">
                                    <option value="">Sélectionner</option>
                                    <option value="1A">1A (Manœuvre)</option>
                                    <option value="2">2ème (Ouvrier spécialisé)</option>
                                    <option value="3">3ème (Ouvrier qualifié)</option>
                                    <option value="4">4ème (Chef d'équipe)</option>
                                    <option value="5">5ème (Agent de maîtrise)</option>
                                    <option value="6">6ème (Technicien)</option>
                                    <option value="7">7ème (Cadre junior)</option>
                                    <option value="8">8ème (Cadre senior)</option>
                                </select>
                            </div>

                            <hr class="my-3">
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Salaire de Base *</label>
                                <div class="input-group">
                                    <input type="number" name="salaire_base" class="form-control" required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Sursalaire</label>
                                <div class="input-group">
                                    <input type="number" name="sursalaire" class="form-control">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Indemnité de Transport</label>
                                <div class="input-group">
                                    <input type="number" name="indemnite_transport" class="form-control">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Indemnité de Logement</label>
                                <div class="input-group">
                                    <input type="number" name="indemnite_logement" class="form-control">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Autres Primes</label>
                                <div class="input-group">
                                    <input type="number" name="autres_primes" class="form-control">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sécurité -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 bg-dark text-white">
                        <h6 class="m-0 font-weight-bold">V. ACCÈS SYSTÈME</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mot de passe provisoire *</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Confirmer le mot de passe *</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-5 text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                    <i class="fas fa-save me-2"></i>VALIDER LE RECRUTEMENT ET GÉNÉRER LE DOSSIER
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
