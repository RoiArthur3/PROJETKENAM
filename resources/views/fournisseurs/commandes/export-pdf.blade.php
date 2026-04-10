<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export Commandes Fournisseurs</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 14px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f1f5f9; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Commandes Fournisseurs - Mission / Prestation</h1>
    <div class="meta">Genere le {{ $generatedAt->format('d/m/Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Fournisseur</th>
                <th>Base</th>
                <th>Objet</th>
                <th>Date</th>
                <th class="right">Montant TTC</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commandes as $commande)
                @php($premiereLigne = $commande->lignes->first())
                <tr>
                    <td>{{ $commande->reference }}</td>
                    <td>{{ $commande->fournisseur->raison_sociale ?? 'N/A' }}</td>
                    <td>{{ ucfirst($commande->nature_commande ?? 'autre') }}</td>
                    <td>{{ $premiereLigne->designation ?? 'Commande generale' }}</td>
                    <td>{{ $commande->date_commande ? \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') : '-' }}</td>
                    <td class="right">{{ number_format((float) ($commande->montant_ttc ?? 0), 0, ',', ' ') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', (string) $commande->statut)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucune commande trouvee.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
