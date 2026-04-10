@extends('layouts.app')

@section('title', 'Détail - ' . $titre)

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $titre }}</h1>
            <p class="text-muted small mb-0">Détail et composition du poste de bilan</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.rapports.bilan') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Retour au bilan
            </a>
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erreur!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Détail du Poste -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bars me-2"></i>Détails
                    </h6>
                    <span class="badge bg-info">{{ count($details) }} élément(s)</span>
                </div>
                <div class="card-body">
                    @if ($details && count($details) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        @switch($poste)
                                            @case('immobilisations_corporelles')
                                            @case('materiel_mobilier')
                                                <th>Désignation</th>
                                                <th class="text-end">Valeur Actuelle</th>
                                                <th>Date d'acquisition</th>
                                                <th>Type</th>
                                                @break
                                            @case('stocks')
                                                <th>Désignation</th>
                                                <th class="text-end">Quantité</th>
                                                <th class="text-end">Valeur Unitaire</th>
                                                <th class="text-end">Valeur Totale</th>
                                                @break
                                            @case('creances_clients')
                                                <th>Numéro Facture</th>
                                                <th>Client</th>
                                                <th>Date</th>
                                                <th class="text-end">Montant TTC</th>
                                                <th>Statut</th>
                                                @break
                                            @case('disponibilites')
                                                <th>Compte / Caisse</th>
                                                <th class="text-end">Solde</th>
                                                <th>Banque</th>
                                                @break
                                            @case('dettes_fournisseurs')
                                                <th>Désignation</th>
                                                <th class="text-end">Montant</th>
                                                <th>Date</th>
                                                <th>Fournisseur</th>
                                                <th>Statut</th>
                                                @break
                                            @case('emprunts_bancaires')
                                                <th>Banque</th>
                                                <th class="text-end">Montant Initial</th>
                                                <th class="text-end">Montant Restant</th>
                                                <th class="text-end">Taux %</th>
                                                <th>Date Fin</th>
                                                @break
                                            @default
                                                <th>Désignation</th>
                                                <th class="text-end">Montant</th>
                                        @endswitch
                                    </tr>
                                </thead>
                                <tbody>
                                    @switch($poste)
                                        @case('immobilisations_corporelles')
                                        @case('materiel_mobilier')
                                            @foreach ($details as $detail)
                                                <tr>
                                                    <td>{{ $detail->designation }}</td>
                                                    <td class="text-end fw-bold">{{ number_format($detail->valeur_actuelle, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ $detail->date_acquisition ? \Carbon\Carbon::parse($detail->date_acquisition)->format('d/m/Y') : '-' }}</td>
                                                    <td><span class="badge bg-secondary">{{ ucfirst($detail->type) }}</span></td>
                                                </tr>
                                            @endforeach
                                            @break
                                        @case('stocks')
                                            @foreach ($details as $detail)
                                                <tr>
                                                    <td>{{ $detail->designation }}</td>
                                                    <td class="text-end">{{ number_format($detail->quantite, 0, ',', ' ') }}</td>
                                                    <td class="text-end">{{ number_format($detail->valeur_unitaire, 0, ',', ' ') }} FCFA</td>
                                                    <td class="text-end fw-bold">{{ number_format($detail->valeur_totale, 0, ',', ' ') }} FCFA</td>
                                                </tr>
                                            @endforeach
                                            @break
                                        @case('creances_clients')
                                            @foreach ($details as $detail)
                                                <tr>
                                                    <td><strong>{{ $detail->numero_facture }}</strong></td>
                                                    <td>{{ $detail->client_nom }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($detail->date_facture)->format('d/m/Y') }}</td>
                                                    <td class="text-end fw-bold">{{ number_format($detail->montant_ttc, 0, ',', ' ') }} FCFA</td>
                                                    <td><span class="badge bg-danger">{{ ucfirst(str_replace('_', ' ', $detail->statut)) }}</span></td>
                                                </tr>
                                            @endforeach
                                            @break
                                        @case('disponibilites')
                                            @foreach ($details as $detail)
                                                <tr>
                                                    <td><strong>{{ $detail->numero_compte }}</strong></td>
                                                    <td class="text-end fw-bold">{{ number_format($detail->solde, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ $detail->banque }}</td>
                                                </tr>
                                            @endforeach
                                            @break
                                        @case('dettes_fournisseurs')
                                            @foreach ($details as $detail)
                                                <tr>
                                                    <td>{{ $detail->designation }}</td>
                                                    <td class="text-end fw-bold">{{ number_format($detail->montant, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ $detail->date_facture ? \Carbon\Carbon::parse($detail->date_facture)->format('d/m/Y') : '-' }}</td>
                                                    <td>{{ $detail->fournisseur_nom }}</td>
                                                    <td><span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $detail->statut)) }}</span></td>
                                                </tr>
                                            @endforeach
                                            @break
                                        @case('emprunts_bancaires')
                                            @foreach ($details as $detail)
                                                <tr>
                                                    <td>{{ $detail->banque }}</td>
                                                    <td class="text-end">{{ number_format($detail->montant_initial, 0, ',', ' ') }} FCFA</td>
                                                    <td class="text-end fw-bold">{{ number_format($detail->montant_restant, 0, ',', ' ') }} FCFA</td>
                                                    <td class="text-end">{{ number_format($detail->taux_interet, 2, ',', ' ') }}%</td>
                                                    <td>{{ $detail->date_fin ? \Carbon\Carbon::parse($detail->date_fin)->format('d/m/Y') : '-' }}</td>
                                                </tr>
                                            @endforeach
                                            @break
                                        @default
                                            @foreach ($details as $detail)
                                                <tr>
                                                    <td>{{ $detail->designation ?? 'N/A' }}</td>
                                                    <td class="text-end fw-bold">{{ number_format($detail->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                                </tr>
                                            @endforeach
                                    @endswitch
                                </tbody>
                                <tfoot class="bg-light border-top-2">
                                    <tr class="fw-bold">
                                        <td colspan="@switch($poste)
                                            @case('immobilisations_corporelles')
                                            @case('materiel_mobilier')
                                                3
                                            @break
                                            @case('stocks')
                                            @case('creances_clients')
                                            @case('dettes_fournisseurs')
                                            @case('emprunts_bancaires')
                                                4
                                            @break
                                            @case('disponibilites')
                                                2
                                            @break
                                            @default
                                                1
                                        @endswitch">
                                            TOTAL
                                        </td>
                                        <td class="text-end">
                                            @switch($poste)
                                                @case('immobilisations_corporelles')
                                                @case('materiel_mobilier')
                                                    {{ number_format($details->sum('valeur_actuelle'), 0, ',', ' ') }} FCFA
                                                @break
                                                @case('stocks')
                                                    {{ number_format($details->sum('valeur_totale'), 0, ',', ' ') }} FCFA
                                                @break
                                                @case('creances_clients')
                                                    {{ number_format($details->sum('montant_ttc'), 0, ',', ' ') }} FCFA
                                                @break
                                                @case('disponibilites')
                                                    {{ number_format(collect($details)->sum('solde'), 0, ',', ' ') }} FCFA
                                                @break
                                                @case('dettes_fournisseurs')
                                                    {{ number_format($details->sum('montant'), 0, ',', ' ') }} FCFA
                                                @break
                                                @case('emprunts_bancaires')
                                                    {{ number_format($details->sum('montant_restant'), 0, ',', ' ') }} FCFA
                                                @break
                                                @default
                                                    {{ number_format($details->sum('montant'), 0, ',', ' ') }} FCFA
                                            @endswitch
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Pas de données</strong> - Aucun enregistrement trouvé pour ce poste.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Section Graphique (Optionnel) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Analyse Visuelle
                    </h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">
                        <i class="fas fa-chart-bar fa-3x me-3"></i>
                        Les graphiques seront affichés ici selon le type de poste sélectionné.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .border-top-2 {
        border-top: 2px solid #dee2e6 !important;
    }
    
    .text-gray-800 {
        color: #2e3338;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
@endpush

@endsection
