@extends('layouts.app')

@section('title', 'Stock - Magasin')

@section('content')
<div class="container-fluid">
  <div class="card shadow mb-3">
    <div class="card-body">
      <form class="row g-2">
        <div class="col-md-4">
          <label class="form-label small">Produit</label>
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
          <label class="form-label small">Catégorie</label>
          <select class="form-select form-select-sm">
            <option value="">Toutes</option>
            <option>Équipement</option>
            <option>Matériel</option>
            <option>Consommables</option>
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
        <a class="btn btn-sm btn-outline-success" href="{{ route('stock.magasin') }}/export"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="magasinTable">
          <thead class="table-light">
            <tr>
              <th>Produit</th>
              <th>Référence</th>
              <th>Entrepôt</th>
              <th>Catégorie</th>
              <th>Stock</th>
              <th>Valeur</th>
              <th>Emplacement</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Casque de chantier</td>
              <td>PRD-001</td>
              <td>WH-ABJ-01</td>
              <td>Équipement</td>
              <td>150</td>
              <td>750 000 FCFA</td>
              <td>Allée A - Rack 3</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
              </td>
            </tr>
            <tr>
              <td>Gants de protection</td>
              <td>PRD-002</td>
              <td>WH-ABJ-01</td>
              <td>Consommables</td>
              <td>15</td>
              <td>22 500 FCFA</td>
              <td>Allée B - Rack 1</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
              </td>
            </tr>
            <tr>
              <td>Casque de chantier</td>
              <td>PRD-001</td>
              <td>WH-YAK-02</td>
              <td>Équipement</td>
              <td>75</td>
              <td>375 000 FCFA</td>
              <td>Zone C - Étagère 2</td>
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
  $(function(){ $('#magasinTable').DataTable({ responsive:true, language:{ url:'//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endpush
@endsection
