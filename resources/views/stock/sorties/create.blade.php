@extends('layouts.app')

@section('title', 'Nouvelle Sortie Stock - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-arrow-up me-2"></i>Nouvelle Sortie de Stock
                    </h6>
                    <a href="{{ route('stock.exits') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="#">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date *</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Type de sortie *</label>
                                <select name="type" class="form-select" required>
                                    <option>Utilisation</option>
                                    <option>Transfert</option>
                                    <option>Retour fournisseur</option>
                                    <option>Perte/Casse</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Matériel *</label>
                                <select name="produit_id" class="form-select" required>
                                    <option value="">-- Sélectionner --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Quantité *</label>
                                <input type="number" name="quantite" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Destinataire</label>
                                <input type="text" name="destinataire" class="form-control" placeholder="Service, projet, véhicule...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Demandeur</label>
                                <input type="text" name="demandeur" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Motif</label>
                            <textarea name="motif" class="form-control" rows="2" placeholder="Raison de la sortie..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('stock.exits') }}" class="btn btn-secondary">
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
