<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle opération assignée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-bell"></i> Nouvelle opération assignée</h1>
        <p>Système de gestion KENAM SERVICES</p>
    </div>

    <div class="content">
        <p>Bonjour {{ $serviceOperationnel->responsable ?? 'Équipe' }},</p>
        
        <p>Une nouvelle opération vous a été assignée et requiert votre attention.</p>

        <div class="alert alert-info">
            <h3><i class="fas fa-info-circle"></i> Détails de l'opération</h3>
            <p><strong>Titre :</strong> {{ $operation->titre }}</p>
            <p><strong>Description :</strong> {{ $operation->description ?? 'Non spécifiée' }}</p>
            <p><strong>Priorité :</strong> 
                @if($operation->priorite === 'haute')
                    <span style="color: #dc3545;">⚠ Haute</span>
                @elseif($operation->priorite === 'moyenne')
                    <span style="color: #ffc107;">⚡ Moyenne</span>
                @else
                    <span style="color: #28a745;">✓ Basse</span>
                @endif
            </p>
            <p><strong>Date d'échéance :</strong> {{ $operation->date_echeance ? \Carbon\Carbon::parse($operation->date_echeance)->format('d/m/Y') : 'Non spécifiée' }}</p>
            <p><strong>Demandeur :</strong> {{ $demandeur->name ?? 'Système' }}</p>
            <p><strong>Date de création :</strong> {{ $operation->created_at ? \Carbon\Carbon::parse($operation->created_at)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
        </div>

        @if(isset($operation->id) && $operation->id != 9999)
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-primary">
                <i class="fas fa-eye"></i> Voir les détails
            </a>
        </div>
        @else
        <p style="color: #6c757d; font-style: italic;">
            <i class="fas fa-info-circle"></i> Ceci est un email de test - Le lien de visualisation n'est pas disponible
        </p>
        @endif

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <p>
            <strong>Cordialement,</strong><br>
            L'équipe KENAM SERVICES<br>
            <small>Système automatisé de gestion des opérations</small>
        </p>

        <p style="margin-top: 15px;">
            <small>
                Référence : OP-{{ $operation->id ?? 'TEST' }} |
                Envoyé le : {{ now()->format('d/m/Y H:i') }} |
                @if(isset($operation->id) && $operation->id != 9999)
                    <a href="{{ route('operations.show', $operation->id) }}" style="color: #007bff;">Voir en ligne</a>
                @else
                    <span style="color: #6c757d;">Test - Pas de lien disponible</span>
                @endif
            </small>
        </p>
    </div>
</body>
</html>
