<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>{{ config('app.name', 'KENAM SERVICES') }} - Connexion</title>

        <!-- Bootstrap CSS -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                            url('{{ asset('images/login-bg.jpg') }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .login-container {
                max-width: 1000px;
                width: 100%;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 24px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15),
                           0 0 0 1px rgba(255, 255, 255, 0.1) inset;
                overflow: hidden;
                min-height: 600px;
                transition: all 0.3s ease;
            }

            .login-container:hover {
                transform: translateY(-2px);
                box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2),
                           0 0 0 1px rgba(255, 255, 255, 0.2) inset;
            }

            .left-panel {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                color: white;
                padding: 40px 30px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                position: relative;
                overflow: hidden;
                min-height: 600px;
            }

            .left-panel::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                animation: pulse 15s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }

            .logo-section {
                position: relative;
                z-index: 1;
                text-align: center;
                margin-bottom: 30px;
            }

            .logo-section i {
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }

            .logo-section h1 {
                font-weight: 700;
                margin-bottom: 8px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            }

            .logo-section p {
                font-size: 1rem;
                opacity: 0.95;
            }

            .feature-list {
                position: relative;
                z-index: 1;
                list-style: none;
                padding: 0;
            }

            .feature-list li {
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                font-size: 0.95rem;
                padding: 12px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
            }

            .feature-list li:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateX(10px);
            }

            .feature-list li i {
                margin-right: 12px;
                font-size: 1.3rem;
                width: 25px;
                text-align: center;
            }

            .right-panel {
                padding: 40px 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                min-height: 600px;
            }

            .form-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .form-header h2 {
                color: #28a745;
                font-weight: 700;
                margin-bottom: 8px;
                font-size: 1.8rem;
            }

            .form-header p {
                color: #20c997;
                font-size: 0.95rem;
            }

            .form-label {
                font-weight: 600;
                color: #28a745;
                margin-bottom: 8px;
            }

            .form-check-label {
                color: #28a745;
            }

            .form-control {
                border-radius: 10px;
                border: 2px solid #e9ecef;
                padding: 10px 15px;
                font-size: 0.95rem;
                transition: all 0.3s ease;
            }

            .form-control:focus {
                border-color: #28a745;
                box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
            }

            .input-group-text {
                background: #f8f9fa;
                border: 2px solid #e9ecef;
                border-right: none;
                border-radius: 10px 0 0 10px;
            }

            .input-group .form-control {
                border-left: none;
                border-radius: 0 10px 10px 0;
            }

            .btn-login {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                border: none;
                border-radius: 10px;
                padding: 12px;
                font-weight: 600;
                font-size: 1rem;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            }

            .btn-login:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            }

            .forgot-link {
                color: #6c757d;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .forgot-link:hover {
                color: #28a745;
            }

            .alert {
                border-radius: 10px;
                border: none;
            }

            @media (max-width: 768px) {
                .left-panel {
                    padding: 30px 20px;
                    min-height: auto;
                }

                .right-panel {
                    padding: 30px 20px;
                    min-height: auto;
                }

                .logo-section h1 {
                    gap: 10px !important;
                }

                .logo-section h1 span {
                    font-size: 1.3rem !important;
                }

                .logo-section h1 i {
                    font-size: 1.3rem !important;
                }
            }
        </style>
    </head>
    <body>
        @yield('content')

        <!-- Bootstrap JS -->
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script>
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        </script>
    </body>
</html>
