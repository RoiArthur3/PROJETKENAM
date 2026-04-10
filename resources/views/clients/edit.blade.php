@extends('layouts.app')

@section('title', 'Modifier Client - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-edit mr-2 text-primary"></i>
                Modifier le Client
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                    <li class="breadcrumb-item active">Modifier Client #{{ $id }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-edit mr-2"></i>
                        Modifier les Informations du Client
                    </h6>
                </div>
                <div class="card-body">
                    <form id="clientEditForm" method="POST" action="{{ route('clients.update', $id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom du Client *</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" name="nom" required
                                       value="{{ old('nom', 'Client #' . $id) }}"
                                       placeholder="Ex: Jean Dupont">
                                <div class="form-text">Entrez le nom complet du client.</div>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">Sélectionner le type</option>
                                    <option value="Particulier" {{ old('type', 'Particulier') == 'Particulier' ? 'selected' : '' }}>Particulier</option>
                                    <option value="Entreprise" {{ old('type', 'Entreprise') == 'Entreprise' ? 'selected' : '' }}>Entreprise</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email', 'client' . $id . '@example.com') }}"
                                       placeholder="Ex: jean.dupont@email.com">
                                <div class="form-text">Adresse email pour les factures et communications.</div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                       id="telephone" name="telephone"
                                       value="{{ old('telephone', '+225 01 02 03 04 05') }}"
                                       placeholder="Ex: +225 01 02 03 04 05">
                                <div class="form-text">Numéro de téléphone principal.</div>
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <textarea class="form-control @error('adresse') is-invalid @enderror"
                                          id="adresse" name="adresse" rows="3"
                                          placeholder="Adresse complète du client">{{ old('adresse', 'Adresse du client #' . $id) }}</textarea>
                                @error('adresse')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control @error('ville') is-invalid @enderror"
                                       id="ville" name="ville"
                                       value="{{ old('ville', 'Abidjan') }}"
                                       placeholder="Ville du client">
                                @error('ville')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pays" class="form-label">Pays</label>
                                <select class="form-select @error('pays') is-invalid @enderror"
                                        id="pays" name="pays">
                                    <option value="Côte d'Ivoire" {{ old('pays', 'Côte d\'Ivoire') == 'Côte d\'Ivoire' ? 'selected' : '' }}>Côte d'Ivoire</option>
                                    <option value="France" {{ old('pays') == 'France' ? 'selected' : '' }}>France</option>
                                    <option value="Sénégal" {{ old('pays') == 'Sénégal' ? 'selected' : '' }}>Sénégal</option>
                                    <option value="Mali" {{ old('pays') == 'Mali' ? 'selected' : '' }}>Mali</option>
                                    <option value="Burkina Faso" {{ old('pays') == 'Burkina Faso' ? 'selected' : '' }}>Burkina Faso</option>
                                </select>
                                @error('pays')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select @error('statut') is-invalid @enderror"
                                        id="statut" name="statut">
                                    <option value="Actif" {{ old('statut', 'Actif') == 'Actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="Inactif" {{ old('statut') == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                                    <option value="Suspendu" {{ old('statut') == 'Suspendu' ? 'selected' : '' }}>Suspendu</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Informations complémentaires sur le client">{{ old('notes', 'Client numéro ' . $id . ' - Informations à compléter') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('clients.show', $id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Annuler
                            </a>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i> Enregistrer les Modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informations Système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>ID Client:</strong> #{{ $id }}
                        </div>
                        <div class="col-md-6">
                            <strong>Dernière Modification:</strong> {{ now()->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Créé le:</strong> {{ now()->subDays(rand(1, 365))->format('d/m/Y') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Modifié par:</strong> {{ auth()->user()->name ?? 'Utilisateur' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation du formulaire
document.getElementById('clientEditForm').addEventListener('submit', function(e) {
    const nom = document.getElementById('nom').value.trim();
    const type = document.getElementById('type').value;

    if (!nom) {
        e.preventDefault();
        alert('Le nom du client est obligatoire.');
        return false;
    }

    if (!type) {
        e.preventDefault();
        alert('Le type de client est obligatoire.');
        return false;
    }

    // Afficher un message de chargement
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Enregistrement...';
    submitBtn.disabled = true;

    // Réactiver après un délai (au cas où la soumission échoue)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

// Auto-formatage du téléphone
document.getElementById('telephone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 0) {
        if (value.length <= 2) {
            value = value;
        } else if (value.length <= 4) {
            value = value.slice(0, 2) + ' ' + value.slice(2);
        } else if (value.length <= 6) {
            value = value.slice(0, 2) + ' ' + value.slice(2, 4) + ' ' + value.slice(4);
        } else if (value.length <= 8) {
            value = value.slice(0, 2) + ' ' + value.slice(2, 4) + ' ' + value.slice(4, 6) + ' ' + value.slice(6);
        } else {
            value = value.slice(0, 2) + ' ' + value.slice(2, 4) + ' ' + value.slice(4, 6) + ' ' + value.slice(6, 8) + ' ' + value.slice(8, 10);
        }
    }
    e.target.value = value;
});
</script>
@endsection
