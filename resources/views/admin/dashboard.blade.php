@extends('layouts.app')

@section('title', 'Administration - KENAM Services')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog text-primary mr-2"></i>
            Administration & Paramétrage
        </h1>
        <div class="d-flex">
            <small class="text-muted mt-2 mr-3">
                <i class="fas fa-user-shield"></i> Administrateur: {{ auth()->user()->name }}
            </small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Utilisateurs actifs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['active_users'] ?? 0 }} / {{ $stats['total_users'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Opérations totales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_operations'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Modules actifs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['system_modules'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                État système
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge badge-success">Opérationnel</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sections de paramétrage -->
    <div class="row">
        <!-- Gestion des utilisateurs -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users-cog mr-2"></i>
                        Gestion des Utilisateurs
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Gérer les comptes utilisateurs, rôles et permissions.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list mr-1"></i> Voir tous les utilisateurs
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus mr-1"></i> Ajouter un utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paramètres système -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-sliders-h mr-2"></i>
                        Paramètres Système
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Configuration générale de l'application et paramètres d'entreprise.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.settings') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-cogs mr-1"></i> Paramètres généraux
                        </a>
                        <a href="{{ route('admin.system') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-server mr-1"></i> Configuration système
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance système -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-tools mr-2"></i>
                        Maintenance Système
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Outils de maintenance, sauvegardes et monitoring système.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.sauvegarde') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-save mr-1"></i> Sauvegardes
                        </a>
                        <a href="{{ route('admin.sante-systeme') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-heartbeat mr-1"></i> Santé système
                        </a>
                        <button onclick="clearCache()" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash mr-1"></i> Vider le cache
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs et audit -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-file-alt mr-2"></i>
                        Logs & Audit
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Consulter les logs système et l'historique des activités.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.logs') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-list mr-1"></i> Voir les logs
                        </a>
                        <a href="{{ route('controle-audit.historique') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history mr-1"></i> Audit système
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt mr-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <button onclick="clearCache()" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-eraser fa-2x d-block mb-2"></i>
                                <small>Vider Cache</small>
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.sante-systeme') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-stethoscope fa-2x d-block mb-2"></i>
                                <small>Diagnostic</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.sauvegarde') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-download fa-2x d-block mb-2"></i>
                                <small>Sauvegarde</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.logs') }}" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-search fa-2x d-block mb-2"></i>
                                <small>Logs</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour vider le cache
function clearCache() {
    if (confirm('Êtes-vous sûr de vouloir vider le cache de l\'application ?')) {
        fetch('{{ route("admin.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache vidé avec succès !');
                location.reload();
            } else {
                alert('Erreur lors du nettoyage du cache.');
            }
        })
        .catch(error => {
            alert('Erreur de connexion.');
        });
    }
}
</script>
@endsection
