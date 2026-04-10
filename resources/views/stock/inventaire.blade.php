@extends('layouts.app')

@section('title', 'Stock - Inventaire')

@section('content')
<div class="container-fluid">
  <div class="card shadow mb-3">
    <div class="card-body">
      <form class="row g-2">
        <div class="col-md-3">
          <label class="form-label small">Référence</label>
          <input type="text" class="form-control form-control-sm" placeholder="INV-XXXX">
        </div>
        <div class="col-md-3">
          <label class="form-label small">Entrepôt</label>
          <select class="form-select form-select-sm">
            <option value="">Tous</option>
            <option>WH-ABJ-01</option>
            <option>WH-YAK-02</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Statut</label>
          <select class="form-select form-select-sm">
            <option value="">Tous</option>
            <option>En cours</option>
            <option>Terminé</option>
            <option>Validé</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Du</label>
          <input type="date" class="form-control form-control-sm">
        </div>
        <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
          <button type="button" class="btn btn-sm btn-secondary">Filtrer</button>
          <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalInventaire">
            <i class="fas fa-plus me-1"></i>Nouveau
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-outline-success" href="{{ route('stock.inventaire') }}/export"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="inventaireTable">
          <thead class="table-light">
            <tr>
              <th>Référence</th>
              <th>Date</th>
              <th>Entrepôt</th>
              <th>Responsable</th>
              <th>Nb produits</th>
              <th>Écarts</th>
              <th>Statut</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>INV-2025-Q3</td>
              <td>2025-09-30</td>
              <td>WH-ABJ-01</td>
              <td>Admin</td>
              <td>125</td>
              <td>3</td>
              <td><span class="badge bg-success">Validé</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></button>
              </td>
            </tr>
            <tr>
              <td>INV-2025-Q4</td>
              <td>2025-10-15</td>
              <td>WH-YAK-02</td>
              <td>Dupont</td>
              <td>87</td>
              <td>-</td>
              <td><span class="badge bg-warning">En cours</span></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Inventaire -->
  <div class="modal fade" id="modalInventaire" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-clipboard-list me-2"></i>Nouvel inventaire</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-2">
              <label class="form-label">Entrepôt</label>
              <select class="form-select">
                <option>WH-ABJ-01</option>
                <option>WH-YAK-02</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Date prévue</label>
              <input type="date" class="form-control">
            </div>
            <div class="mb-2">
              <label class="form-label">Responsable</label>
              <input type="text" class="form-control" placeholder="Nom">
            </div>
            <div class="mb-2">
              <label class="form-label">Commentaire</label>
              <textarea class="form-control" rows="2" placeholder="Notes..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-primary">Enregistrer</button>
        </div>
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
  $(function(){ $('#inventaireTable').DataTable({ responsive:true, order: [[1, 'desc']], language:{ url:'//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endpush
@endsection
