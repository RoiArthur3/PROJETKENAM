@extends('layouts.app')

@section('title', 'Parc Auto - Affectations')

@section('content')
<div class="container-fluid">
  <div class="card mb-3">
    <div class="card-body">
      <form class="row g-2" method="get">
        <div class="col-12 col-md-3">
          <label class="form-label small">Statut</label>
          <select name="status" class="form-select">
            <option value="">Tous</option>
            @php
              $statusLabels = [
                  'assigned'    => "Assignée",
                  'in_progress' => "En cours",
                  'completed'   => "Terminée",
                  'cancelled'   => "Annulée",
              ];
            @endphp
            @foreach(($statuses ?? []) as $s)
              <option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabels[$s] ?? ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small">Du</label>
          <input type="date" name="from" value="{{ request('from') }}" class="form-control" />
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small">Au</label>
          <input type="date" name="to" value="{{ request('to') }}" class="form-control" />
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end">
          <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filtrer</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Nouvelle Affectation -->
  <div class="modal fade" id="modalAffectation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-route me-2"></i>Nouvelle affectation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="affectationForm" method="POST" action="{{ route('parc.affectations.store') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small">Véhicule</label>
                <select name="vehicle_id" class="form-select" required>
                  <option value="">Sélectionnez un véhicule</option>
                  @foreach(($vehicles ?? []) as $v)
                    <option value="{{ $v->id }}">{{ $v->immatriculation }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Conducteur</label>
                <select name="driver_id" class="form-select" required>
                  <option value="">Sélectionnez un conducteur</option>
                  @foreach(($drivers ?? []) as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Mission</label>
                <input type="text" name="mission" class="form-control" placeholder="Objet de la mission">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Destination</label>
                <input type="text" name="destination" class="form-control" placeholder="Destination">
              </div>
              <div class="col-md-6">
                <label class="form-label small">Début</label>
                <input type="datetime-local" name="assigned_at" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small">Fin</label>
                <input type="datetime-local" name="returned_at" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label small">Statut</label>
                <select name="status" class="form-select" required>
                  <option value="assigned">Assigné</option>
                  <option value="in_progress">En cours</option>
                  <option value="completed">Terminée</option>
                  <option value="cancelled">Annulée</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label small">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Notes"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          <button type="submit" form="affectationForm" class="btn btn-primary">Enregistrer</button>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2 gap-2">
        <a href="{{ route('parc.affectations.create') }}" class="btn btn-sm btn-primary">
          <i class="fas fa-plus me-1"></i>Nouvelle affectation
        </a>
        <a class="btn btn-sm btn-outline-success" href="{{ route('parc.affectations.export') }}"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
      <table class="table table-sm align-middle mb-0" id="affectationsTable">
        <thead class="table-light">
          <tr>
            <th>Véhicule</th>
            <th>Conducteur</th>
            <th>Requête</th>
            <th>Mission</th>
            <th>Destination</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse(($assignments ?? []) as $a)
          <tr>
            <td>{{ optional($a->vehicle)->immatriculation ?? '#' }}</td>
            <td>{{ optional($a->driver)->name ?? '-' }}</td>
            <td>
              @if($a->requete)
                <a href="{{ route('requetes.show', $a->requete) }}" class="text-primary" title="Voir la requête">
                  {{ $a->requete->reference }}
                </a>
              @else
                -
              @endif
            </td>
            <td>{{ $a->mission ?? '-' }}</td>
            <td>{{ $a->destination ?? '-' }}</td>
            <td>{{ optional($a->assigned_at)->format('Y-m-d H:i') }}</td>
            <td>{{ optional($a->returned_at)->format('Y-m-d H:i') }}</td>
            <td><span class="badge bg-secondary">{{ $a->status_label }}</span></td>
            <td class="text-end">
              <div class="btn-group btn-group-sm">
                @if($a->status !== 'completed')
                  <form action="{{ route('parc.affectations.update-status', $a) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-outline-success" title="Marquer comme terminée">
                      <i class="fas fa-check"></i>
                    </button>
                  </form>
                @endif
                @if($a->status !== 'cancelled')
                  <form action="{{ route('parc.affectations.update-status', $a) }}" method="POST" class="d-inline ms-1">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-outline-warning" title="Annuler l'affectation">
                      <i class="fas fa-ban"></i>
                    </button>
                  </form>
                @endif
                <form action="{{ route('parc.affectations.destroy', $a) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Supprimer cette affectation ?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          {{-- Laisser le tbody vide pour que DataTables affiche son message emptyTable --}}
          @endforelse
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
  $(document).ready(function() {
    setTimeout(function() {
      if ($.fn.DataTable.isDataTable('#affectationsTable')) {
        $('#affectationsTable').DataTable().destroy();
      }
      $('#affectationsTable').DataTable({
        responsive: true,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json',
          emptyTable: 'Aucune affectation à afficher',
          zeroRecords: 'Aucun résultat correspondant'
        },
        columnDefs: [
          { targets: '_all', defaultContent: '-' }
        ]
      });
    }, 100);
  });
</script>
@endpush
@endsection
