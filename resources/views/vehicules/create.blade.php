@extends('layouts.app')

@section('title', 'Matériel roulant - Nouveau véhicule | KENAM SERVICES')

@section('content')
<div class="container-fluid">
  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Nouveau véhicule</h5>
      <a href="{{ route('materiel.vehicules') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i>Retour</a>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('materiel.vehicules.store') }}" class="row g-3">
        @csrf
        <div class="col-md-4">
          <label class="form-label small">Type de matériel</label>
          <select name="type_materiel" class="form-select" required>
            <option value="Vehicule" {{ old('type_materiel', 'Vehicule') === 'Vehicule' ? 'selected' : '' }}>Véhicule</option>
            <option value="Machine" {{ old('type_materiel') === 'Machine' ? 'selected' : '' }}>Machine</option>
            <option value="Camion" {{ old('type_materiel') === 'Camion' ? 'selected' : '' }}>Camion</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label small">Marque</label>
          <input type="text" name="marque" class="form-control" value="{{ old('marque') }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label small">Modèle</label>
          <input type="text" name="modele" class="form-control" value="{{ old('modele') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label small">Immatriculation</label>
          <input type="text" name="immatriculation" class="form-control" value="{{ old('immatriculation') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label small">Année</label>
          <input type="number" name="annee" class="form-control" value="{{ old('annee') }}" min="1900" max="{{ date('Y') + 1 }}">
        </div>
        <div class="col-md-4">
          <label class="form-label small">Couleur</label>
          <input type="text" name="couleur" class="form-control" value="{{ old('couleur') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label small">Carburant</label>
          <input type="text" name="carburant" class="form-control" value="{{ old('carburant') }}">
        </div>
      </div>

      <!-- Provenance du matériel -->
      <div class="row mb-3">
        <div class="col-12">
          <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>Provenance du matériel</h6>
        </div>
        <!-- Type de fournisseur field removed as requested -->
        <div class="col-md-4">
          <label class="form-label small">Provenance *</label>
          <select name="provenance" id="provenance" class="form-select" required>
            <option value="">Sélectionner la provenance</option>
            <option value="kenam" {{ old('provenance') === 'kenam' ? 'selected' : '' }}>Kenam Services</option>
            <option value="fournisseur" {{ old('provenance') === 'fournisseur' ? 'selected' : '' }}>Autre fournisseur</option>
          </select>
        </div>
        <div class="col-md-4" id="fournisseur_field" style="display: none;">
          <label class="form-label small">Fournisseur *</label>
          <select name="fournisseur_id" id="fournisseur_id" class="form-select">
            <option value="">Sélectionner un fournisseur</option>
            @if(isset($fournisseurs) && count($fournisseurs) > 0)
              @foreach($fournisseurs as $fournisseur)
                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                  {{ $fournisseur->raison_sociale }}
                </option>
              @endforeach
            @else
              <option value="">Aucun fournisseur disponible</option>
            @endif
          </select>
          <div class="form-text">Sélectionnez le fournisseur d'origine du matériel.</div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <label class="form-label small">Date d'achat</label>
          <input type="date" name="date_achat" class="form-control" value="{{ old('date_achat') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label small">Prix d'achat</label>
          <input type="number" name="prix_achat" step="0.01" class="form-control" value="{{ old('prix_achat') }}" min="0">
        </div>
      </div>

      <!-- Informations Location -->
      <div class="row mt-3 mb-3">
        <div class="col-12">
          <h6 class="text-muted mb-3"><i class="fas fa-file-contract me-2"></i>Informations de Location (Commercial)</h6>
        </div>
        <div class="col-md-6">
          <label class="form-label small">Prix de la location (Journalier) *</label>
          <div class="input-group">
            <input type="number" name="prix_location" step="0.01" class="form-control" value="{{ old('prix_location') }}" min="0">
            <span class="input-group-text">FCFA</span>
          </div>
          <div class="form-text">Ce prix sera utilisé pour les devis commerciaux.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small">Date de début du contrat</label>
          <input type="date" name="date_debut_contrat" class="form-control" value="{{ old('date_debut_contrat') }}">
        </div>
      </div>
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="disponible" name="disponible" {{ old('disponible', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="disponible">Disponible</label>
          </div>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('materiel.vehicules') }}" class="btn btn-light">Annuler</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleFournisseurFieldByProvenance() {
  const provenance = document.getElementById('provenance').value;
  const fournisseurField = document.getElementById('fournisseur_field');
  const fournisseurSelect = document.getElementById('fournisseur_id');
  if (provenance === 'fournisseur') {
    fournisseurField.style.display = 'block';
    fournisseurSelect.required = true;
  } else {
    fournisseurField.style.display = 'none';
    fournisseurSelect.required = false;
    fournisseurSelect.value = '';
  }
  updateEnginsDropdown();
}

function updateEnginsDropdown() {
  const provenance = document.getElementById('provenance').value;
  const fournisseurId = document.getElementById('fournisseur_id').value;
  const enginsSelect = document.getElementById('vehicle_id');
  let type = provenance === 'kenam' ? 'kenam' : 'autres';
  let params = provenance === 'kenam' ? { type } : { type, fournisseur_id: fournisseurId };
  fetch('/ajax/fournisseur-engins?' + new URLSearchParams(params))
    .then(response => response.json())
    .then(data => {
      enginsSelect.innerHTML = '<option value="">Sélectionner un engin</option>';
      data.forEach(engin => {
        enginsSelect.innerHTML += `<option value="${engin.id}">${engin.immatriculation} - ${engin.marque} ${engin.modele}</option>`;
      });
    });
}

document.addEventListener('DOMContentLoaded', function() {
  toggleFournisseurFieldByProvenance();
  document.getElementById('provenance').addEventListener('change', toggleFournisseurFieldByProvenance);
  document.getElementById('fournisseur_id').addEventListener('change', updateEnginsDropdown);
});
</script>
@endsection
