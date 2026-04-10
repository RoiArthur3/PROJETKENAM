@extends('layouts.app')

@section('title', 'Dashboard Agenda | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-days mr-2 text-primary"></i>Dashboard Agenda & Planification
            </h1>
            <p class="text-muted">Vue d'ensemble des plannings et rendez-vous</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNouvelEvenement">
                    <i class="fas fa-plus mr-1"></i>Nouvel Événement
                </button>
                <button class="btn btn-outline-info">
                    <i class="fas fa-calendar mr-1"></i>Vue Calendrier
                </button>
                <button class="btn btn-outline-success">
                    <i class="fas fa-file-export mr-1"></i>Exporter Planning
                </button>
            </div>
        </div>
    </div>

    <!-- Période de sélection -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-alt mr-2"></i>Sélection de Période
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select class="form-select" id="periodeSelect">
                        <option value="today">Aujourd'hui</option>
                        <option value="week" selected>Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="quarter">Ce trimestre</option>
                        <option value="year">Cette année</option>
                        <option value="custom">Personnalisé</option>
                    </select>
                </div>
                <div class="col-md-3" id="dateDebutContainer" style="display: none;">
                    <label class="form-label">Date début</label>
                    <input type="date" class="form-control" id="dateDebut" value="2025-11-01">
                </div>
                <div class="col-md-3" id="dateFinContainer" style="display: none;">
                    <label class="form-label">Date fin</label>
                    <input type="date" class="form-control" id="dateFin" value="2025-11-30">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type d'événement</label>
                    <select class="form-select" id="typeEvenement">
                        <option value="all">Tous types</option>
                        <option value="operation">Opération</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="rdv">Rendez-vous</option>
                        <option value="formation">Formation</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs principaux -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Événements Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">5 déjà passés</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Cette Semaine</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">42</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">+12 vs semaine dernière</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Retard</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-warning">Nécessitent attention</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taux d'Exécution</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">94%</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">Objectif: 95%</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier intégré et prochains événements -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar mr-2"></i>Calendrier - Novembre 2025
                    </h6>
                </div>
                <div class="card-body">
                    <div class="calendar-container">
                        <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h5 class="mb-0">Novembre 2025</h5>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <div class="calendar-grid">
                            <div class="calendar-weekdays">
                                <div class="weekday">Lun</div>
                                <div class="weekday">Mar</div>
                                <div class="weekday">Mer</div>
                                <div class="weekday">Jeu</div>
                                <div class="weekday">Ven</div>
                                <div class="weekday">Sam</div>
                                <div class="weekday">Dim</div>
                            </div>

                            <div class="calendar-days">
                                <!-- Jours du calendrier avec événements -->
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day">1</div>
                                <div class="calendar-day">2</div>

                                <div class="calendar-day event-day">
                                    3
                                    <div class="event-dot bg-primary" title="Maintenance générateur"></div>
                                    <div class="event-dot bg-success" title="Réunion équipe"></div>
                                </div>
                                <div class="calendar-day event-day">
                                    4
                                    <div class="event-dot bg-warning" title="Formation sécurité"></div>
                                </div>
                                <div class="calendar-day event-day">
                                    5
                                    <div class="event-dot bg-info" title="Contrôle qualité"></div>
                                </div>
                                <div class="calendar-day event-day">
                                    6
                                    <div class="event-dot bg-danger" title="Audit externe"></div>
                                </div>
                                <div class="calendar-day">7</div>
                                <div class="calendar-day">8</div>

                                <div class="calendar-day event-day">
                                    9
                                    <div class="event-dot bg-primary" title="Entretien ascenseur"></div>
                                </div>
                                <div class="calendar-day event-day">
                                    10
                                    <div class="event-dot bg-success" title="Livraison matériel"></div>
                                </div>
                                <div class="calendar-day event-day">
                                    11
                                    <div class="event-dot bg-warning" title="Planification projets"></div>
                                </div>
                                <div class="calendar-day">12</div>
                                <div class="calendar-day">13</div>
                                <div class="calendar-day">14</div>

                                <div class="calendar-day event-day">
                                    15
                                    <div class="event-dot bg-info" title="Rendez-vous client"></div>
                                </div>
                                <div class="calendar-day event-day">
                                    16
                                    <div class="event-dot bg-danger" title="Maintenance urgente"></div>
                                </div>
                                <div class="calendar-day event-day">
                                    17
                                    <div class="event-dot bg-primary" title="Formation équipe"></div>
                                </div>
                                <div class="calendar-day">18</div>
                                <div class="calendar-day">19</div>
                                <div class="calendar-day">20</div>

                                <div class="calendar-day event-day">
                                    21
                                    <div class="event-dot bg-success" title="Réunion planning"></div>
                                </div>
                                <div class="calendar-day">22</div>
                                <div class="calendar-day">23</div>
                                <div class="calendar-day">24</div>
                                <div class="calendar-day">25</div>
                                <div class="calendar-day">26</div>
                                <div class="calendar-day">27</div>
                                <div class="calendar-day">28</div>
                                <div class="calendar-day">29</div>
                                <div class="calendar-day">30</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock mr-2"></i>Aujourd'hui - {{ now()->format('d/m/Y') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">08:00 - Réception fournisseur</h6>
                                <p class="timeline-text small text-muted">Matériel informatique - Bureau 201</p>
                                <span class="badge bg-success">Terminé</span>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">09:30 - Maintenance générateur</h6>
                                <p class="timeline-text small text-muted">Contrôle mensuel - Équipe technique</p>
                                <span class="badge bg-primary">En cours</span>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">11:00 - Formation sécurité</h6>
                                <p class="timeline-text small text-muted">Salle de formation - Tous services</p>
                                <span class="badge bg-warning">Planifié</span>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">14:00 - Contrôle qualité</h6>
                                <p class="timeline-text small text-muted">Production ligne A - Service qualité</p>
                                <span class="badge bg-info">Planifié</span>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">16:00 - Audit externe</h6>
                                <p class="timeline-text small text-muted">Visite organisme certificateur</p>
                                <span class="badge bg-danger">Urgent</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et analyses -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Répartition par Type d'Événement
                        <small class="text-muted">(Diagramme circulaire)</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container pie-chart-container" style="position: relative; height: 350px; width: 350px; margin: 0 auto;">
                        <canvas id="typesEvenementsChart"></canvas>
                    </div>
                    <div class="mt-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Maintenance</span>
                            <span class="text-muted">35% (42 événements)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Opérations</span>
                            <span class="text-muted">28% (34 événements)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Rendez-vous</span>
                            <span class="text-muted">20% (24 événements)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Formation</span>
                            <span class="text-muted">12% (14 événements)</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Autres</span>
                            <span class="text-muted">5% (6 événements)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-2"></i>Évolution Mensuelle
                        <small class="text-muted">(Courbe)</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="evolutionMensuelleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et rappels importants -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell mr-2"></i>Alertes et Rappels
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>3 événements en retard</strong> nécessitent une reprogrammation immédiate.
                    </div>

                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-calendar-check mr-2"></i>
                        <strong>8 maintenances préventives</strong> sont dues ce trimestre.
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Événements critiques cette semaine</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Audit externe qualité
                                    <span class="badge bg-danger">Demain</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Maintenance générateur principal
                                    <span class="badge bg-warning">Jeudi</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Formation sécurité incendie
                                    <span class="badge bg-info">Vendredi</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Actions recommandées</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-calendar-plus mr-2"></i>Planifier maintenances
                                </button>
                                <button class="btn btn-outline-success">
                                    <i class="fas fa-envelope mr-2"></i>Envoyer rappels
                                </button>
                                <button class="btn btn-outline-warning">
                                    <i class="fas fa-exchange-alt mr-2"></i>Réorganiser planning
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé des services -->
    <div class="row mb-4">
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building mr-2"></i>Activité par Service
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-primary font-weight-bold">18</div>
                                <div class="text-muted small">Opérations</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-success font-weight-bold">15</div>
                                <div class="text-muted small">Maintenance</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 75%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-info font-weight-bold">12</div>
                                <div class="text-muted small">Administration</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 60%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-warning font-weight-bold">9</div>
                                <div class="text-muted small">Logistique</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 45%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Statistiques générales</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="h5 text-success font-weight-bold">94%</div>
                                    <div class="text-muted small">Taux d'exécution</div>
                                </div>
                                <div class="col-6">
                                    <div class="h5 text-info font-weight-bold">2.3h</div>
                                    <div class="text-muted small">Durée moyenne</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Évolution trimestrielle</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="h5 text-primary font-weight-bold">+8%</div>
                                    <div class="text-muted small">Nombre d'événements</div>
                                </div>
                                <div class="col-6">
                                    <div class="h5 text-success font-weight-bold">+12%</div>
                                    <div class="text-muted small">Taux de respect</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvel Événement -->
<div class="modal fade" id="modalNouvelEvenement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Planifier un Nouvel Événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nouvelEvenementForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Titre de l'événement *</label>
                            <input type="text" class="form-control" placeholder="Ex: Maintenance générateur" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type d'événement *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="operation">Opération</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="rdv">Rendez-vous</option>
                                <option value="formation">Formation</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Heure début *</label>
                            <input type="time" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Heure fin *</label>
                            <input type="time" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Service concerné *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option>Opérations</option>
                                <option>Maintenance</option>
                                <option>Administration</option>
                                <option>Logistique</option>
                                <option>Qualité</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Responsable</label>
                            <select class="form-select">
                                <option value="">Sélectionner</option>
                                <option>Jean Dupont</option>
                                <option>Marie Curie</option>
                                <option>Pierre Louis</option>
                                <option>Sophie Martin</option>
                                <option>Ahmed Benali</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Lieu</label>
                            <input type="text" class="form-control" placeholder="Salle, bureau, etc.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priorité</label>
                            <select class="form-select">
                                <option value="normal">Normal</option>
                                <option value="important">Important</option>
                                <option value="urgent">Urgent</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" placeholder="Détails de l'événement..."></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Participants</label>
                            <input type="text" class="form-control" placeholder="Noms ou services concernés">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Rappel</label>
                            <select class="form-select">
                                <option value="aucun">Aucun rappel</option>
                                <option value="15min">15 minutes avant</option>
                                <option value="1h">1 heure avant</option>
                                <option value="1j">1 jour avant</option>
                                <option value="1sem">1 semaine avant</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Planifier l'Événement</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    console.log('🚀 Initialisation Dashboard Agenda...');

    // Vérifier que Chart.js est chargé
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js n\'est pas chargé');
        return;
    }

    // Gestion de la période personnalisée
    $('#periodeSelect').change(function() {
        if ($(this).val() === 'custom') {
            $('#dateDebutContainer, #dateFinContainer').show();
        } else {
            $('#dateDebutContainer, #dateFinContainer').hide();
        }
    });

    // Graphique des types d'événements - Diagramme circulaire (Pie Chart)
    try {
        const typesCanvas = document.getElementById('typesEvenementsChart');
        if (typesCanvas) {
            const typesCtx = typesCanvas.getContext('2d');
            new Chart(typesCtx, {
                type: 'pie',
                data: {
                    labels: ['Maintenance', 'Opérations', 'Rendez-vous', 'Formation', 'Autres'],
                    datasets: [{
                        data: [35, 28, 20, 12, 5],
                        backgroundColor: [
                            'rgb(40, 167, 69)',    // Vert KENAM
                            'rgb(23, 162, 184)',    // Bleu info
                            'rgb(255, 193, 7)',     // Jaune warning
                            'rgb(220, 53, 69)',     // Rouge danger
                            'rgb(108, 117, 125)'    // Gris
                        ],
                        borderColor: 'rgba(255, 255, 255, 0.8)',
                        borderWidth: 3,
                        hoverBorderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: 20
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' événements (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    elements: {
                        arc: {
                            borderRadius: 4
                        }
                    }
                }
            });
            console.log('✅ Diagramme circulaire chargé');
        } else {
            console.error('❌ Canvas typesEvenementsChart non trouvé');
        }
    } catch (error) {
        console.error('❌ Erreur diagramme circulaire:', error);
    }

    // Graphique d'évolution mensuelle - Courbe (Line Chart)
    try {
        const evolutionCanvas = document.getElementById('evolutionMensuelleChart');
        if (evolutionCanvas) {
            const evolutionCtx = evolutionCanvas.getContext('2d');
            new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Nombre d\'événements',
                        data: [38, 42, 45, 39, 52, 48, 55, 58, 51, 60, 56, 62],
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderColor: 'rgb(40, 167, 69)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgb(40, 167, 69)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' événements';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 10,
                                color: '#64748b'
                            },
                            grid: {
                                color: 'rgba(100, 116, 139, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#64748b'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
            console.log('✅ Courbe d\'évolution chargée');
        } else {
            console.error('❌ Canvas evolutionMensuelleChart non trouvé');
        }
    } catch (error) {
        console.error('❌ Erreur courbe évolution:', error);
    }

    // Animation des événements du calendrier
    $('.event-dot').hover(
        function() {
            $(this).addClass('pulse');
        },
        function() {
            $(this).removeClass('pulse');
        }
    );

    // Animation des alertes
    $('.alert').hide().fadeIn(1000);

    console.log('🎉 Dashboard Agenda initialisé avec succès !');
});
</script>

