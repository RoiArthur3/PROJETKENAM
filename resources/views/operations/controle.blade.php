
@extends('layouts.app')

@section('title', 'Opérations - Contrôle')

@section('content')
<div class="container-fluid">


    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Étape Contrôle (Checking)</h6>
                </div>
                <div class="card-body">
                    @php($ctrlStep = $steps->firstWhere('service_name', 'Contrôle'))
                    @if($ctrlStep)
                        <p class="mb-3 text-muted">Statut actuel: <span class="badge bg-{{ getOperationStatusColor($ctrlStep->statut) }}">{{ getOperationStatusText($ctrlStep->statut) }}</span></p>
                        <a class="btn btn-success me-2" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('validations.operations.review', now()->addMinutes(30), ['operation' => $operation->id, 'step' => $ctrlStep->ordre_validation]) }}">
                            <i class="fas fa-check me-1"></i> Ouvrir la fiche de validation (S{{ $ctrlStep->ordre_validation }})
                        </a>
                    @else
                        <div class="alert alert-info">Aucune étape "Contrôle" n'est définie pour cette opération.</div>
                    @endif
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
