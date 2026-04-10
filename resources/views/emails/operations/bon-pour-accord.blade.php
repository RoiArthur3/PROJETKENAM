<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BON POUR ACCORD - Opération #{{ $operation->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 10px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #3498db; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 BON POUR ACCORD</h1>
            <p>Opération #{{ $operation->id }} - Prête pour paiement</p>
        </div>
        
        <div class="content">
            <p>Bonjour,</p>
            <p>L'opération ci-dessous a été entièrement validée et reçoit le <strong>BON POUR ACCORD</strong>. Elle est maintenant prête pour le paiement.</p>
            
            <div class="info-box">
                <h3>📋 Détails de l'opération</h3>
                <p><strong>Référence :</strong> #{{ $operation->id }}</p>
                <p><strong>Type :</strong> {{ $operation->typeOperation->nom ?? 'N/A' }}</p>
                <p><strong>Montant :</strong> {{ number_format($operation->montant, 0, ',', ' ') }} FCFA</p>
                <p><strong>Demandeur :</strong> {{ $operation->demandeur_name ?? 'N/A' }}</p>
                <p><strong>Date de demande :</strong> {{ $operation->created_at->format('d/m/Y H:i') }}</p>
                @if($commentaire)
                    <p><strong>Commentaire :</strong> {{ $commentaire }}</p>
                @endif
            </div>
            
            <div class="info-box">
                <h3>💰 Informations de paiement</h3>
                <p><strong>Statut actuel :</strong> Approuvé_en_attente_paiement</p>
                <p><strong>Action requise :</strong> Procéder au paiement</p>
            </div>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/validations/to-pay') }}" class="btn">🔗 Voir les opérations à payer</a>
            </p>
        </div>
        
        <div class="footer">
            <p>Cet email est généré automatiquement par le système KENAM SERVICES</p>
            <p>Si vous n'êtes pas concerné par cette opération, veuillez ignorer cet email.</p>
        </div>
    </div>
</body>
</html>
