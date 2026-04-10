@extends('layouts.app')

@section('title', 'Prospects & Clients - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!empty($dbError))
        <div class="alert alert-warning alert-dismissible fade show mb-4">
            {{ $dbError }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2 text-primary"></i>Prospects & Clients
            </h1>
            <p class="text-muted mb-0">Gestion de la base clients et prospects en Côte d'Ivoire</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nouveauProspectModal">
            <i class="fas fa-plus me-2"></i>Nouveau Prospect
        </button>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Statut</label>
                    <select class="form-select">
                        <option>Tous</option>
                        <option>Prospect</option>
                        <option>Client actif</option>
                        <option>Client inactif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Ville</label>
                    <select class="form-select">
                        <option>Toutes</option>
                        <option>Abidjan</option>
                        <option>Yamoussoukro</option>
                        <option>Bouaké</option>
                        <option>San-Pédro</option>
                        <option>Korhogo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Secteur d'activité</label>
                    <select class="form-select">
                        <option>Tous</option>
                        <option>Industrie</option>
                        <option>Commerce</option>
                        <option>BTP</option>
                        <option>Agroalimentaire</option>
                        <option>Services</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Rechercher
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Total Prospects</div>
                    <div class="h3 mb-0 text-primary">45</div>
                    <small class="text-muted">Dont 12 à Abidjan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Clients Actifs</div>
                    <div class="h3 mb-0 text-success">128</div>
                    <small class="text-muted">Sur toute la CI</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Négociation</div>
                    <div class="h3 mb-0 text-warning">18</div>
                    <small class="text-muted">Valeur: 45M FCFA</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">CA Total</div>
                    <div class="h3 mb-0 text-info">385M</div>
                    <small class="text-muted">FCFA cette année</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Prospects & Clients
            </h6>
            <button class="btn btn-sm btn-success">
                <i class="fas fa-file-excel me-2"></i>Exporter
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="prospectsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Raison Sociale</th>
                            <th>Contact</th>
                            <th>Téléphone</th>
                            <th>Ville</th>
                            <th>Secteur</th>
                            <th>Statut</th>
                            <th>CA Potentiel</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>SOCIETE IVOIRIENNE DE TRANSPORT (SIT)</strong></td>
                            <td>Kouassi Jean-Marc</td>
                            <td>+225 07 XX XX XX XX</td>
                            <td>Abidjan, Plateau</td>
                            <td><span class="badge bg-secondary">Transport</span></td>
                            <td><span class="badge bg-success">Client actif</span></td>
                            <td class="fw-bold text-success">8.5M FCFA</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="voirProspect(1)"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning" onclick="editerProspect(1)"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>GROUPE PETROCI</strong></td>
                            <td>Diallo Fatou</td>
                            <td>+225 05 XX XX XX XX</td>
                            <td>Abidjan, Marcory</td>
                            <td><span class="badge bg-secondary">Industrie</span></td>
                            <td><span class="badge bg-warning">En négociation</span></td>
                            <td class="fw-bold text-warning">12M FCFA</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="voirProspect(1)"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning" onclick="editerProspect(1)"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>NESTLE COTE D'IVOIRE</strong></td>
                            <td>Koné Aminata</td>
                            <td>+225 01 XX XX XX XX</td>
                            <td>Abidjan, Yopougon</td>
                            <td><span class="badge bg-secondary">Agroalimentaire</span></td>
                            <td><span class="badge bg-success">Client actif</span></td>
                            <td class="fw-bold text-success">15M FCFA</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="voirProspect(1)"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning" onclick="editerProspect(1)"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>BOLLORE TRANSPORT & LOGISTICS CI</strong></td>
                            <td>Traoré Ibrahim</td>
                            <td>+225 07 XX XX XX XX</td>
                            <td>Abidjan, Treichville</td>
                            <td><span class="badge bg-secondary">Logistique</span></td>
                            <td><span class="badge bg-info">Prospect</span></td>
                            <td class="fw-bold text-info">6M FCFA</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="voirProspect(1)"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning" onclick="editerProspect(1)"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>COTE D'IVOIRE TELECOM</strong></td>
                            <td>Bamba Seydou</td>
                            <td>+225 05 XX XX XX XX</td>
                            <td>Abidjan, Cocody</td>
                            <td><span class="badge bg-secondary">Télécommunications</span></td>
                            <td><span class="badge bg-success">Client actif</span></td>
                            <td class="fw-bold text-success">10M FCFA</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="voirProspect(1)"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning" onclick="editerProspect(1)"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Prospect -->
<div class="modal fade" id="nouveauProspectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Nouveau Prospect
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Raison Sociale *</label>
                            <input type="text" class="form-control" placeholder="Ex: SOCIETE IVOIRIENNE...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom du Contact *</label>
                            <input type="text" class="form-control" placeholder="Ex: Kouassi Jean-Marc">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Téléphone *</label>
                            <input type="tel" class="form-control" placeholder="+225 XX XX XX XX XX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" placeholder="contact@entreprise.ci">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ville *</label>
                            <select class="form-select">
                                <option>Abidjan</option>
                                <option>Yamoussoukro</option>
                                <option>Bouaké</option>
                                <option>San-Pédro</option>
                                <option>Korhogo</option>
                                <option>Daloa</option>
                                <option>Man</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Commune/Quartier</label>
                            <input type="text" class="form-control" placeholder="Ex: Plateau, Cocody...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Secteur d'activité *</label>
                            <select class="form-select">
                                <option>Industrie</option>
                                <option>Commerce</option>
                                <option>BTP</option>
                                <option>Agroalimentaire</option>
                                <option>Transport</option>
                                <option>Logistique</option>
                                <option>Télécommunications</option>
                                <option>Services</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">CA Potentiel (FCFA)</label>
                            <input type="number" class="form-control" placeholder="Ex: 5000000">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Adresse complète</label>
                            <textarea class="form-control" rows="2" placeholder="Ex: Rue du Commerce, Plateau, Abidjan"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" rows="3" placeholder="Informations complémentaires..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#prospectsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        pageLength: 10,
        order: [[0, 'asc']]
    });
});

// Fonctions pour les boutons d'action
function voirProspect(id) {
    alert('Détails du prospect #' + id + ' - Fonctionnalité en développement');
}

function editerProspect(id) {
    alert('Modification du prospect #' + id + ' - Fonctionnalité en développement');
}
</script>
@endsection
