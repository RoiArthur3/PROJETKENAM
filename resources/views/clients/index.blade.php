@extends('layouts.app')

@section('title', 'Clients - Liste | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Liste des Clients"
    icon="fa-users"
    :createRoute="route('clients.create')"
    createLabel="Nouveau Client"
    :exportable="true"
    searchPlaceholder="Nom, entreprise, email, téléphone..."
    :count="25"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Clients"
            value="25"
            icon="fa-users"
            color="primary"
            subtitle="22 actifs"
        />
        <x-kpi-card
            title="Clients Actifs"
            value="22"
            icon="fa-check-circle"
            color="success"
            subtitle="88% d'activité"
        />
        <x-kpi-card
            title="Crédit Moyen"
            value="300K FCFA"
            icon="fa-credit-card"
            color="warning"
            subtitle="Par client"
        />
        <x-kpi-card
            title="Entreprises"
            value="15"
            icon="fa-building"
            color="info"
            subtitle="60% des clients"
        />
    </x-slot>

    <!-- Filtres supplémentaires -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="">Tous les types</option>
                <option>Particulier</option>
                <option>Entreprise</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option>Actif</option>
                <option>Inactif</option>
                <option>Suspendu</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Crédit</label>
            <select name="credit" class="form-select">
                <option value="">Tous</option>
                <option>Avec crédit</option>
                <option>Sans crédit</option>
                <option>Crédit dépassé</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Client</th>
            <th>Type</th>
            <th>Contact</th>
            <th>Limite Crédit</th>
            <th>Encours</th>
            <th>Dernière Facture</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-3">EA</div>
                    <div>
                        <div class="fw-bold">Entreprise ABC</div>
                        <div class="text-muted small">ABC Logistics SARL</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-primary">Entreprise</span></td>
            <td>
                <div>
                    <div><i class="fas fa-envelope text-muted me-1"></i>contact@abc.ci</div>
                    <div><i class="fas fa-phone text-muted me-1"></i>+225 01 02 03 04 07</div>
                </div>
            </td>
            <td><span class="fw-bold text-success">500 000 FCFA</span></td>
            <td><span class="fw-bold text-info">125 000 FCFA</span></td>
            <td>15/10/2024</td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('clients.show', 1) }}" class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('clients.edit', 1) }}" class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-outline-danger" title="Supprimer" onclick="confirmDelete(1, 'Entreprise ABC', '{{ route('clients.destroy', 1) }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Factures" onclick="voirFactures(1)">
                        <i class="fas fa-file-invoice"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning text-white me-3">JD</div>
                    <div>
                        <div class="fw-bold">Jean Dupont</div>
                        <div class="text-muted small">Client individuel</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-warning">Particulier</span></td>
            <td>
                <div>
                    <div><i class="fas fa-envelope text-muted me-1"></i>jean.dupont@email.com</div>
                    <div><i class="fas fa-phone text-muted me-1"></i>+225 01 02 03 04 08</div>
                </div>
            </td>
            <td><span class="fw-bold text-success">100 000 FCFA</span></td>
            <td><span class="fw-bold text-success">25 000 FCFA</span></td>
            <td>12/10/2024</td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('clients.show', 2) }}" class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('clients.edit', 2) }}" class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-outline-danger" title="Supprimer" onclick="confirmDelete(2, 'Jean Dupont', '{{ route('clients.destroy', 2) }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Factures" onclick="voirFactures(2)">
                        <i class="fas fa-file-invoice"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-3">TS</div>
                    <div>
                        <div class="fw-bold">Tech Solutions</div>
                        <div class="text-muted small">Services informatiques</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-primary">Entreprise</span></td>
            <td>
                <div>
                    <div><i class="fas fa-envelope text-muted me-1"></i>contact@techsolutions.ci</div>
                    <div><i class="fas fa-phone text-muted me-1"></i>+225 01 02 03 04 09</div>
                </div>
            </td>
            <td><span class="fw-bold text-success">750 000 FCFA</span></td>
            <td><span class="fw-bold text-warning">380 000 FCFA</span></td>
            <td>18/10/2024</td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('clients.show', 3) }}" class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('clients.edit', 3) }}" class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-outline-danger" title="Supprimer" onclick="confirmDelete(3, 'Tech Solutions', '{{ route('clients.destroy', 3) }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Factures" onclick="voirFactures(3)">
                        <i class="fas fa-file-invoice"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info text-white me-3">CM</div>
                    <div>
                        <div class="fw-bold">Construction Moderne</div>
                        <div class="text-muted small">Entreprise de BTP</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-primary">Entreprise</span></td>
            <td>
                <div>
                    <div><i class="fas fa-envelope text-muted me-1"></i>info@construction.ci</div>
                    <div><i class="fas fa-phone text-muted me-1"></i>+225 01 02 03 04 10</div>
                </div>
            </td>
            <td><span class="fw-bold text-danger">0 FCFA</span></td>
            <td><span class="fw-bold text-danger">0 FCFA</span></td>
            <td>08/10/2024</td>
            <td><span class="badge bg-warning">Inactif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('clients.show', 4) }}" class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('clients.edit', 4) }}" class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-outline-success" title="Réactiver" onclick="reactiverClient(4)">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary text-white me-3">MS</div>
                    <div>
                        <div class="fw-bold">Marie Sarr</div>
                        <div class="text-muted small">Consultante indépendante</div>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-warning">Particulier</span></td>
            <td>
                <div>
                    <div><i class="fas fa-envelope text-muted me-1"></i>marie.sarr@consult.ci</div>
                    <div><i class="fas fa-phone text-muted me-1"></i>+225 01 02 03 04 11</div>
                </div>
            </td>
            <td><span class="fw-bold text-success">50 000 FCFA</span></td>
            <td><span class="fw-bold text-success">15 000 FCFA</span></td>
            <td>22/10/2024</td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('clients.show', 5) }}" class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('clients.edit', 5) }}" class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-outline-danger" title="Supprimer" onclick="confirmDelete(5, 'Construction Materiel', '{{ route('clients.destroy', 5) }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Factures" onclick="voirFactures(5)">
                        <i class="fas fa-file-invoice"></i>
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
function voirFactures(id) {
    alert('Historique des factures du client ' + id + ' - Fonctionnalité en développement');
}

function reactiverClient(id) {
    if (confirm('Voulez-vous réactiver ce client ?')) {
        alert('Client ' + id + ' réactivé - Fonctionnalité en développement');
    }
}

// Fonction de confirmation de suppression
function confirmDelete(clientId, clientName, deleteUrl) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le client "${clientName}" (ID: ${clientId}) ?\n\nCette action est irréversible !`)) {
        // Créer un formulaire caché pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteUrl;
        form.style.display = 'none';

        // Ajouter le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        // Ajouter le champ pour la méthode DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
