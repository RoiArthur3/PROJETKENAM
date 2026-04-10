@extends('layouts.app')

@section('title', 'RH - Modifier un Agent | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-edit mr-2 text-primary"></i>Modifier l'Agent
            </h1>
            <p class="text-muted">Mise à jour des informations de l'agent</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('rh.agents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-card mr-2"></i>Informations
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('rh.agents.update', $agentData['id']) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="name" name="name" required value="{{ old('name', $agentData['name'] ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required value="{{ old('email', $agentData['email'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Poste / Rôle *</label>
                                <input type="text" class="form-control" id="role" name="role" required value="{{ old('role', $agentData['role'] ?? '') }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                            <a href="{{ route('rh.agents.index') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
