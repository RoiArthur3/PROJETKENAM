@extends('layouts.app')

@section('title', 'Validations - KENAM SERVICES')

@include('validations._helpers')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-check-circle me-2 text-primary"></i>Validations
        </h1>
    </div>

    <!-- Navigation simplifiée -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <a href="{{ route('validations.pending') }}" class="text-decoration-none">
                        <div class="card border-warning">
                            <div class="card-body">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h5>En Attente</h5>
                                <h3 class="text-warning">{{ \App\Models\Operation::where('statut_courant', 'pending_validation')->count() }}</h3>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('validations.approved') }}" class="text-decoration-none">
                        <div class="card border-success">
                            <div class="card-body">
                                <i class="fas fa-check fa-2x text-success mb-2"></i>
                                <h5>Approuvées</h5>
                                <h3 class="text-success">{{ \App\Models\Operation::where('statut_courant', 'approuvee')->count() }}</h3>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('validations.rejected') }}" class="text-decoration-none">
                        <div class="card border-danger">
                            <div class="card-body">
                                <i class="fas fa-times fa-2x text-danger mb-2"></i>
                                <h5>Rejetées</h5>
                                <h3 class="text-danger">{{ \App\Models\Operation::where('statut_courant', 'rejetee')->count() }}</h3>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <!-- Lien Historique temporairement désactivé -->
                    <div class="card border-secondary">
                        <div class="card-body opacity-50">
                            <i class="fas fa-history fa-2x text-secondary mb-2"></i>
                            <h5>Historique</h5>
                            <h3 class="text-secondary">{{ \App\Models\Operation::count() }}</h3>
                            <small class="text-muted">(Désactivé)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-bolt me-2"></i>Actions Rapides
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('operations.create') }}" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="fas fa-plus me-2"></i>Créer une Opération
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('validations.pending') }}" class="btn btn-warning btn-lg w-100 mb-3">
                        <i class="fas fa-clock me-2"></i>Validations en Attente
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
