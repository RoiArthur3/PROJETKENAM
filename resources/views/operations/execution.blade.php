
@extends('layouts.app')

@section('title', 'Opérations - Exécution')

@section('content')
<div class="container-fluid">


    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions d\'exécution</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Choisissez l\'action à réaliser. L\'identifiant d\'opération sera transmis pour assurer la traçabilité.</p>
                    <div class="d-grid gap-2 d-md-block">
                        <a class="btn btn-success me-2" href="{{ route('stock.entrees', ['operation_id' => $operation->id]) }}">
                            <i class="fas fa-arrow-down me-1"></i> Entrée stock
                        </a>
                        <a class="btn btn-warning me-2" href="{{ route('stock.exits', ['operation_id' => $operation->id]) }}">
                            <i class="fas fa-arrow-up me-1"></i> Sortie stock
                        </a>
                        <a class="btn btn-info me-2" href="{{ route('parc.reparations', ['operation_id' => $operation->id]) }}">
                            <i class="fas fa-wrench me-1"></i> Intervention Parc Auto
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Séquence des Services</h6>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        @foreach($steps as $s)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>S{{ $s->ordre_validation }} • {{ $s->service_name }}</span>
                                <span class="badge bg-{{ getOperationStatusColor($s->statut) }}">{{ getOperationStatusText($s->statut) }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
