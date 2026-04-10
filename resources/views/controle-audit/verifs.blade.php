@extends('layouts.app')

@section('title', 'Vérifications - Contrôle & Audit - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-check me-2 text-primary"></i>Vérifications
            </h1>
            <p class="text-muted mb-0">Contrôles qualité et vérifications opérationnelles</p>
        </div>
        <a href="{{ route('controle-audit.planifs') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Vérification
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Type de vérification</label>
                    <select class="form-select">
                        <option>Toutes</option>
                        <option>Qualité</option>
                        <option>Sécurité</option>
                        <option>Conformité</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option>Toutes</option>
                        <option>En cours</option>
                        <option>Validée</option>
                        <option>Rejetée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select class="form-select">
                        <option>Cette semaine</option>
                        <option>Ce mois</option>
                        <option>Cette année</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des vérifications -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 fw-bold text-primary">Vérifications en cours</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Objet</th>
                            <th>Responsable</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>VER-2024-001</strong></td>
                            <td>Qualité</td>
                            <td>Contrôle palettes réception</td>
                            <td>Jean Dupont</td>
                            <td>07/11/2024</td>
                            <td><span class="badge bg-success">Validée</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>VER-2024-002</strong></td>
                            <td>Sécurité</td>
                            <td>Vérification équipements</td>
                            <td>Marie Curie</td>
                            <td>08/11/2024</td>
                            <td><span class="badge bg-warning">En cours</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
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
