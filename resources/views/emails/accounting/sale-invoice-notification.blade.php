<p>Bonjour,</p>

<p>Une nouvelle vente magasin a été réalisée et doit être prise en compte en comptabilité.</p>

<p>
    <strong>Référence vente :</strong> {{ $sale->reference }}<br>
    @if($invoice)
        <strong>Facture :</strong> {{ $invoice->invoice_number }}<br>
    @endif
    <strong>Date :</strong> {{ optional($sale->sale_date)->format('d/m/Y H:i') }}<br>
    <strong>Client :</strong> {{ $sale->customer_name ?? 'Client comptant' }}<br>
    <strong>Montant TTC :</strong> {{ number_format($sale->net_amount, 0, ',', ' ') }} FCFA
</p>

<p>Détails des articles :</p>
<ul>
    @foreach($sale->items as $item)
        <li>
            {{ $item->product->name ?? 'Produit' }} -
            {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA =
            {{ number_format($item->total_amount, 0, ',', ' ') }} FCFA
        </li>
    @endforeach
</ul>

<p>Cordialement,<br>
Système Magasin KENAM SERVICES</p>
