@extends('layouts.app')

@section('title', 'Rapports - Contrôle & Audit - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-alt me-2 text-primary"></i>Rapports d'Audit
            </h1>
            <p class="text-muted mb-0">Rapports de contrôle et recommandations</p>
        </div>
        <a href="{{ route('controle-audit.planifs') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Rapport
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Type de rapport</label>
                    <select class="form-select">
                        <option>Tous</option>
                        <option>Qualité</option>
                        <option>Sécurité</option>
                        <option>Conformité</option>
                        <option>Technique</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select class="form-select">
                        <option>Ce mois</option>
                        <option>Ce trimestre</option>
                        <option>Cette année</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select">
                        <option>Tous</option>
                        <option>Brouillon</option>
                        <option>Validé</option>
                        <option>Archivé</option>
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

    <!-- Liste des rapports -->
    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 fw-bold text-primary">Rapports récents</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Auteur</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>RAP-2024-001</strong></td>
                                    <td>Audit qualité mensuel</td>
                                    <td>Qualité</td>
                                    <td>Jean Dupont</td>
                                    <td>01/11/2024</td>
                                    <td><span class="badge bg-success">Validé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>RAP-2024-002</strong></td>
                                    <td>Contrôle sécurité plateforme</td>
                                    <td>Sécurité</td>
                                    <td>Marie Curie</td>
                                    <td>15/10/2024</td>
                                    <td><span class="badge bg-warning">En révision</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card h-100" style="aspect-ratio: 1; max-height: 400px;">
                <div class="card-header">
                    <h6 class="m-0 fw-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                        <canvas id="rapportsChart" style="max-width: 100%; max-height: 100%;"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Rapports validés</span>
                            <strong>85%</strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/chart.js') }}"></script>
<script>
const ctx = document.getElementById('rapportsChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Qualité', 'Sécurité', 'Conformité', 'Technique'],
        datasets: [{
            data: [12, 8, 6, 4],
            backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endsection
