<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | KENAM Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --kenam-green: #28a745;
            --kenam-green-light: #20c997;
            --kenam-green-dark: #1e7e34;
            --kenam-blue: #007bff;
            --kenam-orange: #fd7e14;
            --kenam-red: #dc3545;
            --kenam-gray-50: #f8f9fa;
            --kenam-gray-100: #e9ecef;
            --kenam-gray-200: #dee2e6;
            --kenam-gray-300: #ced4da;
            --kenam-gray-600: #6c757d;
            --kenam-gray-800: #343a40;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            overflow: hidden;
            background: url("{{ URL::asset('images/login-bg.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            min-height: 600px;
            width: 900px;
            max-width: 95vw;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* Volet gauche - Branding */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--kenam-green), var(--kenam-green-light));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: white;
            position: relative;
            text-align: center;
        }

        .left-panel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("{{ URL::asset('images/login-bg.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.15;
            z-index: 0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 300px;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            max-width: 200px;
            height: auto;
        }

        .brand-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .brand-subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 30px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .welcome-text {
            font-size: 16px;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 400px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .support-info {
            font-size: 14px;
            opacity: 0.8;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .support-info a {
            color: white;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            transition: border-color 0.3s ease;
        }

        .support-info a:hover {
            border-bottom-color: white;
        }

        /* Volet droit - Formulaire */
        .right-panel {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h3 {
            color: var(--kenam-gray-800);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--kenam-gray-600);
            font-size: 16px;
            margin: 0;
        }

        .form-label {
            font-weight: 600;
            color: var(--kenam-gray-800);
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid var(--kenam-gray-300);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--kenam-green);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            outline: 0;
        }

        .input-group-text {
            background-color: var(--kenam-gray-100);
            border: 1px solid var(--kenam-gray-300);
            border-right: none;
            color: var(--kenam-gray-600);
            font-weight: 600;
            border-radius: 8px 0 0 8px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }

        .input-group .form-control:focus {
            border-color: var(--kenam-green);
            border-left: 1px solid var(--kenam-green);
        }

        .btn-login {
            background: var(--kenam-green);
            border-color: var(--kenam-green);
            color: white;
            padding: 14px 20px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            transition: all 0.15s ease-in-out;
            margin-top: 20px;
        }

        .btn-login:hover {
            background: var(--kenam-green-dark);
            border-color: var(--kenam-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert {
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: var(--kenam-red);
            border-left: 4px solid var(--kenam-red);
        }

        .alert-success {
            background-color: #d4edda;
            color: var(--kenam-green-dark);
            border-left: 4px solid var(--kenam-green);
        }

        .form-check-input:checked {
            background-color: var(--kenam-green);
            border-color: var(--kenam-green);
        }

        .help-text {
            font-size: 13px;
            color: var(--kenam-gray-600);
            margin-top: 5px;
        }

        .phone-format {
            background-color: var(--kenam-gray-50);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            color: var(--kenam-gray-600);
            margin-bottom: 20px;
            border-left: 3px solid var(--kenam-green);
        }

        .loading {
            display: none;
        }

        .spinner-border {
            width: 16px;
            height: 16px;
            border-width: 2px;
        }

        .text-muted {
            color: var(--kenam-gray-600) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .login-container {
                flex-direction: column;
                min-height: auto;
                width: 100%;
                max-width: 400px;
            }

            .left-panel {
                min-height: 200px;
                padding: 30px 20px;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            }

            .logo i {
                font-size: 28px;
            }

            .brand-title {
                font-size: 24px;
            }

            .brand-subtitle {
                font-size: 16px;
            }

            .welcome-text {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .right-panel {
                padding: 30px 20px;
            }

            .login-header h3 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .login-container {
                max-width: 350px;
            }

            .left-panel {
                min-height: 180px;
                padding: 20px 15px;
            }

            .logo i {
                font-size: 24px;
            }

            .brand-title {
                font-size: 20px;
            }

            .brand-subtitle {
                font-size: 14px;
            }

            .welcome-text {
                font-size: 13px;
            }

            .support-info {
                font-size: 12px;
            }

            .right-panel {
                padding: 20px 15px;
            }

            .login-header h3 {
                font-size: 20px;
            }

            .login-header p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Volet gauche - Branding -->
        <div class="left-panel">
            <div class="logo-container">
                <div class="logo">
                    <img src="{{ asset('images/logo-kenam.png') }}" alt="KENAM Services" onerror="console.log('Logo error - path: {{ asset('images/logo-kenam.png') }}'); this.style.display='none'; this.nextElementSibling.style.display='block';" onload="console.log('Logo loaded successfully');">
                    <i class="fas fa-building" style="display:none; font-size: 60px; color: var(--kenam-green);"></i>
                </div>
                <h1 class="brand-title">KENAM Services</h1>
                <p class="brand-subtitle">Plateforme de Gestion</p>
            </div>
        </div>

        <!-- Volet droit - Formulaire -->
        <div class="right-panel">
            <div class="login-card">
                <div class="login-header">
                    <h3>Connexion</h3>
                    <p>Accédez à votre compte</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('kenam.login') }}" id="loginForm">
                    @csrf

                    <div class="phone-format">
                        <i class="fas fa-info-circle me-2"></i>
                        Format requis : 10 chiffres (ex: 0102030405)
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone me-2"></i>Numéro de téléphone
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">+225</span>
                            <input type="tel"
                                   class="form-control"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="0102030405"
                                   maxlength="10"
                                   required
                                   autocomplete="tel">
                        </div>
                        <div class="help-text">
                            Entrez votre numéro de téléphone sans l'indicatif pays
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Mot de passe
                        </label>
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               placeholder="••••••••••"
                               required
                               autocomplete="current-password">
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" checked>
                        <label class="form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>

                    <button type="submit" class="btn btn-login" id="submitBtn">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </span>
                        <span class="loading">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Connexion en cours...
                        </span>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Connexion sécurisée • KENAM Services &copy; 2026
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');

            // Formater le numéro de téléphone en temps réel
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Garder uniquement les chiffres

                // Limiter à 10 chiffres
                if (value.length > 10) {
                    value = value.slice(0, 10);
                }

                e.target.value = value;
            });

            // Empêver la soumission avec des espaces
            phoneInput.addEventListener('keydown', function(e) {
                if (e.key === ' ') {
                    e.preventDefault();
                }
            });

            // Gérer la soumission du formulaire
            loginForm.addEventListener('submit', function(e) {
                const phone = phoneInput.value;

                // Validation finale
                if (!/^[0-9]{10}$/.test(phone)) {
                    e.preventDefault();
                    alert('Le numéro de téléphone doit contenir exactement 10 chiffres.');
                    return false;
                }

                // Afficher l'état de chargement
                submitBtn.disabled = true;
                submitBtn.querySelector('.btn-text').style.display = 'none';
                submitBtn.querySelector('.loading').style.display = 'inline-block';
            });

            // Nettoyer le champ téléphone au focus
            phoneInput.addEventListener('focus', function() {
                this.value = this.value.replace(/\D/g, '');
            });

            // Mettre le focus sur le premier champ vide
            if (!phoneInput.value) {
                phoneInput.focus();
            } else {
                document.getElementById('password').focus();
            }
        });
    </script>
</body>
</html>
