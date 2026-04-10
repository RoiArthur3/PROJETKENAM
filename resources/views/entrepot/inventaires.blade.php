@extends('layouts.app')

@section('title', 'Inventaires - Entrepôt & Magasin')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clipboard-check me-2 text-info"></i>Inventaires</h1>
      <p class="text-muted mb-0">Contrôles de stock et ajustements</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('entrepot.inventaires.export') }}" class="btn btn-outline-primary">
        <i class="fas fa-file-csv me-2"></i>Exporter CSV
      </a>
      <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimer
      </button>
      <button class="btn btn-info"><i class="fas fa-plus me-2"></i>Lancer un Inventaire</button>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
      <h6 class="m-0 fw-bold"><i class="fas fa-list-check me-2"></i>Campagne d'Inventaire</h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label fw-bold">Zone</label>
          <select class="form-select">
            <option>R1</option>
            <option>R2</option>
            <option>R3</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Allée</label>
          <select class="form-select">
            <option>A1</option>
            <option>B4</option>
            <option>C3</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Méthode</label>
          <select class="form-select">
            <option>Inventaire tournant</option>
            <option>Inventaire annuel</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">&nbsp;</label>
          <button class="btn btn-info w-100"><i class="fas fa-play me-2"></i>Démarrer</button>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Inventaires Récents</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="inventairesTable">
          <thead class="table-light">
            <tr>
              <th>N°</th>
              <th>Date</th>
              <th>Zone</th>
              <th>Nb. Articles</th>
              <th>Anomalies</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>INV-2024-031</strong></td>
              <td>07/11/2024</td>
              <td>R2</td>
              <td>48</td>
              <td><span class="badge bg-warning text-dark">3</span></td>
              <td><button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button></td>
            </tr>
            <tr>
              <td><strong>INV-2024-030</strong></td>
              <td>05/11/2024</td>
              <td>R1</td>
              <td>120</td>
              <td><span class="badge bg-success">0</span></td>
              <td><button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(function(){ $('#inventairesTable').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endsection
