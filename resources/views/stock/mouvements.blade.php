@extends('layouts.app')

@section('title', 'Stock - Mouvements')

@section('content')
<div class="container-fluid">
  <div class="card shadow mb-3">
    <div class="card-body">
      <form class="row g-2">
        <div class="col-md-3">
          <label class="form-label small">Produit</label>
          <input type="text" class="form-control form-control-sm" placeholder="Nom ou référence">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Type</label>
          <select class="form-select form-select-sm">
            <option value="">Tous</option>
            <option>Entrée</option>
            <option>Sortie</option>
            <option>Transfert</option>
            <option>Ajustement</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Entrepôt</label>
          <select class="form-select form-select-sm">
            <option value="">Tous</option>
            <option>WH-ABJ-01</option>
            <option>WH-YAK-02</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Du</label>
          <input type="date" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Au</label>
          <input type="date" class="form-control form-control-sm">
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="button" class="btn btn-sm btn-secondary w-100">Filtrer</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-outline-success" href="{{ route('stock.mouvements') }}/export"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="mouvementsTable">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Produit</th>
              <th>Quantité</th>
              <th>Entrepôt</th>
              <th>Référence</th>
              <th>Utilisateur</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>2025-10-20 14:30</td>
              <td><span class="badge bg-success">Entrée</span></td>
              <td>Casque de chantier</td>
              <td>+50</td>
              <td>WH-ABJ-01</td>
              <td>ENT-2025-001</td>
              <td>Admin</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
              </td>
            </tr>
            <tr>
              <td>2025-10-19 09:15</td>
              <td><span class="badge bg-danger">Sortie</span></td>
              <td>Gants de protection</td>
              <td>-30</td>
              <td>WH-ABJ-01</td>
              <td>SOR-2025-012</td>
              <td>Dupont</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
              </td>
            </tr>
            <tr>
              <td>2025-10-18 16:00</td>
              <td><span class="badge bg-info">Transfert</span></td>
              <td>Casque de chantier</td>
              <td>20</td>
              <td>WH-ABJ-01 → WH-YAK-02</td>
              <td>TRF-2025-003</td>
              <td>Admin</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
  $(function(){ $('#mouvementsTable').DataTable({ responsive:true, order: [[0, 'desc']], language:{ url:'//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endpush
@endsection
