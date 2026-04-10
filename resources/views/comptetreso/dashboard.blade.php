@extends('layouts.app')

@section('title', 'Dashboard Compte Treso')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard Compte Treso</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('comptetreso.dashboard') }}">Compte Treso</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bienvenue {{ Auth::user()->name }} - Espace Trésorerie & Comptabilité</h3>
                </div>
                <div class="card-body">
                    @if(isset($stats))
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $stats['count_to_approve'] ?? 0 }}</h3>
                                    <p>À Approuver</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['count_sent_to_cash'] ?? 0 }}</h3>
                                    <p>Envoyé Caisse</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $stats['count_paid'] ?? 0 }}</h3>
                                    <p>Payées</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_to_approve'] ?? 0, 0, ',', ' ') }}</h3>
                                    <p>Total FCFA</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <h5>Opérations en Attente de Bon pour Execution</h5>
                        @if(isset($operationsToApprove) && $operationsToApprove->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>N° Opération</th>
                                            <th>Demandeur</th>
                                            <th>Montant</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($operationsToApprove as $operation)
                                        <tr>
                                            <td>{{ $operation->numero_operation }}</td>
                                            <td>{{ $operation->user->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ $operation->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('comptetreso.operations.show', $operation->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" onclick="showCashierModal({{ $operation->id }})">
                                                    <i class="fas fa-paper-plane"></i> Envoyer Caisse
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Aucune opération en attente de bon pour execution.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
