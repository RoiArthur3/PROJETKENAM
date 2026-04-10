@extends('layouts.app')

@section('title', 'Modifier l\'Agent - ' . $agent->name . ' - RH')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Modifier l'Agent
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('rh.agents.show', $agent) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye me-1"></i> Voir
                        </a>
                        <a href="{{ route('rh.agents.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-list me-1"></i> Liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('rh.agents._form', [
                        'agent' => $agent,
                        'action' => route('rh.agents.update', $agent),
                        'method' => 'PUT',
                        'services' => $services ?? collect([])
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