<style>
.calendar-container {
    background: white;
    border-radius: 0.375rem;
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #f8f9fc;
}

.weekday {
    padding: 10px;
    text-align: center;
    font-weight: bold;
    color: #6c757d;
    background: #f8f9fc;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #dee2e6;
}

.calendar-day {
    background: white;
    min-height: 80px;
    padding: 5px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s;
}

.calendar-day:hover {
    background-color: #f8f9fc;
}

.calendar-day.empty {
    background: #f8f9fc;
}

.event-day {
    background: linear-gradient(135deg, #fff 0%, #f8f9fc 100%);
}

.event-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    position: absolute;
    bottom: 5px;
    left: 50%;
    transform: translateX(-50%);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: translateX(-50%) scale(1); }
    50% { transform: translateX(-50%) scale(1.2); }
    100% { transform: translateX(-50%) scale(1); }
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fc;
    padding: 10px 15px;
    border-radius: 6px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 0.9rem;
    font-weight: 600;
}

.timeline-text {
    margin: 0;
    color: #6c757d;
}

/* Style spécifique pour le diagramme circulaire carré */
.pie-chart-container {
    max-width: 350px;
    aspect-ratio: 1;
    margin: 0 auto;
    border-radius: 8px;
    overflow: hidden;
}

@media (max-width: 768px) {
    .pie-chart-container {
        max-width: 280px;
        margin: 0 auto;
    }
}
</style>
@endsection
