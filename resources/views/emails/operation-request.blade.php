<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Requête d'Opération</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: #fff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .content h2 { color: #28a745; margin-top: 0; }
        .info-box { background: #f8f9fa; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
        .info-box p { margin: 5px 0; }
        .info-box strong { color: #28a745; }
        .btn { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: #fff; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
        .btn:hover { background: linear-gradient(135deg, #1e7e34 0%, #17a2b8 100%); }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .demandeur-info { background: #e8f5e8; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
        .urgence { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Nouvelle Requête d'Opération</h1>
        </div>

        <div class="content">
            <h2>Bonjour,</h2>
            <p>Vous avez reçu une nouvelle requête d'opération nécessitant votre validation.</p>

            <div class="demandeur-info">
                <h4>📤 Informations du demandeur</h4>
                <p><strong>Nom :</strong> {{ $demandeur->name }}</p>
                <p><strong>Email :</strong> {{ $demandeur->email }}</p>
                <p><strong>Service :</strong> {{ $demandeur->service->nom ?? 'Non spécifié' }}</p>
                <p><strong>Date de la demande :</strong> {{ $operation->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            <div class="info-box">
                <h4>📋 Détails de l'opération</h4>
                <p><strong>Titre :</strong> {{ $operation->titre }}</p>
                <p><strong>Description :</strong> {{ $operation->description ?? 'Non renseignée' }}</p>
                <p><strong>Type :</strong> {{ $operation->typeOperation->libelle ?? 'Non spécifié' }}</p>
                <p><strong>Priorité :</strong>
                    @if($operation->priorite === 'urgente')
                        <span style="color: #dc3545; font-weight: bold;">🚨 URGENTE</span>
                    @elseif($operation->priorite === 'haute')
                        <span style="color: #fd7e14; font-weight: bold;">⚠️ Haute</span>
                    @elseif($operation->priorite === 'moyenne')
                        <span style="color: #ffc107; font-weight: bold;">📊 Moyenne</span>
                    @else
                        <span style="color: #28a745;">📝 Basse</span>
                    @endif
                </p>
                @if($operation->echeance)
                <p><strong>Échéance :</strong> {{ $operation->echeance->format('d/m/Y') }}</p>
                @endif
            </div>

            @if($operation->priorite === 'urgente')
            <div class="urgence">
                <h4>🚨 ATTENTION - PRIORITÉ URGENTE</h4>
                <p>Cette opération nécessite une validation rapide. Merci de traiter cette demande dans les plus brefs délais.</p>
            </div>
            @endif

            <p>Pour examiner cette requête et procéder à la validation, veuillez cliquer sur le bouton ci-dessous :</p>

            <div style="text-align: center;">
                <a href="{{ $validationUrl }}" class="btn">
                    ✅ Accéder à la validation
                </a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                Vous pouvez également copier ce lien dans votre navigateur :<br>
                <a href="{{ $validationUrl }}" style="color: #28a745; word-break: break-all;">{{ $validationUrl }}</a>
            </p>

            <p style="margin-top: 20px; font-size: 14px; color: #666;">
                <strong>Statut actuel :</strong> En attente<br>
                <strong>Référence :</strong> OP-{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}
            </p>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement depuis KENAM Services.</p>
            <p>Merci de ne pas répondre directement à cet email.</p>
        </div>
    </div>
</body>
</html>
