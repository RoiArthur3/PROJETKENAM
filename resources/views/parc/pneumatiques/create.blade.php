@extends('layouts.app')
@section('title', 'Nouvelle Opération Pneumatiques - KENAM SERVICES')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-circle-notch me-2"></i>Nouvelle Opération Pneumatiques</h6>
                    <a href="{{ route('parc.pneumatiques') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left me-1"></i>Retour</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="#">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Véhicule *</label><select name="vehicule_id" class="form-select" required><option value="">-- Sélectionner --</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Date *</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Type opération *</label><select name="type" class="form-select" required><option>Changement</option><option>Permutation</option><option>Réparation</option><option>Gonflage</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Position</label><select name="position" class="form-select"><option>Avant gauche</option><option>Avant droit</option><option>Arrière gauche</option><option>Arrière droit</option></select></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Marque pneu</label><input type="text" name="marque" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Coût (FCFA)</label><input type="number" name="cout" class="form-control" min="0" step="0.01"></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('parc.pneumatiques') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Annuler</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
