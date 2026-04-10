@extends('layouts.app')

@section('title', 'Détails de l\'Encaissement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4">Encaissement : {{ $encaissement->reference }}</h1>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print me-2"></i>Imprimer le Reçu
            </button>
            <a href="{{ route('tresorerie.encaissements') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 font-weight-bold text-success">
                    <i class="fas fa-arrow-down me-2"></i> Flux d'Entrée
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Origine / Type :</div>
                        <div class="col-sm-8 text-uppercase fw-bold text-primary">{{ $encaissement->type_encaissement }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Objet :</div>
                        <div class="col-sm-8">{{ $encaissement->description }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Montant encaissé :</div>
                        <div class="col-sm-8 h4 text-success">{{ number_format($encaissement->montant, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Client / Versé par :</div>
                        <div class="col-sm-8">{{ $encaissement->client ?? ($encaissement->clientRel->nom ?? 'N/A') }}</div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Caisse de réception :</div>
                        <div class="col-sm-8">{{ $encaissement->caisse->nom ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Date Encaissement :</div>
                        <div class="col-sm-8">{{ \Carbon\Carbon::parse($encaissement->date_encaissement)->format('d/m/Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Mode de règlement :</div>
                        <div class="col-sm-8 text-capitalize">{{ $encaissement->mode_paiement }}</div>
                    </div>
                    @if($encaissement->reference_externe)
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Réf. Externe :</div>
                        <div class="col-sm-8">{{ $encaissement->reference_externe }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Liens de traçabilité -->
            @if($encaissement->invoice || $encaissement->operation || $encaissement->project)
            <div class="card shadow-sm border-info mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="card-title mb-0">Traçabilité Métier</h6>
                </div>
                <div class="card-body small">
                    @if($encaissement->invoice)
                    <p class="mb-2"><strong>Facture liée:</strong> <span class="text-primary">{{ $encaissement->invoice->invoice_number }}</span></p>
                    @endif
                    @if($encaissement->operation)
                    <p class="mb-2"><strong>Opération liée:</strong> {{ $encaissement->operation->titre }}</p>
                    @endif
                    @if($encaissement->project)
                    <p class="mb-2"><strong>Projet lié:</strong> {{ $encaissement->project->nom }}</p>
                    @endif
                </div>
            </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3 text-muted">
                    <h6 class="card-title mb-0 small">Audit Système</h6>
                </div>
                <div class="card-body small text-muted">
                    <p class="mb-1">Enregistré par : {{ $encaissement->creator->name ?? 'Système' }}</p>
                    <p class="mb-1">Le : {{ $encaissement->created_at->format('d/m/Y à H:i') }}</p>
                    @if($encaissement->notes)
                    <hr>
                    <p class="mb-0 fw-bold">Notes :</p>
                    <p class="mb-0 text-dark italic">{{ $encaissement->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    if (new URLSearchParams(window.location.search).has('print')) {
        window.print();
    }
</script>

<style>
    @media print {
        .content-wrapper { padding: 0 !important; margin: 0 !important; }
        .btn, .main-footer, .main-sidebar, .main-header { display: none !important; }
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
    }
</style>
@endsection
