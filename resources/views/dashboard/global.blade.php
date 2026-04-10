@extends('layouts.app')

@section('title', 'Dashboard Global - KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">

    <!-- COMPTABILITÉ & TRÉSORERIE -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-coins text-success me-2"></i>COMPTABILITÉ & TRÉSORERIE</h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">CA Total</p>
                        <h3 class="text-success fw-bold">{{ number_format($comptabilite['ca_total'] ?? 0, 0) }}</h3>
                        <small class="text-muted"><i class="fas fa-coins me-1"></i>FCFA</small>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-money-bill-wave fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Factures Total</p>
                        <h3 class="text-primary fw-bold">{{ $comptabilite['factures_total'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-file me-1"></i>Émises</small>
                        <div class="text-primary opacity-25 float-end"><i class="fas fa-file-invoice-dollar fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Factures Payées</p>
                        <h3 class="text-info fw-bold">{{ $comptabilite['factures_payees'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-check-circle me-1"></i>Réglées</small>
                        <div class="text-info opacity-25 float-end"><i class="fas fa-receipt fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Impayées</p>
                        <h3 class="text-danger fw-bold">{{ $comptabilite['factures_impayees'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>À relancer</small>
                        <div class="text-danger opacity-25 float-end"><i class="fas fa-exclamation-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Montant Encaissé</p>
                        <h3 class="text-warning fw-bold">{{ number_format($comptabilite['montant_encaisse'] ?? 0, 0) }}</h3>
                        <small class="text-muted"><i class="fas fa-check me-1"></i>FCFA</small>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-wallet fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-secondary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Montant En Attente</p>
                        <h3 class="text-secondary fw-bold">{{ number_format($comptabilite['montant_en_attente'] ?? 0, 0) }}</h3>
                        <small class="text-muted"><i class="fas fa-hourglass-half me-1"></i>FCFA</small>
                        <div class="text-secondary opacity-25 float-end"><i class="fas fa-hourglass fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-dark h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Taux Recouvrement</p>
                        <h3 class="text-dark fw-bold">{{ $comptabilite['ca_total'] ?? 0 > 0 ? round(($comptabilite['montant_encaisse'] ?? 0) / ($comptabilite['ca_total'] ?? 0) * 100) : 0 }}%</h3>
                        <small class="text-muted"><i class="fas fa-percentage me-1"></i>Performance</small>
                        <div class="text-dark opacity-25 float-end"><i class="fas fa-chart-pie fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 h-100" style="border-left-color: #28a745 !important;">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Solde Trésorerie</p>
                        <h3 class="fw-bold" style="color: #28a745;">{{ number_format($comptabilite['solde_tresorerie'] ?? 0, 0) }}</h3>
                        <small class="text-muted"><i class="fas fa-university me-1"></i>FCFA</small>
                        <div class="opacity-25 float-end" style="color: #28a745;"><i class="fas fa-piggy-bank fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <a href="{{ route('comptabilite.rapports.indicateurs-financiers') }}" class="text-decoration-none d-block h-100">
                    <div class="card shadow-sm border-start border-5 border-dark h-100 finance-indicators-card"
                         data-bs-toggle="tooltip"
                         data-bs-placement="top"
                         title="Synthèse financière avancée: marge nette, liquidité, TVA estimée, IS projeté, TFP, couverture des dettes et délais d'encaissement.">
                        <div class="card-body">
                            <p class="text-muted small mb-1 d-flex align-items-center gap-2">
                                <span>Indicateurs Financiers</span>
                                <i class="fas fa-circle-info text-primary"></i>
                            </p>
                            <h3 class="text-dark fw-bold">8+</h3>
                            <small class="text-muted d-block"><i class="fas fa-chart-pie me-1"></i>Ratios détaillés</small>
                            <small class="text-muted d-block">Résultat estimé: {{ number_format($comptabilite['resultat_estime'] ?? 0, 0) }} FCFA</small>
                            <small class="text-muted d-block">Recouvrement: {{ number_format($comptabilite['taux_recouvrement'] ?? 0, 1, ',', ' ') }} %</small>
                            <div class="text-dark opacity-25 float-end"><i class="fas fa-chart-pie fa-2x"></i></div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">EBE</p>
                        <h3 class="fw-bold {{ ($comptabilite['ebe'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($comptabilite['ebe'] ?? 0, 0) }}</h3>
                        <div class="text-info opacity-25 float-end"><i class="fas fa-chart-line fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Capacité d'autofinancement</p>
                        <h3 class="fw-bold {{ ($comptabilite['caf'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($comptabilite['caf'] ?? 0, 0) }}</h3>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-sack-dollar fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Dette fournisseur</p>
                        <h3 class="text-warning fw-bold">{{ number_format($comptabilite['dette_fournisseur'] ?? 0, 0) }}</h3>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-hand-holding-dollar fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">TRI</p>
                        <h3 class="fw-bold {{ ($comptabilite['tri'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($comptabilite['tri'] ?? 0, 2, ',', ' ') }}%</h3>
                        <div class="text-primary opacity-25 float-end"><i class="fas fa-gauge-high fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RH & PERSONNEL -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-users text-primary me-2"></i>RH & PERSONNEL</h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Total Personnel</p>
                        <h3 class="text-primary fw-bold">{{ $rh['total_personnel'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-users me-1"></i>Effectif</small>
                        <div class="text-primary opacity-25 float-end"><i class="fas fa-users fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Personnel Actif</p>
                        <h3 class="text-success fw-bold">{{ $rh['actifs'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-check-circle me-1"></i>En poste</small>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-user-check fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">En Période Essai</p>
                        <h3 class="text-warning fw-bold">{{ $rh['en_essai'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>Essai</small>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-user-clock fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Congés Approuvés</p>
                        <h3 class="text-danger fw-bold">{{ $rh['conges'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-calendar me-1"></i>En cours</small>
                        <div class="text-danger opacity-25 float-end"><i class="fas fa-umbrella-beach fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SÉCURITÉ & SURVEILLANCE -->
    <div class="mb-5">
        <h3 class="mb-3 d-flex align-items-center justify-content-between">
            <span><i class="fas fa-shield-alt text-dark me-2"></i>SÉCURITÉ & SURVEILLANCE</span>
            <div id="camera-status-indicator" class="d-flex align-items-center">
                <span class="badge bg-secondary me-2" id="camera-status-badge">
                    <i class="fas fa-circle-notch fa-spin me-1"></i>Vérification caméra...
                </span>
                <a href="{{ route('hikvision.live') }}" class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3">
                    <i class="fas fa-video me-1"></i>Ouvrir la vue en direct
                </a>
            </div>
        </h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-dark h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Status Dispositif</p>
                        <h4 class="fw-bold mb-0" id="device-status-text">Recherche...</h4>
                        <small class="text-muted" id="device-ip-info">IP Caméra : {{ env('HIKVISION_IP', '192.168.1.70') }}</small>
                        <div class="text-dark opacity-25 float-end"><i class="fas fa-camera fa-2x"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Dernière Détection</p>
                        <h4 class="fw-bold mb-0">Reconnaissance</h4>
                        <small class="text-muted">Système Hikvision Actif</small>
                        <div class="text-primary opacity-25 float-end"><i class="fas fa-user-shield fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOURNISSEURS -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-handshake text-info me-2"></i>FOURNISSEURS & ACHATS</h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Total Fournisseurs</p>
                        <h3 class="text-info fw-bold">{{ $fournisseurs['total_fournisseurs'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-store me-1"></i>Référencés</small>
                        <div class="text-info opacity-25 float-end"><i class="fas fa-building fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Fournisseurs Actifs</p>
                        <h3 class="text-success fw-bold">{{ $fournisseurs['actifs'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-check me-1"></i>Disponibles</small>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-thumbs-up fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Commandes En Cours</p>
                        <h3 class="text-warning fw-bold">{{ $fournisseurs['commandes_en_cours'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-hourglass-half me-1"></i>En attente</small>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-truck fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Montant Total Commandes</p>
                        <h3 class="text-primary fw-bold">{{ number_format($fournisseurs['montant_total_commandes'] ?? 0, 0) }}</h3>
                        <small class="text-muted"><i class="fas fa-money-bill me-1"></i>FCFA</small>
                        <div class="text-primary opacity-25 float-end"><i class="fas fa-shopping-cart fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OPÉRATIONS -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-cog text-warning me-2"></i>OPÉRATIONS</h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Total Opérations</p>
                        <h3 class="text-warning fw-bold">{{ $operations['total_operations'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-list me-1"></i>Enregistrées</small>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-tasks fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">En Cours</p>
                        <h3 class="text-info fw-bold">{{ $operations['en_cours'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-spinner me-1"></i>En progression</small>
                        <div class="text-info opacity-25 float-end"><i class="fas fa-play-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Terminées</p>
                        <h3 class="text-success fw-bold">{{ $operations['terminees'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-check me-1"></i>Complétées</small>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-check-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Revenue Total</p>
                        <h3 class="text-success fw-bold">{{ number_format($operations['revenue_total'] ?? 0, 0) }}</h3>
                        <small class="text-muted"><i class="fas fa-coins me-1"></i>FCFA</small>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-piggy-bank fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CLIENTS -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-user-tie text-success me-2"></i>CLIENTS & COMMANDES</h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Total Clients</p>
                        <h3 class="text-success fw-bold">{{ $clients['total_clients'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-users me-1"></i>Existants</small>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-people-arrows fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Clients Actifs</p>
                        <h3 class="text-info fw-bold">{{ $clients['actifs'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-check me-1"></i>Actifs</small>
                        <div class="text-info opacity-25 float-end"><i class="fas fa-user-check fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Commandes En Attente</p>
                        <h3 class="text-warning fw-bold">{{ $clients['commandes_en_attente'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-hourglass me-1"></i>Traitées</small>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-clock fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Montant Total Commandes</p>
                        <h3 class="text-primary fw-bold">{{ number_format($clients['montant_total_commandes'] ?? 0, 0) }}</h3>
                        <small class="text-muted"><i class="fas fa-money-bill me-1"></i>FCFA</small>
                        <div class="text-primary opacity-25 float-end"><i class="fas fa-cart-shopping fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAGASIN & STOCK -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-boxes text-danger me-2"></i>MAGASIN & STOCK</h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Total Produits</p>
                        <h3 class="text-danger fw-bold">{{ $magasin['total_produits'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-box me-1"></i>Références</small>
                        <div class="text-danger opacity-25 float-end"><i class="fas fa-boxes fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Valeur Stock Total</p>
                        <h3 class="text-primary fw-bold">{{ number_format($magasin['stock_total_valeur'] ?? 0, 0) }}</h3>
                        <small class="text-muted"><i class="fas fa-money-bill me-1"></i>FCFA</small>
                        <div class="text-primary opacity-25 float-end"><i class="fas fa-chart-line fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Articles En Rupture</p>
                        <h3 class="text-warning fw-bold">{{ $magasin['produits_rupture'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-exclamation-triangle me-1"></i>À réapprovisionner</small>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-triangle-exclamation fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Articles En Vente</p>
                        <h3 class="text-success fw-bold">{{ $magasin['articles_en_vente'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-tag me-1"></i>Disponibles</small>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-check-square fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DÉTAILS DES CAISSES -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-cash-register text-info me-2"></i>DÉTAILS DES CAISSES</h3>
        <div class="row g-3">
            @if(isset($caisses) && count($caisses) > 0)
                @foreach($caisses as $caisse)
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-start border-5 border-info h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-2">{{ $caisse->nom }}</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-muted small mb-1">Solde Actuel</p>
                                        <h4 class="text-info fw-bold">{{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA</h4>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted small mb-1">Statut</p>
                                        <span class="badge bg-{{ $caisse->est_active ? 'success' : 'danger' }}">
                                            {{ $caisse->est_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                @if($caisse->responsable)
                                    <div class="mt-2">
                                        <p class="text-muted small mb-1">Responsable</p>
                                        <p class="text-dark fw-bold">{{ $caisse->responsable }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucune caisse configurée pour le moment.
                        </div>
                    </div>
                @endif
        </div>
    </div>

    <!-- VÉHICULES & FLOTTE -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-car text-secondary me-2"></i>VÉHICULES & FLOTTE</h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-secondary h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Total Véhicules</p>
                        <h3 class="text-secondary fw-bold">{{ $vehicules['total_vehicules'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-car me-1"></i>Flotte totale</small>
                        <div class="text-secondary opacity-25 float-end"><i class="fas fa-car fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Disponibles</p>
                        <h3 class="text-success fw-bold">{{ $vehicules['disponibles'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-check-circle me-1"></i>Prêts à rouler</small>
                        <div class="text-success opacity-25 float-end"><i class="fas fa-circle-check fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">En Mission</p>
                        <h3 class="text-info fw-bold">{{ $vehicules['en_mission'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-road me-1"></i>En déplacement</small>
                        <div class="text-info opacity-25 float-end"><i class="fas fa-map-location-dot fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">En Maintenance</p>
                        <h3 class="text-warning fw-bold">{{ $vehicules['en_maintenance'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-screwdriver me-1"></i>Entretien</small>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-toolbox fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ALERTES & RISQUES -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="fas fa-bell text-danger me-2"></i>ALERTES & RISQUES</h3>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Factures Échues</p>
                        <h3 class="text-danger fw-bold">{{ $alerts['factures_echues'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-file-invoice me-1"></i>À relancer</small>
                        <div class="text-danger opacity-25 float-end"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Stock Faible</p>
                        <h3 class="text-warning fw-bold">{{ $alerts['stock_faible'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-cube me-1"></i>Articles</small>
                        <div class="text-warning opacity-25 float-end"><i class="fas fa-boxes fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Assurances Expirées</p>
                        <h3 class="text-danger fw-bold">{{ $alerts['assurances_expirees'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-shield-alt me-1"></i>À renouveler</small>
                        <div class="text-danger opacity-25 float-end"><i class="fas fa-calendar-times fa-2x"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Retards Retours</p>
                        <h3 class="text-danger fw-bold">{{ $alerts['retards_retour'] ?? 0 }}</h3>
                        <small class="text-muted"><i class="fas fa-hourglass-end me-1"></i>Dépassés</small>
                        <div class="text-danger opacity-25 float-end"><i class="fas fa-clock fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .card:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }

    .finance-indicators-card {
        cursor: pointer;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        return;
    }

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
        new bootstrap.Tooltip(element);
    });

    // Gestion du statut caméra Hikvision
    function updateCameraStatus() {
        const statusBadge = document.getElementById('camera-status-badge');
        if (!statusBadge) return;

        fetch("{{ route('hikvision.status') }}")
            .then(response => response.json())
            .then(data => {
                if (data.status === 'online') {
                    statusBadge.className = 'badge bg-success me-2';
                    statusBadge.innerHTML = '<i class="fas fa-circle me-1"></i>Caméra Connectée';
                } else {
                    statusBadge.className = 'badge bg-danger me-2';
                    statusBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i>Caméra Déconnectée';
                }
            })
            .catch(() => {
                statusBadge.className = 'badge bg-secondary me-2';
                statusBadge.textContent = 'Erreur Status';
            });
    }

    updateCameraStatus();
    setInterval(updateCameraStatus, 60000); // Actualiser chaque minute
});
</script>
@endsection
