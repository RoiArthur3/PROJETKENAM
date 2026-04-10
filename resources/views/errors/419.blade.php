<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expirée - KENAM Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --kenam-green: #28a745;
            --kenam-green-dark: #1e7e34;
        }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .error-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
            overflow: hidden;
        }
        .error-header {
            background: linear-gradient(135deg, var(--kenam-green), #20c997);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .error-header .logo {
            width: 60px; height: 60px;
            background: white; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .error-header .logo img { width: 45px; height: 45px; object-fit: contain; }
        .error-body { padding: 35px 30px; text-align: center; }
        .error-icon { font-size: 50px; color: #ffc107; margin-bottom: 20px; }
        .error-body h4 { color: #343a40; font-weight: 600; margin-bottom: 12px; }
        .error-body p { color: #6c757d; margin-bottom: 25px; line-height: 1.6; }
        .info-box {
            background: #fff3cd; border-left: 4px solid #ffc107;
            border-radius: 8px; padding: 15px; text-align: left;
            margin-bottom: 25px; font-size: 14px;
        }
        .info-box ul { margin: 8px 0 0; padding-left: 18px; }
        .info-box li { margin-bottom: 4px; color: #856404; }
        .btn-login {
            background: var(--kenam-green); border: none; color: white;
            padding: 12px 30px; border-radius: 8px; font-size: 16px;
            font-weight: 600; text-decoration: none; display: inline-block;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: var(--kenam-green-dark); color: white;
            transform: translateY(-2px); box-shadow: 0 4px 12px rgba(40,167,69,0.3);
        }
        .btn-back {
            background: transparent; border: 1px solid #dee2e6; color: #6c757d;
            padding: 12px 30px; border-radius: 8px; font-size: 16px;
            font-weight: 500; cursor: pointer; transition: all 0.2s;
        }
        .btn-back:hover { background: #f8f9fa; color: #343a40; }
        .error-footer {
            padding: 15px 30px; border-top: 1px solid #e9ecef;
            text-align: center; font-size: 12px; color: #adb5bd;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-header">
            <div class="logo">
                <img src="{{ asset('images/logo-kenam.png') }}" alt="KENAM" onerror="this.parentElement.innerHTML='<i class=\'fas fa-building\' style=\'font-size:28px;color:#28a745\'></i>'">
            </div>
            <h5 class="mb-0" style="font-weight:600;">KENAM Services</h5>
        </div>
        <div class="error-body">
            <div class="error-icon">
                <i class="fas fa-hourglass-end"></i>
            </div>
            <h4>Session expirée</h4>
            <p>
                Pour des raisons de sécurité, votre session a expiré.<br>
                Veuillez actualiser la page ou vous reconnecter.
            </p>

            <div class="info-box">
                <strong><i class="fas fa-info-circle me-1"></i> Pourquoi cela se produit-il ?</strong>
                <ul>
                    <li>Vous êtes resté inactif trop longtemps</li>
                    <li>Vous avez ouvert plusieurs onglets</li>
                    <li>Votre navigateur a bloqué les cookies</li>
                </ul>
            </div>

            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <a href="{{ url('/login') }}" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </a>
                <button type="button" class="btn-back" onclick="location.reload()">
                    <i class="fas fa-redo me-2"></i>Actualiser
                </button>
            </div>
        </div>
        <div class="error-footer">
            <i class="fas fa-shield-alt me-1"></i>
            Protection CSRF — KENAM Services &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
