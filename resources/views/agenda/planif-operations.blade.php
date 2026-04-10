@extends('layouts.app')

@section('title', 'Planification Opérations | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list-check mr-2 text-primary"></i>Planification des Opérations
            </h1>
            <p class="text-muted">Organisation et suivi des tâches opérationnelles</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNouvelleOperation">
                    <i class="fas fa-plus mr-1"></i>Nouvelle Opération
                </button>
                <button class="btn btn-outline-info" id="btnVueKanban">
                    <i class="fas fa-columns mr-1"></i>Kanban
                </button>
                <button class="btn btn-outline-success" id="btnVuePlanning">
                    <i class="fas fa-calendar-alt mr-1"></i>Planning
                </button>
                <button class="btn btn-outline-warning">
                    <i class="fas fa-file-export mr-1"></i>Exporter
                </button>
            </div>
        </div>
    </div>

    <!-- Filtres et contrôles -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filtres et Recherche
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" id="filterStatus">
                        <option value="all">Tous statuts</option>
                        <option value="planifie">Planifié</option>
                        <option value="en_cours">En cours</option>
                        <option value="en_attente">En attente</option>
                        <option value="termine">Terminé</option>
                        <option value="annule">Annulé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Priorité</label>
                    <select class="form-select" id="filterPriority">
                        <option value="all">Toutes priorités</option>
                        <option value="faible">Faible</option>
                        <option value="normal">Normal</option>
                        <option value="important">Important</option>
                        <option value="urgent">Urgent</option>
                        <option value="critique">Critique</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Service</label>
                    <select class="form-select" id="filterService">
                        <option value="all">Tous services</option>
                        <option value="operations">Opérations</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="qualite">Qualité</option>
                        <option value="logistique">Logistique</option>
                        <option value="administration">Administration</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Titre, description...">
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Kanban -->
    <div id="kanbanView" class="kanban-view">
        <div class="kanban-container">
            <div class="kanban-column" data-status="planifie">
                <div class="kanban-header">
                    <h6 class="kanban-title">
                        <i class="fas fa-calendar-plus text-info mr-2"></i>Planifiées
                        <span class="badge bg-info" id="count-planifie">0</span>
                    </h6>
                </div>
                <div class="kanban-body" id="kanban-planifie">
                    <!-- Les cartes seront ajoutées dynamiquement -->
                </div>
            </div>

            <div class="kanban-column" data-status="en_cours">
                <div class="kanban-header">
                    <h6 class="kanban-title">
                        <i class="fas fa-play-circle text-primary mr-2"></i>En Cours
                        <span class="badge bg-primary" id="count-en_cours">0</span>
                    </h6>
                </div>
                <div class="kanban-body" id="kanban-en_cours">
                    <!-- Les cartes seront ajoutées dynamiquement -->
                </div>
            </div>

            <div class="kanban-column" data-status="en_attente">
                <div class="kanban-header">
                    <h6 class="kanban-title">
                        <i class="fas fa-clock text-warning mr-2"></i>En Attente
                        <span class="badge bg-warning" id="count-en_attente">0</span>
                    </h6>
                </div>
                <div class="kanban-body" id="kanban-en_attente">
                    <!-- Les cartes seront ajoutées dynamiquement -->
                </div>
            </div>

            <div class="kanban-column" data-status="termine">
                <div class="kanban-header">
                    <h6 class="kanban-title">
                        <i class="fas fa-check-circle text-success mr-2"></i>Terminées
                        <span class="badge bg-success" id="count-termine">0</span>
                    </h6>
                </div>
                <div class="kanban-body" id="kanban-termine">
                    <!-- Les cartes seront ajoutées dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <!-- Vue Planning (masquée par défaut) -->
    <div id="planningView" class="planning-view" style="display: none;">
        <div class="row">
            <div class="col-lg-9">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-week mr-2"></i>Planning Semaine - Du <span id="weekStart">04/11</span> au <span id="weekEnd">10/11</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="planning-grid">
                            <!-- En-têtes des jours -->
                            <div class="planning-header">
                                <div class="time-column">Heure</div>
                                <div class="day-column">Lundi<br><small>04/11</small></div>
                                <div class="day-column">Mardi<br><small>05/11</small></div>
                                <div class="day-column">Mercredi<br><small>06/11</small></div>
                                <div class="day-column">Jeudi<br><small>07/11</small></div>
                                <div class="day-column">Vendredi<br><small>08/11</small></div>
                                <div class="day-column">Samedi<br><small>09/11</small></div>
                                <div class="day-column">Dimanche<br><small>10/11</small></div>
                            </div>

                            <!-- Corps du planning -->
                            <div class="planning-body" id="planningBody">
                                <!-- Les lignes horaires seront générées par JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list mr-2"></i>Actions Rapides
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus-circle mr-1"></i>Ajouter opération
                            </button>
                            <button class="btn btn-outline-success btn-sm">
                                <i class="fas fa-check mr-1"></i>Marquer terminée
                            </button>
                            <button class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-clock mr-1"></i>Reporter
                            </button>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-copy mr-1"></i>Dupliquer
                            </button>
                        </div>

                        <hr>

                        <h6>Légende</h6>
                        <div class="legend-item">
                            <div class="legend-color bg-primary"></div>
                            <small>Maintenance</small>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-success"></div>
                            <small>Opérations</small>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-warning"></div>
                            <small>Qualité</small>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color bg-info"></div>
                            <small>Logistique</small>
                        </div>
                    </div>
                </div>
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
                                Total Opérations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOperations">24</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-check fa-2x text-gray-300"></i>
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
                                Terminées Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedToday">6</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="overdueTasks">2</div>
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
                                Taux d'Avancement</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="progressRate">78%</div>
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

<!-- Modal Nouvelle Opération -->
<div class="modal fade" id="modalNouvelleOperation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Planifier une Nouvelle Opération</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nouvelleOperationForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Titre de l'opération *</label>
                            <input type="text" class="form-control" placeholder="Ex: Contrôle qualité ligne A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type d'opération *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="controle">Contrôle qualité</option>
                                <option value="production">Production</option>
                                <option value="logistique">Logistique</option>
                                <option value="administratif">Administratif</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date de début *</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date d'échéance</label>
                            <input type="date" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Heure de début</label>
                            <input type="time" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Durée estimée (heures)</label>
                            <input type="number" class="form-control" placeholder="2" min="0.5" step="0.5">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Service responsable *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="operations">Opérations</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="qualite">Qualité</option>
                                <option value="logistique">Logistique</option>
                                <option value="administration">Administration</option>
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
                            <label class="form-label">Priorité</label>
                            <select class="form-select">
                                <option value="normal">Normal</option>
                                <option value="important">Important</option>
                                <option value="urgent">Urgent</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Équipement/Lieu</label>
                            <input type="text" class="form-control" placeholder="Machine A, Bureau 201, etc.">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description détaillée *</label>
                            <textarea class="form-control" rows="3" placeholder="Décrire les tâches à effectuer..." required></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Matériel nécessaire</label>
                            <input type="text" class="form-control" placeholder="Outils, pièces, documents requis">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Prérequis</label>
                            <textarea class="form-control" rows="2" placeholder="Conditions à respecter avant de commencer..."></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Documents joints</label>
                            <input type="file" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                            <div class="form-text">Procédures, schémas, spécifications</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Planifier l'Opération</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    let currentView = 'kanban';

    // Opérations de test
    const operations = [
        { id: 1, title: 'Contrôle qualité ligne A', type: 'controle', service: 'qualite', status: 'planifie', priority: 'important', date: '2025-11-04', start: '09:00', duration: 2, assignee: 'Marie Curie', description: 'Contrôle systématique des produits finis', location: 'Ligne A' },
        { id: 2, title: 'Maintenance générateur principal', type: 'maintenance', service: 'maintenance', status: 'en_cours', priority: 'critique', date: '2025-11-03', start: '08:00', duration: 4, assignee: 'Pierre Louis', description: 'Révision complète et test de fonctionnement', location: 'Salle technique' },
        { id: 3, title: 'Contrôle inventaire matières premières', type: 'logistique', service: 'logistique', status: 'en_attente', priority: 'normal', date: '2025-11-06', start: '14:00', duration: 3, assignee: 'Ahmed Benali', description: 'Vérification des stocks et mise à jour base de données', location: 'Entrepôt A' },
        { id: 4, title: 'Formation sécurité incendie', type: 'administratif', service: 'administration', status: 'planifie', priority: 'important', date: '2025-11-05', start: '10:00', duration: 2, assignee: 'Sophie Martin', description: 'Formation obligatoire pour tout le personnel', location: 'Salle de formation' },
        { id: 5, title: 'Réparation pompe hydraulique', type: 'maintenance', service: 'maintenance', status: 'termine', priority: 'urgent', date: '2025-11-02', start: '13:00', duration: 1.5, assignee: 'Pierre Louis', description: 'Remplacement joint d\'étanchéité défaillant', location: 'Atelier mécanique' },
        { id: 6, title: 'Audit documentation qualité', type: 'controle', service: 'qualite', status: 'planifie', priority: 'normal', date: '2025-11-07', start: '09:00', duration: 3, assignee: 'Marie Curie', description: 'Vérification conformité des procédures', location: 'Bureau qualité' }
    ];

    function renderKanban() {
        // Vider toutes les colonnes
        $('.kanban-body').empty();

        // Grouper par statut
        const groupedOperations = operations.reduce((acc, op) => {
            if (!acc[op.status]) acc[op.status] = [];
            acc[op.status].push(op);
            return acc;
        }, {});

        // Rendre chaque colonne
        Object.keys(groupedOperations).forEach(status => {
            const columnOps = groupedOperations[status];
            const columnId = `kanban-${status}`;

            columnOps.forEach(op => {
                const cardHtml = createOperationCard(op);
                $(`#${columnId}`).append(cardHtml);
            });

            // Mettre à jour le compteur
            $(`#count-${status}`).text(columnOps.length);
        });
    }

    function createOperationCard(operation) {
        const priorityClass = getPriorityClass(operation.priority);
        const typeClass = getTypeClass(operation.type);

        return `
            <div class="kanban-card" data-operation-id="${operation.id}">
                <div class="kanban-card-header">
                    <h6 class="kanban-card-title">${operation.title}</h6>
                    <span class="badge ${priorityClass} badge-sm">${operation.priority}</span>
                </div>
                <div class="kanban-card-body">
                    <p class="small text-muted mb-2">${operation.description}</p>
                    <div class="kanban-card-meta">
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i>${operation.assignee}<br>
                            <i class="fas fa-map-marker-alt mr-1"></i>${operation.location}<br>
                            <i class="fas fa-clock mr-1"></i>${operation.date} ${operation.start} (${operation.duration}h)
                        </small>
                    </div>
                </div>
                <div class="kanban-card-footer">
                    <span class="badge ${typeClass}">${operation.type}</span>
                </div>
            </div>
        `;
    }

    function getPriorityClass(priority) {
        const classes = {
            'faible': 'bg-secondary',
            'normal': 'bg-info',
            'important': 'bg-warning',
            'urgent': 'bg-danger',
            'critique': 'bg-danger'
        };
        return classes[priority] || 'bg-secondary';
    }

    function getTypeClass(type) {
        const classes = {
            'maintenance': 'bg-primary',
            'controle': 'bg-success',
            'production': 'bg-warning',
            'logistique': 'bg-info',
            'administratif': 'bg-secondary'
        };
        return classes[type] || 'bg-secondary';
    }

    function renderPlanning() {
        const planningBody = $('#planningBody');
        planningBody.empty();

        // Générer les lignes horaires (8h à 18h)
        for (let hour = 8; hour <= 18; hour++) {
            let rowHtml = `<div class="planning-row">
                <div class="time-column">${hour}:00</div>`;

            // Colonnes pour chaque jour de la semaine
            for (let day = 0; day < 7; day++) {
                rowHtml += '<div class="day-column"></div>';
            }

            rowHtml += '</div>';
            planningBody.append(rowHtml);
        }

        // Ajouter les opérations dans le planning
        operations.forEach(op => {
            if (op.start && op.duration) {
                const startHour = parseInt(op.start.split(':')[0]);
                const duration = op.duration;

                // Calculer la position (simplifié)
                if (startHour >= 8 && startHour <= 18) {
                    const dayIndex = new Date(op.date).getDay();
                    const rowIndex = startHour - 8;
                    const height = duration * 40; // 40px par heure

                    const eventHtml = `
                        <div class="planning-event ${getTypeClass(op.type)}" style="height: ${height}px; top: ${rowIndex * 40}px;">
                            <div class="event-content">
                                <strong>${op.title}</strong><br>
                                <small>${op.start} - ${op.assignee}</small>
                            </div>
                        </div>
                    `;

                    if (dayIndex >= 1 && dayIndex <= 7) { // Lundi = 1, Dimanche = 0 mais on le met à 7
                        const actualDayIndex = dayIndex === 0 ? 6 : dayIndex - 1;
                        const targetColumn = planningBody.find('.planning-row').eq(rowIndex).find('.day-column').eq(actualDayIndex);
                        targetColumn.append(eventHtml);
                    }
                }
            }
        });
    }

    function updateStats() {
        const total = operations.length;
        const completedToday = operations.filter(op => op.status === 'termine' && op.date === '2025-11-03').length;
        const overdue = operations.filter(op => new Date(op.date) < new Date() && op.status !== 'termine').length;
        const completed = operations.filter(op => op.status === 'termine').length;
        const progressRate = total > 0 ? Math.round((completed / total) * 100) : 0;

        $('#totalOperations').text(total);
        $('#completedToday').text(completedToday);
        $('#overdueTasks').text(overdue);
        $('#progressRate').text(progressRate + '%');
    }

    // Gestion des vues
    $('#btnVueKanban').click(function() {
        currentView = 'kanban';
        $('#kanbanView').show();
        $('#planningView').hide();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        renderKanban();
    });

    $('#btnVuePlanning').click(function() {
        currentView = 'planning';
        $('#kanbanView').hide();
        $('#planningView').show();
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        renderPlanning();
    });

    // Filtres
    $('#filterStatus, #filterPriority, #filterService').change(function() {
        // Implémentation du filtrage
        console.log('Filtre changé:', $(this).attr('id'), $(this).val());
    });

    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.kanban-card').each(function() {
            const title = $(this).find('.kanban-card-title').text().toLowerCase();
            const description = $(this).find('.kanban-card-body p').text().toLowerCase();
            const visible = title.includes(searchTerm) || description.includes(searchTerm);
            $(this).toggle(visible);
        });
    });

    // Initialisation
    renderKanban();
    updateStats();
});
</script>

