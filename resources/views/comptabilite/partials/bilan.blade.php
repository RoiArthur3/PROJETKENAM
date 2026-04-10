<div class="bilan-section">
    <h4>Bilan au {{ now()->format('d/m/Y') }}</h4>

    <div class="row">
        <div class="col-md-6">
            <h5>Actif</h5>
            <table class="table">
                @foreach($actif as $item)
                <tr>
                    <td>{{ $item->libelle }}</td>
                    <td class="text-end">{{ number_format($item->montant, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
                <tr class="fw-bold">
                    <td>TOTAL ACTIF</td>
                    <td class="text-end">{{ number_format($actif->sum('montant'), 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        </div>

        <div class="col-md-6">
            <h5>Passif</h5>
            <table class="table">
                @foreach($passif as $item)
                <tr>
                    <td>{{ $item->libelle }}</td>
                    <td class="text-end">{{ number_format($item->montant, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
                <tr class="fw-bold">
                    <td>TOTAL PASSIF</td>
                    <td class="text-end">{{ number_format($passif->sum('montant'), 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        </div>
    </div>
</div>
