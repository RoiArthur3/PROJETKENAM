@extends('layouts.app')

@section('title', 'Bons de Livraison - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Bons de Livraison</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('commercial.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Dashboard Commercial
            </a>
            <a href="{{ route('commercial.index') }}" class="btn btn-outline-info">
                <i class="fas fa-shopping-cart me-2"></i>Commercial
            </a>
            <a href="{{ route('commercial.bon-commande') }}" class="btn btn-outline-primary">
                <i class="fas fa-file-invoice me-2"></i>Bons de Commande
            </a>
            <a href="{{ route('commercial.clients.index') }}" class="btn btn-outline-success">
                <i class="fas fa-users me-2"></i>Clients
            </a>
            <button class="btn btn-primary" onclick="showCreateModal()">
                <i class="fas fa-plus me-2"></i>Nouveau Bon de Livraison
            </button>
        </div>
    </div>

    <!-- Tableau des Bons de Livraison -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Date Livraison</th>
                            <th>Bon de Commande</th>
                            <th>Montant</th>
                            <th>Livreur</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        BL
                                    </div>
                                    <div>
                                        <div class="fw-bold">BL-2026-001</div>
                                        <div class="text-muted small">ID: 1</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        CX
                                    </div>
                                    <div>
                                        <div class="fw-bold">Client XYZ</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>22/01/2026</div>
                                    <div class="text-muted small">14:30</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-info text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        BC
                                    </div>
                                    <div>
                                        <div class="fw-bold">BC-2026-001</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">2 500 000 FCFA</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        TE
                                    </div>
                                    <div>
                                        <div class="fw-bold">Transport Express</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success">Livrée</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        BL
                                    </div>
                                    <div>
                                        <div class="fw-bold">BL-2026-002</div>
                                        <div class="text-muted small">ID: 2</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        DF
                                    </div>
                                    <div>
                                        <div class="fw-bold">Client DEF</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>23/01/2026</div>
                                    <div class="text-muted small">10:15</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-info text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        BC
                                    </div>
                                    <div>
                                        <div class="fw-bold">BC-2026-003</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">3 200 000 FCFA</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        LK
                                    </div>
                                    <div>
                                        <div class="fw-bold">Logistique KENAM</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning">En attente</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Confirmer">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" title="Reporter">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Annuler">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        BL
                                    </div>
                                    <div>
                                        <div class="fw-bold">BL-2026-003</div>
                                        <div class="text-muted small">ID: 3</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        GH
                                    </div>
                                    <div>
                                        <div class="fw-bold">Client GHI</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>24/01/2026</div>
                                    <div class="text-muted small">16:45</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-info text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        BC
                                    </div>
                                    <div>
                                        <div class="fw-bold">BC-2026-004</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">1 500 000 FCFA</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        TE
                                    </div>
                                    <div>
                                        <div class="fw-bold">Transport Express</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">Programmé</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" title="Confirmer">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Création de Bon de Livraison -->
<div class="modal fade" id="createLivraisonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer un Bon de Livraison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createLivraisonForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Référence</label>
                            <input type="text" class="form-control" id="reference" name="reference" required readonly>
                            <div class="form-text">Générée automatiquement</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client</label>
                            <select class="form-select" id="client" name="client" required>
                                <option value="">Sélectionner un client</option>
                                <option value="Client XYZ">Client XYZ</option>
                                <option value="Client ABC">Client ABC</option>
                                <option value="Client DEF">Client DEF</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bon de Commande</label>
                            <select class="form-select" id="bon_commande" name="bon_commande" required>
                                <option value="">Sélectionner un BC</option>
                                <option value="BC-2026-001">BC-2026-001</option>
                                <option value="BC-2026-002">BC-2026-002</option>
                                <option value="BC-2026-003">BC-2026-003</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Livraison</label>
                            <input type="date" class="form-control" id="date_livraison" name="date_livraison" required value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Livreur</label>
                            <select class="form-select" id="livreur" name="livreur" required>
                                <option value="">Sélectionner un livreur</option>
                                <option value="Transport Express">Transport Express</option>
                                <option value="Logistique KENAM">Logistique KENAM</option>
                                <option value="Express Service">Express Service</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="programmé">Programmé</option>
                                <option value="en_attente">En attente</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Produits</label>
                        <textarea class="form-control" id="produits" name="produits" rows="3" placeholder="Description des produits livrés..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="createLivraison()">
                    <i class="fas fa-save me-2"></i>Créer le Bon de Livraison
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Générer une référence automatiquement
    function generateReference() {
        const date = new Date();
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const random = Math.floor(Math.random() * 1000);
        return `BL-${year}${month}${day}-${random}`;
    }

    // Afficher la modal de création
    function showCreateModal() {
        // S'assurer que Bootstrap est chargé
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap n\'est pas chargé. Attendez le chargement complet de la page.');
            setTimeout(() => showCreateModal(), 500);
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('createLivraisonModal'));
        document.getElementById('reference').value = generateReference();
        modal.show();
    }

    // Créer un bon de livraison
    function createLivraison() {
        const form = document.getElementById('createLivraisonForm');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Simuler la création
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Afficher un message de succès
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            Bon de livraison créé avec succès !<br>
            Référence: ${data.get('reference')}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
            // Fermer la modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('createLivraisonModal'));
            if (modal) modal.hide();
        }, 3000);

        // Fermer la modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('createLivraisonModal'));
        if (modal) modal.hide();

        // Réinitialiser le formulaire
        form.reset();
        document.getElementById('reference').value = generateReference();
    }

    // Rendre les fonctions globales
    window.showCreateModal = showCreateModal;
    window.createLivraison = createLivraison;
});
</script>
@endsection
