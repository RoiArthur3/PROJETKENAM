@extends('layouts.app')

@section('title', 'Bons de Commande - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Bons de Commande</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('commercial.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Dashboard Commercial
            </a>
            <a href="{{ route('commercial.index') }}" class="btn btn-outline-info">
                <i class="fas fa-shopping-cart me-2"></i>Commercial
            </a>
            <a href="{{ route('commercial.bon-livraison') }}" class="btn btn-outline-success">
                <i class="fas fa-truck me-2"></i>Bons de Livraison
            </a>
            <a href="{{ route('commercial.clients.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-users me-2"></i>Clients
            </a>
            <button class="btn btn-primary" onclick="showCreateModal()">
                <i class="fas fa-plus me-2"></i>Créer un Bon de Commande
            </button>
        </div>
    </div>

    <!-- KPIs Bons de Commande -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Bons</h6>
                            <h3 class="mb-0">{{ $bonsCommande->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-primary text-white">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Montant Total</h6>
                            <h3 class="mb-0">{{ number_format($bonsCommande->sum('montant') ?? 0, 0, ',', ' ') }} FCFA</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">En Attente</h6>
                            <h3 class="mb-0">{{ $bonsCommande->where('statut', 'en_attente')->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-warning text-white">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Validés</h6>
                            <h3 class="mb-0">{{ $bonsCommande->where('statut', 'valide')->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-success text-white">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Annulés</h6>
                            <h3 class="mb-0">{{ $bonsCommande->where('statut', 'annule')->count() ?? 0 }}</h3>
                        </div>
                        <div class="ms-3">
                            <div class="avatar-circle bg-danger text-white">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="validé" {{ request('statut') == 'validé' ? 'selected' : '' }}>Validés</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="livré" {{ request('statut') == 'livré' ? 'selected' : '' }}>Livrés</option>
                        <option value="annulé" {{ request('statut') == 'annulé' ? 'selected' : '' }}>Annulés</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Client</label>
                    <select name="client" class="form-select">
                        <option value="">Tous les clients</option>
                        @foreach($bonsCommande->pluck('client')->unique() as $client)
                            <option value="{{ $client }}" {{ request('client') == $client ? 'selected' : '' }}>{{ $client }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select name="periode" class="form-select">
                        <option value="">Toutes périodes</option>
                        <option value="7" {{ request('periode') == '7' ? 'selected' : '' }}>7 derniers jours</option>
                        <option value="30" {{ request('periode') == '30' ? 'selected' : '' }}>30 derniers jours</option>
                        <option value="90" {{ request('periode') == '90' ? 'selected' : '' }}>90 derniers jours</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Montant Min</label>
                    <input type="number" name="montant_min" class="form-control" placeholder="Montant minimum" value="{{ request('montant_min') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('commercial.bon-commande') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des Bons de Commande -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Produits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bonsCommande as $bon)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        BC
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $bon->reference }}</div>
                                        <div class="text-muted small">ID: {{ $bon->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($bon->client, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $bon->client }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ $bon->date->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $bon->date->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">
                                    {{ number_format($bon->montant, 0, ',', ' ') }} FCFA
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $bon->statut == 'validé' ? 'success' : ($bon->statut == 'livré' ? 'info' : ($bon->statut == 'en_attente' ? 'warning' : 'danger')) }}">
                                    {{ ucfirst($bon->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $bon->produits }}">
                                    {{ $bon->produits }}
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Voir"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewCommandeModal"
                                            data-id="{{ $bon->id }}"
                                            data-reference="{{ $bon->reference }}"
                                            data-client="{{ $bon->client }}"
                                            data-montant="{{ number_format($bon->montant, 0, ',', ' ') }} FCFA"
                                            data-statut="{{ ucfirst($bon->statut) }}"
                                            data-date="{{ $bon->date->format('d/m/Y') }}"
                                            data-produits="{{ $bon->produits }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Modifier"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCommandeModal"
                                            data-id="{{ $bon->id }}"
                                            data-reference="{{ $bon->reference }}"
                                            data-client="{{ $bon->client }}"
                                            data-montant="{{ $bon->montant }}"
                                            data-statut="{{ $bon->statut }}"
                                            data-produits="{{ $bon->produits }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($bon->statut == 'validé')
                                        <button class="btn btn-outline-success" title="Créer Bon de Livraison">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                    @endif
                                    @if($bon->statut == 'en_attente')
                                        <button class="btn btn-outline-info" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-danger" title="Supprimer"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCommandeModal"
                                            data-id="{{ $bon->id }}"
                                            data-reference="{{ $bon->reference }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun bon de commande trouvé</p>
                                <a href="#" class="btn btn-primary" onclick="showCreateModal()">
                                    <i class="fas fa-plus me-2"></i>Créer le premier bon de commande
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Création de Bon de Commande -->
<div class="modal fade" id="createCommandeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer un Bon de Commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createCommandeForm">
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
                                <option value="Client GHI">Client GHI</option>
                                <option value="Client JKL">Client JKL</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Commande</label>
                            <input type="date" class="form-control" id="date_commande" name="date_commande" required value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Livraison Prévue</label>
                            <input type="date" class="form-control" id="date_livraison" name="date_livraison" required value="{{ now()->addDays(7)->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant (FCFA)</label>
                            <input type="number" class="form-control" id="montant" name="montant" required min="0" step="1000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="en_attente">En attente</option>
                                <option value="validé">Validé</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Produits</label>
                        <textarea class="form-control" id="produits" name="produits" rows="3" placeholder="Description des produits ou services..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Notes additionnelles..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="createCommande()">
                    <i class="fas fa-save me-2"></i>Créer le Bon de Commande
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
        return `BC-${year}${month}${day}-${random}`;
    }

    // Afficher la modal de création
    function showCreateModal() {
        // S'assurer que Bootstrap est chargé
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap n\'est pas chargé. Attendez le chargement complet de la page.');
            setTimeout(() => showCreateModal(), 500);
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('createCommandeModal'));
        document.getElementById('reference').value = generateReference();
        modal.show();
    }

    // Créer un bon de commande
    function createCommande() {
        const form = document.getElementById('createCommandeForm');

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
            Bon de commande créé avec succès !<br>
            Référence: ${data.get('reference')}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
            // Fermer la modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('createCommandeModal'));
            if (modal) modal.hide();
        }, 3000);

        // Fermer la modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('createCommandeModal'));
        if (modal) modal.hide();

        // Réinitialiser le formulaire
        form.reset();
        document.getElementById('reference').value = generateReference();
    }

    // Rendre les fonctions globales
    window.showCreateModal = showCreateModal;
    window.createCommande = createCommande;

    // Modal Voir
    const viewModal = document.getElementById('viewCommandeModal');
    viewModal?.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        document.getElementById('viewReference').textContent = button.getAttribute('data-reference') || '';
        document.getElementById('viewClient').textContent = button.getAttribute('data-client') || '';
        document.getElementById('viewDate').textContent = button.getAttribute('data-date') || '';
        document.getElementById('viewMontant').textContent = button.getAttribute('data-montant') || '';
        document.getElementById('viewStatut').textContent = button.getAttribute('data-statut') || '';
        document.getElementById('viewProduits').textContent = button.getAttribute('data-produits') || '';
    });

    // Modal Édition
    const editModal = document.getElementById('editCommandeModal');
    editModal?.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        document.getElementById('editId').value = button.getAttribute('data-id') || '';
        document.getElementById('editReference').value = button.getAttribute('data-reference') || '';
        document.getElementById('editClient').value = button.getAttribute('data-client') || '';
        document.getElementById('editMontant').value = button.getAttribute('data-montant') || '';
        document.getElementById('editStatut').value = button.getAttribute('data-statut') || 'en_attente';
        document.getElementById('editProduits').value = button.getAttribute('data-produits') || '';
    });

    document.getElementById('editCommandeForm')?.addEventListener('submit', (event) => {
        event.preventDefault();
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            Bon de commande mis à jour avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3000);

        const modal = bootstrap.Modal.getInstance(document.getElementById('editCommandeModal'));
        if (modal) modal.hide();
    });

    // Modal Suppression
    const deleteModal = document.getElementById('deleteCommandeModal');
    deleteModal?.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        document.getElementById('deleteId').value = button.getAttribute('data-id') || '';
        document.getElementById('deleteReference').textContent = button.getAttribute('data-reference') || '';
    });

    document.querySelector('#deleteCommandeModal .btn-danger')?.addEventListener('click', () => {
        const alert = document.createElement('div');
        alert.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            <i class="fas fa-trash me-2"></i>
            Bon de commande supprimé.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3000);

        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCommandeModal'));
        if (modal) modal.hide();
    });
});
</script>
@endsection
