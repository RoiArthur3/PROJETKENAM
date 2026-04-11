<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture - {{ $projet->titre }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #007bff;
            font-size: 28px;
            margin: 0;
        }

        .header .facture-info {
            float: right;
            text-align: right;
        }

        .facture-number {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .facture-date {
            font-size: 14px;
            color: #666;
        }

        .company-info {
            margin-bottom: 30px;
        }

        .company-info h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .client-info {
            margin-bottom: 30px;
        }

        .client-info h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .project-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .project-info h3 {
            color: #333;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .table-container {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .total-row td {
            border-bottom: 2px solid #007bff;
        }

        .totals {
            text-align: right;
            margin-top: 20px;
        }

        .totals .row {
            margin-bottom: 5px;
        }

        .totals .label {
            display: inline-block;
            width: 200px;
            text-align: right;
            padding-right: 10px;
        }

        .totals .value {
            display: inline-block;
            width: 150px;
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            font-size: 18px;
            color: #007bff;
            border-top: 2px solid #007bff;
            padding-top: 10px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 11px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: #f0f0f0;
            z-index: -1;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="watermark">FACTURE</div>

    <div class="header">
        <div class="facture-info">
            <div class="facture-number">FACTURE N°: {{ str_pad($projet->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="facture-date">Date: {{ date('d/m/Y') }}</div>
        </div>
        <h1>KENAM SERVICES</h1>
        <p>Société de Transport et Logistique<br>
        BP: 123 Abidjan, Côte d'Ivoire<br>
        Tel: +225 27 XX XX XX XX<br>
        Email: contact@kenamservices.net</p>
    </div>

    <div class="company-info">
        <h3>Informations de l'entreprise</h3>
        <p><strong>KENAM SERVICES</strong><br>
        Société de Transport et Logistique<br>
        RCCM: CI-ABJ-2023-B-1234<br>
        CC: 12345678901234567890123456789012345</p>
    </div>

    <div class="client-info">
        <h3>Informations du client</h3>
        <p><strong>{{ $projet->client ? $projet->client->raison_sociale : 'N/A' }}</strong><br>
        Adresse: {{ $projet->client ? $projet->client->adresse : 'N/A' }}<br>
        Tel: {{ $projet->client ? $projet->client->telephone : 'N/A' }}<br>
        Email: {{ $projet->client ? $projet->client->email : 'N/A' }}</p>
    </div>

    <div class="project-info">
        <h3>Détails du projet</h3>
        <p><strong>Titre:</strong> {{ $projet->titre }}<br>
        <strong>Période:</strong>
        {{ $projet->date_debut ? \Carbon\Carbon::parse($projet->date_debut)->format('d/m/Y') : 'N/A' }} -
        {{ $projet->date_fin ? \Carbon\Carbon::parse($projet->date_fin)->format('d/m/Y') : 'N/A' }}<br>
        <strong>Responsable:</strong> {{ $projet->user ? $projet->user->name : 'N/A' }}</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Engin</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pointages as $pointage)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($pointage->date_pointage)->format('d/m/Y') }}</td>
                        <td>{{ $pointage->vehicule ? $pointage->vehicule->immatriculation : 'N/A' }}</td>
                        <td>{{ ucfirst($pointage->unit_type ?? 'N/A') }}</td>
                        <td>{{ number_format($pointage->quantity ?? 0, 2, ' ', ' ') }}</td>
                        <td class="text-right">{{ number_format($pointage->client_unit_price ?? 0, 0, ' ', ' ') }} FCFA</td>
                        <td class="text-right">{{ number_format($pointage->total_client_amount ?? 0, 0, ' ', ' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun pointage trouvé pour la période sélectionnée</td>
                    </tr>
                @endforelse

                <tr class="total-row">
                    <td colspan="5"><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($totaux['total_client'] ?? 0, 0, ' ', ' ') }} FCFA</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="totals">
        <div class="row">
            <span class="label">Total heures/jours:</span>
            <span class="value">{{ number_format($totaux['total_unites'] ?? 0, 2, ' ', ' ') }}</span>
        </div>
        <div class="row">
            <span class="label">Coût fournisseur:</span>
            <span class="value">{{ number_format($totaux['total_fournisseur'] ?? 0, 0, ' ', ' ') }} FCFA</span>
        </div>
        <div class="row">
            <span class="label">Montant client:</span>
            <span class="value">{{ number_format($totaux['total_client'] ?? 0, 0, ' ', ' ') }} FCFA</span>
        </div>
        <div class="row">
            <span class="label">Marge bénéficiaire:</span>
            <span class="value">{{ number_format($totaux['total_marge'] ?? 0, 0, ' ', ' ') }} FCFA</span>
        </div>

        <div class="grand-total">
            <div class="row">
                <span class="label">MONTANT TOTAL À FACTURER:</span>
                <span class="value">{{ number_format($totaux['total_client'] ?? 0, 0, ' ', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Mentions légales:</strong></p>
        <p>Toute facture non payée à sa date d'échéance donnera lieu à un intérêt de retard égal au taux légal.<br>
        En cas de litige, seul le tribunal d'Abidjan est compétent.</p>
        <p class="mt-20"><strong>Merci de votre confiance!</strong></p>
    </div>
</body>
</html>
