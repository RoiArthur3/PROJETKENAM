@extends('layouts.app')

@section('title', 'Dashboard Global | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">

    <!-- En-tête du Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-2"><i class="fas fa-home me-2"></i>Tableau de Bord Global KENAM</h2>
                    <p class="text-muted mb-0">Suivi consolidé de tous les modules - {{ now()->format('d/m/Y') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>Actualiser
                    </button>
                    <button class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-pdf me-1"></i>Exporter PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECTION RH ============ -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <h3 class="mb-0"><i class="fas fa-users text-primary me-2"></i>RESSOURCES HUMAINES</h3>
            <div class="ms-auto">
                <a href="{{ route('personnel.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-right me-1"></i>Voir tous
                </a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Personnel Total</p>
                                <h3 class="text-primary fw-bold mb-2">{{ $rhStats['total'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-arrow-up text-success me-1"></i>
                                    <span class="text-success">{{ $rhStats['actifs'] ?? 0 }}/{{ $rhStats['total'] ?? 0 }}</span> actifs
                                </small>
                            </div>
                            <div class="text-primary opacity-25">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Actifs (En Service)</p>
                                <h3 class="text-success fw-bold mb-2">{{ $rhStats['actifs'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-percentage text-success me-1"></i>
                                    @php
                                        $pct = $rhStats['total'] > 0 ? round($rhStats['actifs'] / $rhStats['total'] * 100) : 0;
                                    @endphp
                                    {{ $pct }}% du total
                                </small>
                            </div>
                            <div class="text-success opacity-25">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">En Période d'Essai</p>
                                <h3 class="text-warning fw-bold mb-2">{{ $rhStats['en_essai'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-hourglass-half text-warning me-1"></i>
                                    {{ $rhStats['fin_essai_expiring'] ?? 0 }} expirant
                                </small>
                            </div>
                            <div class="text-warning opacity-25">
                                <i class="fas fa-hourglass-half fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Salaire Moyen</p>
                                <h3 class="text-info fw-bold mb-2">{{ number_format($rhStats['salaire_moyen'] ?? 0, 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-money-bill text-info me-1"></i> FCFA/mois
                                </small>
                            </div>
                            <div class="text-info opacity-25">
                                <i class="fas fa-wallet fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECTION FOURNISSEURS ============ -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <h3 class="mb-0"><i class="fas fa-truck text-success me-2"></i>FOURNISSEURS & ACHATS</h3>
            <div class="ms-auto">
                <a href="/fournisseurs" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-arrow-right me-1"></i>Voir tous
                </a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Fournisseurs Total</p>
                                <h3 class="text-success fw-bold mb-2">{{ $fournisseursStats['total'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    {{ $fournisseursStats['actifs'] ?? 0 }} actifs
                                </small>
                            </div>
                            <div class="text-success opacity-25">
                                <i class="fas fa-handshake fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Contrats Actifs</p>
                                <h3 class="text-info fw-bold mb-2">{{ $fournisseursStats['contrats_actifs'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-file-contract text-info me-1"></i>En cours
                                </small>
                            </div>
                            <div class="text-info opacity-25">
                                <i class="fas fa-file-contract fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Commandes Livrées</p>
                                <h3 class="text-primary fw-bold mb-2">{{ $fournisseursStats['commandes_livrees'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-check text-primary me-1"></i>Ce mois
                                </small>
                            </div>
                            <div class="text-primary opacity-25">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Fournisseurs Actifs %</p>
                                <h3 class="text-warning fw-bold mb-2">
                                    @php
                                        $fPct = $fournisseursStats['total'] > 0 ? round($fournisseursStats['actifs'] / $fournisseursStats['total'] * 100) : 0;
                                    @endphp
                                    {{ $fPct }}%
                                </h3>
                                <small class="text-muted">
                                    <i class="fas fa-percentage text-warning me-1"></i>
                                </small>
                            </div>
                            <div class="text-warning opacity-25">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECTION OPÉRATIONS ============ -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <h3 class="mb-0"><i class="fas fa-exchange-alt text-info me-2"></i>OPÉRATIONS & FINANCES</h3>
            <div class="ms-auto">
                <a href="/operations" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-arrow-right me-1"></i>Voir tous
                </a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Total Opérations</p>
                                <h3 class="text-info fw-bold mb-2">{{ $operationsStats['total'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-list text-info me-1"></i>Tous types
                                </small>
                            </div>
                            <div class="text-info opacity-25">
                                <i class="fas fa-exchange-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Encaissées</p>
                                <h3 class="text-success fw-bold mb-2">{{ $operationsStats['encaissees'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-check-circle text-success me-1"></i>Complétées
                                </small>
                            </div>
                            <div class="text-success opacity-25">
                                <i class="fas fa-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Validées</p>
                                <h3 class="text-warning fw-bold mb-2">{{ $operationsStats['validees'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-hourglass-half text-warning me-1"></i>En traitement
                                </small>
                            </div>
                            <div class="text-warning opacity-25">
                                <i class="fas fa-tasks fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">CA Total</p>
                                <h3 class="text-primary fw-bold mb-2">{{ number_format($operationsStats['ca_total'] ?? 0, 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-coins text-primary me-1"></i> FCFA
                                </small>
                            </div>
                            <div class="text-primary opacity-25">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECTION CLIENTS ============ -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <h3 class="mb-0"><i class="fas fa-address-book text-primary me-2"></i>CLIENTS</h3>
            <div class="ms-auto">
                <a href="/clients" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-right me-1"></i>Voir tous
                </a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Clients Total</p>
                                <h3 class="text-primary fw-bold mb-2">{{ $clientsStats['total'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-users text-primary me-1"></i>Base clients
                                </small>
                            </div>
                            <div class="text-primary opacity-25">
                                <i class="fas fa-address-book fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Clients Actifs</p>
                                <h3 class="text-success fw-bold mb-2">{{ $clientsStats['actifs'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    @php
                                        $cPct = $clientsStats['total'] > 0 ? round($clientsStats['actifs'] / $clientsStats['total'] * 100) : 0;
                                    @endphp
                                    {{ $cPct }}%
                                </small>
                            </div>
                            <div class="text-success opacity-25">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">En Retard</p>
                                <h3 class="text-danger fw-bold mb-2">{{ $clientsStats['avec_retard'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-exclamation-circle text-danger me-1"></i>À suivre
                                </small>
                            </div>
                            <div class="text-danger opacity-25">
                                <i class="fas fa-search fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Taux Satisfaction</p>
                                <h3 class="text-info fw-bold mb-2">95%</h3>
                                <small class="text-muted">
                                    <i class="fas fa-star text-info me-1"></i>Très bon
                                </small>
                            </div>
                            <div class="text-info opacity-25">
                                <i class="fas fa-smile fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECTION COMPTABILITÉ ============ -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <h3 class="mb-0"><i class="fas fa-file-invoice-dollar text-success me-2"></i>COMPTABILITÉ & FACTURATIONS</h3>
            <div class="ms-auto">
                <a href="/comptabilite" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-arrow-right me-1"></i>Voir tous
                </a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Total Factures</p>
                                <h3 class="text-success fw-bold mb-2">{{ $comptabiliteStats['factures_total'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-file text-success me-1"></i>Tous types
                                </small>
                            </div>
                            <div class="text-success opacity-25">
                                <i class="fas fa-file-invoice-dollar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Payées</p>
                                <h3 class="text-info fw-bold mb-2">{{ $comptabiliteStats['factures_payees'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-check-circle text-info me-1"></i>Réglées
                                </small>
                            </div>
                            <div class="text-info opacity-25">
                                <i class="fas fa-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Impayées</p>
                                <h3 class="text-danger fw-bold mb-2">{{ $comptabiliteStats['factures_impayees'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-hourglass-half text-danger me-1"></i>À suivre
                                </small>
                            </div>
                            <div class="text-danger opacity-25">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Montant Encaissé</p>
                                <h3 class="text-primary fw-bold mb-2">{{ number_format($comptabiliteStats['montant_encaisse'] ?? 0, 0) }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-coins text-primary me-1"></i> FCFA
                                </small>
                            </div>
                            <div class="text-primary opacity-25">
                                <i class="fas fa-money-bill fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ SECTION VÉHICULES ============ -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <h3 class="mb-0"><i class="fas fa-shuttle-van text-warning me-2"></i>VÉHICULES</h3>
            <div class="ms-auto">
                <a href="/vehicules" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-arrow-right me-1"></i>Voir tous
                </a>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Véhicules Total</p>
                                <h3 class="text-warning fw-bold mb-2">{{ $vehiculesStats['total'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-car text-warning me-1"></i>Parc auto
                                </small>
                            </div>
                            <div class="text-warning opacity-25">
                                <i class="fas fa-shuttle-van fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Opérationnels</p>
                                <h3 class="text-success fw-bold mb-2">{{ $vehiculesStats['operationnels'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    @php
                                        $vPct = $vehiculesStats['total'] > 0 ? round($vehiculesStats['operationnels'] / $vehiculesStats['total'] * 100) : 0;
                                    @endphp
                                    {{ $vPct }}%
                                </small>
                            </div>
                            <div class="text-success opacity-25">
                                <i class="fas fa-thumbs-up fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-danger h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">En Maintenance</p>
                                <h3 class="text-danger fw-bold mb-2">{{ $vehiculesStats['en_maintenance'] ?? 0 }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-tools text-danger me-1"></i>Atelier
                                </small>
                            </div>
                            <div class="text-danger opacity-25">
                                <i class="fas fa-tools fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-start border-5 border-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Disponibilité</p>
                                <h3 class="text-info fw-bold mb-2">
                                    @php
                                        $dispo = $vehiculesStats['total'] > 0 ? round($vehiculesStats['operationnels'] / $vehiculesStats['total'] * 100) : 0;
                                    @endphp
                                    {{ $dispo }}%
                                </h3>
                                <small class="text-muted">
                                    <i class="fas fa-percentage text-info me-1"></i>Parc opérant
                                </small>
                            </div>
                            <div class="text-info opacity-25">
                                <i class="fas fa-tachometer-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
