@extends('layouts.app')

@section('title', 'Bons de Livraison - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-truck me-2 text-success"></i>Bons de Livraison
            </h1>
            <p class="text-muted mb-0">Gestion des bons de livraison clients</p>
        </div>
        <div>
            <a href="{{ route('commercial.bon-livraison.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Nouveau Bon de Livraison
            </a>
            <button class="btn btn-outline-secondary ms-2" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" placeholder="N°, client, référence...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option value="">Tous</option>
                        <option value="en_preparation">En préparation</option>
                        <option value="en_livraison">En livraison</option>
                        <option value="livre">Livré</option>
                        <option value="retour">Retour</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button class="btn btn-outline-success">
                            <i class="fas fa-search me-1"></i> Rechercher
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des bons de livraison -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-success">
                <i class="fas fa-list me-2"></i>Liste des Bons de Livraison
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>N° Bon</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Bon de commande</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">BL-2024-001</td>
                            <td>18/01/2024</td>
                            <td>SOCIETE GENERALE</td>
                            <td class="text-primary">BC-2024-001</td>
                            <td class="text-success fw-bold">2 500 000 FCFA</td>
                            <td><span class="badge bg-warning">En préparation</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">BL-2024-002</td>
                            <td>16/01/2024</td>
                            <td>ORANGE CI</td>
                            <td class="text-primary">BC-2024-002</td>
                            <td class="text-success fw-bold">1 800 000 FCFA</td>
                            <td><span class="badge bg-info">En livraison</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">BL-2024-003</td>
                            <td>14/01/2024</td>
                            <td>SOLIBRA</td>
                            <td class="text-primary">BC-2024-003</td>
                            <td class="text-success fw-bold">3 200 000 FCFA</td>
                            <td><span class="badge bg-success">Livré</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">BL-2024-004</td>
                            <td>12/01/2024</td>
                            <td>MTN CI</td>
                            <td class="text-primary">BC-2024-005</td>
                            <td class="text-success fw-bold">850 000 FCFA</td>
                            <td><span class="badge bg-danger">Retour</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Pagination">
                <ul class="pagination justify-content-center mt-3">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Précédent</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Suivant</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
