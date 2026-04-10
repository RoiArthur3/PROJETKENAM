@extends('layouts.app')

@section('title', 'Alertes de Stock - Entrepôt & Magasin')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-triangle-exclamation me-2 text-danger"></i>Alertes de Stock</h1>
      <p class="text-muted mb-0">Seuils bas, ruptures, péremptions</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('entrepot.alertes.export') }}" class="btn btn-outline-primary">
        <i class="fas fa-file-csv me-2"></i>Exporter CSV
      </a>
      <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimer
      </button>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bell me-2"></i>Liste des Alertes</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="alertesTable">
          <thead class="table-light">
            <tr>
              <th>Référence</th>
              <th>Désignation</th>
              <th>Stock</th>
              <th>Seuil Min</th>
              <th>État</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>FH-10T</td>
              <td>Filtre à huile Camion 10T</td>
              <td class="text-danger fw-bold">0</td>
              <td>20</td>
              <td><span class="badge bg-danger">Rupture</span></td>
              <td><a href="{{ route('entrepot.entrees') }}" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Réceptionner</a></td>
            </tr>
            <tr>
              <td>PN-20T</td>
              <td>Pneu 20T</td>
              <td class="text-warning fw-bold">12</td>
              <td>30</td>
              <td><span class="badge bg-warning text-dark">Faible</span></td>
              <td><a href="{{ route('fournisseurs.commandes') }}" class="btn btn-sm btn-primary"><i class="fas fa-cart-plus"></i> Commander</a></td>
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
$(function(){ $('#alertesTable').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endsection
