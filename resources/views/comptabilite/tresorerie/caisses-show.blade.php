@extends('layouts.app')

@section('title', 'Détails Caisse - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Caisse</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.caisses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.caisses.edit', $caisse->id) }}" class="btn btn-outline-warning">
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
                                    <td>{{ $caisse->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Nom</th>
                                    <td>{{ $caisse->nom }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $caisse->description }}</td>
                                </tr>
                                <tr>
                                    <th>Devise</th>
                                    <td>{{ $caisse->devise }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge bg-{{ $caisse->statut == 'actif' ? 'success' : 'danger' }}">
                                            {{ ucfirst($caisse->statut) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Responsable</th>
                                    <td>{{ $caisse->responsable }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Situation financière</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Solde Initial</th>
                                    <td>{{ number_format($caisse->solde_initial, 0, ',', ' ') }} {{ $caisse->devise }}</td>
                                </tr>
                                <tr>
                                    <th>Solde Actuel</th>
                                    <td class="fw-bold {{ $caisse->solde_actuel > $caisse->solde_initial ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} {{ $caisse->devise }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Variation</th>
                                    <td class="{{ $caisse->solde_actuel > $caisse->solde_initial ? 'text-success' : 'text-danger' }}">
                                        {{ $caisse->solde_actuel > $caisse->solde_initial ? '+' : '' }}{{ number_format($caisse->solde_actuel - $caisse->solde_initial, 0, ',', ' ') }} {{ $caisse->devise }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Pourcentage</th>
                                    <td>
                                        @php
                                        $variation = $caisse->solde_actuel - $caisse->solde_initial;
                                        $pourcentage = $caisse->solde_initial > 0 ? ($variation / $caisse->solde_initial) * 100 : 0;
                                        @endphp
                                        <span class="{{ $pourcentage >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($pourcentage, 2, ',', ' ') }}%
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Graphique d'évolution -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Évolution du solde</h5>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-{{ $caisse->solde_actuel >= $caisse->solde_initial ? 'success' : 'danger' }}"
                                     role="progressbar"
                                     style="width: {{ min(100, max(0, ($caisse->solde_actuel / ($caisse->solde_initial * 2)) * 100)) }}%">
                                    {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} {{ $caisse->devise }}
                                </div>
                            </div>
                            <small class="text-muted">
                                Solde initial: {{ number_format($caisse->solde_initial, 0, ',', ' ') }} {{ $caisse->devise }}
                            </small>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Actions rapides</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('tresorerie.approvisionnements.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Approvisionner
                                </a>
                                <a href="{{ route('tresorerie.depenses.create') }}" class="btn btn-warning">
                                    <i class="fas fa-minus me-2"></i>Dépenser
                                </a>
                                <a href="{{ route('tresorerie.soldes-caisse') }}" class="btn btn-info">
                                    <i class="fas fa-chart-line me-2"></i>Voir l'historique
                                </a>
                                <button class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(isset($caisse->notes))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Notes</h5>
                            <p>{{ $caisse->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
