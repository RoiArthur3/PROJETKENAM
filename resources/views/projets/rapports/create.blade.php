@extends('layouts.app')
@section('title', 'Nouveau Rapport - KENAM SERVICES')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-alt me-2"></i>Nouveau Rapport Projet</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="#" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Projet *</label><select name="projet_id" class="form-select" required><option value="">-- Sélectionner --</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Date *</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Type rapport *</label><select name="type" class="form-select" required><option>Quotidien</option><option>Hebdomadaire</option><option>Mensuel</option><option>Final</option></select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Rédacteur *</label><input type="text" name="redacteur" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Titre *</label><input type="text" name="titre" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Contenu *</label><textarea name="contenu" class="form-control" rows="6" required></textarea></div>
                        <div class="mb-3"><label class="form-label fw-bold">Pièces jointes</label><input type="file" name="fichiers[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"></div>
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
