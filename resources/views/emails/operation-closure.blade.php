@extends('emails.layouts.master')

@section('headerTitle', 'Opération clôturée')
@section('headerSubtitle', 'La demande a terminé son cycle de traitement')

@section('content')
<p>Bonjour {{ $user->name ?? $operation->demandeur_name ?? 'cher utilisateur' }},</p>

<p>Nous vous confirmons que l'opération suivante a été clôturée avec succès.</p>

<div class="info-card">
    <h3>✅ Récapitulatif</h3>
    <p><strong>Référence :</strong> {{ $operation->numero_operation ?? ('#' . $operation->id) }}</p>
    <p><strong>Titre :</strong> {{ $operation->titre ?? 'Non renseigné' }}</p>
    <p><strong>Statut :</strong> <span class="badge badge-success">Clôturée</span></p>
    @if(!empty($operation->montant))
        <p><strong>Montant :</strong> {{ number_format($operation->montant, 0, ',', ' ') }} FCFA</p>
    @endif
    @if(!empty($operation->updated_at))
        <p><strong>Date de clôture :</strong> {{ \Illuminate\Support\Carbon::parse($operation->updated_at)->format('d/m/Y H:i') }}</p>
    @endif
    @if(!empty($operation->description))
        <p><strong>Description :</strong> {{ $operation->description }}</p>
    @endif
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-primary">Consulter l'opération</a>
</div>
@endsection