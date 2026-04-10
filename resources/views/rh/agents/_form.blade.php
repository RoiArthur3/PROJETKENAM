@props(['agent' => null, 'method' => 'POST', 'action' => '#'])

<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name', $agent->name ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email', $agent->email ?? '') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="service_id" class="form-label">Service</label>
                <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id">
                    <option value="">Non assigné</option>
                    @if(isset($services))
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ (string) old('service_id', $agent->service_id ?? '') === (string) $service->id ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('service_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                       id="telephone" name="telephone" value="{{ old('telephone', $agent->telephone ?? ($agent->phone ?? '')) }}">
                @error('telephone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                <select class="form-select @error('role') is-invalid @enderror"
                        id="role" name="role" required>
                    <option value="" disabled {{ !old('role', $agent->role ?? '') ? 'selected' : '' }}>Sélectionner un rôle</option>
                    <option value="agent" {{ old('role', $agent->role ?? '') == 'agent' ? 'selected' : '' }}>Agent</option>
                    <option value="superviseur" {{ old('role', $agent->role ?? '') == 'superviseur' ? 'selected' : '' }}>Superviseur</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="contrat" class="form-label">Type de contrat</label>
                <select class="form-select @error('contrat') is-invalid @enderror" id="contrat" name="contrat">
                    <option value="">Non renseigné</option>
                    <option value="CDD" {{ old('contrat', $agent->contrat ?? '') == 'CDD' ? 'selected' : '' }}>CDD</option>
                    <option value="CDI" {{ old('contrat', $agent->contrat ?? '') == 'CDI' ? 'selected' : '' }}>CDI</option>
                </select>
                @error('contrat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="date_embauche" class="form-label">Début de contrat</label>
                <input type="date" class="form-control @error('date_embauche') is-invalid @enderror"
                       id="date_embauche" name="date_embauche" value="{{ old('date_embauche', $agent->date_embauche ?? '') }}">
                @error('date_embauche')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label for="date_fin_contrat" class="form-label">Fin de contrat (CDD)</label>
                <input type="date" class="form-control @error('date_fin_contrat') is-invalid @enderror"
                       id="date_fin_contrat" name="date_fin_contrat" value="{{ old('date_fin_contrat', $agent->date_fin_contrat ?? '') }}">
                @error('date_fin_contrat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label for="salaire" class="form-label">Salaire</label>
                <input type="number" step="0.01" class="form-control @error('salaire') is-invalid @enderror"
                       id="salaire" name="salaire" value="{{ old('salaire', $agent->salaire ?? '') }}">
                @error('salaire')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="{{ route('rh.agents.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>

        <div class="btn-group">
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo me-1"></i> Réinitialiser
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> {{ isset($agent) ? 'Mettre à jour' : 'Créer' }}
            </button>
        </div>
    </div>
</form>
