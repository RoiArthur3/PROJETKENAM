@extends('layouts.app')

@section('title', 'Stock - Alertes')

@section('content')
<div class="container-fluid">
  <div class="card shadow mb-3">
    <div class="card-body">
      <form class="row g-2">
        <div class="col-md-4">
          <label class="form-label small">Matériel</label>
          <input type="text" class="form-control form-control-sm" placeholder="Nom ou référence">
        </div>
        <div class="col-md-3">
          <label class="form-label small">Entrepôt</label>
          <select class="form-select form-select-sm">
            <option value="">Tous</option>
            <option>WH-ABJ-01</option>
            <option>WH-YAK-02</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small">Type d'alerte</label>
          <select class="form-select form-select-sm">
            <option value="">Toutes</option>
            <option>Stock faible</option>
            <option>Rupture</option>
            <option>Stock excessif</option>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="button" class="btn btn-sm btn-secondary w-100">Filtrer</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-outline-success" href="{{ route('stock.alertes') }}/export"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="alertesTable">
          <thead class="table-light">
            <tr>
              <th>Matériel</th>
              <th>Entrepôt</th>
              <th>Stock actuel</th>
              <th>Stock min</th>
              <th>Stock max</th>
              <th>Type</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Gants de protection</td>
              <td>WH-ABJ-01</td>
              <td>15</td>
              <td>50</td>
              <td>200</td>
              <td><span class="badge bg-warning">Stock faible</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" title="Voir détails"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-outline-warning ms-1" title="Commander"><i class="fas fa-shopping-cart"></i></button>
              </td>
            </tr>
            <tr>
              <td>Casque de chantier</td>
              <td>WH-YAK-02</td>
              <td>0</td>
              <td>20</td>
              <td>100</td>
              <td><span class="badge bg-danger">Rupture</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" title="Voir détails"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-outline-warning ms-1" title="Commander"><i class="fas fa-shopping-cart"></i></button>
              </td>
            </tr>
            <tr>
              <td>Vis à bois 4x40</td>
              <td>WH-ABJ-01</td>
              <td>850</td>
              <td>100</td>
              <td>500</td>
              <td><span class="badge bg-info">Stock excessif</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" title="Voir détails"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-outline-warning ms-1" title="Commander"><i class="fas fa-shopping-cart"></i></button>
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
  $(function(){ $('#alertesTable').DataTable({ responsive:true, language:{ url:'//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endpush
@endsection
