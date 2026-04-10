@extends('layouts.app')

@section('title', 'Parc Auto - Réparations')

@section('content')
<div class="container-fluid">
  <div class="card shadow mb-3">
    <div class="card-body">
      <form class="row g-2">
        <div class="col-md-3">
          <label class="form-label small">Véhicule</label>
          <input type="text" class="form-control form-control-sm" placeholder="Immatriculation ou libellé">
        </div>
        <div class="col-md-3">
          <label class="form-label small">Atelier</label>
          <input type="text" class="form-control form-control-sm" placeholder="Atelier / Prestataire">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Du</label>
          <input type="date" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Au</label>
          <input type="date" class="form-control form-control-sm">
        </div>
        <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
          <button type="button" class="btn btn-sm btn-secondary">Filtrer</button>
          <a href="{{ route('parc.reparations.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Nouvelle réparation
          </a>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-outline-success" href="{{ route('parc.reparations.export') }}"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="reparationsTable">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Véhicule</th>
              <th>Type</th>
              <th>Atelier</th>
              <th>Montant</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>2025-08-10</td>
              <td>AB-123-CD</td>
              <td>Mécanique</td>
              <td>Garage Y</td>
              <td>120 000 FCFA</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></button>
                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Réparation -->
  <div class="modal fade" id="modalReparation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-wrench me-2"></i>Ajouter une réparation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-2">
              <label class="form-label">Véhicule</label>
              <input type="text" class="form-control" placeholder="Immatriculation">
            </div>
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Type</label>
                <select class="form-select">
                  <option>Mécanique</option>
                  <option>Carrosserie</option>
                  <option>Électricité</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Date</label>
                <input type="date" class="form-control">
              </div>
            </div>
            <div class="row g-2 mt-1">
              <div class="col-md-6">
                <label class="form-label">Atelier</label>
                <input type="text" class="form-control" placeholder="Prestataire">
              </div>
              <div class="col-md-6">
                <label class="form-label">Montant</label>
                <input type="number" min="0" step="0.01" class="form-control">
              </div>
            </div>
            <div class="mt-2">
              <label class="form-label">Description</label>
              <textarea class="form-control" rows="3" placeholder="Détails de l'intervention"></textarea>
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
  $(function(){ $('#reparationsTable').DataTable({ responsive: true, language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' } }); });
</script>
@endpush
@endsection
