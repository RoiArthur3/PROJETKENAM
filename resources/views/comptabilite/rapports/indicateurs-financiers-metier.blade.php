@extends('layouts.app')

@section('title', 'Indicateurs Financiers - Analyse Métier - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Indicateurs Financiers - Analyse par Métier</h1>
            <p class="text-muted mb-0">
                Période du {{ $donnees['debut']->format('d/m/Y') }} au {{ $donnees['fin']->format('d/m/Y') }}
                @if($donnees['has_previous_data'])
                    | Comparaison Année N et Année N-1
                @else
                    | Données de première année (pas de comparaison N-1)
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                Dashboard
            </a>
            <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('comptabilite.rapports.indicateurs-financiers') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="periode" class="form-label">Période</label>
                    <select name="periode" id="periode" class="form-select">
                        <option value="mois" {{ $donnees['periode'] === 'mois' ? 'selected' : '' }}>Mois en cours</option>
                        <option value="trimestre" {{ $donnees['periode'] === 'trimestre' ? 'selected' : '' }}>Trimestre en cours</option>
                        <option value="annee" {{ $donnees['periode'] === 'annee' ? 'selected' : '' }}>Année en cours</option>
                        <option value="personnalise" {{ $donnees['periode'] === 'personnalise' ? 'selected' : '' }}>Période personnalisée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut', $donnees['debut']->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin', $donnees['fin']->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-sync-alt me-2"></i>Actualiser
                    </button>
                    <a href="{{ route('comptabilite.rapports.indicateurs-financiers') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs Principaux par Métier -->
    <div class="row g-3 mb-4">
        <!-- Location d'engins et véhicules -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-dolly text-primary me-2"></i>Location d'Engins & Véhicules
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <div class="text-muted small mb-1">Année N</div>
                            <div class="fs-5 fw-bold">
                                {{ number_format($donnees['ca_locations'] ?? 0, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        @if($donnees['has_previous_data'])
                            <div class="text-end">
                                <div class="text-muted small mb-1">Année N-1</div>
                                <div class="fs-5 fw-bold">
                                    {{ number_format($donnees['ca_locations_n1'] ?? 0, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($donnees['has_previous_data'])
                        <div class="alert alert-sm {{ ($donnees['variation_ca_locations'] ?? 0) >= 0 ? 'alert-success' : 'alert-danger' }} py-2 px-3 mb-0">
                            <small>
                                Variation:
                                <strong>
                                    {{ ($donnees['variation_ca_locations'] ?? 0) >= 0 ? '+' : '' }}
                                    {{ number_format($donnees['variation_ca_locations'] ?? 0, 0, ',', ' ') }} FCFA
                                    ({{ ($donnees['ca_locations_n1'] ?? 0) != 0 ? number_format((($donnees['variation_ca_locations'] ?? 0) / ($donnees['ca_locations_n1'] ?? 1)) * 100, 2, ',', '') : '—' }}%)
                                </strong>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Ventes quincaillerie et magasin -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-shopping-cart text-success me-2"></i>Ventes Quincaillerie & Magasin
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <div class="text-muted small mb-1">Année N</div>
                            <div class="fs-5 fw-bold">
                                {{ number_format($donnees['ca_ventes'] ?? 0, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        @if($donnees['has_previous_data'])
                            <div class="text-end">
                                <div class="text-muted small mb-1">Année N-1</div>
                                <div class="fs-5 fw-bold">
                                    {{ number_format($donnees['ca_ventes_n1'] ?? 0, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($donnees['has_previous_data'])
                        <div class="alert alert-sm {{ ($donnees['variation_ca_ventes'] ?? 0) >= 0 ? 'alert-success' : 'alert-danger' }} py-2 px-3 mb-0">
                            <small>
                                Variation:
                                <strong>
                                    {{ ($donnees['variation_ca_ventes'] ?? 0) >= 0 ? '+' : '' }}
                                    {{ number_format($donnees['variation_ca_ventes'] ?? 0, 0, ',', ' ') }} FCFA
                                    ({{ ($donnees['ca_ventes_n1'] ?? 0) != 0 ? number_format((($donnees['variation_ca_ventes'] ?? 0) / ($donnees['ca_ventes_n1'] ?? 1)) * 100, 2, ',', '') : '—' }}%)
                                </strong>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- CA Total et EBE -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-success-subtle">
                <div class="card-header bg-success-subtle">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie text-success me-2"></i>Chiffre d'Affaires Total
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <div class="text-muted small mb-1">Année N</div>
                            <div class="fs-5 fw-bold">
                                {{ number_format($donnees['chiffre_affaires'] ?? 0, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        @if($donnees['has_previous_data'])
                            <div class="text-end">
                                <div class="text-muted small mb-1">Année N-1</div>
                                <div class="fs-5 fw-bold">
                                    {{ number_format(($donnees['chiffre_affaires'] ?? 0) - ($donnees['variation_ca'] ?? 0), 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($donnees['has_previous_data'])
                        <div class="alert alert-sm {{ ($donnees['variation_ca'] ?? 0) >= 0 ? 'alert-success' : 'alert-danger' }} py-2 px-3 mb-0">
                            <small>
                                Variation:
                                <strong>
                                    {{ ($donnees['variation_ca'] ?? 0) >= 0 ? '+' : '' }}
                                    {{ number_format($donnees['variation_ca'] ?? 0, 0, ',', ' ') }} FCFA
                                </strong>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 {{ ($donnees['ebe'] ?? 0) >= 0 ? 'bg-info-subtle' : 'bg-danger-subtle' }}">
                <div class="card-header {{ ($donnees['ebe'] ?? 0) >= 0 ? 'bg-info-subtle' : 'bg-danger-subtle' }}">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar {{ ($donnees['ebe'] ?? 0) >= 0 ? 'text-info' : 'text-danger' }} me-2"></i>
                        EBE (Excédent Brut d'Exploitation)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <div class="text-muted small mb-1">Année N</div>
                            <div class="fs-5 fw-bold {{ ($donnees['ebe'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($donnees['ebe'] ?? 0, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                        @if($donnees['has_previous_data'])
                            <div class="text-end">
                                <div class="text-muted small mb-1">Année N-1</div>
                                <div class="fs-5 fw-bold {{ ($donnees['ebe_n1'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($donnees['ebe_n1'] ?? 0, 0, ',', ' ') }} <small class="text-muted">FCFA</small>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($donnees['has_previous_data'])
                        <div class="alert alert-sm {{ ($donnees['variation_ebe'] ?? 0) >= 0 ? 'alert-success' : 'alert-danger' }} py-2 px-3 mb-0">
                            <small>
                                Variation:
                                <strong>
                                    {{ ($donnees['variation_ebe'] ?? 0) >= 0 ? '+' : '' }}
                                    {{ number_format($donnees['variation_ebe'] ?? 0, 0, ',', ' ') }} FCFA
                                </strong>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau Comparatif Détaillé -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Tableau Comparatif - Analyse {{ $donnees['has_previous_data'] ? 'N vs N-1' : 'Année N' }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-start">Indicateur</th>
                        <th class="text-end">Année N</th>
                        @if($donnees['has_previous_data'])
                            <th class="text-end">Année N-1</th>
                            <th class="text-end">Variation</th>
                            <th class="text-end">Variation %</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-start fw-bold">📦 Location d'engins & véhicules</td>
                        <td class="text-end">{{ number_format($donnees['ca_locations'] ?? 0, 0, ',', ' ') }} FCFA</td>
                        @if($donnees['has_previous_data'])
                            <td class="text-end">{{ number_format($donnees['ca_locations_n1'] ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end {{ ($donnees['variation_ca_locations'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($donnees['variation_ca_locations'] ?? 0) >= 0 ? '+' : '' }}
                                {{ number_format($donnees['variation_ca_locations'] ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end {{ ($donnees['variation_ca_locations'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($donnees['ca_locations_n1'] ?? 0) != 0 ? (($donnees['variation_ca_locations'] ?? 0) >= 0 ? '+' : '') . number_format((($donnees['variation_ca_locations'] ?? 0) / ($donnees['ca_locations_n1'] ?? 1)) * 100, 2, ',', '') : '—' }}%
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td class="text-start fw-bold">🛒 Ventes quincaillerie & magasin</td>
                        <td class="text-end">{{ number_format($donnees['ca_ventes'] ?? 0, 0, ',', ' ') }} FCFA</td>
                        @if($donnees['has_previous_data'])
                            <td class="text-end">{{ number_format($donnees['ca_ventes_n1'] ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end {{ ($donnees['variation_ca_ventes'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($donnees['variation_ca_ventes'] ?? 0) >= 0 ? '+' : '' }}
                                {{ number_format($donnees['variation_ca_ventes'] ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end {{ ($donnees['variation_ca_ventes'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($donnees['ca_ventes_n1'] ?? 0) != 0 ? (($donnees['variation_ca_ventes'] ?? 0) >= 0 ? '+' : '') . number_format((($donnees['variation_ca_ventes'] ?? 0) / ($donnees['ca_ventes_n1'] ?? 1)) * 100, 2, ',', '') : '—' }}%
                            </td>
                        @endif
                    </tr>
                    <tr class="table-light fw-bold">
                        <td class="text-start">💰 CA Total</td>
                        <td class="text-end">{{ number_format($donnees['chiffre_affaires'] ?? 0, 0, ',', ' ') }} FCFA</td>
                        @if($donnees['has_previous_data'])
                            <td class="text-end">{{ number_format(($donnees['chiffre_affaires'] ?? 0) - ($donnees['variation_ca'] ?? 0), 0, ',', ' ') }} FCFA</td>
                            <td class="text-end {{ ($donnees['variation_ca'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($donnees['variation_ca'] ?? 0) >= 0 ? '+' : '' }}
                                {{ number_format($donnees['variation_ca'] ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end {{ ($donnees['variation_ca'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ (($donnees['chiffre_affaires'] ?? 0) - ($donnees['variation_ca'] ?? 0)) != 0 ? (($donnees['variation_ca'] ?? 0) >= 0 ? '+' : '') . number_format((($donnees['variation_ca'] ?? 0) / (($donnees['chiffre_affaires'] ?? 0) - ($donnees['variation_ca'] ?? 0))) * 100, 2, ',', '') : '—' }}%
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td class="text-start fw-bold">📊 EBE (Excédent Brut d'Exploitation)</td>
                        <td class="text-end {{ ($donnees['ebe'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($donnees['ebe'] ?? 0, 0, ',', ' ') }} FCFA
                        </td>
                        @if($donnees['has_previous_data'])
                            <td class="text-end {{ ($donnees['ebe_n1'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($donnees['ebe_n1'] ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end {{ ($donnees['variation_ebe'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($donnees['variation_ebe'] ?? 0) >= 0 ? '+' : '' }}
                                {{ number_format($donnees['variation_ebe'] ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end {{ ($donnees['variation_ebe'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($donnees['ebe_n1'] ?? 0) != 0 ? (($donnees['variation_ebe'] ?? 0) >= 0 ? '+' : '') . number_format((($donnees['variation_ebe'] ?? 0) / ($donnees['ebe_n1'] ?? 1)) * 100, 2, ',', '') : '—' }}%
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td class="text-start">Résultat net</td>
                        <td class="text-end">{{ number_format($donnees['resultat_net'] ?? 0, 0, ',', ' ') }} FCFA</td>
                        @if($donnees['has_previous_data'])
                            <td class="text-end">{{ number_format(($donnees['resultat_net'] ?? 0) - ($donnees['variation_resultat'] ?? 0), 0, ',', ' ') }} FCFA</td>
                            <td class="text-end {{ ($donnees['variation_resultat'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($donnees['variation_resultat'] ?? 0) >= 0 ? '+' : '' }}
                                {{ number_format($donnees['variation_resultat'] ?? 0, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end {{ ($donnees['variation_resultat'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ (($donnees['resultat_net'] ?? 0) - ($donnees['variation_resultat'] ?? 0)) != 0 ? (($donnees['variation_resultat'] ?? 0) >= 0 ? '+' : '') . number_format((($donnees['variation_resultat'] ?? 0) / (($donnees['resultat_net'] ?? 0) - ($donnees['variation_resultat'] ?? 0))) * 100, 2, ',', '') : '—' }}%
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if($donnees['has_previous_data'])
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note :</strong> La comparaison N vs N-1 est basée sur une période équivalente (même nombre de jours) de l'année précédente.
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Note :</strong> Données première année - pas de comparaison N-1 disponible. Les colonnes N-1 et variations ne s'affichent que lors d'une comparaison multi-années.
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .alert-sm {
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    .table-responsive {
        border-radius: 0.25rem;
    }
    
    .card-header.bg-light.bg-success-subtle {
        background-color: rgba(198, 239, 206, 0.5) !important;
    }
    
    .card-header.bg-light.bg-info-subtle {
        background-color: rgba(207, 236, 247, 0.5) !important;
    }
</style>
@endpush
