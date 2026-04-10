@extends('layouts.app')

@section('title', "ANALYSE DE L'ACTIVITE - SIG & CAF - KENAM SERVICES")

@section('content')
<div class="content-wrapper">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">ANALYSE DE L'ACTIVITE</h1>
            <p class="text-muted mb-0">
                Solde Intermediaire de Gestion (SIG) et Capacite d'Auto Financement (CAF)
                <br>
                Exercice {{ $donnees['annee_n'] }} compare a {{ $donnees['annee_n1'] }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                Dashboard
            </a>
            <a href="{{ route('comptabilite.rapports.analyse-activite.export-excel', ['annee' => $donnees['annee_n']]) }}" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            <a href="{{ route('comptabilite.rapports.analyse-activite.export-pdf', ['annee' => $donnees['annee_n']]) }}" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Filtre Exercice</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('comptabilite.rapports.analyse-activite') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="annee" class="form-label">Annee N</label>
                    <input type="number" min="2000" max="2100" step="1" id="annee" name="annee" class="form-control" value="{{ $donnees['annee_n'] }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt me-2"></i>Actualiser
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-body">
                    <div class="text-muted small">CAF Exercice {{ $donnees['annee_n'] }}</div>
                    <div class="h4 mb-0 fw-bold {{ ($donnees['n']['caf'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($donnees['n']['caf'] ?? 0, 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-body">
                    <div class="text-muted small">CAF Exercice {{ $donnees['annee_n1'] }}</div>
                    <div class="h4 mb-0 fw-bold {{ ($donnees['n1']['caf'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($donnees['n1']['caf'] ?? 0, 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Tableau detaille SIG et CAF (N-1 / N)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-start">Poste</th>
                        <th class="text-end">{{ $donnees['annee_n1'] }}</th>
                        <th class="text-end">{{ $donnees['annee_n'] }}</th>
                        <th class="text-end">Variation</th>
                        <th class="text-end">Variation %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donnees['lignes'] as $ligne)
                        @php
                            $valN1 = (float) ($donnees['n1'][$ligne['key']] ?? 0);
                            $valN = (float) ($donnees['n'][$ligne['key']] ?? 0);
                            $variation = $valN - $valN1;
                            $variationPct = $valN1 != 0
                                ? ($variation / abs($valN1)) * 100
                                : ($valN > 0 ? 100 : ($valN < 0 ? -100 : 0));
                        @endphp
                        <tr class="{{ in_array($ligne['key'], ['ebe', 'resultat_net', 'caf']) ? 'table-light fw-bold' : '' }}">
                            <td class="text-start">{{ $ligne['libelle'] }}</td>
                            <td class="text-end {{ $valN1 >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($valN1, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end {{ $valN >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($valN, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end {{ $variation >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $variation >= 0 ? '+' : '' }}{{ number_format($variation, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end {{ $variationPct >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $variationPct >= 0 ? '+' : '' }}{{ number_format($variationPct, 2, ',', ' ') }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        <strong>Note methodologique:</strong>
        Les dotations et reprises sont estimees a partir des libelles comptables contenant les mots-cles
        "amort", "provision" et "reprise".
    </div>
</div>
@endsection
