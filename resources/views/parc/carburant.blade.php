@extends('layouts.app')

@section('title', 'Parc Auto - Carburant')

@section('content')
<div class="container-fluid">
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  <div class="card shadow mb-3">
    <div class="card-body">
      <form class="row g-2" method="GET" action="{{ route('parc.carburant') }}">
        <div class="col-md-3">
          <label class="form-label small">Véhicule</label>
          <input type="text" name="vehicule" value="{{ request('vehicule') }}" class="form-control form-control-sm" placeholder="Ex: AB-123-CD ou ID véhicule">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Type</label>
          <select name="type" class="form-select form-select-sm">
            <option value="" {{ request('type') === null || request('type') === '' ? 'selected' : '' }}>Tous</option>
            <option value="Gasoil" {{ request('type') === 'Gasoil' ? 'selected' : '' }}>Gasoil</option>
            <option value="Essence" {{ request('type') === 'Essence' ? 'selected' : '' }}>Essence</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Du</label>
          <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Au</label>
          <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-3 d-flex align-items-end justify-content-end gap-2">
          <button type="submit" class="btn btn-sm btn-secondary">Filtrer</button>
          <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCarburant">
            <i class="fas fa-plus me-1"></i>Nouvel appoint
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-outline-success" href="{{ route('parc.carburant.export', request()->only(['vehicule','type','from','to'])) }}"><i class="fas fa-file-csv me-1"></i>Exporter CSV</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="carburantTable">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Véhicule</th>
              <th>Type</th>
              <th>Litres</th>
              <th>Prix/Litre</th>
              <th>Montant</th>
              <th>Station</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($appoints ?? []) as $a)
              <tr>
                <td>{{ optional($a->date_depense)->format('Y-m-d H:i') }}</td>
                <td>{{ optional($a->vehicle)->immatriculation ?? ('#'.$a->vehicule_id) }}</td>
                <td>{{ $a->type ?? 'Gasoil' }}</td>
                <td>{{ number_format($a->quantite ?? 0, 2, ',', ' ') }}</td>
                <td>{{ number_format($a->prix_unitaire ?? 0, 0, ',', ' ') }} FCFA</td>
                <td class="fw-semibold">{{ number_format($a->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                <td>{{ $a->station ?? $a->fournisseur ?? '-' }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                  <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></button>
                  <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted">Aucun appoint enregistré pour le moment.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        @if(isset($appoints) && $appoints->hasPages())
          <div class="d-flex justify-content-center mt-3">
            {{ $appoints->appends(request()->query())->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Modal Carburant -->
  <div class="modal fade" id="modalCarburant" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-gas-pump me-2"></i>Nouvel appoint carburant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('parc.carburant.store') }}" id="carburantForm">
            @csrf
            <div class="mb-2">
              <label class="form-label">Véhicule *</label>
              @if(!empty($vehicules) && count($vehicules))
                <select name="vehicule_id" class="form-select" required>
                  <option value="">— Sélectionner un véhicule —</option>
                  @foreach($vehicules as $v)
                    <option value="{{ $v->id }}">{{ $v->immatriculation ?? ('Veh #'.$v->id) }} @if(!empty($v->modele)) · {{ $v->modele }} @endif</option>
                  @endforeach
                </select>
              @else
                <input type="number" name="vehicule_id" class="form-control" placeholder="ID véhicule" required>
                <small class="text-muted">Liste des véhicules non disponible, saisir l'ID.</small>
              @endif
            </div>
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select" required>
                  <option value="Gasoil">Gasoil</option>
                  <option value="Essence">Essence</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Date *</label>
                <input type="datetime-local" name="date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
              </div>
            </div>
            <div class="row g-2 mt-1">
              <div class="col-md-4">
                <label class="form-label">Litres *</label>
                <input type="number" name="litres" min="0" step="0.01" class="form-control" required oninput="calcMontant()">
              </div>
              <div class="col-md-4">
                <label class="form-label">Prix/Litre *</label>
                <input type="number" name="prix_unitaire" min="0" step="0.01" class="form-control" required oninput="calcMontant()">
              </div>
              <div class="col-md-4">
                <label class="form-label">Station *</label>
                <input type="text" name="station" class="form-control" placeholder="Fournisseur" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" form="carburantForm" class="btn btn-primary">Enregistrer</button>
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

  @push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script>
    $(function(){
      $('#carburantTable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json' }
      });
    })

    function calcMontant(){
      const litres = parseFloat(document.querySelector('[name="litres"]').value)||0;
      const pu = parseFloat(document.querySelector('[name="prix_unitaire"]').value)||0;
      const montant = Math.round(litres * pu);
      // Vous pouvez afficher ce montant si un champ est prévu
    }
  </script>
  @endpush
@endsection
