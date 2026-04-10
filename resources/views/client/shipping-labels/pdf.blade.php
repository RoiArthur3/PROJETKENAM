<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Étiquette d'Expédition - {{ $shippingLabel->label_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .label-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .label-number {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            background: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        .addresses {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 30px;
        }
        .address-block {
            flex: 1;
            padding: 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
        }
        .address-title {
            font-size: 14px;
            font-weight: bold;
            color: #6b7280;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .address-content {
            font-size: 14px;
            line-height: 1.4;
            color: #1f2937;
        }
        .address-content strong {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #111827;
        }
        .parcel-info {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .parcel-title {
            font-size: 14px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 10px;
        }
        .parcel-content {
            font-size: 13px;
            color: #78350f;
            line-height: 1.4;
        }
        .barcode-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed #9ca3af;
            border-radius: 8px;
        }
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 3px;
            margin: 10px 0;
        }
        .instructions {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .instructions-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .instructions-content {
            font-size: 12px;
            color: #1e3a8a;
            line-height: 1.4;
        }
        .instructions-content ul {
            margin: 0;
            padding-left: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #6b7280;
        }
        .cut-line {
            border-top: 2px dashed #333;
            margin: 20px 0;
            height: 1px;
        }
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="label-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">GROUPAGE PRO</div>
            <div style="font-size: 14px; color: #6b7280;">Étiquette d'Expédition</div>
        </div>

        <!-- Numéro d'étiquette -->
        <div class="label-number">
            {{ $shippingLabel->label_number }}
        </div>

        <!-- Adresses -->
        <div class="addresses">
            <!-- Expéditeur -->
            <div class="address-block">
                <div class="address-title">EXPÉDITEUR</div>
                <div class="address-content">
                    <strong>{{ $shippingLabel->sender_name }}</strong>
                    {{ $shippingLabel->sender_address }}<br>
                    {{ $shippingLabel->sender_postal_code }} {{ $shippingLabel->sender_city }}<br>
                    {{ $shippingLabel->sender_country }}<br>
                    Tel: {{ $shippingLabel->sender_phone }}<br>
                    Email: {{ $shippingLabel->sender_email }}
                </div>
            </div>

            <!-- Destinataire -->
            <div class="address-block">
                <div class="address-title">DESTINATAIRE</div>
                <div class="address-content">
                    @if($shippingLabel->receivingAddress)
                        <strong>{{ $shippingLabel->receivingAddress->name }}</strong>
                        {{ $shippingLabel->receivingAddress->address }}<br>
                        {{ $shippingLabel->receivingAddress->postal_code }} {{ $shippingLabel->receivingAddress->city }}<br>
                        {{ $shippingLabel->receivingAddress->country }}<br>
                        @if($shippingLabel->receivingAddress->contact_person)
                            Contact: {{ $shippingLabel->receivingAddress->contact_person }}<br>
                        @endif
                        @if($shippingLabel->receivingAddress->phone)
                            Tel: {{ $shippingLabel->receivingAddress->phone }}<br>
                        @endif
                    @else
                        <strong>GROUPAGE PRO</strong>
                        Adresse de réception par défaut<br>
                        (Voir documentation en ligne)
                    @endif
                </div>
            </div>
        </div>

        <!-- Informations colis -->
        <div class="parcel-info">
            <div class="parcel-title">INFORMATIONS COLIS</div>
            <div class="parcel-content">
                <strong>Contenu:</strong> {{ $shippingLabel->parcel_contents }}<br>
                @if($shippingLabel->declared_value)
                    <strong>Valeur déclarée:</strong> {{ number_format($shippingLabel->declared_value, 2) }} €<br>
                @endif
                @if($shippingLabel->weight)
                    <strong>Poids:</strong> {{ $shippingLabel->weight }} kg<br>
                @endif
                @if($shippingLabel->purchase_store)
                    <strong>Magasin:</strong> {{ $shippingLabel->purchase_store }}<br>
                @endif
                @if($shippingLabel->purchase_date)
                    <strong>Date d'achat:</strong> {{ $shippingLabel->purchase_date->format('d/m/Y') }}<br>
                @endif
                <strong>Date d'émission:</strong> {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Code barres -->
        <div class="barcode-section">
            <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">CODE DE SUIVI</div>
            <div class="barcode">{{ $shippingLabel->label_number }}</div>
            @if($shippingLabel->tracking_number)
                <div style="font-size: 12px; color: #6b7280; margin-top: 10px;">Tracking: {{ $shippingLabel->tracking_number }}</div>
            @endif
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <div class="instructions-title">INSTRUCTIONS POUR LA BOUTIQUE</div>
            <div class="instructions-content">
                <ul>
                    <li>Coller cette étiquette bien visible sur le colis</li>
                    <li>Expédier le colis à l'adresse du destinataire indiquée</li>
                    <li>Conserver une copie de cette étiquette</li>
                    <li>Le colis sera traité par Groupage Pro à réception</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Groupage Pro - Service d'expédition international</p>
            <p>Généré le {{ now()->format('d/m/Y H:i') }} | Document valable pour l'expédition</p>
        </div>
    </div>

    <!-- Ligne de coupe (optionnel) -->
    <div class="cut-line no-print"></div>

    <!-- Instructions d'impression -->
    <div class="no-print" style="text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280;">
        <p>Pour une meilleure qualité d'impression, utilisez du papier adhésif ou collez cette étiquette sur le colis.</p>
        <p>Assurez-vous que le code barres est bien lisible.</p>
    </div>
</body>
</html>
