@extends('layouts.app')

@section('title', 'Offres & Devis - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice me-2 text-primary"></i>Offres & Devis
            </h1>
            <p class="text-muted mb-0">Création et suivi des devis en FCFA</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nouveauDevisModal">
            <i class="fas fa-plus me-2"></i>Nouveau Devis
        </button>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Devis en Attente</div>
                    <div class="h3 mb-0 text-primary">12</div>
                    <small class="text-muted">Valeur: 28M FCFA</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Devis Acceptés</div>
                    <div class="h3 mb-0 text-success">8</div>
                    <small class="text-muted">Valeur: 45M FCFA</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En Négociation</div>
                    <div class="h3 mb-0 text-warning">5</div>
                    <small class="text-muted">Valeur: 18M FCFA</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Taux Conversion</div>
                    <div class="h3 mb-0 text-danger">64%</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +8% ce mois</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Devis -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des Devis
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="devisTable">
                    <thead class="table-light">
                        <tr>
                            <th>N° Devis</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Objet</th>
                            <th>Montant (FCFA)</th>
                            <th>Validité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>DEV-2024-001</strong></td>
                            <td>05/11/2024</td>
                            <td>SOCIETE IVOIRIENNE DE TRANSPORT</td>
                            <td>Transport de marchandises Abidjan-Bouaké</td>
                            <td class="fw-bold text-success">8,500,000</td>
                            <td>30 jours</td>
                            <td><span class="badge bg-success">Accepté</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" title="Voir"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-success" title="PDF"><i class="fas fa-file-pdf"></i></button>
                                <button class="btn btn-sm btn-info" title="Convertir en facture" onclick="convertToFacture('DEV-2024-001')">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>DEV-2024-002</strong></td>
                            <td>06/11/2024</td>
                            <td>GROUPE PETROCI</td>
                            <td>Logistique et stockage - San-Pédro</td>
                            <td class="fw-bold text-warning">12,000,000</td>
                            <td>45 jours</td>
                            <td><span class="badge bg-warning">En attente</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" title="Voir"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-success" title="PDF"><i class="fas fa-file-pdf"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>DEV-2024-003</strong></td>
                            <td>07/11/2024</td>
                            <td>NESTLE COTE D'IVOIRE</td>
                            <td>Transport frigorifique Abidjan-Korhogo</td>
                            <td class="fw-bold text-success">15,000,000</td>
                            <td>60 jours</td>
                            <td><span class="badge bg-success">Accepté</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" title="Voir"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-success" title="PDF"><i class="fas fa-file-pdf"></i></button>
                                <button class="btn btn-sm btn-info" title="Convertir en facture" onclick="convertToFacture('DEV-2024-003')">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Devis -->
<div class="modal fade" id="nouveauDevisModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i>Nouveau Devis
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Client *</label>
                            <select class="form-select">
                                <option>SOCIETE IVOIRIENNE DE TRANSPORT</option>
                                <option>GROUPE PETROCI</option>
                                <option>NESTLE COTE D'IVOIRE</option>
                                <option>BOLLORE TRANSPORT & LOGISTICS CI</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Date d'émission</label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Validité (jours)</label>
                            <input type="number" class="form-control" value="30">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Objet du devis *</label>
                            <input type="text" class="form-control" placeholder="Ex: Transport de marchandises...">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">Lignes du Devis</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%">Désignation</th>
                                    <th style="width: 15%">Quantité</th>
                                    <th style="width: 20%">Prix Unitaire (FCFA)</th>
                                    <th style="width: 20%">Total (FCFA)</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="lignesDevis">
                                <tr>
                                    <td><input type="text" class="form-control" placeholder="Transport Abidjan-Bouaké"></td>
                                    <td><input type="number" class="form-control" value="1"></td>
                                    <td><input type="number" class="form-control" placeholder="500000"></td>
                                    <td><input type="number" class="form-control" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-success" onclick="ajouterLigne()">
                            <i class="fas fa-plus me-2"></i>Ajouter une ligne
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Conditions de paiement</label>
                            <textarea class="form-control" rows="3" placeholder="Ex: 30% à la commande, 70% à la livraison"></textarea>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Sous-total:</span>
                                        <strong id="sousTotal">0 FCFA</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>TVA (18%):</span>
                                        <strong id="tva">0 FCFA</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">TOTAL:</span>
                                        <strong class="text-primary h5" id="total">0 FCFA</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-file-pdf me-2"></i>Générer PDF
                </button>
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
    $('#devisTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']]
    });
});

function ajouterLigne() {
    const tbody = document.getElementById('lignesDevis');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="form-control" placeholder="Désignation"></td>
        <td><input type="number" class="form-control" value="1"></td>
        <td><input type="number" class="form-control" placeholder="Prix unitaire"></td>
        <td><input type="number" class="form-control" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function convertToFacture(devisId) {
    if (confirm('Voulez-vous convertir ce devis en facture ? La facture sera créée dans le module comptabilité.')) {
        // Rediriger vers la création de facture avec les données du devis
        window.location.href = '/comptabilite/factures/create?devis=' + devisId;
    }
}
</script>
@endsection
