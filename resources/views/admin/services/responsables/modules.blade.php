@extends('layouts.app')

@section('title', 'Modules du Service | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs mr-2 text-primary"></i>Modules du Service
            </h1>
            <p class="text-muted">Configuration des modules accessibles pour: {{ $service->nom }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.services.responsables.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </a>
        </div>
    </div>

    <!-- Informations du service -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Service:</strong> {{ $service->nom }}
                        </div>
                        @if($service->responsable)
                        <div class="col-md-4">
                            <strong>Responsable:</strong> {{ $service->responsable->name }}
                        </div>
                        <div class="col-md-4">
                            <strong>Email:</strong> {{ $service->responsable->email }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration des modules -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Modules accessibles</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.services.modules.update', $service) }}">
                        @csrf

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> Le module "Requêtes" est obligatoire pour tous les responsables et ne peut pas être désactivé.
                        </div>

                        <div class="row">
                            @foreach($allModules as $moduleCode => $moduleInfo)
                            @if($moduleCode !== 'operations') // operations = requêtes (obligatoire)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                   id="module_{{ $moduleCode }}"
                                                   name="modules[]"
                                                   value="{{ $moduleCode }}"
                                                   {{ in_array($moduleCode, $activeModules) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_{{ $moduleCode }}">
                                                <i class="{{ $moduleInfo['icon'] }} mr-2"></i>
                                                <strong>{{ $moduleInfo['label'] }}</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">{{ $moduleInfo['route'] ?? '' }}</small>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        <!-- Modules obligatoires -->
                        <div class="mt-4">
                            <h6>Modules obligatoires</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-success bg-light">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                       id="module_operations"
                                                       name="modules[]"
                                                       value="operations"
                                                       checked disabled>
                                                <label class="form-check-label text-success" for="module_operations">
                                                    <i class="fas fa-tasks mr-2"></i>
                                                    <strong>Requêtes</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Obligatoire pour tous les responsables</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success bg-light">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                       id="module_dashboard"
                                                       name="modules[]"
                                                       value="dashboard"
                                                       checked disabled>
                                                <label class="form-check-label text-success" for="module_dashboard">
                                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                                    <strong>Tableau de bord</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Obligatoire pour tous les responsables</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Enregistrer les modules
                            </button>
                            <a href="{{ route('admin.services.responsables.index') }}" class="btn btn-outline-secondary ml-2">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Informations importantes</h6>
                </div>
                <div class="card-body">
                    <h6>Modules sélectionnés:</h6>
                    <div class="mb-3">
                        @foreach($activeModules as $module)
                        <span class="badge bg-primary mr-1 mb-1">
                            {{ $allModules[$module]['label'] ?? $module }}
                        </span>
                        @endforeach
                    </div>

                    <h6>Impact sur le responsable:</h6>
                    <ul>
                        <li>Le responsable verra uniquement les modules sélectionnés</li>
                        <li>Les permissions seront mises à jour automatiquement</li>
                        <li>Le module Requêtes reste toujours accessible</li>
                        <li>Le Tableau de bord reste toujours accessible</li>
                    </ul>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Attention:</strong> La modification des modules affecte immédiatement l'accès du responsable.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
