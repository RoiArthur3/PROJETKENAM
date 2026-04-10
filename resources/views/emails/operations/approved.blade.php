@extends('emails.layouts.master')

@section('headerTitle', 'Opération Validée 🎉')
@section('headerSubtitle', 'Votre opération a été entièrement approuvée')

@section('content')
<p>Bonjour {{ $operation->demandeur_name }},</p>

<p>Bonne nouvelle ! Votre opération <strong>#{{ $operation->id }}</strong> a été entièrement validée par tous les services concernés.</p>

<div class="info-card">
    <h3>📋 Résumé de l'opération</h3>
    <p><strong>Titre :</strong> {{ $operation->titre }}</p>
    @if($operation->description)
        <p><strong>Description :</strong> {{ $operation->description }}</p>
    @endif
    <p><strong>Montant :</strong> {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</p>
    <p><strong>Priorité :</strong>
        <span class="priority-{{ $operation->priorite }}">
            {{ ucfirst($operation->priorite) }}
        </span>
    </p>
    <p><strong>Date de demande :</strong> {{ $operation->created_at ? $operation->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
    @if($operation->echeance)
        <p><strong>Date d'échéance :</strong> {{ \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') }}</p>
    @endif
    <p><strong>Date de validation :</strong> {{ now()->format('d/m/Y H:i') }}</p>
</div>

<p>Vous pouvez consulter les détails complets et passer à l'étape suivante en cliquant sur le bouton ci-dessous :</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-primary">
        Voir mon opération
    </a>
</div>
@endsection
