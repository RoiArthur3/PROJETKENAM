<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Cost Controle - Engin</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 8px; }
        .meta { margin-bottom: 10px; }
        .meta span { margin-right: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px; vertical-align: top; }
        th { background: #f1f5f9; text-align: left; }
        .right { text-align: right; }
        .totals { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Rapport Cost Controle - Engin</h1>
    <div class="meta">
        <span>Genere le: {{ now()->format('d/m/Y H:i') }}</span>
        <span>Periode: {{ $filters['date_from'] ?: '...' }} -> {{ $filters['date_to'] ?: '...' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Engin</th>
                <th>Mission</th>
                <th>Periode mission</th>
                <th class="right">Quantite</th>
                <th class="right">Cout fournisseur</th>
                <th class="right">Montant client</th>
                <th class="right">Marge</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $p)
                @php
                    $mission = $p->mission;
                    $missionStart = optional($mission?->start_at)->format('d/m/Y H:i');
                    $missionEnd = optional($mission?->end_at)->format('d/m/Y H:i');
                    $supplier = (float) ($p->total_supplier_cost ?? 0);
                    $client = (float) ($p->total_client_amount ?? 0);
                    $margin = $client - $supplier;
                @endphp
                <tr>
                    <td>{{ optional($p->date_pointage)->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ optional($p->vehicle)->immatriculation ?? optional($p->vehicle)->name ?? 'Engin inconnu' }}</td>
                    <td>{{ $mission->reference ?? ('Mission #' . ($p->vehicle_mission_id ?? '-')) }}</td>
                    <td>{{ $missionStart ?? '-' }} -> {{ $missionEnd ?? '-' }}</td>
                    <td class="right">{{ number_format((float)($p->quantity ?? 0), 2, ',', ' ') }}</td>
                    <td class="right">{{ number_format($supplier, 0, ',', ' ') }}</td>
                    <td class="right">{{ number_format($client, 0, ',', ' ') }}</td>
                    <td class="right">{{ number_format($margin, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <th>Total lignes</th>
            <th>Total quantite</th>
            <th>Total cout fournisseur</th>
            <th>Total montant client</th>
            <th>Total marge</th>
        </tr>
        <tr>
            <td>{{ number_format($totals['lines'] ?? 0, 0, ',', ' ') }}</td>
            <td>{{ number_format($totals['quantity'] ?? 0, 2, ',', ' ') }}</td>
            <td>{{ number_format($totals['supplier'] ?? 0, 0, ',', ' ') }}</td>
            <td>{{ number_format($totals['client'] ?? 0, 0, ',', ' ') }}</td>
            <td>{{ number_format($totals['margin'] ?? 0, 0, ',', ' ') }}</td>
        </tr>
    </table>
</body>
</html>
