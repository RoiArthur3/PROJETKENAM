<div class="compte-resultat-section">
    <h4>Compte de résultat {{ now()->format('Y') }}</h4>

    <table class="table">
        <tr>
            <th colspan="2">Produits</th>
        </tr>
        @foreach($produits as $item)
        <tr>
            <td>{{ $item->libelle }}</td>
            <td class="text-end">{{ number_format($item->montant, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endforeach
        <tr class="fw-bold">
            <td>TOTAL PRODUITS</td>
            <td class="text-end">{{ number_format($produits->sum('montant'), 0, ',', ' ') }} FCFA</td>
        </tr>

        <tr>
            <th colspan="2">Charges</th>
        </tr>
        @foreach($charges as $item)
        <tr>
            <td>{{ $item->libelle }}</td>
            <td class="text-end">{{ number_format($item->montant, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endforeach
        <tr class="fw-bold">
            <td>TOTAL CHARGES</td>
            <td class="text-end">{{ number_format($charges->sum('montant'), 0, ',', ' ') }} FCFA</td>
        </tr>

        <tr class="table-success fw-bold">
            <td>RÉSULTAT</td>
            <td class="text-end">
                {{ number_format($produits->sum('montant') - $charges->sum('montant'), 0, ',', ' ') }} FCFA
            </td>
        </tr>
    </table>
</div>
