@extends('layouts.app')

@section('title', 'Nouvelle Réparation - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-wrench me-2"></i>Ajouter une Réparation
                    </h6>
                    <a href="{{ route('parc.reparations') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('parc.reparations.store') }}">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Informations générales</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="vehicule_id" class="form-label required">Véhicule</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-car"></i></span>
                                    <select name="vehicule_id" id="vehicule_id" 
                                            class="form-select @error('vehicule_id') is-invalid @enderror" required>
                                        <option value="">-- Sélectionner un véhicule --</option>
                                        @foreach($vehicules as $vehicule)
                                            <option value="{{ $vehicule->id }}" {{ old('vehicule_id') == $vehicule->id ? 'selected' : '' }}>
                                                {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('vehicule_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label required">Date de la panne</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                    <input type="date" name="date" id="date" 
                                           class="form-control @error('date') is-invalid @enderror" 
                                           value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>
                                @error('date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Détails de la panne</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type_panne" class="form-label required">Type de panne</label>
                                <select name="type_panne" id="type_panne" 
                                        class="form-select @error('type_panne') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Mécanique" {{ old('type_panne') == 'Mécanique' ? 'selected' : '' }}>Mécanique</option>
                                    <option value="Électrique" {{ old('type_panne') == 'Électrique' ? 'selected' : '' }}>Électrique</option>
                                    <option value="Carrosserie" {{ old('type_panne') == 'Carrosserie' ? 'selected' : '' }}>Carrosserie</option>
                                    <option value="Pneumatique" {{ old('type_panne') == 'Pneumatique' ? 'selected' : '' }}>Pneumatique</option>
                                    <option value="Autre" {{ old('type_panne') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('type_panne')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="urgence" class="form-label required">Niveau d'urgence</label>
                                <select name="urgence" id="urgence" 
                                        class="form-select @error('urgence') is-invalid @enderror" required>
                                    <option value="Normale" {{ old('urgence', 'Normale') == 'Normale' ? 'selected' : '' }}>Normale</option>
                                    <option value="Urgente" {{ old('urgence') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                                    <option value="Critique" {{ old('urgence') == 'Critique' ? 'selected' : '' }}>Critique</option>
                                </select>
                                @error('urgence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">
                                    <span class="badge bg-secondary">Normale</span> = Peut attendre | 
                                    <span class="badge bg-warning">Urgente</span> = Prioritaire | 
                                    <span class="badge bg-danger">Critique</span> = Immédiat
                                </small>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label required">Description de la panne</label>
                                <textarea name="description" id="description" rows="4" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          placeholder="Décrivez en détail la panne constatée..." required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Informations de réparation</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="garage" class="form-label">Garage / Prestataire</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tools"></i></span>
                                    <input type="text" name="garage" id="garage" 
                                           class="form-control @error('garage') is-invalid @enderror" 
                                           value="{{ old('garage') }}" 
                                           placeholder="Nom du garage ou prestataire">
                                </div>
                                @error('garage')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cout_estime" class="form-label">Coût estimé (FCFA)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-coins"></i></span>
                                    <input type="number" name="cout_estime" id="cout_estime" 
                                           class="form-control @error('cout_estime') is-invalid @enderror" 
                                           value="{{ old('cout_estime') }}" 
                                           min="0" step="0.01" placeholder="0">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('cout_estime')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes complémentaires</label>
                                <textarea name="notes" id="notes" rows="2" 
                                          class="form-control @error('notes') is-invalid @enderror" 
                                          placeholder="Informations supplémentaires...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information:</strong> La réparation sera créée avec le statut "En attente". Vous pourrez la mettre à jour ultérieurement.
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <a href="{{ route('parc.reparations') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer la réparation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
