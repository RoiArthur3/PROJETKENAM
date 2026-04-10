@extends('layouts.app')

@section('title', 'Nouveau Bon de Livraison - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-truck me-2 text-success"></i>Nouveau Bon de Livraison
            </h1>
            <p class="text-muted mb-0">Création d'un nouveau bon de livraison</p>
        </div>
        <div>
            <a href="{{ route('commercial.bon-livraison.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <form>
        <!-- Informations générales -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success">
                    <i class="fas fa-info-circle me-2"></i>Informations générales
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Numéro du bon *</label>
                        <input type="text" class="form-control" value="BL-2024-005" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date de livraison prévue *</label>
                        <input type="date" class="form-control" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Bon de commande associé *</label>
                        <select class="form-select" required onchange="loadCommandeInfo()">
                            <option value="">Sélectionner un bon de commande</option>
                            <option value="BC-2024-001">BC-2024-001 - SOCIETE GENERALE</option>
                            <option value="BC-2024-002">BC-2024-002 - ORANGE CI</option>
                            <option value="BC-2024-003">BC-2024-003 - SOLIBRA</option>
                            <option value="BC-2024-005">BC-2024-005 - MTN CI</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Client</label>
                        <input type="text" class="form-control" id="clientInfo" readonly placeholder="Sélectionner un bon de commande">
                    </div>
                </div>
            </div>
        </div>

        <!-- Adresse de livraison -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success">
                    <i class="fas fa-map-marker-alt me-2"></i>Adresse de livraison
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Adresse complète *</label>
                        <textarea class="form-control" rows="2" placeholder="Adresse complète de livraison..." required></textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Ville *</label>
                        <input type="text" class="form-control" placeholder="Ville" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Région/Département</label>
                        <input type="text" class="form-control" placeholder="Région ou département">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Code postal</label>
                        <input type="text" class="form-control" placeholder="Code postal">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Personne à contacter</label>
                        <input type="text" class="form-control" placeholder="Nom du contact">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Téléphone du contact</label>
                        <input type="tel" class="form-control" placeholder="Téléphone">
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles à livrer -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success">
                    <i class="fas fa-box me-2"></i>Articles à livrer
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Quantité commandée</th>
                                <th>Quantité à livrer</th>
                                <th>Quantité restante</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody id="articlesLivraison">
                            <tr>
                                <td>Matériel informatique - Ordinateur portable</td>
                                <td class="text-center">10</td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm" min="0" max="10" value="10" onchange="updateRestante(this)">
                                </td>
                                <td class="text-center">0</td>
                                <td><span class="badge bg-success">Complet</span></td>
                            </tr>
                            <tr>
                                <td>Logiciel - Licence Office 365</td>
                                <td class="text-center">5</td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm" min="0" max="5" value="3" onchange="updateRestante(this)">
                                </td>
                                <td class="text-center">2</td>
                                <td><span class="badge bg-warning">Partiel</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informations de transport -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success">
                    <i class="fas fa-shipping-fast me-2"></i>Informations de transport
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Transporteur</label>
                        <select class="form-select">
                            <option value="">Livraison propre</option>
                            <option value="express">Express Transport</option>
                            <option value="rapid">Rapid Delivery</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Véhicule</label>
                        <input type="text" class="form-control" placeholder="Immatriculation ou type">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Chauffeur</label>
                        <input type="text" class="form-control" placeholder="Nom du chauffeur">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Heure de départ</label>
                        <input type="time" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Heure d'arrivée prévue</label>
                        <input type="time" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success">
                    <i class="fas fa-sticky-note me-2"></i>Notes spéciales
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Instructions de livraison</label>
                        <textarea class="form-control" rows="3" placeholder="Instructions spéciales pour la livraison..."></textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label">Conditions particulières</label>
                        <textarea class="form-control" rows="2" placeholder="Conditions particulières de livraison..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-warning">
                            <i class="fas fa-save me-2"></i>Enregistrer brouillon
                        </button>
                        <button type="button" class="btn btn-outline-info ms-2">
                            <i class="fas fa-print me-2"></i>Imprimer
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('commercial.bon-livraison.index') }}" class="btn btn-outline-danger me-2">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-truck me-2"></i>Créer le bon de livraison
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function loadCommandeInfo() {
    const select = document.querySelector('select[onchange="loadCommandeInfo()"]');
    const clientInput = document.getElementById('clientInfo');
    
    const clients = {
        'BC-2024-001': 'SOCIETE GENERALE',
        'BC-2024-002': 'ORANGE CI',
        'BC-2024-003': 'SOLIBRA',
        'BC-2024-005': 'MTN CI'
    };
    
    if (select.value && clients[select.value]) {
        clientInput.value = clients[select.value];
    } else {
        clientInput.value = '';
    }
}

function updateRestante(input) {
    const row = input.closest('tr');
    const qtyCommandee = parseInt(row.cells[1].textContent);
    const qtyLivrer = parseInt(input.value);
    const qtyRestante = qtyCommandee - qtyLivrer;
    const statutCell = row.cells[4];
    
    row.cells[3].textContent = qtyRestante;
    
    if (qtyRestante === 0) {
        statutCell.innerHTML = '<span class="badge bg-success">Complet</span>';
    } else if (qtyLivrer > 0) {
        statutCell.innerHTML = '<span class="badge bg-warning">Partiel</span>';
    } else {
        statutCell.innerHTML = '<span class="badge bg-secondary">En attente</span>';
    }
}
</script>

@endsection
