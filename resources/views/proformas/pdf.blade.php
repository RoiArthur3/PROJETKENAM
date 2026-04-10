<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma {{ $proforma->reference }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #1B5E20;
            padding-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-info h1 {
            color: #1B5E20;
            margin: 0;
            font-size: 24px;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        .proforma-info {
            text-align: right;
            flex: 1;
        }

        .proforma-info h2 {
            color: #1B5E20;
            margin: 0;
            font-size: 18px;
        }

        .proforma-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        .client-section {
            display: flex;
            margin-bottom: 30px;
        }

        .client-info {
            flex: 1;
            padding-right: 20px;
        }

        .client-info h3 {
            color: #1B5E20;
            margin: 0 0 10px 0;
            font-size: 14px;
        }

        .proforma-details {
            flex: 1;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .proforma-details h3 {
            color: #1B5E20;
            margin: 0 0 10px 0;
            font-size: 14px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
            font-size: 11px;
        }

        .table th {
            background-color: #1B5E20;
            color: white;
            font-weight: 500;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .totals table {
            width: 100%;
        }

        .totals .total-row {
            border-top: 2px solid #1B5E20;
            font-weight: bold;
        }

        .amount-words {
            margin: 20px 0;
            font-style: italic;
            color: #666;
        }

        .conditions {
            margin: 30px 0;
            font-size: 11px;
            line-height: 1.5;
        }

        .conditions h4 {
            color: #1B5E20;
            margin: 0 0 10px 0;
            font-size: 13px;
        }

        .signature {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-3 {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    @include('pdf.company-header')

    <div class="header" style="justify-content: flex-end;">
        <div class="proforma-info">
            <h2>PROFORMA</h2>
            <p><strong>Référence:</strong> {{ $proforma->reference }}</p>
            <p><strong>Date:</strong> {{ $proforma->date_proposition->format('d/m/Y') }}</p>
            <p><strong>Validité:</strong> {{ $proforma->date_validite->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Client Info -->
    <div class="client-section">
        <div class="client-info">
            <h3>DESTINATAIRE</h3>
            @if($proforma->client)
                <p><strong>{{ $proforma->client->name }}</strong></p>
                <p>{{ $proforma->client->address ?? 'Adresse non spécifiée' }}</p>
                <p>{{ $proforma->client->phone ?? '' }}</p>
                <p>{{ $proforma->client->email ?? '' }}</p>
            @else
                <p>Client non spécifié</p>
            @endif
        </div>

        <div class="proforma-details">
            <h3>DÉTAILS DE LA PROFORMA</h3>
            <p><strong>Projet:</strong> {{ $proforma->projet ? $proforma->projet->titre : 'Non spécifié' }}</p>
            <p><strong>Statut:</strong> {{ ucfirst($proforma->statut) }}</p>
        </div>
    </div>

    <!-- Items Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="5%">N°</th>
                <th width="40%">Désignation</th>
                <th width="10%">TVA</th>
                <th width="15%">Prix Unit. HT</th>
                <th width="10%">Quantité</th>
                <th width="10%">Unité</th>
                <th width="10%">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proforma->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->designation }}</td>
                <td>{{ $item->tva }}%</td>
                <td class="text-right">{{ number_format($item->prix_unitaire_ht, 2, ',', ' ') }} FCFA</td>
                <td class="text-right">{{ number_format($item->quantite, 2, ',', ' ') }}</td>
                <td>{{ $item->unite }}</td>
                <td class="text-right">{{ number_format($item->total_ht, 2, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr>
                <td><strong>Total HT :</strong></td>
                <td class="text-right">{{ number_format($proforma->total_ht, 2, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Total TVA :</strong></td>
                <td class="text-right">{{ number_format($proforma->total_tva, 2, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="total-row">
                <td><strong>Total TTC :</strong></td>
                <td class="text-right">{{ number_format($proforma->total_ttc, 2, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>

    <!-- Amount in words -->
    <div class="amount-words">
        <strong>Montant en lettres : </strong>{{ $proforma->montant_lettres }}
    </div>

    <!-- Conditions -->
    @if($proforma->conditions_generales)
    <div class="conditions">
        <h4>Conditions générales :</h4>
        <p>{{ $proforma->conditions_generales }}</p>
    </div>
    @endif

    <!-- Notes -->
    @if($proforma->note)
    <div class="conditions">
        <h4>Notes :</h4>
        <p>{{ $proforma->note }}</p>
    </div>
    @endif

    <!-- Signature -->
    <div class="signature">
        <p>Bon pour accord,</p>
        <br><br>
        <p>_______________________________</p>
        <p>Signature du client</p>
        <p class="mt-3">KENAM SERVICES</p>
        <p>Date: {{ now()->format('d/m/Y') }}</p>
    </div>
    @include('pdf.company-footer')
</body>
</html>
