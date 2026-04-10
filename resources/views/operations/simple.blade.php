@extends('layouts.authenticated')
@section('header-title', 'Requêtes')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>
                        Module Requêtes
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Bienvenue dans le module des Requêtes. Vous avez accès à toutes les fonctionnalités de gestion des requêtes inter-services.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-chart-line me-2 text-primary"></i>Statistiques</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Requêtes en attente: <strong>12</strong></li>
                                <li><i class="fas fa-clock text-warning me-2"></i>Requêtes en cours: <strong>8</strong></li>
                                <li><i class="fas fa-check-double text-success me-2"></i>Requêtes terminées: <strong>45</strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-cogs me-2 text-primary"></i>Actions rapides</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ url('/requetes/create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Nouvelle requête
                                </a>
                                <a href="{{ url('/requetes') }}" class="btn btn-primary">
                                    <i class="fas fa-list me-2"></i>Voir toutes les requêtes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
