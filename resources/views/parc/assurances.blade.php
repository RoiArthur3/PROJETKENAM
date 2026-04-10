@extends('layouts.app')

@section('title', 'Parc Auto - Assurances')

@section('content')
<div class="container-fluid">
  <div class="card shadow mb-3">
    <div class="card-body">
      <form class="row g-2" method="GET" action="{{ route('parc.assurances.index') }}">
        <div class="col-md-3">
          <label class="form-label small">Véhicule</label>
          <input type="text" name="vehicule" value="{{ request('vehicule') }}" class="form-control form-control-sm" placeholder="Immatriculation ou libellé">
        </div>
        <div class="col-md-3">
          <label class="form-label small">Assureur</label>
          <input type="text" name="assureur" value="{{ request('assureur') }}" class="form-control form-control-sm" placeholder="Compagnie d'assurance">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Du</label>
          <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Au</label>
          <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
          <button type="submit" class="btn btn-sm btn-secondary">Filtrer</button>
          <a href="{{ route('parc.assurances.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Nouvelle police
          </a>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-2">
        {{-- Export à implémenter si nécessaire sur Assurance --}}
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Véhicule</th>
              <th>Police</th>
              <th>Assureur</th>
              <th>Début</th>
              <th>Échéance</th>
              <th>Statut</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($assurances as $a)
              <tr>
                <td>{{ optional($a->vehicle)->immatriculation ?? '—' }}</td>
                <td>{{ $a->numero_police }}</td>
                <td>{{ $a->assureur }}</td>
                <td data-order="{{ optional($a->date_debut)->format('Y-m-d') }}">{{ optional($a->date_debut)->format('d/m/Y') }}</td>
                <td data-order="{{ optional($a->date_fin)->format('Y-m-d') }}">{{ optional($a->date_fin)->format('d/m/Y') }}</td>
                <td data-order="{{ $a->statut }}">
                  <span class="badge bg-{{ $a->statut === 'active' ? 'success' : ($a->statut === 'a_renouveler' ? 'warning' : 'danger') }}">
                    {{ $a->statut_label }}
                  </span>
                </td>
                <td class="text-end">
                  <form action="{{ route('parc.assurances.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette police ?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">Aucune police d'assurance enregistrée pour le moment.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>


</div>
@endsection
