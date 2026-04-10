<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Parcours de l'Opération #{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4e73df; padding-bottom: 10px; }
        .header h1 { color: #4e73df; margin: 0; font-size: 24px; }
        .section { margin-bottom: 20px; }
        .section-title { background: #f8f9fa; padding: 5px 10px; border-left: 4px solid #4e73df; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; font-size: 14px; }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { padding: 5px; vertical-align: top; }
        .info-label { font-weight: bold; width: 30%; color: #555; }
        .status-badge { padding: 3px 8px; border-radius: 4px; color: white; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-payee { background-color: #1cc88a; }
        .status-rejetee { background-color: #e74a3b; }
        .status-pending { background-color: #f6c23e; }
        .status-default { background-color: #4e73df; }
        
        .timeline-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .timeline-table th, .timeline-table td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        .timeline-table th { background-color: #f8f9fa; color: #4e73df; font-size: 11px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KENAM SERVICES</h1>
        <p>Rapport de Parcours d'Opération</p>
    </div>

    <div class="section">
        <div class="section-title">Informations Générales</div>
        <table class="info-grid">
            <tr>
                <td class="info-label">Numéro :</td>
                <td>#{{ str_pad($operation->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="info-label">Date Création :</td>
                <td>{{ $operation->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="info-label">Titre :</td>
                <td colspan="3">{{ $operation->titre }}</td>
            </tr>
            <tr>
                <td class="info-label">Montant :</td>
                <td>{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</td>
                <td class="info-label">Statut Actuel :</td>
                <td>
                    <span class="status-badge status-{{ in_array($operation->statut_courant, ['payee', 'termine']) ? 'payee' : ($operation->statut_courant == 'rejetee' ? 'rejetee' : 'default') }}">
                        {{ $operation->statut_courant }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="info-label">Demandeur :</td>
                <td>{{ $operation->demandeur_name }}</td>
                <td class="info-label">Email :</td>
                <td>{{ $operation->demandeur_email }}</td>
            </tr>
        </table>
    </div>

    @if($operation->description)
    <div class="section">
        <div class="section-title">Description</div>
        <div style="padding: 10px; border: 1px solid #eee; background: #fafafa;">
            {{ $operation->description }}
        </div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Circuit de Validation</div>
        <table class="timeline-table">
            <thead>
                <tr>
                    <th>Ordre</th>
                    <th>Service / Rôle</th>
                    <th>Validateur</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                @foreach($steps as $step)
                <tr>
                    <td style="text-align: center;">{{ $step->ordre_validation }}</td>
                    <td>{{ $step->service_name }}<br><small>({{ $step->role_label }})</small></td>
                    <td>{{ $step->validator_name ?? '—' }}</td>
                    <td>{{ $step->date_validation ? \Carbon\Carbon::parse($step->date_validation)->format('d/m/Y H:i') : '—' }}</td>
                    <td>
                        <span style="color: {{ $step->statut == 'APPROUVE' ? '#1cc88a' : ($step->statut == 'REJETE' ? '#e74a3b' : '#f6c23e') }}; font-weight: bold;">
                            {{ $step->statut }}
                        </span>
                    </td>
                    <td>{{ $step->commentaire ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Historique des Actions</div>
        <table class="timeline-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action / Statut</th>
                    <th>Utilisateur</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statusHistory as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $log->to_status }}</strong>
                        @if($log->from_status)
                        <br><small>depuis {{ $log->from_status }}</small>
                        @endif
                    </td>
                    <td>{{ $log->user_name ?? 'Système' }}</td>
                    <td>{{ $log->commentaire ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Généré le {{ now()->format('d/m/Y H:i') }} par {{ auth()->user()->name }} | KENAM SERVICES - Système de Gestion Opérationnelle
    </div>
</body>
</html>
