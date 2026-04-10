@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-plus-circle me-2"></i>Nouvelle requête entre services
            </h2>
            <p class="text-muted small mt-1">Créer et envoyer une nouvelle demande à un service</p>
        </div>
        <div>
            <a href="{{ url('/requetes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour aux requêtes
            </a>
        </div>
    </div>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('requetes.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Informations générales -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-info-circle me-2"></i>Informations de la demande
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label fw-bold">
                                                Objet de la demande <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('nom') is-invalid @enderror" 
                                                   id="nom" 
                                                   name="nom" 
                                                   value="{{ old('nom') }}"
                                                   placeholder="Ex: Demande d'approvisionnement matériel"
                                                   required>
                                            @error('nom')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label fw-bold">
                                                Description détaillée <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" 
                                                      name="description" 
                                                      rows="4" 
                                                      placeholder="Décrivez précisément votre demande..."
                                                      required>{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="type_requete" class="form-label fw-bold">
                                                        Type de requête <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('type_requete') is-invalid @enderror" 
                                                            id="type_requete" 
                                                            name="type_requete" 
                                                            required>
                                                        <option value="">Sélectionner un type</option>
                                                        <option value="demande_achat" {{ old('type_requete') == 'demande_achat' ? 'selected' : '' }}>Demande d'achat</option>
                                                        <option value="demande_maintenance" {{ old('type_requete') == 'demande_maintenance' ? 'selected' : '' }}>Demande de maintenance</option>
                                                        <option value="demande_rh" {{ old('type_requete') == 'demande_rh' ? 'selected' : '' }}>Demande RH</option>
                                                        <option value="demande_comptable" {{ old('type_requete') == 'demande_comptable' ? 'selected' : '' }}>Demande comptable</option>
                                                        <option value="demande_technique" {{ old('type_requete') == 'demande_technique' ? 'selected' : '' }}>Demande technique</option>
                                                        <option value="demande_magasin" {{ old('type_requete') == 'demande_magasin' ? 'selected' : '' }}>Demande magasin</option>
                                                        <option value="autre" {{ old('type_requete') == 'autre' ? 'selected' : '' }}>Autre</option>
                                                    </select>
                                                    @error('type_requete')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="priorite" class="form-label fw-bold">
                                                        Priorité <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('priorite') is-invalid @enderror" 
                                                            id="priorite" 
                                                            name="priorite" 
                                                            required>
                                                        <option value="BASSE" {{ old('priorite') == 'BASSE' ? 'selected' : '' }}>Basse</option>
                                                        <option value="MOYENNE" {{ old('priorite') == 'MOYENNE' ? 'selected' : '' }} selected>Moyenne</option>
                                                        <option value="HAUTE" {{ old('priorite') == 'HAUTE' ? 'selected' : '' }}>Haute</option>
                                                        <option value="URGENTE" {{ old('priorite') == 'URGENTE' ? 'selected' : '' }}>Urgente</option>
                                                    </select>
                                                    @error('priorite')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pièces jointes -->
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-paperclip me-2"></i>Pièces jointes
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="pieces_jointes" class="form-label">
                                                Joindre des documents (facultatif)
                                            </label>
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="pieces_jointes" 
                                                   name="pieces_jointes[]" 
                                                   multiple 
                                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                            <small class="text-muted">
                                                Formats acceptés : PDF, Word, Excel, Images (max 5MB par fichier)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Service destinataire -->
                                <div class="card mb-4">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">
                                            <i class="fas fa-users me-2"></i>Service destinataire
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="service_destinataire_id" class="form-label fw-bold">
                                                Service à solliciter <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('service_destinataire_id') is-invalid @enderror" 
                                                    id="service_destinataire_id" 
                                                    name="service_destinataire_id" 
                                                    required>
                                                <option value="">Sélectionner un service</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" {{ old('service_destinataire_id') == $service->id ? 'selected' : '' }}>
                                                        {{ $service->nom }} ({{ $service->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('service_destinataire_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="destinataire_id" class="form-label">
                                                Personne ressource (facultatif)
                                            </label>
                                            <select class="form-select @error('destinataire_id') is-invalid @enderror" 
                                                    id="destinataire_id" 
                                                    name="destinataire_id">
                                                <option value="">Sélectionner une personne</option>
                                            </select>
                                            <small class="text-muted">
                                                Choisissez une personne spécifique dans le service
                                            </small>
                                            @error('destinataire_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Aperçu de la notification -->
                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-envelope me-2"></i>Notification automatique</h6>
                                            <small>
                                                Le service sélectionné recevra un email contenant :<br>
                                                <strong>Objet :</strong> <span id="preview-objet">[objet de la demande]</span><br>
                                                <strong>Demandeur :</strong> {{ auth()->user()->name }}<br>
                                                <strong>Service :</strong> {{ optional(auth()->user()->service)->nom ?? 'Non défini' }}<br>
                                                <strong>Signature :</strong> KENAM SERVICES / {{ optional(auth()->user()->service)->nom ?? 'Service demandeur' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Résumé -->
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-check-circle me-2"></i>Résumé
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted">
                                            Une fois validée, cette demande sera :<br>
                                            • Enregistrée avec un numéro de référence<br>
                                            • Envoyée au service destinataire<br>
                                            • Traçable dans l'historique<br>
                                            • Notifiée par email automatique
                                        </p>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane me-2"></i>Créer et envoyer la demande
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Mise à jour de l'aperçu de l'objet
        document.getElementById('nom').addEventListener('input', function() {
            document.getElementById('preview-objet').textContent = this.value || '[objet de la demande]';
        });

        // Chargement dynamique des personnes du service
        document.getElementById('service_destinataire_id').addEventListener('change', function() {
            const serviceId = this.value;
            const destSelect = document.getElementById('destinataire_id');
            
            if (serviceId) {
                fetch(`/api/services/${serviceId}/users`)
                    .then(response => response.json())
                    .then(users => {
                        destSelect.innerHTML = '<option value="">Sélectionner une personne</option>';
                        users.forEach(user => {
                            destSelect.innerHTML += `<option value="${user.id}">${user.name}</option>`;
                        });
                    })
                    .catch(error => console.error('Erreur:', error));
            } else {
                destSelect.innerHTML = '<option value="">Sélectionner une personne</option>';
            }
        });
    </script>
    @endpush
@endsection
