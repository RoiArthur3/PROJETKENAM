<p>Bonjour,</p>

<p>Une nouvelle facture a été générée à partir d'une proforma.</p>

<p>
    <strong>Facture :</strong> {{ $invoice->invoice_number }}<br>
    <strong>Date :</strong> {{ optional($invoice->issue_date)->format('d/m/Y') }}<br>
    <strong>Client :</strong> {{ optional($invoice->client)->name ?? '—' }}<br>
    <strong>Montant TTC :</strong> {{ number_format((float)$invoice->net_amount, 0, ',', ' ') }} FCFA
</p>

<p>La facture détaillée est jointe à cet email au format PDF.</p>

<p>Cordialement,<br>
Système KENAM SERVICES</p>
