@extends('layouts.app')

@section('title', 'RH - Gestion de la Paie | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-euro-sign mr-2 text-primary"></i>Gestion de la Paie
            </h1>
            <p class="text-muted">Administration des salaires et bulletins de paie</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a class="btn btn-primary" href="{{ route('rh.paie.create') }}">
                    <i class="fas fa-plus mr-1"></i>Nouveau Bulletin
                </a>
                <a class="btn btn-outline-secondary" href="{{ route('rh.paie.export', ['mois' => optional($period)->format('Y-m')]) }}">
                    <i class="fas fa-download mr-1"></i>Exporter
                </a>
                <button id="btnSimulateur" class="btn btn-outline-info" type="button">
                    <i class="fas fa-calculator mr-1"></i>Simulateur
                </button>
            </div>
        </div>
    </div>

    <!-- Cartes de synthèse -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Masse Salariale</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalBrut ?? 0, 0, ',', ' ') }} FCFA</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">Mois: {{ optional($period)->translatedFormat('F Y') }}</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Bulletins Générés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bulletins ?? 0 }}</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-success">Période : {{ optional($period)->translatedFormat('F Y') }}</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Charges Sociales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalCharges ?? 0, 0, ',', ' ') }} FCFA</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-warning">30% de la masse</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Salaire Moyen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ($bulletins ?? 0) > 0 ? number_format(intval(($totalBrut ?? 0)/max(1,$bulletins)), 0, ',', ' ') : 0 }} FCFA</div>
                            <div class="text-xs text-muted mt-1">
                                <span class="text-info">Brut mensuel</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- État de la paie du mois en cours -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header py-4 bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-calendar-check me-3"></i>État de la Paie - {{ optional($period)->translatedFormat('F Y') }}
                            </h4>
                            <p class="mb-0 opacity-75">Suivi des opérations de paie mensuelles</p>
                        </div>
                        <div class="badge bg-light text-primary fs-6 px-3 py-2">
                            <i class="fas fa-clock me-1"></i>Mois en cours
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- KPIs principaux -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center mb-4">
                            <div class="position-relative">
                                <div class="progress-circle progress-circle-lg mx-auto mb-3" data-value="95" style="--progress-color: #28a745;">
                                    <div class="progress-circle-inner">
                                        <div class="progress-value text-success fw-bold">{{ ($bulletins ?? 0) > 0 ? 100 : 0 }}%</div>
                                    </div>
                                </div>
                                <h5 class="text-success mb-1">Bulletins</h5>
                                <small class="text-muted">Générés avec succès</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <div class="position-relative">
                                <div class="progress-circle progress-circle-lg mx-auto mb-3" data-value="87" style="--progress-color: #007bff;">
                                    <div class="progress-circle-inner">
                                        <div class="progress-value text-primary fw-bold">{{ ($bulletins ?? 0) > 0 ? round((($bulletinsPayes ?? 0)/max(1,$bulletins))*100) : 0 }}%</div>
                                    </div>
                                </div>
                                <h5 class="text-primary mb-1">Paiements</h5>
                                <small class="text-muted">Effectués</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <div class="position-relative">
                                <div class="progress-circle progress-circle-lg mx-auto mb-3" data-value="100" style="--progress-color: #17a2b8;">
                                    <div class="progress-circle-inner">
                                        <div class="progress-value text-info fw-bold">100%</div>
                                    </div>
                                </div>
                                <h5 class="text-info mb-1">DSN</h5>
                                <small class="text-muted">Déclarée</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <div class="position-relative">
                                <div class="progress-circle progress-circle-lg mx-auto mb-3" data-value="92" style="--progress-color: #ffc107;">
                                    <div class="progress-circle-inner">
                                        <div class="progress-value text-warning fw-bold">92%</div>
                                    </div>
                                </div>
                                <h5 class="text-warning mb-1">Validation</h5>
                                <small class="text-muted">Comptable</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Actions détaillées -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-check-circle me-2"></i>Actions Terminées
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Génération des bulletins</strong>
                                            <br><small class="text-muted">Basée sur les données de la période courante</small>
                                        </li>
                                        <li>
                                            <i class="fas fa-check text-success me-2"></i>
                                            <strong>Calcul des charges sociales</strong>
                                            <br><small class="text-muted">Montants issus des bulletins enregistrés</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-clock me-2"></i>Actions en Attente
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="fas fa-clock text-warning me-2"></i>
                                            <strong>Paiement des salaires</strong>
                                            <br><small class="text-muted">À planifier selon le calendrier de paie</small>
                                        </li>
                                        <li>
                                            <i class="fas fa-clock text-muted me-2"></i>
                                            <strong>Remise / archivage des bulletins</strong>
                                            <br><small class="text-muted">Suivant les procédures internes</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filtres et Recherche
            </h6>
        </div>
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Agent</label>
                    <select class="form-select">
                        <option value="">Tous les agents</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mois</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option>Généré</option>
                        <option>Payé</option>
                        <option>En attente</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Service</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>Filtrer
                        </button>
                        <button id="btnResetFiltres" type="button" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-1"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des bulletins de paie -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Bulletins de Paie - {{ optional($period)->translatedFormat('F Y') }} ({{ $bulletins ?? 0 }})
            </h6>
            <div class="btn-group btn-group-sm">
                <form method="POST" action="{{ route('rh.paie.generate-all') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="mois" value="{{ optional($period)->format('Y-m') }}">
                    <button class="btn btn-outline-success" type="submit">
                        <i class="fas fa-play mr-1"></i>Générer Tous
                    </button>
                </form>
                <a class="btn btn-outline-primary" href="{{ route('rh.paie.export', ['mois' => optional($period)->format('Y-m')]) }}">
                    <i class="fas fa-download mr-1"></i>Télécharger Tous
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="paieTable">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            <th>Poste</th>
                            <th>Salaire Brut</th>
                            <th>Charges Salariales</th>
                            <th>Net à Payer</th>
                            <th>Statut</th>
                            <th>Date Paiement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($salaires ?? collect()) as $row)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white mr-3">{{ strtoupper(substr($row->user->name ?? 'NA',0,1)) }}</div>
                                        <div>
                                            <div class="font-weight-bold">{{ $row->user->name ?? '—' }}</div>
                                            <div class="text-muted small">{{ ucfirst($row->user->role ?? '') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ ucfirst($row->user->role ?? '—') }}</td>
                                <td>{{ number_format($row->brut ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td>{{ number_format(($row->cnps_salariale ?? 0) + ($row->autres_retenues ?? 0), 0, ',', ' ') }} FCFA</td>
                                <td class="font-weight-bold">{{ number_format($row->net_a_payer ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    @php
                                        $status = $row->statut ?? 'Non établi';
                                        $color = $status === 'paye' ? 'success' : ($status === 'en_attente' ? 'warning' : 'secondary');
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($status) }}</span>
                                </td>
                                <td>{{ $row->date_paiement ? $row->date_paiement->format('d/m/Y') : '—' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('rh.paie.show', $row->id) }}" class="btn btn-outline-primary" title="Voir Bulletin"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('rh.paie.edit', $row->id) }}" class="btn btn-outline-warning" title="Modifier"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('rh.paie.pdf', $row->id) }}" class="btn btn-outline-info" title="Télécharger"><i class="fas fa-download"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Aucun bulletin pour la période</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Résumé du mois -->
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body text-center">
                            <div class="h6">Total Brut</div>
                            <div class="h4 text-primary font-weight-bold">{{ number_format($totalBrut ?? 0, 0, ',', ' ') }} FCFA</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body text-center">
                            <div class="h6">Charges Sociales</div>
                            <div class="h4 text-warning font-weight-bold">{{ number_format($totalCharges ?? 0, 0, ',', ' ') }} FCFA</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body text-center">
                            <div class="h6">Total Net</div>
                            <div class="h4 text-success font-weight-bold">{{ number_format($totalNet ?? 0, 0, ',', ' ') }} FCFA</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body text-center">
                            <div class="h6">Bulletins Payés</div>
                            <div class="h4 text-info font-weight-bold">{{ ($bulletinsPayes ?? 0) }}/{{ $bulletins ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
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
            </nav>
        </div>
    </div>
</div>

<!-- Styles personnalisés -->
<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 11px;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}

.progress-circle {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.progress-circle-lg {
    width: 120px !important;
    height: 120px !important;
}

.progress-circle::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(var(--progress-color, #4e73df) calc(var(--value) * 1%), #e9ecef 0);
    mask: radial-gradient(circle at center, transparent 50%, black 52%);
    -webkit-mask: radial-gradient(circle at center, transparent 50%, black 52%);
}

.progress-circle-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    height: 80%;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-circle-lg .progress-circle-inner {
    width: 85% !important;
    height: 85% !important;
}

.progress-value {
    font-size: 18px;
    font-weight: bold;
    color: var(--progress-color, #4e73df);
}

.progress-circle-lg .progress-value {
    font-size: 24px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}
</style>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Initialisation de DataTables
    $('#paieTable').DataTable({
        "pageLength": 10,
        "language": {
            "search": "Rechercher:",
            "lengthMenu": "Afficher _MENU_ éléments par page",
            "zeroRecords": "Aucun résultat trouvé",
            "info": "Page _PAGE_ sur _PAGES_",
            "infoEmpty": "Aucun élément disponible",
            "infoFiltered": "(filtré sur _MAX_ éléments au total)",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            }
        }
    });

    // Cercle de progression pour les grands cercles
    document.querySelectorAll('.progress-circle').forEach(circle => {
        const value = circle.getAttribute('data-value');
        circle.style.setProperty('--value', value);
    });

    $('#btnResetFiltres').on('click', function(){
        $(this).closest('form').find('select').val('');
    });
});
</script>
@endsection
