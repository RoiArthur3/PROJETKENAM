@extends('emails.layouts.master')

@section('headerTitle', 'Opération Rejetée ❌')
@section('headerSubtitle', 'Votre demande a été refusée')

@section('content')
<p>Bonjour {{ $operation->demandeur_name }},</p>

<p>Nous vous informons que votre demande d'opération <strong>#{{ $operation->id }}</strong> a été <strong>rejetée</strong> lors du circuit de validation.</p>

<div class="info-card" style="border-left-color: #ef4444;">
    <h3 style="color: #ef4444;">❌ Motif du rejet</h3>
    <p style="font-size: 16px;">{{ $commentaire }}</p>
</div>

<div class="info-card">
    <h3>📋 Résumé de l'opération</h3>
    <p><strong>Titre :</strong> {{ $operation->titre }}</p>
    <p><strong>Montant :</strong> {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</p>
    <p><strong>Priorité :</strong> 
        <span class="priority-{{ $operation->priorite }}">
            {{ ucfirst($operation->priorite) }}
        </span>
    </p>
    <p><strong>Type d'opération :</strong> {{ $operation->typeOperation->libelle ?? $operation->type ?? 'Non spécifié' }}</p>
    <p><strong>Date :</strong> {{ $operation->date_operation ? \Carbon\Carbon::parse($operation->date_operation)->format('d/m/Y') : 'N/A' }}</p>
</div>

<p>Vous pouvez consulter les détails et les observations du validateur en cliquant sur le bouton ci-dessous :</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-secondary">
        Consulter les détails
    </a>
</div>
@endsection
