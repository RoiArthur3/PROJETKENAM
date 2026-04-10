@extends('layouts.app')

@section('title', "ANALYSE DE RENTABILITE - KENAM SERVICES")

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>
                        <i class="fas fa-percentage me-2"></i>ANALYSE DE RENTABILITE
                    </h2>
                    <small class="text-muted">Comparatif N-1 / N sur les indicateurs de rentabilite</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                        Dashboard
                    </a>
                    <a href="{{ route('comptabilite.rapports.analyse-rentabilite.export-excel', ['annee' => $donnees['annee_n']]) }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </a>
                    <a href="{{ route('comptabilite.rapports.analyse-rentabilite.export-pdf', ['annee' => $donnees['annee_n']]) }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('comptabilite.rapports.analyse-rentabilite') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold" for="annee">Annee N</label>
                        <input type="number" id="annee" name="annee" min="2000" max="2100" class="form-control" value="{{ $donnees['annee_n'] }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>

            <div class="card shadow">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="m-0">
                        <i class="fas fa-chart-line me-2"></i>Analyse Rentabilite {{ $donnees['annee_n1'] }} / {{ $donnees['annee_n'] }}
                    </h5>
                    <span class="badge bg-dark">{{ count($donnees['lignes']) }} indicateurs</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Indicateur</th>
                                    <th class="text-end">{{ $donnees['annee_n1'] }}</th>
                                    <th class="text-end">{{ $donnees['annee_n'] }}</th>
                                    <th class="text-end">Variation</th>
                                    <th class="text-end">Variation %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donnees['lignes'] as $ligne)
                                    @php
                                        $n1 = (float) ($donnees['n1'][$ligne['key']] ?? 0);
                                        $n = (float) ($donnees['n'][$ligne['key']] ?? 0);
                                        $variation = $n - $n1;
                                        $variationPct = $n1 != 0
                                            ? ($variation / abs($n1)) * 100
                                            : ($n > 0 ? 100 : ($n < 0 ? -100 : 0));
                                        $isPercent = $ligne['format'] === 'percent';
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ $ligne['libelle'] }}</td>
                                        <td class="text-end">{{ number_format($n1, 2, ',', ' ') }}{{ $isPercent ? ' %' : ' FCFA' }}</td>
                                        <td class="text-end">{{ number_format($n, 2, ',', ' ') }}{{ $isPercent ? ' %' : ' FCFA' }}</td>
                                        <td class="text-end {{ $variation >= 0 ? 'text-success' : 'text-danger' }}">{{ $variation >= 0 ? '+' : '' }}{{ number_format($variation, 2, ',', ' ') }}{{ $isPercent ? ' %' : ' FCFA' }}</td>
                                        <td class="text-end {{ $variationPct >= 0 ? 'text-success' : 'text-danger' }}">{{ ($variationPct >= 0 ? '+' : '') . number_format($variationPct, 2, ',', ' ') . '%' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
