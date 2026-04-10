@extends('emails.layouts.master')

@section('headerTitle', '🎉 Requête d\'Opération Soumise')
@section('headerSubtitle', 'Votre demande a été enregistrée avec succès')

@section('content')
<p>Bonjour {{ $demandeur->name }},</p>

<p>Nous vous confirmons que votre requête d'opération a été soumise avec succès et est maintenant en cours de validation par nos services.</p>

<div class="info-card">
    <h3>📋 Détails de l'Opération</h3>
    <p><strong>Titre:</strong> {{ $operation->titre }}</p>
    <p><strong>Description:</strong> {{ $operation->description ?: 'Non spécifiée' }}</p>
    <p><strong>Montant:</strong> {{ number_format($operation->montant, 0, ',', ' ') }} FCFA</p>
    <p><strong>Priorité:</strong> <span class="priority-{{ $operation->priorite }}">{{ $operation->priorite }}</span></p>
    <p><strong>Date de soumission:</strong> {{ $operation->created_at->format('d/m/Y H:i') }}</p>
    @if($operation->echeance)
        <p><strong>Échéance:</strong> {{ $operation->echeance->format('d/m/Y') }}</p>
    @endif
</div>

<h3>📧 Prochaines étapes</h3>
<ul>
    <li>Votre requête est maintenant en attente</li>
    <li>Vous recevrez une notification dès qu'elle sera traitée</li>
    <li>Vous pouvez suivre l'état d'avancement dans votre espace personnel</li>
</ul>

<div style="text-align: center; margin: 30px 0;">
    <a href="http://127.0.0.1:8000/validations/dashboard" class="btn btn-primary">
        👁️ Suivre ma requête
    </a>
</div>

<p style="font-size: 14px; color: #6b7280;">
    Pour toute question, n'hésitez pas à nous contacter via les informations ci-dessous.
</p>
@endsection
