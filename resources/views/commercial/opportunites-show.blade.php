@extends('layouts.app')

@section('title', 'Détail Opportunité - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détail de l'opportunité</h1>
        <a href="{{ route('commercial.opportunites.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">{{ $opportunite->titre }}</h5>
            <p class="text-muted mb-1">Client : {{ $opportunite->client->nom ?? '-' }}</p>
            <p class="text-muted mb-1">Montant : {{ number_format($opportunite->montant, 0, ',', ' ') }} FCFA</p>
            <p class="text-muted mb-1">Statut : {{ ucfirst($opportunite->statut) }}</p>
            <p class="text-muted mb-1">Probabilité : {{ $opportunite->probabilite }} %</p>
            <p class="text-muted mb-1">Échéance : {{ optional($opportunite->date_echeance)->format('d/m/Y') }}</p>
            @if($opportunite->description)
                <hr>
                <p>{{ $opportunite->description }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
