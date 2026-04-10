<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle requête reçue</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px 0;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-style: italic;
        }
        .highlight {
            background: #fff3cd;
            padding: 10px;
            border-left: 4px solid #ffc107;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                @if($type == 'nouvelle')
                    <i class="fas fa-bell"></i> Nouvelle requête reçue
                @elseif($type == 'transfert')
                    <i class="fas fa-exchange-alt"></i> Requête transférée
                @elseif($type == 'cloture')
                    <i class="fas fa-check-circle"></i> Requête clôturée
                @endif
            </h1>
        </div>

        <div class="content">
            <p>Bonjour,</p>

            @if($type == 'nouvelle')
                <p>Vous avez reçu une nouvelle requête qui nécessite votre attention.</p>
            @elseif($type == 'transfert')
                <p>Une requête a été transférée vers votre service.</p>
            @elseif($type == 'cloture')
                <p>Une requête a été clôturée.</p>
            @endif

            <div class="info-box">
                <h3><strong>Objet :</strong> {{ $operation->nom ?? '[Sans objet]' }}</h3>
                
                <p><strong>Demandeur :</strong> {{ optional($operation->user)->name ?? 'Utilisateur inconnu' }}</p>
                <p><strong>Email :</strong> {{ optional($operation->user)->email ?? '—' }}</p>
                <p><strong>Service demandeur :</strong> {{ optional($operation->serviceEmetteur)->nom ?? 'Non défini' }}</p>
                <p><strong>Service destinataire :</strong> {{ optional($operation->serviceDestinataire)->nom ?? '—' }}</p>
                <p><strong>Priorité :</strong> 
                    @php
                        $prioColor = $operation->priorite == 'URGENTE' ? '#dc3545' : ($operation->priorite == 'HAUTE' ? '#fd7e14' : ($operation->priorite == 'MOYENNE' ? '#ffc107' : '#28a745'));
                    @endphp
                    <span style="color: {{ $prioColor }}">{{ $operation->priorite }}</span>
                </p>
                <p><strong>Type :</strong> {{ $operation->type_requete }}</p>
                <p><strong>Référence :</strong> {{ $operation->reference_requete }}</p>
            </div>

            <div class="highlight">
                <h4>Description de la demande :</h4>
                <p>{{ $operation->description }}</p>
            </div>

            @if($type == 'nouvelle' || $type == 'transfert')
                <p>
                    <a href="{{ route('requetes.show', $operation) }}" class="btn">
                        <i class="fas fa-eye"></i> Voir la demande
                    </a>
                </p>
            @endif

            <div class="signature">
                <p>Cordialement,</p>
                <p><strong>{{ optional($operation->serviceEmetteur)->nom ?? 'Service demandeur' }}</strong><br>
                <strong>KENAM SERVICES</strong></p>
                
                <small class="text-muted">
                    Cet email a été envoyé automatiquement via le système de gestion des requêtes KENAM Services.
                </small>
            </div>
        </div>
    </div>
</body>
</html>
