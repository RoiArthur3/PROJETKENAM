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
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
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

        .service-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }

        .service-name {
            font-size: 20px;
            font-weight: 600;
            color: #28a745;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .info-item {
            background: #ffffff;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 500;
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

        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
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

        .badge-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .badge-info {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }

        .badge-warning {
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

            .info-grid {
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
        <div class="email-header">
            <h1>{{ $titre }}</h1>
            <p>Plateforme de Gestion KENAM SERVICES</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Message principal -->
            <p style="font-size: 16px; margin-bottom: 20px;">
                {!! $message !!}
            </p>

            <!-- Informations du service -->
            <div class="service-info">
                <div class="service-name">
                    <i class="fas fa-cogs" style="margin-right: 10px;"></i>
                    {{ $service->nom }}
                    <span class="badge badge-success" style="margin-left: 10px;">
                        {{ $service->code }}
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email du Service</div>
                        <div class="info-value">{{ $service->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Responsable</div>
                        <div class="info-value">{{ $service->responsable ?? 'Non défini' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">{{ $service->telephone ?? 'Non défini' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Statut</div>
                        <div class="info-value">
                            <span class="badge badge-{{ $service->actif ? 'success' : 'warning' }}">
                                {{ $service->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>
                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                    Instructions
                </h3>
                <ul>
                    @foreach($instructions as $instruction)
                        <li>{{ $instruction }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Bouton d'action -->
            @if($typeNotification === 'reinitialisation')
                <div style="text-align: center;">
                    <a href="{{ url('/login') }}" class="action-button">
                        <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                        Se Connecter
                    </a>
                </div>
            @endif

            <!-- Message de sécurité -->
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0;">
                <p style="color: #856404; font-size: 14px; margin: 0;">
                    <strong>⚠️ Sécurité :</strong>
                    @if($typeNotification === 'reinitialisation')
                        Veuillez changer ce mot de passe lors de votre première connexion pour des raisons de sécurité.
                    @else
                        Ne partagez jamais ces informations d'identification avec des tiers.
                    @endif
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-info">
                <p style="margin-bottom: 10px;">
                    <strong>Cet email a été généré automatiquement</strong><br>
                    par la plateforme KENAM SERVICES
                </p>
                <p style="font-size: 11px; color: #adb5bd;">
                    Si vous n'êtes pas destinataire de cet email, veuillez le supprimer.
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
