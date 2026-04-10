@extends('layouts.app')

@section('title', 'Parc Auto - Nouvelle affectation')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-route me-2"></i>Nouvelle affectation</h5>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('parc.affectations.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
          <label class="form-label small">Véhicule</label>
          <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
            <option value="">Sélectionnez un véhicule</option>
            @foreach(($vehicles ?? []) as $v)
              <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                {{ $v->immatriculation }} — {{ $v->marque }} {{ $v->modele }}
              </option>
            @endforeach
            @if(empty($vehicles ?? []))
              <option value="" disabled>Aucun véhicule trouvé</option>
            @endif
          </select>
          @error('vehicle_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Conducteur</label>
          <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror" required>
            <option value="">Sélectionnez un conducteur</option>
            @foreach(($drivers ?? []) as $d)
              <option value="{{ $d->id }}" {{ old('driver_id') == $d->id ? 'selected' : '' }}>
                @if(isset($d->nom) && isset($d->prenoms))
                    {{ $d->nom }} {{ $d->prenoms }}
                @else
                    {{ $d->name }}
                @endif
              </option>
            @endforeach
            @if(empty($drivers ?? []))
              <option value="" disabled>Aucun conducteur trouvé</option>
            @endif
          </select>
          @error('driver_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Requête associée (optionnel)</label>
          <select name="requete_id" class="form-select @error('requete_id') is-invalid @enderror">
            <option value="">Sélectionnez une requête (optionnel)</option>
            @foreach(\App\Models\Requete::orderBy('reference', 'desc')->get() as $requete)
              <option value="{{ $requete->id }}" {{ old('requete_id') == $requete->id ? 'selected' : '' }}>
                {{ $requete->reference }} - {{ $requete->objet }}
              </option>
            @endforeach
          </select>
          @error('requete_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Mission</label>
          <input type="text" name="mission" value="{{ old('mission') }}" class="form-control @error('mission') is-invalid @enderror" placeholder="Objet de la mission">
          @error('mission')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Destination</label>
          <input type="text" name="destination" value="{{ old('destination') }}" class="form-control @error('destination') is-invalid @enderror" placeholder="Destination">
          @error('destination')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Début</label>
          <input type="datetime-local" name="assigned_at" value="{{ old('assigned_at') }}" class="form-control @error('assigned_at') is-invalid @enderror" required>
          @error('assigned_at')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Fin</label>
          <input type="datetime-local" name="returned_at" value="{{ old('returned_at') }}" class="form-control @error('returned_at') is-invalid @enderror">
          @error('returned_at')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Statut</label>
          <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="">Sélectionnez un statut</option>
            @foreach(($statuses ?? []) as $s)
              <option value="{{ $s }}" {{ old('status') === $s ? 'selected' : '' }}>
                @php
                  $labels = [
                    'assigned' => 'Assigné',
                    'in_progress' => 'En cours',
                    'completed' => 'Terminée',
                    'cancelled' => 'Annulée',
                  ];
                @endphp
                {{ $labels[$s] ?? ucfirst($s) }}
              </option>
            @endforeach
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-12">
          <label class="form-label small">Notes</label>
          <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Notes">{{ old('notes') }}</textarea>
          @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('parc.affectations.index') }}" class="btn btn-light">Annuler</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
