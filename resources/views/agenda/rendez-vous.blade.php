@extends('layouts.app')

@section('title', 'Rendez-vous | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-handshake mr-2 text-primary"></i>Gestion des Rendez-vous
            </h1>
            <p class="text-muted">Planification et suivi des rendez-vous professionnels</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNouveauRdv">
                    <i class="fas fa-plus mr-1"></i>Nouveau RDV
                </button>
                <button class="btn btn-outline-info" id="btnVueCalendrier">
                    <i class="fas fa-calendar mr-1"></i>Calendrier
                </button>
                <button class="btn btn-outline-success" id="btnVueListe">
                    <i class="fas fa-list mr-1"></i>Liste
                </button>
                <button class="btn btn-outline-warning">
                    <i class="fas fa-envelope mr-1"></i>Notifications
                </button>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filtres et Recherche
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Type de rendez-vous</label>
                    <select class="form-select" id="filterType">
                        <option value="all">Tous types</option>
                        <option value="client">Client</option>
                        <option value="fournisseur">Fournisseur</option>
                        <option value="partenaire">Partenaire</option>
                        <option value="interne">Interne</option>
                        <option value="prospection">Prospection</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" id="filterStatut">
                        <option value="all">Tous statuts</option>
                        <option value="planifie">Planifié</option>
                        <option value="confirme">Confirmé</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="annule">Annulé</option>
                        <option value="reporte">Reporté</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select class="form-select" id="filterPeriode">
                        <option value="today">Aujourd'hui</option>
                        <option value="week" selected>Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="quarter">Ce trimestre</option>
                        <option value="all">Toutes périodes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Interlocuteur, objet...">
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Calendrier (par défaut) -->
    <div id="calendrierView">
        <div class="row">
            <div class="col-lg-9 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-alt mr-2"></i>Calendrier des Rendez-vous - Novembre 2025
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="calendar-container">
                            <div class="calendar-weekdays">
                                <div class="weekday">Lundi</div>
                                <div class="weekday">Mardi</div>
                                <div class="weekday">Mercredi</div>
                                <div class="weekday">Jeudi</div>
                                <div class="weekday">Vendredi</div>
                                <div class="weekday">Samedi</div>
                                <div class="weekday">Dimanche</div>
                            </div>

                            <div class="calendar-days" id="calendarDays">
                                <!-- Les jours seront générés par JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock mr-2"></i>Aujourd'hui
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="todayRdvs">
                            <!-- Rendez-vous du jour affichés ici -->
                        </div>
                    </div>
                </div>

                <!-- Prochains RDV -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-forward mr-2"></i>Prochains RDV
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="upcomingRdvs">
                            <!-- Prochains rendez-vous affichés ici -->
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bolt mr-2"></i>Actions Rapides
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus-circle mr-1"></i>Nouveau RDV
                            </button>
                            <button class="btn btn-outline-success btn-sm">
                                <i class="fas fa-phone mr-1"></i>Appeler
                            </button>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-envelope mr-1"></i>Email
                            </button>
                            <button class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-clock mr-1"></i>Reporter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Liste (masquée par défaut) -->
    <div id="listeView" style="display: none;">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list mr-2"></i>Liste des Rendez-vous (45)
                </h6>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-success">
                        <i class="fas fa-file-excel mr-1"></i>Export Excel
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="fas fa-print mr-1"></i>Imprimer
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="rdvsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Interlocuteur</th>
                                <th>Type</th>
                                <th>Date & Heure</th>
                                <th>Objet</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="rdvsTableBody">
                            <!-- Les rendez-vous seront ajoutés dynamiquement -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav class="mt-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Précédent</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">Suivant</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mt-4">
        <div class="col-lg-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                RDV ce Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthRdvsCount">45</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                RDV Confirmés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="confirmedRdvsCount">38</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingRdvsCount">7</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taux de Réussite</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="successRate">89%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Rendez-vous -->
<div class="modal fade" id="modalNouveauRdv" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Planifier un Nouveau Rendez-vous</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nouveauRdvForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Type de rendez-vous *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="client">Client</option>
                                <option value="fournisseur">Fournisseur</option>
                                <option value="partenaire">Partenaire</option>
                                <option value="interne">Interne</option>
                                <option value="prospection">Prospection</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Interlocuteur *</label>
                            <input type="text" class="form-control" placeholder="Nom de l'interlocuteur" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Société/Organisation</label>
                            <input type="text" class="form-control" placeholder="Nom de l'entreprise">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" placeholder="+225 XX XX XX XX">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="contact@example.com">
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
                            <label class="form-label">Durée (minutes)</label>
                            <select class="form-select">
                                <option value="30">30 min</option>
                                <option value="60" selected>1 heure</option>
                                <option value="90">1h30</option>
                                <option value="120">2 heures</option>
                                <option value="180">3 heures</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Lieu *</label>
                            <input type="text" class="form-control" placeholder="Adresse ou salle de réunion" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Organisateur</label>
                            <select class="form-select">
                                <option value="">Sélectionner</option>
                                <option>Jean Dupont</option>
                                <option>Marie Curie</option>
                                <option>Pierre Louis</option>
                                <option>Sophie Martin</option>
                                <option>Ahmed Benali</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Objet du rendez-vous *</label>
                            <input type="text" class="form-control" placeholder="Sujet de la réunion" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" placeholder="Ordre du jour, points à aborder..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Priorité</label>
                            <select class="form-select">
                                <option value="normal">Normal</option>
                                <option value="important">Important</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rappel</label>
                            <select class="form-select">
                                <option value="aucun">Aucun rappel</option>
                                <option value="15min">15 minutes avant</option>
                                <option value="1h">1 heure avant</option>
                                <option value="1j">1 jour avant</option>
                                <option value="1sem">1 semaine avant</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Participants supplémentaires</label>
                            <input type="text" class="form-control" placeholder="Autres participants (séparés par des virgules)">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Documents joints</label>
                            <input type="file" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <div class="form-text">Présentations, contrats, documents de préparation</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Planifier le Rendez-vous</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    let currentDate = new Date(2025, 10, 1); // Novembre 2025

    // Données des rendez-vous
    const rdvs = [
        { id: 1, reference: 'RDV-2025-045', interlocuteur: 'Marc Dubois', societe: 'TechSolutions CI', type: 'client', date: '2025-11-03', heure: '09:00', duree: 60, lieu: 'Salle de réunion A', objet: 'Suivi projet digitalisation', statut: 'confirme', organisateur: 'Jean Dupont' },
        { id: 2, reference: 'RDV-2025-046', interlocuteur: 'Sarah Koné', societe: 'Logistics Plus', type: 'fournisseur', date: '2025-11-03', heure: '14:00', duree: 90, lieu: 'Bureau direction', objet: 'Négociation contrat transport', statut: 'planifie', organisateur: 'Marie Curie' },
        { id: 3, reference: 'RDV-2025-047', interlocuteur: 'Dr. Alain Traoré', societe: 'CHU Cocody', type: 'partenaire', date: '2025-11-04', heure: '10:00', duree: 45, lieu: 'Téléconférence', objet: 'Maintenance équipements médicaux', statut: 'confirme', organisateur: 'Pierre Louis' },
        { id: 4, reference: 'RDV-2025-048', interlocuteur: 'Équipe commerciale', societe: 'KENAM Services', type: 'interne', date: '2025-11-05', heure: '09:30', duree: 120, lieu: 'Salle formation', objet: 'Réunion stratégie commerciale', statut: 'planifie', organisateur: 'Sophie Martin' },
        { id: 5, reference: 'RDV-2025-049', interlocuteur: 'Paul N\'Guessan', societe: 'Construction Moderne', type: 'prospection', date: '2025-11-06', heure: '15:00', duree: 60, lieu: 'Site client', objet: 'Présentation services maintenance', statut: 'planifie', organisateur: 'Ahmed Benali' },
        { id: 6, reference: 'RDV-2025-050', interlocuteur: 'Marie-Claire Kouassi', societe: 'Banque Atlantique', type: 'client', date: '2025-11-07', heure: '11:00', duree: 30, lieu: 'Agence Plateau', objet: 'Signature contrat maintenance', statut: 'termine', organisateur: 'Jean Dupont' },
        { id: 7, reference: 'RDV-2025-051', interlocuteur: 'Thomas Anderson', societe: 'Global Industries', type: 'partenaire', date: '2025-11-10', heure: '14:30', duree: 75, lieu: 'Siège social', objet: 'Partenariat stratégique', statut: 'confirme', organisateur: 'Marie Curie' },
        { id: 8, reference: 'RDV-2025-052', interlocuteur: 'Équipe RH', societe: 'KENAM Services', type: 'interne', date: '2025-11-12', heure: '10:00', duree: 90, lieu: 'Salle conseil', objet: 'Recrutement techniciens', statut: 'planifie', organisateur: 'Sophie Martin' }
    ];

    function renderCalendrier() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay() + 1);

        let html = '';

        for (let i = 0; i < 42; i++) {
            const currentDay = new Date(startDate);
            currentDay.setDate(startDate.getDate() + i);

            const isCurrentMonth = currentDay.getMonth() === month;
            const isToday = currentDay.toDateString() === new Date().toDateString();
            const dayRdvs = rdvs.filter(rdv => rdv.date === currentDay.toISOString().split('T')[0]);

            let classes = 'calendar-day';
            if (!isCurrentMonth) classes += ' empty';
            if (isToday) classes += ' today';
            if (dayRdvs.length > 0) classes += ' has-rdv';

            html += `<div class="${classes}" data-date="${currentDay.toISOString().split('T')[0]}">`;

            if (isCurrentMonth) {
                html += `<div class="day-number">${currentDay.getDate()}</div>`;

                // Afficher les RDV
                dayRdvs.slice(0, 2).forEach(rdv => {
                    const typeClass = getRdvTypeClass(rdv.type);
                    html += `<div class="rdv-item ${typeClass}" data-rdv-id="${rdv.id}" title="${rdv.interlocuteur} - ${rdv.objet}">${rdv.interlocuteur}</div>`;
                });

                if (dayRdvs.length > 2) {
                    html += `<div class="more-rdv">+${dayRdvs.length - 2} autres</div>`;
                }
            }

            html += '</div>';
        }

        $('#calendarDays').html(html);

        // Afficher les RDV du jour
        renderTodayRdvs();

        // Afficher les prochains RDV
        renderUpcomingRdvs();
    }

    function renderTodayRdvs() {
        const today = new Date().toISOString().split('T')[0];
        const todayRdvs = rdvs.filter(rdv => rdv.date === today);

        let html = '';
        if (todayRdvs.length === 0) {
            html = '<p class="text-muted small">Aucun rendez-vous prévu aujourd\'hui</p>';
        } else {
            todayRdvs.forEach(rdv => {
                const typeClass = getRdvTypeClass(rdv.type);
                const statusClass = getStatusClass(rdv.statut);

                html += `
                    <div class="rdv-today-item ${typeClass}">
                        <h6 class="mb-1">${rdv.interlocuteur}</h6>
                        <p class="small mb-1">${rdv.objet}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${rdv.heure} (${rdv.duree}min)</small>
                            <span class="badge ${statusClass}">${rdv.statut}</span>
                        </div>
                        <small class="text-muted">${rdv.lieu}</small>
                    </div>
                `;
            });
        }

        $('#todayRdvs').html(html);
    }

    function renderUpcomingRdvs() {
        const today = new Date();
        const nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);

        const upcomingRdvs = rdvs.filter(rdv => {
            const rdvDate = new Date(rdv.date);
            return rdvDate >= today && rdvDate <= nextWeek && rdv.statut !== 'termine';
        }).slice(0, 5);

        let html = '';
        if (upcomingRdvs.length === 0) {
            html = '<p class="text-muted small">Aucun rendez-vous prévu dans les 7 jours</p>';
        } else {
            upcomingRdvs.forEach(rdv => {
                const typeClass = getRdvTypeClass(rdv.type);
                const daysUntil = Math.ceil((new Date(rdv.date) - today) / (1000 * 60 * 60 * 24));
                html += `
                    <div class="upcoming-rdv ${typeClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong class="small">${rdv.interlocuteur}</strong>
                                <br>
                                <small class="text-muted">${rdv.objet}</small>
                                <br>
                                <small class="text-muted">${rdv.date} ${rdv.heure}</small>
                            </div>
                            <span class="badge bg-secondary">${daysUntil}j</span>
                        </div>
                    </div>
                `;
            });
        }

        $('#upcomingRdvs').html(html);
    }

    function renderRdvsTable() {
        let html = '';

        rdvs.forEach(rdv => {
            const typeClass = getRdvTypeClass(rdv.type);
            const statusClass = getStatusClass(rdv.statut);

            html += `
                <tr>
                    <td><span class="badge bg-primary">${rdv.reference}</span></td>
                    <td>
                        <div class="font-weight-bold">${rdv.interlocuteur}</div>
                        <div class="text-muted small">${rdv.societe}</div>
                    </td>
                    <td><span class="badge ${typeClass}">${rdv.type}</span></td>
                    <td>
                        <div>${rdv.date}</div>
                        <div class="text-muted small">${rdv.heure} (${rdv.duree}min)</div>
                    </td>
                    <td>
                        <div class="font-weight-bold">${rdv.objet}</div>
                        <div class="text-muted small">${rdv.lieu}</div>
                    </td>
                    <td><span class="badge ${statusClass}">${rdv.statut}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-success" title="Confirmer">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#rdvsTableBody').html(html);
    }

    function getRdvTypeClass(type) {
        const classes = {
            'client': 'rdv-client',
            'fournisseur': 'rdv-fournisseur',
            'partenaire': 'rdv-partenaire',
            'interne': 'rdv-interne',
            'prospection': 'rdv-prospection',
            'autre': 'rdv-autre'
        };
        return classes[type] || 'rdv-autre';
    }

    function getStatusClass(status) {
        const classes = {
            'planifie': 'bg-info',
            'confirme': 'bg-success',
            'en_cours': 'bg-warning',
            'termine': 'bg-secondary',
            'annule': 'bg-danger',
            'reporte': 'bg-warning'
        };
        return classes[status] || 'bg-secondary';
    }

    function updateStats() {
        const monthRdvs = rdvs.length;
        const confirmedRdvs = rdvs.filter(rdv => rdv.statut === 'confirme').length;
        const pendingRdvs = rdvs.filter(rdv => ['planifie', 'confirme'].includes(rdv.statut)).length;
        const completedRdvs = rdvs.filter(rdv => rdv.statut === 'termine').length;
        const successRate = monthRdvs > 0 ? Math.round((completedRdvs / monthRdvs) * 100) : 0;

        $('#monthRdvsCount').text(monthRdvs);
        $('#confirmedRdvsCount').text(confirmedRdvs);
        $('#pendingRdvsCount').text(pendingRdvs);
        $('#successRate').text(successRate + '%');
    }

    // Gestion des vues
    $('#btnVueCalendrier').click(function() {
        $('#calendrierView').show();
        $('#listeView').hide();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        renderCalendrier();
    });

    $('#btnVueListe').click(function() {
        $('#calendrierView').hide();
        $('#listeView').show();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        renderRdvsTable();
    });

    // Filtres
    $('#filterType, #filterStatut, #filterPeriode').change(function() {
        // Implémentation du filtrage
        console.log('Filtre changé:', $(this).attr('id'), $(this).val());
    });

    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.rdv-item').each(function() {
            const title = $(this).text().toLowerCase();
            const visible = title.includes(searchTerm);
            $(this).toggle(visible);
        });
    });

    // Gestion des clics sur les RDV
    $(document).on('click', '.rdv-item', function(e) {
        e.stopPropagation();
        const rdvId = $(this).data('rdv-id');
        // Ouvrir modal détails RDV
        console.log('RDV cliqué:', rdvId);
    });

    // Initialisation
    renderCalendrier();
    updateStats();
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
    padding: 15px 10px;
    text-align: center;
    font-weight: bold;
    color: #6c757d;
    background: #f8f9fc;
    border-bottom: 1px solid #dee2e6;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #dee2e6;
}

.calendar-day {
    background: white;
    min-height: 120px;
    padding: 8px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s;
}

.calendar-day:hover {
    background-color: #f8f9fc;
    transform: scale(1.02);
}

.calendar-day.empty {
    background: #f8f9fc;
    cursor: default;
}

.calendar-day.empty:hover {
    background: #f8f9fc;
    transform: none;
}

.calendar-day.today {
    background: linear-gradient(135deg, #fff3cd 0%, #f8f9fc 100%);
    border: 2px solid #ffc107;
}

.calendar-day.has-rdv {
    background: linear-gradient(135deg, #fff 0%, #f8f9fc 100%);
}

.day-number {
    font-weight: bold;
    color: #495057;
    margin-bottom: 5px;
}

.rdv-item {
    font-size: 0.7rem;
    padding: 2px 4px;
    margin-bottom: 2px;
    border-radius: 3px;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
}

.rdv-client { background-color: #007bff; }
.rdv-fournisseur { background-color: #28a745; }
.rdv-partenaire { background-color: #17a2b8; }
.rdv-interne { background-color: #ffc107; color: #212529; }
.rdv-prospection { background-color: #fd7e14; }
.rdv-autre { background-color: #6c757d; }

.more-rdv {
    font-size: 0.7rem;
    color: #6c757d;
    font-style: italic;
    text-align: center;
    margin-top: 5px;
}

.rdv-today-item {
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
}

.upcoming-rdv {
    padding: 8px;
    margin-bottom: 6px;
    border-radius: 4px;
    border-left: 3px solid #007bff;
    background: #f8f9fc;
}
</style>
@endsection
