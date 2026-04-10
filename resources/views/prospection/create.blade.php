@extends('layouts.app')

@section('title', 'Ajouter un Prospect - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Ajouter un Nouveau Prospect</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du Prospect</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom du Prospect</label>
                                <input type="text" class="form-control" id="nom" required placeholder="Ex: Entreprise ABC">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="entreprise" class="form-label">Entreprise (optionnel)</label>
                                <input type="text" class="form-control" id="entreprise" placeholder="Ex: ABC Logistics">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Ex: contact@abc.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" placeholder="Ex: +225 01 02 03 04 05">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="adresse" rows="2" placeholder="Adresse complète..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="source" class="form-label">Source</label>
                                <select class="form-select" id="source" required>
                                    <option value="reseaux_sociaux">Réseaux Sociaux</option>
                                    <option value="referencement">Référencement</option>
                                    <option value="bouche_oreille">Bouche à Oreille</option>
                                    <option value="publicite">Publicité</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="valeur_potentielle" class="form-label">Valeur Potentielle (FCFA)</label>
                                <input type="number" class="form-control" id="valeur_potentielle" placeholder="Ex: 500000" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" rows="3" placeholder="Informations supplémentaires..."></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('prospection.index') }}" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Ajouter le Prospect</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
