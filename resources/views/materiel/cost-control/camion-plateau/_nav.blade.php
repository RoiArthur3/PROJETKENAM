{{-- Shared navigation for Camion Plateau sub-module --}}
@php
    $cpRoute = request()->route()->getName();
@endphp
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning bg-gradient py-2 d-flex align-items-center gap-2">
        <i class="fas fa-truck-moving text-white"></i>
        <strong class="text-white">Camion Plateau</strong>
        <a href="{{ route('materiel.cost-control.plateau.dashboard') }}"
           class="btn btn-sm btn-light ms-auto">
            <i class="fas fa-arrow-left me-1"></i>Retour Plateau
        </a>
    </div>
    <div class="card-body py-2 px-3">
        <nav class="nav nav-pills flex-wrap gap-1">
            <a class="nav-link {{ Str::startsWith($cpRoute, 'materiel.cost-control.plateau.parametrage') ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.plateau.parametrage.index') }}">
                <i class="fas fa-cog me-1"></i>Paramétrage
            </a>
            <a class="nav-link {{ $cpRoute === 'materiel.cost-control.plateau.list' ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.plateau.list') }}">
                <i class="fas fa-truck me-1"></i>Enregistrement Engins
            </a>
            <a class="nav-link {{ $cpRoute === 'materiel.cost-control.plateau.list' ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.plateau.list') }}">
                <i class="fas fa-clipboard-list me-1"></i>Missions / Pointage
            </a>
            <a class="nav-link {{ $cpRoute === 'materiel.cost-control.plateau.chrono.dashboard' ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.plateau.chrono.dashboard') }}">
                <i class="fas fa-stopwatch me-1"></i>Pointage Chrono
            </a>
            <a class="nav-link {{ $cpRoute === 'materiel.cost-control.plateau.facturation.mois' ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.plateau.facturation.mois') }}">
                <i class="fas fa-calendar-check me-1"></i>Facturation au Mois
            </a>
            <a class="nav-link text-dark"
               href="{{ route('materiel.cost-control.plateau.list') }}">
                <i class="fas fa-route me-1"></i>Facturation au Voyage
            </a>
            <a class="nav-link {{ $cpRoute === 'materiel.cost-control.plateau.suivi-voyages' ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.plateau.suivi-voyages') }}">
                <i class="fas fa-map-marked-alt me-1"></i>Suivi des Voyages
            </a>
            <a class="nav-link {{ $cpRoute === 'materiel.cost-control.plateau.projets-termines' ? 'active' : 'text-dark' }}"
               href="{{ route('materiel.cost-control.plateau.projets-termines') }}">
                <i class="fas fa-chart-bar me-1"></i>Calcul de la Marge
            </a>
            <a class="nav-link text-dark"
               href="{{ route('materiel.cost-control.plateau.projets-termines') }}">
                <i class="fas fa-file-alt me-1"></i>Rapports
            </a>
        </nav>
    </div>
</div>
