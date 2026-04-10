@extends('layouts.app')

@section('title', 'Rapports Audit - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Rapports d'Audit</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('audit.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="{{ route('audit.controles') }}" class="btn btn-outline-primary">
                <i class="fas fa-clipboard-check me-2"></i>Contrôles
            </a>
            <a href="{{ route('audit.rapports') }}" class="btn btn-outline-info active">
                <i class="fas fa-file-alt me-2"></i>Rapports
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rapports Mensuels</h6>
                </div>
                <div class="card-body">
                    <p>Télécharger le récapitulatif des audits du mois.</p>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-download me-2"></i>Télécharger PDF
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Conformité</h6>
                </div>
                <div class="card-body">
                    <p>Analyse de la conformité par département.</p>
                    <button class="btn btn-success w-100">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Incidents</h6>
                </div>
                <div class="card-body">
                    <p>Liste des alertes et incidents critiques.</p>
                    <button class="btn btn-warning w-100">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
