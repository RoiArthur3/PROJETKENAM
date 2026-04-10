@extends('layouts.app')

@section('title', 'Dashboard Principal | KENAM SERVICES')

@push('styles')
<link href="{{ asset('css/dashboard-modern.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-clickable.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-layout.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">

        <!-- MODERN DASHBOARD HEADER -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-chart-line me-3"></i>Tableau de Bord Principal
                    </h1>
                    <p class="subtitle mb-0">
                        Vue d'ensemble complète de tous les modules KENAM SERVICES (Données Synchronisées)
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-light btn-sm" onclick="exporterDashboard()">
                            <i class="fas fa-file-excel me-1"></i>Exporter
                        </button>
                        <button class="btn btn-light btn-sm" onclick="imprimerDashboard()">
                            <i class="fas fa-print me-1"></i>Imprimer
                        </button>
                        <button class="btn btn-light btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Actualiser
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end align-items-center mb-4">
            <span class="data-source-badge bdd">
                <i class="fas fa-database me-1"></i>Données Synchronisées
            </span>
            <span class="ms-2 text-muted">
                <i class="fas fa-clock me-1"></i>{{ now()->format('d/m/Y H:i') }}
            </span>
        </div>

        <!-- INDICATEURS FINANCIERS CLÉS -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">EBE</h6>
                                <h3 class="mb-0 fw-bold {{ ($comptabilite['ebe'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($comptabilite['ebe'] ?? 0, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                                </h3>
                                <div class="mt-2 small text-muted">Calcul métier plateforme (missions - coûts directs - charges)</div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-info text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-chart-column"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Capacité d'autofinancement</h6>
                                <h3 class="mb-0 fw-bold {{ ($comptabilite['caf'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($comptabilite['caf'] ?? 0, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                                </h3>
                                <div class="mt-2 small text-muted">CAF métier estimée selon les flux de la plateforme</div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-success text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-piggy-bank"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Dette fournisseur</h6>
                                <h3 class="mb-0 fw-bold text-warning">
                                    {{ number_format($comptabilite['dette_fournisseur'] ?? 0, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small>
                                </h3>
                                <div class="mt-2 small text-muted">Reste à payer fournisseurs</div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-warning text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">TRI</h6>
                                <h3 class="mb-0 fw-bold {{ ($comptabilite['tri'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($comptabilite['tri'] ?? 0, 2, ',', ' ') }} <small class="fs-6 text-muted">%</small>
                                </h3>
                                <div class="mt-2 small text-muted">TRI opérationnel basé sur les coûts fournisseurs missions</div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-primary text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-percent"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPIs PRINCIPAUX -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <a href="{{ route('tresorerie.dashboard') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Trésorerie Globale</h6>
                                    <h3 class="mb-0 fw-bold">{{ number_format($tresorerie['total'] ?? 0, 0, ',', ' ') }} <small class="fs-6">FCFA</small></h3>
                                    <div class="mt-2 small {{ ($tresorerie['variation_mois'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fas fa-arrow-{{ ($tresorerie['variation_mois'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>
                                        {{ number_format(abs($tresorerie['variation_mois'] ?? 0), 0, ',', ' ') }} ce mois
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="avatar-circle bg-primary text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Chiffre d'Affaires</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['ca']['mois'] ?? 0, 0, ',', ' ') }} <small class="fs-6">FCFA</small></h3>
                                <div class="mt-2 small text-info">
                                    <i class="fas fa-calendar-alt me-1"></i>{{ $stats['ca']['mois_label'] ?? 'Mois en cours' }}
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-info text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Missions actives</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['missions']['actives'] ?? 0 }}</h3>
                                <div class="mt-2 small text-success">
                                    <i class="fas fa-check-circle me-1"></i>{{ $stats['missions']['mois'] ?? 0 }} ce mois
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-success text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-truck-loading"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Total Clients</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['clients']['total'] ?? 0 }}</h3>
                                <div class="mt-2 small text-warning">
                                    <i class="fas fa-users me-1"></i>Base active
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-warning text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🚨 ALERTES CRITIQUES & PERFORMANCE -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <a href="{{ route('comptabilite.factures.index') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 bg-white border-start border-danger border-4" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-danger mb-1 small fw-bold text-uppercase">Factures Impayées</h6>
                                    <h3 class="mb-0 fw-bold">{{ $comptabilite['factures_impayees'] ?? 0 }}</h3>
                                    <div class="mt-2 small text-muted">
                                        {{ number_format($comptabilite['montant_impaye'] ?? 0, 0, ',', ' ') }} FCFA
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="avatar-circle bg-danger text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <a href="{{ route('validations.dashboard') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 bg-white border-start border-warning border-4" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-warning mb-1 small fw-bold text-uppercase">Validations Requises</h6>
                                    <h3 class="mb-0 fw-bold">{{ ($stats['validations']['en_attente'] ?? 0) + ($stats['validations']['paiement_en_attente'] ?? 0) }}</h3>
                                    <div class="mt-2 small text-muted">
                                        {{ $stats['validations']['en_attente'] ?? 0 }} missions &bull; {{ $stats['validations']['paiement_en_attente'] ?? 0 }} paiements
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="avatar-circle bg-warning text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                        <i class="fas fa-check-double"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <a href="{{ route('warehouse.dashboard') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 bg-white border-start border-info border-4" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-info mb-1 small fw-bold text-uppercase">Stock Critique</h6>
                                    <h3 class="mb-0 fw-bold">{{ $magasin['alertes_stock'] ?? 0 }}</h3>
                                    <div class="mt-2 small text-muted">
                                        Articles en alerte
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="avatar-circle bg-info text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <a href="{{ route('parc.dashboard') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 bg-white border-start border-primary border-4" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-primary mb-1 small fw-bold text-uppercase">Visites Techniques</h6>
                                    <h3 class="mb-0 fw-bold text-danger">{{ $alerts['visites_expirees'] ?? 0 }}</h3>
                                    <div class="mt-2 small text-muted">
                                        Alertes (15 jours)
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="avatar-circle bg-primary text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- SECTION GRAPHIQUES OPÉRATIONS -->
        <div class="row g-4 mb-5">
            <!-- Camembert Opérations par Statut -->
            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm border-0 bg-white h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-chart-pie me-2 text-primary"></i>Opérations par Statut
                            <span class="badge bg-primary ms-2">{{ $operationsParStatut['total'] ?? 0 }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(($operationsParStatut['total'] ?? 0) > 0 && count($operationsParStatut['labels'] ?? []) > 0)
                            <div style="height: 280px; position: relative; margin-bottom: 20px;">
                                <canvas id="chartOperationsStatut" style="display: block;"></canvas>
                            </div>
                            <div class="mt-2 pt-2" style="border-top: 1px solid #eee;">
                                @foreach($operationsParStatut['labels'] ?? [] as $index => $label)
                                    @php
                                        $count = $operationsParStatut['data'][$index] ?? 0;
                                        $percentage = ($operationsParStatut['total'] > 0) ? round(($count / $operationsParStatut['total']) * 100, 1) : 0;
                                        $color = $operationsParStatut['colors'][$index] ?? '#999';
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2 small" style="padding: 4px 0;">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <span class="badge rounded-circle me-2" style="background-color: {{ $color }}; width: 14px; height: 14px; display: inline-block; flex-shrink: 0;"></span>
                                            <span class="fw-bold text-dark">{{ $label }}</span>
                                        </div>
                                        <div class="text-end ms-2">
                                            <span style="background-color: {{ $color }}20; padding: 2px 8px; border-radius: 4px; color: {{ $color }}; font-weight: bold;">{{ $count }}</span>
                                            <span class="ms-1 text-muted">({{ $percentage }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-chart-pie fa-2x mb-2"></i>
                                <p class="mb-0">Aucune opération disponible</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Graphique Évolution 6 derniers mois -->
            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm border-0 bg-white h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-chart-line me-2 text-success"></i>Évolution des Opérations (6 derniers mois)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div style="height: 280px; position: relative;">
                            <canvas id="chartOperationsEvolution"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Performance KPIs -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Performance Mensuelle</h5>
                <p class="text-muted small mt-1">Résumé des revenus, dépenses et bénéfices du mois</p>
            </div>
        </div>

        <!-- Performance KPIs -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 shadow-sm border-0 bg-white">
                    <div class="card-body">
                        <h6 class="card-title fw-bold text-dark mb-4 d-flex justify-content-between align-items-center">
                            <span>Résultat Net du Mois</span>
                            <span class="badge bg-primary px-3 py-2">{{ number_format($comptabilite['resultat_mois'] ?? 0, 0, ',', ' ') }} FCFA</span>
                        </h6>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Revenus</span>
                                <span class="text-success fw-bold">+{{ number_format($comptabilite['revenus_mois'] ?? 0, 0, ',', ' ') }}</span>
                            </div>
                            <div class="progress mb-4" style="height: 10px; border-radius: 5px; background-color: #f0f0f0;">
                                <div class="progress-bar bg-success" style="width: 100%; border-radius: 5px;"></div>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Dépenses</span>
                                <span class="text-danger fw-bold">-{{ number_format($comptabilite['depenses_mois'] ?? 0, 0, ',', ' ') }}</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px; background-color: #f0f0f0;">
                                @php
                                    $taux_depense = ($comptabilite['revenus_mois'] ?? 0) > 0 
                                        ? min(100, (($comptabilite['depenses_mois'] ?? 0) / ($comptabilite['revenus_mois'] ?? 0)) * 100) 
                                        : 0;
                                @endphp
                                <div class="progress-bar bg-danger" style="width: {{ $taux_depense }}%; border-radius: 5px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 shadow-sm border-0 bg-white">
                    <div class="card-body">
                        <h6 class="card-title fw-bold text-dark mb-4">Assurances</h6>
                        <div class="text-center py-3">
                            <div class="row align-items-center">
                                <div class="col-6 border-end">
                                    <h4 class="fw-bold mb-2">{{ $assurances['total_engins'] ?? 0 }}</h4>
                                    <p class="text-muted small text-uppercase mb-0">Engins Assurés</p>
                                </div>
                                <div class="col-6">
                                    <h4 class="fw-bold text-danger mb-2">{{ $assurances['assurances_expiration_2mois'] ?? 0 }}</h4>
                                    <p class="text-muted small text-uppercase mb-0">Expiration (2 mois)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NOUVELLES SECTIONS SYNCHRONISÉES -->
        <div class="row g-4 mb-5">
            <!-- 1. Flux de Trésorerie Prévisionnel -->
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 shadow-sm border-0 bg-white">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-project-diagram me-2 text-primary"></i>Flux de Trésorerie Prévisionnel
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-4">
                            <div class="col-6 border-end">
                                <h5 class="text-success mb-0 fw-bold">{{ number_format($previsionalTreasury['revenu_attendu'] ?? 0, 0, ',', ' ') }} <small>FCFA</small></h5>
                                <p class="text-muted small text-uppercase mb-0">Factures à encaisser</p>
                            </div>
                            <div class="col-6">
                                <h5 class="text-danger mb-0 fw-bold">{{ number_format($previsionalTreasury['depenses_prevues'] ?? 0, 0, ',', ' ') }} <small>FCFA</small></h5>
                                <p class="text-muted small text-uppercase mb-0">Dépenses approuvées</p>
                            </div>
                        </div>
                        <div class="p-3 rounded {{ ($previsionalTreasury['solde_previsionnel'] ?? 0) >= 0 ? 'bg-light-success' : 'bg-light-danger' }}" style="background-color: {{ ($previsionalTreasury['solde_previsionnel'] ?? 0) >= 0 ? '#e8f5e9' : '#ffebee' }};">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small">Solde Prévisionnel Net</span>
                                    <h4 class="mb-0 fw-bold {{ ($previsionalTreasury['solde_previsionnel'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($previsionalTreasury['solde_previsionnel'] ?? 0, 0, ',', ' ') }} FCFA
                                    </h4>
                                </div>
                                <i class="fas {{ ($previsionalTreasury['solde_previsionnel'] ?? 0) >= 0 ? 'fa-chart-line text-success' : 'fa-exclamation-triangle text-danger' }} fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SMS Credits & RH Alerts -->
            <div class="col-lg-6 col-md-12">
                <div class="row g-4 h-100">
                    <!-- SMS Widget -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0 bg-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-muted mb-1 small fw-bold text-uppercase">SMS & Notifications</h6>
                                        <h4 class="mb-1 fw-bold">{{ $smsStats['envoyes_mois'] ?? 0 }} <small class="fs-6 text-muted">envoyés ce mois</small></h4>
                                        <div class="mt-2">
                                            <span class="badge {{ ($smsStats['is_active'] ?? false) ? 'bg-success' : 'bg-danger' }}">
                                                {{ ($smsStats['is_active'] ?? false) ? 'Service Actif' : 'Service Inactif' }}
                                            </span>
                                            <span class="small text-muted ms-2">Provider: <strong>{{ $smsStats['provider'] ?? 'N/A' }}</strong></span>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <div class="avatar-circle bg-info text-white shadow-sm" style="width: 54px; height: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            <i class="fas fa-sms"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- RH Contracts Alert -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0 bg-white border-start border-warning border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-warning mb-1 small fw-bold text-uppercase">Alertes RH (Contrats)</h6>
                                        <h4 class="mb-0 fw-bold">{{ $expiringContracts ?? 0 }} <small class="fs-6 text-muted">CDD/Stages expirant</small></h4>
                                        <p class="mb-0 mt-1 small text-muted">Échéance dans les 30 prochains jours</p>
                                    </div>
                                    <div class="ms-3">
                                        <div class="avatar-circle bg-warning text-white shadow-sm" style="width: 54px; height: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                            <i class="fas fa-file-contract"></i>
                                        </div>
                                    </div>
                                </div>
                                @if(($expiringContracts ?? 0) > 0)
                                <div class="mt-2 text-end">
                                    <a href="{{ route('rh.contrats.index') }}" class="btn btn-sm btn-link text-warning p-0">Voir les détails <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                                @endif
                            </div>
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
// ===== FONCTIONS DASHBOARD MODERNE =====

// Actualiser les alertes
function refreshAlertes() {
    // Animation de chargement
    const alertesSection = document.querySelector('.section-card:last-child');
    if (alertesSection) {
        alertesSection.style.opacity = '0.5';

        setTimeout(() => {
            location.reload();
        }, 500);
    }
}

// Animation du compteur d'alertes
function updateAlertesCount() {
    const alertesCount = document.getElementById('alertes-count');
    if (alertesCount) {
        let count = 0;

        // Factures impayées
        const facturesImpayees = {{ $comptabilite['factures_impayees'] ?? 0 }};
        if (facturesImpayees > 5) count++;

        // Stock critique
        const stockAlerte = {{ $magasin['alertes_stock'] ?? 0 }};
        if (stockAlerte > 3) count++;

        // Opérations en attente
        const operationsEnCours = {{ $stats['operations']['en_attente_count'] ?? 0 }};
        if (operationsEnCours > 10) count++;

        alertesCount.textContent = count;
        alertesCount.className = count > 0 ? 'badge bg-danger' : 'badge bg-secondary';
    }
}

// Initialiser les animations au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateAlertesCount();

    // Animation d'entrée pour les KPIs
    const kpiCards = document.querySelectorAll('.kpi-card');
    kpiCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Initialiser les graphiques
    initChartsOperations();

    // Auto-rafraîchissement des alertes toutes les 30 secondes
    setInterval(() => {
        updateAlertesCount();
    }, 30000);
});

// Initialiser les graphiques des opérations
function initChartsOperations() {
    // 1. Graphique - Opérations par Statut (Camembert)
    const ctxStatut = document.getElementById('chartOperationsStatut');
    if (ctxStatut) {
        const labels = @json($operationsParStatut['labels'] ?? []);
        const data = @json($operationsParStatut['data'] ?? []);
        const colors = @json($operationsParStatut['colors'] ?? []);
        
        // Vérifier s'il y a des données
        if (labels.length > 0 && data.length > 0) {
            new Chart(ctxStatut, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.length > 0 ? colors : [
                            '#007BFF', '#28A745', '#FFC107', '#17A2B8', 
                            '#DC3545', '#6C757D', '#6F42C1'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2.5,
                        hoverBorderColor: '#333',
                        hoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 12, weight: 'bold' },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: '#333'
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            cornerRadius: 4,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed || 0) / total * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // 2. Graphique - Évolution Opérations 6 mois
    const ctxEvolution = document.getElementById('chartOperationsEvolution');
    if (ctxEvolution) {
        new Chart(ctxEvolution, {
            type: 'line',
            data: {
                labels: @json($operationsSixMois['labels'] ?? []),
                datasets: [
                    {
                        label: 'Créées',
                        data: @json($operationsSixMois['creees'] ?? []),
                        borderColor: '#0D6EFD',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#0D6EFD',
                    },
                    {
                        label: 'Terminées',
                        data: @json($operationsSixMois['terminees'] ?? []),
                        borderColor: '#6C757D',
                        backgroundColor: 'rgba(108, 117, 125, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#6C757D',
                    },
                    {
                        label: 'Payées',
                        data: @json($operationsSixMois['payees'] ?? []),
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#198754',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 12 },
                            padding: 15,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        borderColor: '#ddd',
                        borderWidth: 1,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
}
</script>
<script src="{{ asset('js/dashboard-clickable.js') }}"></script>
@endpush
