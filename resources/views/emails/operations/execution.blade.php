<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4e73df; padding: 20px; text-align: center; color: white; border-radius: 5px 5px 0 0; }
        .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; border-top: none; }
        .badge { display: inline-block; padding: 5px 10px; background: #1cc88a; color: white; border-radius: 4px; font-weight: bold; margin-bottom: 10px; }
        .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; border: 1px solid #eee; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .message-box { background: #fff3cd; padding: 15px; border-left: 5px solid #ffc107; margin: 15px 0; border-radius: 0 5px 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Bon pour accord d'exécution</h2>
        </div>
        <div class="content">
            <div class="badge">BON POUR ACCORD</div>
            
            <p>Bonjour,</p>
            
            <p>Par la présente, nous vous transmettons le <strong>Bon pour accord</strong> concernant l'opération suivante pour exécution immédiate :</p>
            
            <div class="details">
                <p><strong>N° Opération :</strong> #{{ $operation->id }}</p>
                <p><strong>Titre :</strong> {{ $operation->titre }}</p>
                <p><strong>Type :</strong> {{ $operation->typeOperation->libelle ?? 'Opération' }}</p>
                <p><strong>Service concerné :</strong> {{ $operation->operationalService?->nom ?? 'Non spécifié' }}</p>
                @if($operation->client)
                    <p><strong>Client :</strong> {{ $operation->client->nom_complet }}</p>
                @endif
                <p><strong>Montant =</strong> {{ number_format($operation->montant, 0, ',', ' ') }} FCFA</p>
                <p><strong>Priorité :</strong> {{ ucfirst($operation->priorite) }}</p>
                <p><strong>Demandeur :</strong> {{ $operation->demandeur_name }} ({{ $operation->demandeur_email }})</p>
                <p><strong>Échéance :</strong> {{ $operation->echeance ? $operation->echeance->format('d/m/Y') : 'Non spécifiée' }}</p>
                <hr style="border: 0; border-top: 1px solid #eee;">
                <p><strong>Description :</strong><br>
                {{ $operation->description ?? 'Aucun détail supplémentaire fourni.' }}</p>
            </div>

            @if($messageCustom)
            <div class="message-box">
                <strong>Message du demandeur :</strong><br>
                {!! nl2br(e($messageCustom)) !!}
            </div>
            @endif

                <p>Cette opération a dûment validée selon le circuit de validation interne de KENAM SERVICES.</p>

                @if(isset($markAsPaidUrl) && $markAsPaidUrl)
                <div style="margin: 30px 0; text-align: center;">
                    <a href="{{ $markAsPaidUrl }}" style="background: #1cc88a; color: white; padding: 12px 28px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 16px;">Marquer comme payée</a>
                    <p style="font-size: 12px; color: #888; margin-top: 8px;">Ce lien est sécurisé et utilisable une seule fois.</p>
                </div>
                @endif
        </div>
        <div class="footer">
            <p>Ceci est un message officiel de la plateforme KENAM SERVICES.</p>
        </div>
    </div>
</body>
</html>
