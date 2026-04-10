@extends('layouts.app')

@section('title', 'Services Opérationnels')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2 text-primary"></i>Services Opérationnels
            </h1>
            <p class="text-muted mb-0">Gestion des services opérationnels de KENAM SERVICES</p>
        </div>
        <div>
            <a href="{{ route('services.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la gestion complète
            </a>
        </div>
    </div>

    <!-- Message d'information -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Page de redirection</strong><br>
        Cette page est une vue simplifiée. Pour la gestion complète des services opérationnels, 
        utilisez le bouton ci-dessus pour accéder à l'interface principale.
    </div>

    <!-- Cartes de services statiques -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-handshake fa-2x mb-2"></i>
                    <h6>Commercial</h6>
                    <small>Ventes & Marketing</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-screwdriver-wrench fa-2x mb-2"></i>
                    <h6>Opérations</h6>
                    <small>Exécution & Logistique</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h6>RH</h6>
                    <small>Ressources Humaines</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-scale-balanced fa-2x mb-2"></i>
                    <h6>Comptabilité</h6>
                    <small>Finance & Comptabilité</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
