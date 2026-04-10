                                                                @extends('layouts.app')

@section('title', 'Dashboard Opérations | KENAM SERVICES')

@push('styles')
<link href="{{ asset('css/dashboard-modern.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-clickable.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-layout.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">

        <!-- MODERN DASHBOARD HEADER -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-tachometer-alt me-3"></i>Dashboard des Opérations
                    </h1>
                    <p class="subtitle mb-0">
                        Gestion et suivi des opérations KENAM SERVICES
                        @if(Auth::user()->role === 'agent')
                            - Mes opérations
                        @elseif(Auth::user()->role === 'moderator' || Auth::user()->role === 'moderateur')
                            - Espace modérateur
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-light btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Actualiser
                        </button>
                        <a href="{{ route('operations.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list me-1"></i>Toutes les opérations
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPIs PRINCIPAUX -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Total Opérations</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['total_operations'] }}</h3>
                                <div class="mt-2 small text-primary">
                                    <i class="fas fa-list me-1"></i>Toutes périodes
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-primary text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Montant Total</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['decaissement_total'], 0, ',', ' ') }} <small class="fs-6">FCFA</small></h3>
                                <div class="mt-2 small text-info">
                                    <i class="fas fa-money-bill-wave me-1"></i>Décaissements
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-info text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-coins"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">En Cours</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['operations_en_cours'] }}</h3>
                                <div class="mt-2 small text-warning">
                                    <i class="fas fa-spinner me-1"></i>En traitement
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-warning text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1 small fw-bold text-uppercase">Terminées</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['operations_terminees'] }}</h3>
                                <div class="mt-2 small text-success">
                                    <i class="fas fa-check-circle me-1"></i>Achevées
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-success text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-check-double"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🚨 ALERTES CRITIQUES -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm bg-white border-start border-warning border-4" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-warning mb-1 small fw-bold text-uppercase">En Attente</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['en_attente_validation'] }}</h3>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-hourglass-half me-1"></i>Validation requise
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-warning text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm bg-white border-start border-success border-4" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-success mb-1 small fw-bold text-uppercase">Approuvées</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['approuvees'] }}</h3>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-thumbs-up me-1"></i>Validées
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-success text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm bg-white border-start border-danger border-4" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-danger mb-1 small fw-bold text-uppercase">Rejetées</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['rejetees'] }}</h3>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-times-circle me-1"></i>Non validées
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-danger text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 shadow-sm bg-white border-start border-info border-4" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-info mb-1 small fw-bold text-uppercase">En Retard</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['operations_en_retard'] }}</h3>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Dépassées
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="avatar-circle bg-info text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-bell"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- SYNCHRONISATION AVEC COST CONTROL -->
        <div class="section-card">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="fas fa-sync me-2"></i>Synchronisé avec Cost Control & Missions
                </h6>
                <div class="section-actions">
                    <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-cog me-1"></i>Gérer
                    </a>
                </div>
            </div>
            <div class="section-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body text-center">
                                        <div class="avatar-circle bg-primary text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                            <i class="fas fa-database"></i>
                                        </div>
                                        <h5 class="card-title">{{ number_format($operations['total'] ?? 0, 0, ',', ' ') }}</h5>
                                        <p class="card-text text-muted small">Total opérations</p>
                                    </div>
                                </div>
                            <div class="col-md-3">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body text-center">
                                        <div class="avatar-circle bg-info text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                            <i class="fas fa-car"></i>
                                        </div>
                                        <h5 class="card-title">{{ number_format($operations['missions_actives'] ?? 0, 0, ',', ' ') }}</h5>
                                        <p class="card-text text-muted small">Total missions</p>
                                    </div>
                                </div>
                            <div class="col-md-3">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body text-center">
                                        <div class="avatar-circle bg-success text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <h5 class="card-title">{{ number_format($operations['missions_mois'] ?? 0, 0, ',', ' ') }}</h5>
                                        <p class="card-text text-muted small">Missions terminées</p>
                                    </div>
                                </div>
                            <div class="col-md-3">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body text-center">
                                        <div class="avatar-circle bg-warning text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <h5 class="card-title">{{ number_format($operations['en_cours'] ?? 0, 0, ',', ' ') }}</h5>
                                        <p class="card-text text-muted small">Missions en cours</p>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- GANTT DES MISSIONS -->
        <div class="section-card">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="fas fa-project-diagram me-2"></i>Planning des Missions
                </h6>
                <div class="section-actions">
                    <a href="{{ route('projets.dashboard') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-tasks me-1"></i>Gérer les missions
                    </a>
                </div>
            </div>
            <div class="section-body">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <div id="gantt-chart" style="height: 400px; position: relative; overflow: auto;">
                            @if(isset($missions) && $missions->count() > 0)
                                <div class="gantt-container" style="font-family: Arial, sans-serif; font-size: 12px;">
                                    <!-- En-tête du Gantt -->
                                    <div class="gantt-header" style="display: flex; border-bottom: 2px solid #dee2e6; padding: 10px 0; background: #f8f9fa;">
                                        <div style="width: 200px; font-weight: bold; padding: 5px;">Véhicule</div>
                                        <div style="width: 150px; font-weight: bold; padding: 5px;">Chauffeur</div>
                                        <div style="width: 200px; font-weight: bold; padding: 5px;">Destination</div>
                                        <div style="width: 150px; font-weight: bold; padding: 5px;">Statut</div>
                                        <div style="width: 100px; font-weight: bold; padding: 5px;">Dates</div>
                                    </div>

                                    <!-- Lignes du Gantt -->
                                    @foreach($missions as $mission)
                                        <div class="gantt-row" style="display: flex; border-bottom: 1px solid #e9ecef; min-height: 40px; align-items: center;">
                                            <div style="width: 200px; padding: 5px; border-right: 1px solid #e9ecef;">
                                                <i class="fas fa-car text-primary"></i>
                                                <span class="ms-2">{{ $mission->vehicle->immatriculation ?? 'N/A' }}</span>
                                            </div>
                                            <div style="width: 150px; padding: 5px; border-right: 1px solid #e9ecef;">
                                                <i class="fas fa-user text-info"></i>
                                                <span class="ms-2">{{ $mission->personnel->name ?? 'N/A' }}</span>
                                            </div>
                                            <div style="width: 200px; padding: 5px; border-right: 1px solid #e9ecef;">
                                                <span>{{ $mission->destination ?? 'N/A' }}</span>
                                            </div>
                                            <div style="width: 150px; padding: 5px; border-right: 1px solid #e9ecef;">
                                                @php
                                                    $statusColor = '#6c757d'; // Gris par défaut
                                                    $statusText = 'En attente';

                                                    if($mission->status === 'completed') {
                                                        $statusColor = '#28a745'; // Vert
                                                        $statusText = 'Terminée';
                                                    } elseif($mission->status === 'in_progress') {
                                                        $statusColor = '#ffc107'; // Jaune
                                                        $statusText = 'En cours';
                                                    } elseif($mission->status === 'assigned') {
                                                        $statusColor = '#17a2b8'; // Bleu
                                                        $statusText = 'Assignée';
                                                    }
                                                @endphp
                                                <span style="color: {{ $statusColor }}; font-weight: bold;">{{ $statusText }}</span>
                                            </div>
                                            <div style="width: 100px; padding: 5px; border-right: 1px solid #e9ecef;">
                                                <span style="font-size: 11px;">
                                                    {{ $mission->start_at ? $mission->start_at->format('d/m') : 'N/A' }}
                                                    @if($mission->end_at && $mission->start_at)
                                                        - {{ $mission->end_at->format('d/m') }}
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- Barre de progression -->
                                            @if($mission->start_at && $mission->end_at)
                                                @php
                                                    $totalDays = $mission->start_at->diffInDays($mission->end_at);
                                                    $currentDay = now()->diffInDays($mission->start_at);
                                                    $progress = min(100, max(0, ($currentDay / $totalDays) * 100));
                                                @endphp
                                                <div style="width: 100%; height: 4px; background: #e9ecef; margin: 0 5px;">
                                                    <div style="width: {{ $progress }}%; height: 100%; background: linear-gradient(90deg, #007bff, #0056b3); border-radius: 2px;"></div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-project-diagram fa-3x mb-3"></i>
                                    <h5>Aucune mission disponible</h5>
                                    <p class="small">Les missions apparaîtront ici une fois créées.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

    <!-- SECTION RH -->
    <div class="section-card">
        <div class="section-header">
            <h6 class="section-title">
                <i class="fas fa-users-cog me-2"></i>Ressources Humaines
            </h6>
            <div class="section-actions">
                <a href="{{ route('rh.personnel.dashboard') }}" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-tachometer-alt me-1"></i>Tableau de bord RH
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-info text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">{{ App\Models\User::count() }}</h5>
                            <p class="card-text text-muted small">Total employés</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-success text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5 class="card-title">{{ \App\Models\Personnel::where('statut', 'ACTIF')->count() }}</h5>
                            <p class="card-text text-muted small">Personnel actif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-warning text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h5 class="card-title">{{ \App\Models\PersonnelConge::where('statut', 'EN_ATTENTE')->count() }}</h5>
                            <p class="card-text text-muted small">Congés en attente</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-primary text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="card-title">{{ \App\Models\Pointage::whereDate('date_pointage', '>=', now()->subDays(7))->count() }}</h5>
                            <p class="card-text text-muted small">Pointages 7 derniers jours</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-12">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-link me-2"></i>Accès Rapide RH
                            </h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('rh.personnel.dashboard') }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-users me-1"></i>Personnel
