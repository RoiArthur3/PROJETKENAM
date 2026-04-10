@extends('layouts.app')

@section('title', 'Commercial - Proforma | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Proformas"
    icon="fa-file-invoice"
    :createRoute="route('commercial.devis.create')"
    createLabel="Nouvelle Proforma"
    :exportable="true"
    searchPlaceholder="N° proforma, client, contrat..."
    :count="23"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="En Attente"
            value="8"
            icon="fa-clock"
            color="warning"
            subtitle="42M FCFA"
        />
        <x-kpi-card
            title="Validées"
            value="15"
            icon="fa-check-circle"
            color="success"
            subtitle="85M FCFA"
        />
        <x-kpi-card
            title="Ce Mois"
            value="23"
            icon="fa-calendar"
            color="info"
            subtitle="+12 vs mois dernier"
        />
        <x-kpi-card
            title="Total Année"
            value="285M FCFA"
            icon="fa-file-invoice"
            color="primary"
            subtitle="Émis cette année"
        />
    </x-slot>

    <!-- Filtres supplémentaires -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option>Brouillon</option>
                <option>Envoyé</option>
                <option>Accepté</option>
                <option>Refusé</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Client</label>
            <select name="client" class="form-select">
                <option value="">Tous les clients</option>
                <option>Entreprise ABC</option>
                <option>Tech Solutions</option>
                <option>Construction Moderne</option>
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
            <th>N° Proforma</th>
            <th>Date</th>
            <th>Client</th>
            <th>Contrat</th>
            <th>Montant HT</th>
            <th>TVA 18%</th>
            <th>Total TTC</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>DEV-0023</strong></td>
            <td>20/10/2024</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2">TS</div>
                    <span class="fw-bold small">Tech Solutions</span>
                </div>
            </td>
            <td><span class="badge bg-info">CTR-2024-015</span></td>
            <td><span class="fw-bold">3 500 000 FCFA</span></td>
            <td>630 000 FCFA</td>
            <td><span class="fw-bold text-success">4 130 000 FCFA</span></td>
            <td><span class="badge bg-warning">Envoyé</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirProforma(23)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" title="PDF" onclick="telechargerPDF(23)">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Dupliquer" onclick="dupliquerProforma(23)">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>DEV-0022</strong></td>
            <td>19/10/2024</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2">CM</div>
                    <span class="fw-bold small">Construction Moderne</span>
                </div>
            </td>
            <td><span class="badge bg-success">CTR-2024-014</span></td>
            <td><span class="fw-bold">8 200 000 FCFA</span></td>
            <td>1 476 000 FCFA</td>
            <td><span class="fw-bold text-success">9 676 000 FCFA</span></td>
            <td><span class="badge bg-success">Accepté</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirProforma(22)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" title="PDF" onclick="telechargerPDF(22)">
                        <i class="fas fa-file-pdf"></i>
                    </button>

                </div>
            </td>
        </tr>
        <tr>
            <td><strong>DEV-0021</strong></td>
            <td>18/10/2024</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning text-white me-2">EA</div>
                    <span class="fw-bold small">Entreprise ABC</span>
                </div>
            </td>
            <td><span class="badge bg-primary">CTR-2024-013</span></td>
            <td><span class="fw-bold">2 800 000 FCFA</span></td>
            <td>504 000 FCFA</td>
            <td><span class="fw-bold text-success">3 304 000 FCFA</span></td>
            <td><span class="badge bg-info">Envoyé</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirProforma(21)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" title="PDF" onclick="telechargerPDF(21)">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Relancer" onclick="relancerClient(21)">
                        <i class="fas fa-envelope"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>DEV-0020</strong></td>
            <td>17/10/2024</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info text-white me-2">LP</div>
                    <span class="fw-bold small">Logistics Pro</span>
                </div>
            </td>
            <td><span class="badge bg-warning">CTR-2024-012</span></td>
            <td><span class="fw-bold">1 900 000 FCFA</span></td>
            <td>342 000 FCFA</td>
            <td><span class="fw-bold text-success">2 242 000 FCFA</span></td>
            <td><span class="badge bg-success">Accepté</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirProforma(20)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" title="PDF" onclick="telechargerPDF(20)">
                        <i class="fas fa-file-pdf"></i>
                    </button>

                </div>
            </td>
        </tr>
        <tr>
            <td><strong>DEV-0019</strong></td>
            <td>16/10/2024</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-secondary text-white me-2">MS</div>
                    <span class="fw-bold small">Maintenance Solutions</span>
                </div>
            </td>
            <td><span class="badge bg-danger">CTR-2024-011</span></td>
            <td><span class="fw-bold">4 100 000 FCFA</span></td>
            <td>738 000 FCFA</td>
            <td><span class="fw-bold text-success">4 838 000 FCFA</span></td>
            <td><span class="badge bg-secondary">Brouillon</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" title="Voir" onclick="voirProforma(19)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" title="Modifier" onclick="modifierProforma(19)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" title="Envoyer" onclick="envoyerProforma(19)">
                        <i class="fas fa-paper-plane"></i>
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
function voirProforma(id) {
    window.location.href = '/commercial/proforma/' + id;
}

function telechargerPDF(id) {
    window.location.href = '/commercial/proforma/' + id + '/pdf';
}

function dupliquerProforma(id) {
    if (confirm('Voulez-vous dupliquer cette proforma ?')) {
        alert('Proforma dupliquée - Fonctionnalité en développement');
    }
}



function relancerClient(id) {
    if (confirm('Voulez-vous envoyer un email de relance ?')) {
        alert('Email de relance envoyé - Fonctionnalité en développement');
    }
}

function modifierProforma(id) {
    alert('Modification de la proforma DEV-' + String(id).padStart(4, '0') + ' - Fonctionnalité en développement');
}

function envoyerProforma(id) {
    if (confirm('Voulez-vous envoyer cette proforma par email ?')) {
        alert('Proforma envoyée par email - Fonctionnalité en développement');
    }
}
</script>
@endsection
