@extends('layouts.app')

@section('title', 'Modifier incident')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-triangle-exclamation text-warning me-2"></i>
            Modifier incident #{{ $incident->id }}
        </h1>
        <a href="{{ route('parc.incidents.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>

    <!-- Formulaire -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-edit me-2"></i>Informations de l'incident
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('parc.incidents.update', $incident) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Véhicule -->
                    <div class="col-md-6">
                        <label for="vehicle_id" class="form-label">Véhicule <span class="text-danger">*</span></label>
                        <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">Sélectionnez un véhicule</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $incident->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                            </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="col-md-6">
                        <label for="type" class="form-label">Type d'incident <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Sélectionnez un type</option>
                            @foreach($types as $type)
                            <option value="{{ $type }}" {{ old('type', $incident->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date de l'incident -->
                    <div class="col-md-6">
                        <label for="date_incident" class="form-label">Date de l'incident <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="date_incident" 
                               id="date_incident" 
                               class="form-control @error('date_incident') is-invalid @enderror" 
                               value="{{ old('date_incident', $incident->date_incident->format('Y-m-d')) }}" 
                               required>
                        @error('date_incident')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sévérité -->
                    <div class="col-md-6">
                        <label for="severity" class="form-label">Sévérité <span class="text-danger">*</span></label>
                        <select name="severity" id="severity" class="form-select @error('severity') is-invalid @enderror" required>
                            <option value="">Sélectionnez la sévérité</option>
                            @foreach($severities as $severity)
                            <option value="{{ $severity }}" {{ old('severity', $incident->severity) == $severity ? 'selected' : '' }}>{{ $severity }}</option>
                            @endforeach
                        </select>
                        @error('severity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="col-md-6">
                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="open" {{ old('status', $incident->status) == 'open' ? 'selected' : '' }}>Ouvert</option>
                            <option value="in_progress" {{ old('status', $incident->status) == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="closed" {{ old('status', $incident->status) == 'closed' ? 'selected' : '' }}>Fermé</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Coût estimé -->
                    <div class="col-md-6">
                        <label for="estimated_cost" class="form-label">Coût estimé (FCFA)</label>
                        <input type="number" 
                               name="estimated_cost" 
                               id="estimated_cost" 
                               class="form-control @error('estimated_cost') is-invalid @enderror" 
                               value="{{ old('estimated_cost', $incident->estimated_cost) }}" 
                               min="0" 
                               step="0.01">
                        @error('estimated_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Coût final -->
                    <div class="col-md-6">
                        <label for="final_cost" class="form-label">Coût final (FCFA)</label>
                        <input type="number" 
                               name="final_cost" 
                               id="final_cost" 
                               class="form-control @error('final_cost') is-invalid @enderror" 
                               value="{{ old('final_cost', $incident->final_cost) }}" 
                               min="0" 
                               step="0.01">
                        @error('final_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pièce jointe actuelle -->
                    @if($incident->attachment)
                    <div class="col-md-6">
                        <label class="form-label">Pièce jointe actuelle</label>
                        <div>
                            <a href="{{ asset('storage/' . $incident->attachment) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-paperclip me-1"></i> Voir la pièce jointe
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Nouvelle pièce jointe -->
                    <div class="col-md-6">
                        <label for="attachment" class="form-label">{{ $incident->attachment ? 'Remplacer la pièce jointe' : 'Pièce jointe' }}</label>
                        <input type="file" 
                               name="attachment" 
                               id="attachment" 
                               class="form-control @error('attachment') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Formats acceptés: PDF, JPG, PNG (max 5 Mo)</small>
                        @error('attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="5" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  required>{{ old('description', $incident->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Boutons -->
                <div class="mt-4 d-flex justify-content-between">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i> Rapporté par: {{ $incident->reporter->name ?? 'N/A' }}<br>
                            <i class="fas fa-clock me-1"></i> Créé le: {{ $incident->created_at->format('d/m/Y à H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('parc.incidents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
