@extends('layouts.app')
@section('title', 'Parc Auto - Historiques')
@section('content')
<div class="container-fluid">
  <div class="card shadow mb-3">
    <div class="card-body">
      <form class="row g-2">
        <div class="col-md-3">
          <label class="form-label small">Véhicule</label>
          <input type="text" class="form-control form-control-sm" placeholder="Immatriculation">
        </div>
        <div class="col-md-3">
          <label class="form-label small">Type d'opération</label>
          <select class="form-select form-select-sm">
            <option value="">Tous</option>
            <option>Carburant</option>
            <option>Entretien</option>
            <option>Réparation</option>
            <option>Assurance</option>
            <option>Pneumatique</option>
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
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-outline-success" href="{{ route('parc.historiques.export') }}"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Véhicule</th>
              <th>Type</th>
              <th>Description</th>
              <th>Montant</th>
              <th>Responsable</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($historiques ?? []) as $h)
              <tr>
                <td>{{ $h->date ?? '—' }}</td>
                <td>{{ $h->vehicule ?? '—' }}</td>
                <td>{{ $h->type ?? '—' }}</td>
                <td>{{ $h->description ?? '—' }}</td>
                <td>{{ isset($h->montant) ? number_format($h->montant, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td>{{ $h->responsable ?? '—' }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">Aucun historique disponible pour le moment.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
