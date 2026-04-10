@extends('layouts.app')

@section('title', 'Rapports - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Rapports - Module Magasin
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Actions rapides -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('magasin.dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                                <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-info">
                                    <i class="fas fa-list me-2"></i>Inventaire
                                </a>
                                <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success">
                                    <i class="fas fa-arrow-down me-2"></i>Entrées
                                </a>
                                <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-arrow-up me-2"></i>Sorties
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres de rapports -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Type de rapport</label>
                            <select class="form-select">
                                <option>Mouvements de stock</option>
                                <option>Inventaire complet</option>
                                <option>Valeur du stock</option>
                                <option>Produits en rupture</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date début</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date fin</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Générer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques rapides -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Valeur Totale Stock</h5>
                                    <h3>1 642 500 FCFA</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Entrées Mois</h5>
                                    <h3>15</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Sorties Mois</h5>
                                    <h3>23</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Produits Rupture</h5>
                                    <h3>2</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des rapports -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Rapports Disponibles</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nom du Rapport</th>
                                            <th>Type</th>
                                            <th>Période</th>
                                            <th>Date Génération</th>
                                            <th>Taille</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                Rapport_inventaire_Q4_2025.pdf
                                            </td>
                                            <td><span class="badge bg-info">Inventaire</span></td>
                                            <td>01/10/2025 - 31/12/2025</td>
                                            <td>15/01/2026 14:30</td>
                                            <td>2.3 MB</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-primary" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-excel text-success me-2"></i>
                                                Mouvements_stock_Décembre_2025.xlsx
                                            </td>
                                            <td><span class="badge bg-warning">Mouvements</span></td>
                                            <td>01/12/2025 - 31/12/2025</td>
                                            <td>02/01/2026 09:15</td>
                                            <td>856 KB</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-primary" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                Produits_rupture_Janvier_2026.pdf
                                            </td>
                                            <td><span class="badge bg-danger">Rupture</span></td>
                                            <td>01/01/2026 - 15/01/2026</td>
                                            <td>15/01/2026 16:45</td>
                                            <td>1.1 MB</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-primary" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
