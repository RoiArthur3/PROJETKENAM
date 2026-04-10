@extends('layouts.app')

@section('title', 'Nouveau Produit - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-box me-2"></i>Nouveau Produit
                    </h6>
                    <a href="{{ route('stock.produits') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('stock.produits.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Code produit *</label>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Désignation *</label>
                                <input type="text" name="designation" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Catégorie</label>
                                <select name="categorie" class="form-select">
                                    <option>Pièces détachées</option>
                                    <option>Consommables</option>
                                    <option>Équipements</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Unité</label>
                                <input type="text" name="unite" class="form-control" placeholder="Ex: pièce, litre, kg" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Stock minimum</label>
                                <input type="number" name="stock_min" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Prix unitaire (FCFA)</label>
                                <input type="number" name="prix_unitaire" class="form-control" min="0" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Emplacement</label>
                                <input type="text" name="emplacement" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('stock.produits') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
