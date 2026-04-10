<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background: #0d6efd; color: #fff; padding: 15px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .cta { text-align: center; margin-top: 30px; }
        .btn { background: #198754; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { font-size: 12px; color: #777; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔔 Notification Trésorerie</h2>
        </div>
        <div class="content">
            <p>Bonjour AKANIE,</p>
            <p>Une nouvelle opération a été <strong>entièrement approuvée</strong> et est désormais prête pour le règlement.</p>
            
            <div class="details">
                <p><strong>N° Opération :</strong> {{ $operation->numero_operation ?? $operation->id }}</p>
                <p><strong>Objet :</strong> {{ $operation->titre }}</p>
                <p><strong>Montant :</strong> {{ number_format($operation->montant, 0, ',', ' ') }} FCFA</p>
                <p><strong>Demandeur :</strong> {{ $operation->demandeur_name }}</p>
                <p><strong>Service :</strong> {{ $operation->operationalService->nom ?? 'N/A' }}</p>
            </div>

            <p>Veuillez cliquer sur le bouton ci-dessous pour choisir la caisse qui effectuera le paiement et émettre le Bon Pour Exécution.</p>
            
            <div class="cta">
                <a href="{{ url('/validations/payer') }}" class="btn">💰 Choisir la Caisse</a>
            </div>
        </div>
        <div class="footer">
            <p>Cet email a été envoyé automatiquement par le système de gestion KENAM SERVICES.</p>
        </div>
    </div>
</body>
</html>
