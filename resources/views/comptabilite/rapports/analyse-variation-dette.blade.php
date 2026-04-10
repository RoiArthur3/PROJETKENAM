@extends('layouts.app')

@section('title', "ANALYSE VARIATION DE LA DETTE - KENAM SERVICES")

@section('content')
<div class="content-wrapper">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">ANALYSE VARIATION DE LA DETTE</h1>
            <p class="text-muted mb-0">Comparatif N-1 / N des dettes et de leur poids financier.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
            <a href="{{ route('comptabilite.rapports.analyse-variation-dette.export-excel', ['annee' => $donnees['annee_n']]) }}" class="btn btn-outline-success"><i class="fas fa-file-excel me-2"></i>Export Excel</a>
            <a href="{{ route('comptabilite.rapports.analyse-variation-dette.export-pdf', ['annee' => $donnees['annee_n']]) }}" class="btn btn-outline-danger"><i class="fas fa-file-pdf me-2"></i>Export PDF</a>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('comptabilite.rapports.analyse-variation-dette') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Annee N</label>
                    <input type="number" name="annee" min="2000" max="2100" class="form-control" value="{{ $donnees['annee_n'] }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Actualiser</button>
                    <a href="{{ route('comptabilite.rapports.analyse-variation-dette') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
@endsection
