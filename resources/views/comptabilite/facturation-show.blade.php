@extends('layouts.app')

@section('title', 'Détails de la Facture')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Détails de la Facture #{{ $facture->numero }}</h1>
                <a href="{{ route('comptabilite.factures.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Numéro :</strong> {{ $facture->numero }}</p>
                    <p><strong>Client :</strong> {{ $facture->client->nom ?? 'N/A' }}</p>
                    <p><strong>Date :</strong> {{ $facture->date_facture ? $facture->date_facture->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Montant HT :</strong> {{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</p>
                    <p><strong>TVA ({{ $facture->tva }}%) :</strong> {{ number_format($facture->montant_ht * ($facture->tva / 100), 0, ',', ' ') }} FCFA</p>
                    <p><strong>Montant TTC :</strong> {{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</p>
                    <p><strong>Statut :</strong>
                        @if($facture->statut == 'payee')
                            <span class="badge bg-success">Payée</span>
                        @elseif($facture->statut == 'en_attente')
                            <span class="badge bg-warning">En attente</span>
                        @elseif($facture->statut == 'en_retard')
                            <span class="badge bg-danger">En retard</span>
                        @elseif($facture->statut == 'annulee')
                            <span class="badge bg-secondary">Annulée</span>
                        @else
                            <span class="badge bg-secondary">{{ $facture->statut }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
