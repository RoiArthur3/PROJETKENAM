@extends('layouts.print')

@section('title', 'Facture #' . $facture->numero)

@section('content')
<div class="container-fluid p-4">
    <!-- En-tête de la facture -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="mb-3">FACTURE</h2>
            <div class="border-bottom border-3 border-dark pb-2">
                <h3 class="mb-0">#{{ $facture->numero }}</h3>
            </div>
        </div>
    </div>

    <!-- Informations entreprise et client -->
    <div class="row mb-4">
        <div class="col-6">
            <h5 class="text-decoration-underline mb-3">EMETTEUR</h5>
            <div class="company-info">
                <strong>KENAM SERVICES</strong><br>
                [Adresse de l'entreprise]<br>
                [Téléphone] | [Email]<br>
                [N° IFU / RCCM]
            </div>
        </div>
        <div class="col-6">
            <h5 class="text-decoration-underline mb-3">CLIENT</h5>
            <div class="client-info">
                <strong>{{ $facture->client->nom ?? 'N/A' }}</strong><br>
                @if($facture->client->adresse ?? null)
                    {{ $facture->client->adresse }}<br>
                @endif
                @if($facture->client->telephone ?? null)
                    Tél: {{ $facture->client->telephone }}<br>
                @endif
                @if($facture->client->email ?? null)
                    Email: {{ $facture->client->email }}
                @endif
            </div>
        </div>
    </div>

    <!-- Dates -->
    <div class="row mb-4">
        <div class="col-6">
            <strong>Date de facturation :</strong>
            {{ $facture->date_facture ? $facture->date_facture->format('d/m/Y') : 'N/A' }}
        </div>
        <div class="col-6">
            <strong>Date d'échéance :</strong>
            {{ $facture->date_echeance ? $facture->date_echeance->format('d/m/Y') : 'N/A' }}
        </div>
    </div>

    <!-- Détails de la facture -->
    <div class="row mb-4">
        <div class="col-12">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Quantité</th>
                        <th class="text-end">Prix Unitaire</th>
                        <th class="text-end">Montant HT</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($facture->articles) && $facture->articles->count() > 0)
                        @foreach($facture->articles as $article)
                            <tr>
                                <td>{{ $article->description }}</td>
                                <td class="text-end">{{ $article->quantite }}</td>
                                <td class="text-end">{{ number_format($article->prix_unitaire, 0, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format($article->montant_ht, 0, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">Services rendus selon devis/contrat</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Totaux -->
    <div class="row">
        <div class="col-8">
            @if($facture->observations)
                <div class="border p-3 bg-light">
                    <strong>Observations :</strong><br>
                    {{ $facture->observations }}
                </div>
            @endif
        </div>
        <div class="col-4">
            <table class="table table-bordered">
                <tr>
                    <td><strong>Total HT :</strong></td>
                    <td class="text-end">{{ number_format($facture->montant_ht, 0, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td><strong>TVA ({{ $facture->tva }}%) :</strong></td>
                    <td class="text-end">{{ number_format($facture->montant_ht * ($facture->tva / 100), 0, ',', ' ') }}</td>
                </tr>
                <tr class="table-dark">
                    <td><strong>Total TTC :</strong></td>
                    <td class="text-end"><strong>{{ number_format($facture->montant_ttc, 0, ',', ' ') }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Statut et mentions -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="mb-3">
                @if($facture->statut == 'payee')
                    <span class="badge bg-success fs-6">PAYÉE</span>
                @elseif($facture->statut == 'en_attente')
                    <span class="badge bg-warning fs-6">EN ATTENTE DE PAIEMENT</span>
                @elseif($facture->statut == 'en_retard')
                    <span class="badge bg-danger fs-6">EN RETARD</span>
                @elseif($facture->statut == 'annulee')
                    <span class="badge bg-secondary fs-6">ANNULÉE</span>
                @else
                    <span class="badge bg-secondary fs-6">{{ $facture->statut }}</span>
                @endif
            </div>

            <div class="mt-4">
                <small>
                    <em>Mentions légales : Selon conditions générales de vente<br>
                    TVA non applicable, art. 293 B du CGI</em>
                </small>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { font-size: 12px; }
        .table { font-size: 11px; }
        .company-info, .client-info { font-size: 11px; }
    }
</style>
@endsection
