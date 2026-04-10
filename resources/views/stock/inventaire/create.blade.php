@extends('layouts.app')
@section('title', 'Nouvel Inventaire - KENAM SERVICES')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-check me-2"></i>Nouvel Inventaire</h6>
                    <a href="{{ route('stock.inventaire') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left me-1"></i>Retour</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('stock.inventaire.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Date *</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Entrepôt *</label><select name="entrepot_id" class="form-select" required><option>Entrepôt Principal</option></select></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Responsable *</label><input type="text" name="responsable" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Type</label><select name="type" class="form-select"><option>Inventaire complet</option><option>Inventaire partiel</option></select></div>
                        <div class="mb-3"><label class="form-label fw-bold">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('stock.inventaire') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Annuler</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Créer l'inventaire</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
