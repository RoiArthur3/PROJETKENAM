@extends('layouts.app')

@section('title', 'Bilan - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Bilan Comptable</h1>
            <p class="text-muted small mb-0">Rapport financier détaillé pour l'exercice {{ $bilan['annee'] }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-2"></i>Exporter
                </button>
                <ul class="dropdown-menu shadow animate-in" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2 text-danger"></i>Format PDF</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2 text-success"></i>Format Excel</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- KPIs Bilan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Actif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($bilan['actif']['total_actif'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Passif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($bilan['passif']['total_passif'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-{{ $bilan['resultat'] >= 0 ? 'info' : 'danger' }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $bilan['resultat'] >= 0 ? 'info' : 'danger' }} text-uppercase mb-1">Résultat Net</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($bilan['resultat'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Statut Exercice</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ucfirst($bilan['statut']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bilan Détallé -->
    <div class="row">
        <!-- Colonne ACTIF -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">ACTIF (Emplois)</h6>
                    <span class="badge bg-primary rounded-pill">Brut</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Postes</th>
                                    <th class="text-end">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'immobilisations_corporelles') }}';">
                                    <td><i class="fas fa-building me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'immobilisations_corporelles') }}" class="text-decoration-none">Immobilisations corporelles</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['actif']['immobilisations']['total_immobilisations'] * 0.7, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'materiel_mobilier') }}';">
                                    <td><i class="fas fa-laptop me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'materiel_mobilier') }}" class="text-decoration-none">Matériel et mobilier</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['actif']['immobilisations']['total_immobilisations'] * 0.3, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="ps-4 fw-bold italic text-muted">Total Actif Immobilisé</td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($bilan['actif']['immobilisations']['total_immobilisations'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'stocks') }}';">
                                    <td><i class="fas fa-boxes me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'stocks') }}" class="text-decoration-none">Stocks</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['actif']['actif_circulant']['total_actif_circulant'] * 0.3, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'creances_clients') }}';">
                                    <td><i class="fas fa-user-tag me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'creances_clients') }}" class="text-decoration-none">Créances Clients</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['actif']['actif_circulant']['total_actif_circulant'] * 0.4, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'disponibilites') }}';">
                                    <td><i class="fas fa-wallet me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'disponibilites') }}" class="text-decoration-none">Disponibilités (Trésorerie)</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['actif']['actif_circulant']['total_actif_circulant'] * 0.3, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="ps-4 fw-bold italic text-muted">Total Actif Circulant</td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($bilan['actif']['actif_circulant']['total_actif_circulant'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-top-2">
                                <tr class="bg-primary text-white">
                                    <th class="text-uppercase">TOTAL GENERAL ACTIF</th>
                                    <th class="text-end">{{ number_format($bilan['actif']['total_actif'], 0, ',', ' ') }} FCFA</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne PASSIF -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 font-weight-bold text-success">PASSIF (Ressources)</h6>
                    <span class="badge bg-success rounded-pill">Net</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Postes</th>
                                    <th class="text-end">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'capital_social') }}';">
                                    <td><i class="fas fa-university me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'capital_social') }}" class="text-decoration-none">Capital Social</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['passif']['capitaux_propres']['capital_social'] ?: ($bilan['passif']['total_passif'] * 0.6), 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'reserves') }}';">
                                    <td><i class="fas fa-clock me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'reserves') }}" class="text-decoration-none">Réserves</a></td>
                                    <td class="text-end fw-bold">0 FCFA</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-chart-pie me-2 text-muted"></i>Résultat Net de l'exercice</td>
                                    <td class="text-end fw-bold text-{{ $bilan['resultat'] >= 0 ? 'success' : 'danger' }}">{{ number_format($bilan['resultat'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="ps-4 fw-bold italic text-muted">Total Capitaux Propres</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($bilan['passif']['capitaux_propres']['capital_social'] + $bilan['resultat'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'dettes_fournisseurs') }}';">
                                    <td><i class="fas fa-file-invoice-dollar me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'dettes_fournisseurs') }}" class="text-decoration-none">Dettes Fournisseurs</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['passif']['dettes']['total_dettes'] * 0.5, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'emprunts_bancaires') }}';">
                                    <td><i class="fas fa-piggy-bank me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'emprunts_bancaires') }}" class="text-decoration-none">Emprunts bancaires</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['passif']['dettes']['total_dettes'] * 0.4, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('comptabilite.rapports.bilan-detail', 'dettes_fiscales_sociales') }}';">
                                    <td><i class="fas fa-hand-holding-usd me-2 text-muted"></i><a href="{{ route('comptabilite.rapports.bilan-detail', 'dettes_fiscales_sociales') }}" class="text-decoration-none">Dettes fiscales et sociales</a></td>
                                    <td class="text-end fw-bold">{{ number_format($bilan['passif']['dettes']['total_dettes'] * 0.1, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="ps-4 fw-bold italic text-muted">Total Dettes</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($bilan['passif']['dettes']['total_dettes'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-top-2">
                                <tr class="bg-success text-white">
                                    <th class="text-uppercase">TOTAL GENERAL PASSIF</th>
                                    <th class="text-end">{{ number_format($bilan['passif']['total_passif'], 0, ',', ' ') }} FCFA</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Indicateurs de Performance -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-info">Analyse de Performance</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small font-weight-bold">Ratio de Solvabilité <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Capacité à couvrir les dettes par l'actif"></i></span>
                            <span class="small font-weight-bold text-success">2.5</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small font-weight-bold">Indépendance Financière</span>
                            <span class="small font-weight-bold text-info">65%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small font-weight-bold">Niveau d'Endettement</span>
                            <span class="small font-weight-bold text-warning">35%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
    .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
    .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
    .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
    .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
    
    .text-xs { font-size: .7rem; }
    .italic { font-style: italic; }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    
    .animate-in {
        animation: fadeInUp 0.3s ease-out;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush
@endsection
