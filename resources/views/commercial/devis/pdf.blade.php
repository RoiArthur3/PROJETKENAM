<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma {{ $devis->reference }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 10px;
            line-height: 1.3;
        }

        .document-title {
            flex: 1;
            text-align: right;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }

        .document-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-size: 11px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
        }

        .client-section {
            margin: 30px 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .client-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }

        .table th {
            background: #007bff;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #dee2e6;
        }

        .table td {
            padding: 10px 8px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        .table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background: #e3f2fd !important;
            font-weight: bold;
        }

        .grand-total {
            background: #007bff !important;
            color: white !important;
            font-size: 13px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
        }

        .legal-notice {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
            font-size: 10px;
        }

        .signature-section {
            margin-top: 40px;
            text-align: center;
        }

        .signature-box {
            display: inline-block;
            border: 1px solid #dee2e6;
            padding: 40px 60px;
            margin-top: 20px;
            border-radius: 5px;
            background: #f8f9fa;
        }

        .amount-in-words {
            background: #e8f5e8;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    @include('pdf.company-header')

    <div style="display: flex; justify-content: flex-end; margin-bottom: 30px;">
        <div class="document-title" style="width: 100%;">
            <div class="title">PROFORMA</div>
            <div class="document-info" style="width: 300px; float: right;">
                <div class="info-row">
                    <span class="info-label">N° Proforma:</span>
                    <strong>{{ $devis->reference }}</strong>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span>{{ $devis->issue_date ? $devis->issue_date->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Échéance:</span>
                    <span>{{ $devis->due_date ? $devis->due_date->format('d/m/Y') : now()->addDays(30)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Devise:</span>
                    <span>FCFA</span>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- Informations client -->
    <div class="client-section">
        <div class="section-title">Facturer à</div>
        <div class="client-info">
            <strong>{{ $devis->client_name }}</strong><br>
            {{ $devis->client_address ?? 'Adresse du client' }}<br>
            Côte d'Ivoire
        </div>
    </div>

    <!-- Objet -->
    <div class="client-section">
        <div class="section-title">Objet</div>
        <p>{{ $devis->objet }}</p>
    </div>

    <!-- Détail des prestations -->
    <div class="section-title">Détail des Prestations</div>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center" width="5%">N°</th>
                <th width="50%">Désignation</th>
                <th class="text-center" width="10%">Qté</th>
                <th class="text-center" width="15%">Prix Unit.</th>
                <th class="text-center" width="10%">TVA</th>
                <th class="text-center" width="10%">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($devis->prestations) && is_array($devis->prestations))
                @foreach($devis->prestations as $prestation)
                <tr>
                    <td class="text-center">{{ $prestation['numero'] }}</td>
                    <td>
                        <strong>{{ $prestation['description'] }}</strong><br>
                        <small style="color: #666;">{{ $prestation['details'] }}</small>
                    </td>
                    <td class="text-center">{{ number_format($prestation['quantite'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($prestation['prix_unitaire'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-center">{{ $prestation['tva'] }}%</td>
                    <td class="text-right">{{ number_format($prestation['total_ht'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            @else
                <!-- Données par défaut si prestations non définies -->
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <strong>Maintenance préventive mensuelle</strong><br>
                        <small style="color: #666;">Visites techniques programmées avec contrôles complets</small>
                    </td>
                    <td class="text-center">12</td>
                    <td class="text-right">150,000 FCFA</td>
                    <td class="text-center">18%</td>
                    <td class="text-right">1,800,000 FCFA</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>
                        <strong>Réparation d'urgence</strong><br>
                        <small style="color: #666;">Interventions d'urgence 24/7</small>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">500,000 FCFA</td>
                    <td class="text-center">18%</td>
                    <td class="text-right">500,000 FCFA</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>
                        <strong>Pièces de rechange</strong><br>
                        <small style="color: #666;">Fourniture de composants</small>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">200,000 FCFA</td>
                    <td class="text-center">18%</td>
                    <td class="text-right">200,000 FCFA</td>
                </tr>
            @endif

            <!-- Sous-totaux -->
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>SOUS-TOTAL HT</strong></td>
                <td class="text-right"><strong>{{ number_format($devis->montant_ht ?? 2500000, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TVA (18%)</strong></td>
                <td class="text-right"><strong>{{ number_format($devis->tva ?? 450000, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
            <tr class="grand-total">
                <td colspan="5" class="text-right"><strong>TOTAL TTC</strong></td>
                <td class="text-right"><strong>{{ number_format($devis->total_ttc ?? 2950000, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Montant en lettres -->
    <div class="amount-in-words">
        Arrêté la présente proforma à la somme de :
        <strong>Deux millions neuf cent cinquante mille francs CFA</strong>
    </div>

    <!-- Conditions et mentions légales -->
    <div class="legal-notice">
        <strong>Conditions de paiement :</strong> Règlement à 30 jours date d'émission.<br>
        <strong>Pénalités de retard :</strong> 1% par mois de retard.<br>
        <strong>Conformément au Code Général des Impôts et à la loi n°2017-564 du 13 juillet 2017.</strong>
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <div class="signature-box">
            <strong>KENAM SERVICES</strong><br>
            <small>Date: {{ now()->format('d/m/Y') }}</small>
        </div>
    </div>

    @include('pdf.company-footer')
</body>
</html>
