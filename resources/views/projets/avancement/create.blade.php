@extends('layouts.app')
@section('title', 'Nouvelle Mise à Jour - KENAM SERVICES')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line me-2"></i>Nouvelle Mise à Jour Avancement</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projets.avancement.update') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Projet *</label><select name="projet_id" class="form-select" required><option value="">-- Sélectionner --</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Date *</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Pourcentage avancement *</label><input type="number" name="pourcentage" class="form-control" min="0" max="100" required><small class="text-muted">0 à 100%</small></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Statut</label><select name="statut" class="form-select"><option>En cours</option><option>En pause</option><option>Terminé</option></select></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Tâches réalisées</label><textarea name="taches_realisees" class="form-control" rows="3"></textarea></div>
                        <div class="mb-3"><label class="form-label fw-bold">Difficultés rencontrées</label><textarea name="difficultes" class="form-control" rows="2"></textarea></div>
                        <div class="mb-3"><label class="form-label fw-bold">Prochaines étapes</label><textarea name="prochaines_etapes" class="form-control" rows="2"></textarea></div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
