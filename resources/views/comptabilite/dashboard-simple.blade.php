@extends('layouts.app')

@section('title', 'Tableau de Bord - Comptabilité - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calculator me-2 text-primary"></i>Tableau de Bord Comptabilité
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble de la comptabilité</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.factures.index') }}" class="btn btn-primary">
                <i class="fas fa-file-invoice-dollar me-2"></i>Gestion des Factures
            </a>
            <a href="{{ route('comptabilite.recettes.index') }}" class="btn btn-outline-success">
                <i class="fas fa-plus me-2"></i>Nouvelle Recette
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Factures</div>
                    <div class="h3 mb-0 text-primary">{{ number_format($stats['total_factures'], 0, ',', ' ') }}</div>
                    <small class="text-muted">Documents émis</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Factures Impayées</div>
                    <div class="h3 mb-0 text-danger">{{ number_format($stats['factures_impayees'], 0, ',', ' ') }}</div>
                    <small class="text-muted">En attente de paiement</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Recettes</div>
                    <div class="h3 mb-0 text-success">{{ number_format($stats['total_recettes'], 0, ',', ' ') }}</div>
                    <small class="text-muted">Opérations enregistrées</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Dépenses</div>
                    <div class="h3 mb-0 text-warning">{{ number_format($stats['total_depenses'], 0, ',', ' ') }}</div>
                    <small class="text-muted">Opérations enregistrées</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Solde Caisses</div>
                    <div class="h3 mb-0 text-info">{{ number_format($stats['solde_caisses'], 0, ',', ' ') }} FCFA</div>
                    <small class="text-muted">Disponibilités</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-secondary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Clients</div>
                    <div class="h3 mb-0 text-secondary">{{ number_format($stats['total_clients'], 0, ',', ' ') }}</div>
                    <small class="text-muted">Actifs enregistrés</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-dark border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Fournisseurs</div>
                    <div class="h3 mb-0 text-dark">{{ number_format($stats['total_fournisseurs'], 0, ',', ' ') }}</div>
                    <small class="text-muted">Partenaires enregistrés</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Actions Rapides</div>
                    <div class="d-flex gap-1 mt-2">
                        <a href="{{ route('comptabilite.factures.create') }}" class="btn btn-sm btn-success" title="Nouvelle Facture">
                            <i class="fas fa-plus"></i>
                        </a>
                        <a href="{{ route('comptabilite.recettes.create') }}" class="btn btn-sm btn-primary" title="Nouvelle Recette">
                            <i class="fas fa-arrow-down"></i>
                        </a>
                        <a href="{{ route('comptabilite.depenses.create') }}" class="btn btn-sm btn-warning" title="Nouvelle Dépense">
                            <i class="fas fa-arrow-up"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation rapide -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-th-large me-2"></i>Navigation Rapide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 text-center p-3 border-primary">
                                <i class="fas fa-file-invoice-dollar fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">Facturation</h6>
                                <p class="card-text small text-muted">Gérer les factures et devis</p>
                                <a href="{{ route('comptabilite.factures.index') }}" class="btn btn-primary btn-sm">Accéder</a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 text-center p-3 border-success">
                                <i class="fas fa-arrow-down fa-2x text-success mb-2"></i>
                                <h6 class="card-title">Recettes</h6>
                                <p class="card-text small text-muted">Enregistrer les revenus</p>
                                <a href="{{ route('comptabilite.recettes.index') }}" class="btn btn-success btn-sm">Accéder</a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 text-center p-3 border-warning">
                                <i class="fas fa-arrow-up fa-2x text-warning mb-2"></i>
                                <h6 class="card-title">Dépenses</h6>
                                <p class="card-text small text-muted">Suivre les dépenses</p>
                                <a href="{{ route('comptabilite.depenses.index') }}" class="btn btn-warning btn-sm">Accéder</a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 text-center p-3 border-info">
                                <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                                <h6 class="card-title">Rapports</h6>
                                <p class="card-text small text-muted">États financiers</p>
                                <a href="/comptabilite/rapports" class="btn btn-info btn-sm">Accéder</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
