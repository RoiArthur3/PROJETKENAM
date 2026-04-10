@extends('emails.layouts.master')

@section('headerTitle', 'Suivi de votre colis')
@section('headerSubtitle', $messageData['title'] ?? 'Une nouvelle mise à jour est disponible')

@section('content')
<p>Bonjour {{ $parcel->user?->name ?? $parcel->recipient_name ?? 'cher client' }},</p>

<p>{{ $messageData['body'] ?? 'Une nouvelle mise à jour est disponible pour votre colis.' }}</p>

<div class="info-card">
    <h3>📦 Détails du colis</h3>
    <p><strong>Numéro de suivi :</strong> {{ $parcel->tracking_number }}</p>
    <p><strong>Statut actuel :</strong> <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $parcel->status ?? $event)) }}</span></p>
    @if($parcel->shipment_type)
        <p><strong>Type d'expédition :</strong> {{ $parcel->shipment_type_text ?? ucfirst($parcel->shipment_type) }}</p>
    @endif
    @if($parcel->transport_mode)
        <p><strong>Mode de transport :</strong> {{ $parcel->transport_mode_text ?? $parcel->transport_mode }}</p>
    @endif
    @if($parcel->destination_country)
        <p><strong>Destination :</strong> {{ $parcel->full_destination ?? $parcel->destination_country }}</p>
    @endif
    @if($parcel->recipient_name)
        <p><strong>Destinataire :</strong> {{ $parcel->recipient_name }}</p>
    @endif
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $trackingUrl }}" class="btn btn-primary">Accéder à mon espace</a>
</div>

<p style="font-size: 14px; color: #6b7280;">Cet email est envoyé automatiquement pour vous tenir informé de l'évolution de votre expédition.</p>
@endsection