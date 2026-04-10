@extends('layouts.app')

@section('title', 'Validation Technique - Contrôle & Audit - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-microchip me-2 text-primary"></i>Validation Technique
            </h1>
            <p class="text-muted mb-0">Validation des aspects techniques et technologiques</p>
        </div>
        <a href="{{ route('controle-audit.planifs') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Validation
        </a>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Validations en attente</div>
                            <div class="h3 mb-0 text-primary">12</div>
                            <small class="text-muted">À traiter</small>
                        </div>
                        <i class="fas fa-clock fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Validées ce mois</div>
                            <div class="h3 mb-0 text-success">8</div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> +15%</small>
                        </div>
                        <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">En révision</div>
                            <div class="h3 mb-0 text-warning">3</div>
                            <small class="text-warning">Corrections demandées</small>
                        </div>
                        <i class="fas fa-tools fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Taux de conformité</div>
                            <div class="h3 mb-0 text-info">94%</div>
                            <small class="text-muted">Objectif 95%</small>
                        </div>
                        <i class="fas fa-percentage fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des validations -->
    <div class="card">
        <div class="card-header">
            <h6 class="m-0 fw-bold text-primary">Validations techniques en attente</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Projet/Module</th>
                            <th>Type validation</th>
                            <th>Demandé par</th>
                            <th>Date demande</th>
                            <th>Priorité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>VAL-TECH-2024-001</strong></td>
                            <td>Module Transport</td>
                            <td>Validation API</td>
                            <td>Équipe Dev</td>
                            <td>05/11/2024</td>
                            <td><span class="badge bg-danger">Haute</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>VAL-TECH-2024-002</strong></td>
                            <td>Dashboard RH</td>
                            <td>Validation UX/UI</td>
                            <td>Marie Martin</td>
                            <td>06/11/2024</td>
                            <td><span class="badge bg-warning">Moyenne</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
