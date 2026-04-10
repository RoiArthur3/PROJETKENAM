@extends('layouts.app')

@section('title', 'Stock - Sorties | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Sorties Stock"
    icon="fa-arrow-up"
    :createRoute="route('stock.exits.create')"
    createLabel="Nouvelle Sortie"
    :exportable="true"
    searchPlaceholder="Référence, destinataire, numéro BS..."
    :count="85"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Sorties"
            value="85"
            icon="fa-arrow-up"
            color="primary"
            subtitle="Ce mois"
        />
        <x-kpi-card
            title="Articles Sortis"
            value="1 420"
            icon="fa-boxes"
            color="warning"
            subtitle="Pièces/units"
        />
        <x-kpi-card
            title="Valeur Totale"
            value="28.5M FCFA"
            icon="fa-money-bill-wave"
            color="info"
            subtitle="Sorties expédiées"
        />
        <x-kpi-card
            title="Destinataires Actifs"
            value="15"
            icon="fa-building"
            color="success"
            subtitle="Ce trimestre"
        />
    </x-slot>

    <!-- Filtres supplémentaires -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Entrepôt</label>
            <select name="entrepot" class="form-select">
                <option value="">Tous</option>
                <option>WH-ABJ-01</option>
                <option>WH-YAK-02</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option>En préparation</option>
                <option>Expédiée</option>
                <option>Livrée</option>
                <option>Annulée</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Destinataire</label>
            <select name="destinataire" class="form-select">
                <option value="">Tous</option>
                <option>Chantiers</option>
                <option>Services</option>
                <option>Projets</option>
                <option>Clients</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Référence</th>
            <th>Date</th>
            <th>Entrepôt</th>
            <th>Destinataire</th>
            <th>Nb Articles</th>
            <th>Montant Total</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>SOR-2025-001</strong></td>
            <td>20/10/2024</td>
            <td><span class="badge bg-primary">WH-ABJ-01</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2">CX</div>
                    <span class="fw-bold small">Chantier X</span>
                </div>
            </td>
            <td><span class="fw-bold">5</span></td>
            <td><span class="fw-bold text-warning">120 000 FCFA</span></td>
            <td><span class="badge bg-success">Expédiée</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirSortie(1)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierSortie(1)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Suivi" onclick="suivreSortie(1)">
                        <i class="fas fa-truck"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>SOR-2025-002</strong></td>
            <td>19/10/2024</td>
            <td><span class="badge bg-info">WH-YAK-02</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning text-white me-2">SY</div>
                    <span class="fw-bold small">Service Y</span>
                </div>
            </td>
            <td><span class="fw-bold">3</span></td>
            <td><span class="fw-bold text-warning">85 000 FCFA</span></td>
            <td><span class="badge bg-warning">En préparation</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirSortie(2)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierSortie(2)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Expédier" onclick="expedierSortie(2)">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>SOR-2025-003</strong></td>
            <td>18/10/2024</td>
            <td><span class="badge bg-primary">WH-ABJ-01</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-danger text-white me-2">PZ</div>
                    <span class="fw-bold small">Projet Z</span>
                </div>
            </td>
            <td><span class="fw-bold">8</span></td>
            <td><span class="fw-bold text-warning">240 000 FCFA</span></td>
            <td><span class="badge bg-info">Livrée</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirSortie(3)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Imprimer" onclick="imprimerSortie(3)">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Suivi" onclick="suivreSortie(3)">
                        <i class="fas fa-truck"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>SOR-2025-004</strong></td>
            <td>17/10/2024</td>
            <td><span class="badge bg-info">WH-YAK-02</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary text-white me-2">CA</div>
                    <span class="fw-bold small">Client A</span>
                </div>
            </td>
            <td><span class="fw-bold">2</span></td>
            <td><span class="fw-bold text-warning">65 000 FCFA</span></td>
            <td><span class="badge bg-success">Expédiée</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirSortie(4)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierSortie(4)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Suivi" onclick="suivreSortie(4)">
                        <i class="fas fa-truck"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>SOR-2025-005</strong></td>
            <td>16/10/2024</td>
            <td><span class="badge bg-primary">WH-ABJ-01</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info text-white me-2">CB</div>
                    <span class="fw-bold small">Client B</span>
                </div>
            </td>
            <td><span class="fw-bold">4</span></td>
            <td><span class="fw-bold text-warning">95 000 FCFA</span></td>
            <td><span class="badge bg-danger">Annulée</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirSortie(5)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Motif annulation" onclick="voirMotifAnnulation(5)">
                        <i class="fas fa-exclamation-triangle"></i>
                    </button>
                </div>
            </td>
        </tr>
    </tbody>

    <!-- Pagination -->
    <x-slot name="pagination">
        <ul class="pagination justify-content-center mb-0">
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
            <li class="page-item disabled">
                <a class="page-link" href="#">Suivant</a>
            </li>
        </ul>
    </x-slot>
</x-list-layout>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
}
</style>

<script>
function voirSortie(id) {
    alert('Détails de la sortie ' + id + ' - Fonctionnalité en développement');
}

function modifierSortie(id) {
    alert('Modification de la sortie ' + id + ' - Fonctionnalité en développement');
}

function expedierSortie(id) {
    if (confirm('Voulez-vous marquer cette sortie comme expédiée ?')) {
        alert('Sortie ' + id + ' marquée comme expédiée - Fonctionnalité en développement');
    }
}

function imprimerSortie(id) {
    alert('Impression de la sortie ' + id + ' - Fonctionnalité en développement');
}

function suivreSortie(id) {
    alert('Suivi de la livraison pour la sortie ' + id + ' - Fonctionnalité en développement');
}

function voirMotifAnnulation(id) {
    alert('Motif d\'annulation pour la sortie ' + id + ' : Demande client - Fonctionnalité en développement');
}
</script>
@endsection
