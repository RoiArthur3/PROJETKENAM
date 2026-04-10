@extends('layouts.app')

@section('title', 'Stock - Entrées | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Entrées Stock"
    icon="fa-arrow-down"
    :createRoute="route('stock.entrees.create')"
    createLabel="Nouvelle Entrée"
    :exportable="true"
    searchPlaceholder="Référence, fournisseur, numéro BL..."
    :count="150"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Entrées"
            value="150"
            icon="fa-arrow-down"
            color="primary"
            subtitle="Ce mois"
        />
        <x-kpi-card
            title="Articles Reçus"
            value="2 450"
            icon="fa-boxes"
            color="success"
            subtitle="Pièces/units"
        />
        <x-kpi-card
            title="Valeur Totale"
            value="45.8M FCFA"
            icon="fa-money-bill-wave"
            color="info"
            subtitle="Entrées validées"
        />
        <x-kpi-card
            title="Fournisseurs Actifs"
            value="12"
            icon="fa-industry"
            color="warning"
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
                <option>En attente</option>
                <option>Validée</option>
                <option>Rejetée</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Période</label>
            <select name="periode" class="form-select">
                <option value="">Toutes</option>
                <option>Ce mois</option>
                <option>Mois dernier</option>
                <option>Ce trimestre</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Référence</th>
            <th>Date</th>
            <th>Entrepôt</th>
            <th>Fournisseur</th>
            <th>Nb Articles</th>
            <th>Montant Total</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>ENT-2025-001</strong></td>
            <td>20/10/2024</td>
            <td><span class="badge bg-primary">WH-ABJ-01</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2">FA</div>
                    <span class="fw-bold small">Fournisseur A</span>
                </div>
            </td>
            <td><span class="fw-bold">3</span></td>
            <td><span class="fw-bold text-success">250 000 FCFA</span></td>
            <td><span class="badge bg-success">Validée</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntree(1)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierEntree(1)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Imprimer" onclick="imprimerEntree(1)">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>ENT-2025-002</strong></td>
            <td>19/10/2024</td>
            <td><span class="badge bg-info">WH-YAK-02</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning text-white me-2">FB</div>
                    <span class="fw-bold small">Fournisseur B</span>
                </div>
            </td>
            <td><span class="fw-bold">5</span></td>
            <td><span class="fw-bold text-success">380 000 FCFA</span></td>
            <td><span class="badge bg-warning">En attente</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntree(2)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierEntree(2)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Valider" onclick="validerEntree(2)">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>ENT-2025-003</strong></td>
            <td>18/10/2024</td>
            <td><span class="badge bg-primary">WH-ABJ-01</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-danger text-white me-2">FC</div>
                    <span class="fw-bold small">Fournisseur C</span>
                </div>
            </td>
            <td><span class="fw-bold">2</span></td>
            <td><span class="fw-bold text-success">150 000 FCFA</span></td>
            <td><span class="badge bg-success">Validée</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntree(3)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierEntree(3)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Imprimer" onclick="imprimerEntree(3)">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>ENT-2025-004</strong></td>
            <td>17/10/2024</td>
            <td><span class="badge bg-info">WH-YAK-02</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary text-white me-2">FD</div>
                    <span class="fw-bold small">Fournisseur D</span>
                </div>
            </td>
            <td><span class="fw-bold">8</span></td>
            <td><span class="fw-bold text-success">620 000 FCFA</span></td>
            <td><span class="badge bg-danger">Rejetée</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntree(4)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Motif rejet" onclick="voirMotifRejet(4)">
                        <i class="fas fa-exclamation-triangle"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>ENT-2025-005</strong></td>
            <td>16/10/2024</td>
            <td><span class="badge bg-primary">WH-ABJ-01</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info text-white me-2">FE</div>
                    <span class="fw-bold small">Fournisseur E</span>
                </div>
            </td>
            <td><span class="fw-bold">4</span></td>
            <td><span class="fw-bold text-success">290 000 FCFA</span></td>
            <td><span class="badge bg-success">Validée</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntree(5)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierEntree(5)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Imprimer" onclick="imprimerEntree(5)">
                        <i class="fas fa-print"></i>
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
function voirEntree(id) {
    alert('Détails de l\'entrée ' + id + ' - Fonctionnalité en développement');
}

function modifierEntree(id) {
    alert('Modification de l\'entrée ' + id + ' - Fonctionnalité en développement');
}

function validerEntree(id) {
    if (confirm('Voulez-vous valider cette entrée ?')) {
        alert('Entrée ' + id + ' validée - Fonctionnalité en développement');
    }
}

function imprimerEntree(id) {
    alert('Impression de l\'entrée ' + id + ' - Fonctionnalité en développement');
}

function voirMotifRejet(id) {
    alert('Motif de rejet pour l\'entrée ' + id + ' : Stock insuffisant - Fonctionnalité en développement');
}
</script>
@endsection
