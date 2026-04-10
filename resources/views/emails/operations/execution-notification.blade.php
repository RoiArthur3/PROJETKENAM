@extends('emails.layouts.master')

@section('content')
<div class="email-content">
    <!-- Header de l'email -->
    <div class="email-header-content">
        <h1 style="font-size: 28px; font-weight: 700; color: var(--kenam-orange); margin-bottom: 15px;">
            🚀 Opération à Exécuter
        </h1>
        <p style="font-size: 16px; color: var(--kenam-text-light); margin: 0;">
            Une opération a été validée et est prête pour exécution
        </p>
    </div>

    <!-- Détails de l'opération -->
    <div class="operation-details" style="background: var(--kenam-gray); padding: 30px; border-radius: 15px; margin: 30px 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="font-size: 20px; font-weight: 600; color: var(--kenam-text); margin: 0;">
                    {{ $operationTitle }}
                </h2>
                <p style="font-size: 14px; color: var(--kenam-text-light); margin: 5px 0 0 0;">
                    Référence : {{ $operationRef }}
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 24px; font-weight: 700; color: var(--kenam-green);">
                    {{ $operationAmount }}
                </div>
                <div style="font-size: 12px; color: var(--kenam-text-light); text-transform: uppercase;">
                    Montant
                </div>
            </div>
        </div>

        @if($message)
        <div style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid var(--kenam-orange); margin-top: 20px;">
            <h3 style="font-size: 16px; font-weight: 600; color: var(--kenam-text); margin: 0 0 10px 0;">
                💬 Message d'exécution
            </h3>
            <p style="font-size: 14px; color: var(--kenam-text); margin: 0; line-height: 1.6;">
                {!! nl2br(e($message)) !!}
            </p>
        </div>
        @endif
    </div>

    <!-- Bouton d'action -->
    <div style="text-align: center; margin: 40px 0;">
        <a href="{{ $operationUrl }}" class="btn btn-primary" style="
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, var(--kenam-green) 0%, var(--kenam-green-dark) 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
            transition: all 0.3s ease;
        ">
            👁️ Voir les Détails de l'Opération
        </a>
    </div>

    <!-- Instructions -->
    <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 10px; margin: 30px 0;">
        <h3 style="font-size: 16px; font-weight: 600; color: #92400e; margin: 0 0 10px 0;">
            📋 Instructions d'Exécution
        </h3>
        <ul style="font-size: 14px; color: #92400e; margin: 0; padding-left: 20px; line-height: 1.6;">
            <li>Vérifiez tous les détails de l'opération avant de commencer</li>
            <li>Assurez-vous d'avoir toutes les ressources nécessaires</li>
            <li>Respectez les délais et les budgets définis</li>
            <li>Documentez toutes les étapes d'exécution</li>
        </ul>
    </div>

    <!-- Contact -->
    <div style="text-align: center; margin: 30px 0;">
        <p style="font-size: 14px; color: var(--kenam-text-light); margin: 0;">
            Pour toute question sur cette opération, contactez l'équipe de gestion
        </p>
        <p style="font-size: 14px; color: var(--kenam-text-light); margin: 10px 0 0 0;">
            <strong>Email :</strong> {{ $entrepriseSettings->email_contact ?? 'contact@kenamservices.net' }}
        </p>
    </div>
</div>
@endsection
