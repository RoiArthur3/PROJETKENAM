<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Requêtes - KENAM SERVICES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid bg-light min-vh-100">
        <!-- Header -->
        <div class="bg-white shadow-sm p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-success">
                        <i class="fas fa-tasks me-2"></i>
                        Module Requêtes
                    </h1>
                    <small class="text-muted">Gestion des requêtes inter-services</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name }} ({{ auth()->user()->getRoleNames()->first() }})
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Navigation rapide -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-compass me-2 text-primary"></i>
                                Navigation rapide
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    @if(auth()->user()->canAccessModule('operations'))
                                        <a href="{{ url('/operations') }}" class="btn btn-success w-100 mb-2">
                                            <i class="fas fa-tasks me-2"></i>Requêtes
                                            <span class="badge bg-light text-success ms-2">Actif</span>
                                        </a>
                                    @else
                                        <button class="btn btn-secondary w-100 mb-2" disabled>
                                            <i class="fas fa-tasks me-2"></i>Requêtes
                                            <span class="badge bg-dark ms-2">Non autorisé</span>
                                        </button>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if(auth()->user()->canAccessModule('operations'))
                                        <button class="btn btn-outline-primary w-100 mb-2" disabled>
                                            <i class="fas fa-check-circle me-2"></i>Suivi & Validation
                                            <span class="badge bg-primary ms-2">Inclus</span>
                                        </button>
                                    @else
                                        <button class="btn btn-outline-secondary w-100 mb-2" disabled>
                                            <i class="fas fa-check-circle me-2"></i>Suivi & Validation
                                            <span class="badge bg-dark ms-2">Non autorisé</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>
                            Bienvenue dans le module Requêtes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Manager RH</strong> - Vous avez accès aux fonctionnalités suivantes :
                        </div>

                        <h6><i class="fas fa-check-circle text-success me-2"></i>Fonctionnalités disponibles</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-plus text-success me-2"></i>
                                <strong>Créer des requêtes</strong> - Soumettre des demandes inter-services
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-eye text-success me-2"></i>
                                <strong>Voir les requêtes</strong> - Consulter toutes les requêtes existantes
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-edit text-success me-2"></i>
                                <strong>Modifier les requêtes</strong> - Mettre à jour les informations
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <strong>Suivi</strong> - Suivre l'état d'avancement des requêtes
                            </li>
                        </ul>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <a href="{{ url('/operations') }}" class="btn btn-success">
                                <i class="fas fa-list me-2"></i>Voir toutes les requêtes
                            </a>
                            <a href="{{ url('/operations/create') }}" class="btn btn-outline-success">
                                <i class="fas fa-plus me-2"></i>Nouvelle requête
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Vos permissions
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6>Modules accessibles</h6>
                        <div class="list-group list-group-flush">
                            @if(auth()->user()->canAccessModule('operations'))
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-tasks text-success me-2"></i>Requêtes</span>
                                    <span class="badge bg-success rounded-pill">✅</span>
                                </div>
                            @endif

                            @if(auth()->user()->canAccessModule('fleet'))
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-car text-primary me-2"></i>Parc Auto</span>
                                    <span class="badge bg-success rounded-pill">✅</span>
                                </div>
                            @endif

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-dashboard text-muted me-2"></i>Dashboard</span>
                                <span class="badge bg-secondary rounded-pill">❌</span>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-users text-muted me-2"></i>RH</span>
                                <span class="badge bg-secondary rounded-pill">❌</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
