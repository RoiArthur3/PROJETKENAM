@extends('layouts.app')
@section('title', 'Nouvelle Affectation - KENAM SERVICES')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-cog me-2"></i>Nouvelle Affectation Ressources</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="#">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Projet *</label><select name="projet_id" class="form-select" required><option value="">-- Sélectionner --</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Date début *</label><input type="date" name="date_debut" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Type ressource *</label><select name="type_ressource" class="form-select" required><option>Véhicule</option><option>Personnel</option><option>Équipement</option></select></div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Ressource *</label><select name="ressource_id" class="form-select" required><option value="">-- Sélectionner --</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Rôle/Fonction</label><input type="text" name="role" class="form-control" placeholder="Ex: Chauffeur, Superviseur..."></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Date fin prévue</label><input type="date" name="date_fin" class="form-control"></div>
                        <div class="mb-3"><label class="form-label fw-bold">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Affecter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
