<x-dashboard-layout title="Créer une Commande" icon="fa-solid fa-plus-circle">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-plus-circle me-2"></i>
                        Créer une Commande
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commande.store') }}" class="row g-3">
                        @csrf

                        <!-- Informations principales -->
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Informations Principales
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label for="reference_commande" class="form-label">Référence *</label>
                            <input type="text" name="reference_commande" id="reference_commande" class="form-control @error('reference_commande', 'is-invalid')"
                                   value="{{ old('reference_commande') }}" placeholder="CMD-2025-001" required>
                            @error('reference_commande')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="type_commande" class="form-label">Type de commande *</label>
                            <select name="type_commande" id="type_commande" class="form-select @error('type_commande', 'is-invalid')" required>
                                <option value="">Sélectionner un type</option>
                                <option value="Transport de marchandises" {{ old('type_commande') == 'Transport de marchandises' ? 'selected' : '' }}>
                                    Transport de marchandises
                                </option>
                                <option value="Transport de personnel" {{ old('type_commande') == 'Transport de personnel' ? 'selected' : '' }}>
                                    Transport de personnel
                                </option>
                                <option value="Autre" {{ old('type_commande') == 'Autre' ? 'selected' : '' }}>
                                    Autre
                                </option>
                            </select>
                            @error('type_commande')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description', 'is-invalid')"
                                      rows="3" placeholder="Décrire la commande...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Dates -->
                        <div class="col-12">
                            <h5 class="mb-3 mt-4">
                                <i class="fa-solid fa-calendar me-2"></i>
                                Dates
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label for="date_commande" class="form-label">Date commande *</label>
                            <input type="date" name="date_commande" id="date_commande" class="form-control @error('date_commande', 'is-invalid')"
                                   value="{{ old('date_commande') }}" required>
                            @error('date_commande')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="date_debut" class="form-label">Date début *</label>
                            <input type="date" name="date_debut" id="date_debut" class="form-control @error('date_debut', 'is-invalid')"
                                   value="{{ old('date_debut') }}" required>
                            @error('date_debut')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="date_fin" class="form-label">Date fin</label>
                            <input type="date" name="date_fin" id="date_fin" class="form-control @error('date_fin', 'is-invalid')"
                                   value="{{ old('date_fin') }}">
                            @error('date_fin')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Affectation -->
                        <div class="col-12">
                            <h5 class="mb-3 mt-4">
                                <i class="fa-solid fa-users me-2"></i>
                                Affectation
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label for="chauffeur_id" class="form-label">Chauffeur *</label>
                            <select name="chauffeur_id" id="chauffeur_id" class="form-select @error('chauffeur_id', 'is-invalid')" required>
                                <option value="">Sélectionner un chauffeur</option>
                                @foreach($chauffeurs as $chauffeur)
                                    <option value="{{ $chauffeur->id }}" {{ old('chauffeur_id') == $chauffeur->id ? 'selected' : '' }}>
                                        {{ $chauffeur->nom }} {{ $chauffeur->prenom }} ({{ $chauffeur->telephone }})
                                    </option>
                                @endforeach
                            </select>
                            @error('chauffeur_id')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="materiel_roulant_id" class="form-label">Véhicule *</label>
                            <select name="materiel_roulant_id" id="materiel_roulant_id" class="form-select @error('materiel_roulant_id', 'is-invalid')" required>
                                <option value="">Sélectionner un véhicule</option>
                                @foreach($vehicules as $vehicule)
                                    <option value="{{ $vehicule->id }}" {{ old('materiel_roulant_id') == $vehicule->id ? 'selected' : '' }}>
                                        {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                                    </option>
                                @endforeach
                            </select>
                            @error('materiel_roulant_id')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="client_id" class="form-label">Client</label>
                            <select name="client_id" id="client_id" class="form-select @error('client_id', 'is-invalid')">
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Lieux et Kilométrage -->
                        <div class="col-12">
                            <h5 class="mb-3 mt-4">
                                <i class="fa-solid fa-map-marker-alt me-2"></i>
                                Lieux et Kilométrage
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label for="lieu_depart" class="form-label">Lieu de départ *</label>
                            <input type="text" name="lieu_depart" id="lieu_depart" class="form-control @error('lieu_depart', 'is-invalid')"
                                   value="{{ old('lieu_depart') }}" placeholder="Siège social" required>
                            @error('lieu_depart')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="lieu_arrivee" class="form-label">Lieu d'arrivée</label>
                            <input type="text" name="lieu_arrivee" id="lieu_arrivee" class="form-control @error('lieu_arrivee', 'is-invalid')"
                                   value="{{ old('lieu_arrivee') }}" placeholder="Client">
                            @error('lieu_arrivee')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="lieu_retour" class="form-label">Lieu de retour</label>
                            <input type="text" name="lieu_retour" id="lieu_retour" class="form-control @error('lieu_retour', 'is-invalid')"
                                   value="{{ old('lieu_retour') }}" placeholder="Siège social">
                            @error('lieu_retour')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="kilometrage_depart" class="form-label">Kilométrage au départ *</label>
                            <input type="number" name="kilometrage_depart" id="kilometrage_depart" class="form-control @error('kilometrage_depart', 'is-invalid')"
                                   value="{{ old('kilometrage_depart') }}" placeholder="0" step="0.01" min="0" required>
                            @error('kilometrage_depart')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="kilometrage_retour" class="form-label">Kilométrage au retour</label>
                            <input type="number" name="kilometrage_retour" id="kilometrage_retour" class="form-control @error('kilometrage_retour', 'is-invalid')"
                                   value="{{ old('kilometrage_retour') }}" placeholder="0" step="0.01" min="0">
                            @error('kilometrage_retour')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <h5 class="mb-3 mt-4">
                                <i class="fa-solid fa-comment me-2"></i>
                                Notes
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label for="observations" class="form-label">Observations</label>
                            <textarea name="observations" id="observations" class="form-control @error('observations', 'is-invalid')"
                                      rows="3" placeholder="Observations sur la commande...">{{ old('observations') }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="notes" class="form-label">Notes supplémentaires</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes', 'is-invalid')"
                                      rows="3" placeholder="Notes supplémentaires...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="col-md-6">
                            <label for="statut" class="form-label">Statut *</label>
                            <select name="statut" id="statut" class="form-select @error('statut', 'is-invalid')" required>
                                <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminée</option>
                                <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulée</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('commande.index') }}" class="btn btn-secondary">
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
                                        Créer la commande
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
