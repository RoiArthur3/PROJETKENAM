@extends('layouts.app')

@section('title', 'Détail Virement - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Virement {{ $virement->reference }}</h1>
        <a href="{{ route('tresorerie.virements.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><th width="40%">Référence</th><td>{{ $virement->reference }}</td></tr>
                        <tr><th>Compte source</th><td>{{ $virement->compteSource->intitule_compte ?? $virement->compteSource->numero_compte ?? '-' }}</td></tr>
                        <tr><th>Compte destination</th><td>{{ $virement->compteDestination->intitule_compte ?? $virement->compteDestination->numero_compte ?? '-' }}</td></tr>
                        <tr><th>Montant</th><td class="fw-bold">{{ number_format($virement->montant, 0, ',', ' ') }} FCFA</td></tr>
                        <tr><th>Frais</th><td>{{ number_format($virement->frais ?? 0, 0, ',', ' ') }} FCFA</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><th width="40%">Date</th><td>{{ \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y') }}</td></tr>
                        <tr><th>Statut</th><td><span class="badge bg-{{ $virement->statut == 'effectue' ? 'success' : ($virement->statut == 'en_attente' ? 'warning' : 'danger') }}">{{ ucfirst(str_replace('_', ' ', $virement->statut)) }}</span></td></tr>
                        <tr><th>Motif</th><td>{{ $virement->motif }}</td></tr>
                        <tr><th>Initié par</th><td>{{ $virement->initiateur->name ?? '-' }}</td></tr>
                        <tr><th>Notes</th><td>{{ $virement->notes ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
