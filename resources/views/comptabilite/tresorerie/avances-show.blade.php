@extends('layouts.app')

@section('title', 'Détails Avance - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Avance</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.avances') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.avances.edit', $avance->id) }}" class="btn btn-outline-warning">
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
                                    <td>{{ $avance->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date de l'avance</th>
                                    <td>{{ \Carbon\Carbon::parse($avance->date_avance)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Bénéficiaire</th>
                                    <td>{{ $avance->beneficiaire }}</td>
                                </tr>
                                <tr>
                                    <th>Motif</th>
                                    <td>{{ $avance->motif }}</td>
                                </tr>
                                <tr>
                                    <th>Caisse</th>
                                    <td>
                                        <span class="badge bg-info">{{ $avance->caisse }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge bg-{{ $avance->statut == 'validé' ? 'success' : $avance->statut == 'en attente' ? 'warning' : 'danger' }}">
                                            {{ ucfirst($avance->statut) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Informations financières</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Montant</th>
                                    <td class="fw-bold text-warning">{{ number_format($avance->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Date de remboursement</th>
                                    <td>{{ $avance->date_remboursement ? \Carbon\Carbon::parse($avance->date_remboursement)->format('d/m/Y') : 'Non définie' }}</td>
                                </tr>
                                <tr>
                                    <th>Temps écoulé</th>
                                    <td>{{ \Carbon\Carbon::parse($avance->date_avance)->diffForHumans() }}</td>
                                </tr>
                                <tr>
                                    <th>Mode de paiement</th>
                                    <td>Espèces</td>
                                </tr>
                                <tr>
                                    <th>Justificatif</th>
                                    <td>
                                        @if($avance->statut == 'validé')
                                            <span class="badge bg-success">Reçu</span>
                                        @else
                                            <span class="badge bg-warning">En attente</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Timeline de l'avance -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Historique de l'avance</h5>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Demande d'avance</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($avance->date_avance)->format('d/m/Y H:i') }}</p>
                                        <small>Demande de {{ number_format($avance->montant, 0, ',', ' ') }} FCFA pour {{ $avance->motif }}</small>
                                    </div>
                                </div>
                                @if($avance->statut == 'validé')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Validation</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($avance->date_avance)->addHours(2)->format('d/m/Y H:i') }}</p>
                                        <small>Avance validée par le responsable</small>
                                    </div>
                                </div>
                                @endif
                                @if($avance->statut == 'remboursé')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Remboursement</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($avance->date_remboursement)->format('d/m/Y H:i') }}</p>
                                        <small>Avance remboursée intégralement</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Actions rapides</h5>
                            <div class="d-flex gap-2">
                                @if($avance->statut == 'validé')
                                    <button class="btn btn-success" onclick="rembourserAvance()">
                                        <i class="fas fa-check me-2"></i>Rembourser
                                    </button>
                                @endif
                                @if($avance->statut == 'en attente')
                                    <button class="btn btn-primary" onclick="validerAvance()">
                                        <i class="fas fa-check-circle me-2"></i>Valider
                                    </button>
                                    <button class="btn btn-danger" onclick="annulerAvance()">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </button>
                                @endif
                                <a href="{{ route('tresorerie.paiements.create') }}" class="btn btn-info">
                                    <i class="fas fa-money-bill-wave me-2"></i>Payer
                                </a>
                                <button class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(isset($avance->description))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Description</h5>
                            <p>{{ $avance->description }}</p>
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
function validerAvance() {
    if (confirm('Valider cette avance?')) {
        alert('Avance validée avec succès!');
    }
}

function annulerAvance() {
    if (confirm('Annuler cette avance?')) {
        alert('Avance annulée!');
    }
}

function rembourserAvance() {
    if (confirm('Marquer cette avance comme remboursée?')) {
        alert('Avance remboursée avec succès!');
    }
}
</script>

@endsection
