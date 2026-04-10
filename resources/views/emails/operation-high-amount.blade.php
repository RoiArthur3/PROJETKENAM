<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titre }} - KENAM SERVICES</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background-color: #f8f9fa;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            background: linear-gradient(135deg, {{ $couleurUrgence }} 0%, {{ $couleurUrgence }}dd 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .email-header.urgent {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .email-header.high {
            background: linear-gradient(135deg, #fd7e14 0%, #e8590c 100%);
        }

        .email-header.medium {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }

        .urgency-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            backdrop-filter: blur(10px);
        }

        .email-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .email-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .email-body {
            padding: 40px 30px;
            background: #ffffff;
        }

        .alert-box {
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid {{ $couleurUrgence }};
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .alert-box.urgent {
            border-left-color: #dc3545;
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        }

        .alert-box.high {
            border-left-color: #fd7e14;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        }

        .alert-box.medium {
            border-left-color: #ffc107;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        }

        .operation-details {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .operation-details h3 {
            color: {{ $couleurUrgence }};
            font-size: 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .detail-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .detail-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 600;
        }

        .amount-highlight {
            font-size: 24px;
            font-weight: 700;
            color: {{ $couleurUrgence }};
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            margin: 20px 0;
            border: 2px solid {{ $couleurUrgence }};
        }

        .instructions {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .instructions h3 {
            color: #0c5460;
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .instructions ul {
            list-style: none;
            padding: 0;
        }

        .instructions li {
            padding: 8px 0;
            border-bottom: 1px solid #bee5eb;
            color: #0c5460;
            font-size: 14px;
        }

        .instructions li:last-child {
            border-bottom: none;
        }

        .instructions li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }

        .services-section {
            margin: 25px 0;
        }

        .services-section h3 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .service-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .service-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .service-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .service-info {
            flex: 1;
        }

        .service-name {
            font-weight: 600;
            font-size: 13px;
            color: #2c3e50;
        }

        .service-email {
            font-size: 11px;
            color: #6c757d;
        }

        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, {{ $couleurUrgence }} 0%, {{ $couleurUrgence }}dd 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .footer-info {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .footer-contact {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-urgent {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .badge-high {
            background: linear-gradient(135deg, #fd7e14, #e8590c);
            color: white;
        }

        .badge-medium {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }

            .email-header {
                padding: 30px 20px;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .email-body {
                padding: 30px 20px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .service-list {
                grid-template-columns: 1fr;
            }

            .action-button {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header {{ $destinataireType === 'dg' ? 'urgent' : ($destinataireType === 'manager' ? 'high' : 'medium') }}">
            <div class="urgency-badge">
                {{ $niveauUrgence }}
            </div>
            <h1>{{ $titre }}</h1>
            <p>Plateforme de Gestion KENAM SERVICES</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Message principal -->
            <div class="alert-box {{ $destinataireType === 'dg' ? 'urgent' : ($destinataireType === 'manager' ? 'high' : 'medium') }}">
                <p style="font-size: 16px; margin: 0;">
                    {!! $messagePrincipal !!}
                </p>
            </div>

            <!-- Montant en évidence -->
            <div class="amount-highlight">
                <div style="font-size: 14px; color: #6c757d; margin-bottom: 5px;">MONTANT DE L'OPÉRATION</div>
                <div>{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</div>
                <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">
                    Seuil de validation: 1.000.000 FCFA
                </div>
            </div>

            <!-- Détails de l'opération -->
            <div class="operation-details">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    Détails de l'Opération
                </h3>

                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Référence</div>
                        <div class="detail-value">{{ $operation->reference ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Date</div>
                        <div class="detail-value">{{ $operation->date_operation->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Initiateur</div>
                        <div class="detail-value">{{ $operation->initiateur ?? 'Non spécifié' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Statut</div>
                        <div class="detail-value">
                            <span class="badge badge-{{ $destinataireType === 'dg' ? 'urgent' : ($destinataireType === 'manager' ? 'high' : 'medium') }}">
                                {{ $operation->statut ?? 'En validation' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($operation->description)
                <div style="margin-top: 20px;">
                    <div class="detail-label">Description</div>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 5px;">
                        {{ $operation->description }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Services additionnels -->
            @if(count($servicesAdditionnels) > 0)
            <div class="services-section">
                <h3>
                    <i class="fas fa-users-cog"></i>
                    Services Additionnels Notifiés
                </h3>
                <div class="service-list">
                    @foreach($servicesAdditionnels as $service)
                    <div class="service-item">
                        <div class="service-icon" style="background: {{ $service->couleur }}20; color: {{ $service->couleur }};">
                            <i class="{{ $service->icone }}"></i>
                        </div>
                        <div class="service-info">
                            <div class="service-name">{{ $service->nom }}</div>
                            <div class="service-email">{{ $service->email }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Instructions -->
            <div class="instructions">
                <h3>
                    <i class="fas fa-tasks"></i>
                    Instructions
                </h3>
                <ul>
                    @foreach($instructions as $instruction)
                        <li>{{ $instruction }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Bouton d'action -->
            <div style="text-align: center;">
                <a href="{{ url('/login?redirect=' . urlencode('/operations/' . $operation->id)) }}" class="action-button">
                    <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i>
                    Accéder à l'Opération
                </a>
            </div>

            <!-- Message de sécurité -->
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0;">
                <p style="color: #856404; font-size: 14px; margin: 0;">
                    <strong>⚠️ Sécurité :</strong>
                    Cette opération dépasse le seuil autorisé de 1.000.000 FCFA et requiert une validation spéciale.
                    Veuillez vérifier attentivement tous les détails avant de prendre votre décision.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-info">
                <p style="margin-bottom: 10px;">
                    <strong>Cet email a été généré automatiquement</strong><br>
                    par la plateforme KENAM SERVICES - Système de Validation
                </p>
                <p style="font-size: 11px; color: #adb5bd;">
                    Si vous n'êtes pas destinataire de cet email, veuillez le supprimer immédiatement.
                </p>
            </div>

            <div class="footer-contact">
                <strong>KENAM SERVICES</strong><br>
                📧 contact@kenamservices.com<br>
                📱 +225 XX XX XX XX<br>
                🌐 www.kenamservices.com
            </div>
        </div>
    </div>
</body>
</html>
