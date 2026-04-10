@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>Tableau de Bord
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble de l'activité KENAM Services</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary text-white px-3 py-2">{{ now()->translatedFormat('d/m/Y H:i') }}</span>
            <button class="btn btn-sm btn-outline-primary ms-2" onclick="location.reload()"><i class="fas fa-sync-alt"></i></button>
        </div>
    </div>

    <!-- Section Financière (Trésorerie & Comptabilité) -->
    <div class="row mb-4">
        <!-- Trésorerie Totale -->
        <div class="col-xl-6 col-md-12 mb-3">
            <a href="{{ route('tresorerie.dashboard') }}" class="text-decoration-none">
                <div class="card border-start border-info border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Trésorerie Globale</div>
                                <div class="h4 mb-1 fw-bold text-dark">
                                    {{ number_format(($tresorerie['total'] ?? 0) / 1000000, 2) }}M FCFA
                                </div>
                                <div class="small text-muted">
                                    <span class="me-3"><i class="fas fa-cash-register me-1"></i>Caisses: {{ number_format(($tresorerie['solde_caisses'] ?? 0) / 1000000, 2) }}M</span>
                                    <span><i class="fas fa-university me-1"></i>Banques: {{ number_format(($tresorerie['solde_banques'] ?? 0) / 1000000, 2) }}M</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    <span class="me-3 text-success"><i class="fas fa-arrow-circle-down me-1"></i>Appros: {{ number_format(($tresorerie['approvisionnements_mois'] ?? 0) / 1000000, 2) }}M</span>
                                    <span class="text-danger"><i class="fas fa-arrow-circle-up me-1"></i>Dépenses: {{ number_format(($tresorerie['depenses_mois'] ?? 0) / 1000000, 2) }}M</span>
                                </div>
                                <div class="small mt-1">
                                    <span class="me-3 text-muted"><i class="fas fa-store-alt me-1"></i>{{ $tresorerie['nombre_caisses'] ?? 0 }} caisses</span>
                                    <span class="fw-semibold {{ ($tresorerie['variation_mois'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fas fa-chart-line me-1"></i>Variation: {{ number_format(($tresorerie['variation_mois'] ?? 0) / 1000000, 2) }}M
                                    </span>
                                    @if(($tresorerie['approvisionnements_en_attente'] ?? 0) > 0)
                                        <span class="badge bg-warning text-dark ms-2">{{ $tresorerie['approvisionnements_en_attente'] }} en attente</span>
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-wallet fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Comptabilité -->
        <div class="col-xl-6 col-md-12 mb-3">
            <a href="{{ route('comptabilite.dashboard') }}" class="text-decoration-none">
                <div class="card border-start border-dark border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small text-uppercase fw-bold">Comptabilité ({{ now()->translatedFormat('F Y') }})</div>
                                <div class="h4 mb-1 fw-bold {{ (($comptabilite['revenus_mois'] ?? 0) - ($comptabilite['depenses_mois'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format((($comptabilite['revenus_mois'] ?? 0) - ($comptabilite['depenses_mois'] ?? 0)) / 1000000, 2) }}M FCFA
                                </div>
                                <div class="small text-muted">
                                    <span class="me-3 text-success"><i class="fas fa-arrow-up me-1"></i>Revenus: {{ number_format(($comptabilite['revenus_mois'] ?? 0) / 1000000, 2) }}M</span>
                                    <span class="text-danger"><i class="fas fa-arrow-down me-1"></i>Dépenses: {{ number_format(($comptabilite['depenses_mois'] ?? 0) / 1000000, 2) }}M</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    <span class="me-3"><i class="fas fa-book me-1"></i>{{ $comptabilite['ecritures_mois'] ?? 0 }} écritures ce mois</span>
                                    @if(($comptabilite['factures_impayees'] ?? 0) > 0)
                                        <span class="badge bg-danger">{{ $comptabilite['factures_impayees'] }} factures impayées</span>
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-calculator fa-2x text-dark opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- KPIs Principaux -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('tresorerie.encaissements') }}" class="text-decoration-none">
                <div class="card border-start border-primary border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">CA du Jour</div>
                        <div class="h5 mb-0 fw-bold text-dark">{{ number_format($stats['ca']['jour'] ?? 0, 0, ',', ' ') }} FCFA</div>
                        <small class="text-muted">Mois: {{ number_format($stats['ca']['mois'] ?? 0, 0, ',', ' ') }} FCFA</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('materiel.missions.index') }}" class="text-decoration-none">
                <div class="card border-start border-success border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Activité Missions</div>
                        <div class="h5 mb-0 fw-bold text-dark">{{ $stats['missions']['actives'] ?? 0 }} en cours</div>
                        <div class="small mt-1">
                            <span class="text-success fw-bold" title="Chiffre d'affaires missions ce mois">
                                {{ number_format(($stats['missions']['montant_mois'] ?? 0), 0, ',', ' ') }} <small>FCFA</small>
                            </span>
                        </div>
                        <div class="small">
                            <span class="text-muted">Marge: </span>
                            <span class="text-info fw-bold">{{ number_format(($stats['missions']['marge_mois'] ?? 0), 0, ',', ' ') }} <small>FCFA</small></span>
                        </div>
                        <small class="text-muted">Mois: {{ $stats['missions']['mois'] ?? 0 }} missions</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('fleet.dashboard') }}" class="text-decoration-none">
                <div class="card border-start border-info border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Engins Disponibles</div>
                        <div class="h5 mb-0 fw-bold text-dark">{{ $stats['parc']['dispo'] ?? 0 }} / {{ $stats['parc']['total'] ?? 0 }}</div>
                        <small class="text-muted">Taux utilisation: {{ $stats['parc']['taux_utilisation'] ?? 0 }}%</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('comptabilite.factures.index') }}" class="text-decoration-none">
                <div class="card border-start border-warning border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Impayés / À encaisser</div>
                        <div class="h5 mb-0 fw-bold {{ ($stats['factures_impayees']['count'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($stats['factures_impayees']['total'] ?? 0, 0, ',', ' ') }} FCFA</div>
                        <small class="text-muted">{{ $stats['factures_impayees']['count'] ?? 0 }} documents en attente</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Ligne KPIs : Flux Financier & Opérationnel -->
    <div class="row mb-4">
        <!-- 1. Engagements en cours (En attente de validation) -->
        <div class="col-xl col-md-6 mb-3">
            <a href="{{ route('validations.pending') }}" class="text-decoration-none">
                <div class="card border-start border-info border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Engagements en cours</div>
                        <div class="h5 mb-0 fw-bold text-info">
                            {{ number_format($stats['operations']['engagements_en_cours'] ?? 0, 0, ',', ' ') }} <small>FCFA</small>
                        </div>
                        <small class="text-muted">En attente de validation</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. À Payer (Approuvé) -->
        <div class="col-xl col-md-6 mb-3">
            <a href="{{ route('validations.to-pay') }}" class="text-decoration-none">
                <div class="card border-start border-warning border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">À Payer</div>
                        <div class="h5 mb-0 fw-bold text-warning">
                            {{ number_format($stats['operations']['a_payer_total'] ?? 0, 0, ',', ' ') }} <small>FCFA</small>
                        </div>
                        <small class="text-muted">Approuvé / En attente</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Payé (Mois) -->
        <div class="col-xl col-md-6 mb-3">
            <a href="{{ route('validations.paid') }}" class="text-decoration-none">
                <div class="card border-start border-success border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Payé (Mois)</div>
                        <div class="h5 mb-0 fw-bold text-success">
                            {{ number_format($stats['operations']['paye_mois_total'] ?? 0, 0, ',', ' ') }} <small>FCFA</small>
                        </div>
                        <small class="text-muted">Règlements effectués</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Volume Opérations -->
        <div class="col-xl col-md-6 mb-3">
            <a href="{{ route('operations.dashboard') }}" class="text-decoration-none">
                <div class="card border-start border-secondary border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Opérations</div>
                        <div class="h5 mb-0 fw-bold text-dark">{{ $stats['operations']['mois'] ?? 0 }}</div>
                        <small class="text-muted">ce mois-ci</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- 5. Clients & Fournisseurs (Regroupés pour lisibilité horizontale) -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100 card-hover">
                <div class="card-body px-3">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Partenaires</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pe-2">
                            <span class="h6 mb-0 fw-bold text-dark">{{ $stats['clients']['total'] ?? 0 }}</span>
                            <small class="text-muted d-block">Clients</small>
                        </div>
                        <div class="border-start ps-3">
                            <span class="h6 mb-0 fw-bold text-dark">{{ $stats['fournisseurs']['total'] ?? 0 }}</span>
                            <small class="text-muted d-block">Fourn.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Cumul annuel -->
        <div class="col-xl col-md-6 mb-3">
            <a href="{{ route('comptabilite.factures.index') }}" class="text-decoration-none">
                <div class="card border-start border-dark border-4 shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Cumul CA {{ now()->year }}</div>
                        <div class="h5 mb-0 fw-bold text-dark">{{ number_format(($stats['ca']['annee'] ?? 0) / 1000000, 1) }}M <small>FCFA</small></div>
                        <small class="text-muted">Total facturé</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Magasin / Stock -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('magasin.dashboard') }}" class="text-decoration-none">
                <div class="card shadow-sm card-hover">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary"><i class="fas fa-boxes me-2"></i>Stock & Magasin</h6>
                        <span class="badge bg-primary">Voir détail <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-2 col-4 mb-2">
                                <div class="h4 mb-0 fw-bold text-primary">{{ $magasin['total_produits'] ?? 0 }}</div>
                                <small class="text-muted">Produits</small>
                            </div>
                            <div class="col-md-2 col-4 mb-2">
                                <div class="h4 mb-0 fw-bold {{ ($magasin['alertes_stock'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">{{ $magasin['alertes_stock'] ?? 0 }}</div>
                                <small class="text-muted">Alertes stock</small>
                            </div>
                            <div class="col-md-2 col-4 mb-2">
                                <div class="h4 mb-0 fw-bold text-dark">{{ number_format(($magasin['valeur_stock'] ?? 0) / 1000000, 2) }}M</div>
                                <small class="text-muted">Valeur stock</small>
                            </div>
                            <div class="col-md-2 col-4 mb-2">
                                <div class="h4 mb-0 fw-bold text-success">{{ $magasin['entrees_mois'] ?? 0 }}</div>
                                <small class="text-muted">Entrées mois</small>
                            </div>
                            <div class="col-md-2 col-4 mb-2">
                                <div class="h4 mb-0 fw-bold text-warning">{{ $magasin['sorties_mois'] ?? 0 }}</div>
                                <small class="text-muted">Sorties mois</small>
                            </div>
                            <div class="col-md-2 col-4 mb-2">
                                <div class="h4 mb-0 fw-bold text-info">{{ $magasin['categories'] ?? 0 }}</div>
                                <small class="text-muted">Catégories</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Section Graphiques -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-line me-2"></i>Chiffre d'Affaires Mensuel</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="caChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-truck me-2"></i>Utilisation des Engins</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="utilisationChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-3"><i class="fas fa-circle text-primary"></i> Disponibles</span>
                        <span class="me-3"><i class="fas fa-circle text-success"></i> En mission</span>
                        <span><i class="fas fa-circle text-danger"></i> Maintenance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes Critiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Alertes Critiques</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('comptabilite.factures.index') }}" class="text-decoration-none">
                                <div class="alert alert-danger d-flex align-items-center mb-0 card-hover" role="alert">
                                    <i class="fas fa-file-invoice-dollar fa-lg me-3"></i>
                                    <div>
                                        <div class="h5 mb-0 fw-bold">{{ $alerts['factures_echues'] ?? 0 }}</div>
                                        <div class="small">Factures échues</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('operations.index') }}" class="text-decoration-none">
                                <div class="alert alert-warning d-flex align-items-center mb-0 card-hover" role="alert">
                                    <i class="fas fa-clock fa-lg me-3"></i>
                                    <div>
                                        <div class="h5 mb-0 fw-bold">{{ $alerts['retards_retour'] ?? 0 }}</div>
                                        <div class="small">Opérations en retard</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('magasin.inventaire') }}" class="text-decoration-none">
                                <div class="alert alert-info d-flex align-items-center mb-0 card-hover" role="alert">
                                    <i class="fas fa-box fa-lg me-3"></i>
                                    <div>
                                        <div class="h5 mb-0 fw-bold">{{ $alerts['stock_faible'] ?? 0 }}</div>
                                        <div class="small">Stock faible</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('fleet.dashboard') }}" class="text-decoration-none">
                                <div class="alert alert-secondary d-flex align-items-center mb-0 card-hover" role="alert">
                                    <i class="fas fa-shield-alt fa-lg me-3"></i>
                                    <div>
                                        <div class="h5 mb-0 fw-bold">{{ $alerts['assurances_expirees'] ?? 0 }}</div>
                                        <div class="small">Assurances expirées</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité Récente -->
    <div class="row mb-4">
        <!-- Dernières factures -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-file-invoice-dollar me-2"></i>Dernières Factures</h6>
                    <a href="{{ route('comptabilite.factures.index') }}" class="btn btn-sm btn-outline-primary">Tout voir</a>
                </div>
                <div class="card-body">
                    @if(isset($recentData['factures']) && $recentData['factures']->count() > 0)
                        @foreach($recentData['factures'] as $facture)
                        <a href="{{ route('comptabilite.factures.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center mb-3 p-2 rounded card-hover">
                                <div class="flex-grow-1">
                                    <div class="small text-muted">{{ $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') : '-' }}</div>
                                    <div class="fw-bold text-dark">{{ $facture->client->raison_sociale ?? ($facture->numero ?? 'N/A') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-dark">{{ number_format($facture->montant_ttc ?? 0, 0, ',', ' ') }}</div>
                                    <span class="badge {{ in_array($facture->statut, ['payee', 'payée']) ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($facture->statut) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-file-invoice fa-2x text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">Aucune facture récente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Opérations en cours -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-success"><i class="fas fa-cogs me-2"></i>Opérations en Cours</h6>
                    <a href="{{ route('operations.index') }}" class="btn btn-sm btn-outline-success">Tout voir</a>
                </div>
                <div class="card-body">
                    @if(isset($recentData['missions']) && count($recentData['missions']) > 0)
                        @foreach($recentData['missions'] as $operation)
                        <a href="{{ route('operations.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center mb-3 p-2 rounded card-hover">
                                <div class="flex-grow-1">
                                    <div class="small text-muted">{{ $operation->date_operation ? \Carbon\Carbon::parse($operation->date_operation)->format('d/m/Y') : '-' }}</div>
                                    <div class="fw-bold text-dark">{{ Str::limit($operation->titre ?? '', 30) }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted">{{ $operation->demandeur_name ?? '' }}</div>
                                    <span class="badge bg-{{ $operation->statut_courant == 'en_cours' ? 'info' : ($operation->statut_courant == 'pending_validation' ? 'warning' : 'secondary') }}">
                                        {{ $operation->statut_courant == 'pending_validation' ? 'En attente' : ucfirst(str_replace('_', ' ', $operation->statut_courant ?? '')) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-cogs fa-2x text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">Aucune opération en cours</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Commandes en attente -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-warning"><i class="fas fa-shopping-cart me-2"></i>Commandes en Attente</h6>
                    <a href="{{ route('fournisseurs.commandes.index') }}" class="btn btn-sm btn-outline-warning">Tout voir</a>
                </div>
                <div class="card-body">
                    @if(isset($recentData['commandes']) && $recentData['commandes']->count() > 0)
                        @foreach($recentData['commandes'] as $commande)
                        <a href="{{ route('fournisseurs.commandes.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center mb-3 p-2 rounded card-hover">
                                <div class="flex-grow-1">
                                    <div class="small text-muted">{{ $commande->date_commande ? \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') : '-' }}</div>
                                    <div class="fw-bold text-dark">{{ $commande->fournisseur->raison_sociale ?? ($commande->reference ?? 'N/A') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-dark">{{ number_format($commande->montant_ttc ?? 0, 0, ',', ' ') }}</div>
                                    <span class="badge bg-warning text-dark">{{ ucfirst($commande->statut ?? '') }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-shopping-cart fa-2x text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">Aucune commande en attente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Accès Rapide aux Modules -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-th-large me-2"></i>Accès Rapide aux Modules</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('commercial.dashboard') }}" class="btn btn-outline-primary w-100 py-3 text-center card-hover">
                                <i class="fas fa-handshake fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Commercial</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-info w-100 py-3 text-center card-hover">
                                <i class="fas fa-wallet fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Trésorerie</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-dark w-100 py-3 text-center card-hover">
                                <i class="fas fa-calculator fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Comptabilité</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('operations.dashboard') }}" class="btn btn-outline-success w-100 py-3 text-center card-hover">
                                <i class="fas fa-cogs fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Opérations</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('fleet.dashboard') }}" class="btn btn-outline-warning w-100 py-3 text-center card-hover">
                                <i class="fas fa-truck fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Parc / Flotte</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('magasin.dashboard') }}" class="btn btn-outline-secondary w-100 py-3 text-center card-hover">
                                <i class="fas fa-warehouse fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Magasin</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('fournisseurs.dashboard') }}" class="btn btn-outline-danger w-100 py-3 text-center card-hover">
                                <i class="fas fa-truck-loading fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Fournisseurs</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('rh.dashboard') }}" class="btn btn-outline-primary w-100 py-3 text-center card-hover">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                <small class="fw-bold">RH</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('entrepots.dashboard') }}" class="btn btn-outline-info w-100 py-3 text-center card-hover">
                                <i class="fas fa-boxes fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Entrepôts</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-dark w-100 py-3 text-center card-hover">
                                <i class="fas fa-search fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Audit</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('analyses.dashboard') }}" class="btn btn-outline-success w-100 py-3 text-center card-hover">
                                <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Analyses</small>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-4 col-6">
                            <a href="{{ route('materiel.dashboard') }}" class="btn btn-outline-warning w-100 py-3 text-center card-hover">
                                <i class="fas fa-tools fa-2x d-block mb-2"></i>
                                <small class="fw-bold">Matériel</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-hover { transition: all 0.2s ease; }
.card-hover:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important; }
.alert.card-hover:hover { filter: brightness(0.95); }
.border-left-primary { border-left: .25rem solid #4e73df !important; }
.border-left-success { border-left: .25rem solid #1cc88a !important; }
.border-left-info { border-left: .25rem solid #36b9cc !important; }
.border-left-warning { border-left: .25rem solid #f6c23e !important; }
.border-left-danger { border-left: .25rem solid #e74a3b !important; }
.border-left-secondary { border-left: .25rem solid #858796 !important; }
.border-left-dark { border-left: .25rem solid #5a5c69 !important; }
</style>

<!-- JavaScript pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique CA Mensuel
    const caCtx = document.getElementById('caChart');
    if (caCtx) {
        new Chart(caCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($charts['ca_labels'] ?? []) !!},
                datasets: [{
                    label: 'Chiffre d\'Affaires (FCFA)',
                    data: {!! json_encode($charts['ca_data'] ?? []) !!},
                    borderColor: 'rgb(78, 115, 223)',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(78, 115, 223)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Utilisation Engins
    const utilCtx = document.getElementById('utilisationChart');
    if (utilCtx) {
        new Chart(utilCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Disponibles', 'En mission', 'Maintenance'],
                datasets: [{
                    data: {!! json_encode($charts['util_data'] ?? [0, 0, 0]) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#e74a3b'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
});
</script>
@endsection
