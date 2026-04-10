<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Étiquette Colis Alibaba - {{ $parcel->tracking_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }

        .label-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border: 3px solid #000;
            padding: 20px;
            page-break-after: always;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #d97706;
            margin-bottom: 5px;
        }

        .tracking-number {
            font-size: 32px;
            font-weight: bold;
            background: #000;
            color: white;
            padding: 10px;
            text-align: center;
            margin: 20px 0;
            letter-spacing: 3px;
        }

        .section {
            margin: 20px 0;
            border: 2px solid #333;
            padding: 15px;
        }

        .section-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
            background: #f3f4f6;
            padding: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 14px;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
        }

        .transport-mode {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            margin: 15px 0;
            border: 3px solid #000;
        }

        .air { background: #dbeafe; color: #1e40af; }
        .sea { background: #dcfce7; color: #166534; }

        .fragile {
            background: #fef2f2;
            color: #dc2626;
            border: 2px solid #dc2626;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 15px 0;
        }

        .qr-placeholder {
            width: 100px;
            height: 100px;
            border: 2px solid #000;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }

        .warning {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="header">
            <div class="company-name">GROUPAGE INTERNATIONAL</div>
            <div>Service de Groupage de Colis</div>
        </div>

        <div class="tracking-number">
            {{ $parcel->tracking_number }}
        </div>

        <div class="section">
            <div class="section-title">📦 INFORMATIONS DESTINATAIRE</div>
            <div class="info-row">
                <span class="info-label">Nom:</span>
                <span>{{ $parcel->full_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pays:</span>
                <span>{{ $parcel->destination_country }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Téléphone 1:</span>
                <span>{{ $parcel->phone1 }}</span>
            </div>
            @if($parcel->phone2)
            <div class="info-row">
                <span class="info-label">Téléphone 2:</span>
                <span>{{ $parcel->phone2 }}</span>
            </div>
            @endif
            @if($parcel->alibaba_order_number)
            <div class="info-row">
                <span class="info-label">Commande Alibaba:</span>
                <span>{{ $parcel->alibaba_order_number }}</span>
            </div>
            @endif
        </div>

        <div class="transport-mode {{ $parcel->transport_mode }}">
            {{ $parcel->transport_mode_text }}
        </div>

        @if($parcel->fragile)
        <div class="fragile">
            ⚠️ FRAGILE - HANDLE WITH CARE
        </div>
        @endif

        <div class="section">
            <div class="section-title">🏢 ADRESSE DU GROUPEUR</div>
            <div class="info-row">
                <span class="info-label">Société:</span>
                <span>{{ $groupAddress['company'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Adresse:</span>
                <span>{{ $groupAddress['address'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ville:</span>
                <span>{{ $groupAddress['city'] }}, {{ $groupAddress['country'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Téléphone:</span>
                <span>{{ $groupAddress['phone'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span>{{ $groupAddress['email'] }}</span>
            </div>
        </div>

        <div class="warning">
            📋 COLIS À REMETTRE AU GROUPEUR POUR EXPÉDITION INTERNATIONALE
        </div>

        <div class="qr-placeholder">
            QR CODE<br>{{ $parcel->tracking_number }}
        </div>

        @if($parcel->notes)
        <div class="section">
            <div class="section-title">📝 NOTES</div>
            <div>{{ $parcel->notes }}</div>
        </div>
        @endif

        <div class="footer">
            Créé le: {{ $parcel->created_at->format('d/m/Y H:i') }} |
            Statut: {{ strtoupper($parcel->status) }}
        </div>
    </div>
</body>
</html>
