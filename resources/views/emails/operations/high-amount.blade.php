@extends('emails.layouts.master')

@section('headerTitle', '🚨 Opération de Montant Élevé')
@section('headerSubtitle', 'Attention requise - Validation nécessaire')

@section('content')
<p>Cher Direction Générale,</p>

<p>Nous vous informons qu'une nouvelle opération avec un montant élevé a été soumise et requiert votre attention particulière.</p>

<div class="info-card">
    <h3>📋 Détails de l'Opération</h3>
    <p><strong>Titre:</strong> {{ $operation->titre }}</p>
    <p><strong>Description:</strong> {{ $operation->description ?: 'Non spécifiée' }}</p>
    <p><strong>Montant:</strong> <span style="color: #dc2626; font-weight: bold; font-size: 18px;">{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</span></p>
    <p><strong>Priorité:</strong> <span class="priority-{{ $operation->priorite }}">{{ $operation->priorite }}</span></p>
    <p><strong>Demandeur:</strong> {{ $demandeur->name }} ({{ $demandeur->email }})</p>
    <p><strong>Date de soumission:</strong> {{ $operation->created_at->format('d/m/Y H:i') }}</p>
    @if($operation->echeance)
        <p><strong>Échéance:</strong> {{ $operation->echeance->format('d/m/Y') }}</p>
    @endif
</div>

<div class="info-card" style="border-left-color: #dc2626;">
    <h3>⚠️ Action Requise</h3>
    <p>Cette opération dépasse le seuil de 99 999 FCFA et nécessite votre validation avant de pouvoir être traitée par les services opérationnels.</p>
    <p>Montant: <strong>{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</strong></p>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="http://127.0.0.1:8000/validations/dashboard" class="btn btn-primary">
        👁️ Voir les détails et valider
    </a>
</div>

<p style="font-size: 14px; color: #6b7280;">
    Pour toute question, n'hésitez pas à nous contacter via les informations ci-dessous.
</p>
@endsection
