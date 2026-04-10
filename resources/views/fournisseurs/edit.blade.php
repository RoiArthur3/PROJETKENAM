@extends('layouts.app')

@section('title', 'Fournisseur - Édition | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier le fournisseur</h1>
        <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour au détail
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('fournisseurs.update', $fournisseur) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Raison sociale *</label>
                        <input type="text" name="raison_sociale" class="form-control" value="{{ old('raison_sociale', $fournisseur->raison_sociale) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Catégorie</label>
                        <select name="categorie_id" class="form-select">
                            <option value="">Aucune</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}" @selected(old('categorie_id', $fournisseur->categorie_id) == $categorie->id)>
                                    {{ $categorie->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ville *</label>
                        <input type="text" name="ville" class="form-control" value="{{ old('ville', $fournisseur->ville) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pays *</label>
                        <input type="text" name="pays" class="form-control" value="{{ old('pays', $fournisseur->pays) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Actif</label>
                        <select name="est_actif" class="form-select">
                            <option value="1" @selected(old('est_actif', $fournisseur->est_actif))>Oui</option>
                            <option value="0" @selected(!old('est_actif', $fournisseur->est_actif))>Non</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $fournisseur->email) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone *</label>
                        <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $fournisseur->telephone) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Adresse *</label>
                    <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $fournisseur->adresse) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $fournisseur->notes) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Année contractuelle (optionnel)</label>
                        <input type="number" name="annee_contractuelle" class="form-control"
                               min="1900" max="2100"
                               value="{{ old('annee_contractuelle', $fournisseur->annee_contractuelle) }}"
                               placeholder="Ex: 2026">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Note (manuel)</label>
                        <select name="note_appreciation" class="form-select">
                            <option value="">Sélectionner une note</option>
                            @foreach(['Bon', 'Difficile', 'Tres difficile', 'Passable', 'Mauvais'] as $note)
                                <option value="{{ $note }}" @selected(old('note_appreciation', $fournisseur->note_appreciation) === $note)>{{ $note }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>

                    <form action="{{ route('fournisseurs.destroy', $fournisseur) }}" method="POST" onsubmit="return confirm('Supprimer ce fournisseur ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Supprimer
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
