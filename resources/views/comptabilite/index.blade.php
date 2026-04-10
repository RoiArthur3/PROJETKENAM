@extends('layouts.app')

@section('title', 'Comptabilité')

@section('content')
<style>
    .btn-success {
        color: #28a745 !important;
        background-color: white !important;
        border-color: #28a745 !important;
        transition: none !important;
    }
    .btn-success:hover,
    .btn-success:focus,
    .btn-success:active {
        color: #28a745 !important;
        background-color: white !important;
        border-color: #28a745 !important;
        box-shadow: none !important;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Comptabilité</h1>
                    <p class="text-muted">Gestion financière et conformité</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" onclick="window.location.href='{{ route('comptabilite.operations-to-ecritures') }}'">
                        <i class="fas fa-exchange-alt me-2"></i>Transformer opérations
                    </button>
                    <button class="btn btn-success" onclick="window.location.href='{{ route('comptabilite.import-param-compta.form') }}'">
                        <i class="fas fa-file-upload me-2"></i>Import param compta
                    </button>
                    <button class="btn btn-success" onclick="window.location.href='{{ route('comptabilite.etats-financiers') }}'">
                        <i class="fas fa-chart-line me-2"></i>États financiers
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['total_credits'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                            <p class="mb-0">Chiffre d'affaires</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-euro-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['total_depenses'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                            <p class="mb-0">Dépenses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['factures_impayees'] ?? 0, 0, ',', ' ') }}</h4>
                            <p class="mb-0">Factures en attente</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-invoice fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['total_ecritures'] ?? 0, 0, ',', ' ') }}</h4>
                            <p class="mb-0">Écritures ce mois</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-book fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice-dollar fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Facturation</h5>
                    <p class="card-text text-muted">Gérer les factures clients et fournisseurs</p>
                    <a href="{{ route('comptabilite.facturation') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Accéder
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Rapports financiers</h5>
                    <p class="card-text text-muted">Analyser les performances financières</p>
                    <a href="{{ route('comptabilite.rapports') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Accéder
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-coins fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Charges</h5>
                    <p class="card-text text-muted">Suivi des charges et dépenses</p>
                    <a href="{{ route('comptabilite.charges') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Accéder
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Factures</h5>
                    <p class="card-text text-muted">Gestion des factures clients</p>
                    <a href="{{ route('comptabilite.factures') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Accéder
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-secondary mb-3"></i>
                    <h5 class="card-title">Écritures comptables</h5>
                    <p class="card-text text-muted">Journal des écritures comptables</p>
                    <a href="{{ route('comptabilite.rapports.grand-journal') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Accéder
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-balance-scale fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">États financiers</h5>
                    <p class="card-text text-muted">Bilan, compte de résultat</p>
                    <a href="{{ route('comptabilite.etats-financiers') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Accéder
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-3x text-dark mb-3"></i>
                    <h5 class="card-title">Conformité fiscale</h5>
                    <p class="card-text text-muted">Déclarations et obligations fiscales</p>
                    <a href="{{ route('comptabilite.conformite') }}" class="btn btn-success">
                        <i class="fas fa-arrow-right me-2"></i>Accéder
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📌 Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Transformer les opérations en écritures comptables</h6>
                            <p class="text-muted">Convertir automatiquement les opérations validées en écritures comptables</p>
                            <button class="btn btn-success" onclick="window.location.href='{{ route('comptabilite.operations-to-ecritures') }}'">
                                <i class="fas fa-exchange-alt me-2"></i>Transformer opérations
                            </button>
                        </div>
                        <div class="col-md-6">
                            <h6>Classer dépenses vs charges</h6>
                            <p class="text-muted">Organiser et catégoriser les dépenses et charges</p>
                            <button class="btn btn-success" onclick="window.location.href='{{ route('comptabilite.classification.index') }}'">
                                <i class="fas fa-tags me-2"></i>Classer dépenses
                            </button>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h6>Produire les états financiers</h6>
                            <p class="text-muted">Générer bilan, compte de résultat et autres états</p>
                            <button class="btn btn-success" onclick="window.location.href='{{ route('comptabilite.etats-financiers') }}'">
                                <i class="fas fa-chart-line me-2"></i>Générer états
                            </button>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h6>Assurer la conformité fiscale</h6>
                            <p class="text-muted">Vérifier et générer les déclarations fiscales</p>
                            <button class="btn btn-success" onclick="window.location.href='{{ route('comptabilite.conformite') }}'">
                                <i class="fas fa-shield-alt me-2"></i>Vérifier conformité
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
