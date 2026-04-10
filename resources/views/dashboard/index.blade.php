@extends('layouts.app')

@section('title', 'Tableau de Bord - KENAM SERVICES')

@section('content')
@php
    $authorizedModules = $authorizedModules ?? [];
    $hasAuthorizedModules = count($authorizedModules) > 0;
    $totalAvailableModules = $totalAvailableModules ?? count(config('submodules', []));
    $businessKpis = $businessKpis ?? [];
    $primaryDashboardUrl = $primaryDashboardUrl ?? '/operations';
@endphp

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Tableau de Bord General
                            </h3>
                            <small class="text-white-50">Vue intelligente selon votre metier et vos permissions</small>
                        </div>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded bg-light border h-100">
                                <div class="text-muted small">Modules accessibles</div>
                                <div class="h4 mb-0 fw-bold">{{ count($authorizedModules) }}</div>
                                <div class="small text-muted">sur {{ $totalAvailableModules }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded bg-light border h-100">
                                <div class="text-muted small">Role</div>
                                <div class="h5 mb-0 fw-bold">{{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="p-3 rounded bg-light border h-100 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Action rapide</div>
                                    <div class="fw-semibold">Aller vers votre module principal</div>
                                </div>
                                <a href="{{ $primaryDashboardUrl }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i>Ouvrir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($businessKpis))
        @foreach($businessKpis as $section)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <h5 class="mb-0">{{ $section['title'] }}</h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                @foreach(($section['items'] ?? []) as $item)
                                    <div class="col-sm-6 col-lg-3">
                                        <div class="p-3 rounded border bg-white h-100">
                                            <div class="text-muted small text-uppercase">{{ $item['label'] }}</div>
                                            <div class="h5 mb-0 mt-1 fw-bold">
                                                @if(($item['format'] ?? 'int') === 'money')
                                                    {{ number_format((float)($item['value'] ?? 0), 0, ',', ' ') }} FCFA
                                                @else
                                                    {{ number_format((float)($item['value'] ?? 0), 0, ',', ' ') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Acces Metier</h5>
                </div>
                <div class="card-body">
                    @if($hasAuthorizedModules)
                        <div class="row">
                            @foreach($authorizedModules as $module)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card h-100 border-0 shadow-sm hover-card">
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <div class="icon-box bg-primary bg-gradient text-white rounded-circle p-3 d-inline-block">
                                                    <i class="{{ $module['icon'] }} fa-2x"></i>
                                                </div>
                                            </div>
                                            <h5 class="card-title">{{ $module['name'] }}</h5>
                                            <p class="card-text text-muted small">{{ $module['description'] }}</p>
                                            <a href="{{ $module['url'] }}" class="btn btn-primary">
                                                <i class="fas fa-arrow-right me-1"></i>Acceder
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3"><i class="fas fa-lock fa-3x text-muted"></i></div>
                            <h5 class="text-muted">Aucun module autorise</h5>
                            <p class="text-muted mb-0">Contactez l'administrateur pour activer vos acces metier.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.icon-box {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
</style>
@endsection
