@extends('layouts.app')

@section('title', 'RH - Détails Agent | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user mr-2 text-primary"></i>Détails de l'Agent
            </h1>
            <p class="text-muted">Informations de l'agent</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('rh.agents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </a>
            <a href="{{ route('rh.agents.edit', $agentData['id']) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-card mr-2"></i>Informations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Nom</div>
                            <div class="fw-semibold">{{ $agentData['name'] ?? '' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold">{{ $agentData['email'] ?? '' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Rôle / Poste</div>
                            <div class="fw-semibold">{{ $agentData['role'] ?? '' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Statut</div>
                            <div>
                                <span class="badge bg-success">{{ $agentData['statut'] ?? 'Actif' }}</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <form action="{{ route('rh.agents.destroy', $agentData['id']) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cet agent ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash mr-1"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