<style>
.kanban-view {
    margin-top: 20px;
}

.kanban-container {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding: 20px 0;
}

.kanban-column {
    flex: 1;
    min-width: 300px;
    background: #f8f9fc;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.kanban-header {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    background: white;
    border-radius: 8px 8px 0 0;
}

.kanban-title {
    margin: 0;
    font-weight: 600;
    color: #495057;
}

.kanban-body {
    padding: 15px;
    min-height: 500px;
    max-height: 600px;
    overflow-y: auto;
}

.kanban-card {
    background: white;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 10px;
    border-left: 4px solid #007bff;
    cursor: pointer;
    transition: all 0.3s;
}

.kanban-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.kanban-card-header {
    padding: 12px 15px 8px;
    border-bottom: 1px solid #f8f9fc;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.kanban-card-title {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    flex: 1;
}

.kanban-card-body {
    padding: 10px 15px;
}

.kanban-card-meta {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #f8f9fc;
}

.kanban-card-footer {
    padding: 8px 15px 12px;
    border-top: 1px solid #f8f9fc;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.planning-view {
    margin-top: 20px;
}

.planning-grid {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.planning-header {
    display: grid;
    grid-template-columns: 80px repeat(7, 1fr);
    background: #f8f9fc;
    border-bottom: 1px solid #dee2e6;
}

.planning-header > div {
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    color: #495057;
    border-right: 1px solid #dee2e6;
}

.time-column {
    background: #e9ecef;
    font-weight: bold;
}

.day-column {
    min-width: 120px;
}

.planning-body {
    display: grid;
}

.planning-row {
    display: grid;
    grid-template-columns: 80px repeat(7, 1fr);
    border-bottom: 1px solid #f8f9fc;
}

.planning-row > div {
    padding: 8px;
    border-right: 1px solid #f8f9fc;
    position: relative;
    min-height: 40px;
}

.planning-event {
    position: absolute;
    left: 4px;
    right: 4px;
    border-radius: 4px;
    padding: 4px 8px;
    color: white;
    font-size: 0.8rem;
    z-index: 10;
    cursor: pointer;
    border: 1px solid rgba(255,255,255,0.3);
}

.planning-event .event-content {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
}
</style>
@endsection
