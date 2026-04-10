<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KENAM SERVICES - {{ $subject }}</title>
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
            padding: 20px;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
            position: relative;
        }

        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .logo {
            width: 45px;
            height: 22px;
            background: white;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            max-width: 40px;
            max-height: 20px;
            object-fit: contain;
        }

        .email-subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
            font-weight: 400;
        }

        .email-body {
            padding: 40px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .message-intro {
            font-size: 16px;
            color: #495057;
            margin-bottom: 25px;
            line-height: 1.7;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .info-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-left: 4px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .info-card-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .info-card-content {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-urgente {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .priority-haute {
            background: linear-gradient(135deg, #fd7e14, #e8590c);
            color: white;
        }

        .priority-moyenne {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        .priority-basse {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
        }

        .description-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            position: relative;
        }

        .description-box::before {
            content: '"';
            position: absolute;
            top: 10px;
            left: 15px;
            font-size: 40px;
            color: #dee2e6;
            font-family: Georgia, serif;
        }

        .description-content {
            font-size: 16px;
            line-height: 1.8;
            color: #495057;
            font-style: italic;
            position: relative;
            z-index: 1;
        }

        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            margin: 30px 0;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .email-footer {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 30px 40px;
            text-align: center;
        }

        .signature {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.6;
        }

        .signature-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }

        .company-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 13px;
            color: #6c757d;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: #e9ecef;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            margin: 0 5px;
            transition: all 0.3s ease;
            color: #6c757d;
            text-decoration: none;
        }

        .social-links a:hover {
            background: #28a745;
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .email-container {
                border-radius: 8px;
            }

            .email-header {
                padding: 25px 20px;
            }

            .email-body {
                padding: 25px 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .info-card {
                padding: 15px;
            }

            .action-button {
                width: 100%;
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">
                <img src="{{ asset('images/logo-kenam.png') }}" alt="KENAM SERVICES" style="max-width: 160px; max-height: 80px; object-fit: contain;" />
            </div>
            <div class="email-subtitle">{{ $subtitle }}</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">Bonjour {{ $recipientName }},</div>

            <div class="message-intro">
                {{ $introMessage }}
            </div>

            <!-- Informations principales -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-card-title">Référence</div>
                    <div class="info-card-content">{{ $operation->reference_requete }}</div>
                </div>

                <div class="info-card">
                    <div class="info-card-title">Priorité</div>
                    <div class="info-card-content">
                        <span class="priority-badge priority-{{ strtolower($operation->priorite) }}">
                            {{ $operation->priorite }}
                        </span>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-title">Demandeur</div>
                    <div class="info-card-content">{{ optional($operation->user)->name }}</div>
                </div>

                <div class="info-card">
                    <div class="info-card-title">Service</div>
                    <div class="info-card-content">{{ optional($operation->serviceEmetteur)->nom }}</div>
                </div>
            </div>

            <!-- Description -->
            <div class="description-box">
                <div class="description-content">{{ $operation->description }}</div>
            </div>

            <!-- Bouton d'action -->
            @if($showActionBtn)
            <div style="text-align: center;">
                <a href="{{ $actionUrl }}" class="action-button">
                    <i class="fas fa-eye" style="margin-right: 8px;"></i>
                    {{ $actionButtonText }}
                </a>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="signature">
                <div class="signature-name">{{ $senderName }}</div>
                <div>{{ $senderTitle }}</div>
                <div><strong>KENAM SERVICES</strong></div>

                <div class="company-info">
                    <div>Email: {{ $senderEmail }}</div>
                    <div>Téléphone: {{ $senderPhone }}</div>
                    <div>Site web: www.kenamservices.com</div>
                </div>

                <div class="social-links">
                    <a href="#" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
