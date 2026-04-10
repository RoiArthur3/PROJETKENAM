@extends('layouts.app')

@section('title', 'Détails Opération Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Détails Opération Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tresorerie.banque.edit', $banque->id) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted">Informations générales</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Référence</th>
                                    <td>{{ $banque->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date d'opération</th>
                                    <td>{{ \Carbon\Carbon::parse($banque->date_operation)->format('d/m/Y à H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td><span class="badge bg-{{ $banque->type == 'crédit' ? 'success' : 'danger' }}">{{ ucfirst($banque->type) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Banque</th>
                                    <td>{{ $banque->banque }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td><span class="badge bg-{{ $banque->statut == 'validé' ? 'success' : 'warning' }}">{{ ucfirst($banque->statut) }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Montant</h5>
                            <h3 class="text-{{ $banque->type == 'crédit' ? 'success' : 'danger' }}">
                                {{ number_format($banque->montant, 0, ',', ' ') }} FCFA
                            </h3>
                        </div>
                    </div>
                    
                    @if(isset($banque->notes))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-muted">Notes</h5>
                            <p>{{ $banque->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
