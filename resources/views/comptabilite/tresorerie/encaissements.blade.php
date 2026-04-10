@extends('layouts.app')

@section('title', 'Encaissements - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Encaissements</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Trésorerie
            </a>
            <a href="{{ route('tresorerie.caisses') }}" class="btn btn-outline-info">
                <i class="fas fa-cash-register me-2"></i>Caisses
            </a>
            <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-outline-warning">
                <i class="fas fa-money-bill-wave me-2"></i>Décaissements
            </a>
            <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-primary">
                <i class="fas fa-hand-holding-usd me-2"></i>Paiements
            </a>
            <a href="#" class="btn btn-primary" onclick="showCreateModal()">
                <i class="fas fa-plus me-2"></i>Nouvel Encaissement
            </a>
        </div>
    </div>

    <!-- KPIs Encaissements -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Encaissements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $encaissements->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Montant Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($encaissements->sum('montant'), 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Virements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $encaissements->where('mode_paiement', 'Virement')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Espèces</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $encaissements->where('mode_paiement', 'Espèces')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Encaissements -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Encaissements</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Rechercher un encaissement..." id="searchInput">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statutFilter">
                        <option value="">Tous les statuts</option>
                        <option value="validé">Validé</option>
                        <option value="en attente">En attente</option>
                        <option value="annulé">Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="modeFilter">
                        <option value="">Tous les modes</option>
                        <option value="Virement">Virement</option>
                        <option value="Espèces">Espèces</option>
                        <option value="Chèque">Chèque</option>
                        <option value="Mobile Money">Mobile Money</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="clientFilter">
                        <option value="">Tous les clients</option>
                        <option value="CLIENT A">CLIENT A</option>
                        <option value="CLIENT B">CLIENT B</option>
                        <option value="CLIENT C">CLIENT C</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportEncaissements()">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Mode Paiement</th>
                            <th>Montant</th>
                            <th>Caisse</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($encaissements as $encaissement)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($encaissement->reference, -3)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $encaissement->reference }}</div>
                                        <div class="text-muted small">ID: {{ $encaissement->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($encaissement->date_encaissement)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($encaissement->date_encaissement)->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>{{ $encaissement->client }}</td>
                            <td>
                                <span class="badge bg-{{ $encaissement->mode_paiement == 'Virement' ? 'info' : $encaissement->mode_paiement == 'Espèces' ? 'success' : 'primary' }}">
                                    {{ $encaissement->mode_paiement }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($encaissement->montant, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <span class="badge bg-info">Caisse Principale</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $encaissement->statut == 'validé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($encaissement->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="showDetails({{ $encaissement->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editEncaissement({{ $encaissement->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteEncaissement({{ $encaissement->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de création -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvel Encaissement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Référence</label>
                            <input type="text" class="form-control" name="reference" placeholder="ENC-2024-XXX" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date_encaissement" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client</label>
                            <input type="text" class="form-control" name="client" placeholder="Nom du client" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant (FCFA)</label>
                            <input type="number" class="form-control" name="montant" placeholder="0" min="0" step="100" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mode de paiement</label>
                            <select class="form-select" name="mode_paiement" required>
                                <option value="">Choisir un mode</option>
                                <option value="Virement">Virement</option>
                                <option value="Espèces">Espèces</option>
                                <option value="Chèque">Chèque</option>
                                <option value="Mobile Money">Mobile Money</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="statut" required>
                                <option value="validé">Validé</option>
                                <option value="en attente">En attente</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Description de l'encaissement"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveEncaissement()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction de recherche
    function filterEncaissements() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const mode = document.getElementById('modeFilter').value;
        const client = document.getElementById('clientFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[8]?.textContent.toLowerCase().includes(statut);
            const matchesMode = !mode || cells[4]?.textContent.toLowerCase().includes(mode);
            const matchesClient = !client || cells[3]?.textContent.toLowerCase().includes(client);

            row.style.display = (matchesSearch && matchesStatut && matchesMode && matchesClient) ? '' : 'none';
        });
    }

    // Fonctions CRUD
    function showCreateModal() {
        const modal = new bootstrap.Modal(document.getElementById('createModal'));
        modal.show();
    }

    function saveEncaissement() {
        // Simulation de sauvegarde
        alert('Encaissement enregistré avec succès!');
        bootstrap.Modal.getInstance(document.getElementById('createModal')).hide();
    }

    function showDetails(id) {
        alert('Afficher les détails de l\'encaissement ID: ' + id);
    }

    function editEncaissement(id) {
        alert('Modifier l\'encaissement ID: ' + id);
    }

    function deleteEncaissement(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet encaissement?')) {
            alert('Encaissement supprimé!');
        }
    }

    function exportEncaissements() {
        alert('Export des encaissements simulé');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterEncaissements);
    document.getElementById('statutFilter').addEventListener('change', filterEncaissements);
    document.getElementById('modeFilter').addEventListener('change', filterEncaissements);
    document.getElementById('clientFilter').addEventListener('change', filterEncaissements);
});
</script>

@endsection
