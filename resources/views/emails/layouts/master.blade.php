<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'KENAM SERVICES' }}</title>
    <style>
        /* Variables CSS KENAM */
        :root {
            --kenam-orange: #ff6b35;
            --kenam-orange-dark: #e55a2b;
            --kenam-orange-light: #ff8c42;
            --kenam-green: #16a34a;
            --kenam-green-dark: #15803d;
            --kenam-green-light: #22c55e;
            --kenam-white: #ffffff;
            --kenam-gray: #f8fafc;
            --kenam-text: #1f2937;
            --kenam-text-light: #6b7280;
        }

        /* Reset et styles de base */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--kenam-text);
            max-width: 650px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Container principal */
        .email-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 107, 53, 0.1);
        }

        /* Header avec logo KENAM premium */
        .email-header {
            background: linear-gradient(135deg, var(--kenam-orange) 0%, var(--kenam-orange-light) 50%, var(--kenam-orange-dark) 100%);
            color: white;
            padding: 50px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .email-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .logo-container {
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }

        .logo {
            width: 45px;
            height: 22px;
            background: white;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.4);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logo img {
            max-width: 40px;
            max-height: 20px;
            object-fit: contain;
        }

        .email-title {
            font-size: 32px;
            font-weight: 800;
            margin: 0;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
        }

        .email-subtitle {
            font-size: 18px;
            opacity: 0.95;
            margin: 12px 0 0 0;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        /* Contenu principal premium */
        .email-content {
            background: white;
            padding: 50px 40px;
            position: relative;
        }

        .email-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--kenam-orange), var(--kenam-green));
        }

        .email-content h1 {
            color: var(--kenam-orange);
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 25px;
            border-bottom: 3px solid transparent;
            border-image: linear-gradient(90deg, var(--kenam-orange), var(--kenam-green)) 1;
            padding-bottom: 20px;
            position: relative;
        }

        .email-content h1::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--kenam-orange);
        }

        .email-content h2 {
            color: var(--kenam-text);
            font-size: 22px;
            font-weight: 600;
            margin: 30px 0 20px 0;
            position: relative;
            padding-left: 15px;
        }

        .email-content h2::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: var(--kenam-orange);
            border-radius: 2px;
        }

        .email-content h3 {
            color: var(--kenam-text);
            font-size: 18px;
            font-weight: 600;
            margin: 25px 0 15px 0;
        }

        .email-content p {
            margin-bottom: 20px;
            color: var(--kenam-text);
            line-height: 1.7;
            font-size: 16px;
        }

        .email-content ul, .email-content ol {
            margin: 20px 0;
            padding-left: 25px;
        }

        .email-content li {
            margin-bottom: 12px;
            color: var(--kenam-text);
            line-height: 1.6;
            position: relative;
        }

        .email-content li::before {
            content: '•';
            color: var(--kenam-orange);
            font-weight: bold;
            position: absolute;
            left: -20px;
        }

        /* Cartes d'information premium */
        .info-card {
            background: linear-gradient(135deg, var(--kenam-gray) 0%, white 100%);
            border-left: 5px solid var(--kenam-orange);
            padding: 30px;
            border-radius: 15px;
            margin: 30px 0;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, var(--kenam-orange) 0%, transparent 70%);
            opacity: 0.05;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.15);
            border-left-color: var(--kenam-green);
        }

        .info-card h3 {
            color: var(--kenam-orange);
            margin-top: 0;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .info-card p {
            margin: 12px 0;
            color: var(--kenam-text);
            font-size: 15px;
        }

        .info-card strong {
            color: var(--kenam-text);
            font-weight: 600;
        }

        /* Badges premium */
        .badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3));
            transition: left 0.3s ease;
        }

        .badge:hover::before {
            left: 0;
        }

        .badge-primary {
            background: linear-gradient(135deg, var(--kenam-orange), var(--kenam-orange-light));
            color: white;
            border: 1px solid var(--kenam-orange-dark);
        }

        .badge-success {
            background: linear-gradient(135deg, var(--kenam-green), var(--kenam-green-light));
            color: white;
            border: 1px solid var(--kenam-green-dark);
        }

        .badge-warning {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: white;
            border: 1px solid #d97706;
        }

        .badge-danger {
            background: linear-gradient(135deg, #ef4444, #f87171);
            color: white;
            border: 1px solid #dc2626;
        }

        .badge-info {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            color: white;
            border: 1px solid #4b5563;
        }

        .priority-urgente {
            color: #dc2626;
            font-weight: 700;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(239, 68, 68, 0.1));
            padding: 4px 12px;
            border-radius: 8px;
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .priority-haute {
            color: #d97706;
            font-weight: 700;
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.1), rgba(245, 158, 11, 0.1));
            padding: 4px 12px;
            border-radius: 8px;
            border: 1px solid rgba(217, 119, 6, 0.2);
        }

        .priority-moyenne {
            color: var(--kenam-orange);
            font-weight: 700;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(255, 140, 66, 0.1));
            padding: 4px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }

        .priority-basse {
            color: var(--kenam-green);
            font-weight: 700;
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.1), rgba(34, 197, 94, 0.1));
            padding: 4px 12px;
            border-radius: 8px;
            border: 1px solid rgba(22, 163, 74, 0.2);
        }

        /* Boutons premium avec animations */
        .btn {
            display: inline-block;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
            margin: 15px 10px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            font-size: 16px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3));
            transition: left 0.4s ease;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn:hover::before {
            left: 0;
        }

        .btn:hover::after {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--kenam-orange), var(--kenam-orange-light));
            color: white;
            border: none;
            box-shadow: 0 6px 25px rgba(255, 107, 53, 0.4);
            position: relative;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--kenam-orange-dark), var(--kenam-orange));
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 35px rgba(255, 107, 53, 0.5);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
            border: none;
            box-shadow: 0 6px 25px rgba(107, 114, 128, 0.4);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563, #374151);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 35px rgba(75, 85, 99, 0.5);
        }

        /* Footer premium avec signature KENAM */
        .email-footer {
            background: #e5e7eb;
            color: #1f2937;
            padding: 50px 40px;
            border-radius: 0 0 20px 20px;
            text-align: center;
            box-shadow: 0 -10px 30px rgba(31, 41, 55, 0.1);
            position: relative;
            overflow: hidden;
        }

        .email-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--kenam-orange), var(--kenam-green));
        }

        .signature {
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .signature-logo {
            width: 120px;
            height: 60px;
            background: white;
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 24px;
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.4);
            letter-spacing: 2px;
            margin-bottom: 25px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .signature-logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 48%, rgba(255, 255, 255, 0.2) 52%);
            animation: shine 4s ease-in-out infinite;
        }

        .signature-logo span:first-child {
            color: var(--kenam-orange);
            text-shadow: 0 2px 4px rgba(255, 107, 53, 0.3);
        }

        .signature-logo span:last-child {
            color: var(--kenam-green);
            text-shadow: 0 2px 4px rgba(22, 163, 74, 0.3);
        }

        .signature-title {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            color: #1f2937;
            text-shadow: none;
            letter-spacing: 1px;
        }

        .signature-subtitle {
            font-size: 16px;
            opacity: 0.7;
            margin: 10px 0 25px 0;
            font-weight: 500;
            color: #4b5563;
        }

        .contact-info {
            background: rgba(31, 41, 55, 0.05);
            border-radius: 15px;
            padding: 30px;
            margin: 25px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(31, 41, 55, 0.1);
            position: relative;
            z-index: 2;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 15px 0;
            color: #1f2937;
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 500;
        }

        .contact-item:hover {
            background: rgba(31, 41, 55, 0.1);
            transform: translateY(-2px);
        }

        .contact-item i {
            margin-right: 15px;
            font-size: 20px;
        }

        .footer-links {
            font-size: 13px;
            opacity: 0.8;
            margin-top: 30px;
            position: relative;
            z-index: 2;
        }

        .footer-links a {
            color: #4b5563;
            text-decoration: none;
            margin: 0 20px;
            transition: all 0.3s ease;
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 500;
        }

        .footer-links a:hover {
            opacity: 1;
            background: rgba(31, 41, 55, 0.1);
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .logo {
                width: 140px;
                height: 70px;
                font-size: 28px;
            }

            .email-title {
                font-size: 24px;
            }

            .email-subtitle {
                font-size: 16px;
            }

            .email-content {
                padding: 30px 20px;
            }

            .email-header {
                padding: 30px 20px;
            }

            .btn {
                display: block;
                margin: 15px 0;
                padding: 14px 24px;
                font-size: 14px;
            }

            .contact-item {
                flex-direction: column;
                text-align: center;
                padding: 15px 20px;
            }

            .contact-item i {
                margin-right: 0;
                margin-bottom: 8px;
                font-size: 18px;
            }

            .signature-logo {
                width: 100px;
                height: 50px;
                font-size: 20px;
            }

            .signature-title {
                font-size: 20px;
            }

            .signature-subtitle {
                font-size: 14px;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body>
    @php
        // Defensive fallback for queued/standalone mail renders where shared view vars may be absent.
        $entrepriseSettings = $entrepriseSettings ?? null;
    @endphp
    <div class="email-container">
    <!-- Header -->
    <div class="email-header">
        <h1 class="email-title">@yield('headerTitle', 'KENAM SERVICES')</h1>
        @hasSection('headerSubtitle')
            <p class="email-subtitle">@yield('headerSubtitle')</p>
        @endif
    </div>

    <!-- Contenu principal -->
    <div class="email-content fade-in">
        @yield('content')
    </div>

    <!-- Footer avec signature -->
    <div class="email-footer">
        <div class="signature">
            <div class="signature-logo">
                @if($entrepriseSettings && $entrepriseSettings->logo_path)
                    <img src="{{ asset('storage/' . $entrepriseSettings->logo_path) }}" alt="{{ $entrepriseSettings->nom_entreprise ?? 'KENAM SERVICES' }}" style="width: 180px; height: 90px; object-fit: contain;" />
                @else
                    <img src="{{ asset('images/logo-kenam.png') }}" alt="KENAM SERVICES" style="width: 180px; height: 90px; object-fit: contain;" />
                @endif
            </div>
            <h2 class="signature-title">{{ $entrepriseSettings?->nom_entreprise ?? 'KENAM SERVICES' }}</h2>
            <p class="signature-subtitle">{{ $entrepriseSettings?->sigle ?? 'Votre Partenaire de Confiance pour les Opérations Commerciales' }}</p>
        </div>

        <div class="contact-info">
            <div class="contact-item" style="display: block; margin-bottom: 20px;">
                <p style="margin: 0; font-weight: 700; color: #1f2937;">KENAM SERVICES</p>
                <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.8;">Star 11 Cocody, Abidjan - Côte d’Ivoire</p>
            </div>

            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
                <a href="mailto:contact@kenamservices.net" class="contact-item" style="margin: 5px; flex: 1; min-width: 200px;">
                    <i class="fas fa-envelope"></i>
                    contact@kenamservices.net
                </a>

                <a href="https://kenamservices.net" class="contact-item" style="margin: 5px; flex: 1; min-width: 200px;">
                    <i class="fas fa-globe"></i>
                    kenamservices.net
                </a>
            </div>

            <div class="contact-item" style="margin-top: 15px; border-top: 1px solid rgba(31, 41, 55, 0.1); padding-top: 15px;">
                <i class="fas fa-phone"></i>
                (+225) 27 22 30 45 71 - (+225) 07 09 48 48 96
            </div>
        </div>

        <div class="footer-links">
            <a href="{{ url('/privacy') }}">Confidentialité</a>
            <a href="{{ url('/terms') }}">Conditions</a>
            <a href="{{ url('/contact') }}">Contact</a>
        </div>

        <p style="margin-top: 20px; font-size: 11px; opacity: 0.6;">
            &copy; {{ date('Y') }} {{ $entrepriseSettings?->nom_entreprise ?? 'KENAM SERVICES' }} - Tous droits réservés<br>
            Cet email a été généré automatiquement, merci de ne pas y répondre.
        </p>
    </div>
</body>
</html>
