@extends('layouts.app')

@section('title', 'Planification des Contrôles | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-alt mr-2 text-primary"></i>Planification des Contrôles
            </h1>
            <p class="text-muted">Organisation et suivi des contrôles qualité et audits</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('controleur.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>Nouveau Contrôle
                </a>
                <button class="btn btn-outline-info">
                    <i class="fas fa-calendar mr-1"></i>Vue Calendrier
                </button>
                <button class="btn btn-outline-success">
                    <i class="fas fa-file-export mr-1"></i>Exporter Planning
                </button>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filtres
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select class="form-select">
                        <option value="month" selected>Ce mois</option>
                        <option value="quarter">Ce trimestre</option>
                        <option value="semester">Ce semestre</option>
                        <option value="year">Cette année</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type de contrôle</label>
                    <select class="form-select">
                        <option value="">Tous types</option>
                        <option value="qualite">Contrôle Qualité</option>
                        <option value="securite">Audit Sécurité</option>
                        <option value="conformite">Vérification Conformité</option>
                        <option value="technique">Validation Technique</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous statuts</option>
                        <option value="planifie">Planifié</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="annule">Annulé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Responsable</label>
                    <select class="form-select">
                        <option value="">Tous responsables</option>
                        <option>Jean Dupont</option>
                        <option>Marie Curie</option>
                        <option>Pierre Louis</option>
                        <option>Sophie Martin</option>
                        <option>Ahmed Benali</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Contrôles Planifiés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">23</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">Ce mois</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-plus fa-2x text-gray-300"></i>
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
                                Réalisés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">78% d'exécution</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                À Venir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">Cette semaine</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier des contrôles -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar mr-2"></i>Calendrier des Contrôles - Novembre 2025
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
                                <!-- Jours du calendrier avec contrôles -->
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day empty"></div>
                                <div class="calendar-day">1</div>
                                <div class="calendar-day">2</div>

                                <div class="calendar-day">3</div>
                                <div class="calendar-day">4</div>
                                <div class="calendar-day">5</div>
                                <div class="calendar-day event-day">
                                    6
                                    <div class="event-dot bg-primary" title="Audit Sécurité - Groupe électrogène A"></div>
                                </div>
                                <div class="calendar-day">7</div>
                                <div class="calendar-day">8</div>
                                <div class="calendar-day">9</div>

                                <div class="calendar-day event-day">
                                    10
                                    <div class="event-dot bg-success" title="Contrôle Qualité - Système HVAC"></div>
                                </div>
                                <div class="calendar-day">11</div>
                                <div class="calendar-day event-day">
                                    12
                                    <div class="event-dot bg-warning" title="Validation Technique - Ascenseur 1"></div>
                                </div>
                                <div class="calendar-day">13</div>
                                <div class="calendar-day">14</div>
                                <div class="calendar-day">15</div>

                                <div class="calendar-day">16</div>
                                <div class="calendar-day event-day">
                                    17
                                    <div class="event-dot bg-info" title="Vérification Conformité - Documentation"></div>
                                </div>
                                <div class="calendar-day">18</div>
                                <div class="calendar-day">19</div>
                                <div class="calendar-day">20</div>
                                <div class="calendar-day">21</div>
                                <div class="calendar-day">22</div>

                                <div class="calendar-day event-day">
                                    23
                                    <div class="event-dot bg-danger" title="Audit Critique - Sécurité incendie"></div>
                                </div>
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
                        <i class="fas fa-list mr-2"></i>Légende
                    </h6>
                </div>
                <div class="card-body">
                    <div class="legend-item mb-2">
                        <span class="legend-dot bg-primary"></span>
                        <span class="ms-2">Audit Sécurité</span>
                    </div>
                    <div class="legend-item mb-2">
                        <span class="legend-dot bg-success"></span>
                        <span class="ms-2">Contrôle Qualité</span>
                    </div>
                    <div class="legend-item mb-2">
                        <span class="legend-dot bg-warning"></span>
                        <span class="ms-2">Validation Technique</span>
                    </div>
                    <div class="legend-item mb-2">
                        <span class="legend-dot bg-info"></span>
                        <span class="ms-2">Vérification Conformité</span>
                    </div>
                    <div class="legend-item mb-2">
                        <span class="legend-dot bg-danger"></span>
                        <span class="ms-2">Audit Critique</span>
                    </div>

                    <hr>

                    <h6>Actions Rapides</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus-circle mr-1"></i>Ajouter Contrôle
                        </button>
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-calendar-check mr-1"></i>Marquer Réalisé
                        </button>
                        <button class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-clock mr-1"></i>Reporter
                        </button>
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-times mr-1"></i>Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste détaillée des contrôles -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Contrôles Planifiés (23)
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
                <table class="table table-bordered table-hover" id="controlesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Équipement/Service</th>
                            <th>Date Prévue</th>
                            <th>Responsable</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-danger">
                            <td>
                                <span class="badge bg-primary">CTL-2025-045</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">Sécurité</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">Groupe électrogène A</div>
                                <div class="text-muted small">Audit sécurité mensuel</div>
                            </td>
                            <td>2025-11-06</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white mr-2">JD</div>
                                    <span>Jean Dupont</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-danger">Critique</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">En retard</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Marquer réalisé">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Reporter">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge bg-primary">CTL-2025-046</span>
                            </td>
                            <td>
                                <span class="badge bg-success">Qualité</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">Système HVAC</div>
                                <div class="text-muted small">Contrôle qualité trimestriel</div>
                            </td>
                            <td>2025-11-10</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white mr-2">MC</div>
                                    <span>Marie Curie</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning">Haute</span>
                            </td>
                            <td>
                                <span class="badge bg-info">Planifié</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Commencer">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge bg-primary">CTL-2025-047</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">Technique</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">Ascenseur 1</div>
                                <div class="text-muted small">Validation technique annuelle</div>
                            </td>
                            <td>2025-11-12</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-info text-white mr-2">PL</div>
                                    <span>Pierre Louis</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">Moyenne</span>
                            </td>
                            <td>
                                <span class="badge bg-info">Planifié</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Commencer">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="badge bg-primary">CTL-2025-048</span>
                            </td>
                            <td>
                                <span class="badge bg-info">Conformité</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">Documentation qualité</div>
                                <div class="text-muted small">Vérification conformité normes</div>
                            </td>
                            <td>2025-11-17</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white mr-2">SM</div>
                                    <span>Sophie Martin</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">Moyenne</span>
                            </td>
                            <td>
                                <span class="badge bg-info">Planifié</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Commencer">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table-success">
                            <td>
                                <span class="badge bg-primary">CTL-2025-049</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">Sécurité</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">Système incendie</div>
                                <div class="text-muted small">Audit sécurité critique</div>
                            </td>
                            <td>2025-11-23</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-danger text-white mr-2">AB</div>
                                    <span>Ahmed Benali</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-danger">Critique</span>
                            </td>
                            <td>
                                <span class="badge bg-success">Terminé</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir Rapport">
                                        <i class="fas fa-file-alt"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Approuver">
                                        <i class="fas fa-thumbs-up"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
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

<!-- Modal Nouveau Contrôle -->
<div class="modal fade" id="modalNouveauControle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Planifier un Nouveau Contrôle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Type de contrôle</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="qualite">Contrôle Qualité</option>
                                <option value="securite">Audit Sécurité</option>
                                <option value="conformite">Vérification Conformité</option>
                                <option value="technique">Validation Technique</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priorité</label>
                            <select class="form-select">
                                <option value="faible">Faible</option>
                                <option value="moyenne" selected>Moyenne</option>
                                <option value="haute">Haute</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Équipement/Service concerné</label>
                            <input type="text" class="form-control" placeholder="Nom de l'équipement ou service" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date prévue</label>
                            <input type="date" class="form-control" required>
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
                        <div class="col-md-12">
                            <label class="form-label">Description/Objectifs</label>
                            <textarea class="form-control" rows="3" placeholder="Décrire les objectifs du contrôle..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Documents requis</label>
                            <input type="file" class="form-control" multiple accept=".pdf,.doc,.docx">
                            <div class="form-text">Procédures, checklists, normes de référence</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Planifier le Contrôle</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Initialisation de DataTables
    $('#controlesTable').DataTable({
        "pageLength": 10,
        "language": {
            "search": "Rechercher:",
            "lengthMenu": "Afficher _MENU_ éléments par page",
            "zeroRecords": "Aucun résultat trouvé",
            "info": "Page _PAGE_ sur _PAGES_",
            "infoEmpty": "Aucun élément disponible",
            "infoFiltered": "(filtré sur _MAX_ éléments au total)",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            }
        },
        "order": [[ 3, "asc" ]] // Trier par date prévue
    });

    // Animation des événements du calendrier
    $('.event-dot').hover(
        function() {
            $(this).addClass('pulse');
        },
        function() {
            $(this).removeClass('pulse');
        }
    );
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

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.table-success {
    background-color: rgba(28, 200, 138, 0.1) !important;
}
</style>
@endsection
