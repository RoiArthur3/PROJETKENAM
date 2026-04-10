<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Indicateurs Financiers</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1, h2, h3 { margin: 0 0 10px; }
        .meta { margin-bottom: 16px; color: #4b5563; }
        .grid { width: 100%; margin-bottom: 18px; }
        .card { width: 48%; display: inline-block; vertical-align: top; border: 1px solid #d1d5db; border-radius: 6px; padding: 10px; margin: 0 1% 10px 0; box-sizing: border-box; }
        .label { font-size: 10px; text-transform: uppercase; color: #6b7280; }
        .value { font-size: 18px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .section { margin-top: 18px; }
        ul { padding-left: 18px; }
    </style>
</head>
<body>
    <h1>Synthèse des Principaux Indicateurs Financiers</h1>
    <div class="meta">Période du {{ $donnees['debut']->format('d/m/Y') }} au {{ $donnees['fin']->format('d/m/Y') }}</div>

    <div class="grid">
        <div class="card">
            <div class="label">Chiffre d'affaires</div>
            <div class="value">{{ number_format($donnees['chiffre_affaires'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="card">
            <div class="label">Résultat net</div>
            <div class="value">{{ number_format($donnees['resultat_net'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="card">
            <div class="label">Trésorerie disponible</div>
            <div class="value">{{ number_format($donnees['tresorerie_disponible'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="card">
            <div class="label">Besoin en fonds de roulement</div>
            <div class="value">{{ number_format($donnees['besoin_fonds_roulement'], 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    <div class="section">
        <h3>Ratios DGI</h3>
        <table>
            <thead>
                <tr>
                    <th>Indicateur</th>
                    <th>Valeur</th>
                    <th>Explication</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donnees['ratios_dgi'] as $ratio)
                    <tr>
                        <td>{{ $ratio['libelle'] }}</td>
                        <td>
                            @if($ratio['format'] === 'currency')
                                {{ number_format($ratio['valeur'], 0, ',', ' ') }} FCFA
                            @elseif($ratio['format'] === 'percent')
                                {{ number_format($ratio['valeur'], 1, ',', ' ') }} %
                            @else
                                {{ number_format($ratio['valeur'], 2, ',', ' ') }}
                            @endif
                        </td>
                        <td>{{ $ratio['explication'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Ratios SYSCOHADA</h3>
        <table>
            <thead>
                <tr>
                    <th>Indicateur</th>
                    <th>Valeur</th>
                    <th>Explication</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donnees['ratios_syscohada'] as $ratio)
                    <tr>
                        <td>{{ $ratio['libelle'] }}</td>
                        <td>
                            @if($ratio['format'] === 'currency')
                                {{ number_format($ratio['valeur'], 0, ',', ' ') }} FCFA
                            @elseif($ratio['format'] === 'percent')
                                {{ number_format($ratio['valeur'], 1, ',', ' ') }} %
                            @elseif($ratio['format'] === 'days')
                                {{ number_format($ratio['valeur'], 1, ',', ' ') }} jours
                            @else
                                {{ number_format($ratio['valeur'], 2, ',', ' ') }}
                            @endif
                        </td>
                        <td>{{ $ratio['explication'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Points d'attention</h3>
        <ul>
            @foreach($donnees['points_attention'] as $point)
                <li>{{ $point }}</li>
            @endforeach
        </ul>
    </div>
</body>
</html>