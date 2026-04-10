@extends('layouts.app')

@section('title', 'Ajouter un Fournisseur - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Ajouter un Nouveau Fournisseur</h1>
        <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Informations du Fournisseur
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('fournisseurs.store') }}" method="POST">
                        <!-- Type de fournisseur -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="type_fournisseur" class="form-label">Type de fournisseur</label>
                                <select class="form-select" id="type_fournisseur" name="type_fournisseur" onchange="toggleEnginsList()" required>
                                    <option value="">Sélectionner le type</option>
                                    <option value="kenam">Interne - Kenam</option>
                                    <option value="autres">Externe - Autres</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="engins_list_field" style="display:none;">
                                <label for="engins_list" class="form-label">Liste des engins</label>
                                <select class="form-select" id="engins_list" name="engins_list">
                                    <option value="">Sélectionner un engin</option>
                                    <!-- Les options doivent être générées dynamiquement côté backend selon le type -->
                                </select>
                                <div class="form-text">Liste des engins selon le type de fournisseur.</div>
                            </div>
                        </div>
                        @csrf

                        <!-- Informations Principales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Informations Principales
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="raison_sociale" class="form-label">
                                    Nom du Fournisseur
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <input type="text" class="form-control" id="raison_sociale" name="raison_sociale"
                                           placeholder="Ex: Total Energies" value="{{ old('raison_sociale') }}">
                                </div>
                                @error('raison_sociale')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="categorie_id" class="form-label">Catégorie</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <select class="form-select" id="categorie_id" name="categorie_id">
                                        <option value="">Sélectionner une catégorie</option>
                                        @forelse(($categories ?? []) as $categorie)
                                            <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                                {{ $categorie->nom }}
                                            </option>
                                        @empty
                                            <option value="">Aucune catégorie disponible</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Coordonnées -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-phone me-2"></i>
                                    Coordonnées
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email"
                                           placeholder="contact@total.ci" value="{{ old('email') }}">
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" id="telephone" name="telephone"
                                           placeholder="+225 01 02 03 04 05" value="{{ old('telephone') }}">
                                </div>
                                @error('telephone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Adresse
                                </h6>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="adresse" class="form-label">Adresse Complète</label>
                                <textarea class="form-control" id="adresse" name="adresse" rows="2"
                                          placeholder="Adresse complète du fournisseur...">{{ old('adresse') }}</textarea>
                                @error('adresse')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-city"></i>
                                    </span>
                                    <input type="text" class="form-control" id="ville" name="ville"
                                           placeholder="Abidjan" value="{{ old('ville') }}">
                                </div>
                                @error('ville')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pays" class="form-label">Pays</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                    <input type="text" class="form-control" id="pays" name="pays"
                                           placeholder="Côte d'Ivoire" value="{{ old('pays') ?? 'Côte d\'Ivoire' }}">
                                </div>
                                @error('pays')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informations Supplémentaires -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    Informations Supplémentaires
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="annee_contractuelle" class="form-label">Année contractuelle (optionnel)</label>
                                <input type="number" class="form-control" id="annee_contractuelle" name="annee_contractuelle"
                                       min="1900" max="2100" value="{{ old('annee_contractuelle') }}"
                                       placeholder="Ex: 2026">
                                @error('annee_contractuelle')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="note_appreciation" class="form-label">Note (manuel)</label>
                                <select class="form-select" id="note_appreciation" name="note_appreciation">
                                    <option value="">Sélectionner une note</option>
                                    @foreach(['Bon', 'Difficile', 'Tres difficile', 'Passable', 'Mauvais'] as $note)
                                        <option value="{{ $note }}" {{ old('note_appreciation') === $note ? 'selected' : '' }}>{{ $note }}</option>
                                    @endforeach
                                </select>
                                @error('note_appreciation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes / Description</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                          placeholder="Informations supplémentaires sur le fournisseur...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinner"></span>
                                <i class="fas fa-save me-2"></i>
                                Ajouter le Fournisseur
                            </button>
                        </div>
                    </form>
                <script>
                function toggleEnginsList() {
    const type = document.getElementById('type_fournisseur').value;
    const enginsField = document.getElementById('engins_list_field');
    const enginsSelect = document.getElementById('engins_list');

    if (type === 'kenam' || type === 'autres') {
        enginsField.style.display = 'block';
        enginsSelect.innerHTML = '<option value="">Chargement des engins...</option>';

        // Charger dynamiquement la liste des engins
        fetch(`/ajax/fournisseur-engins?type=${type}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(result => {
                enginsSelect.innerHTML = '<option value="">Sélectionner un engin</option>';

                if (result.success && result.data && result.data.length > 0) {
                    result.data.forEach(engin => {
                        enginsSelect.innerHTML += `<option value="${engin.id}">${engin.label}</option>`;
                    });
                    console.log(`${result.count} engin(s) chargé(s) pour le type: ${type}`);
                } else if (result.success && result.data && result.data.length === 0) {
                    enginsSelect.innerHTML = '<option value="">Aucun engin disponible pour ce type</option>';
                    console.log('Aucun engin trouvé pour le type: ' + type);
                } else {
                    enginsSelect.innerHTML = '<option value="">Erreur lors du chargement</option>';
                    console.error('Erreur:', result.message || 'Erreur inconnue');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                enginsSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });
    } else {
        enginsField.style.display = 'none';
        enginsSelect.innerHTML = '<option value="">Sélectionner un engin</option>';
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    toggleEnginsList();

    // Ajouter un écouteur d'événements sur le champ type_fournisseur
    const typeField = document.getElementById('type_fournisseur');
    if (typeField) {
        typeField.addEventListener('change', toggleEnginsList);
    }

    // Gestion du formulaire
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Afficher le spinner et désactiver le bouton
            const spinner = document.getElementById('spinner');
            const submitBtn = document.getElementById('submitBtn');

            spinner.classList.remove('d-none');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Enregistrement en cours...
            `;
        });
    }
});
</script>
@endsection
