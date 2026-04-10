@extends('layouts.app')

@section('title', 'Transferts Internes - Entrepôt & Magasin')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-right-left me-2 text-secondary"></i>Transferts Internes</h1>
      <p class="text-muted mb-0">Déplacements entre emplacements et entrepôts</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('entrepot.transferts.export') }}" class="btn btn-outline-primary">
        <i class="fas fa-file-csv me-2"></i>Exporter CSV
      </a>
      <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimer
      </button>
      <button class="btn btn-secondary"><i class="fas fa-plus me-2"></i>Nouveau Transfert</button>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
      <h6 class="m-0 fw-bold"><i class="fas fa-right-left me-2"></i>Effectuer un Transfert</h6>
    </div>
    <div class="card-body">
      <form>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">Référence</label>
            <input type="text" class="form-control" placeholder="Ex: PN-20T">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Depuis</label>
            <input type="text" class="form-control" placeholder="Ex: R2-C1">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Vers</label>
            <input type="text" class="form-control" placeholder="Ex: R1-A4">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Quantité</label>
            <input type="number" class="form-control" value="1">
          </div>
          <div class="col-12">
            <button type="button" class="btn btn-secondary"><i class="fas fa-save me-2"></i>Transférer</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Transferts Récents</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="transfertsTable">
          <thead class="table-light">
            <tr>
              <th>N°</th>
              <th>Date</th>
              <th>Référence</th>
              <th>Depuis</th>
              <th>Vers</th>
              <th>Qté</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>TR-2024-021</strong></td>
              <td>07/11/2024</td>
              <td>PN-20T</td>
              <td>R2-C1</td>
              <td>R1-A4</td>
              <td>6</td>
            </tr>
            <tr>
              <td><strong>TR-2024-019</strong></td>
              <td>05/11/2024</td>
              <td>KF-PL</td>
              <td>R3-B4</td>
              <td>R3-B2</td>
              <td>10</td>
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
$(function(){ $('#transfertsTable').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endsection