</a>
                                <a href="{{ route('rh.pointages.index') }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-clock me-1"></i>Pointages
                                </a>
                                <a href="{{ route('rh.conges.index') }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-calendar me-1"></i>Congés
                                </a>
                                <a href="{{ route('rh.personnel.contrats.index') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-contract me-1"></i>Contrats
                                </a>
                                <a href="{{ route('rh.personnel.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-money-bill-wave me-1"></i>Paie
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION VALIDATION D'OPÉRATIONS -->
    <div class="section-card">
        <div class="section-header">
            <h6 class="section-title">
                <i class="fas fa-check-circle me-2"></i>Validation d'Opérations
            </h6>
            <div class="section-actions">
                <a href="{{ route('operations.validations.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-tasks me-1"></i>Gérer les validations
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-success text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h5 class="card-title">{{ $operations['approuvees'] ?? 0 }}</h5>
                            <p class="card-text text-muted small">Opérations approuvées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-warning text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="card-title">{{ $operations['en_cours'] ?? 0 }}</h5>
                            <p class="card-text text-muted small">En cours de validation</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-info text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="card-title">{{ $operations['rejetees'] ?? 0 }}</h5>
                            <p class="card-text text-muted small">Opérations rejetées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-danger text-white mb-3" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto;">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <h5 class="card-title">{{ $operations['terminees'] ?? 0 }}</h5>
                            <p class="card-text text-muted small">Opérations terminées</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-12">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-chart-line me-2"></i>Statistiques de Validation
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4 class="text-success">{{ round(($operations['approuvees'] / max(1, $operations['total'] ?? 1)) * 100, 1) }}%</h4>
                                        <p class="text-muted small">Taux d'approbation</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4 class="text-warning">{{ round(($operations['en_cours'] / max(1, $operations['total'] ?? 1)) * 100, 1) }}%</h4>
                                        <p class="text-muted small">En cours</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h4 class="text-danger">{{ round(($operations['rejetees'] / max(1, $operations['total'] ?? 1)) * 100, 1) }}%</h4>
                                        <p class="text-muted small">Rejetées</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OPÉRATIONS RÉCENTES -->
        <div class="section-card">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="fas fa-clock me-2"></i>Opérations Récentes
                </h6>
                <div class="section-actions">
                    <a href="{{ route('operations.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Voir tout
                    </a>
                </div>
            </div>
            <div class="section-body">
                @if($recentOperations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" style="border-radius: 8px; overflow: hidden;">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-tag me-1"></i>Titre</th>
                                    <th><i class="fas fa-user me-1"></i>Client</th>
                                    <th><i class="fas fa-info-circle me-1"></i>Statut</th>
                                    <th><i class="fas fa-flag me-1"></i>Priorité</th>
                                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                                    <th><i class="fas fa-cog me-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOperations as $operation)
                                    <tr style="cursor: pointer;" onclick="window.location.href='{{ route('operations.show', $operation) }}'">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-2" style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                    <i class="fas fa-clipboard"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $operation->titre }}</div>
                                                    <div class="small text-muted">{{ $operation->typeOperation->libelle ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-info text-white me-2" style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                {{ $operation->client->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $operation->statut_courant == 'terminee' || $operation->statut_courant == 'payee' ? 'success' : ($operation->statut_courant == 'en_cours' || $operation->statut_courant == 'en_execution' ? 'warning' : ($operation->statut_courant == 'rejetee' ? 'danger' : 'primary')) }} rounded-pill">
                                                <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                                                {{ $operation->statut_courant }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $operation->priorite == 'urgent' ? 'danger' : ($operation->priorite == 'haute' ? 'warning' : 'info') }} rounded-pill">
                                                <i class="fas fa-flag me-1" style="font-size: 8px;"></i>
                                                {{ $operation->priorite }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div>{{ $operation->created_at->format('d/m/Y') }}</div>
                                                <div class="text-muted">{{ $operation->created_at->format('H:i') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('operations.show', $operation) }}" class="btn btn-outline-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
                                                    <a href="{{ route('operations.edit', $operation) }}" class="btn btn-outline-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="avatar-circle bg-light text-muted mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h5 class="text-muted mb-2">Aucune opération récente</h5>
                        <p class="text-muted mb-4">
                            @if(Auth::user()->role === 'agent')
                                Vous n'avez pas encore créé d'opérations
                            @else
                                Aucune opération n'a été enregistrée récemment
                            @endif
                        </p>
                        <a href="{{ route('operations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Créer une opération
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- SECTION FOOTER -->
        <div class="text-center mt-4 mb-4">
            <div class="data-source-badge bdd">
                <i class="fas fa-database me-1"></i>Données Synchronisées
            </div>
            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>Dernière mise à jour: {{ now()->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
// Initialisation du Gantt chart
document.addEventListener('DOMContentLoaded', function() {
    const ganttContainer = document.getElementById('gantt-chart');
    if (ganttContainer) {
        // Placeholder pour le Gantt chart
        // En production, vous pouvez intégrer une librairie comme Frappe Gantt ou DHTMLX Gantt
        ganttContainer.innerHTML = `
            <div class="gantt-placeholder">
                <div class="text-center py-5">
                    <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Diagramme Gantt</h5>
                    <p class="text-muted small">Visualisation du planning des missions</p>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" onclick="alert('Fonctionnalité Gantt à implémenter avec une librairie JavaScript')">
                            <i class="fas fa-info-circle me-1"></i>Guide d'implémentation
                        </button>
                    </div>
                </div>
            </div>
            <style>
                .gantt-placeholder {
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 2px dashed #dee2e6;
                    border-radius: 8px;
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                }
            </style>
        `;
    }
});
</script>
@endsection
