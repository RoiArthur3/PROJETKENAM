@extends('layouts.app')

@section('title', 'Paramétrage - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog text-primary"></i>
            Paramétrage du Système
        </h1>
    </div>

    <!-- Cartes de paramétrage -->
    <div class="row">
        <!-- Carte Entreprise -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Entreprise
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $entreprise->nom_entreprise ?? 'Non configuré' }}
                            </div>
                            <div class="text-xs text-gray-600 mt-2">
                                @if($entreprise)
                                    <i class="fas fa-check-circle text-success"></i> Configuré
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning"></i> À configurer
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            @if($entreprise && $entreprise->logo_path)
                                <img src="{{ asset($entreprise->logo_path) }}" alt="Logo" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                            @else
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('parametrage.entreprise') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Configurer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Pointage Hikvision -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-purple shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                Pointage Hikvision
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Dispositif IA
                            </div>
                            <div class="text-xs text-gray-600 mt-2">
                                @if(config('hikvision.enabled'))
                                    <i class="fas fa-check-circle text-success"></i> Activé
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning"></i> Désactivé
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-camera fa-2x text-purple"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('parametrage.hikvision.config') }}" class="btn btn-purple btn-sm">
                            <i class="fas fa-cog"></i> Configurer
                        </a>
                        <a href="{{ route('parametrage.hikvision.diagnostic') }}" class="btn btn-info btn-sm ms-2">
                            <i class="fas fa-stethoscope"></i> Diagnostic
                        </a>
                        @if(!config('hikvision.enabled', false))
                            <button class="btn btn-success btn-sm ms-2" onclick="enableHikvision()">
                                <i class="fas fa-power-off"></i> Activer
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Services -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Services
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['services'] ?? 0 }} actifs
                            </div>
                            <div class="text-xs text-gray-600 mt-2">
                                @if($stats['services'] > 0)
                                    <i class="fas fa-check-circle text-success"></i> Configurés
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning"></i> À configurer
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('parametrage.services') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-edit"></i> Configurer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Utilisateurs -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Utilisateurs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['users'] ?? 0 }} total
                            </div>
                            <div class="text-xs text-gray-600 mt-2">
                                @if($stats['users'] > 0)
                                    <i class="fas fa-check-circle text-success"></i> {{ $stats['active_users'] ?? 0 }} actifs
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning"></i> Aucun utilisateur
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.comptes.users.index') }}" class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i> Gérer
</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Types d'Opérations -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Types d'Opérations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['types_operations'] ?? 0 }} types
                            </div>
                            <div class="text-xs text-gray-600 mt-2">
                                @if($stats['types_operations'] > 0)
                                    <i class="fas fa-check-circle text-success"></i> Configurés
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning"></i> À configurer
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('types-operations.index') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-edit"></i> Gérer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Fonctions pour la configuration Hikvision
function enableHikvision() {
    if (confirm('Activer le pointage Hikvision ?')) {
        console.log('Activation de Hikvision...');
        // Logique d'activation
        location.reload();
    }
}
</script>
@endpush
