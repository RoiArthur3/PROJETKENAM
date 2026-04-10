@extends('layouts.app')

@section('title', 'Planification Entretiens | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tools mr-2 text-primary"></i>Planification des Entretiens
            </h1>
            <p class="text-muted">Maintenance préventive et curative des équipements</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNouvelEntretien">
                    <i class="fas fa-plus mr-1"></i>Nouvel Entretien
                </button>
                <button class="btn btn-outline-info" id="btnVueCalendrier">
                    <i class="fas fa-calendar mr-1"></i>Calendrier
                </button>
                <button class="btn btn-outline-success" id="btnVueEquipements">
                    <i class="fas fa-cogs mr-1"></i>Équipements
                </button>
                <button class="btn btn-outline-warning">
                    <i class="fas fa-file-export mr-1"></i>Exporter Planning
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
                    <label class="form-label">Type d'entretien</label>
                    <select class="form-select" id="filterType">
                        <option value="all">Tous types</option>
                        <option value="preventif">Préventif</option>
                        <option value="curatif">Curatif</option>
                        <option value="predictif">Prédictif</option>
                        <option value="correctif">Correctif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Équipement</label>
                    <select class="form-select" id="filterEquipement">
                        <option value="all">Tous équipements</option>
                        <option value="generateur">Générateur</option>
                        <option value="ascenseur">Ascenseur</option>
                        <option value="climatisation">Climatisation</option>
                        <option value="electrique">Électrique</option>
                        <option value="mecanique">Mécanique</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" id="filterStatut">
                        <option value="all">Tous statuts</option>
                        <option value="planifie">Planifié</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="annule">Annulé</option>
                        <option value="en_retard">En retard</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Urgence</label>
                    <select class="form-select" id="filterUrgence">
                        <option value="all">Tous niveaux</option>
                        <option value="faible">Faible</option>
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                        <option value="critique">Critique</option>
                    </select>
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
                            <i class="fas fa-calendar-alt mr-2"></i>Calendrier des Entretiens - Novembre 2025
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
                        <div id="todayMaintenances">
                            <!-- Entretiens du jour affichés ici -->
                        </div>
                    </div>
                </div>

                <!-- Prochains entretiens -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-forward mr-2"></i>Prochains (7 jours)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="upcomingMaintenances">
                            <!-- Prochains entretiens affichés ici -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Équipements (masquée par défaut) -->
    <div id="equipementsView" style="display: none;">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs mr-2"></i>État des Équipements
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row" id="equipementsGrid">
                            <!-- Équipements affichés ici -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste détaillée des entretiens -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Entretiens Programmés (28)
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
                <table class="table table-hover" id="entretiensTable">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Équipement</th>
                            <th>Type</th>
                            <th>Date Prévue</th>
                            <th>Responsable</th>
                            <th>Durée Estimée</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="entretiensTableBody">
                        <!-- Les entretiens seront ajoutés dynamiquement -->
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

    <!-- Statistiques -->
    <div class="row mt-4">
        <div class="col-lg-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Entretiens Préventifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">64% du total</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
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
                                Réalisés ce Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">+3 vs mois dernier</span>
                            </div>
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
                                En Retard</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">4</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-warning">Nécessitent action</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Taux de Disponibilité</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">96%</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">Objectif: 98%</span>
                            </div>
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

<!-- Modal Nouvel Entretien -->
<div class="modal fade" id="modalNouvelEntretien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Planifier un Nouvel Entretien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nouvelEntretienForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Équipement *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner un équipement</option>
                                <option value="generateur-principal">Générateur Principal</option>
                                <option value="ascenseur-1">Ascenseur 1</option>
                                <option value="climatisation-principale">Climatisation Principale</option>
                                <option value="groupe-electrogene">Groupe Électrogène</option>
                                <option value="tableau-electrique">Tableau Électrique</option>
                                <option value="compresseur">Compresseur</option>
                                <option value="chaudiere">Chaudière</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type d'entretien *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner le type</option>
                                <option value="preventif">Préventif</option>
                                <option value="curatif">Curatif</option>
                                <option value="predictif">Prédictif</option>
                                <option value="correctif">Correctif</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date prévue *</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Heure de début</label>
                            <input type="time" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Durée estimée (heures)</label>
                            <input type="number" class="form-control" placeholder="2" min="0.5" step="0.5">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Technicien responsable *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option>Jean Dupont</option>
                                <option>Pierre Louis</option>
                                <option>Marc André</option>
                                <option>Thomas Bernard</option>
                            </select>
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
                            <label class="form-label">Description des travaux *</label>
                            <textarea class="form-control" rows="3" placeholder="Décrire les opérations à effectuer..." required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pièces nécessaires</label>
                            <textarea class="form-control" rows="2" placeholder="Lister les pièces de rechange..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Outils requis</label>
                            <textarea class="form-control" rows="2" placeholder="Lister les outils nécessaires..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Coût estimé (FCFA)</label>
                            <input type="number" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Arrêt de production nécessaire</label>
                            <select class="form-select">
                                <option value="non">Non</option>
                                <option value="partiel">Partiel</option>
                                <option value="total">Total</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Notes et recommandations</label>
                            <textarea class="form-control" rows="2" placeholder="Observations, recommandations particulières..."></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Documents de référence</label>
                            <input type="file" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                            <div class="form-text">Manuels, schémas, procédures d'entretien</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Planifier l'Entretien</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    let currentDate = new Date(2025, 10, 1); // Novembre 2025

    // Données des entretiens
    const entretiens = [
        { id: 1, reference: 'ENT-2025-045', equipement: 'Générateur Principal', type: 'preventif', date: '2025-11-03', heure: '08:00', duree: 4, responsable: 'Pierre Louis', priorite: 'important', statut: 'planifie', description: 'Maintenance trimestrielle complète' },
        { id: 2, reference: 'ENT-2025-046', equipement: 'Ascenseur 1', type: 'preventif', date: '2025-11-05', heure: '14:00', duree: 2, responsable: 'Jean Dupont', priorite: 'normal', statut: 'planifie', description: 'Contrôle sécurité et lubrification' },
        { id: 3, reference: 'ENT-2025-047', equipement: 'Climatisation Principale', type: 'curatif', date: '2025-11-07', heure: '09:00', duree: 6, responsable: 'Pierre Louis', priorite: 'urgent', statut: 'en_cours', description: 'Réparation fuite réfrigérant' },
        { id: 4, reference: 'ENT-2025-048', equipement: 'Groupe Électrogène', type: 'preventif', date: '2025-11-10', heure: '08:00', duree: 3, responsable: 'Marc André', priorite: 'important', statut: 'planifie', description: 'Test de charge et vérification' },
        { id: 5, reference: 'ENT-2025-049', equipement: 'Tableau Électrique', type: 'preventif', date: '2025-11-12', heure: '10:00', duree: 2, responsable: 'Thomas Bernard', priorite: 'normal', statut: 'termine', description: 'Contrôle connexions et sécurité' },
        { id: 6, reference: 'ENT-2025-050', equipement: 'Compresseur', type: 'correctif', date: '2025-11-15', heure: '13:00', duree: 1.5, responsable: 'Pierre Louis', priorite: 'critique', statut: 'en_retard', description: 'Remplacement courroie défaillante' }
    ];

    // Équipements avec leur état
    const equipements = [
        { id: 1, nom: 'Générateur Principal', categorie: 'Électrique', etat: 'bon', dernierEntretien: '2025-10-15', prochainEntretien: '2025-11-03', criticite: 'haute' },
        { id: 2, nom: 'Ascenseur 1', categorie: 'Mécanique', etat: 'moyen', dernierEntretien: '2025-09-20', prochainEntretien: '2025-11-05', criticite: 'haute' },
        { id: 3, nom: 'Climatisation Principale', categorie: 'HVAC', etat: 'critique', dernierEntretien: '2025-08-10', prochainEntretien: '2025-11-07', criticite: 'moyenne' },
        { id: 4, nom: 'Groupe Électrogène', categorie: 'Électrique', etat: 'bon', dernierEntretien: '2025-09-01', prochainEntretien: '2025-11-10', criticite: 'haute' },
        { id: 5, nom: 'Tableau Électrique', categorie: 'Électrique', etat: 'bon', dernierEntretien: '2025-11-12', prochainEntretien: '2026-02-12', criticite: 'haute' },
        { id: 6, nom: 'Compresseur', categorie: 'Mécanique', etat: 'mauvais', dernierEntretien: '2025-10-01', prochainEntretien: '2025-11-15', criticite: 'moyenne' }
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
            const dayEntretiens = entretiens.filter(ent => ent.date === currentDay.toISOString().split('T')[0]);

            let classes = 'calendar-day';
            if (!isCurrentMonth) classes += ' empty';
            if (isToday) classes += ' today';
            if (dayEntretiens.length > 0) classes += ' has-maintenance';

            html += `<div class="${classes}" data-date="${currentDay.toISOString().split('T')[0]}">`;

            if (isCurrentMonth) {
                html += `<div class="day-number">${currentDay.getDate()}</div>`;

                // Afficher les entretiens
                dayEntretiens.slice(0, 2).forEach(ent => {
                    const typeClass = getMaintenanceTypeClass(ent.type);
                    const priorityDot = getPriorityDot(ent.priorite);
                    html += `<div class="maintenance-item ${typeClass}" title="${ent.equipement} - ${ent.description}">${priorityDot} ${ent.equipement}</div>`;
                });

                if (dayEntretiens.length > 2) {
                    html += `<div class="more-maintenance">+${dayEntretiens.length - 2} autres</div>`;
                }
            }

            html += '</div>';
        }

        $('#calendarDays').html(html);

        // Afficher les entretiens du jour
        renderTodayEntretiens();

        // Afficher les prochains entretiens
        renderUpcomingEntretiens();
    }

    function renderTodayEntretiens() {
        const today = new Date().toISOString().split('T')[0];
        const todayEntretiens = entretiens.filter(ent => ent.date === today);

        let html = '';
        if (todayEntretiens.length === 0) {
            html = '<p class="text-muted small">Aucun entretien prévu aujourd\'hui</p>';
        } else {
            todayEntretiens.forEach(ent => {
                const typeClass = getMaintenanceTypeClass(ent.type);
                const statusClass = getStatusClass(ent.statut);
                html += `
                    <div class="maintenance-today-item ${typeClass}">
                        <h6 class="mb-1">${ent.equipement}</h6>
                        <p class="small mb-1">${ent.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${ent.heure} - ${ent.duree}h</small>
                            <span class="badge ${statusClass}">${ent.statut}</span>
                        </div>
                    </div>
                `;
            });
        }

        $('#todayMaintenances').html(html);
    }

    function renderUpcomingEntretiens() {
        const today = new Date();
        const nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);

        const upcomingEntretiens = entretiens.filter(ent => {
            const entDate = new Date(ent.date);
            return entDate >= today && entDate <= nextWeek && ent.statut !== 'termine';
        }).slice(0, 5);

        let html = '';
        if (upcomingEntretiens.length === 0) {
            html = '<p class="text-muted small">Aucun entretien prévu dans les 7 jours</p>';
        } else {
            upcomingEntretiens.forEach(ent => {
                const typeClass = getMaintenanceTypeClass(ent.type);
                const daysUntil = Math.ceil((new Date(ent.date) - today) / (1000 * 60 * 60 * 24));
                html += `
                    <div class="upcoming-item ${typeClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong class="small">${ent.equipement}</strong>
                                <br>
                                <small class="text-muted">${ent.date} - ${ent.heure}</small>
                            </div>
                            <span class="badge bg-secondary">${daysUntil}j</span>
                        </div>
                    </div>
                `;
            });
        }

        $('#upcomingMaintenances').html(html);
    }

    function renderEquipementsView() {
        let html = '';

        equipements.forEach(equip => {
            const etatClass = getEquipementEtatClass(equip.etat);
            const criticiteBadge = getCriticiteBadge(equip.criticite);

            html += `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card equipement-card ${etatClass}">
                        <div class="card-header py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">${equip.nom}</h6>
                                ${criticiteBadge}
                            </div>
                            <small class="text-muted">${equip.categorie}</small>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="small text-muted">Dernier</div>
                                    <div class="font-weight-bold">${formatDate(equip.dernierEntretien)}</div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-muted">Prochain</div>
                                    <div class="font-weight-bold">${formatDate(equip.prochainEntretien)}</div>
                                </div>
                            </div>
                            <hr>
                            <div class="equipement-status">
                                <span class="badge ${getEquipementEtatBadge(equip.etat)}">${equip.etat.toUpperCase()}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#equipementsGrid').html(html);
    }

    function renderEntretiensTable() {
        let html = '';

        entretiens.forEach(ent => {
            const typeClass = getMaintenanceTypeClass(ent.type || 'preventif');
            const statusClass = getStatusClass(ent.statut || 'planifie');
            const priorityBadge = getPriorityBadge(ent.priorite || 'normal');

            html += `
                <tr>
                    <td><span class="badge bg-primary">${ent.reference || 'ENT-XXXX'}</span></td>
                    <td>
                        <div class="font-weight-bold">${ent.equipement || 'Équipement non défini'}</div>
                        <div class="text-muted small">${ent.description || 'Aucune description'}</div>
                    </td>
                    <td><span class="badge ${typeClass}">${ent.type || 'preventif'}</span></td>
                    <td>${ent.date || 'Date non définie'} ${ent.heure || ''}</td>
                    <td>${ent.responsable || 'Non assigné'}</td>
                    <td>${ent.duree || '—'}h</td>
                    <td>${priorityBadge}</td>
                    <td><span class="badge ${statusClass}">${ent.statut || 'planifie'}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-success" title="Marquer terminé">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#entretiensTableBody').html(html);

        // Réinitialiser DataTable si elle existe déjà
        if ($.fn.DataTable.isDataTable('#entretiensTable')) {
            $('#entretiensTable').DataTable().destroy();
        }

        // Initialiser DataTable avec les bonnes options
        $('#entretiensTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
            },
            order: [[3, 'asc']], // Trier par date
            pageLength: 10,
            responsive: true,
            columnDefs: [
                { orderable: false, targets: 8 } // Désactiver le tri sur la colonne Actions
            ]
        });
    }

    function getMaintenanceTypeClass(type) {
        const classes = {
            'preventif': 'maintenance-preventif',
            'curatif': 'maintenance-curatif',
            'predictif': 'maintenance-predictif',
            'correctif': 'maintenance-correctif'
        };
        return classes[type] || 'maintenance-autre';
    }

    function getStatusClass(status) {
        const classes = {
            'planifie': 'bg-info',
            'en_cours': 'bg-warning',
            'termine': 'bg-success',
            'annule': 'bg-secondary',
            'en_retard': 'bg-danger'
        };
        return classes[status] || 'bg-secondary';
    }

    function getPriorityDot(priority) {
        const dots = {
            'faible': '⚪',
            'normal': '🟡',
            'important': '🟠',
            'urgent': '🔴',
            'critique': '🔴'
        };
        return dots[priority] || '⚪';
    }

    function getPriorityBadge(priority) {
        const badges = {
            'faible': '<span class="badge bg-light text-dark">Faible</span>',
            'normal': '<span class="badge bg-info">Normal</span>',
            'important': '<span class="badge bg-warning">Important</span>',
            'urgent': '<span class="badge bg-danger">Urgent</span>',
            'critique': '<span class="badge bg-danger">Critique</span>'
        };
        return badges[priority] || '<span class="badge bg-secondary">Normal</span>';
    }

    function getEquipementEtatClass(etat) {
        const classes = {
            'bon': 'equipement-bon',
            'moyen': 'equipement-moyen',
            'mauvais': 'equipement-mauvais',
            'critique': 'equipement-critique'
        };
        return classes[etat] || 'equipement-inconnu';
    }

    function getEquipementEtatBadge(etat) {
        const badges = {
            'bon': 'bg-success',
            'moyen': 'bg-warning',
            'mauvais': 'bg-danger',
            'critique': 'bg-danger'
        };
        return badges[etat] || 'bg-secondary';
    }

    function getCriticiteBadge(criticite) {
        const badges = {
            'faible': '<span class="badge bg-success">Faible</span>',
            'moyenne': '<span class="badge bg-warning">Moyenne</span>',
            'haute': '<span class="badge bg-danger">Haute</span>'
        };
        return badges[criticite] || '<span class="badge bg-secondary">Moyenne</span>';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR');
    }

    // Gestion des vues
    $('#btnVueCalendrier').click(function() {
        $('#calendrierView').show();
        $('#equipementsView').hide();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        renderCalendrier();
    });

    $('#btnVueEquipements').click(function() {
        $('#calendrierView').hide();
        $('#equipementsView').show();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        renderEquipementsView();
    });

    // Initialisation
    renderCalendrier();
    renderEntretiensTable();
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

.calendar-day.has-maintenance {
    background: linear-gradient(135deg, #fff 0%, #f8f9fc 100%);
}

.day-number {
    font-weight: bold;
    color: #495057;
    margin-bottom: 5px;
}

.maintenance-item {
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

.maintenance-preventif { background-color: #007bff; }
.maintenance-curatif { background-color: #28a745; }
.maintenance-predictif { background-color: #17a2b8; }
.maintenance-correctif { background-color: #ffc107; color: #212529; }

.more-maintenance {
    font-size: 0.7rem;
    color: #6c757d;
    font-style: italic;
    text-align: center;
    margin-top: 5px;
}

.maintenance-today-item {
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
}

.upcoming-item {
    padding: 8px;
    margin-bottom: 6px;
    border-radius: 4px;
    border-left: 3px solid #007bff;
    background: #f8f9fc;
}

.equipement-card {
    border-left: 4px solid #007bff;
}

.equipement-bon { border-left-color: #28a745; }
.equipement-moyen { border-left-color: #ffc107; }
.equipement-mauvais { border-left-color: #fd7e14; }
.equipement-critique { border-left-color: #dc3545; }

.equipement-card .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid #dee2e6;
}
</style>
@endsection
