{{-- Navigation pour le module Pointage Chrono --}}
@php
    $route = request()->route()->getName();
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header bg-info bg-gradient py-2 d-flex align-items-center gap-2">
        <i class="fas fa-stopwatch text-white"></i>
        <strong class="text-white">Pointage Chronométrique</strong>
        <a href="{{ route('materiel.cost-control.list') }}"
           class="btn btn-sm btn-light ms-auto">
            <i class="fas fa-arrow-left me-1"></i>Retour Cost Control
        </a>
    </div>
    <div class="card-body py-2 px-3">
        <nav class="nav nav-pills flex-wrap gap-1">
            <a class="nav-link {{ Str::startsWith($route, 'materiel.cost-control.chrono.dashboard') ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.chrono.dashboard') }}">
                <i class="fas fa-chart-pie me-1"></i>Dashboard
            </a>
            <a class="nav-link {{ Str::startsWith($route, 'materiel.cost-control.chrono.start') ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.chrono.start-form') }}">
                <i class="fas fa-play-circle me-1"></i>Démarrer Pointage
            </a>
            <a class="nav-link {{ Str::startsWith($route, 'materiel.cost-control.chrono.to-invoice') ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.chrono.to-invoice') }}">
                <i class="fas fa-file-invoice-dollar me-1"></i>À Facturer
            </a>
        </nav>
    </div>
</div>
