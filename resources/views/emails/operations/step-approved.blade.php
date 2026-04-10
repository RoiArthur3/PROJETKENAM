@extends('emails.layouts.master')

@section('headerTitle', 'Évolution de l\'opération 🚀')
@section('headerSubtitle', 'Votre opération progresse dans le circuit')

@section('content')
<p>Bonjour {{ $operation->demandeur_name }},</p>

<p>Votre opération <strong>#{{ $operation->id }}</strong> avance ! Elle vient d'être approuvée à une étape du circuit.</p>

<div class="info-card">
    <h3>✅ Détails de la validation</h3>
    <p><strong>Validé par :</strong> {{ $validator->name }}</p>
    @if($commentaire)
        <p><strong>Commentaire :</strong> <em>"{{ $commentaire }}"</em></p>
    @endif
    @if($nextService)
        <p><strong>Prochaine étape :</strong> <span class="badge badge-primary">{{ $nextService->nom }}</span></p>
    @endif
</div>

<div class="info-card" style="border-left-color: #4e73df;">
    <h3>📋 Résumé de l'opération</h3>
    <p><strong>Titre :</strong> {{ $operation->titre }}</p>
    @if($operation->description)
        <p><strong>Description :</strong> {{ $operation->description }}</p>
    @endif
    <p><strong>Montant :</strong> {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</p>
    <p><strong>Date de demande :</strong> {{ $operation->created_at ? $operation->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
    @if($operation->echeance)
        <p><strong>Date d'échéance :</strong> {{ \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') }}</p>
    @endif
</div>

<p>Vous pouvez suivre le circuit de validation complet en temps réel ici :</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-primary">
        Suivre mon opération
    </a>
</div>
@endsection
