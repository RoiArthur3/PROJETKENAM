<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Accès Refusé - KENAM SERVICES</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: none;
            max-width: 500px;
            width: 100%;
        }

        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .btn-custom {
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="card error-card">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle error-icon"></i>
                </div>

                <h1 class="text-danger mb-3">Accès Refusé</h1>
                <h2 class="text-muted mb-4">Erreur 403</h2>

                <p class="text-muted mb-4 fs-5">
                    Vous n'avez pas accès à ce module, veuillez contacter le support.
                </p>

                <div class="d-grid gap-3">
                    @if(auth()->check())
                        <a href="{{ url('/operations') }}" class="btn btn-primary btn-custom">
                            <i class="fas fa-tasks me-2"></i>Requêtes
                        </a>

                        <a href="{{ url('/fleet') }}" class="btn btn-success btn-custom">
                            <i class="fas fa-car me-2"></i>Parc Auto
                        </a>

                        <button onclick="history.back()" class="btn btn-outline-secondary btn-custom">
                            <i class="fas fa-arrow-left me-2"></i>Page Précédente
                        </button>

                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-custom w-100">
                                <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-custom">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </a>

                        <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-custom">
                            <i class="fas fa-home me-2"></i>Page d'accueil
                        </a>
                    @endif
                </div>

                <hr class="my-4">

                <div class="text-muted small">
                    <p class="mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Si vous pensez qu'il s'agit d'une erreur, contactez votre administrateur système.
                    </p>
                    <p class="mb-0">
                        <strong>Email support:</strong> admin@kenamservices.com
                    </p>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        Code erreur: 403 |
                        Date: {{ now()->format('d/m/Y H:i:s') }} |
                        IP: {{ request()->ip() }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
