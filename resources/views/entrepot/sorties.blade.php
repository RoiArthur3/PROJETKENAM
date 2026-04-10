@extends('layouts.app')

@section('title', 'Sorties - Entrepôt & Magasin')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-dolly me-2 text-primary"></i>Sorties</h1>
      <p class="text-muted mb-0">Consommations vers Opérations, Parc Auto et Projets</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('entrepot.sorties.export') }}" class="btn btn-outline-primary">
        <i class="fas fa-file-csv me-2"></i>Exporter CSV
      </a>
      <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimer
      </button>
      <a href="{{ route('projets.index') }}" class="btn btn-outline-primary"><i class="fas fa-diagram-project me-2"></i>Vers Projets</a>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
      <h6 class="m-0 fw-bold"><i class="fas fa-minus me-2"></i>Nouvelle Sortie</h6>
    </div>
    <div class="card-body">
      <form>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">Destination *</label>
            <select class="form-select">
              <option>Opérations</option>
              <option>Parc Auto</option>
              <option>Projet</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Référence Projet / Opération</label>
            <input type="text" class="form-control" placeholder="Ex: PRJ-2024-015 ou OPS-2024-021">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Date</label>
            <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
          </div>
          <div class="col-12">
            <label class="form-label fw-bold">Référence article</label>
            <input type="text" class="form-control" placeholder="Ex: KF-PL">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Quantité</label>
            <input type="number" class="form-control" value="1">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Emplacement</label>
            <input type="text" class="form-control" placeholder="Ex: R3-B4">
          </div>
          <div class="col-12">
            <button type="button" class="btn btn-primary"><i class="fas fa-save me-2"></i>Valider Sortie</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Sorties Récentes</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="sortiesTable">
          <thead class="table-light">
            <tr>
              <th>N°</th>
              <th>Date</th>
              <th>Destination</th>
              <th>Ref. Projet/Op.</th>
              <th>Référence</th>
              <th>Désignation</th>
              <th>Qté</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>SO-2024-112</strong></td>
              <td>07/11/2024</td>
              <td>Projet</td>
              <td>PRJ-2024-018</td>
              <td>KF-PL</td>
              <td>Kit frein PL</td>
              <td>2</td>
            </tr>
            <tr>
              <td><strong>SO-2024-109</strong></td>
              <td>06/11/2024</td>
              <td>Parc Auto</td>
              <td>Maintenance CI-AB-1234-XX</td>
              <td>FH-10T</td>
              <td>Filtre à huile 10T</td>
              <td>1</td>
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
$(function(){ $('#sortiesTable').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endsection
