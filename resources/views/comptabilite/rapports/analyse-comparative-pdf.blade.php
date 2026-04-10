<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $titre ?? 'Analyse Comparative' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h2 { margin: 0 0 6px 0; }
        .muted { color: #666; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d8d8d8; padding: 7px; }
        th { background: #f2f2f2; text-align: left; }
        td.num, th.num { text-align: right; }
    </style>
</head>
<body>
    <h2>{{ $titre ?? 'Analyse Comparative' }}</h2>
    <div class="muted">Comparatif {{ $donnees['annee_n1'] }} / {{ $donnees['annee_n'] }}</div>

    <table>
        <thead>
            <tr>
                <th>Indicateur</th>
                <th class="num">{{ $donnees['annee_n1'] }}</th>
                <th class="num">{{ $donnees['annee_n'] }}</th>
                <th class="num">Variation</th>
                <th class="num">Variation %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donnees['lignes'] as $ligne)
                @php
                    $n1 = (float) ($donnees['n1'][$ligne['key']] ?? 0);
                    $n = (float) ($donnees['n'][$ligne['key']] ?? 0);
                    $variation = $n - $n1;
                    $variationPct = $n1 != 0 ? ($variation / abs($n1)) * 100 : null;
                @endphp
                <tr>
                    <td>{{ $ligne['libelle'] }}</td>
                    <td class="num">{{ number_format($n1, 2, ',', ' ') }}</td>
                    <td class="num">{{ number_format($n, 2, ',', ' ') }}</td>
                    <td class="num">{{ $variation >= 0 ? '+' : '' }}{{ number_format($variation, 2, ',', ' ') }}</td>
                    <td class="num">
                        @if($variationPct === null)
                            -
                        @else
                            {{ $variationPct >= 0 ? '+' : '' }}{{ number_format($variationPct, 2, ',', ' ') }}%
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
