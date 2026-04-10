<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Grand Journal</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin: 0 0 4px 0; }
        .meta { margin-bottom: 12px; color: #4b5563; }
        .stats { margin-bottom: 14px; }
        .stats table { width: 100%; border-collapse: collapse; }
        .stats td { border: 1px solid #e5e7eb; padding: 6px; }
        table.journal { width: 100%; border-collapse: collapse; }
        table.journal th, table.journal td { border: 1px solid #d1d5db; padding: 5px; }
        table.journal th { background: #f3f4f6; text-align: left; }
        .text-right { text-align: right; }
        .small { font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>Grand Journal</h1>
    <div class="meta">
        Période: du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }}
        @if(!empty($filters['journal'])) | Journal: {{ $filters['journal'] }} @endif
        @if(!empty($filters['source'])) | Source: {{ $filters['source'] }} @endif
        @if(!empty($filters['search'])) | Recherche: {{ $filters['search'] }} @endif
    </div>

    <div class="stats">
        <table>
            <tr>
                <td><strong>Écritures</strong><br>{{ number_format($stats['total'], 0, ',', ' ') }}</td>
                <td><strong>Total débit</strong><br>{{ number_format($stats['total_debit'], 0, ',', ' ') }} FCFA</td>
                <td><strong>Total crédit</strong><br>{{ number_format($stats['total_credit'], 0, ',', ' ') }} FCFA</td>
                <td><strong>Solde</strong><br>{{ number_format($stats['solde'], 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <table class="journal">
        <thead>
            <tr>
                <th>Date</th>
                <th>Journal</th>
                <th>Référence</th>
                <th>Libellé</th>
                <th>Compte Débit</th>
                <th>Compte Crédit</th>
                <th class="text-right">Montant</th>
                <th>Source</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td>{{ $entry['journal_code'] }}</td>
                    <td>{{ $entry['reference'] }}@if($entry['piece'])<div class="small">Pièce: {{ $entry['piece'] }}</div>@endif</td>
                    <td>{{ $entry['libelle'] }}</td>
                    <td>{{ $entry['compte_debit'] }}</td>
                    <td>{{ $entry['compte_credit'] }}</td>
                    <td class="text-right">{{ number_format($entry['montant'], 0, ',', ' ') }}</td>
                    <td>{{ $entry['source_type'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Aucune écriture sur la période.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>