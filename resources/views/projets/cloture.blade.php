@extends('layouts.app')

@section('title', 'Clôture & Reporting - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-flag-checkered me-2 text-primary"></i>Clôture & Reporting Final
            </h1>
            <p class="text-muted mb-0">Validation finale et archivage des projets terminés</p>
        </div>
    </div>

    <!-- Projets à Clôturer -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h6 class="m-0 fw-bold">
                <i class="fas fa-exclamation-circle me-2"></i>Projets en Attente de Clôture
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>N° Projet</th>
                            <th>Client</th>
                            <th>Date Fin</th>
                            <th>Avancement</th>
                            <th>Budget</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>PRJ-2024-012</strong></td>
                            <td>SOLIBRA</td>
                            <td>05/11/2024</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                                <small class="text-success">100% terminé</small>
                            </td>
                            <td>8,500,000 FCFA</td>
                            <td>
                                <span class="badge bg-warning">En attente</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>PRJ-2024-010</strong></td>
                            <td>PALMCI</td>
                            <td>03/11/2024</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                                <small class="text-success">100% terminé</small>
                            </td>
                            <td>12,000,000 FCFA</td>
                            <td>
                                <span class="badge bg-warning">En attente</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulaire de Clôture -->
    <div class="row" id="formCloture" style="display: none;">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-file-signature me-2"></i>Clôture du Projet <span id="numProjetCloture">PRJ-2024-012</span>
                    </h6>
                </div>
                <div class="card-body">
                    <form>
                        <!-- Informations Projet -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Client:</strong> SOLIBRA<br>
                                    <strong>Contrat:</strong> <a href="{{ route('commercial.contrats.index') }}">CTR-2024-001</a><br>
                                    <strong>Trajet:</strong> Abidjan → Yamoussoukro
                                </div>
                                <div class="col-md-6">
                                    <strong>Date Début:</strong> 20/10/2024<br>
                                    <strong>Date Fin:</strong> 05/11/2024<br>
                                    <strong>Durée:</strong> 16 jours
                                </div>
                            </div>
                        </div>

                        <!-- Bilan Financier -->
                        <h6 class="fw-bold mb-3 mt-4">Bilan Financier</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Budget Initial (FCFA)</label>
                                <input type="number" class="form-control" value="8500000" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coûts Réels (FCFA) *</label>
                                <input type="number" class="form-control" id="coutsReels" value="7850000" oninput="calculerMarge()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Marge (FCFA)</label>
                                <input type="number" class="form-control" id="marge" readonly>
                            </div>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Détail des Coûts</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Carburant:</strong> 2,500,000 FCFA
                                        </div>
                                        <div class="mb-2">
                                            <strong>Personnel:</strong> 3,200,000 FCFA
                                        </div>
                                        <div class="mb-2">
                                            <strong>Péages & Frais:</strong> 450,000 FCFA
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Maintenance:</strong> 800,000 FCFA
                                        </div>
                                        <div class="mb-2">
                                            <strong>Assurances:</strong> 600,000 FCFA
                                        </div>
                                        <div class="mb-2">
                                            <strong>Divers:</strong> 300,000 FCFA
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>TOTAL:</strong>
                                    <strong class="text-primary">7,850,000 FCFA</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Bilan Opérationnel -->
                        <h6 class="fw-bold mb-3">Bilan Opérationnel</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Distance Totale (km)</label>
                                <input type="number" class="form-control" value="240">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nombre de Rotations</label>
                                <input type="number" class="form-control" value="8">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tonnage Transporté (T)</label>
                                <input type="number" class="form-control" value="160">
                            </div>
                        </div>

                        <!-- Satisfaction Client -->
                        <h6 class="fw-bold mb-3">Satisfaction Client</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Note Globale *</label>
                                <select class="form-select">
                                    <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                    <option value="4">⭐⭐⭐⭐ Très Bien</option>
                                    <option value="3">⭐⭐⭐ Bien</option>
                                    <option value="2">⭐⭐ Moyen</option>
                                    <option value="1">⭐ Insuffisant</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Retour Client</label>
                                <select class="form-select">
                                    <option>Très satisfait</option>
                                    <option>Satisfait</option>
                                    <option>Peu satisfait</option>
                                    <option>Insatisfait</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Commentaires Client</label>
                                <textarea class="form-control" rows="2" placeholder="Retours et observations du client..."></textarea>
                            </div>
                        </div>

                        <!-- Rapport Final -->
                        <h6 class="fw-bold mb-3">Rapport Final</h6>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Synthèse du Projet *</label>
                            <textarea class="form-control" rows="4" placeholder="Bilan global, points forts, axes d'amélioration...">Le projet s'est déroulé conformément au planning. Toutes les livraisons ont été effectuées dans les délais. Le client SOLIBRA se dit très satisfait de la prestation. Aucun incident majeur à signaler.</textarea>
                        </div>

                        <!-- Documents Finaux -->
                        <h6 class="fw-bold mb-3">Documents Finaux</h6>
                        <div class="list-group mb-4">
                            <div class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Bon de Livraison Final</strong> - Signé le 05/11/2024
                            </div>
                            <div class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Procès-Verbal de Réception</strong> - Validé
                            </div>
                            <div class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Facture Finale</strong> - <a href="{{ route('commercial.devis.index') }}">PRO-2024-045</a>
                            </div>
                            <div class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Rapports d'Exécution</strong> - <a href="{{ route('projets.rapports') }}">8 rapports</a>
                            </div>
                        </div>

                        <!-- Validation -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmCloture">
                            <label class="form-check-label" for="confirmCloture">
                                Je confirme que tous les documents sont complets et que le projet peut être clôturé définitivement.
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Indicateurs -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-chart-pie me-2"></i>Indicateurs de Performance
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Respect Délais</span>
                            <strong class="text-success">100%</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Respect Budget</span>
                            <strong class="text-success">92%</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 92%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Satisfaction Client</span>
                            <strong class="text-success">95%</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 95%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Rentabilité</span>
                            <strong class="text-success">7.6%</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 76%"></div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Transmission -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-share-nodes me-2"></i>Transmission Automatique
                    </h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        À la clôture, les données seront transmises vers :
                    </small>
                    <ul class="list-unstyled mt-2">
                        <li><i class="fas fa-check text-success me-2"></i>Comptabilité (coûts réels)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Reporting (KPIs)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Commercial (satisfaction)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Archives</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Projets Clôturés -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-archive me-2"></i>Projets Clôturés (Archives)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="cloturesTable">
                    <thead class="table-light">
                        <tr>
                            <th>N° Projet</th>
                            <th>Client</th>
                            <th>Date Clôture</th>
                            <th>Budget</th>
                            <th>Coûts Réels</th>
                            <th>Marge</th>
                            <th>Satisfaction</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>PRJ-2024-008</strong></td>
                            <td>SITARAIL</td>
                            <td>28/10/2024</td>
                            <td>18,500,000</td>
                            <td>17,200,000</td>
                            <td class="text-success fw-bold">+1,300,000</td>
                            <td>⭐⭐⭐⭐⭐</td>
                            <td>
                                <span class="badge bg-success">Terminé</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>PRJ-2024-005</strong></td>
                            <td>SOCOCE</td>
                            <td>15/10/2024</td>
                            <td>22,000,000</td>
                            <td>21,500,000</td>
                            <td class="text-success fw-bold">+500,000</td>
                            <td>⭐⭐⭐⭐</td>
                            <td>
                                <span class="badge bg-success">Terminé</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#cloturesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[2, 'desc']]
    });
});

function afficherCloture(numProjet) {
    document.getElementById('formCloture').style.display = 'block';
    document.getElementById('numProjetCloture').textContent = numProjet;
    document.getElementById('formCloture').scrollIntoView({ behavior: 'smooth' });
    calculerMarge();
}

function calculerMarge() {
    const budget = 8500000;
    const couts = parseFloat(document.getElementById('coutsReels')?.value) || 0;
    const marge = budget - couts;
    if (document.getElementById('marge')) {
        document.getElementById('marge').value = marge;
    }
}

function cloturerProjet() {
    if (!document.getElementById('confirmCloture').checked) {
        alert('Veuillez confirmer la clôture en cochant la case.');
        return;
    }

    if (confirm('Êtes-vous sûr de vouloir clôturer définitivement ce projet ?')) {
        alert('Projet clôturé avec succès ! Les données ont été transmises aux services concernés.');
        location.reload();
    }
}
</script>
@endsection
