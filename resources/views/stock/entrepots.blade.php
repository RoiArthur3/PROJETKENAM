@extends('layouts.app')

@section('title', 'Stock - Entrepôts | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Gestion des Entrepôts"
    icon="fa-warehouse"
    :createRoute="route('stock.entrepots.create')"
    createLabel="Nouvel Entrepôt"
    :exportable="true"
    searchPlaceholder="Code, nom, ville, responsable..."
    :count="8"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Entrepôts"
            value="8"
            icon="fa-warehouse"
            color="primary"
            subtitle="Tous sites confondus"
        />
        <x-kpi-card
            title="Capacité Totale"
            value="12 500 m²"
            icon="fa-ruler-combined"
            color="success"
            subtitle="Surface disponible"
        />
        <x-kpi-card
            title="Taux Occupation"
            value="72%"
            icon="fa-percentage"
            color="warning"
            subtitle="Moyenne globale"
        />
        <x-kpi-card
            title="Entrepôts Actifs"
            value="7"
            icon="fa-check-circle"
            color="info"
            subtitle="87.5% opérationnels"
        />
    </x-slot>

    <!-- Filtres supplémentaires -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Ville</label>
            <select name="ville" class="form-select">
                <option value="">Toutes</option>
                <option>Abidjan</option>
                <option>Bouaké</option>
                <option>Yamoussoukro</option>
                <option>Autres</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option>Actif</option>
                <option>Inactif</option>
                <option>Saturé</option>
                <option>Maintenance</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Occupation</label>
            <select name="occupation" class="form-select">
                <option value="">Toutes</option>
                <option>0-25%</option>
                <option>26-50%</option>
                <option>51-75%</option>
                <option>76-100%</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Code</th>
            <th>Nom</th>
            <th>Ville</th>
            <th>Responsable</th>
            <th>Capacité</th>
            <th>Occupation</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>WH-ABJ-01</strong></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2">EP</div>
                    <div>
                        <div class="fw-bold">Entrepôt Principal</div>
                        <div class="text-muted small">Site central Abidjan</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-success">Abidjan</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info text-white me-2">MK</div>
                    <span class="fw-bold small">Marie Koné</span>
                </div>
            </td>
            <td><span class="fw-bold">2 000 m²</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="fw-bold text-warning me-2">68%</span>
                    <div class="progress" style="width: 60px; height: 6px;">
                        <div class="progress-bar bg-warning" style="width: 68%"></div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntrepot(1)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierEntrepot(1)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Inventaire" onclick="faireInventaire(1)">
                        <i class="fas fa-clipboard-list"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>WH-YAK-02</strong></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning text-white me-2">DY</div>
                    <div>
                        <div class="fw-bold">Dépôt YAK</div>
                        <div class="text-muted small">Zone industrielle Yamoussoukro</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-info">Yamoussoukro</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2">JD</div>
                    <span class="fw-bold small">Jean Dupont</span>
                </div>
            </td>
            <td><span class="fw-bold">850 m²</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="fw-bold text-danger me-2">91%</span>
                    <div class="progress" style="width: 60px; height: 6px;">
                        <div class="progress-bar bg-danger" style="width: 91%"></div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-warning">Saturé</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntrepot(2)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierEntrepot(2)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger" title="Alerte" onclick="alerteSaturation(2)">
                        <i class="fas fa-exclamation-triangle"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>WH-BOU-03</strong></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2">EB</div>
                    <div>
                        <div class="fw-bold">Entrepôt Bouaké</div>
                        <div class="text-muted small">Plateforme régionale</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-secondary">Bouaké</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2">AT</div>
                    <span class="fw-bold small">Ahmed Traoré</span>
                </div>
            </td>
            <td><span class="fw-bold">1 200 m²</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="fw-bold text-info me-2">45%</span>
                    <div class="progress" style="width: 60px; height: 6px;">
                        <div class="progress-bar bg-info" style="width: 45%"></div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntrepot(3)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierEntrepot(3)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Rapport" onclick="genererRapport(3)">
                        <i class="fas fa-chart-bar"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>WH-ABJ-04</strong></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-danger text-white me-2">ED</div>
                    <div>
                        <div class="fw-bold">Entrepôt Dangbé</div>
                        <div class="text-muted small">Zone portuaire</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-success">Abidjan</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning text-white me-2">SK</div>
                    <span class="fw-bold small">Sophie Kouamé</span>
                </div>
            </td>
            <td><span class="fw-bold">3 500 m²</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="fw-bold text-success me-2">78%</span>
                    <div class="progress" style="width: 60px; height: 6px;">
                        <div class="progress-bar bg-success" style="width: 78%"></div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirEntrepot(4)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierEntrepot(4)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Inventaire" onclick="faireInventaire(4)">
                        <i class="fas fa-clipboard-list"></i>
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
function voirEntrepot(id) {
    alert('Détails de l\'entrepôt ' + id + ' - Fonctionnalité en développement');
}

function modifierEntrepot(id) {
    alert('Modification de l\'entrepôt ' + id + ' - Fonctionnalité en développement');
}

function faireInventaire(id) {
    if (confirm('Voulez-vous lancer un inventaire pour cet entrepôt ?')) {
        alert('Inventaire lancé pour l\'entrepôt ' + id + ' - Fonctionnalité en développement');
    }
}

function alerteSaturation(id) {
    alert('Alerte de saturation envoyée pour l\'entrepôt ' + id + ' - Fonctionnalité en développement');
}

function genererRapport(id) {
    alert('Rapport généré pour l\'entrepôt ' + id + ' - Fonctionnalité en développement');
}
</script>
@endsection
