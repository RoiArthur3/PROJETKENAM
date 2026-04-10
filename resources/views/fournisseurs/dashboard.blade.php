@extends('layouts.app')

@section('title', 'Dashboard Fournisseurs | KENAM SERVICES')

@section('content')
<x-dashboard-layout title="Dashboard Fournisseurs & Achats" icon="fa-truck" subtitle="Suivi des performances et gestion stratégique des relations fournisseurs">

    <!-- KPIs améliorés -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Fournisseurs Actifs"
            value="{{ $stats['actifs'] ?? 0 }}"
            icon="fa-handshake"
            color="primary"
            subtitle="Partenaires validés"
            trend="up"
            trendValue="+3 ce trimestre"
        />

        <x-kpi-card
            title="CA Achats 2025"
            value="892M FCFA"
            icon="fa-chart-line"
            color="success"
            subtitle="+8% vs objectif"
            trend="up"
            trendValue="+12% vs 2024"
        />

        <x-kpi-card
            title="Note Moyenne"
            value="4.1/5"
            icon="fa-star"
            color="warning"
            subtitle="Performance globale"
        />

        <x-kpi-card
            title="Délais Moyens"
            value="7.2 jours"
            icon="fa-clock"
            color="info"
            subtitle="-15% vs année dernière"
            trend="down"
            trendValue="Amélioration"
        />
    </x-slot>

    <!-- Actions rapides -->
    <x-slot name="headerActions">
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="rafraichirDashboard()">
                <i class="fas fa-sync-alt me-1"></i>Actualiser
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="exporterRapport()">
                <i class="fas fa-file-excel me-1"></i>Exporter
            </button>
            <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#nouveauFournisseurModal">
                <i class="fas fa-plus me-1"></i>Nouveau Fournisseur
            </button>
        </div>
    </x-slot>

    <!-- Section Filtres et Contrôles -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 text-primary fw-bold">
                <i class="fas fa-filter me-2"></i>Contrôles et Filtres
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Période</label>
                    <select class="form-select form-select-sm" id="periodeSelect">
                        <option value="month" selected>Ce mois</option>
                        <option value="quarter">Ce trimestre</option>
                        <option value="semester">Ce semestre</option>
                        <option value="year">Cette année</option>
                        <option value="custom">Personnalisé</option>
                    </select>
                </div>
                <div class="col-md-2" id="dateDebutContainer" style="display: none;">
                    <label class="form-label fw-bold">Début</label>
                    <input type="date" class="form-control form-control-sm" id="dateDebut" value="2025-01-01">
                </div>
                <div class="col-md-2" id="dateFinContainer" style="display: none;">
                    <label class="form-label fw-bold">Fin</label>
                    <input type="date" class="form-control form-control-sm" id="dateFin" value="2025-12-31">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Catégorie</label>
                    <select class="form-select form-select-sm" id="categorieSelect">
                        <option value="all" selected>Toutes catégories</option>
                        <option value="logistique">Logistique</option>
                        <option value="entretien">Entretien</option>
                        <option value="fournitures">Fournitures</option>
                        <option value="carburant">Carburant</option>
                        <option value="autres">Autres</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Performance</label>
                    <select class="form-select form-select-sm" id="performanceSelect">
                        <option value="all" selected>Tous niveaux</option>
                        <option value="excellent">Excellent (4.5-5)</option>
                        <option value="bon">Bon (4-4.5)</option>
                        <option value="moyen">Moyen (3-4)</option>
                        <option value="critique">Critique (<3)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-sm w-100" onclick="appliquerFiltres()">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Métriques Avancées -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-tachometer-alt me-2"></i>Indicateurs de Performance Détaillés
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-success">94%</div>
                                <div class="metric-label">Respect délais</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 94%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-primary">87%</div>
                                <div class="metric-label">Qualité produits</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: 87%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-info">91%</div>
                                <div class="metric-label">Satisfaction</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: 91%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-warning">76%</div>
                                <div class="metric-label">Renouvellement</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: 76%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-danger">23</div>
                                <div class="metric-label">Litiges actifs</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-danger" style="width: 30%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="metric-card">
                                <div class="metric-value text-success">98%</div>
                                <div class="metric-label">Taux service</div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 98%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Engins et Mouvements -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-cogs me-2"></i>Statistiques des Engins
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="metric-card">
                                    <div class="metric-value text-primary">{{ $enginsStats['total_engins'] ?? 0 }}</div>
                                    <div class="metric-label">Total Engins</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="metric-card">
                                    <div class="metric-value text-success">{{ $enginsStats['engins_disponibles'] ?? 0 }}</div>
                                    <div class="metric-label">Disponibles</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="metric-card">
                                    <div class="metric-value text-danger">{{ $enginsStats['engins_en_panne'] ?? 0 }}</div>
                                    <div class="metric-label">En Panne</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $enginsStats['total_engins'] > 0 ? round(($enginsStats['engins_disponibles'] / $enginsStats['total_engins']) * 100, 1) : 0 }}%"></div>
                            </div>
                            <div class="text-center small text-muted mt-2">
                                Taux de disponibilité: {{ $enginsStats['total_engins'] > 0 ? round(($enginsStats['engins_disponibles'] / $enginsStats['total_engins']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-success text-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-exchange-alt me-2"></i>Mouvements des Prestations
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="metric-card">
                                    <div class="metric-value text-info">{{ $mouvementsStats['prestations_payees'] ?? 0 }}</div>
                                    <div class="metric-label">Prestations Payées</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="metric-card">
                                    <div class="metric-value text-warning">{{ $mouvementsStats['prestations_non_payees'] ?? 0 }}</div>
                                    <div class="metric-label">Prestations Non Payées</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="metric-card">
                                    <div class="metric-value text-primary">{{ $mouvementsStats['total_prestations'] ?? 0 }}</div>
                                    <div class="metric-label">Total Prestations</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ $mouvementsStats['taux_paiement'] }}%"></div>
                            </div>
                            <div class="text-center small text-muted mt-2">
                                Taux de paiement: {{ $mouvementsStats['taux_paiement'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>Évolution des Achats par Catégorie
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="achatsEvolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par Catégorie
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 280px;">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                    <div class="mt-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fas fa-circle text-primary me-1"></i>Logistique</span>
                            <span class="fw-bold">32% (15)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fas fa-circle text-success me-1"></i>Entretien</span>
                            <span class="fw-bold">28% (13)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fas fa-circle text-warning me-1"></i>Fournitures</span>
                            <span class="fw-bold">18% (8)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fas fa-circle text-info me-1"></i>Carburant</span>
                            <span class="fw-bold">15% (7)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span><i class="fas fa-circle text-secondary me-1"></i>Autres</span>
                            <span class="fw-bold">7% (4)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Analyses Détaillées -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Top 10 Fournisseurs par CA
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="topFournisseursChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Évolution des Performances
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Alertes et Actions -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Fournisseurs à Surveiller
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fournisseur</th>
                                    <th>Note</th>
                                    <th>Risques</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-warning">Fournitures Express</td>
                                    <td><span class="badge bg-danger">2.8/5</span></td>
                                    <td class="small">Délais, Qualité</td>
                                    <td><button class="btn btn-sm btn-warning">Audit</button></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-warning">Quick Service</td>
                                    <td><span class="badge bg-warning">3.2/5</span></td>
                                    <td class="small">Prix élevés</td>
                                    <td><button class="btn btn-sm btn-info">Renégocier</button></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-warning">Global Parts</td>
                                    <td><span class="badge bg-warning">3.5/5</span></td>
                                    <td class="small">Fiabilité</td>
                                    <td><button class="btn btn-sm btn-secondary">Surveiller</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-star me-2"></i>Fournisseurs Excellents
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fournisseur</th>
                                    <th>Note</th>
                                    <th>Spécialité</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-success">TechnoPlus SA</td>
                                    <td><span class="badge bg-success">4.8/5 ⭐</span></td>
                                    <td class="small">Équipements tech</td>
                                    <td><button class="btn btn-sm btn-success">Prioriser</button></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-success">Logistics Pro</td>
                                    <td><span class="badge bg-success">4.7/5 ⭐</span></td>
                                    <td class="small">Transport & logistique</td>
                                    <td><button class="btn btn-sm btn-success">Prioriser</button></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-success">Energy Solutions</td>
                                    <td><span class="badge bg-success">4.6/5 ⭐</span></td>
                                    <td class="small">Maintenance énergétique</td>
                                    <td><button class="btn btn-sm btn-success">Prioriser</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Résumé et Objectifs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-bullseye me-2"></i>Résumé Annuel & Objectifs 2025
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Évolution 2024 → 2025</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="h4 text-success fw-bold">+15%</div>
                                        <div class="small text-muted">Nombre fournisseurs</div>
                                        <div class="small text-success mt-1">47 → 54 cibles</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="h4 text-info fw-bold">+8%</div>
                                        <div class="small text-muted">CA fournisseurs</div>
                                        <div class="small text-info mt-1">892M → 963M</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Objectifs 2025</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="h4 text-primary fw-bold">54</div>
                                        <div class="small text-muted">Fournisseurs actifs</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: 87%"></div>
                                        </div>
                                        <div class="small text-primary mt-1">Progression: 87%</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="h4 text-warning fw-bold">4.3</div>
                                        <div class="small text-muted">Note moyenne cible</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-warning" style="width: 95%"></div>
                                        </div>
                                        <div class="small text-warning mt-1">Progression: 95%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<!-- Modal Nouveau Fournisseur -->
<div class="modal fade" id="nouveauFournisseurModal" tabindex="-1" aria-labelledby="nouveauFournisseurModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nouveauFournisseurModalLabel">
                    <i class="fas fa-plus me-2"></i>Nouveau Fournisseur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="nouveauFournisseurForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Nom du fournisseur <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomFournisseur" required placeholder="Ex: TechnoPlus SA">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="categorieFournisseur" required>
                                <option value="">Sélectionner</option>
                                <option value="logistique">Logistique</option>
                                <option value="entretien">Entretien</option>
                                <option value="fournitures">Fournitures</option>
                                <option value="carburant">Carburant</option>
                                <option value="autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Email</label>
                            <input type="email" class="form-control" id="emailFournisseur" placeholder="contact@fournisseur.ci">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Téléphone</label>
                            <input type="tel" class="form-control" id="telFournisseur" placeholder="+225 XX XX XX XX">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Adresse</label>
                            <textarea class="form-control" id="adresseFournisseur" rows="3" placeholder="Adresse complète"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Spécialités/Produits</label>
                            <textarea class="form-control" id="specialitesFournisseur" rows="3" placeholder="Domaines d'expertise, produits/services proposés"></textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" onclick="ajouterFournisseur()">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
$(document).ready(function() {
    // Gestion de la période personnalisée
    $('#periodeSelect').change(function() {
        if ($(this).val() === 'custom') {
            $('#dateDebutContainer, #dateFinContainer').show();
        } else {
            $('#dateDebutContainer, #dateFinContainer').hide();
        }
    });

    // Graphiques avec données améliorées
    const achatsCtx = document.getElementById('achatsEvolutionChart').getContext('2d');
    new Chart(achatsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Logistique (FCFA)',
                data: [45000000, 52000000, 48000000, 61000000, 55000000, 58000000, 62000000, 65000000, 67000000, 69000000, 71000000, 75000000],
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Entretien (FCFA)',
                data: [38000000, 42000000, 41000000, 45000000, 48000000, 46000000, 50000000, 52000000, 54000000, 56000000, 57000000, 59000000],
                borderColor: 'rgb(28, 200, 138)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Fournitures (FCFA)',
                data: [25000000, 28000000, 26000000, 30000000, 32000000, 31000000, 34000000, 36000000, 37000000, 38000000, 39000000, 41000000],
                borderColor: 'rgb(255, 193, 7)',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000) + 'M FCFA';
                        }
                    }
                }
            }
        }
    });

    // Graphique catégories amélioré
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Logistique', 'Entretien', 'Fournitures', 'Carburant', 'Autres'],
            datasets: [{
                data: [32, 28, 18, 15, 7],
                backgroundColor: [
                    'rgb(78, 115, 223)',
                    'rgb(28, 200, 138)',
                    'rgb(255, 193, 7)',
                    'rgb(23, 162, 184)',
                    'rgb(108, 117, 125)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
            }
        }
    });

    // Graphique top fournisseurs amélioré
    const topFournisseursCtx = document.getElementById('topFournisseursChart').getContext('2d');
    new Chart(topFournisseursCtx, {
        type: 'bar',
        data: {
            labels: ['TechnoPlus', 'Logistics Pro', 'Energy Solutions', 'Maintenance Plus', 'SupplyChain', 'Global Parts', 'Quick Service', 'Fournitures Exp', 'Parts Plus', 'Service Direct'],
            datasets: [{
                label: 'CA par fournisseur (M FCFA)',
                data: [245, 189, 156, 134, 123, 98, 87, 76, 65, 54],
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgb(78, 115, 223)',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 50 }
                }
            }
        }
    });

    // Graphique performances amélioré
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Note moyenne',
                data: [4.0, 4.1, 4.2, 4.1, 4.3, 4.2, 4.3, 4.2, 4.1, 4.2, 4.1, 4.1],
                borderColor: 'rgb(28, 200, 138)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Respect délais (%)',
                data: [92, 93, 94, 93, 95, 94, 96, 95, 94, 95, 94, 94],
                borderColor: 'rgb(255, 193, 7)',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top' } },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    min: 3.5,
                    max: 5.0
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    min: 85,
                    max: 100,
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
});

// Fonctions utilitaires
function rafraichirDashboard() {
    location.reload();
}

function appliquerFiltres() {
    // Appliquer les filtres sélectionnés
    const periode = document.getElementById('periodeSelect').value;
    const categorie = document.getElementById('categorieSelect').value;
    const performance = document.getElementById('performanceSelect').value;

    console.log('Filtres appliqués:', { periode, categorie, performance });
    // Ici, on pourrait recharger les graphiques avec les données filtrées
}

function exporterRapport() {
    alert('Fonctionnalité d\'export en cours de développement');
}

function ajouterFournisseur() {
    // Logique d'ajout de fournisseur
    alert('Fonctionnalité d\'ajout de fournisseur en cours de développement');
}
</script>
@endpush

<style>
.metric-card {
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
}

.metric-value {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.metric-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
}

.card-header {
    border-bottom: 2px solid #e9ecef !important;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .chart-container {
        height: 250px !important;
    }
}
</style>
@endsection
