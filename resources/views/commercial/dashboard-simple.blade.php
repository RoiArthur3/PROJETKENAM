@extends('layouts.app')

@section('title', 'Dashboard Commercial - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line me-2 text-primary"></i>Tableau de Bord Commercial
            </h1>
        </div>
        <div>
            <a href="{{ route('commercial.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-vial me-1"></i> Test
            </a>
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Actualiser
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Total Clients</div>
                            <div class="h3 mb-0 text-primary">--</div>
                            <small class="text-muted">Base de données requise</small>
                        </div>
                        <i class="fas fa-users fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Contrats Actifs</div>
                            <div class="h3 mb-0 text-success">--</div>
                            <small class="text-muted">Base de données requise</small>
                        </div>
                        <i class="fas fa-file-contract fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Devis & Proformas</div>
                            <div class="h3 mb-0 text-warning">--</div>
                            <small class="text-muted">Base de données requise</small>
                        </div>
                        <i class="fas fa-file-invoice fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
