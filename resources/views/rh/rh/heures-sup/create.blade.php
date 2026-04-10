@extends('layouts.app')
@section('title', 'Déclarer Heures Supplémentaires - KENAM SERVICES')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clock me-2"></i>Déclarer Heures Supplémentaires</h6>
                    <a href="{{ route('rh.heures-sup') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left me-1"></i>Retour</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="#">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Agent *</label><select name="agent_id" class="form-select" required><option value="">-- Sélectionner --</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Date *</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><label class="form-label fw-bold">Heure début *</label><input type="time" name="heure_debut" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Heure fin *</label><input type="time" name="heure_fin" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Durée (heures)</label><input type="number" name="duree" class="form-control" min="0" step="0.5" readonly></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Type *</label><select name="type" class="form-select" required><option>Heures normales (125%)</option><option>Heures nuit/dimanche (150%)</option><option>Heures férié (200%)</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Projet/Mission</label><input type="text" name="projet" class="form-control"></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Motif *</label><textarea name="motif" class="form-control" rows="2" required></textarea></div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('rh.heures-sup') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Annuler</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Déclarer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
