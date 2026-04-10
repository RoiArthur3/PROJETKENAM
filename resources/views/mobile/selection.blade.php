<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KENAM OPS - Choisir votre version</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ff6b35;
            --primary-dark: #e85a2a;
            --bg-light: #f8f9fa;
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .selection-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        .header-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), #ff8c00);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.2);
        }
        .app-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }
        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            color: inherit;
        }
        .app-card.pwa {
            border-left: 6px solid #007bff;
        }
        .app-card.native {
            border-left: 6px solid var(--primary);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .pwa .icon-box { background-color: #e7f1ff; color: #007bff; }
        .native .icon-box { background-color: #fff0eb; color: var(--primary); }
        
        .content-box h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.15rem;
        }
        .content-box p {
            margin: 5px 0 0;
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        .badge-new {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--primary);
            color: white;
            padding: 4px 20px;
            font-size: 0.7rem;
            font-weight: bold;
            transform: rotate(45deg) translate(15px, -5px);
            width: 100px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="selection-container text-center">
        <div class="header-logo">
            <i class="fas fa-truck-loading"></i>
        </div>
        <h2 class="fw-bold mb-1">KENAM OPS</h2>
        <p class="text-muted mb-5">Choisissez votre expérience mobile</p>

        <!-- Option PWA -->
        <a href="{{ route('mobile.app') }}" class="app-card pwa">
            <div class="icon-box">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="content-box text-start">
                <h4>Version Web (PWA)</h4>
                <p>Accès instantané sans installation. Rapide et fluide.</p>
            </div>
            <i class="fas fa-chevron-right ms-auto text-muted"></i>
        </a>

        <!-- Option APK -->
        <a href="#" class="app-card native" id="apkDownload">
            <div class="badge-new">RECOMMANDÉ</div>
            <div class="icon-box">
                <i class="fab fa-android"></i>
            </div>
            <div class="content-box text-start">
                <h4>Application Native (APK)</h4>
                <p>Performance maximale sur le terrain. Idéal pour Android.</p>
            </div>
            <i class="fas fa-download ms-auto text-muted"></i>
        </a>

        <div class="mt-5 text-start p-4 bg-white rounded-4 shadow-sm border small info-box">
            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Comment installer ?</h6>
            <div class="mb-2">
                <strong>Android :</strong> Cliquez sur les 3 points du navigateur et choisissez <em>"Installer l'application"</em> ou <em>"Ajouter à l'écran d'accueil"</em>.
            </div>
            <div>
                <strong>iPhone (Safari) :</strong> Cliquez sur le bouton <em>Partager</em> (carré avec flèche) et choisissez <em>"Sur l'écran d'accueil"</em>.
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ url('/') }}" class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left me-1"></i> Retour au tableau de bord
            </a>
        </div>
    </div>

    <style>
        .info-box {
            border-left: 4px solid var(--primary) !important;
        }
    </style>

    <script>
        document.getElementById('apkDownload').onclick = function(e) {
            // Placeholder pour le lien APK. À mettre à jour une fois le build fini.
            alert("La version APK est en cours de préparation. Le téléchargement sera disponible sous peu.");
        };
    </script>
</body>
</html>
