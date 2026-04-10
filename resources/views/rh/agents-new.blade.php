@extends('layouts.app')

@section('title', 'RH - Gestion des Agents | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Gestion des Agents"
    icon="fa-users"
    :createRoute="route('rh.employes.create')"
    createLabel="Ajouter Agent"
    :exportable="true"
    searchPlaceholder="Nom, prénom, poste, email..."
    :count="42"
>
    <!-- Slot KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Agents"
            value="42"
            icon="fa-users"
            color="primary"
        />
        <x-kpi-card
            title="Actifs"
            value="40"
            icon="fa-check-circle"
            color="success"
        />
        <x-kpi-card
            title="En Essai"
            value="2"
            icon="fa-clock"
            color="warning"
        />
        <x-kpi-card
            title="Départ Prévu"
            value="1"
            icon="fa-sign-out-alt"
            color="danger"
        />
    </x-slot>

    <!-- Slot Filtres -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Service</label>
            <select name="service" class="form-select">
                <option value="">Tous les services</option>
                <option>Logistique</option>
                <option>Entretien</option>
                <option>Administration</option>
                <option>Commercial</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous les statuts</option>
                <option>Actif</option>
                <option>En essai</option>
                <option>En congé</option>
                <option>Démission</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Contrat</label>
            <select name="contrat" class="form-select">
                <option value="">Tous les contrats</option>
                <option>CDI</option>
                <option>CDD</option>
                <option>Intérim</option>
                <option>Stage</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Agent</th>
            <th>Service</th>
            <th>Poste</th>
            <th>Contrat</th>
            <th>Date Embauche</th>
            <th>Salaire</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-3">JD</div>
                    <div>
                        <div class="fw-bold">Jean Dupont</div>
                        <div class="text-muted small">jean.dupont@kenam.com</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-primary">Logistique</span></td>
            <td>Agent de quai</td>
            <td><span class="badge bg-success">CDI</span></td>
            <td>15/01/2022</td>
            <td>1 377 600 FCFA</td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Pointage">
                        <i class="fas fa-clock"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-3">MC</div>
                    <div>
                        <div class="fw-bold">Marie Curie</div>
                        <div class="text-muted small">marie.curie@kenam.com</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-success">Entretien</span></td>
            <td>Chef d'équipe</td>
            <td><span class="badge bg-success">CDI</span></td>
            <td>10/05/2021</td>
            <td>1 836 800 FCFA</td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Pointage">
                        <i class="fas fa-clock"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning text-white me-3">PL</div>
                    <div>
                        <div class="fw-bold">Pierre Louis</div>
                        <div class="text-muted small">pierre.louis@kenam.com</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-info">Commercial</span></td>
            <td>Commercial</td>
            <td><span class="badge bg-warning">CDD</span></td>
            <td>01/11/2024</td>
            <td>1 443 200 FCFA</td>
            <td><span class="badge bg-warning">En essai</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Pointage">
                        <i class="fas fa-clock"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-danger text-white me-3">SM</div>
                    <div>
                        <div class="fw-bold">Sophie Martin</div>
                        <div class="text-muted small">sophie.martin@kenam.com</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-secondary">Administration</span></td>
            <td>Assistante RH</td>
            <td><span class="badge bg-success">CDI</span></td>
            <td>15/03/2023</td>
            <td>1 574 400 FCFA</td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Pointage">
                        <i class="fas fa-clock"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info text-white me-3">AB</div>
                    <div>
                        <div class="fw-bold">Ahmed Benali</div>
                        <div class="text-muted small">ahmed.benali@kenam.com</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-primary">Logistique</span></td>
            <td>Chauffeur</td>
            <td><span class="badge bg-success">CDI</span></td>
            <td>20/08/2022</td>
            <td>1 246 400 FCFA</td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Pointage">
                        <i class="fas fa-clock"></i>
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

<script>
function exportData() {
    alert('Export en cours de développement...');
}
</script>
@endsection
