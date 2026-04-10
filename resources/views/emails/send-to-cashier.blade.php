@extends('emails.layouts.master')

@section('headerTitle', 'Bon pour Exécution')
@section('headerSubtitle', 'Une opération est prête pour traitement en caisse')

@section('content')
<p>Bonjour{{ isset($caisse->nom) ? ' ' . $caisse->nom : '' }},</p>

<p>L'opération ci-dessous a été validée et peut maintenant être prise en charge pour exécution.</p>

<div class="info-card">
    <h3>💼 Détails de l'opération</h3>
    <p><strong>Référence :</strong> {{ $operation->numero_operation ?? ('#' . $operation->id) }}</p>
    <p><strong>Titre :</strong> {{ $operation->titre ?? 'Non renseigné' }}</p>
    @if(!empty($operation->description))
        <p><strong>Description :</strong> {{ $operation->description }}</p>
    @endif
    <p><strong>Montant :</strong> {{ number_format($operation->montant ?? 0, 0, ',', ' ') }} FCFA</p>
    @if(!empty($operation->demandeur_name))
        <p><strong>Demandeur :</strong> {{ $operation->demandeur_name }}</p>
    @endif
    @if(!empty($operation->created_at))
        <p><strong>Date de création :</strong> {{ \Illuminate\Support\Carbon::parse($operation->created_at)->format('d/m/Y H:i') }}</p>
    @endif
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-primary">Voir l'opération</a>
</div>
@endsection