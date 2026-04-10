@extends('layouts.app')

@section('title', 'Détails Dépense - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Dépense de Trésorerie</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.depenses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.depenses.edit', $depense->id) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted">Informations générales</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Référence</th>
                                    <td>{{ $depense->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date de dépense</th>
                                    <td>{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Libellé</th>
                                    <td>{{ $depense->libelle }}</td>
                                </tr>
                                <tr>
                                    <th>Montant</th>
                                    <td class="fw-bold text-danger">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Caisse</th>
                                    <td>
                                        <span class="badge bg-info">{{ $depense->caisse }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Motif</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $depense->motif }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Informations de paiement</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Bénéficiaire</th>
                                    <td>{{ $depense->beneficiaire }}</td>
                                </tr>
                                <tr>
                                    <th>Mode de paiement</th>
                                    <td>
                                        <span class="badge bg-secondary">{{ $depense->mode_paiement }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Référence paiement</th>
                                    <td>{{ $depense->reference_paiement ?? 'Non spécifiée' }}</td>
                                </tr>
                                <tr>
                                    <th>Responsable</th>
                                    <td>{{ $depense->responsable }}</td>
                                </tr>
                                <tr>
                                    <th>Date de validation</th>
                                    <td>{{ $depense->date_validation ? \Carbon\Carbon::parse($depense->date_validation)->format('d/m/Y H:i') : 'Non validée' }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge bg-{{ $depense->statut == 'validé' ? 'success' : 'warning' }}">
                                            {{ ucfirst($depense->statut) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- KPIs de la dépense -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Montant</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Caisse</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $depense->caisse }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
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
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Motif</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $depense->motif }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tag fa-2x text-gray-300"></i>
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
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Statut</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ucfirst($depense->statut) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Actions rapides</h5>
                            <div class="d-flex gap-2">
                                @if($depense->statut == 'en attente')
                                    <button class="btn btn-success" onclick="validerDepense()">
                                        <i class="fas fa-check me-2"></i>Valider
                                    </button>
                                @endif
                                <a href="{{ route('tresorerie.depenses.edit', $depense->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <button class="btn btn-info" onclick="imprimerDepense()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                                <button class="btn btn-outline-primary" onclick="telechargerRecu()">
                                    <i class="fas fa-download me-2"></i>Télécharger reçu
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline de la dépense -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Historique de la dépense</h5>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Demande de dépense</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y H:i') }}</p>
                                        <small>Dépense de {{ number_format($depense->montant, 0, ',', ' ') }} FCFA pour {{ $depense->motif }}</small>
                                    </div>
                                </div>
                                @if($depense->date_validation)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Validation</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($depense->date_validation)->format('d/m/Y H:i') }}</p>
                                        <small>Dépense validée par {{ $depense->responsable }}</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(isset($depense->notes))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Notes</h5>
                            <p>{{ $depense->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}
.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
</style>

<script>
function validerDepense() {
    if (confirm('Valider cette dépense?')) {
        alert('Dépense validée avec succès!');
    }
}

function imprimerDepense() {
    window.print();
}

function telechargerRecu() {
    alert('Téléchargement du reçu simulé');
}
</script>

@endsection
