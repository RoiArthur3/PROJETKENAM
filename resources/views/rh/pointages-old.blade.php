@extends('layouts.app')

@section('title', 'RH - Pointages | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock mr-2 text-primary"></i>Système de Pointage
            </h1>
            <p class="text-muted">Suivi des heures travaillées et gestion des présences</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <button class="btn btn-success" id="pointageBtn">
                    <i class="fas fa-play-circle mr-1"></i>Pointer l'arrivée
                </button>
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-download mr-1"></i>Exporter
                </button>
            </div>
        </div>
    </div>

    <!-- Cartes de synthèse -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Présents Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">38</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">90.5% de présence</span>
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
                                <span class="text-warning">Moyenne: 15 min</span>
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
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Absents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">4</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-danger">2 sans justification</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                                Heures Supp. Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">127h</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">+12h cette semaine</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes de conformité légale -->
    <div class="row mb-4">
        <div class="col">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-gavel mr-2"></i>
                <strong>Conformité légale - Côte d'Ivoire :</strong> Durée maximale légale de travail : 8h/jour, 40h/semaine (Code du Travail Art. 27-28).
                Pause déjeuner obligatoire : 1 heure minimum pour les journées > 6h.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>

    <!-- Mon pointage du jour -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-day mr-2"></i>Mon Pointage - Aujourd'hui (15 Janvier 2025)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="fas fa-play-circle fa-2x text-success"></i>
                            </div>
                            <div class="h6 mb-1">Arrivée</div>
                            <div class="text-success font-weight-bold">08:45</div>
                            <div class="small text-muted">À l'heure</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="fas fa-pause-circle fa-2x text-warning"></i>
                            </div>
                            <div class="h6 mb-1">Pause Déjeuner</div>
                            <div class="text-warning font-weight-bold">12:30 - 13:30</div>
                            <div class="small text-muted">1h00</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="fas fa-stop-circle fa-2x text-danger"></i>
                            </div>
                            <div class="h6 mb-1">Départ</div>
                            <div class="text-danger font-weight-bold">17:15</div>
                            <div class="small text-muted">+15 min</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <i class="fas fa-calculator fa-2x text-primary"></i>
                            </div>
                            <div class="h6 mb-1">Total Aujourd'hui</div>
                            <div class="text-primary font-weight-bold">8h15</div>
                            <div class="small text-muted">Heures travaillées</div>
                            <div class="small text-warning mt-1">
                                <i class="fas fa-exclamation-triangle"></i> +15 min (H.S.)
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-success">Pointage actif</span>
                            <span class="text-muted ms-2">Dernière activité: il y a 2 minutes</span>
                        </div>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-stop mr-1"></i>Terminer la journée
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filtres et Recherche
            </h6>
        </div>
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Agent</label>
                    <select class="form-select">
                        <option value="">Tous les agents</option>
                        <option>Jean Dupont</option>
                        <option>Marie Curie</option>
                        <option>Pierre Louis</option>
                        <option>Sophie Martin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" class="form-control" value="2025-01-01">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" class="form-control" value="2025-01-15">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option>Présent</option>
                        <option>En retard</option>
                        <option>Absent</option>
                        <option>Congé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>Filtrer
                        </button>
                        <button type="button" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-1"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Historique des pointages -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history mr-2"></i>Historique des Pointages
            </h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary active">
                    <i class="fas fa-list"></i>
                </button>
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-calendar"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="pointagesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            <th>Date</th>
                            <th>Arrivée</th>
                            <th>Pause Déj.</th>
                            <th>Départ</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Conformité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white mr-3">JD</div>
                                    <div>
                                        <div class="font-weight-bold">Jean Dupont</div>
                                        <div class="text-muted small">Logistique</div>
                                    </div>
                                </div>
                            </td>
                            <td>Aujourd'hui</td>
                            <td><span class="badge bg-success">08:45</span></td>
                            <td>12:30 - 13:30</td>
                            <td><span class="badge bg-danger">17:15</span></td>
                            <td><span class="font-weight-bold">8h15</span></td>
                            <td><span class="badge bg-success">Présent</span></td>
                            <td>
                                <span class="badge bg-warning" title="Heures supplémentaires détectées">
                                    <i class="fas fa-exclamation-triangle"></i> H.S.
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white mr-3">MC</div>
                                    <div>
                                        <div class="font-weight-bold">Marie Curie</div>
                                        <div class="text-muted small">Entretien</div>
                                    </div>
                                </div>
                            </td>
                            <td>Aujourd'hui</td>
                            <td><span class="badge bg-warning">09:15</span></td>
                            <td>12:00 - 13:00</td>
                            <td><span class="badge bg-success">17:00</span></td>
                            <td><span class="font-weight-bold">7h45</span></td>
                            <td><span class="badge bg-warning">Retard</span></td>
                            <td>
                                <span class="badge bg-success" title="Conforme aux normes ivoiriennes">
                                    <i class="fas fa-check-circle"></i> OK
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white mr-3">PL</div>
                                    <div>
                                        <div class="font-weight-bold">Pierre Louis</div>
                                        <div class="text-muted small">Commercial</div>
                                    </div>
                                </div>
                            </td>
                            <td>14 Jan</td>
                            <td><span class="badge bg-success">08:30</span></td>
                            <td>12:15 - 13:15</td>
                            <td><span class="badge bg-success">17:30</span></td>
                            <td><span class="font-weight-bold">8h45</span></td>
                            <td><span class="badge bg-success">Présent</span></td>
                            <td>
                                <span class="badge bg-warning" title="Heures supplémentaires détectées">
                                    <i class="fas fa-exclamation-triangle"></i> H.S.
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-danger text-white mr-3">SM</div>
                                    <div>
                                        <div class="font-weight-bold">Sophie Martin</div>
                                        <div class="text-muted small">Administration</div>
                                    </div>
                                </div>
                            </td>
                            <td>14 Jan</td>
                            <td><span class="badge bg-gray">Absent</span></td>
                            <td>-</td>
                            <td><span class="badge bg-gray">Absent</span></td>
                            <td><span class="font-weight-bold">0h00</span></td>
                            <td><span class="badge bg-danger">Absent</span></td>
                            <td>
                                <span class="badge bg-danger" title="Absence non justifiée">
                                    <i class="fas fa-times-circle"></i> Absence
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Justificatif">
                                        <i class="fas fa-file-alt"></i>
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

<!-- Styles personnalisés -->
<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 11px;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}

