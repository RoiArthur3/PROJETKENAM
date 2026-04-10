@extends('layouts.app')

@section('title', 'Détails Virement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Virement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.virements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.virements.edit', $virement->id) }}" class="btn btn-outline-warning">
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
                                    <td>{{ $virement->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date de virement</th>
                                    <td>{{ \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Type de virement</th>
                                    <td>
                                        <span class="badge bg-info">{{ $virement->type_virement }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Motif</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $virement->motif }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Responsable</th>
                                    <td>{{ $virement->responsable }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge bg-{{ $virement->statut == 'exécuté' ? 'success' : ($virement->statut == 'validé' ? 'info' : 'warning') }}">
                                            {{ ucfirst($virement->statut) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Détails du virement</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Compte source</th>
                                    <td>
                                        <span class="badge bg-danger">{{ $virement->compte_source }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Compte destination</th>
                                    <td>
                                        <span class="badge bg-success">{{ $virement->compte_destination }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Montant</th>
                                    <td class="fw-bold text-primary">{{ number_format($virement->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Frais</th>
                                    <td class="text-warning">{{ number_format($virement->frais ?? 0, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Montant total</th>
                                    <td class="fw-bold text-danger">{{ number_format($virement->montant + ($virement->frais ?? 0), 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Date d'exécution</th>
                                    <td>{{ $virement->date_execution ? \Carbon\Carbon::parse($virement->date_execution)->format('d/m/Y H:i') : 'Non exécuté' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- KPIs du virement -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Montant</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($virement->montant, 0, ',', ' ') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Source</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $virement->compte_source }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-arrow-left fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Destination</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $virement->compte_destination }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-arrow-right fa-2x text-gray-300"></i>
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
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ucfirst($virement->statut) }}</div>
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
                                @if($virement->statut == 'en attente')
                                    <button class="btn btn-success" onclick="validerVirement()">
                                        <i class="fas fa-check me-2"></i>Valider
                                    </button>
                                @endif
                                @if($virement->statut == 'validé')
                                    <button class="btn btn-info" onclick="executerVirement()">
                                        <i class="fas fa-play me-2"></i>Exécuter
                                    </button>
                                @endif
                                <a href="{{ route('tresorerie.virements.edit', $virement->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <button class="btn btn-info" onclick="imprimerVirement()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                                <button class="btn btn-outline-primary" onclick="telechargerRecu()">
                                    <i class="fas fa-download me-2"></i>Télécharger reçu
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline du virement -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Historique du virement</h5>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Demande de virement</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y H:i') }}</p>
                                        <small>Virement de {{ number_format($virement->montant, 0, ',', ' ') }} FCFA de {{ $virement->compte_source }} vers {{ $virement->compte_destination }}</small>
                                    </div>
                                </div>
                                @if($virement->date_execution)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Exécution</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($virement->date_execution)->format('d/m/Y H:i') }}</p>
                                        <small>Virement exécuté avec succès</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(isset($virement->beneficiaire) || isset($virement->reference_bancaire))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Informations bancaires</h5>
                            <table class="table table-sm">
                                @if(isset($virement->beneficiaire))
                                <tr>
                                    <th width="150">Bénéficiaire</th>
                                    <td>{{ $virement->beneficiaire }}</td>
                                </tr>
                                @endif
                                @if(isset($virement->reference_bancaire))
                                <tr>
                                    <th>Référence bancaire</th>
                                    <td>{{ $virement->reference_bancaire }}</td>
                                </tr>
                                @endif
                                @if(isset($virement->taux_change))
                                <tr>
                                    <th>Taux de change</th>
                                    <td>{{ $virement->taux_change }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    @endif

                    @if(isset($virement->notes))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Notes</h5>
                            <p>{{ $virement->notes }}</p>
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
function validerVirement() {
    if (confirm('Valider ce virement?')) {
        alert('Virement validé avec succès!');
    }
}

function executerVirement() {
    if (confirm('Exécuter ce virement?')) {
        alert('Virement exécuté avec succès!');
    }
}

function imprimerVirement() {
    window.print();
}

function telechargerRecu() {
    alert('Téléchargement du reçu simulé');
}
</script>

@endsection
