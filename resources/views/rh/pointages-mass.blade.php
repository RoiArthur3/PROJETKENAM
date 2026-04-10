@extends('layouts.app')

@section('title', 'RH - Pointage en masse | KENAM SERVICES')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-clipboard-check me-2 text-primary"></i>Pointage en masse
      </h1>
      <p class="text-muted mb-0">Sélectionnez les agents et appliquez une action d'entrée ou de sortie</p>
    </div>
    <a href="{{ route('rh.pointages.index') }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
  </div>

  <form method="POST" action="{{ route('rh.pointages.mass.store') }}" id="massForm">
    @csrf

    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label small">Action</label>
            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
              <option value="depart" {{ old('type')==='depart' ? 'selected' : '' }}>Entrée</option>
              <option value="arrivee" {{ old('type')==='arrivee' ? 'selected' : '' }}>Sortie</option>
            </select>
            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label small">Date</label>
            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', now()->toDateString()) }}" required>
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label small">Heure</label>
            <input type="time" name="heure" id="heure" class="form-control @error('heure') is-invalid @enderror" value="{{ old('heure', '08:00') }}" required>
            @error('heure')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label small">Heures (info)</label>
            <input type="number" name="heures" id="heures" class="form-control" value="{{ old('heures', 8) }}" min="0" step="0.25">
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row g-2 mb-2">
          <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un agent (nom ou email)">
          </div>
          <div class="col-md-6 text-end">
            <div class="form-check d-inline-block me-2">
              <input class="form-check-input" type="checkbox" id="selectAll">
              <label class="form-check-label" for="selectAll">Tout sélectionner</label>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-check me-1"></i>Valider les pointages
            </button>
          </div>
        </div>

        @error('selected_users')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

        <div class="table-responsive">
          <table class="table table-sm align-middle" id="agentsTable">
            <thead class="table-light">
              <tr>
                <th style="width:40px;"></th>
                <th>Nom</th>
                <th>Email</th>
              </tr>
            </thead>
            <tbody>
              @forelse(($agents ?? []) as $a)
                <tr>
                  <td>
                    <input type="checkbox" class="form-check-input row-check" name="selected_users[]" value="{{ $a->id }}">
                  </td>
                  <td class="fw-semibold">{{ $a->name }}</td>
                  <td class="text-muted">{{ $a->email }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">
                    Aucun agent à afficher
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </form>

  <div class="card shadow-sm mt-3">
    <div class="card-header bg-white">
      <h6 class="mb-0"><i class="fas fa-list me-2"></i>Pointages du jour</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>Agent</th>
              <th>Action</th>
              <th>Date</th>
              <th>Heure</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($todayPointages ?? []) as $p)
              <tr>
                <td>{{ optional($p->user)->name ?? '—' }}</td>
                <td>
                  @php $label = $p->type === 'depart' ? 'Entrée' : ($p->type === 'arrivee' ? 'Sortie' : ucfirst($p->type)); @endphp
                  <span class="badge bg-{{ $p->type === 'depart' ? 'success' : 'secondary' }}">{{ $label }}</span>
                </td>
                <td>{{ optional($p->date_pointage)->format('d/m/Y') ?? ( $p->date_pointage ? \Carbon\Carbon::parse($p->date_pointage)->format('d/m/Y') : '—') }}</td>
                <td>{{ optional($p->heure_pointage)->format('H:i') ?? ( $p->heure_pointage ? \Carbon\Carbon::parse($p->heure_pointage)->format('H:i') : '—') }}</td>
                <td class="text-muted small">{{ $p->notes ?? '' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">Aucun pointage aujourd'hui</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const type = document.getElementById('type');
    const heure = document.getElementById('heure');
    const searchInput = document.getElementById('searchInput');
    const selectAll = document.getElementById('selectAll');
    const checks = document.querySelectorAll('.row-check');

    // Valeurs par défaut CI: Entrée 08:00, Sortie 17:00
    function applyDefaultHour(){
      if(type.value === 'depart' && !heure.value){ heure.value = '08:00'; }
      if(type.value === 'arrivee' && !heure.value){ heure.value = '17:00'; }
    }
    // Ajuste par changement d'action
    type.addEventListener('change', function(){
      heure.value = (type.value === 'depart') ? '08:00' : '17:00';
    });
    applyDefaultHour();

    // Recherche simple
    searchInput.addEventListener('input', function(){
      const q = this.value.toLowerCase();
      document.querySelectorAll('#agentsTable tbody tr').forEach(tr => {
        const txt = tr.innerText.toLowerCase();
        tr.style.display = txt.includes(q) ? '' : 'none';
      });
    });

    // Sélection globale
    selectAll.addEventListener('change', function(){
      checks.forEach(c => { c.checked = selectAll.checked; });
    });
  });
</script>
@endpush
