@extends('layouts.app')

@section('title', 'Détails Paiement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Paiement</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.paiements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.paiements.edit', $paiement->id) }}" class="btn btn-outline-warning">
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
                                    <td>{{ $paiement->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date de paiement</th>
                                    <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Bénéficiaire</th>
                                    <td>{{ $paiement->beneficiaire }}</td>
                                </tr>
                                <tr>
                                    <th>Type de paiement</th>
                                    <td>
                                        <span class="badge bg-{{ $paiement->type == 'Fournisseur' ? 'info' : 'success' }}">
                                            {{ $paiement->type }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mode de paiement</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $paiement->mode_paiement }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge bg-{{ $paiement->statut == 'validé' ? 'success' : 'warning' }}">
                                            {{ ucfirst($paiement->statut) }}
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
                                    <td class="fw-bold text-danger">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <th>Compte source</th>
                                    <td>
                                        <span class="badge bg-info">{{ $paiement->compte_source }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Référence document</th>
                                    <td>{{ $paiement->reference_document ?? 'Non spécifiée' }}</td>
                                </tr>
                                <tr>
                                    <th>Frais de transaction</th>
                                    <td>{{ $paiement->mode_paiement == 'Virement' ? '2 500 FCFA' : '0 FCFA' }}</td>
                                </tr>
                                <tr>
                                    <th>Montant net</th>
                                    <td class="fw-bold text-primary">{{ number_format($paiement->montant - ($paiement->mode_paiement == 'Virement' ? 2500 : 0), 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Détails du paiement -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Détails de la transaction</h5>
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Informations de paiement</h6>
                                <p class="mb-0">
                                    <strong>Type :</strong> {{ $paiement->type }}<br>
                                    <strong>Mode :</strong> {{ $paiement->mode_paiement }}<br>
                                    <strong>Compte source :</strong> {{ $paiement->compte_source }}<br>
                                    @if($paiement->reference_document)
                                    <strong>Document :</strong> {{ $paiement->reference_document }}<br>
                                    @endif
                                    <strong>Date :</strong> {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Actions rapides</h5>
                            <div class="d-flex gap-2">
                                @if($paiement->statut == 'en attente')
                                    <button class="btn btn-success" onclick="validerPaiement()">
                                        <i class="fas fa-check me-2"></i>Valider
                                    </button>
                                    <button class="btn btn-danger" onclick="annulerPaiement()">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </button>
                                @endif
                                <a href="{{ route('tresorerie.rapprochements.create') }}" class="btn btn-info">
                                    <i class="fas fa-balance-scale me-2"></i>Rapprocher
                                </a>
                                <button class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                                <button class="btn btn-outline-secondary" onclick="telechargerRecu()">
                                    <i class="fas fa-download me-2"></i>Télécharger reçu
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline du paiement -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Historique du paiement</h5>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Initiation du paiement</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') }}</p>
                                        <small>Paiement de {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA à {{ $paiement->beneficiaire }}</small>
                                    </div>
                                </div>
                                @if($paiement->statut == 'validé')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Validation</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($paiement->date_paiement)->addHours(1)->format('d/m/Y H:i') }}</p>
                                        <small>Paiement validé et traité</small>
                                    </div>
                                </div>
                                @endif
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Traitement bancaire</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($paiement->date_paiement)->addHours(2)->format('d/m/Y H:i') }}</p>
                                        <small>{{ $paiement->mode_paiement }} traité avec succès</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($paiement->description))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Description</h5>
                            <p>{{ $paiement->description }}</p>
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
function validerPaiement() {
    if (confirm('Valider ce paiement?')) {
        alert('Paiement validé avec succès!');
    }
}

function annulerPaiement() {
    if (confirm('Annuler ce paiement?')) {
        alert('Paiement annulé!');
    }
}

function telechargerRecu() {
    alert('Téléchargement du reçu simulé');
}
</script>

@endsection
