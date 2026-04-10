<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de Requête d'Opération</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px 10px 0 0; text-align: center; }
        .validation-1 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .validation-suivante { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .notification { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .content { background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .operation-info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #007bff; }
        .service-info { background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .validation-badge { display: inline-block; background: #ff6b6b; color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 14px; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; text-align: center; margin: 10px 5px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .urgent { color: #dc3545; font-weight: bold; }
        .service-list { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .service-item { display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #eee; }
        .service-item:last-child { border-bottom: none; }
        .service-order { background: #007bff; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; margin-right: 10px; }
        .service-current { background: #28a745; }
        .service-waiting { background: #ffc107; color: #333; }
    </style>
</head>
<body>
    <div class="header {{ $typeNotification === 'validation_1' ? 'validation-1' : ($typeNotification === 'validation_suivante' ? 'validation-suivante' : 'notification') }}">
        <h1>
            @if($typeNotification === 'validation_1')
                <i class="fas fa-exclamation-triangle"></i> URGENT - Validation 1 Requise
            @elseif($typeNotification === 'validation_suivante')
                <i class="fas fa-clock"></i> Validation {{ $ordreValidation ?? 1 }} Requise
            @else
                <i class="fas fa-info-circle"></i> Information - Requête d'Opération
            @endif
        </h1>
        <p>KENAM SERVICES - Système de Validation</p>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $serviceDestinataire->responsable ?? $serviceDestinataire->nom ?? 'Service' }}</strong>,</p>

        @if($typeNotification === 'validation_1')
            <p class="urgent">
                <i class="fas fa-exclamation-circle"></i>
                <strong>URGENT :</strong> Vous êtes le premier service à devoir valider cette requête d'opération.
            </p>
        @elseif($typeNotification === 'validation_suivante')
            <p>
                <i class="fas fa-arrow-right"></i>
                La requête a été validée par le(s) service(s) précédent(s).
                <strong>C'est votre tour de valider (Validation {{ $ordreValidation ?? 1 }}).</strong>
            </p>
        @else
            <p>
                <i class="fas fa-bell"></i>
                Une nouvelle requête d'opération a été créée et requiert la validation des services.
            </p>
        @endif

        <div class="operation-info">
            <h3><i class="fas fa-clipboard-list"></i> Détails de la requête</h3>

            <p><strong>Titre :</strong> {{ $operation->titre }}</p>
            <p><strong>Description :</strong> {{ $operation->description ?? 'Non spécifiée' }}</p>

            @if($operation->priorite)
            <p>
                <strong>Priorité :</strong>
                @switch($operation->priorite)
                    @case('urgente')
                        <span style="color: #dc3545; font-weight: bold;">🔴 URGENTE</span>
                        @break
                    @case('haute')
                        <span style="color: #fd7e14; font-weight: bold;">🟠 HAUTE</span>
                        @break
                    @case('moyenne')
                        <span style="color: #ffc107; font-weight: bold;">🟡 MOYENNE</span>
                        @break
                    @case('basse')
                        <span style="color: #28a745; font-weight: bold;">🟢 BASSE</span>
                        @break
                @endswitch
            </p>
            @endif

            @if($operation->echeance)
            <p><strong>Échéance :</strong> {{ \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') }}</p>
            @endif

            <p><strong>Demandeur :</strong> {{ $demandeur->name ?? $operation->demandeur_name }} ({{ $demandeur->email ?? $operation->demandeur_email }})</p>

            @if($operation->type_operation_id)
            @php
                $typeOperation = \App\Models\TypeOperation::find($operation->type_operation_id);
            @endphp
            @if($typeOperation)
            <p><strong>Type d'opération :</strong> {{ $typeOperation->libelle }}</p>
            @endif
            @endif

            @if($serviceOperationnel)
            <p><strong>Service émetteur :</strong> {{ $serviceOperationnel->nom }}</p>
            @endif
        </div>

        @if($typeNotification !== 'notification')
            <div class="service-info">
                <h4><i class="fas fa-tasks"></i> Ordre de validation</h4>

                <div class="validation-badge">
                    @if($typeNotification === 'validation_1')
                        Validation 1 - Votre tour
                    @else
                        Validation {{ $ordreValidation ?? 1 }} - Votre tour
                    @endif
                </div>

                <p>Vous devez valider cette requête avant qu'elle ne soit transmise au service suivant.</p>
            </div>
        @endif

        @if(isset($services) && count($services) > 0)
        <div class="service-list">
            <h4><i class="fas fa-users"></i> Services concernés ({{ count($services) }})</h4>

            @foreach($services as $index => $serviceData)
                <div class="service-item">
                    <div class="service-order {{ $serviceData['statut'] === 'EN_COURS' ? 'service-current' : ($serviceData['statut'] === 'EN_ATTENTE' ? 'service-waiting' : '') }}">
                        {{ $serviceData['ordre'] }}
                    </div>
                    <div>
                        <strong>{{ $serviceData['service']->nom }}</strong><br>
                        <small>{{ $serviceData['service']->email }}</small>
                        @if($serviceData['service']->responsable)
                            <br><small>Responsable: {{ $serviceData['service']->responsable }}</small>
                        @endif
                    </div>
                    <div style="margin-left: auto;">
                        @if($serviceData['statut'] === 'EN_COURS')
                            <span style="color: #28a745; font-weight: bold;">En cours</span>
                        @elseif($serviceData['statut'] === 'EN_ATTENTE')
                            <span style="color: #ffc107; font-weight: bold;">En attente</span>
                        @elseif($serviceData['statut'] === 'APPROUVE')
                            <span style="color: #28a745; font-weight: bold;">✓ Validé</span>
                        @elseif($serviceData['statut'] === 'REJETE')
                            <span style="color: #dc3545; font-weight: bold;">✗ Rejeté</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        @if($typeNotification === 'validation_1' || $typeNotification === 'validation_suivante')
            @if(isset($operation->id) && $operation->id != 9999)
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $lienValidation ?? '#' }}" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Valider la requête
                </a>

                <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> Voir les détails
                </a>
            </div>
            @else
            <p style="color: #6c757d; font-style: italic;">
                <i class="fas fa-info-circle"></i> Ceci est un email de test - Les boutons d'action ne sont pas disponibles
            </p>
            @endif
        @else
            <p style="color: #6c757d; font-style: italic;">
                <i class="fas fa-info-circle"></i> Email d'information - Aucune action requise
            </p>
        @endif

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <p>
            <strong>Cordialement,</strong><br>
            L'équipe KENAM SERVICES<br>
            <small>Système automatisé de gestion des opérations</small>
        </p>
    </div>

    <div class="footer">
        <p>
            Cet email a été généré automatiquement par le système KENAM SERVICES.<br>
            Si vous n'êtes pas le destinataire prévu, veuillez ignorer cet email.<br>
            Pour toute question, contactez : support@kenamservices.com
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
