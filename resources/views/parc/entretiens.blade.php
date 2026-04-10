@extends('layouts.app')

@section('title', 'Parc Auto - Entretiens')

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
          <label class="form-label small">Type d'entretien</label>
          <select class="form-select form-select-sm">
            <option value="">Tous</option>
            <option>Vidange</option>
            <option>Révision</option>
            <option>Freinage</option>
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
        <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
          <button type="button" class="btn btn-sm btn-secondary">Filtrer</button>
          <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEntretien">
            <i class="fas fa-plus me-1"></i>Nouvel entretien
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-outline-success" href="{{ route('parc.entretiens.export') }}"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="entretiensTable">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Véhicule</th>
              <th>Type</th>
              <th>Kilométrage</th>
              <th>Atelier</th>
              <th>Montant</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            {{-- Laisser vide pour éviter tout décalage colonnes/Cellules; DataTables affiche emptyTable --}}
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Entretien -->
  <div class="modal fade" id="modalEntretien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-screwdriver-wrench me-2"></i>Ajouter un entretien</h5>
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
                  <option>Vidange</option>
                  <option>Révision</option>
                  <option>Freinage</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Date</label>
                <input type="date" class="form-control">
              </div>
            </div>
            <div class="row g-2 mt-1">
              <div class="col-md-6">
                <label class="form-label">Kilométrage</label>
                <input type="number" class="form-control" min="0" step="1">
              </div>
              <div class="col-md-6">
                <label class="form-label">Montant</label>
                <input type="number" class="form-control" min="0" step="0.01">
              </div>
            </div>
            <div class="mt-2">
              <label class="form-label">Atelier</label>
              <input type="text" class="form-control" placeholder="Nom atelier">
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
  $(document).ready(function(){
    // Vérifier si DataTable existe déjà et la détruire
    if ($.fn.DataTable.isDataTable('#entretiensTable')) {
      $('#entretiensTable').DataTable().destroy();
    }

    // Initialiser DataTable avec configuration appropriée
    $('#entretiensTable').DataTable({
      responsive: true,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json',
        emptyTable: 'Aucun entretien à afficher',
        zeroRecords: 'Aucun résultat correspondant'
      },
      order: [[0, 'desc']], // Trier par date décroissante
      pageLength: 10,
      columnDefs: [
        { orderable: false, targets: 6 }
      ]
    });
  });
</script>
@endpush
@endsection
