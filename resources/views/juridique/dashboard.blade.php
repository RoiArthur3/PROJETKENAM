@extends('layouts.app')

@section('title', 'Dashboard Juridique - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Module Juridique</h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('juridique.documents.index') }}" class="btn btn-outline-secondary">Documents</a>
            <a href="{{ route('juridique.financements.index') }}" class="btn btn-outline-success">Financements</a>
            <a href="{{ route('juridique.offres.index') }}" class="btn btn-outline-info">Offres bancaires</a>
            <a href="{{ route('juridique.echeances.index') }}" class="btn btn-primary">Échéanciers</a>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Contrats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['contrats'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Documents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['documents'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dossiers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['dossiers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Offres</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['offres'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-university fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes financières -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Montant demandé</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['montant_demande'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Montant obtenu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['montant_obtenu'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taux d'acceptation</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tauxAcceptation }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux récents -->
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>Contrats récents</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Titre</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contratsRecents as $contrat)
                                <tr>
                                    <td>{{ $contrat->reference ?? '-' }}</td>
                                    <td>{{ $contrat->titre ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $contrat->statut == 'actif' ? 'success' : 'secondary' }}">
                                            {{ $contrat->statut ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun contrat récent</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <strong>Échéances proches</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Banque</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($echeancesProches as $echeance)
                                <tr>
                                    <td>{{ optional($echeance->date_echeance)->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ optional($echeance->offre)->banque ?? '-' }}</td>
                                    <td>{{ number_format($echeance->mensualite ?? 0, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucune échéance proche</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dossiers récents -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <strong>Dossiers de financement récents</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Intitulé</th>
                                <th>Type</th>
                                <th>Montant demandé</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dossiersRecents as $dossier)
                                <tr>
                                    <td>{{ $dossier->reference ?? '-' }}</td>
                                    <td>{{ $dossier->intitule ?? '-' }}</td>
                                    <td>{{ $dossier->type_financement ?? '-' }}</td>
                                    <td>{{ number_format($dossier->montant_demande ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge badge-{{ $dossier->statut == 'approuve' ? 'success' : ($dossier->statut == 'rejete' ? 'danger' : 'warning') }}">
                                            {{ $dossier->statut ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aucun dossier récent</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
