@extends('layouts.app')

@section('title', 'Nouveau document juridique')

@section('content')
<div class="content-wrapper">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-file-plus me-2"></i>Nouveau document juridique
        </h1>
        <a href="{{ route('juridique.documents.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('juridique.documents.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <!-- Référence -->
                    <div class="col-md-4">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" id="reference" name="reference" class="form-control"
                               value="{{ old('reference') }}" placeholder="Ex: DOC-2026-001">
                        @error('reference')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Titre -->
                    <div class="col-md-8">
                        <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" id="titre" name="titre" class="form-control"
                               value="{{ old('titre') }}" required placeholder="Titre du document">
                        @error('titre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type de document -->
                    <div class="col-md-4">
                        <label for="type_document" class="form-label">Type de document</label>
                        <select id="type_document" name="type_document" class="form-select">
                            <option value="">Sélectionner un type</option>
                            <option value="contrat" {{ old('type_document') == 'contrat' ? 'selected' : '' }}>Contrat</option>
                            <option value="convention" {{ old('type_document') == 'convention' ? 'selected' : '' }}>Convention</option>
                            <option value="facture" {{ old('type_document') == 'facture' ? 'selected' : '' }}>Facture</option>
                            <option value="devis" {{ old('type_document') == 'devis' ? 'selected' : '' }}>Devis</option>
                            <option value="acte" {{ old('type_document') == 'acte' ? 'selected' : '' }}>Acte</option>
                            <option value="courrier" {{ old('type_document') == 'courrier' ? 'selected' : '' }}>Courrier</option>
                            <option value="autre" {{ old('type_document') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type_document')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contrat lié -->
                    <div class="col-md-8">
                        <label for="contrat_id" class="form-label">Contrat lié</label>
                        <select id="contrat_id" name="contrat_id" class="form-select">
                            <option value="">Aucun contrat</option>
                            @foreach($contrats as $contrat)
                                <option value="{{ $contrat->id }}" {{ old('contrat_id') == $contrat->id ? 'selected' : '' }}>
                                    {{ $contrat->titre }} ({{ $contrat->reference }})
                                </option>
                            @endforeach
                        </select>
                        @error('contrat_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date du document -->
                    <div class="col-md-4">
                        <label for="date_document" class="form-label">Date du document</label>
                        <input type="date" id="date_document" name="date_document" class="form-control"
                               value="{{ old('date_document') }}">
                        @error('date_document')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date d'expiration -->
                    <div class="col-md-4">
                        <label for="date_expiration" class="form-label">Date d'expiration</label>
                        <input type="date" id="date_expiration" name="date_expiration" class="form-control"
                               value="{{ old('date_expiration') }}">
                        @error('date_expiration')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="col-md-4">
                        <label for="statut" class="form-label">Statut</label>
                        <select id="statut" name="statut" class="form-select">
                            <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                            <option value="archive" {{ old('statut') == 'archive' ? 'selected' : '' }}>Archivé</option>
                            <option value="expire" {{ old('statut') == 'expire' ? 'selected' : '' }}>Expiré</option>
                        </select>
                        @error('statut')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fichier -->
                    <div class="col-12">
                        <label for="fichier" class="form-label">Fichier du document</label>
                        <input type="file" id="fichier" name="fichier" class="form-control"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.txt,.jpg,.jpeg,.png,.webp,.zip,.rar">
                        <small class="text-muted">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, CSV, TXT, JPG, PNG, ZIP, RAR (Max: 10MB)</small>
                        @error('fichier')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="4"
                                  placeholder="Notes ou observations sur ce document...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('juridique.documents.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer le document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-body {
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    padding: 0.5rem 1.5rem;
}

.text-danger {
    font-size: 0.875rem;
}

.text-muted {
    font-size: 0.75rem;
}
</style>

<script>
// Auto-génération de référence
document.addEventListener('DOMContentLoaded', function() {
    const referenceInput = document.getElementById('reference');
    const titreInput = document.getElementById('titre');

    // Si la référence est vide, en générer une automatiquement
    if (!referenceInput.value) {
        const today = new Date();
        const year = today.getFullYear();
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        referenceInput.value = `DOC-${year}-${random}`;
    }

    // Optionnel: générer une référence basée sur le titre
    titreInput.addEventListener('blur', function() {
        if (!referenceInput.value || referenceInput.value.startsWith('DOC-')) {
            const titre = this.value.trim();
            if (titre) {
                const today = new Date();
                const year = today.getFullYear();
                const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                referenceInput.value = `DOC-${year}-${random}`;
            }
        }
    });

    // Validation de la date d'expiration
    const dateDocument = document.getElementById('date_document');
    const dateExpiration = document.getElementById('date_expiration');

    dateExpiration.addEventListener('change', function() {
        if (dateDocument.value && this.value) {
            if (new Date(this.value) < new Date(dateDocument.value)) {
                alert('La date d\'expiration ne peut pas être antérieure à la date du document.');
                this.value = '';
            }
        }
    });

    // Animation des champs
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach((input, index) => {
        input.style.opacity = '0';
        input.style.transform = 'translateY(10px)';
        setTimeout(() => {
            input.style.transition = 'all 0.3s ease';
            input.style.opacity = '1';
            input.style.transform = 'translateY(0)';
        }, index * 50);
    });
});
</script>
@endsection