.badge {
    font-size: 0.75em;
}
</style>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Initialisation de DataTables si nécessaire
    $('#pointagesTable').DataTable({
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
        }
    });

    // Charger le statut du pointage actuel
    loadTodayStatus();

    // Gestion du bouton de pointage
    $('#pointageBtn').click(function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        // Désactiver le bouton pendant la requête
        $btn.prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Traitement...');
        
        $.ajax({
            url: '{{ route("rh.pointage.check-in-out") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Afficher la notification de succès
                    showNotification(response.message, 'success');
                    
                    // Recharger le statut
                    loadTodayStatus();
                    
                    // Mettre à jour l'affichage du pointage du jour
                    updateTodayDisplay(response);
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Une erreur est survenue lors du pointage.';
                showNotification(message, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    function loadTodayStatus() {
        $.ajax({
            url: '{{ route("rh.pointage.today-status") }}',
            method: 'GET',
            success: function(data) {
                updatePointageButton(data);
                updateTodayCard(data);
            }
        });
    }

    function updatePointageButton(data) {
        const $btn = $('#pointageBtn');
        
        if (!data.has_checked_in) {
            $btn.removeClass('btn-danger btn-warning').addClass('btn-success');
            $btn.html('<i class="fas fa-play-circle mr-1"></i>Pointer l\'arrivée');
        } else if (!data.has_checked_out) {
            $btn.removeClass('btn-success btn-warning').addClass('btn-danger');
            $btn.html('<i class="fas fa-stop-circle mr-1"></i>Pointer le départ');
        } else {
            $btn.removeClass('btn-success btn-danger').addClass('btn-warning');
            $btn.html('<i class="fas fa-check-circle mr-1"></i>Journée terminée');
            $btn.prop('disabled', true);
        }
    }

    function updateTodayCard(data) {
        const todayCard = $('.card:contains("Mon Pointage - Aujourd\'hui")').find('.card-body');
        const now = new Date();
        const today = now.toLocaleDateString('fr-FR');
        
        // Mettre à jour la date
        todayCard.find('h6').text(`Mon Pointage - Aujourd'hui (${today})`);
        
        // Mettre à jour les informations d'arrivée
        if (data.has_checked_in) {
            todayCard.find('.col-md-3').eq(0).find('.text-success').text(data.check_in_time || '--:--');
        }
        
        // Mettre à jour les informations de départ
        if (data.has_checked_out) {
            todayCard.find('.col-md-3').eq(2).find('.text-danger').text(data.check_out_time || '--:--');
        }
        
        // Calculer et afficher le total
        if (data.has_checked_in && data.has_checked_out && data.check_in_time && data.check_out_time) {
            const total = calculateTotalHours(data.check_in_time, data.check_out_time);
            todayCard.find('.col-md-3').eq(3).find('.text-primary').text(total);
        }
        
        // Mettre à jour le badge de statut
        const statusBadge = todayCard.find('.badge').first();
        if (data.has_checked_out) {
            statusBadge.removeClass('bg-success bg-warning').addClass('bg-info').text('Journée terminée');
        } else if (data.has_checked_in) {
            statusBadge.removeClass('bg-info bg-warning').addClass('bg-success').text('Pointage actif');
        } else {
            statusBadge.removeClass('bg-success bg-info').addClass('bg-warning').text('Non pointé');
        }
    }

    function updateTodayDisplay(response) {
        // Cette fonction peut être utilisée pour mettre à jour l'affichage après un pointage
        loadTodayStatus(); // Recharger simplement le statut
    }

    function calculateTotalHours(checkIn, checkOut) {
        const [checkInHour, checkInMin] = checkIn.split(':').map(Number);
        const [checkOutHour, checkOutMin] = checkOut.split(':').map(Number);
        
        const checkInMinutes = checkInHour * 60 + checkInMin;
        const checkOutMinutes = checkOutHour * 60 + checkOutMin;
        
        const totalMinutes = checkOutMinutes - checkInMinutes;
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        
        return `${hours}h${minutes.toString().padStart(2, '0')}`;
    }

    function showNotification(message, type) {
        // Créer une notification temporaire
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas fa-${icon} mr-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-dismiss après 5 secondes
        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, 5000);
    }
});
</script>
@endsection
