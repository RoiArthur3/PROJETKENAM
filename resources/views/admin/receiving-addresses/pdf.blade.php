<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Adresse de Réception - {{ $receivingAddress->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            color: #6b7280;
        }
        .address-card {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 30px;
            margin: 20px 0;
            background: #f9fafb;
        }
        .address-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            text-align: center;
        }
        .address-info {
            font-size: 16px;
            line-height: 1.6;
            color: #374151;
        }
        .address-info strong {
            color: #1f2937;
        }
        .contact-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .instructions {
            margin-top: 20px;
            padding: 15px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
        .barcode {
            text-align: center;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 24px;
            letter-spacing: 2px;
        }
        .qr-placeholder {
            width: 100px;
            height: 100px;
            border: 2px dashed #9ca3af;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">GROUPAGE PRO</div>
        <div class="title">Adresse de Réception Autorisée</div>
    </div>

    <div class="address-card">
        <div class="address-title">{{ $receivingAddress->name }}</div>

        <div class="address-info">
            <p><strong>Adresse complète :</strong></p>
            <p>{{ $receivingAddress->address }}</p>
            <p>{{ $receivingAddress->postal_code }} {{ $receivingAddress->city }}</p>
            <p>{{ $receivingAddress->country }}</p>
        </div>

        @if($receivingAddress->contact_person || $receivingAddress->phone || $receivingAddress->email)
            <div class="contact-info">
                <div class="address-info">
                    @if($receivingAddress->contact_person)
                        <p><strong>Contact :</strong> {{ $receivingAddress->contact_person }}</p>
                    @endif
                    @if($receivingAddress->phone)
                        <p><strong>Téléphone :</strong> {{ $receivingAddress->phone }}</p>
                    @endif
                    @if($receivingAddress->email)
                        <p><strong>Email :</strong> {{ $receivingAddress->email }}</p>
                    @endif
                </div>
            </div>
        @endif

        @if($receivingAddress->warehouse_code)
            <div class="barcode">
                {{ $receivingAddress->warehouse_code }}
            </div>
        @endif

        @if($receivingAddress->instructions)
            <div class="instructions">
                <strong>Instructions spéciales :</strong><br>
                {{ $receivingAddress->instructions }}
            </div>
        @endif
    </div>

    <div class="qr-placeholder">
        QR Code
    </div>

    <div class="footer">
        <p>Ce document certifie que l'adresse ci-dessus est une adresse de réception officielle de Groupage Pro.</p>
        <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
        <p>Document valable jusqu'à {{ now()->addMonths(6)->format('d/m/Y') }}</p>
    </div>
</body>
</html>
