<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification de Validation - KENAM SERVICES</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .operation-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .operation-card h3 {
            margin: 0 0 15px 0;
            color: #007bff;
            font-size: 20px;
        }
        .operation-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            font-size: 16px;
        }
        .step-badge {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin: 15px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        .previous-comment {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .previous-comment h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }
        .previous-comment p {
            margin: 0;
            color: #856404;
            font-style: italic;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .footer .signature {
            font-weight: 600;
            color: #007bff;
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .operation-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Notification de Validation</h1>
            <p>Une opération requiert votre attention</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $validatorName }}</strong>,</p>

            <p>Une opération a été validée à l'étape précédente et requiert maintenant votre intervention pour la {{ $stepText }}.</p>

            <div class="operation-card">
                <h3>📋 Détails de l'Opération</h3>

                <div class="operation-info">
                    <div class="info-item">
                        <span class="info-label">Référence</span>
                        <span class="info-value">{{ $operationRef }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Montant</span>
                        <span class="info-value">{{ $operationAmount }}</span>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="info-label">Titre</span>
                        <span class="info-value">{{ $operationTitle }}</span>
                    </div>
                    @if($operation->description)
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="info-label">Description</span>
                        <span class="info-value">{{ $operation->description }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <span class="info-label">Date de demande</span>
                        <span class="info-value">{{ $operation->created_at ? $operation->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                    @if($operation->echeance)
                    <div class="info-item">
                        <span class="info-label">Date d'échéance</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>

                <div class="step-badge">
                    📍 {{ $stepText }}
                </div>
            </div>

            @if($previousComment)
                <div class="previous-comment">
                    <h4>💬 Commentaire du validateur précédent</h4>
                    <p>"{{ $previousComment }}"</p>
                </div>
            @endif

            <p>Veuillez cliquer sur le bouton ci-dessous pour accéder à la page de validation et prendre votre décision :</p>

            <div style="text-align: center;">
                <a href="{{ $validationUrl }}" class="cta-button">
                    ✅ Accéder à la Validation
                </a>
            </div>

            <p style="margin-top: 30px; padding: 15px; background: #e3f2fd; border-radius: 8px; border-left: 4px solid #2196f3;">
                <strong>⏰ Important :</strong> Cette opération a une date d'échéance. Si elle n'est pas validée avant cette date, elle sera automatiquement expirée.
            </p>
        </div>

        <div class="footer">
            <p>Cet email a été généré automatiquement par le système de gestion KENAM SERVICES.</p>
            <p class="signature">🚀 KENAM SERVICES - Solutions Digitales Innovantes</p>
        </div>
    </div>
</body>
</html>
