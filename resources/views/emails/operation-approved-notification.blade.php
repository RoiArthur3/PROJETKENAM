<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification d'Opération Approuvée - KENAM SERVICES</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            border-left: 4px solid #28a745;
        }
        .operation-card h3 {
            margin: 0 0 15px 0;
            color: #28a745;
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
        .approval-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin: 15px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        .final-comment {
            background: #d1ecf1d;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .final-comment h4 {
            margin: 0 0 10px 0;
            color: #0c5460;
            font-size: 16px;
        }
        .final-comment p {
            margin: 0;
            color: #0c5460;
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
            <h1>✅ Opération Approuvée</h1>
            <p>Une opération a été validée et vous est assignée</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $recipientName }}</strong>,</p>

            <p>Nous avons le plaisir de vous informer que l'opération suivante a été complètement validée par tous les validateurs et vous est maintenant assignée pour exécution.</p>

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

                <div class="approval-badge">
                    🎉 Validation Complète - Prêt pour Exécution
                </div>
            </div>

            @if($finalComment)
                <div class="final-comment">
                    <h4>💬 Commentaire Final du Validateur</h4>
                    <p>"{{ $finalComment }}"</p>
                </div>
            @endif

            <p>Pour transmettre le <strong>Bon Pour Accord</strong> au service comptable ou trésorerie, cliquez sur le bouton ci-dessous :</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('operations.bon-pour-accord', $operation->id) }}" style="
                    display: inline-block;
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                    padding: 18px 40px;
                    text-decoration: none;
                    border-radius: 10px;
                    font-weight: 700;
                    font-size: 18px;
                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                    letter-spacing: 0.5px;
                ">
                    📄 Envoyer le Bon Pour Accord
                </a>
            </div>

            <div style="text-align: center; margin-bottom: 20px;">
                <a href="{{ $operationUrl }}" style="color: #007bff; text-decoration: underline; font-size: 14px;">
                    🔍 Voir les détails de l'opération
                </a>
            </div>

            <p style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                <strong>⚠️ Important :</strong> Cliquez sur le bouton ci-dessus pour envoyer le Bon Pour Accord au comptable ou à la trésorerie. L'opération ne sera exécutée qu'après réception de ce bon.
            </p>
        </div>

        <div class="footer">
            <p>Cet email a été généré automatiquement par le système de gestion KENAM SERVICES.</p>
            <p class="signature">🚀 KENAM SERVICES - Solutions Digitales Innovantes</p>
        </div>
    </div>
</body>
</html>
