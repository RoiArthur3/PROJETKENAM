@extends('layouts.app')

@section('title', 'Modifier Visite Technique | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-warning py-3 text-dark">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i>Modifier la Visite Technique</h5>
                </div>
                
                <div class="card-body p-4 p-xl-5">
                    <form action="{{ route('materiel.visites.update', $visite) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Sélection du véhicule -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Sélectionner l'engin / véhicule *</label>
                                <select name="vehicle_id" class="form-select select2 @error('vehicle_id') is-invalid @enderror" required>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" {{ old('vehicle_id', $visite->vehicle_id) == $vehicule->id ? 'selected' : '' }}>
                                            {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Dates -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date de la visite *</label>
                                <input type="date" name="date_visite" class="form-control @error('date_visite') is-invalid @enderror" 
                                       value="{{ old('date_visite', $visite->date_visite->format('Y-m-d')) }}" required>
                                @error('date_visite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date d'expiration *</label>
                                <input type="date" name="date_expiration" class="form-control @error('date_expiration') is-invalid @enderror" 
                                       value="{{ old('date_expiration', $visite->date_expiration->format('Y-m-d')) }}" required>
                                @error('date_expiration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Centre et Certificat -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Centre de visite (Lieu)</label>
                                <input type="text" name="centre_visite" class="form-control @error('centre_visite') is-invalid @enderror" 
                                       placeholder="ex : SICTA Abidjan" value="{{ old('centre_visite', $visite->centre_visite) }}">
                                @error('centre_visite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Numéro de certificat</label>
                                <input type="text" name="numero_certificat" class="form-control @error('numero_certificat') is-invalid @enderror" 
                                       placeholder="N° du document" value="{{ old('numero_certificat', $visite->numero_certificat) }}">
                                @error('numero_certificat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Coût -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Coût de la visite (FCFA)</label>
                                <div class="input-group">
                                    <input type="number" name="cout" class="form-control @error('cout') is-invalid @enderror" 
                                           placeholder="Montant payé" value="{{ old('cout', $visite->cout) }}" min="0">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('cout')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Pièce jointe -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Changer Scan du certificat</label>
                                <input type="file" name="piece_jointe" class="form-control @error('piece_jointe') is-invalid @enderror">
                                @if($visite->piece_jointe)
                                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Document existe déjà</small>
                                @endif
                                @error('piece_jointe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Commentaires -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Observations / Commentaires</label>
                                <textarea name="commentaires" class="form-control @error('commentaires') is-invalid @enderror" 
                                          rows="3" placeholder="Notes particulières sur l'état du véhicule...">{{ old('commentaires', $visite->commentaires) }}</textarea>
                                @error('commentaires')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                            <a href="{{ route('materiel.visites.index') }}" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg px-5 shadow">
                                <i class="fas fa-check-circle me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });
</script>
@endpush
