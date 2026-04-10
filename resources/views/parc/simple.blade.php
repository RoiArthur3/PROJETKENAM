@extends('layouts.authenticated')
@section('header-title', 'Parc Auto')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-car me-2"></i>
                        Module Parc Auto
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Bienvenue dans le module Parc Auto. Vous avez accès à la gestion complète du parc automobile.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-chart-line me-2 text-primary"></i>Statistiques</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-car text-success me-2"></i>Véhicules actifs: <strong>25</strong></li>
                                <li><i class="fas fa-tools text-warning me-2"></i>En maintenance: <strong>3</strong></li>
                                <li><i class="fas fa-gas-pump text-info me-2"></i>Consommation carburant: <strong>1,250L</strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-cogs me-2 text-primary"></i>Actions rapides</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ url('/parc') }}" class="btn btn-primary">
                                    <i class="fas fa-list me-2"></i>Gestion des véhicules
                                </a>
                                <a href="{{ url('/parc/carburant') }}" class="btn btn-success">
                                    <i class="fas fa-gas-pump me-2"></i>Suivi carburant
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
