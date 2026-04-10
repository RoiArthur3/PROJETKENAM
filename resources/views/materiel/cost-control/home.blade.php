@extends('layouts.app')

@section('title', 'Cost Control - Gestion des Pointages | KENAM SERVICES')

@section('content')
<div class="container-fluid">

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- EN-TÊTE PRINCIPAL --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-meter me-2 text-primary"></i>Cost Control
            </h1>
            <p class="text-muted mb-0">Gestion intégrée des pointages et des marges de rentabilité</p>
        </div>
        <div class="col-lg-4 text-end">
            <span class="badge bg-light text-dark fs-6">
                <i class="fas fa-calendar-today me-1"></i>{{ now()->format('d/m/Y') }}
            </span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- DESCRIPTION DU MODULE --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="card border-start border-info border-4 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="fas fa-info-circle fa-2x text-info"></i>
                </div>
                <div class="col">
                    <h5 class="card-title mb-1">Bienvenue dans le module Cost Control</h5>
                    <p class="card-text text-muted mb-0">
                        Suivez les pointages de vos équipes, calculez les marges en temps réel, et facturez vos clients 
                        en fonction de leurs modèles tarifaires. Trois modes de pointage disponibles pour répondre à vos besoins.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODULES PRINCIPAUX --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="row mb-4">
        {{-- MODULE 1: ENGIN STANDARD --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-start border-info border-4 h-100">
                <div class="card-header bg-info bg-gradient text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Engin Standard
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Gestion des pointages horaires pour les engins standards (location, heures de travail, coûts).
                    </p>
                    <ul class="small mb-3 flex-grow-1">
                        <li><strong>Pointages:</strong> Par heure ou par jour</li>
                        <li><strong>Facturation:</strong> Basée sur les heures/jours travaillés</li>
                        <li><strong>Suivi:</strong> Marge brute par engin et par période</li>
                    </ul>
                    <a href="{{ route('materiel.cost-control.engin.list') }}" class="btn btn-info align-self-start">
                        <i class="fas fa-arrow-right me-1"></i>Accéder au module
                    </a>
                </div>
            </div>
        </div>

        {{-- MODULE 2: CAMION PLATEAU --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-start border-warning border-4 h-100">
                <div class="card-header bg-warning bg-gradient text-dark py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>Camion Plateau
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Suivi complet des camions plateau : voyages, facturation, suivi géographique et chrono.
                    </p>
                    <ul class="small mb-3 flex-grow-1">
                        <li><strong>Pointages:</strong> Par voyage, au mois ou en temps réel (chrono)</li>
                        <li><strong>Facturation:</strong> Flexible selon le modèle de tarification</li>
                        <li><strong>Suivi:</strong> GPS, trajets, marges de rentabilité</li>
                    </ul>
                    <a href="{{ route('materiel.cost-control.plateau.list') }}" class="btn btn-warning align-self-start">
                        <i class="fas fa-arrow-right me-1"></i>Accéder au module
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ACTION RAPIDES --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="text-uppercase text-muted mb-3">
                <i class="fas fa-lightning-bolt me-1"></i>Actions rapides
            </h6>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('materiel.cost-control.plateau.chrono.start-form') }}" class="card shadow-sm border-0 text-decoration-none text-dark hover-effect">
                <div class="card-body text-center py-4">
                    <i class="fas fa-play-circle fa-2x text-success mb-2"></i>
                    <h6 class="small mb-0">Pointage Chrono</h6>
                    <small class="text-muted">Clic début / Clic fin</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('materiel.cost-control.plateau.chrono.to-invoice') }}" class="card shadow-sm border-0 text-decoration-none text-dark hover-effect">
                <div class="card-body text-center py-4">
                    <i class="fas fa-file-invoice-dollar fa-2x text-warning mb-2"></i>
                    <h6 class="small mb-0">À Facturer</h6>
                    <small class="text-muted">Pointages prêts</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('materiel.cost-control.plateau.parametrage.index') }}" class="card shadow-sm border-0 text-decoration-none text-dark hover-effect">
                <div class="card-body text-center py-4">
                    <i class="fas fa-sliders-h fa-2x text-primary mb-2"></i>
                    <h6 class="small mb-0">Paramètres</h6>
                    <small class="text-muted">Coûts & tarifs</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('materiel.cost-control.plateau.projets-termines') }}" class="card shadow-sm border-0 text-decoration-none text-dark hover-effect">
                <div class="card-body text-center py-4">
                    <i class="fas fa-chart-pie fa-2x text-success mb-2"></i>
                    <h6 class="small mb-0">Marges</h6>
                    <small class="text-muted">Projets terminés</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('materiel.cost-control.engin.rapport') }}" class="card shadow-sm border-0 text-decoration-none text-dark hover-effect">
                <div class="card-body text-center py-4">
                    <i class="fas fa-file-signature fa-2x text-info mb-2"></i>
                    <h6 class="small mb-0">Rapport Cost Controle</h6>
                    <small class="text-muted">Par engin et mission</small>
                </div>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- TABLEAU RÉCAPITULATIF --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="text-uppercase text-muted mb-3">
                <i class="fas fa-layer-group me-1"></i>Vue comparée des modules
            </h6>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Caractéristique</th>
                            <th class="text-center">Engin Standard</th>
                            <th class="text-center">Camion Plateau</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Unité de base</strong></td>
                            <td class="text-center"><span class="badge bg-info">Heures / Jours</span></td>
                            <td class="text-center"><span class="badge bg-warning">Voyages</span></td>
                        </tr>
                        <tr>
                            <td><strong>Facturation</strong></td>
                            <td class="text-center">Simple (h/j)</td>
                            <td class="text-center">Flexible (voyage/mois)</td>
                        </tr>
                        <tr>
                            <td><strong>Pointage Chrono</strong></td>
                            <td class="text-center">
                                <i class="fas fa-times text-muted"></i>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-check text-success"></i>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Suivi GPS</strong></td>
                            <td class="text-center">
                                <i class="fas fa-times text-muted"></i>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-check text-success"></i>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Rapports</strong></td>
                            <td class="text-center"><span class="badge bg-light text-dark">Marges</span></td>
                            <td class="text-center"><span class="badge bg-light text-dark">Marges + Trajets</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- AIDE & DOCUMENTATION --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-header bg-success bg-gradient text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-book me-2"></i>Guide d'utilisation
                    </h6>
                </div>
                <div class="card-body small">
                    <ol class="mb-0">
                        <li class="mb-2">Choisir le module adapté (Engin ou Camion Plateau)</li>
                        <li class="mb-2">Paramétrer les coûts et tarifs clients</li>
                        <li class="mb-2">Enregistrer les pointages (manuel ou chrono)</li>
                        <li class="mb-2">Vérifier les marges en temps réel</li>
                        <li class="mb-2">Facturer les clients</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-header bg-danger bg-gradient text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Conseils d'optimisation
                    </h6>
                </div>
                <div class="card-body small">
                    <ul class="mb-0">
                        <li class="mb-2">✓ Utilisez le pointage chrono pour plus de précision</li>
                        <li class="mb-2">✓ Vérifiez les marges tous les 15 jours</li>
                        <li class="mb-2">✓ Paramétrez correctement les coûts de base</li>
                        <li class="mb-2">✓ Facturez rapidement après terminer une mission</li>
                        <li class="mb-2">✓ Consultez les rapports pour identifier les inefficacités</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .hover-effect {
        transition: all 0.3s ease;
    }
    
    .hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
