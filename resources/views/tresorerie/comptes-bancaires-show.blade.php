@extends('layouts.app')

@section('title', 'Détails Compte Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Compte Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.comptes-bancaires') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.comptes-bancaires.edit', $compte->id) }}" class="btn btn-outline-warning">
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
                                    <td>{{ $compte->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Nom du compte</th>
                                    <td>{{ $compte->nom }}</td>
                                </tr>
                                <tr>
                                    <th>Banque</th>
                                    <td>
                                        <span class="badge bg-info">{{ $compte->banque }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Numéro de compte</th>
                                    <td><code>{{ $compte->numero_compte }}</code></td>
                                </tr>
                                <tr>
                                    <th>Devise</th>
                                    <td>{{ $compte->devise }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge bg-{{ $compte->statut == 'actif' ? 'success' : 'danger' }}">
                                            {{ ucfirst($compte->statut) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Responsable</th>
                                    <td>{{ $compte->responsable }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Situation financière</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Solde actuel</th>
                                    <td class="fw-bold text-success">{{ number_format($compte->solde, 0, ',', ' ') }} {{ $compte->devise }}</td>
                                </tr>
                                <tr>
                                    <th>Date d'ouverture</th>
                                    <td>{{ \Carbon\Carbon::parse($compte->date_ouverture ?? now())->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Dernière opération</th>
                                    <td>{{ now()->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Type de compte</th>
                                    <td>Compte courant</td>
                                </tr>
                                <tr>
                                    <th>Frais de tenue</th>
                                    <td>5 000 FCFA / mois</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Graphique d'évolution -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Évolution du solde</h5>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%">
                                    {{ number_format($compte->solde, 0, ',', ' ') }} {{ $compte->devise }}
                                </div>
                            </div>
                            <small class="text-muted">Limite autorisée: {{ number_format($compte->solde * 1.5, 0, ',', ' ') }} {{ $compte->devise }}</small>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Actions rapides</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('tresorerie.virements.create') }}" class="btn btn-info">
                                    <i class="fas fa-exchange-alt me-2"></i>Virement
                                </a>
                                <a href="{{ route('tresorerie.rapprochements.create') }}" class="btn btn-warning">
                                    <i class="fas fa-balance-scale me-2"></i>Rapprochement
                                </a>
                                <button class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Opérations récentes -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted mb-3">Opérations récentes</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Référence</th>
                                            <th>Type</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ now()->subDays(1)->format('d/m/Y') }}</td>
                                            <td>BAN-2024-001</td>
                                            <td><span class="badge bg-success">Crédit</span></td>
                                            <td class="text-success">+500 000 FCFA</td>
                                            <td><span class="badge bg-success">Validé</span></td>
                                        </tr>
                                        <tr>
                                            <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                                            <td>BAN-2024-002</td>
                                            <td><span class="badge bg-danger">Débit</span></td>
                                            <td class="text-danger">-150 000 FCFA</td>
                                            <td><span class="badge bg-success">Validé</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if(isset($compte->notes))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Notes</h5>
                            <p>{{ $compte->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
