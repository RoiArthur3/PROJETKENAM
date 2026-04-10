@extends('layouts.app')

@section('title', 'Détails Approvisionnement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Approvisionnement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.approvisionnements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.approvisionnements.edit', $approvisionnement->id) }}" class="btn btn-outline-warning">
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
                                    <td>{{ $approvisionnement->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date d'approvisionnement</th>
                                    <td>{{ \Carbon\Carbon::parse($approvisionnement->date_approvisionnement)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Libellé</th>
                                    <td>{{ $approvisionnement->libelle }}</td>
                                </tr>
                                <tr>
                                    <th>Montant</th>
                                    <td class="fw-bold text-success">{{ number_format($approvisionnement->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Caisse</th>
                                    <td>
                                        <span class="badge bg-info">{{ $approvisionnement->caisse }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge bg-{{ $approvisionnement->statut == 'validé' ? 'success' : 'warning' }}">
                                            {{ ucfirst($approvisionnement->statut) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Informations de source</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Source</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $approvisionnement->source }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Référence source</th>
                                    <td>{{ $approvisionnement->reference_source ?? 'Non spécifiée' }}</td>
                                </tr>
                                <tr>
                                    <th>Responsable</th>
                                    <td>{{ $approvisionnement->responsable }}</td>
                                </tr>
                                <tr>
                                    <th>Date de validation</th>
                                    <td>{{ $approvisionnement->date_validation ? \Carbon\Carbon::parse($approvisionnement->date_validation)->format('d/m/Y H:i') : 'Non validée' }}</td>
                                </tr>
                                <tr>
                                    <th>Temps écoulé</th>
                                    <td>{{ \Carbon\Carbon::parse($approvisionnement->date_approvisionnement)->diffForHumans() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- KPIs de l'approvisionnement -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Montant</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($approvisionnement->montant, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvisionnement->caisse }}</div>
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
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Source</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvisionnement->source }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ucfirst($approvisionnement->statut) }}</div>
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
                                @if($approvisionnement->statut == 'en attente')
                                    <button class="btn btn-success" onclick="validerApprovisionnement()">
                                        <i class="fas fa-check me-2"></i>Valider
                                    </button>
                                @endif
                                <a href="{{ route('tresorerie.approvisionnements.edit', $approvisionnement->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <button class="btn btn-info" onclick="imprimerApprovisionnement()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                                <button class="btn btn-outline-primary" onclick="telechargerRecu()">
                                    <i class="fas fa-download me-2"></i>Télécharger reçu
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline de l'approvisionnement -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Historique de l'approvisionnement</h5>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Demande d'approvisionnement</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($approvisionnement->date_approvisionnement)->format('d/m/Y H:i') }}</p>
                                        <small>Approvisionnement de {{ number_format($approvisionnement->montant, 0, ',', ' ') }} FCFA pour {{ $approvisionnement->caisse }}</small>
                                    </div>
                                </div>
                                @if($approvisionnement->date_validation)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Validation</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($approvisionnement->date_validation)->format('d/m/Y H:i') }}</p>
                                        <small>Approvisionnement validé par {{ $approvisionnement->responsable }}</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(isset($approvisionnement->notes))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Notes</h5>
                            <p>{{ $approvisionnement->notes }}</p>
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
function validerApprovisionnement() {
    if (confirm('Valider cet approvisionnement?')) {
        alert('Approvisionnement validé avec succès!');
    }
}

function imprimerApprovisionnement() {
    window.print();
}

function telechargerRecu() {
    alert('Téléchargement du reçu simulé');
}
</script>

@endsection
