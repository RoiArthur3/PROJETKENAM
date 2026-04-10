@php
    $entreprise = $entreprise ?? [];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletins de paie - {{ $period->format('m/Y') }}</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .logo img { height: 60px; width: auto; }
        .company-info { text-align: right; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin: 10px 0 5px; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 11px; margin-bottom: 10px; }
        .block { border: 1px solid #000; padding: 6px 8px; margin-bottom: 6px; }
        .block-title { font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th, td { border: 1px solid #000; padding: 3px 4px; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .small { font-size: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@foreach($bulletins as $index => $b)
    <div class="page">
        <div class="header">
            <div class="logo">
                @if(!empty($entreprise['logo_path']) && file_exists($entreprise['logo_path']))
                    <img src="{{ $entreprise['logo_path'] }}" alt="Logo">
                @else
                    <strong>{{ $entreprise['nom'] ?? 'KENAM SERVICES' }}</strong>
                @endif
            </div>
            <div class="company-info small">
                <strong>{{ $entreprise['nom'] ?? 'KENAM SERVICES' }}</strong><br>
                {{ $entreprise['adresse'] ?? '' }}<br>
                @if(!empty($entreprise['telephone']))
                    Tél : {{ $entreprise['telephone'] }}<br>
                @endif
                @if(!empty($entreprise['email']))
                    Email : {{ $entreprise['email'] }}<br>
                @endif
                @if(!empty($entreprise['website']))
                    Web : {{ $entreprise['website'] }}<br>
                @endif
                @if(!empty($entreprise['cnps']))
                    CNPS employeur : {{ $entreprise['cnps'] }}
                @endif
            </div>
        </div>

        <div class="title">Bulletin de paie</div>
        <div class="subtitle">Mois de {{ $b['mois_label'] ?? $period->translatedFormat('F Y') }}</div>

        <div class="block">
            <div class="block-title">Salarié</div>
            <table>
                <tr>
                    <td><strong>Nom / Prénoms :</strong> {{ $b['agent']->name ?? '' }}</td>
                    <td><strong>Matricule :</strong> {{ $b['agent']->id ?? '' }}</td>
                </tr>
                <tr>
                    <td><strong>Emploi :</strong> {{ $b['agent']->role ?? '' }}</td>
                    <td><strong>N° CNPS :</strong> <!-- à renseigner plus tard --></td>
                </tr>
            </table>
        </div>

        <div class="block">
            <div class="block-title">Rémunération</div>
            <table>
                <thead>
                <tr>
                    <th>Libellé</th>
                    <th class="text-right">Montant (FCFA)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Salaire de base</td>
                    <td class="text-right">{{ number_format($b['salaire_base'], 0, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td>Heures supplémentaires</td>
                    <td class="text-right">{{ number_format($b['heures_sup'], 0, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td>Primes</td>
                    <td class="text-right">{{ number_format($b['primes'], 0, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td><strong>Total brut</strong></td>
                    <td class="text-right"><strong>{{ number_format($b['brut'], 0, ',', ' ') }}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="block">
            <div class="block-title">Cotisations et retenues</div>
            <table>
                <thead>
                <tr>
                    <th>Libellé</th>
                    <th class="text-right">Part salariale</th>
                    <th class="text-right">Part patronale</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>CNPS</td>
                    <td class="text-right">{{ number_format($b['cnps_salariale'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($b['cnps_patronale'], 0, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td>Autres retenues (IRPP, AMU, ...)</td>
                    <td class="text-right">{{ number_format($b['autres_retenues'], 0, ',', ' ') }}</td>
                    <td class="text-right">0</td>
                </tr>
                <tr>
                    <td><strong>Total des retenues salariales</strong></td>
                    <td class="text-right"><strong>{{ number_format($b['cnps_salariale'] + $b['autres_retenues'], 0, ',', ' ') }}</strong></td>
                    <td class="text-right"></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="block">
            <table>
                <tr>
                    <td><strong>Net à payer</strong></td>
                    <td class="text-right"><strong>{{ number_format($b['net_a_payer'], 0, ',', ' ') }} FCFA</strong></td>
                </tr>
            </table>
        </div>

        <div class="block">
            <table>
                <tr>
                    <td class="small">Fait à ................................, le ..........................</td>
                </tr>
                <tr>
                    <td>
                        <table style="margin-top: 10px; width: 100%; border: 0;">
                            <tr>
                                <td class="text-center small" style="border:0;">Pour l'employeur</td>
                                <td class="text-center small" style="border:0;">Le salarié</td>
                            </tr>
                            <tr>
                                <td style="border:0; height:40px;"></td>
                                <td style="border:0;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @if($index < count($bulletins) - 1)
        <div class="page-break"></div>
    @endif
@endforeach
</body>
</html>
