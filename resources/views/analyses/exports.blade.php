@extends('layouts.app')

@section('title', 'Exports de Données - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-export me-2 text-primary"></i>Exports de Données
            </h1>
            <p class="text-muted mb-0">Exportez vos données dans différents formats</p>
        </div>
    </div>

    <!-- Options d'Export -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-file-excel me-2"></i>Export Excel</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Exportez vos données au format Excel (.xlsx) avec mise en forme</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type de données</label>
                        <select class="form-select" id="excelType">
                            <option>Opérations</option>
                            <option>Finances</option>
                            <option>Clients</option>
                            <option>Véhicules</option>
                            <option>Personnel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Période</label>
                        <select class="form-select">
                            <option>Ce mois</option>
                            <option>Ce trimestre</option>
                            <option>Cette année</option>
                            <option>Personnalisé</option>
                        </select>
                    </div>
                    <button class="btn btn-success w-100" onclick="exporterExcel()">
                        <i class="fas fa-download me-2"></i>Télécharger Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-file-pdf me-2"></i>Export PDF</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Générez des rapports PDF professionnels</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type de rapport</label>
                        <select class="form-select" id="pdfType">
                            <option>Rapport mensuel</option>
                            <option>Rapport trimestriel</option>
                            <option>Rapport annuel</option>
                            <option>Rapport personnalisé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfGraphiques" checked>
                            <label class="form-check-label" for="pdfGraphiques">Inclure les graphiques</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pdfTableaux" checked>
                            <label class="form-check-label" for="pdfTableaux">Inclure les tableaux</label>
                        </div>
                    </div>
                    <button class="btn btn-danger w-100" onclick="exporterPDF()">
                        <i class="fas fa-download me-2"></i>Télécharger PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-file-csv me-2"></i>Export CSV</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Exportez vos données au format CSV pour traitement</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Données à exporter</label>
                        <select class="form-select" id="csvType">
                            <option>Toutes les opérations</option>
                            <option>Transactions financières</option>
                            <option>Liste clients</option>
                            <option>Inventaire véhicules</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Séparateur</label>
                        <select class="form-select">
                            <option>Virgule (,)</option>
                            <option>Point-virgule (;)</option>
                            <option>Tabulation</option>
                        </select>
                    </div>
                    <button class="btn btn-info w-100" onclick="exporterCSV()">
                        <i class="fas fa-download me-2"></i>Télécharger CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des Exports -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Historique des Exports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Format</th>
                                    <th>Taille</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>07/11/2024 10:30</td>
                                    <td>Rapport Mensuel</td>
                                    <td><span class="badge bg-danger">PDF</span></td>
                                    <td>2.5 MB</td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>06/11/2024 15:45</td>
                                    <td>Opérations</td>
                                    <td><span class="badge bg-success">Excel</span></td>
                                    <td>1.8 MB</td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>05/11/2024 09:20</td>
                                    <td>Liste Clients</td>
                                    <td><span class="badge bg-info">CSV</span></td>
                                    <td>450 KB</td>
                                    <td><span class="badge bg-success">Terminé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
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
    </div>
</div>

<script>
function exporterExcel() {
    alert('Export Excel en cours...');
}

function exporterPDF() {
    alert('Génération PDF en cours...');
}

function exporterCSV() {
    alert('Export CSV en cours...');
}
</script>
@endsection
