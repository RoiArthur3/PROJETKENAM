@extends('layouts.app')

@section('title', 'Stock - Gestion des Produits | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Gestion des Produits"
    icon="fa-box"
    :createRoute="route('stock.produits.create')"
    createLabel="Ajouter Produit"
    :exportable="true"
    searchPlaceholder="Référence, nom, catégorie, code-barres..."
    :count="150"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Produits"
            value="150"
            icon="fa-box"
            color="primary"
        />
        <x-kpi-card
            title="En Stock"
            value="120"
            icon="fa-check-circle"
            color="success"
            subtitle="80% du total"
        />
        <x-kpi-card
            title="Stock Faible"
            value="15"
            icon="fa-exclamation-triangle"
            color="warning"
            subtitle="Alertes actives"
        />
        <x-kpi-card
            title="Rupture Stock"
            value="5"
            icon="fa-times-circle"
            color="danger"
            subtitle="Réapprovisionnement urgent"
        />
    </x-slot>

    <!-- Filtres supplémentaires -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Catégorie</label>
            <select name="categorie" class="form-select">
                <option value="">Toutes</option>
                <option>Équipement</option>
                <option>Matériel</option>
                <option>Consommables</option>
                <option>Pièces détachées</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Statut Stock</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option>En stock</option>
                <option>Stock faible</option>
                <option>Rupture</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Entrepôt</label>
            <select name="entrepot" class="form-select">
                <option value="">Tous</option>
                <option>Principal</option>
                <option>Secondaire</option>
                <option>Extérieur</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Référence</th>
            <th>Nom</th>
            <th>Catégorie</th>
            <th>Stock Actuel</th>
            <th>Stock Min</th>
            <th>Prix Unitaire</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>PRD-001</strong></td>
            <td>Casque de chantier</td>
            <td><span class="badge bg-primary">Équipement</span></td>
            <td><span class="fw-bold text-success">150</span></td>
            <td>20</td>
            <td>5 000 FCFA</td>
            <td><span class="badge bg-success">En stock</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Mouvements">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>PRD-002</strong></td>
            <td>Gants de protection</td>
            <td><span class="badge bg-warning">Consommables</span></td>
            <td><span class="fw-bold text-warning">15</span></td>
            <td>50</td>
            <td>1 500 FCFA</td>
            <td><span class="badge bg-warning">Stock faible</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Mouvements">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>PRD-003</strong></td>
            <td>Vêtements de sécurité</td>
            <td><span class="badge bg-primary">Équipement</span></td>
            <td><span class="fw-bold text-danger">0</span></td>
            <td>10</td>
            <td>12 000 FCFA</td>
            <td><span class="badge bg-danger">Rupture</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Commander">
                        <i class="fas fa-cart-plus"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>PRD-004</strong></td>
            <td>Huile moteur</td>
            <td><span class="badge bg-info">Consommables</span></td>
            <td><span class="fw-bold text-success">75</span></td>
            <td>20</td>
            <td>8 500 FCFA</td>
            <td><span class="badge bg-success">En stock</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Mouvements">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>PRD-005</strong></td>
            <td>Pneumatiques</td>
            <td><span class="badge bg-secondary">Pièces détachées</span></td>
            <td><span class="fw-bold text-warning">8</span></td>
            <td>15</td>
            <td>45 000 FCFA</td>
            <td><span class="badge bg-warning">Stock faible</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Mouvements">
                        <i class="fas fa-exchange-alt"></i>
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
            <li class="page-item">
                <a class="page-link" href="#">Suivant</a>
            </li>
        </ul>
    </x-slot>
</x-list-layout>

<script>
function exportProduits() {
    alert('Export des produits - Fonctionnalité en développement');
}
</script>
@endsection
