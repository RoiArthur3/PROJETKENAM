@extends('layouts.app')

@section('title', 'Détails Opération #' . $operation->numero_operation)

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Détails Opération #{{ $operation->numero_operation }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('comptetreso.dashboard') }}">Compte Treso</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('comptetreso.operations') }}">Opérations</a></li>
                    <li class="breadcrumb-item active">Détails</li>
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
                    <h3 class="card-title">Détails de l'opération</h3>
                    <a href="{{ route('comptetreso.operations') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">N° Opération</th>
                                    <td>{{ $operation->numero_operation }}</td>
                                </tr>
                                <tr>
                                    <th>Service</th>
                                    <td>{{ $operation->service->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ $operation->typeOperation->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Montant</th>
                                    <td>{{ number_format($operation->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Statut</th>
                                    <td>
                                        <span class="badge badge-warning">
                                            {{ $operation->statut_courant }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date création</th>
                                    <td>{{ $operation->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Créé par</th>
                                    <td>{{ $operation->user->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
