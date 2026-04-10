@extends('layouts.app')

@section('title', 'Ajouter un Agent - RH')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Nouvel Agent
                    </h5>
                    <a href="{{ route('rh.agents.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
                <div class="card-body">
                    @include('rh.agents._form', [
                        'action' => route('rh.agents.store'),
                        'method' => 'POST',
                        'services' => $services ?? collect([])
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
