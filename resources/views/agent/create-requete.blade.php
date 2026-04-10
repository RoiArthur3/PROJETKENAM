@extends('layouts.app')

@section('title', 'Créer une Requête - KENAM SERVICES')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-6">
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nouvelle requête</h5>
          <a href="{{ route('agent.requetes.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
        </div>
        <div class="card-body">
      <form method="POST" action="{{ route('agent.requetes.store') }}" class="row g-3" enctype="multipart/form-data">
        @csrf
        <div class="col-md-6">
          <label class="form-label small">Type d'opération *</label>
          <select name="operation_id" class="form-select @error('operation_id') is-invalid @enderror" required>
            <option value="">Sélectionnez une opération</option>
            @foreach($operations as $operation)
              <option value="{{ $operation->id }}" {{ old('operation_id') == $operation->id ? 'selected' : '' }}>
                {{ $operation->libelle }}
              </option>
            @endforeach
          </select>
          @error('operation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small">Priorité *</label>
          <select name="priorite" class="form-select @error('priorite') is-invalid @enderror" required>
            <option value="">Sélectionnez une priorité</option>
            <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
            <option value="moyenne" {{ old('priorite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
            <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
            <option value="urgente" {{ old('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
          </select>
          @error('priorite') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
          <label class="form-label small">Titre de la requête *</label>
          <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror" value="{{ old('titre') }}" placeholder="Entrez un titre clair et concis" required>
          @error('titre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
          <label class="form-label small">Description détaillée *</label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6" placeholder="Décrivez en détail votre requête, les raisons, les objectifs, etc." required>{{ old('description') }}</textarea>
          @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
          <label class="form-label small">Fichiers joints (optionnel)</label>
          <input type="file" name="fichiers[]" class="form-control @error('fichiers.*') is-invalid @enderror" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
          @error('fichiers.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('agent.requetes.index') }}" class="btn btn-light">Annuler</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Soumettre</button>
        </div>
      </form>
    </div>
      </div>
    </div>
  </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compteur de caractères pour la description
    const description = document.getElementById('description');
    const maxLength = 2000;

    // Ajouter un compteur de caractères
    const counter = document.createElement('small');
    counter.className = 'text-muted';
    counter.style.marginTop = '5px';
    description.parentNode.insertBefore(counter, description.nextSibling);

    function updateCounter() {
        const remaining = maxLength - description.value.length;
        counter.textContent = `${description.value.length}/${maxLength} caractères`;

        if (remaining < 100) {
            counter.className = 'text-warning';
        } else {
            counter.className = 'text-muted';
        }
    }

    description.addEventListener('input', updateCounter);
    updateCounter();

    // Validation du formulaire avant soumission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const titre = document.getElementById('titre').value.trim();
        const description = document.getElementById('description').value.trim();

        if (titre.length < 5) {
            alert('Le titre doit contenir au moins 5 caractères.');
            e.preventDefault();
            return;
        }

        if (description.length < 20) {
            alert('La description doit contenir au moins 20 caractères.');
            e.preventDefault();
            return;
        }

        if (!confirm('Êtes-vous sûr de vouloir soumettre cette requête ? Une fois soumise, elle ne pourra plus être modifiée.')) {
            e.preventDefault();
        }
    });

    // Aperçu des fichiers sélectionnés
    const fileInput = document.getElementById('fichiers');
    fileInput.addEventListener('change', function() {
        const files = this.files;
        let fileInfo = '';

        if (files.length > 0) {
            fileInfo = `<small class="text-info mt-2 d-block">Fichiers sélectionnés (${files.length}):</small><ul class="small text-muted mt-1">`;
            for (let i = 0; i < files.length; i++) {
                const size = (files[i].size / 1024 / 1024).toFixed(2);
                fileInfo += `<li>${files[i].name} (${size} MB)</li>`;
            }
            fileInfo += '</ul>';
        }

        // Supprimer l'ancien aperçu s'il existe
        const oldPreview = this.parentNode.querySelector('.file-preview');
        if (oldPreview) {
            oldPreview.remove();
        }

        if (fileInfo) {
            const preview = document.createElement('div');
            preview.className = 'file-preview';
            preview.innerHTML = fileInfo;
            this.parentNode.insertBefore(preview, this.nextSibling);
        }
    });
});
</script>
@endsection
