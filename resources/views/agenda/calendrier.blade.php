@extends('layouts.app')

@section('title', 'Calendrier | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-alt mr-2 text-primary"></i>Calendrier des Événements
            </h1>
            <p class="text-muted">Vue d'ensemble mensuelle de tous les événements planifiés</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNouvelEvenement">
                    <i class="fas fa-plus mr-1"></i>Nouvel Événement
                </button>
                <button class="btn btn-outline-info" id="btnVueHebdomadaire">
                    <i class="fas fa-calendar-week mr-1"></i>Vue Semaine
                </button>
                <button class="btn btn-outline-success" id="btnVueListe">
                    <i class="fas fa-list mr-1"></i>Vue Liste
                </button>
                <button class="btn btn-outline-warning">
                    <i class="fas fa-file-export mr-1"></i>Exporter
                </button>
            </div>
        </div>
    </div>

    <!-- Contrôles du calendrier -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnPrevMonth">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnToday">Aujourd'hui</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnNextMonth">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <h5 class="d-inline ml-3 mb-0" id="calendarTitle">Novembre 2025</h5>
                </div>
                <div class="col-md-8">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="filterType">
                                <option value="all">Tous types</option>
                                <option value="operation">Opérations</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="rdv">Rendez-vous</option>
                                <option value="formation">Formation</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="filterService">
                                <option value="all">Tous services</option>
                                <option value="operations">Opérations</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="administration">Administration</option>
                                <option value="logistique">Logistique</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="filterStatut">
                                <option value="all">Tous statuts</option>
                                <option value="planifie">Planifié</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                                <option value="annule">Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Rechercher...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Légende des couleurs -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h6 class="mb-3"><i class="fas fa-palette mr-2"></i>Légende des couleurs</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-primary me-2"></div>
                            <span class="small">Maintenance</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-success me-2"></div>
                            <span class="small">Opérations</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-warning me-2"></div>
                            <span class="small">Rendez-vous</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-info me-2"></div>
                            <span class="small">Formation</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-danger me-2"></div>
                            <span class="small">Urgent</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-color bg-secondary me-2"></div>
                            <span class="small">Autre</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier principal -->
    <div class="card shadow">
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

    <!-- Événements du jour sélectionné -->
    <div class="card shadow mt-4" id="dayEventsCard" style="display: none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-day mr-2"></i>Événements du <span id="selectedDate">01 novembre 2025</span>
            </h6>
        </div>
        <div class="card-body">
            <div id="dayEventsList">
                <!-- Les événements du jour seront affichés ici -->
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mt-4">
        <div class="col-lg-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Événements ce mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthEventsCount">42</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-month fa-2x text-gray-300"></i>
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
                                Cette semaine</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="weekEventsCount">12</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
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
                                Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayEventsCount">8</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                                Taux d'occupation</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="occupancyRate">68%</div>
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

<!-- Modal Détails Événement -->
<div class="modal fade" id="modalDetailsEvenement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Détails de l'événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventDetailsContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-warning">Modifier</button>
                <button type="button" class="btn btn-danger">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    let currentDate = new Date(2025, 10, 1); // Novembre 2025
    let selectedDate = null;

    // Événements de test
    const events = [
        { id: 1, title: 'Maintenance générateur', type: 'maintenance', service: 'maintenance', date: '2025-11-03', start: '09:00', end: '12:00', status: 'planifie', priority: 'normal' },
        { id: 2, title: 'Réunion équipe', type: 'rdv', service: 'operations', date: '2025-11-03', start: '14:00', end: '16:00', status: 'planifie', priority: 'normal' },
        { id: 3, title: 'Formation sécurité', type: 'formation', service: 'operations', date: '2025-11-04', start: '09:00', end: '17:00', status: 'planifie', priority: 'important' },
        { id: 4, title: 'Contrôle qualité', type: 'operation', service: 'qualite', date: '2025-11-05', start: '10:00', end: '12:00', status: 'planifie', priority: 'normal' },
        { id: 5, title: 'Audit externe', type: 'autre', service: 'administration', date: '2025-11-06', start: '09:00', end: '17:00', status: 'planifie', priority: 'critique' },
        { id: 6, title: 'Entretien ascenseur', type: 'maintenance', service: 'maintenance', date: '2025-11-09', start: '08:00', end: '12:00', status: 'planifie', priority: 'urgent' },
        { id: 7, title: 'Livraison matériel', type: 'operation', service: 'logistique', date: '2025-11-10', start: '14:00', end: '16:00', status: 'termine', priority: 'normal' },
        { id: 8, title: 'Planification projets', type: 'rdv', service: 'administration', date: '2025-11-11', start: '10:00', end: '12:00', status: 'planifie', priority: 'important' },
        { id: 9, title: 'Rendez-vous client', type: 'rdv', service: 'commercial', date: '2025-11-15', start: '14:00', end: '16:00', status: 'planifie', priority: 'normal' },
        { id: 10, title: 'Maintenance urgente', type: 'maintenance', service: 'maintenance', date: '2025-11-16', start: '13:00', end: '15:00', status: 'en_cours', priority: 'critique' },
        { id: 11, title: 'Formation équipe', type: 'formation', service: 'operations', date: '2025-11-17', start: '09:00', end: '17:00', status: 'planifie', priority: 'normal' },
        { id: 12, title: 'Réunion planning', type: 'rdv', service: 'administration', date: '2025-11-21', start: '10:00', end: '12:00', status: 'planifie', priority: 'normal' }
    ];

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay() + 1);

        $('#calendarTitle').text(firstDay.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' }));

        let html = '';

        for (let i = 0; i < 42; i++) {
            const currentDay = new Date(startDate);
            currentDay.setDate(startDate.getDate() + i);

            const isCurrentMonth = currentDay.getMonth() === month;
            const isToday = currentDay.toDateString() === new Date().toDateString();
            const dayEvents = events.filter(event => event.date === currentDay.toISOString().split('T')[0]);

            let classes = 'calendar-day';
            if (!isCurrentMonth) classes += ' empty';
            if (isToday) classes += ' today';
            if (dayEvents.length > 0) classes += ' has-events';

            html += `<div class="${classes}" data-date="${currentDay.toISOString().split('T')[0]}">`;

            if (isCurrentMonth) {
                html += `<div class="day-number">${currentDay.getDate()}</div>`;

                // Afficher les événements
                dayEvents.slice(0, 3).forEach(event => {
                    const eventClass = getEventClass(event.type);
                    html += `<div class="event-item ${eventClass}" data-event-id="${event.id}" title="${event.title} - ${event.start}">${event.title}</div>`;
                });

                if (dayEvents.length > 3) {
                    html += `<div class="more-events">+${dayEvents.length - 3} autres</div>`;
                }
            }

            html += '</div>';
        }

        $('#calendarDays').html(html);

        updateStats();
    }

    function getEventClass(type) {
        const classes = {
            'maintenance': 'event-maintenance',
            'operation': 'event-operation',
            'rdv': 'event-rdv',
            'formation': 'event-formation',
            'autre': 'event-autre'
        };
        return classes[type] || 'event-autre';
    }

    function updateStats() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const monthEvents = events.filter(event => {
            const eventDate = new Date(event.date);
            return eventDate.getFullYear() === year && eventDate.getMonth() === month;
        });

        $('#monthEventsCount').text(monthEvents.length);

        // Calculer les événements de la semaine en cours
        const today = new Date();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay() + 1);
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        const weekEvents = events.filter(event => {
            const eventDate = new Date(event.date);
            return eventDate >= startOfWeek && eventDate <= endOfWeek;
        });

        $('#weekEventsCount').text(weekEvents.length);

        // Événements d'aujourd'hui
        const todayEvents = events.filter(event => event.date === today.toISOString().split('T')[0]);
        $('#todayEventsCount').text(todayEvents.length);

        // Taux d'occupation (estimation basée sur les événements)
        const occupancyRate = Math.min(100, Math.round((monthEvents.length / 30) * 100));
        $('#occupancyRate').text(occupancyRate + '%');
    }

    // Gestion des clics sur les jours
    $(document).on('click', '.calendar-day:not(.empty)', function() {
        const date = $(this).data('date');
        selectedDate = date;

        $('.calendar-day').removeClass('selected');
        $(this).addClass('selected');

        showDayEvents(date);
    });

    // Gestion des clics sur les événements
    $(document).on('click', '.event-item', function(e) {
        e.stopPropagation();
        const eventId = $(this).data('event-id');
        showEventDetails(eventId);
    });

    function showDayEvents(date) {
        const dayEvents = events.filter(event => event.date === date);

        if (dayEvents.length > 0) {
            const dateObj = new Date(date);
            const formattedDate = dateObj.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            $('#selectedDate').text(formattedDate);

            let html = '';
            dayEvents.forEach(event => {
                const eventClass = getEventClass(event.type);
                const statusClass = getStatusClass(event.status);

                html += `
                    <div class="day-event-item ${eventClass}" data-event-id="${event.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${event.title}</h6>
                                <small class="text-muted">${event.start} - ${event.end}</small>
                                <br>
                                <small class="text-muted">${event.service} - ${event.type}</small>
                            </div>
                            <span class="badge ${statusClass}">${event.status}</span>
                        </div>
                    </div>
                `;
            });

            $('#dayEventsList').html(html);
            $('#dayEventsCard').show();
        } else {
            $('#dayEventsCard').hide();
        }
    }

    function getStatusClass(status) {
        const classes = {
            'planifie': 'bg-info',
            'en_cours': 'bg-warning',
            'termine': 'bg-success',
            'annule': 'bg-danger'
        };
        return classes[status] || 'bg-secondary';
    }

    function showEventDetails(eventId) {
        const event = events.find(e => e.id === eventId);
        if (!event) return;

        const eventClass = getEventClass(event.type);
        const statusClass = getStatusClass(event.status);

        const html = `
            <div class="event-details">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="${eventClass}">${event.title}</h4>
                        <div class="mb-3">
                            <span class="badge ${statusClass} me-2">${event.status}</span>
                            <span class="badge bg-secondary">${event.type}</span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Date:</strong><br>
                                ${new Date(event.date).toLocaleDateString('fr-FR')}
                            </div>
                            <div class="col-6">
                                <strong>Horaire:</strong><br>
                                ${event.start} - ${event.end}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Service:</strong><br>
                                ${event.service}
                            </div>
                            <div class="col-6">
                                <strong>Priorité:</strong><br>
                                ${event.priority}
                            </div>
                        </div>

                        <p><strong>Description:</strong> ${event.description || 'Aucune description'}</p>
                        <p><strong>Lieu:</strong> ${event.location || 'Non spécifié'}</p>
                        <p><strong>Participants:</strong> ${event.participants || 'Non spécifiés'}</p>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="event-icon-large ${eventClass}">
                                <i class="fas fa-calendar-check fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#eventDetailsContent').html(html);
        $('#modalDetailsEvenement').modal('show');
    }

    // Navigation du calendrier
    $('#btnPrevMonth').click(function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    $('#btnNextMonth').click(function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    $('#btnToday').click(function() {
        currentDate = new Date(2025, 10, 1); // Novembre 2025
        renderCalendar();
    });

    // Initialisation
    renderCalendar();
});
</script>

<style>
.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 50%;
}

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

.calendar-day.selected {
    background: linear-gradient(135deg, #cce7ff 0%, #f8f9fc 100%);
    border: 2px solid #007bff;
    box-shadow: 0 0 10px rgba(0,123,255,0.3);
}

.calendar-day.has-events {
    background: linear-gradient(135deg, #fff 0%, #f8f9fc 100%);
}

.day-number {
    font-weight: bold;
    color: #495057;
    margin-bottom: 5px;
}

.event-item {
    font-size: 0.75rem;
    padding: 2px 4px;
    margin-bottom: 2px;
    border-radius: 3px;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
}

.event-maintenance { background-color: #007bff; }
.event-operation { background-color: #28a745; }
.event-rdv { background-color: #ffc107; color: #212529; }
.event-formation { background-color: #17a2b8; }
.event-autre { background-color: #6c757d; }

.more-events {
    font-size: 0.7rem;
    color: #6c757d;
    font-style: italic;
    text-align: center;
    margin-top: 5px;
}

.day-event-item {
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
    cursor: pointer;
    transition: all 0.3s;
}

.day-event-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.event-icon-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    color: white;
}

.event-icon-large.event-maintenance { background-color: #007bff; }
.event-icon-large.event-operation { background-color: #28a745; }
.event-icon-large.event-rdv { background-color: #ffc107; color: #212529; }
.event-icon-large.event-formation { background-color: #17a2b8; }
.event-icon-large.event-autre { background-color: #6c757d; }
</style>
@endsection
