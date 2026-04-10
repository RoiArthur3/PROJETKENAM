@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Modifier le véhicule</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('materiel.vehicules.update', $vehicule) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="type_materiel" class="form-label">Type de matériel</label>
                                <select name="type_materiel" id="type_materiel" class="form-select" required>
                                    <option value="Vehicule" {{ old('type_materiel', $vehicule->type_materiel ?? 'Vehicule') === 'Vehicule' ? 'selected' : '' }}>Véhicule</option>
                                    <option value="Machine" {{ old('type_materiel', $vehicule->type_materiel ?? '') === 'Machine' ? 'selected' : '' }}>Machine</option>
                                    <option value="Camion" {{ old('type_materiel', $vehicule->type_materiel ?? '') === 'Camion' ? 'selected' : '' }}>Camion</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="marque" class="form-label">Marque</label>
                                <input type="text" class="form-control" id="marque" name="marque" value="{{ old('marque', $vehicule->marque) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="modele" class="form-label">Modèle</label>
                                <input type="text" class="form-control" id="modele" name="modele" value="{{ old('modele', $vehicule->modele) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="immatriculation" class="form-label">Immatriculation</label>
                            <input type="text" class="form-control" id="immatriculation" name="immatriculation" value="{{ old('immatriculation', $vehicule->immatriculation) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="annee" class="form-label">Année</label>
                            <input type="number" class="form-control" id="annee" name="annee" value="{{ old('annee', $vehicule->annee) }}" min="1900" max="{{ date('Y') + 1 }}">
                        </div>

                        <div class="mb-3">
                            <label for="couleur" class="form-label">Couleur</label>
                            <input type="text" class="form-control" id="couleur" name="couleur" value="{{ old('couleur', $vehicule->couleur) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="provenance" class="form-label">Provenance</label>
                                <select name="provenance" id="provenance" class="form-select" required>
                                    <option value="">Sélectionner la provenance</option>
                                    <option value="kenam" {{ old('provenance', $vehicule->provenance) === 'kenam' ? 'selected' : '' }}>Kenam Services</option>
                                    <option value="fournisseur" {{ old('provenance', $vehicule->provenance) === 'fournisseur' ? 'selected' : '' }}>Autre fournisseur</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="fournisseur_field" style="display: none;">
                                <label for="fournisseur_id" class="form-label">Fournisseur</label>
                                <select name="fournisseur_id" id="fournisseur_id" class="form-select">
                                    <option value="">Sélectionner un fournisseur</option>
                                    @if(isset($fournisseurs) && count($fournisseurs) > 0)
                                        @foreach($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $vehicule->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>
                                                {{ $fournisseur->raison_sociale }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Aucun fournisseur disponible</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="date_achat" class="form-label">Date d'achat</label>
                            <input type="date" class="form-control" id="date_achat" name="date_achat" value="{{ old('date_achat', optional($vehicule->date_achat)->format('Y-m-d')) }}">
                        </div>

                        <div class="mb-3">
                            <label for="prix_achat" class="form-label">Prix d'achat</label>
                            <input type="number" step="0.01" class="form-control" id="prix_achat" name="prix_achat" value="{{ old('prix_achat', $vehicule->prix_achat) }}" min="0">
                        </div>

                        <div class="mb-3">
                            <label for="carburant" class="form-label">Type de carburant</label>
                            <input type="text" class="form-control" id="carburant" name="carburant" value="{{ old('carburant', $vehicule->carburant) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prix_location" class="form-label">Prix de la location (Journalier)</label>
                                <input type="number" step="0.01" class="form-control" id="prix_location" name="prix_location" value="{{ old('prix_location', $vehicule->prix_location) }}" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_debut_contrat" class="form-label">Date début contrat</label>
                                <input type="date" class="form-control" id="date_debut_contrat" name="date_debut_contrat" value="{{ old('date_debut_contrat', $vehicule->date_debut_contrat ? \Carbon\Carbon::parse($vehicule->date_debut_contrat)->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="disponible" name="disponible" {{ old('disponible', $vehicule->disponible) ? 'checked' : '' }}>
                            <label class="form-check-label" for="disponible">
                                Véhicule disponible
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        <a href="{{ route('materiel.vehicules') }}" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFournisseurFieldByProvenanceEdit() {
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
}

document.addEventListener('DOMContentLoaded', function () {
    toggleFournisseurFieldByProvenanceEdit();
    document.getElementById('provenance').addEventListener('change', toggleFournisseurFieldByProvenanceEdit);
});
</script>
@endsection
