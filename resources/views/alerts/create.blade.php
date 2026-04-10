<x-dashboard-layout title="Créer une Alerte" icon="fa-solid fa-bell">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-bell me-2"></i>
                        Créer une Alerte
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('alerts.store') }}" class="row g-3">
                        @csrf

                        <!-- Véhicule -->
                        <div class="col-md-6">
                            <label for="vehicle_id" class="form-label">Véhicule *</label>
                            <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id', 'is-invalid')" required>
                                <option value="">Sélectionner un véhicule</option>
                                @foreach($vehicules as $vehicule)
                                    <option value="{{ $vehicule->id }}" {{ old('vehicle_id') == $vehicule->id ? 'selected' : '' }}>
                                        {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Type d'alerte -->
                        <div class="col-md-6">
                            <label for="type_alerte" class="form-label">Type d'alerte *</label>
                            <select name="type_alerte" id="type_alerte" class="form-select @error('type_alerte', 'is-invalid')" required>
                                <option value="">Sélectionner un type</option>
                                <option value="assurance" {{ old('type_alerte') == 'assurance' ? 'selected' : '' }}>Assurance</option>
                                <option value="maintenance" {{ old('type_alerte') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="kilometrage" {{ old('type_alerte') == 'kilometrage' ? 'selected' : '' }}>Kilométrage</option>
                            </select>
                            @error('type_alerte')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="col-12">
                            <label for="message" class="form-label">Message de l'alerte *</label>
                            <textarea name="message" id="message" class="form-control @error('message', 'is-invalid')" rows="4" required placeholder="Décrivez le message de l'alerte...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Date et niveau -->
                        <div class="col-md-6">
                            <label for="date_alerte" class="form-label">Date de l'alerte *</label>
                            <input type="date" name="date_alerte" id="date_alerte" class="form-control @error('date_alerte', 'is-invalid')" required>
                            @error('date_alerte')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="niveau_alerte" class="form-label">Niveau de l'alerte *</label>
                            <select name="niveau_alerte" id="niveau_alerte" class="form-select @error('niveau_alerte', 'is-invalid')" required>
                                <option value="">Sélectionner un niveau</option>
                                <option value="info" {{ old('niveau_alerte') == 'info' ? 'selected' : '' }}>Information</option>
                                <option value="warning" {{ old('niveau_alerte') == 'warning' ? 'selected' : '' }}>Avertissement</option>
                                <option value="critical" {{ old('niveau_alerte') == 'critical' ? 'selected' : '' }}>Critique</option>
                            </select>
                            @error('niveau_alerte')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('alerts.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Annuler
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-warning me-2">
                                        <i class="fa-solid fa-undo me-2"></i>
                                        Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save me-2"></i>
                                        Créer l'alerte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
