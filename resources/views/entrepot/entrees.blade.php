@extends('layouts.app')

@section('title', 'Entrées - Entrepôt & Magasin')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-truck-ramp-box me-2 text-success"></i>Entrées</h1>
      <p class="text-muted mb-0">Réceptions fournisseurs et retours</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('entrepot.entrees.export') }}" class="btn btn-outline-primary">
        <i class="fas fa-file-csv me-2"></i>Exporter CSV
      </a>
      <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimer
      </button>
      <a href="{{ route('fournisseurs.commandes') }}" class="btn btn-outline-success"><i class="fas fa-file-signature me-2"></i>Voir Commandes</a>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
      <h6 class="m-0 fw-bold"><i class="fas fa-plus me-2"></i>Nouvelle Réception</h6>
    </div>
    <div class="card-body">
      <form>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">Fournisseur *</label>
            <select class="form-select">
              <option>TOTAL CI</option>
              <option>CIMAF</option>
              <option>PROSUMA</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Bon de commande</label>
            <select class="form-select">
              <option>BC-2024-045</option>
              <option>BC-2024-052</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Date réception</label>
            <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
          </div>
          <div class="col-12">
            <label class="form-label fw-bold">Référence article</label>
            <input type="text" class="form-control" placeholder="Ex: PN-20T">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Quantité</label>
            <input type="number" class="form-control" value="10">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Prix Unitaire (FCFA)</label>
            <input type="number" class="form-control" value="150000">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Emplacement</label>
            <input type="text" class="form-control" placeholder="Ex: R2-C1">
          </div>
          <div class="col-12">
            <button type="button" class="btn btn-success"><i class="fas fa-save me-2"></i>Enregistrer</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Réceptions Récentes</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="entreesTable">
          <thead class="table-light">
            <tr>
              <th>N°</th>
              <th>Date</th>
              <th>Fournisseur</th>
              <th>Référence</th>
              <th>Désignation</th>
              <th>Qté</th>
              <th>PU</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>RC-2024-089</strong></td>
              <td>07/11/2024</td>
              <td>TOTAL CI</td>
              <td>FH-10T</td>
              <td>Filtre à huile 10T</td>
              <td>50</td>
              <td>5 500</td>
              <td class="fw-bold">275 000</td>
            </tr>
            <tr>
              <td><strong>RC-2024-088</strong></td>
              <td>06/11/2024</td>
              <td>CIMAF</td>
              <td>PN-20T</td>
              <td>Pneu 20T</td>
              <td>20</td>
              <td>150 000</td>
              <td class="fw-bold">3 000 000</td>
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
$(function(){
  $('#entreesTable').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } });
});
</script>
@endsection
