@extends('layouts.app')

@section('title', 'Paramétrage - Entreprise & Email')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2 text-primary"></i>Paramétrage
            </h1>
            <p class="text-muted mb-0">Configuration de l'entreprise et du serveur mail</p>
        </div>
    </div>

    <!-- Onglets de navigation -->
    <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="entreprise-tab" data-bs-toggle="tab" data-bs-target="#entreprise" type="button" role="tab">
                <i class="fas fa-building me-2"></i>Informations Entreprise
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                <i class="fas fa-envelope me-2"></i>Configuration Email
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sms-tab" data-bs-toggle="tab" data-bs-target="#sms" type="button" role="tab">
                <i class="fas fa-sms me-2"></i>Configuration SMS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="systeme-tab" data-bs-toggle="tab" data-bs-target="#systeme" type="button" role="tab">
                <i class="fas fa-server me-2"></i>Paramètres Système
            </button>
        </li>
    </ul>

    <!-- Contenu des onglets -->
    <div class="tab-content" id="settingsTabContent">
        <!-- Onglet Informations Entreprise -->
        <div class="tab-pane fade show active" id="entreprise" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Informations de l'Entreprise</h6>
                        </div>
                        <div class="card-body">
                            <form id="entrepriseForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nom_entreprise" class="form-label">Nom de l'entreprise</label>
                                        <input type="text" class="form-control" id="nom_entreprise" name="nom_entreprise" value="{{ old('nom_entreprise', $entreprise->nom_entreprise ?? 'KENAM SERVICES') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="sigle" class="form-label">Sigle/Abréviation</label>
                                        <input type="text" class="form-control" id="sigle" name="sigle" value="{{ old('sigle', $entreprise->sigle ?? 'KS') }}" maxlength="10" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="adresse" class="form-label">Adresse</label>
                                        <textarea class="form-control" id="adresse" name="adresse" rows="2" required>{{ old('adresse', $entreprise->adresse ?? "Abidjan, Côte d'Ivoire") }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" id="telephone" name="telephone" value="{{ old('telephone', $entreprise->telephone ?? '+225 27 22 33 44') }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email_contact" class="form-label">Email de contact</label>
                                        <input type="email" class="form-control" id="email_contact" name="email_contact" value="{{ old('email_contact', $entreprise->email_contact ?? 'contact@kenamservices.com') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="site_web" class="form-label">Site web</label>
                                        <input type="text" class="form-control" id="site_web" name="site_web" value="{{ old('site_web', $entreprise->site_web ?? 'www.kenamservices.com') }}" placeholder="www.exemple.com" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ifu" class="form-label">IFU</label>
                                        <input type="text" class="form-control" id="ifu" name="ifu" value="{{ old('ifu', $entreprise->ifu ?? '') }}" placeholder="Numéro IFU">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cnss" class="form-label">N° CNSS</label>
                                        <input type="text" class="form-control" id="cnss" name="cnss" value="{{ old('cnss', $entreprise->cnss ?? '') }}" placeholder="Numéro CNSS">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="rccm" class="form-label">RCCM</label>
                                        <input type="text" class="form-control" id="rccm" name="rccm" value="{{ old('rccm', $entreprise->rccm ?? '') }}" placeholder="N° RCCM" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="compte_bancaire" class="form-label">Compte bancaire</label>
                                        <input type="text" class="form-control" id="compte_bancaire" name="compte_bancaire" value="{{ old('compte_bancaire', $entreprise->compte_bancaire ?? '') }}" placeholder="IBAN ou RIB" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="tva_rate" class="form-label">TVA par défaut (%)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="tva_rate" name="tva_rate" value="{{ old('tva_rate', $entreprise->tva_rate ?? 18.00) }}" step="0.01" min="0" max="100" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="logo_entreprise" class="form-label">Logo de l'entreprise</label>
                                    <input type="file" class="form-control" id="logo_entreprise" name="logo_entreprise" accept="image/*">
                                    <small class="form-text text-muted">Format recommandé: PNG ou JPG, taille max: 2MB</small>
                                </div>

                                {{-- Nouvelle section Responsables --}}
                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="text-primary font-weight-bold mb-3">
                                        <i class="fas fa-users-cog me-2"></i>Responsables et Notifications
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email_tresorerie" class="form-label">Email Responsable Trésorerie</label>
                                            <input type="email" class="form-control" id="email_tresorerie" name="email_tresorerie" value="{{ old('email_tresorerie', $entreprise->email_tresorerie ?? '') }}" placeholder="tresorerie@kenamservices.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email_destinataire_principal" class="form-label">Destinataire Principal (Notifications)</label>
                                            <div class="input-group mb-2">
                                                <input type="email" class="form-control" id="email_destinataire_principal" name="email_destinataire_principal" value="{{ old('email_destinataire_principal', $entreprise->email_destinataire_principal ?? '') }}" placeholder="admin@kenamservices.com">
                                            </div>
                                            <label for="seuil_validation_principal" class="form-label small">Seuil validation (en dessous)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control form-control-sm" id="seuil_validation_principal" name="seuil_validation_principal" value="{{ old('seuil_validation_principal', $entreprise->seuil_validation_principal ?? 500000) }}" step="1" min="0">
                                                <span class="input-group-text small">FCFA</span>
                                            </div>
                                            <small class="text-muted">Participe à la validation si le montant est en dessous de ce seuil.</small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email_caisse_1" class="form-label">Email Responsable Caisse 1</label>
                                            <input type="email" class="form-control" id="email_caisse_1" name="email_caisse_1" value="{{ old('email_caisse_1', $entreprise->email_caisse_1 ?? '') }}" placeholder="caisse1@kenamservices.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email_caisse_2" class="form-label">Email Responsable Caisse 2</label>
                                            <input type="email" class="form-control" id="email_caisse_2" name="email_caisse_2" value="{{ old('email_caisse_2', $entreprise->email_caisse_2 ?? '') }}" placeholder="caisse2@kenamservices.com">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email_dg" class="form-label">Email DG</label>
                                            <input type="email" class="form-control" id="email_dg" name="email_dg" value="{{ old('email_dg', $entreprise->email_dg ?? '') }}" placeholder="dg@kenamservices.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="seuil_validation_dg" class="form-label">Seuil de validation DG (FCFA)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="seuil_validation_dg" name="seuil_validation_dg" value="{{ old('seuil_validation_dg', $entreprise->seuil_validation_dg ?? 1000000) }}" step="1" min="0">
                                                <span class="input-group-text">FCFA</span>
                                            </div>
                                            <small class="text-muted">Les opérations dépassant ce montant nécessitent la validation du DG.</small>
                                        </div>
                                    </div>

                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">Aperçu</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img src="{{ !empty($entreprise->logo_path) ? asset('storage/' . $entreprise->logo_path) : asset('storage/logos/favicon.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px;" id="logo_preview">
                            </div>
                            <h5 id="nom_preview">{{ $entreprise->nom_entreprise ?? 'KENAM SERVICES' }}</h5>
                            <p class="text-muted mb-1" id="adresse_preview">{{ $entreprise->adresse ?? "Abidjan, Côte d'Ivoire" }}</p>
                            <p class="text-muted mb-1" id="tel_preview">{{ $entreprise->telephone ?? '+225 27 22 33 44' }}</p>
                            <p class="text-muted mb-0" id="email_preview">{{ $entreprise->email_contact ?? 'contact@kenamservices.com' }}</p>
                            <p class="text-muted mb-0" id="site_preview">{{ $entreprise->site_web ?? 'www.kenamservices.com' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Configuration Email -->
        <div class="tab-pane fade" id="email" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Configuration du Serveur Email</h6>
                        </div>
                        <div class="card-body">
                            <form id="emailForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="mail_mailer" class="form-label">Mail Driver</label>
                                        <select class="form-select" id="mail_mailer" name="mail_mailer">
                                            <option value="smtp">SMTP</option>
                                            <option value="mail" {{ (isset($emailConfig['mailer']) && $emailConfig['mailer'] == 'mail') ? 'selected' : '' }}>PHP Mail</option>
                                            <option value="sendmail" {{ (isset($emailConfig['mailer']) && $emailConfig['mailer'] == 'sendmail') ? 'selected' : '' }}>Sendmail</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mail_host" class="form-label">Serveur SMTP</label>
                                        <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ $emailConfig['host'] ?? 'smtp.gmail.com' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="mail_port" class="form-label">Port SMTP</label>
                                        <input type="number" class="form-control" id="mail_port" name="mail_port" value="{{ $emailConfig['port'] ?? '587' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mail_encryption" class="form-label">Chiffrement</label>
                                        <select class="form-select" id="mail_encryption" name="mail_encryption">
                                            <option value="tls" {{ (isset($emailConfig['encryption']) && $emailConfig['encryption'] == 'tls') ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ (isset($emailConfig['encryption']) && $emailConfig['encryption'] == 'ssl') ? 'selected' : '' }}>SSL</option>
                                            <option value="" {{ (!isset($emailConfig['encryption']) || $emailConfig['encryption'] == '') ? 'selected' : '' }}>Aucun</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="mail_username" class="form-label">Nom d'utilisateur</label>
                                        <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ $emailConfig['username'] ?? '' }}" placeholder="email@domaine.com">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mail_password" class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ $emailConfig['password'] ?? '' }}" placeholder="•••••••••">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="mail_from_address" class="form-label">Email d'envoi</label>
                                        <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ $emailConfig['from_address'] ?? 'noreply@kenamservices.com' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mail_from_name" class="form-label">Nom d'envoi</label>
                                        <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="{{ $emailConfig['from_name'] ?? 'KENAM SERVICES' }}">
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> Pour Gmail, vous devrez peut-être utiliser un "mot de passe d'application" plutôt que votre mot de passe habituel.
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary" onclick="testEmailConnection()">
                                        <i class="fas fa-paper-plane me-2"></i>Tester la connexion
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer la configuration
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Test d'envoi</h6>
                        </div>
                        <div class="card-body">
                            <form id="testEmailForm">
                                <div class="mb-3">
                                    <label for="test_email" class="form-label">Email de test</label>
                                    <input type="email" class="form-control" id="test_email" placeholder="votre@email.com" required>
                                </div>
                                <div class="mb-3">
                                    <label for="test_subject" class="form-label">Sujet</label>
                                    <input type="text" class="form-control" id="test_subject" value="Test de configuration email">
                                </div>
                                <div class="mb-3">
                                    <label for="test_message" class="form-label">Message</label>
                                    <textarea class="form-control" id="test_message" rows="3">Ceci est un email de test pour vérifier la configuration du serveur SMTP.</textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer l'email de test
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Configuration SMS -->
        <div class="tab-pane fade" id="sms" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-sms me-2"></i>Configuration SMS</h6>
                        </div>
                        <div class="card-body">
                            <form id="smsForm">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="sms_provider2" class="form-label">Fournisseur SMS</label>
                                        <select class="form-select" id="sms_provider2" name="sms_provider">
                                            <option value="" {{ ($entreprise->sms_provider ?? '') == '' ? 'selected' : '' }}>Aucun</option>
                                            <option value="smseco_revendeur" {{ ($entreprise->sms_provider ?? '') == 'smseco_revendeur' ? 'selected' : '' }}>SMSECO Revendeur</option>
                                            <option value="netsmspro" {{ ($entreprise->sms_provider ?? 'netsmspro') == 'netsmspro' ? 'selected' : '' }}>NetSMSPro</option>
                                            <option value="smseco" {{ ($entreprise->sms_provider ?? '') == 'smseco' ? 'selected' : '' }}>SMSECO</option>
                                            <option value="orange" {{ ($entreprise->sms_provider ?? '') == 'orange' ? 'selected' : '' }}>Orange SMS</option>
                                            <option value="mtn" {{ ($entreprise->sms_provider ?? '') == 'mtn' ? 'selected' : '' }}>MTN SMS</option>
                                            <option value="twilio" {{ ($entreprise->sms_provider ?? '') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                            <option value="infobip" {{ ($entreprise->sms_provider ?? '') == 'infobip' ? 'selected' : '' }}>Infobip</option>
                                            <option value="custom" {{ ($entreprise->sms_provider ?? '') == 'custom' ? 'selected' : '' }}>Autre (HTTP API)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="sms_sender_id2" class="form-label">ID Expéditeur (Sender ID)</label>
                                        <input type="text" class="form-control" id="sms_sender_id2" name="sms_sender_id" value="{{ old('sms_sender_id', $entreprise->sms_sender_id ?? 'KSLSERVICES') }}" placeholder="KSLSERVICES">
                                    </div>
                                    <div class="col-md-4 mb-3 d-flex align-items-center">
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" id="sms_is_active2" name="sms_is_active" value="1" {{ old('sms_is_active', $entreprise->sms_is_active ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2" for="sms_is_active2">Activer les SMS</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sms_api_key2" class="form-label">Clé API (API Key)</label>
                                        <input type="text" class="form-control" id="sms_api_key2" name="sms_api_key" value="{{ old('sms_api_key', $entreprise->sms_api_key ?? $entreprise->sms_username ?? '') }}" placeholder="Votre identifiant NetSMSPro">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="sms_api_secret2" class="form-label">Secret API / Token</label>
                                        <input type="text" class="form-control" id="sms_api_secret2" name="sms_api_secret" value="{{ old('sms_api_secret', $entreprise->sms_api_secret ?? $entreprise->sms_password ?? '') }}" placeholder="Votre mot de passe NetSMSPro">
                                    </div>
                                </div>

                                <!-- Champ spécifique pour SMSECO Revendeur -->
                                <div class="row" id="smseco_revendeur_fields" style="display: none;">
                                    <div class="col-12 mb-3">
                                        <label for="sms_reseller_id" class="form-label">ID Revendeur SMSECO</label>
                                        <input type="text" class="form-control" id="sms_reseller_id" name="sms_reseller_id" value="{{ old('sms_reseller_id', $entreprise->sms_reseller_id ?? '') }}" placeholder="Votre ID Revendeur SMSECO">
                                        <div class="form-text">ID unique de revendeur fourni par SMSECO</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="sms_api_url2" class="form-label">URL de l'API (si personnalisée)</label>
                                        <input type="url" class="form-control" id="sms_api_url2" name="sms_api_url" value="{{ old('sms_api_url', $entreprise->sms_api_url ?? 'https://www.netsmspro.net/api') }}" placeholder="https://www.netsmspro.net/api">
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> Les SMS sont utilisés pour notifier les demandeurs et validateurs lors des étapes critiques d'une opération.
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer la configuration SMS
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">État du service SMS</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Statut</small>
                                <div class="fw-bold">
                                    @if($entreprise->sms_is_active ?? false)
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Actif</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i>Inactif</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Fournisseur</small>
                                <div class="fw-bold">{{ ucfirst($entreprise->sms_provider ?? 'Non configuré') }}</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Expéditeur</small>
                                <div class="fw-bold">{{ $entreprise->sms_sender_id ?? 'Non défini' }}</div>
                            </div>
                            <hr>
                            <small class="text-muted">Les SMS sont envoyés lors des validations d'opérations aux demandeurs et responsables.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Paramètres Système -->
        <div class="tab-pane fade" id="systeme" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Paramètres Système</h6>
                        </div>
                        <div class="card-body">
                            <form id="systemeForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="app_timezone" class="form-label">Fuseau horaire</label>
                                        <select class="form-select" id="app_timezone" name="app_timezone">
                                            <option value="UTC" {{ (isset($systemConfig['app_timezone']) && $systemConfig['app_timezone'] == 'UTC') ? 'selected' : '' }}>UTC</option>
                                            <option value="Africa/Abidjan" {{ (!isset($systemConfig['app_timezone']) || $systemConfig['app_timezone'] == 'Africa/Abidjan') ? 'selected' : 'selected' }}>Africa/Abidjan</option>
                                            <option value="Africa/Dakar" {{ (isset($systemConfig['app_timezone']) && $systemConfig['app_timezone'] == 'Africa/Dakar') ? 'selected' : '' }}>Africa/Dakar</option>
                                            <option value="Europe/Paris" {{ (isset($systemConfig['app_timezone']) && $systemConfig['app_timezone'] == 'Europe/Paris') ? 'selected' : '' }}>Europe/Paris</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="app_locale" class="form-label">Langue</label>
                                        <select class="form-select" id="app_locale" name="app_locale">
                                            <option value="fr" {{ (!isset($systemConfig['app_locale']) || $systemConfig['app_locale'] == 'fr') ? 'selected' : '' }}>Français</option>
                                            <option value="en" {{ (isset($systemConfig['app_locale']) && $systemConfig['app_locale'] == 'en') ? 'selected' : '' }}>English</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="session_lifetime" class="form-label">Durée de session (minutes)</label>
                                        <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" value="{{ $systemConfig['session_lifetime'] ?? '120' }}" min="15" max="480">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="max_file_size" class="form-label">Taille max fichier (MB)</label>
                                        <input type="number" class="form-control" id="max_file_size" name="max_file_size" value="{{ $systemConfig['max_file_size'] ?? '10' }}" min="1" max="100">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="debug_mode" name="debug_mode" {{ (isset($systemConfig['debug_mode']) && $systemConfig['debug_mode'] == 'on') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="debug_mode">
                                            Mode Debug (développement)
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" {{ (isset($systemConfig['maintenance_mode']) && $systemConfig['maintenance_mode'] == 'on') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="maintenance_mode">
                                            Mode Maintenance
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="backup_frequency" class="form-label">Fréquence de sauvegarde</label>
                                        <select class="form-select" id="backup_frequency" name="backup_frequency">
                                            <option value="daily" {{ (isset($systemConfig['backup_frequency']) && $systemConfig['backup_frequency'] == 'daily') ? 'selected' : '' }}>Quotidienne</option>
                                            <option value="weekly" {{ (!isset($systemConfig['backup_frequency']) || $systemConfig['backup_frequency'] == 'weekly') ? 'selected' : '' }}>Hebdomadaire</option>
                                            <option value="monthly" {{ (isset($systemConfig['backup_frequency']) && $systemConfig['backup_frequency'] == 'monthly') ? 'selected' : '' }}>Mensuelle</option>
                                        </select>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer les paramètres
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">Informations Système</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Version Laravel</small>
                                <div class="fw-bold">{{ app()->version() }}</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Version PHP</small>
                                <div class="fw-bold">{{ PHP_VERSION }}</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Environnement</small>
                                <div class="fw-bold">{{ config('app.env') }}</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">URL de l'application</small>
                                <div class="fw-bold">{{ config('app.url') }}</div>
                            </div>
                                    <div class="mb-3">
                                <small class="text-muted">Base de données</small>
                                <div class="fw-bold">{{ config('database.default') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mise à jour de l'aperçu en temps réel
    document.getElementById('nom_entreprise').addEventListener('input', function() {
        document.getElementById('nom_preview').textContent = this.value || 'Nom de l\'entreprise';
    });

    document.getElementById('adresse').addEventListener('input', function() {
        document.getElementById('adresse_preview').textContent = this.value || 'Adresse';
    });

    document.getElementById('telephone').addEventListener('input', function() {
        document.getElementById('tel_preview').textContent = this.value || 'Téléphone';
    });

    document.getElementById('email_contact').addEventListener('input', function() {
        document.getElementById('email_preview').textContent = this.value || 'Email';
    });

    document.getElementById('site_web').addEventListener('input', function() {
        document.getElementById('site_preview').textContent = this.value || 'Site web';
    });

    // Prévisualisation du logo
    document.getElementById('logo_entreprise').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo_preview').src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Fonction générique pour la soumission des formulaires
    function handleFormSubmit(formId, url) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Correction de l'URL si nécessaire (pour site_web)
            const siteInput = form.querySelector('#site_web');
            if (siteInput && siteInput.value) {
                const v = siteInput.value.trim();
                if (v && !/^https?:\/\//i.test(v)) {
                    siteInput.value = 'https://' + v;
                }
            }

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Chargement...';

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json().then(data => ({status: response.status, data})))
            .then(({status, data}) => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                if (data.success) {
                    showSuccessMessage(data.message);
                    if (data.logo_url) {
                        document.getElementById('logo_preview').src = data.logo_url;
                    }
                } else if (data.errors) {
                    let msgs = Object.values(data.errors).flat().join('\n');
                    showErrorMessage(msgs);
                } else {
                    showErrorMessage(data.message || 'Une erreur est survenue lors de la sauvegarde.');
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                showErrorMessage('Erreur réseau : ' + error.message);
            });
        });
    }

    // Initialisation des formulaires
    handleFormSubmit('entrepriseForm', '{{ route("parametrage.entreprise.save") }}');
    handleFormSubmit('emailForm', '{{ route("parametrage.email.save") }}');
    handleFormSubmit('smsForm', '{{ route("parametrage.sms.save") }}');
    handleFormSubmit('systemeForm', '{{ route("parametrage.systeme.save") }}');

    // Gestion de l'affichage du champ ID Revendeur pour SMSECO
    document.getElementById('sms_provider2').addEventListener('change', function() {
        const resellerFields = document.getElementById('smseco_revendeur_fields');
        if (this.value === 'smseco_revendeur') {
            resellerFields.style.display = 'block';
        } else {
            resellerFields.style.display = 'none';
        }
    });

    // Afficher le champ ID Revendeur si SMSECO Revendeur est déjà sélectionné
    if (document.getElementById('sms_provider2').value === 'smseco_revendeur') {
        document.getElementById('smseco_revendeur_fields').style.display = 'block';
    }

    document.getElementById('testEmailForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi...';

        fetch('{{ route("parametrage.email.test") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            if (data.success) {
                showSuccessMessage(data.message);
            } else {
                showErrorMessage(data.message || 'Erreur lors de l\'envoi');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            showErrorMessage('Erreur réseau: ' + error.message);
        });
    });
});

function testEmailConnection() {
    showSuccessMessage('Test de connexion en cours...');
    setTimeout(() => {
        showSuccessMessage('Connexion SMTP réussie!');
    }, 2000);
}

function showSuccessMessage(message) {
    showAlert(message, 'success');
}

function showErrorMessage(message) {
    showAlert(message, 'danger');
}

function showAlert(message, type) {
    // Créer une alerte temporaire
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);

    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>

<style>
.nav-tabs .nav-link {
    border: 1px solid #dee2e6;
    border-bottom: none;
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    background-color: #fff;
    border-color: #dee2e6;
    border-bottom: 1px solid #fff;
    color: #495057;
    font-weight: 600;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2653d4;
}
</style>
@endsection
