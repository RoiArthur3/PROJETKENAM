<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1cc88a; padding: 20px; text-align: center; color: white; border-radius: 5px 5px 0 0; }
        .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; border-top: none; }
        .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #eee; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Requête exécutée</h2>
        </div>
        <div class="content">
            <p>Bonjour {{ $demandeur->name ?? $demandeur->demandeur_name ?? 'Demandeur' }},</p>
            <p>Votre opération <strong>#{{ $operation->id }} - {{ $operation->titre }}</strong> a été <strong>exécutée</strong> (paiement effectué).</p>
            <div class="details">
                <p><strong>Montant :</strong> {{ number_format($operation->montant, 0, ',', ' ') }} FCFA</p>
                <p><strong>Statut :</strong> Payée</p>
                <p><strong>Date de paiement :</strong> {{ $operation->paid_at ? $operation->paid_at->format('d/m/Y H:i') : 'Non renseignée' }}</p>
            </div>
            <p>Merci d'utiliser la plateforme KENAM SERVICES.</p>
        </div>
        <div class="footer">
            <p>Ceci est un message automatique. Ne pas répondre directement à cet email.</p>
        </div>
    </div>
</body>
</html>
